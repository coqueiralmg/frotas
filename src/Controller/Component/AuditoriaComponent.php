<?php

namespace App\Controller\Component;

use App\Error\AuditoriaException;
use App\Model\Entity\Usuario;
use Cake\Core\Configure;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Exception;

/**
 * Classe que representa o componente de controle e gerenciamento de auditoria.
 * @package App\Controller\Component
 */
class AuditoriaComponent extends Component
{
    /**
     * Faz a associação com componentes relacionados
     */
    public $components = ['Atividade'];

    /**
     * Faz o registro de auditoria no sistema.
     *
     * @param Auditoria $dados Dados a serem adicionados no banco de dados de auditoria.
     * @return int Código de auditoria gerada no banco de dados.
     */
    public function registrar(Auditoria $auditoria)
    {
        try
        {
            $atividade = $this->Atividade->validar($auditoria->ocorrencia);
            $request = $this->getController()->getRequest();
            $id = 0; $table = TableRegistry::get('Auditoria');

            if($atividade == null)
            {
                throw new AuditoriaException([
                    'message' => 'Ocorreu um erro ao buscar uma atividade',
                    'atividade' => $auditoria->ocorrencia
                ]);
            }

            $auditoria->data = date("Y-m-d H:i:s");
            $auditoria->usuario = $auditoria->usuario ?: $request->getSession()->read('Usuario.ID');
            $auditoria->ip = $request->clientIp();
            $auditoria->agent = $request->getHeaderLine('User-Agent');
            $auditoria->assinatura = $request->getParam('_csrfToken');
            $auditoria->sessao = $request->getSession()->id();

            if($atividade->validar && !$auditoria->assinatura)
            {
                throw new AuditoriaException([
                    'message' => 'É obrigatório informar a assinatura do registro de auditoria.',
                    'atividade' => $auditoria->ocorrencia
                ]);
            }

            if($table->save($auditoria))
            {
                $id = $auditoria->id;
            }

            return $id;
        }
        catch(Exception $aex)
        {
            throw new AuditoriaException('Ocorreu um erro ao gerar um registro de auditoria', null, $aex);
        }
    }

    /**
     * Retorna uma lista de registros de auditoria de um determinado usuário.
     * @param int $usuario Um usuário do sistema
     * @return array Lista de registro de auditoria
     */
    public function listar(int $usuario)
    {
       $table = TableRegistry::get('Auditoria');

       $query = $table->find('all', [
           'conditions' => [
               'usuario' => $usuario
           ]
       ]);

        return $query->toArray();
    }

    /**
     * Retorna uma quantidade de registros de auditoria de um determinado usuário.
     * @param int $usuario Um usuário do sistema
     * @return int Quantidade de registro de auditoria no sistema
     */
    public function quantidade(int $usuario)
    {
        $table = TableRegistry::get('Auditoria');

        $query = $table->find('all', [
            'conditions' => [
                'usuario' => $usuario
            ]
        ]);

        return $query->count();
    }

    /**
     * Exclui toda a auditoria de um determinado usuário
     * @param int $usuario Um usuário do sistema
     */
    public function limpar(int $usuario)
    {
        $table = TableRegistry::get('Auditoria');
        $table->deleteAll(['usuario' => $usuario]);
    }

    /**
    * Busca o nome da ocorrência da auditoria por código
    * @param int $codigo Código da ocorrência
    * @return string Nome da ocorrência pré-cadastrada na lista
    */
    public function buscarNomeOcorrencia(int $codigo)
    {
        $ocorrencias = Configure::read('Auditoria.ocorrencias');
        return $ocorrencias[$codigo];
    }

    /**
    * Obtém todas as ocorrências pré definidas do código
    * @return array Coletânea de todas as ocorrências pré definidas
    */
    public function obterOcorrencias()
    {
        $ocorrencias = Configure::read('Auditoria.ocorrencias');
        return $ocorrencias;
    }

    /**
    * Obtém a lista de campos originais que foram modificados
    * @param Entity $entity Entidade a ser analisada
    * @return array Lista de campos modificados com seus valores originais
    */
    public function changedOriginalFields(Entity $entity)
    {
        return $entity->extractOriginalChanged($entity->visibleProperties());
    }

    /**
    * Obtém a lista de campos modificados em uma entidade, com seus respectivos valores atualizados
    * @param Entity $entity Entidade a ser analisada
    * @param array $propriedades Lista de campos de uma propriedade com seus respectivos valores.
    * @return array Lista de campos modificados com seus valores originais
    */
    public function changedFields(Entity $entity, array $propriedades)
    {
        $campos = array();

        foreach($propriedades as $chave => $valor)
        {
            $campos[$chave] = $entity->get($chave);
        }

        return $campos;
    }
}
