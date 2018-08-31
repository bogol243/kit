<?php

namespace Personal {
    class Module extends \Module
    {
        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
                $menu[] = array(
                    'name' => 'Кадры',
                    'url' => '/personal.index/',
                    'icon' => 'user',
                );
        }
    }
}