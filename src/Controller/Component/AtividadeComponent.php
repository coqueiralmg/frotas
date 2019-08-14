<?php

namespace App\Controller\Component;

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
}
