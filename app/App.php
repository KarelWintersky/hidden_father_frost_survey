<?php

namespace SecretFatherFrost;

use Arris\Database\DBWrapper;
use Arris\Presenter\FlashMessages;
use Arris\Presenter\Template;
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

    public static DBWrapper|PDO $pdo;

    public static function init()
    {
    }

}