<?php

namespace Stock {
    class Module extends \Module
    {
        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
            $menu[] = array(
                'name'  => 'Сырье',
                'url'  => '/stock.main/',
                'icon'  => 'database',
            );
        }
    }
}