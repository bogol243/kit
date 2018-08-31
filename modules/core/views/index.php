<?php
namespace Core {
   class Index extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $template = $this->CreateTemplate($facade, $data);
            $template->Set("title", "Сводная информация");
            return $template->Execute();
        }
    }
}