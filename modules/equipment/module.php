<?php

namespace Equipment {
    class Module extends \Module
    {
        const Name = 'equipment';
        const Title = 'Рабочие центры';

        function GetOrder()
        {
            return 10;
        }

        function BuildMenu(&$menu)
        {
            parent::BuildMenu($menu);
            $menu[] = array(
                'name' => self::Title,
                'url'  => '/'.self::Name.'.main/',
                'icon' => 'cog',
            );
        }

        public static function getStates()
        {
            return array(
                0 => array(
                    'name' => 'Выключен',
                ),
                1 => array(
                    'name' => 'Работает',
                ),
                2 => array(
                    'name' => 'Остановлен',
                ),
            );
        }
    }
}