<?php

namespace App\Controller\Component;

use App\Error\AuditoriaException;

use App\Model\Entity\Atividade;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Classe que representa a atividade a ser registrada na auditoria
 * @package App\Controller\Component
 */
class AtividadeComponent extends Component
{
    const SYSTEM_LOGON_SISTEMA = 1;
    const SYSTEM_TROCA_SENHA = 2;
    const SYSTEM_BLOQUEIO_IP = 3;
    const SYSTEM_SUSPENSAO_CONTA = 4;
    const SYSTEM_LIMPEZA_CACHE_SESSAO = 5;
    const SYSTEM_ACESSO_SUSPEITO = 6;
    const SYSTEM_ACESSO_BLOQUEADO = 7;
    const SYSTEM_LOGOFF_SISTEMA = 8;
    const SYSTEM_IMPRESSAO_DOCUMENTO = 9;

    /**
     * Verifica se a atividade existe no banco de dados e retorna a mesma, com todas as informações requeridas.
     * @param int $codigoAtividade Código de atividade
     * @throws AuditoriaException Ocorreu uma falha ao obter uma atividade de auditoria..
     * @return Atividade Atividade da auditoria do sistema.
     */
    public function validar(int $codigoAtividade)
    {
        try
        {
            $atividade = $this->obterAtividade($codigoAtividade);
            return $atividade;
        }
        catch(Exception $ae)
        {
            $message = [
                'message' => 'Ocorreu um erro ao buscar uma atividade',
                'atividade' => $codigoAtividade
            ];

            throw new AuditoriaException($message, null, $ae);
        }
    }

    /**
     * Obtém uma atividade específica, informando um código.
     * @param int $codigoAtividade Código de atividade
     * @throws AuditoriaException Ocorreu uma falha ao obter uma atividade de auditoria.
     * @return Atividade Atividade do sistema de auditoria.
     */
    public function obterAtividade(int $codigoAtividade)
    {
        try
        {
            $atividade = null;
            $atividades = $this->obterAtividades();

            foreach($atividades as $pivot)
            {
                if($pivot->id == $codigoAtividade)
                {
                    $atividade = $pivot;
                    break;
                }
            }

            return $atividade;
        }
        catch(Exception $ae)
        {
            throw new AuditoriaException('Ocorreu um erro ao buscar uma atividade', null, $ae);
        }
    }

    /**
     * Obtém uma coletânea de atividades, incluindo a possibilidade da consulta ser cacheável.
     * @throws AuditoriaException Ocorreu uma falha ao obter uma atividade de auditoria.
     */
    public function obterAtividades()
    {
        try
        {
            $atividades = null;

            if(Cache::read('ACTIVITY_AUDIT') != null)
            {
                $atividades = Cache::read('ACTIVITY_AUDIT');
            }
            else
            {
                $t_atividades = TableRegistry::get('Atividade');
                $atividades = $t_atividades->find('all')->all();
                Cache::write('ACTIVITY_AUDIT', $atividades);
            }

            return $atividades;
        }
        catch(Exception $ae)
        {
            throw new AuditoriaException('Ocorreu um erro ao buscar uma atividade', null, $ae);
        }
    }
}
