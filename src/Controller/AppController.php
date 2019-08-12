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

        $this->registerAccessLog();

        $this->validationRole = true;
    }

    /**
     * Efetua o registro de log de acesso
     */
    private function registerAccessLog()
    {
        $ip = $this->request->clientIp();
        $method = $this->request->getMethod();
        $scheme = $this->request->scheme();
        $host = $this->request->host();
        $here = $this->request->getRequestTarget();
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $registro = "$ip    $method   $scheme://$host$here    $agent";

        Log::info($registro, ['register']);
    }
}
