<?php

namespace Common {
    class Form extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $source = \Utils::ArrayGet('form.source', $data);
            $form_id = \Utils::ArrayGet('form.id', $data);
            $record_id = \Utils::ArrayGet('form.record_id', $data);
            if(empty($form_id)) {
                $form_id = 'f' . substr(\Utils::CreateGUID('', false), 0, 8);
                $data['form']['id'] = $form_id;
            }
            foreach($data['form']['fields'] as $n => $f)
                $data['form']['fields'][$n]['form_id'] = $form_id;

            if($record_id) {
                if(is_callable($source)) {
                    $rec = $source($record_id, $facade, $data);
                } else {
                    $select_fields = array($data['form']['id_field']);
                    foreach($data['form']['fields'] as $n => $f) {
                        if(isset($f['fields']) && is_array($f['fields'])) {
                            foreach($f['fields'] as $ff)
                                if(!in_array($ff, $select_fields))
                                    $select_fields[] = $ff;
                        } else if(isset($f['name']) && !in_array($f['name'], $select_fields)) {
                            $select_fields[] = $f['name'];
                        }
                    }
                    \Utils::DB()->where($data['form']['id_field'], $record_id, '=');
                    $rec = \Utils::DB()->arrayBuilder()->get($data['form']['source'], null, $select_fields);
                    if(sizeof($rec) > 0)
                        $rec = $rec[0];
                }
                if(!empty($rec)) {
                    foreach($data['form']['fields'] as $n => $f) {
                        if(isset($f['formatter']) && is_callable($f['formatter']))
                            $data['form']['fields'][$n]['value'] = htmlentities($f['formatter']($rec, $data));
                        else if(isset($f['name']) && array_key_exists($f['name'], $rec))
                            $data['form']['fields'][$n]['value'] = htmlentities($rec[$f['name']]);
                    }
                }
            }

            $template = $this->CreateTemplate($facade, $data);
            return $template->Execute();
        }

        function SaveData(\Facade $facade, $data)
        {
            $upd = array();
            $id = intval(\Utils::ArrayGet('id', $data['data']));
            foreach($data['form']['fields'] as $n => $f) {
                $val = \Utils::ArrayGet($f['name'], $data['data']);
                if(isset($f['callback']) && is_callable($f['callback'])) {
                    $f['callback']($f, $val, $upd, $id, $data);
                } else if(substr($f['name'], 0, 1) != '_') {
                    $upd[$f['name']] = $val;
                }
            }
            if($id > 0) {
                \Utils::DB()->where($data['form']['id_field'], $id, '=')->update($data['form']['source'], $upd);
            } else {
                $id = \Utils::DB()->insert($data['form']['source'], $upd);
            }
            if(!is_array($data['add_data']))
                $data['add_data'] = array();
            return json_encode(array_merge($data['add_data'], array(
                'result' => 'success',
                'data'   => array(
                    'id' => $id,
                ),
            )));
        }
    }
}