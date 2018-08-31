<?php
namespace Common {
    class Table extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            foreach($data['table']['columns'] as $k => $f)
                if(!isset($f['field']))
                    $data['table']['columns'][$k]['field'] = $f['name'];

            $template = $this->CreateTemplate($facade, $data);
            $key = 'table_' . \Utils::ArrayGet('table.view', $data) . '_';
            $page_limit = \Utils::ArrayGet('table.page_limit', $data);
            $page_number = \Utils::ArrayGet('table.page_number', $data);
            $sort_column = \Utils::ArrayGet('table.sort_column', $data);
            $sort_dir = \Utils::ArrayGet('table.sort_dir', $data);
            $search_string = \Utils::ArrayGet('table.search_string', $data);
            $source = \Utils::ArrayGet('table.source', $data);
            if(empty($source))
                return null;
            if(intval($page_limit) < 20)
                $page_limit = 20;

            $select_fields = array();
            $formatters = array();
            $search_init = false;
            $columns = \Utils::ArrayGet('table.columns', $data);
            array_walk($columns, function($val, $key) use (&$select_fields, $search_string, &$search_init, &$formatters) {
                if(!isset($val['hidden']) || $val['hidden'] !== true)
                    $select_fields[] = $val['field'];
                if(!empty($search_string) && isset($val['searchable']) && $val['searchable']) {
                    $search_str = isset($val['search_fmt']) ? sprintf($val['search_fmt'], $search_string) : '%' . $search_string . '%';
                    $search_op = isset($val['search_op']) ? $val['search_op'] : 'like';
                    if($search_init)
                        \Utils::DB()->orwhere($val['field'], $search_str, $search_op);
                    else {
                        $search_init = true;
                        \Utils::DB()->where($val['field'], $search_str, $search_op);
                    }
                }
            });
            if(is_callable($source)) {
                $items = $source($template, \Utils::DB());
            } else {
                if(empty($sort_dir)) $sort_dir = 'acs';
                if(!empty($sort_column)) {
                    \Utils::DB()->orderBy($sort_column, $sort_dir);
                }
                \Utils::DB()->pageLimit = $page_limit;
                $callback = \Utils::ArrayGet('table.before_query', $data);
                if(is_callable($callback))
                    $callback($template, \Utils::DB());
                $items = \Utils::DB()->arraybuilder()->paginate($source, $page_number, $select_fields);
                $template->Set('table.page_count', \Utils::DB()->totalPages);
            }
            $group_column = \Utils::ArrayGet('table.group_column', $data);
            $groups = array();
            array_walk($items, function($row, $key) use (&$items, &$columns, $group_column, &$groups) {
                foreach($columns as $col) {
                    if($col['field'] != $col['name'])
                        $items[$key][$col['name']] = $items[$key][$col['field']];
                    if(isset($col['formatter']) && is_callable($col['formatter']))
                        $items[$key][$col['name']] = $col['formatter']($items[$key][$col['name']], $items[$key]);
                }
                if(!empty($group_column)) {
                    if(!isset($groups[$items[$key][$group_column]]))
                        $groups[$items[$key][$group_column]] = sizeof($groups);
                    $items[$key]['_group'] = $groups[$items[$key][$group_column]];
                } else {
                    $items[$key]['_group'] = 0;
                }
            });
            if(!empty($groups)) {
                $group_names = array_keys($groups);
                sort($group_names);
                $new_groups = array();
                foreach($group_names as $gn) {
                    $new_groups[] = array(
                        'id'      => $groups[$gn],
                        'name'    => $gn,
                        'display' => true,
                    );
                }
            } else {
                $new_groups = array(array(
                    'id'      => 0,
                    'name'    => 'default',
                    'display' => false,
                ));
            }
            $cv = 0;
            foreach($columns as $c)
                if((!isset($c['hidden']) || !$c['hidden']) && $c['name'] != $group_column)
                    $cv++;
            $actions = \Utils::ArrayGet('table.actions', $data);
            if(!empty($actions))
                $cv++;
            foreach($new_groups as $g) {
                $cn = 1;
                for($i = 0; $i < sizeof($items); $i++)
                    if($g['id'] == $items[$i]['_group']) {
                        $items[$i]['_pos'] = $cn;
                        $cn++;
                    }
            }
            $template->Set('table.groups', $new_groups);
            $template->Set('table.items', $items);
            $template->Set('table.columns_count', sizeof($columns));
            $template->Set('table.columns_visible', $cv);
            return $template->Execute();
        }
    }
}