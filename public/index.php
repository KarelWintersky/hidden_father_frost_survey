<?php

use AJUR\FluentPDO\Query;
use Arris\AppLogger;
use Arris\AppRouter;
use Arris\Cache\Cache;
use SecretFatherFrost\App;

define('PATH_ROOT', dirname(__DIR__, 1));
define('ENGINE_START_TIME', microtime(true));
const PATH_ENV = '/etc/arris/hidden_father_frost/';

if (!session_id()) @session_start();
error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

try {
    if (!is_file(__DIR__ . '/../vendor/autoload.php')) {
        throw new RuntimeException("[FATAL ERROR] No 3rd-party libraries installed.");
    }

    require_once __DIR__ . '/../vendor/autoload.php';

    App::init();

    $options = parse_ini_file(PATH_ENV . 'site.ini', true);
    $credentials = $options['database'];
    $credentials_redis = $options['redis'] ?? [ 'REDIS.ENABLED' =>  0 ];

    $activity = $_GET['activity'] ?? $options['DEFAULT_ACTIVITY'] ?? 'default';

    AppLogger::init("secret_father_frost", bin2hex(random_bytes(4)), [
        'default_logfile_path'      =>  PATH_ROOT . '/logs/',
        'default_logfile_prefix'    =>  date_format(date_create(), 'Y-m') . '__'
    ]);
    AppLogger::addScope('site_usage', [
        [ 'site_usage.log', AppLogger\Monolog\Logger::NOTICE]
    ]);

    App::$pdo = new \Arris\Database\DBWrapper([
        'driver'            =>  'mysql',
        'hostname'          =>  $credentials['DB.HOST'],
        'database'          =>  $credentials['DB.NAME'],
        'username'          =>  $credentials['DB.USERNAME'],
        'password'          =>  $credentials['DB.PASSWORD'],
        'port'              =>  $credentials['DB.PORT'],
        'charset'           =>  $credentials['DB.CHARSET'],
        'charset_collate'   =>  $credentials['DB.COLLATE'],
        'slow_query_threshold'  => 1
    ]);

    Cache::init([
        'enabled'   =>  $credentials_redis['REDIS.ENABLED'],
        'database'  =>  $credentials_redis['REDIS.DATABASE'],
    ], [], App::$pdo);

    Cache::addRule(App::REDIS_KEY, [
        'source'    =>  \Arris\Cache\CacheInterface::RULE_SOURCE_CALLBACK,
        'action'    =>  static function() {
            $query = new Query(App::$pdo, includeTableAliasColumns: true);
            $members_count = $query->from(App::SQL_TABLE)->count();
            return $members_count ?? 0;
        }
    ]);

    App::$template = new \Arris\Presenter\Template();
    App::$template
        ->setTemplateDir(__DIR__)
        ->setCompileDir(PATH_ROOT . '/cache/')
        ->setForceCompile(true)
        ->registerClass("Arris\AppRouter", "Arris\AppRouter");

    App::$flash = new \Arris\Presenter\FlashMessages();

    AppRouter::init();
    AppRouter::setDefaultNamespace("\SecretFatherFrost");
    AppRouter::get('/', [ \SecretFatherFrost\Main::class, 'view'], 'view');
    AppRouter::post('/', [ \SecretFatherFrost\Main::class, 'callback'], 'callback');
    AppRouter::dispatch();

    // либо так, а в шаблоне: const flash_messages = {$flash_messages|json_encode|default:"{ }"};
    App::$template->assign("flash_messages", App::$flash->getMessage('flash', []));

    // либо так, но в шаблоне: const flash_messages = {$flash_messages|default:"{ }"};
    // App::$template->assign("flash_messages", json_encode( App::$flash->getMessage('flash', []), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ));

} catch (Exception $e) {
    dd($e);
}

$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();

    echo $render;
}

logSiteUsage( AppLogger::scope('site_usage'));

if (App::$template->isRedirect()) {
    App::$template->makeRedirect();
}



