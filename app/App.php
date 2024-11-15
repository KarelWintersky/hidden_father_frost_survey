<?php

namespace SecretFatherFrost;

use Arris\Database\DBWrapper;
use Arris\Template\FlashMessages;
use Arris\Template\Template;
use PDO;

class App extends \Arris\App
{
    public static Template $template;

    public static FlashMessages $flash;

    public static DBWrapper|PDO $pdo;

    public static function init()
    {

    }

}