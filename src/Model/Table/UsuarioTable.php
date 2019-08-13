<?php

namespace App\Model\Table;

class UsuarioTable extends BaseTable
{
    public function initialize(array $config)
    {
        $this->setTable('usuario');
        $this->setPrimaryKey('id');
        $this->setEntityClass('Usuario');

        $this->belongsTo('GrupoUsuario', [
            'className' => 'GrupoUsuario',
            'foreignKey' => 'grupo',
            'propertyName' => 'grupoUsuario',
            'joinType' => 'INNER'
        ]);
    }
}
