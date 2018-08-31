<?php

namespace Repair {
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
                'name' => 'Ремонт',
                'url' => '/repair.index/',
                'icon' => 'wrench',
            );
        }
    }
}