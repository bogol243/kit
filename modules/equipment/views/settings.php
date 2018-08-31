<?php

namespace Equipment {
    class Settings extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $template = $this->CreateTemplate($facade, $data);
            $template->Set("title", Module::Title.": Настройки");
            $template->Set('title_buttons', array());
            return $template->Execute();
        }
    }
}