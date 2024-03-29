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
        if($this->getRequest()->getSession()->check('Usuario'))
        {
            $idUsuario = $this->getRequest()->getSession()->read('Usuario.ID');
            $t_usuario = TableRegistry::get('Usuario');

            $query = $t_usuario->find('all', [
                'contain' => ['GrupoUsuario'],
                'conditions' => [
                    'Usuario.id' => $idUsuario
                ]
            ]);

            if ($query->count() > 0)
            {
                $usuario = $query->first();
                $this->validarLogin($usuario);
            }
            else
            {
                $this->atualizarTentativas('Os dados estão inválidos. Favor efetuar login.');
            }
        }
        else
        {
            $login = $this->obterLoginCookie();
            $this->viewBuilder()->setLayout('guest');
            $this->configurarTentativas();
            $this->set('title', 'Controle de Acesso');
            $this->set('login', $login);
        }
    }

    public function logon()
    {
        if($this->request->is('post'))
        {
            $login = $this->getRequest()->getData('usuario');
            $senha = $this->getRequest()->getData('senha');

            if($login == '' || $senha == '')
            {
                $this->redirectLogin('É obrigatório informar o login e a senha.');
            }
            else
            {
                $this->Cookie->write('Login.user', $login);
                $t_usuarios = TableRegistry::get('Usuario');

                $query = $t_usuarios->find('all', [
                    'contain' => ['GrupoUsuario'],
                    'conditions' => ['OR' => [
                        'usuario.usuario' => $login,
                        'usuario.email' => $login
                        ]
                    ]
                ]);

                if($query->count() > 0)
                {
                    $usuario = $query->first();
                    $this->validarLogin($usuario, $senha);
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

    private function bloquearAcesso()
    {
        $login = $this->Cookie->read('Login.User');
        $t_usuario = TableRegistry::get('Usuario');

        $query = $t_usuario->find('all', [
            'contain' => ['Pessoa'],
            'conditions' => ['OR' => [
                'usuario.usuario' => $login,
                'usuario.email' => $login
                ]
            ]
        ]);

        if($query->count() > 0)
        {
            $resultado = $query->all();

            foreach($resultado as $usuario)
            {
                $usuario->suspenso = true;
                $t_usuario->save($usuario);
                $this->Monitoria->alertarContaSuspensa($usuario->pessoa->nome, $usuario->email);
            }
        }

        $access = Configure::read('Security.login.access');

        if($access == 'restrict')
        {
            $this->Firewall->bloquear('Tentativas de acesso indevidos.');
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

        $this->Auditoria->registrar($auditoria);

        if ($senha != '')
        {
            $session->write('Usuario', $usuario);
            $session->write('Usuario.ID', $usuario->id);
            $session->write('Usuario.nick', $usuario->usuario);
            $session->write('Usuario.nome', $usuario->nome);
            $session->write('Usuario.email', $usuario->email);
            $session->write('Usuario.grupo', $usuario->grupo);

            $tentativa = $session->read('Login.attemps');

            if($tentativa >= Configure::read('Security.login.attemps.warning'))
            {
                $session->write('Usuario.suspeito', true);
                $this->Monitoria->monitorar($auditoria);
            }
            else
            {
                $session->write('Usuario.suspeito', false);
            }

        }

        $session->write('Usuario.entrada', date("Y-m-d H:i:s"));
        $this->redirect(['controller' => 'system', 'action' => 'board']);
    }
}
