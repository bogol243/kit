<?php

class Template
{
    private $tpl_name;
    private $tpl_file;
    private $tpl_c_name;
    private $tpl_c_file;
    private $tpl_c_func;
    private $del_l = '{{';
    private $del_r = '}}';
    private $facade;
    private $for_depth;
    private $for_rows;
    private $for_names;
    private $defaultFormatter = null;

    private $_tpl_var;

    public function __construct(Facade $facade, $name, $data = null)
    {
        $name = trim(strtolower($name));
        $name = preg_replace('/\.+/', '.', $name);
        $this->for_rows = array();
        $this->for_names = array();
        $this->for_depth = 0;
        $this->facade = $facade;
        $this->tpl_name = $name;
        $files = array();
        $vv = explode('.', $this->tpl_name);
        $module = $vv[0];
        unset($vv[0]);
        $files[] = implode('/', $vv);
        foreach($files as $file) {
            $this->tpl_file = $facade->getPath() . '/modules/' . $module . '/templates/' . $file . '.tpl';
            if(file_exists($this->tpl_file))
                break;
        }
        $this->tpl_c_name = $this->tpl_name;
        $this->tpl_c_name = md5($this->tpl_file);
        $this->tpl_c_file = $facade->getPath() . '/cache/.ht.' . $this->tpl_name . '.php';
        $this->tpl_c_func = 'tpl_' . $this->tpl_c_name;
        // Basic vars
        $this->_tpl_var = $data;
        if(!is_array($this->_tpl_var)) $this->_tpl_var = array();
        if(!array_key_exists('globals', $this->_tpl_var)) $this->_tpl_var['globals'] = array();
        if(!array_key_exists('get', $this->_tpl_var['globals'])) $this->_tpl_var['globals']['get'] = &$_GET;
        if(!array_key_exists('post', $this->_tpl_var['globals'])) $this->_tpl_var['globals']['post'] = &$_POST;
        if(!array_key_exists('sess_name', $this->_tpl_var['globals'])) $this->_tpl_var['globals']['sess_name'] = (session_status() == PHP_SESSION_ACTIVE) ? session_name() : null;
        if(!array_key_exists('sess_id', $this->_tpl_var['globals'])) $this->_tpl_var['globals']['sess_id'] = (session_status() == PHP_SESSION_ACTIVE) ? session_id() : null;
        if(!array_key_exists('rnd', $this->_tpl_var['globals'])) $this->_tpl_var['globals']['rnd'] = mt_rand();
        if(array_key_exists('HTTP_USER_AGENT', $_SERVER) && !array_key_exists('agent', $this->_tpl_var['globals'])) {
            $this->_tpl_var['globals']['agent'] = array(
                'name' => $_SERVER['HTTP_USER_AGENT'],
                'kind' => 'UNKNOWN',
            );
            $ag = array(
                'IE'      => 'msie',
                'OPERA'   => 'opera',
                'FIREFOX' => 'firefox',
                'CHROME'  => 'chrome',
            );
            foreach($ag as $br => $str) {
                if(stripos($_SERVER['HTTP_USER_AGENT'], $str) > 0) {
                    $this->_tpl_var['globals']['agent']['kind'] = $br;
                    break;
                }
            }
        }
        $this->_tpl_var['template'] = array(
            'name' => $name,
            'url'  => $this->getFacadeUrl(),
        );
    }

    public function getFacadeUrl()
    {
        return $this->getFacade()->getUrl();
    }

    public function getFacade()
    {
        return $this->facade;
    }

    public function getData()
    {
        return $this->_tpl_var;
    }

    static public function PreventXSS($var)
    {
        if(!is_array($var)) {
            $var = htmlspecialchars($var);
            return $var;
        } else {
            foreach($var as &$v)
                $v = Template::PreventXSS($v);
            return $var;
        }
    }

    public function XSSPreventEnable()
    {
        $this->SetDefaultVarFormatter("Template::PreventXSS");
    }

    public function SetDefaultVarFormatter($formatterCB)
    {
        $this->defaultFormatter = $formatterCB;
    }

    private function GetDefaultVarFormatter()
    {
        return $this->defaultFormatter;
    }

    public function Get($name)
    {
        return Utils::ArrayGet($name, $this->_tpl_var);
    }

    public function Set($name, $var, $formatterCB = null)
    {
        if(is_callable($formatterCB)) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $var = $formatterCB($var);
        } elseif(is_callable($this->GetDefaultVarFormatter())) {
            $defFormatter = $this->GetDefaultVarFormatter();
            /** @noinspection PhpUnusedLocalVariableInspection */
            $var = call_user_func_array($defFormatter, array($var));
        }

        $name = explode('.', strtolower($name));
        $idx = sizeof($name) - 1;
        $last = $name[$idx];
        $suff = '';
        if(strlen($last) > 2) {
            if(substr($last, strlen($last) - 2) == '[]') {
                $name[$idx] = substr($last, 0, strlen($last) - 2);
                $suff = '[]';
            }
        }
        $code = '$this->_tpl_var[\'' . implode('\'][\'', $name) . '\']' . $suff . ' = $var;';
        eval($code);
        return $this;
    }

    private function compile_parse_vars_preg($block)
    {
        $block = strtolower(@$block[1]);
        $block = explode('.', $block);
        $var_name = array_shift($block);
        $var_path = implode('.', $block);

        for($i = 0; $i < $this->for_depth; $i++) {
            if($this->for_names[$i] == '$' . $var_name) {
                return '(isset($' . $var_name . ') ? $' . $var_name . ' : null)';
            } elseif($this->for_rows[$i] == '$' . $var_name) {
                if(empty($var_path))
                    return '(isset($' . $var_name . ') ? $' . $var_name . ' : null)';
                else
                    return 'Utils::ArrayGet(\'' . $var_path . '\', $' . $var_name . ', null)';
            }
        }

        return 'Utils::ArrayGet(\'' . $var_name . (empty($var_path) ? '' : '.' . $var_path) . '\', $__tpl_data, null)';
    }

    private function compile_parse_vars($block)
    {
        // Compile vars PREG
        return preg_replace_callback('/\$([a-z0-9_.]+)/uims', array($this, 'compile_parse_vars_preg'), $block);
    }

    private function compile_array($from, $step, $to)
    {
        if(substr($from, 0, 1) == '$')
            $from = $this->compile_parse_vars($from);
        else
            $from = intval($from);
        if(substr($step, 0, 1) == '$')
            $step = $this->compile_parse_vars($step);
        else
            $step = intval($step);
        if(substr($to, 0, 1) == '$')
            $to = $this->compile_parse_vars($to);
        else
            $to = intval($to);
        return '$__tpl->create_array(' . $from . ', ' . $step . ', ' . $to . ')';
    }

    public function create_array($from, $step, $to)
    {
        $res = array($from);
        $next = $from;
        while(sizeof($res) < 1000) {
            if($from > $to) {
                $next = $next - $step;
                if($next < $to)
                    break;
            } else {
                $next = $next + $step;
                if($next > $to)
                    break;
            }
            $res[] = $next;
        }
        return $res;
    }

    private function compile_block($block)
    {
        $block = @$block[1];
        // IF construction?
        if(strtolower(substr($block, 0, 2)) == 'if') {
            // Yep!
            $block = '<?php if (' . $this->compile_parse_vars(trim(substr($block, 2))) . ') { ?>';
            return $block;
        }
        // End IF construction?
        if(strtolower(trim($block)) == '/if') {
            $block = '<?php } ?>';
            return $block;
        }
        if(strtolower(trim($block)) == 'else') {
            $block = '<?php } else { ?>';
            return $block;
        }

        // FOR construction?
        if(strtolower(substr($block, 0, 3)) == 'for') {
            $pp = strpos($block, ' as ');
            $subj = 'undefined';
            $row = 'undefined';
            if($pp) {
                $subj_str = trim(substr($block, 3, $pp - 3));
                if(preg_match('#^([\$0-9a-z_\-\.]+)\s?\.\.\s?([\$0-9a-z_\-\.]+)\s?\.\.\s?([\$0-9a-z_\-\.]+)$#ims', $subj_str, $subj_mm)) {
                    $subj = $this->compile_array($subj_mm[1], $subj_mm[2], $subj_mm[3]);
                } elseif(preg_match('#^([\$0-9a-z_\-\.]+)\s?\.\.\s?([\$0-9a-z_\-\.]+)$#ims', $subj_str, $subj_mm)) {
                    $subj = $this->compile_array($subj_mm[1], 1, $subj_mm[2]);
                } else {
                    $subj = $this->compile_parse_vars($subj_str);
                }
                $row = trim(substr($block, $pp + 4));
            }
            $row_items = explode('=>', $row);
            if(sizeof($row_items) < 2) {
                $row_items[1] = trim($row_items[0]);
                $row_items[0] = null;
            } else {
                $row_items[0] = trim($row_items[0]);
                $row_items[1] = trim($row_items[1]);
            }
            $this->for_names[$this->for_depth] = $row_items[0];
            $this->for_rows[$this->for_depth] = $row_items[1];
            $this->for_depth++;

            $block = '<?php if(is_array(' . $subj . ')) { foreach (' . $subj . ' as ' . $row . ') { ?>';
            return $block;
        }
        // End FOR construction?
        if(strtolower(trim($block)) == '/for') {
            $this->for_depth--;
            $block = '<?php }} ?>';
            return $block;
        }

        if(strtolower(substr($block, 0, 7)) == 'include') {
            $block = trim(str_replace("\t", " ", $block));
            $block = trim(str_replace("\r", "", $block));
            $block = trim(str_replace("\n", " ", $block));
            while(strpos($block, "  ")) $block = str_replace("  ", " ", $block);
            if(preg_match('#include (.*?) add (.*?) as (.*?)$#ims', $block, $match)) {
                $block = '<?php Facade::Run(' . $this->compile_parse_vars($match[1]) . ', array_merge($__tpl_data, array(' . $this->compile_parse_vars($match[3]) . ' => ' . $this->compile_parse_vars($match[2]) . ')), $__tpl->getFacade()->getPath(), $__tpl->getFacade()->getUrl()); ?>';
                return $block;
            } elseif(preg_match('#include (.*?)$#ims', $block, $match)) {
                $block = '<?php Facade::Run(' . $this->compile_parse_vars($match[1]) . ', $__tpl_data, $__tpl->getFacade()->getPath(), $__tpl->getFacade()->getUrl()); ?>';
                return $block;
            }
        }
        if(strtolower(substr($block, 0, 1)) == ':') {

            $block = '<?php echo CORE::Str("' . trim(substr($block, 1)) . '"); ?>';
            return $block;
        }
        // Default - show the var
        $block = '<?php echo ' . $this->compile_parse_vars(trim($block)) . '; ?>';
        return $block;
    }

    private function compile()
    {
        // Delete old TPL file...
        if(file_exists($this->tpl_c_file)) {
            @unlink($this->tpl_c_file);
        }

        $file_out = '<?php' . PHP_EOL;
        $file_out .= '/*' . PHP_EOL;
        $file_out .= '    Kblog simple compiled template.' . PHP_EOL;
        $file_out .= '    This script is generated, do not modify.' . PHP_EOL;
        $file_out .= '    Compiled: ' . date('d.m.Y H:i:s') . '' . PHP_EOL;
        $file_out .= '    TPL file: /' . $this->tpl_name . '.tpl' . PHP_EOL;
        $file_out .= '*/' . PHP_EOL;
        $file_out .= 'function ' . $this->tpl_c_func . '(Template $__tpl, &$__tpl_data){' . PHP_EOL;
        $file_out .= '?>' . PHP_EOL;

        // Get original template...
        $tpl = file_get_contents($this->tpl_file);
        $tpl = preg_replace_callback('/' . preg_quote($this->del_l, '/') . '(.*)' . preg_quote($this->del_r, '/') . '(\r?\n)?/Uuims', array($this, 'compile_block'), $tpl);
        $file_out .= $tpl;
        $file_out .= '<?php' . PHP_EOL;
        $file_out .= '} // ' . $this->tpl_c_func . PHP_EOL;
        $file_out .= '?>' . PHP_EOL;
        //echo $tpl;
        //return;
        $f = fopen($this->tpl_c_file, 'w');
        if(!$f) {
            throw new Exception('Unable to write compiled template (' . $this->tpl_c_file . ')!');
        }
        fputs($f, $file_out);
        fclose($f);
    }

    public function Execute()
    {
        // Tpl exists?
        if(!file_exists($this->tpl_file)) {
            return sprintf("<br/>Template %s not found!\r\n", $this->tpl_name);
        }

        // Has compiled tpl?
        if(!file_exists($this->tpl_c_file) || filemtime($this->tpl_c_file) < filemtime($this->tpl_file) || filemtime($this->tpl_c_file) < filemtime(__FILE__)) {
            // No! Need to compile!
            $this->compile();
        }

        ob_start();
        try {
            /** @noinspection PhpIncludeInspection */
            require_once($this->tpl_c_file);
            $func = $this->tpl_c_func;
            $func($this, $this->_tpl_var);
            $out = ob_get_contents();
            ob_end_clean();
            return $out;
        } catch(Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }
}

