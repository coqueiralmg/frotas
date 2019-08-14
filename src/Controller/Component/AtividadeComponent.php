<?php

namespace App\Controller\Component;

use App\Error\AuditoriaException;
use App\Model\Entity\Atividade;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Classe que representa a atividade a ser registrada na auditoria
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
     * @throws AuditoriaException O usuário não tem a permissão de acessar a determinada tela do sistema.
     * $return Atividade Atividade da auditoria do sistema.
     */
    public function validar(int $codigoAtividade)
    {
        try
        {
            $t_atividades = TableRegistry::get('Atividade');
            $atividade = $t_atividades->get($codigoAtividade);

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
}
