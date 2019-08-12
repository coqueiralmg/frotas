<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Session;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use \Exception;

class InstallController extends AppController
{
    public function addadmin()
    {
        $t_usuario = TableRegistry::get('Usuario');
        $qtd = $t_usuario->find()->where([
            'grupo' => 1
        ])->count();

        if($qtd > 0)
        {
            $this->redirectLogin('Já existe usuário com privilégios administrativos. Nenhum usuário foi criado.');
        }
        else
        {
            $entity = $t_usuario->newEntity();
            $entity->usuario = 'admin';
            $entity->nome = 'Administrador';
            $entity->email = 'frotas@coqueiral.mg.gov.br';
            $entity->senha = Security::hash('123456', 'md5', true);
            $entity->grupo = 1;
            $entity->ativo = true;
            $entity->suspenso = false;
            $entity->verificar = false;

            try
            {
                $t_usuario->save($entity);
                $this->redirectLogin('Usuário administrativo criado com sucesso.', false);
            }
            catch (Exception $ex)
            {
                $this->redirectLogin($ex->getMessage(), false);
            }
        }
    }
}
