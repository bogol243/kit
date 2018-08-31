<?php
namespace Common {
    class Menu extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $template = $this->CreateTemplate($facade, $data);
            $items = array();
            $modules = \Utils::Modules();
            $order = array();
            foreach($modules as $m => $module) {
                $order[$m] = $module['module']->GetOrder();
            }
            asort($order);
            foreach($order as $m => $o) {
                $modules[$m]['module']->BuildMenu($items);
            }
            $template->Set('items', $items);
            return $template->Execute();
        }
    }
}