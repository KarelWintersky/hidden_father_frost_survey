<?php

namespace SecretFatherFrost;

use Arris\AppLogger;
use Arris\Cache\Cache;
use Arris\Database\Connector;
use Arris\Presenter\FlashMessages;
use Arris\Presenter\Template;
use Arris\Toolkit\RedisClientException;
use PDO;

class App extends \Arris\App
{
    /**
     * SQL Table for collecting data
     */
    const SQL_TABLE = 'participants';

    /**
     * REDIS key for faster access to members count
     */
    const REDIS_KEY = 'members_count';

    public static Template $template;

    public static FlashMessages $flash;

    public static Connector $pdo;

    public static array $credentials;

    public static array $credentials_redis;

    public static array $options;

    /**
     * @throws RedisClientException
     * @throws \RedisException
     * @throws \SmartyException
     * @throws \JsonException
     */
    public static function init()
    {
        $config_path = PATH_ENV . 'site.ini';
        if (!is_file($config_path)) {
            throw new \Exception("Config file not found");
        }

        $options = parse_ini_file($config_path, true);

        self::$options = $options;
        self::$credentials = $options['DATABASE'];
        self::$credentials_redis = $options['REDIS'] ?? [];

        AppLogger::init("secret_father_frost", bin2hex(random_bytes(4)), [
            'default_logfile_path'      =>  PATH_ROOT . '/logs/',
            'default_logfile_prefix'    =>  date_format(date_create(), 'Y-m') . '__'
        ]);
        AppLogger::addScope('site_usage', [
            [ 'site_usage.log', AppLogger\Monolog\Logger::NOTICE]
        ]);

        App::$pdo = (new \Arris\Database\Config())
            ->setHost(self::$credentials['HOST'])
            ->setUsername(self::$credentials['USERNAME'])
            ->setPassword(self::$credentials['PASSWORD'])
            ->setDatabase(self::$credentials['DBNAME'])
            ->connect();

        Cache::init(
            redis_database: self::$credentials_redis['DATABASE'],
            redis_enabled: self::$credentials_redis['ENABLED'] ?? false,
            PDO: App::$pdo
        );

        Cache::addRule(
            App::REDIS_KEY,
            source: Cache::RULE_SOURCE_CALLBACK,
            action: static function() {
                $query = new \AJUR\FluentPDO\Query(App::$pdo, includeTableAliasColumns: true);
                $members_count = $query->from(App::SQL_TABLE)->where('event_year', App::$options['EVENT_YEAR'])->count();
                return $members_count ?? 0;
            }
        );

        App::$template = new \Arris\Presenter\Template();
        App::$template
            ->setTemplateDir(__DIR__)
            ->setCompileDir(PATH_ROOT . '/cache/')
            ->setForceCompile(true)
            ->registerClass("Arris\AppRouter", "Arris\AppRouter");

        App::$flash = new \Arris\Presenter\FlashMessages();



    }

}