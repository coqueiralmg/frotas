<?php

namespace App\Error;

use Cake\Core\Exception\Exception;

/**
 * Exceção que gerencia a auditoria
 */
class AuditoriaException extends Exception
{
    public function getAtividadeRelativa()
    {
        return $this->_attributes['atividade'];
    }
}
