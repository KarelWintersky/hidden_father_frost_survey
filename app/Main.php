<?php

namespace SecretFatherFrost;

use AllowDynamicProperties;
use Arris\AppRouter;
use AJUR\FluentPDO\Exception;
use AJUR\FluentPDO\Query;
use Arris\Cache\Cache;

#[AllowDynamicProperties]
class Main
{
    public function __construct()
    {
        $this->app = App::factory();

        $this->pdo = App::$pdo;
        $this->template = App::$template;

        $this->table = App::SQL_TABLE;
    }

    public function view()
    {
        if ($session = App::$flash->getMessage('json_session')) {
            $this->template->assign("session", $session[0]);
            App::$flash->clearMessage('json_session');
        }
        // App::$flash->addMessage("flash", ['notify' => "Форма загружена"]);
        // App::$flash->addMessage("flash", ['notify' => "Форма загружена2"]);

        $members_count = Cache::get(App::REDIS_KEY);

        $this->template->assign("members_count", $members_count);

        $event_year = App::$options['EVENT_YEAR'];
        $event_end = App::$options['EVENT_END'] ?? "15 декабря {$event_year}";

        $this->template->assign("event_end", $event_end);

        $this->template->assign("domain", App::$options['DOMAIN'] ?? '');

        $this->template->assign("use_radio", (int)App::$options['FEATURES']['USE_RADIO_BUTTONS'] ?? 0);

        $this->template->assign("state", "anketa");
        $this->template->assign("title", "Анкета участника");
        $this->template->setTemplate('templates/anketa.tpl');
    }

    /**
     * @throws Exception
     */
    public function callback()
    {
        if ($_REQUEST['captcha'] !== $_SESSION['captcha_keystring']) {
            unset($_REQUEST['captcha']); // иначе значение капчи окажется сохранено в flash-message

            App::$flash->addMessage("flash", ['error' => 'Капча введена неправильно!']);
            App::$flash->addMessage('json_session', json_encode($_REQUEST));

            $this->template->setRedirect(AppRouter::getRouter('view'));

            return true;
        }

        $dataset = [
            'fio'           =>  input('fio'),
            'email'         =>  input('email'),
            'address'       =>  input('address'),
            'cards_count'   =>  input('cards_count'),
            'event_year'    =>  App::$options['EVENT_YEAR']
        ];

        $query = new Query($this->pdo, includeTableAliasColumns: true);

        // проверяем, была ли заявка?
        $query = $query->from($this->table)->where('email = ?', $dataset['email']);

        $email = $query->fetch();

        if ($email) {
            $this->template->assign("state", "already");
            return true;
        }

        $query = new Query($this->pdo, includeTableAliasColumns: true);

        $this->template->assign("state", "success");

        $query = $query
            ->insertInto($this->table)
            ->values($dataset);

        $query->execute();

        Cache::drop(App::REDIS_KEY);

        $this->template->setTemplate( 'templates/result.tpl');

        return true;
    }

}