<?php

namespace Report {
    class Module extends \Module
    {
        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
                $menu[] = array(
                    'name' => 'Отчеты',
                    'url' => '/report.index/',
                    'icon' => 'list',
                );
        }
    }
}