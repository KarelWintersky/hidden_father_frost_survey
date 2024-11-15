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

        $this->table = 'participants';
    }

    public function view()
    {
        if ($session = App::$flash->getMessage('json_session')) {
            $this->template->assign("session", $session[0]);
            App::$flash->clearMessage('json_session');
        }
        // App::$flash->addMessage("flash", ['notify' => "Форма загружена"]);
        // App::$flash->addMessage("flash", ['notify' => "Форма загружена2"]);

        $this->template->assign("state", "anketa");
        $this->template->assign("title", "Анкета участника");
        $this->template->setTemplate('templates/_anketa.tpl');
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

        $this->template->setTemplate( 'templates/_result.tpl');

        $dataset = [
            'fio'           =>  input('fio'),
            'email'         =>  input('email'),
            'address'       =>  input('address'),
            'cards_count'   =>  input('cards_count'),
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

        // $sth = $this->pdo->prepare("INSERT INTO participants (fio, email, address, cards_count) VALUES (:fio, :email, :address, :cards_count)" );

        // (new Mailer())->mailToAdmin("Новый клуб", "Некто с адресом {$dataset['owner_email']} подал заявку на добавление клуба {$dataset['title']}");

        return true;
    }

}