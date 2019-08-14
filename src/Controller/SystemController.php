<?php

namespace App\Controller;

use App\Model\Entity\Auditoria;
use App\Model\Entity\Usuario;
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

    public function logon()
    {
        if($this->request->is('post'))
        {
            $login = $this->request->data['usuario'];
            $senha = $this->request->data['senha'];

            if($login == '' || $senha == '')
            {
                $this->redirectLogin('É obrigatório informar o login e a senha.');
            }
            else
            {
                $this->Cookie->write('Login.user', $login);
                $t_usuarios = TableRegistry::get('Usuario');

                $query = $t_usuario->find('all', [
                    'contain' => ['GrupoUsuario'],
                    'conditions' => [
                        'Usuario.usuario' => $login,
                    ],
                ])->orWhere([
                    'Usuario.email' => $login,
                ]);

                if($query->count > 0)
                {
                    $usuario = $query->first();
                }
                else
                {
                    $this->atualizarTentativas('Os dados estão inválidos');
                }
            }
        }
    }

    private function atualizarTentativas(string $mensagem)
    {
        $session = $this->getRequest()->getSession();
        $tentativa = $session->read('Login.attemps');
        $aviso = Configure::read('Security.login.attemps.warning');
        $limite = Configure::read('Security.login.attemps.max');
        $session->write('Login.attemps', $tentativa + 1);

        if ($tentativa >= $aviso && $tentativa < $limite)
        {
            $this->redirectLogin('Você tentou o acesso ' . $tentativa . ' vezes. Caso você tente ' . $limite . ' vezes sem sucesso, você será bloqueado.');
        }
        elseif ($tentativa >= $limite)
        {
            $this->bloquearAcesso();
            $this->redirectLogin('O acesso ao sistema encontra-se bloqueado.');
        }
        else
        {
            $this->redirectLogin($mensagem);
        }
    }

    private function obterLoginCookie()
    {
        $login = "";

        if ($this->Cookie->check('Login.user')) {
            $login = $this->Cookie->read('Login.user');
        }

        return $login;
    }

    private function configurarTentativas()
    {
        $session = $this->getRequest()->getSession();

        if (!$session->check('Login.attemps'))
        {
            $session->write('Login.attemps', 0);
        }
    }

    private function validarLogin(Usuario $usuario, string $senha = '')
    {
        $session = $this->getRequest()->getSession();

        if (!$usuario->ativo)
        {
            $this->redirectLogin("O usuário encontra-se inativo para o sistema.");
            return;
        }

        if ($usuario->suspenso)
        {
            $this->redirectLogin("O usuário encontra-se suspenso no sistema. Favor entrar em contato com o administrador do sistema.");
            return;
        }

        if (!$usuario->grupoUsuario->ativo)
        {
            $this->redirectLogin("O usuário encontra-se em um grupo de usuário inativo.");
            return;
        }

        if ($senha != '')
        {
            $pivot = Security::hash($senha, 'md5', true);

            if ($usuario->senha != $pivot)
            {
                $this->atualizarTentativas('A senha informada é inválida.');
                return;
            }
        }

        if ($usuario->verificar)
        {
            $session->write('Usuario.ID', $usuario->id);
            $this->Flash->success('Por favor, troque a senha.');
            $this->redirect(['controller' => 'system', 'action' => 'password']);
            return;
        }

        $auditoria = new Auditoria([
            'ocorrencia' => $this->Atividade::SYSTEM_LOGON_SISTEMA,
            'descricao' => 'O usuário efetuou login com sucesso',
            'usuario' => $usuario->id
        ]);

        if ($senha != '')
        {
            $session->write('Usuario', $usuario);
            $session->write('Usuario.ID', $usuario->id);
            $session->write('Usuario.nick', $usuario->usuario);
            $session->write('Usuario.nome', $usuario->nome);
            $session->write('Usuario.email', $usuario->email);
            $session->write('Usuario.grupo', $usuario->grupo);
        }

        $session->write('Usuario.entrada', date("Y-m-d H:i:s"));
        $this->redirect(['controller' => 'system', 'action' => 'board']);
    }
}
