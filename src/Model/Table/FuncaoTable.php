<?php

namespace App\Model\Table;


class FuncaoTable extends BaseTable
{
    public function initialize(array $config)
    {
        $this->setTable('funcao');
        $this->setPrimaryKey('id');
        $this->setEntityClass('Funcao');

        $this->belongsToMany('GrupoUsuario', [
            'joinTable' => 'funcao_grupo',
            'foreignKey' => 'funcaos_id',
            'targetForeignKey' => 'grupo_id',
            'propertyName' => 'grupos'
        ]);
    }
}
