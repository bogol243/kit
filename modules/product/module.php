<?php

namespace Product {
    class Module extends \Module
    {
        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
            $menu[] = array(
                'name'  => 'Продукция',
                'url'  => '/product.main/',
                'icon'  => 'gift',
            );
        }
    }
}