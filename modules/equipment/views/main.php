<?php

namespace Equipment {
    class Main extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $template = $this->CreateTemplate($facade, $data);
            $template->Set("title", Module::Title);
            $template->Set('title_buttons', array(
                array(
                    'name' => 'Добавить',
                    'url'  => '/'.Module::Name.'.edit/',
                    'icon' => 'address-book-o',
                ),
                array(
                    'name' => 'Простои',
                    'url'  => '/'.Module::Name.'.edit/',
                    'icon' => 'ambulance',
                ),
                array(
                    'name' => 'Настройки',
                    'url'  => '/'.Module::Name.'.settings/',
                    'icon' => 'cog',
                ),
            ));
            $template->Set('table', array(
                'source'        => Module::Name,
                'view'          => ''.Module::Name.'.main',
                'filter'        => true,
                'page_limit'    => \Utils::DefVal(filter_input(INPUT_GET, 'page_limit', FILTER_SANITIZE_STRING), 50),
                'page_number'   => \Utils::DefVal(filter_input(INPUT_GET, 'page_number', FILTER_SANITIZE_STRING), 1),
                'search_string' => filter_input(INPUT_GET, 'search_string', FILTER_SANITIZE_STRING),
                'sort_column'   => \Utils::DefVal(filter_input(INPUT_GET, 'sort_column', FILTER_SANITIZE_STRING), 'name'),
                'sort_dir'      => \Utils::DefVal(filter_input(INPUT_GET, 'sort_dir', FILTER_SANITIZE_STRING), 'asc'),
                'actions'       => array(
                    array(
                        'name'  => 'edit',
                        'title' => 'Редактировать',
                        'icon'  => 'edit',
                        'id'    => 'id',
                        'href'  => '/'.Module::Name.'.edit/?',
                    ),
                    array(
                        'name'  => 'delete',
                        'title' => 'Удалить',
                        'icon'  => 'trash',
                        'id'    => 'id',
                        'href'  => '/'.Module::Name.'.main/?action=delete',
                    ),
                ),
                'columns'       => array(
                    array(
                        'name'         => 'rowid',
                        'field'        => 'id',
                        'title'        => 'Номер',
                        'control'      => 'string',
                        'searchable'   => true,
                        'sortable'     => true,
                        'style'        => 'text-align: center',
                        'header_style' => 'width: 30px;',
                        'formatter'    => function($val, $row) {
                            return '<a href="' . ROOT_URL . '/'.Module::Name.'.edit/?id=' . $row['id'] . '">' . $row['id'] . '</a>';
                        },
                    ),
                    array(
                        'name'       => 'link',
                        'field'      => 'name',
                        'title'      => 'Наименование',
                        'control'    => 'string',
                        'searchable' => true,
                        'sortable'   => true,
                        'formatter'    => function($val, $row) {
                            return '<a href="' . ROOT_URL . '/'.Module::Name.'.edit/?id=' . $row['id'] . '">' . $row['name'] . '</a>';
                        },
                    ),
                    array(
                        'name'       => 'state',
                        'title'      => 'Состояние',
                        'control'    => 'string',
                        'searchable' => true,
                        'sortable'   => true,
                        'style'      => 'text-align: center',
                        'formatter'  => function($val) {
                            $states = \Equipment\Module::getStates();
                            if(isset($states[$val]))
                                return $states[$val]['name'];
                            else
                                return "Unknown state (" . $val . ")";
                        },
                    ),
                ),
            ));
            return $template->Execute();
        }
    }
}