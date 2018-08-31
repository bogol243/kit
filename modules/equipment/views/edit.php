<?php
namespace Equipment {

    class Edit extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $action = \Utils::Request('action');
            switch($action) {
                case 'save':
                    return $this->saveForm($facade, $data);
                case 'save_ret':
                    return $this->saveForm($facade, $data, array('location' => '/'.Module::Name.'.main/'));
                case 'save_add':
                    return $this->saveForm($facade, $data, array('location' => '/'.Module::Name.'.edit/'));
                default:
                    return $this->showForm($facade, $data);
            }
        }

        private function saveForm(\Facade $facade, $data, $add_data = null)
        {
            return $facade->ExecView('common.form', array('data' => $_POST, 'add_data' => $add_data, 'form' => $this->getForm(intval(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING)))), 'SaveData');
        }

        private function showForm(\Facade $facade, $data)
        {
            $id = intval(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING));
            if($id) {
                $row = \Utils::DB()->where('id', $id)->getOne('equipment', array('name'));
                $title = implode(' ', $row);
            } else {
                $title = 'Новое оборудование';
            }
            $template = $this->CreateTemplate($facade, $data);
            $template->Set('title', $title);
            $template->Set('form', $this->getForm($id));
            return $template->Execute();
        }

        private function getForm($record_id = 0)
        {
            return array(
                'view'      => Module::Name.'.edit',
                'source'    => 'equipment',
                'id_field'  => 'id',
                'record_id' => $record_id,
                'buttons'   => array(
                    array(
                        'type'  => 'submit',
                        'name'  => 'action',
                        'value' => 'save',
                        'title' => 'Сохранить',
                        'icon'  => 'save',
                    ),
                    array(
                        'type'  => 'submit',
                        'name'  => 'action',
                        'value' => 'save_ret',
                        'title' => 'Сохранить и закрыть',
                        'icon'  => 'reply-all',
                    ),
                    array(
                        'type'  => 'submit',
                        'name'  => 'action',
                        'value' => 'save_add',
                        'title' => 'Сохранить и создать',
                        'icon'  => 'retweet',
                    ),
                ),
                'fields'    => array(
                    array(
                        'name'    => 'name',
                        'title'   => 'Название',
                        'icon'    => 'edit',
                        'control' => 'common.controls.text',
                    ),
                ),
            );
        }
    }
}