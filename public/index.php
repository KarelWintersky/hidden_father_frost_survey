<?php

use Arris\AppLogger;
use Arris\AppRouter;
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

    $options = parse_ini_file(PATH_ENV . 'site.ini', true);
    $credentials = $options['database'];

    AppLogger::init("rpgClubs", bin2hex(random_bytes(4)), [
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

    App::$template = new \Arris\Template\Template();
    App::$template
        ->setTemplateDir(__DIR__)
        ->setCompileDir(PATH_ROOT . '/cache/')
        ->setForceCompile(true)
        ->registerClass("Arris\AppRouter", "Arris\AppRouter");

    App::$flash = new \Arris\Template\FlashMessages();

    AppRouter::init();
    AppRouter::setDefaultNamespace("\SecretFatherFrost");
    AppRouter::get('/', [ \SecretFatherFrost\Main::class, 'view'], 'view');
    AppRouter::post('/', [ \SecretFatherFrost\Main::class, 'callback'], 'callback');

    AppRouter::dispatch();

} catch (Exception $e) {
    dd($e);
}
$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();

    $render = \preg_replace('/^\h*\v+/m', '', $render); // удаляем лишние переводы строк

    echo $render;
}

logSiteUsage( AppLogger::scope('site_usage') );

if (App::$template->isRedirect()) {
    App::$template->makeRedirect();
}



