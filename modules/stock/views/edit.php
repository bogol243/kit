<?php
namespace Stock {

    class Edit extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $action = \Utils::Request('action');
            switch($action) {
                case 'save':
                    return $this->saveForm($facade, $data);
                case 'save_ret':
                    return $this->saveForm($facade, $data, array('location' => '/stock.main/'));
                case 'save_add':
                    return $this->saveForm($facade, $data, array('location' => '/stock.edit/'));
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
                $row = \Utils::DB()->where('id', $id)->getOne('stock', array('name'));
                $title = implode(' ', $row);
            } else {
                $title = 'Новое сырье';
            }
            $template = $this->CreateTemplate($facade, $data);
            $template->Set('title', $title);
            $template->Set('form', $this->getForm($id));
            return $template->Execute();
        }

        private function getForm($record_id = 0)
        {
            return array(
                'view'      => 'stock.edit',
                'source'    => 'stock',
                'id_field'  => 'id',
                'well'      => true,
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