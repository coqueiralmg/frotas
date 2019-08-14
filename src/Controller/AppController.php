<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use \Exception;


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Define se a haverá validação do papel dentro da tela de sistema.
     * @var bool
     */
    protected $validationRole;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);

        $this->loadComponent('Flash');
        $this->loadComponent('Cookie');
        $this->loadComponent('Atividade');
        $this->loadComponent('Auditoria');

        $this->registerAccessLog();

        $this->validationRole = true;
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        if ($this->validationRole)
        {
            $this->controlAuth();

            if($this->isAuthorized())
            {
                $this->accessRole();
            }
        }
    }

    /**
     * Controle simplificado de autenticação do usuário
     */
    protected function controlAuth()
    {
        if (!$this->isAuthorized()) {
            $this->redirectLogin("A sessão foi expirada!");
        }
    }

    /**
     * Redireciona para a tela de login com uma mensagem.
     *
     * @param string $mensagem Mensagem a ser exibida na tela de login.
     * @param bool $error Se a mensagem de erro é sucesso.
     */
    protected function redirectLogin(string $mensagem, bool $error = true)
    {
        if ($error) {
            $this->Flash->error($mensagem);
        } else {
            $this->Flash->success($mensagem);
        }

        $this->redirect(['controller' => 'system', 'action' => 'login']);
    }

    /**
     * Verifica se a sessão do usuário foi criada e ativa, ou seja, se o mesmo efetuou o login.
     *
     * @return boolean Se o usuário está logado no sistema e com acesso
     */
    protected function isAuthorized()
    {
        return $this->getRequest()->getSession()->check('Usuario');
    }

    /**
     * Verifica se o usuário possui a permissão de acessar a tela do sistema.
     * @throws ForbiddenException O usuário não tem a permissão de acessar a determinada tela do sistema.
     */
    protected function accessRole()
    {
        /*
        $controller = strtolower($this->request->getParam('controller'));
        $action = strtolower($this->request->getParam('action'));

        $url = ["controller" => $controller, "action" => $action];
        $userID = (int) $this->getRequest()->getSession()->read('Usuario.ID');

        if (!$this->Membership->handleRole($url, $userID)) {
            throw new ForbiddenException();
        }
        */
    }

    /**
     * Efetua o registro de log de acesso
     */
    private function registerAccessLog()
    {
        $ip = $this->getRequest()->clientIp();
        $method = $this->getRequest()->getMethod();
        $scheme = $this->getRequest()->scheme();
        $host = $this->getRequest()->host();
        $here = $this->getRequest()->getRequestTarget();
        $agent = $this->getRequest()->getHeaderLine('User-Agent');

        $registro = "$ip    $method   $scheme://$host$here    $agent";

        Log::info($registro, ['register']);
    }
}
