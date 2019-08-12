<?php

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Classe de manipulação de validação de menus do sistema
 */
class MenuHelper extends Helper
{
    /**
     * Valida o estilo do menu ativo do sistema
     * @param array $location Localização determinada pelo menu do sistema
     * @return string Estilo retornado para menu ativo ou não
     */
    public function activeMenu(array $location)
    {
        $style = '';
        $controller = strtolower($this->getView()->getRequest()->getParam('controller'));
        $action = strtolower($this->getView()->getRequest()->getParam('action'));

        if(isset($location['action']))
        {
            if($controller == $location['controller'] && $action == $location['action'])
                $style = 'active';
        }
        else
        {
            if($controller == $location['controller'])
                $style = 'active';
        }

        return $style;
    }

    /**
     * Verifica se o menu corrente é o equivalente a página corrente
     * @param array $location Localização determinada pelo menu do sistema
     * @return bool Se o menu é ativo ou não
     */
    public function activeMenuItem(array $location)
    {
        $active = false;
        $controller = strtolower($this->getView()->getRequest()->getParam('controller'));
        $action = strtolower($this->getView()->getRequest()->getParam('action'));

        if(isset($location['action']))
        {
            if($controller == $location['controller'] && $action == $location['action'])
                $active = true;
        }
        else
        {
            if($controller == $location['controller'])
                $active = true;
        }

        return $active;
    }
}
