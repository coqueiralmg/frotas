<?php

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Network\Session;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use \Exception;

class SystemController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->validationRole = false;
    }

    public function login()
    {
        $login = $this->obterLoginCookie();
        $this->viewBuilder()->setLayout('guest');
        $this->configurarTentativas();
        $this->set('title', 'Controle de Acesso');
        $this->set('login', $login);
    }

    protected function obterLoginCookie()
    {
        $login = "";

        if ($this->Cookie->check('Login.user')) {
            $login = $this->Cookie->read('Login.user');
        }

        return $login;
    }

    protected function configurarTentativas()
    {
        if (!$this->getRequest()->getSession()->check('Login.attemps'))
        {
            $this->getRequest()->getSession()->write('Login.attemps', 0);
        }
    }
}
