<?php

namespace SecretFatherFrost;

use AllowDynamicProperties;
use Arris\AppRouter;
use AJUR\FluentPDO\Exception;
use AJUR\FluentPDO\Query;

#[AllowDynamicProperties]
class Main
{
    public function __construct()
    {
        $this->app = App::factory();

        $this->pdo = App::$pdo;
        $this->template = App::$template;
    }

    public function view()
    {
        if ($session = App::$flash->getMessage('json_session')) {
            $this->template->assign("session", $session[0]);
            App::$flash->clearMessage('json_session');
        }

        $this->template->assign("state", "anketa");
        $this->template->assign("title", "Анкета участника");
        $this->template->setTemplate('anketa.tpl');
    }

    public function callback()
    {
        if ($_REQUEST['captcha'] != $_SESSION['captcha_keystring']) {
            unset($_REQUEST['captcha']); // иначе значение капчи окажется сохранено в flash-message
            App::$flash->addMessage('error', 'Капча введена неправильно!');
            App::$flash->addMessage('json_session', json_encode($_REQUEST));
            $this->template->setRedirect(AppRouter::getRouter('view'));
            return;
        }

        $query = new Query(App::$pdo, includeTableAliasColumns: false);

        $dataset = [
            'fio'           =>  input('fio'),
            'email'         =>  input('email'),
            'address'       =>  input('address'),
            'cards_count'   =>  input('cards_count'),
        ];
        try {
            $query = $query
                ->insertInto('participants')
                ->values($dataset);

            $query->execute();

        } catch (Exception $e) {
            dd($e);
        }

        // (new Mailer())->mailToAdmin("Новый клуб", "Некто с адресом {$dataset['owner_email']} подал заявку на добавление клуба {$dataset['title']}");
        $this->template->assign("state", "success");
        $this->template->assign("title", "Принято!");
        $this->template->setRedirect( AppRouter::getRouter('view') );
    }

}