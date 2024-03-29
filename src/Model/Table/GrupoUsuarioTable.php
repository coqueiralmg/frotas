<?php

namespace App\Model\Table;

class GrupoUsuarioTable extends BaseTable
{
    public function initialize(array $config)
    {
        $this->setTable('grupo');
        $this->setPrimaryKey('id');
        $this->setEntityClass('GrupoUsuario');

         $this->belongsToMany('Funcao', [
            'joinTable' => 'funcao_grupo',
            'foreignKey' => 'grupo_id',
            'targetForeignKey' => 'funcao_id',
            'propertyName' => 'funcoes'
        ]);
    }
}
