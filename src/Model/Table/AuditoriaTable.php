<?php

namespace App\Model\Table;


class AuditoriaTable extends BaseTable
{
    public function initialize(array $config)
    {
        $this->setTable('auditoria');
        $this->setPrimaryKey('id');
        $this->setEntityClass('Auditoria');

        $this->belongsTo('Usuario', [
            'className' => 'Usuario',
            'foreignKey' => 'usuario',
            'propertyName' => 'usuario',
            'joinType' => 'LEFT OUTER'
        ]);
    }
}
