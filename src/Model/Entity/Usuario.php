<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;


class Usuario extends Entity
{
    protected function _getAtivado()
    {
        return $this->_properties['ativo'] ? 'Sim' : 'Não';
    }

    protected function _getImpedido()
    {
        return $this->_properties['suspenso'] ? 'Sim' : 'Não';
    }
}
