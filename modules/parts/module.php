<?php

namespace Parts {
    class Module extends \Module
    {
        function GetOrder()
        {
            return \Equipment\Module::GetOrder() + 1;
        }

        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
            $menu[] = array(
                'name' => 'Детали',
                'url'  => '/parts.index/',
                'icon' => 'gears',
            );
        }
    }
}