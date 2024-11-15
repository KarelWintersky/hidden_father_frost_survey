<?php

use Arris\Database\DBWrapper;
use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;
use SecretFatherFrost\App;

/**
 * @param string|array $key
 * @param $value [optional]
 * @return string|array|bool|mixed|null
 */
function config($key = '', $value = null) {
    $app = App::factory();

    if (!is_null($value) && !empty($key)) {
        $app->setConfig($key, $value);
        return true;
    }

    if (is_array($key)) {
        foreach ($key as $k => $v) {
            $app->setConfig($k, $v);
        }
        return true;
    }

    if (empty($key)) {
        return $app->getConfig();
    }

    return $app->getConfig($key);
}

function logSiteUsage(LoggerInterface $logger, $is_print = false): void
{
    $metrics = [
        'time.total'        =>  number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 6, '.', ''),
        'memory.usage'      =>  memory_get_usage(true),
        'site.url'          =>  idn_to_utf8($_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'],
    ];

    /**
     * @var DBWrapper $pdo
     */
    $pdo = App::$pdo;

    if (!is_null($pdo)) {
        $stats = $pdo->getStats();
        $metrics['mysql.queries'] = $stats['total_queries'];
        $metrics['mysql.time'] = $stats['total_time'];
    }

    $metrics['ipv4'] = Server::getIP();

    if ($is_print) {
        $site_usage_stats = sprintf(
            '<!-- Consumed memory: %u bytes, SQL query count: %u, SQL time %g sec, Total time: %g sec. -->',
            $metrics['memory.usage'],
            $metrics['MySQL']['Queries'],
            $metrics['MySQL']['Time'],
            $metrics['time.total']
        );
        echo $site_usage_stats . PHP_EOL;
    }

    $logger->notice('', $metrics);
}


/**
 * @param string $key
 * @param string $default
 * @return mixed
 */
function input(string $key = '', string $default = ''):mixed
{
    if (empty($key)) {
        return $_REQUEST;
    }

    return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default;
}
