<?php

namespace Stock {
    class Main extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $template = $this->CreateTemplate($facade, $data);
            $template->Set("title", "Сырье");
            $template->Set('title_buttons', array(
                array(
                    'name' => 'Добавить',
                    'url'  => '/stock.edit/',
                    'icon' => 'address-book-o',
                ),
            ));
            $template->Set('table', array(
                'source'        => 'stock',
                'view'          => 'stock.main',
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
                        'href'  => '/stock.edit/?',
                    ),
                    array(
                        'name'  => 'delete',
                        'title' => 'Удалить',
                        'icon'  => 'trash',
                        'id'    => 'id',
                        'href'  => '/stock.main/?action=delete',
                    ),
                ),
                'columns'       => array(
                    array(
                        'name'         => 'id',
                        'title'        => 'Номер',
                        'control'      => 'string',
                        'searchable'   => true,
                        'sortable'     => true,
                        'style'        => 'text-align: center',
                        'header_style' => 'width: 30px;',
                    ),
                    array(
                        'name'       => 'name',
                        'title'      => 'Наименование',
                        'control'    => 'string',
                        'searchable' => true,
                        'sortable'   => true,
                    ),
                ),
            ));
            return $template->Execute();
        }
    }
}