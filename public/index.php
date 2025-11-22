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

    App::init();

    $activity = $_GET['activity'] ?? $options['DEFAULT_ACTIVITY'] ?? 'default';

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
    echo $render;
}

logSiteUsage( AppLogger::scope('site_usage'));

if (App::$template->isRedirect()) {
    App::$template->makeRedirect();
}



