<?php
namespace Core {
    class Module extends \Module {
        function GetOrder() {
            return 0;
        }
        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
            $menu[] = array(
                'name' => 'Сводка',
                'url' => '/',
                'icon' => 'dashboard',
            );
        }
    }
}