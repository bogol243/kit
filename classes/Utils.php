<?php

/**
 * Вспомогальные функции
 *
 * @encoding UTF-8
 * @author   V.Ponomarev <sbnnlab@gmail.com>
 */
class Utils
{
    const CT_JSON = "application/json";
    const CT_SERIALIZED = "application/php-serialized";
    const CT_ENCODED = "application/php-encoded";
    const CT_COMPRESSED = "application/php-compressed";

    static public $db;
    static private $cache = array();
    static private $modules = array();

    /**
     * @return MysqliDb
     */
    public static function DB()
    {
        return self::$db;
    }

    public static function Request($name, $default = null)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    public static function GetArg($name, $default = null)
    {
        global $argv;
        if(isset($argv) && is_array($argv)) {
            $prev_arg = "";
            $_argv = $argv;
            $_argv[] = 1;
            foreach($_argv as $arg) {
                if($prev_arg == '-' . $name)
                    return $arg;
                $prev_arg = $arg;
            }
        }
        return $default;
    }

    public static function DefVal($val, $def)
    {
        return empty($val) ? $def : $val;
    }

    public static function FormatArray(&$array, $formatter)
    {
        foreach($array as &$item)
            $item = $formatter($item);
    }

    public static function ArrayGet($names, &$array, $default = null, $check_empty = false, $delimeter = '.')
    {
        if(is_array($array)) {
            if(!is_array($names)) {
                $path = explode($delimeter, $names);
                $p = &$array;

                foreach($path as $v) {
                    if(!isset($p[$v])) {
                        return $default;
                    }
                    $p = &$p[$v];
                }

                if($check_empty)
                    if(!empty($p)) return $p;
                    else
                        return $p;
                return $p;
            } else {
                foreach($names as $name) {
                    $res = null;
                    if(self::doArrayGet($name, $array, $res, $check_empty, $delimeter))
                        return $res;
                }
            }
        }
        return $default;
    }

    private static function doArrayGet($name, &$array, &$result, $check_empty, $delimeter)
    {
        $path = explode($delimeter, $name);
        $p = &$array;
        $sz = sizeof($path);
        $result = null;
        for($i = 0; $i < $sz; $i++) {
            if(isset($p[$path[$i]])) {
                $p = &$p[$path[$i]];
                if($i == $sz - 1) {
                    if($check_empty) {
                        if(!empty($p)) {
                            $result = $p;
                            return true;
                        }
                    } else {
                        $result = $p;
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function DecodeDate($date)
    {
        if(intval($date) > 0 && date('Y', intval($date)) >= 2000) {
            return intval($date);
        } else {
            if($d = date_parse($date)) {
                if($d['error_count'] == 0 && $d['year'] > 0)
                    return mktime($d['hour'], $d['minute'], $d['second'], $d['month'], $d['day'], $d['year']);
            }
        }
        return null;
    }

    public static function ForceDirectories($dirName)
    {
        if(strlen($dirName) > 0 && !is_dir($dirName)) {
            $info = pathinfo($dirName);
            if(!is_dir($info["dirname"])) self::ForceDirectories($info["dirname"]);
            if(is_dir($info["dirname"])) {
                mkdir($dirName, 0777);
                chmod($dirName, 0777);
            }
        }
    }

    public static function HexToE32($hex, $Len = 0)
    {
        $arr = array("00000" => "0", "00001" => "1", "00010" => "2", "00011" => "3", "00100" => "4", "00101" => "5",
                     "00110" => "6", "00111" => "7", "01000" => "8", "01001" => "9", "01010" => "A", "01011" => "B",
                     "01100" => "C", "01101" => "D", "01110" => "E", "01111" => "F", "10000" => "G", "10001" => "H",
                     "10010" => "I", "10011" => "J", "10100" => "K", "10101" => "L", "10110" => "M", "10111" => "N",
                     "11000" => "O", "11001" => "P", "11010" => "Q", "11011" => "R", "11100" => "S", "11101" => "T",
                     "11110" => "U", "11111" => "V");
        $count = ceil(strlen($hex) / 8);
        $hex = str_repeat("0", ($count * 8) - strlen($hex)) . $hex;
        $bin = "";
        $part = '00000000';
        for($i = 0; $i < $count; $i++) {
            for($z = 0; $z < 8; $z++) $part{$z} = $hex{$i * 8 + $z};
            //$part = substr($hex, $i * 8, 8);
            $bin = $bin . sprintf("%032s", decbin(hexdec($part)));
        }
        $count = ceil(strlen($bin) / 5);
        $bin = str_repeat("0", ($count * 5) - strlen($bin)) . $bin;
        $e32 = "";
        $part = '00000';
        for($i = 0; $i < $count; $i++) {
            for($z = 0; $z < 5; $z++) $part{$z} = $bin{$i * 5 + $z};
            //$part = substr($bin, $i * 5, 5);
            $char = $arr[$part];
            if($e32 <> '' || $char <> '0')
                $e32 = $e32 . $char;
        }
        if(strlen($e32) < $Len) $e32 = str_repeat("0", $Len - strlen($e32)) . $e32;
        return $e32;
    }

    public static function E32ToHex($e32, $Len = 0)
    {
        $arr = array("0" => "00000", "1" => "00001", "2" => "00010", "3" => "00011", "4" => "00100", "5" => "00101",
                     "6" => "00110", "7" => "00111", "8" => "01000", "9" => "01001", "A" => "01010", "B" => "01011",
                     "C" => "01100", "D" => "01101", "E" => "01110", "F" => "01111", "G" => "10000", "H" => "10001",
                     "I" => "10010", "J" => "10011", "K" => "10100", "L" => "10101", "M" => "10110", "N" => "10111",
                     "O" => "11000", "P" => "11001", "Q" => "11010", "R" => "11011", "S" => "11100", "T" => "11101",
                     "U" => "11110", "V" => "11111");
        $arr1 = array("0000" => "0", "0001" => "1", "0010" => "2", "0011" => "3", "0100" => "4", "0101" => "5",
                      "0110" => "6", "0111" => "7", "1000" => "8", "1001" => "9", "1010" => "A", "1011" => "B",
                      "1100" => "C", "1101" => "D", "1110" => "E", "1111" => "F");
        $bin = "";
        $e32 = strtoupper($e32);
        $count = strlen($e32);
        for($i = 0; $i < $count; $i++) {
            if(array_key_exists($e32{$i}, $arr))
                $bin = $bin . $arr[$e32{$i}];
            else
                $bin = $bin . '0';
        }
        $count = ceil(strlen($bin) / 4);
        $bin = str_repeat("0", ($count * 4) - strlen($bin)) . $bin;
        $hex = "";
        for($i = 0; $i < $count; $i++) {
            $part = substr($bin, $i * 4, 4);
            $char = $arr1[$part];
            if($hex <> '' || $char <> '0')
                $hex = $hex . $char;
        }
        if(strlen($hex) < $Len) $hex = str_repeat("0", $Len - strlen($hex)) . $hex;
        return $hex;
    }

    public static function CreateGUID($namespace = '', $format = true)
    {
        $uid = uniqid("", true);
        $data = $namespace . getmypid();
        if(array_key_exists('REQUEST_TIME', $_SERVER)) $data .= $_SERVER['REQUEST_TIME'];
        if(array_key_exists('HTTP_USER_AGENT', $_SERVER)) $data .= $_SERVER['HTTP_USER_AGENT'];
        if(array_key_exists('LOCAL_ADDR', $_SERVER)) $data .= $_SERVER['LOCAL_ADDR'];
        if(array_key_exists('LOCAL_PORT', $_SERVER)) $data .= $_SERVER['LOCAL_PORT'];
        if(array_key_exists('REMOTE_ADDR', $_SERVER)) $data .= $_SERVER['REMOTE_ADDR'];
        if(array_key_exists('REMOTE_PORT', $_SERVER)) $data .= $_SERVER['REMOTE_PORT'];
        if(array_key_exists('HOSTNAME', $_SERVER)) $data .= $_SERVER['HOSTNAME'];
        $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
        if($format) {
            return '{' .
                substr($hash, 0, 8) .
                '-' .
                substr($hash, 8, 4) .
                '-' .
                substr($hash, 12, 4) .
                '-' .
                substr($hash, 16, 4) .
                '-' .
                substr($hash, 20, 12) .
                '}';
        } else {
            return $hash;
        }
    }

    public static function num2hash($num)
    {
        $hash = md5($num);
        $hex = sprintf('%08x', $num);
        for($i = 0; $i < 8; $i++) {
            $hash{1 + $i * 3} = $hex{7 - $i};
        }
        $hash{29} = $hex{6};
        $hash{31} = $hex{7};
        return $hash;
    }

    public static function hash2num($hash)
    {
        $hex = "00000000";
        for($i = 0; $i < 8; $i++) {
            $hex{7 - $i} = $hash{1 + $i * 3};
        }
        if($hash{29} == $hex{6} && $hash{31} == $hex{7})
            return hexdec($hex);
        else
            return 0;
    }


    public static function ReadIni($data)
    {
        $res = array();
        $lines = explode("\n", str_replace("\r", "", $data));
        $curr_section = 0;
        for($i = 0; $i < sizeof($lines); $i++) {
            if(preg_match("/(.*?)=(.*?)\t/i", trim($lines[$i]) . "\t", $matches)) {
                if(!array_key_exists($curr_section, $res))
                    $res[$curr_section] = array();
                $res[$curr_section][$matches[1]] = $matches[2];
            } elseif(preg_match("/\[(.*?)\]\t/i", trim($lines[$i]) . "\t", $matches)) {
                $curr_section = $matches[1];
            }
        }
        return $res;
    }

    public static function ReadIniFile($path)
    {
        return self::ReadIni(file_get_contents($path));
    }

    public static function WriteIni($data)
    {
        $res = "";
        foreach($data as $section => $items) {
            $res .= '[' . $section . ']' . PHP_EOL;
            foreach($items as $name => $value) {
                $res .= $name . '=' . $value . PHP_EOL;
            }
        }
        return $res;
    }

    public static function DecodeData($content_type, $data, $arr = false)
    {
        if(is_null($content_type)) {
            switch($data{0}) {
                case 'J':
                    return self::DecodeData(self::CT_JSON, substr($data, 1), $arr);
                case 'S':
                    return self::DecodeData(self::CT_SERIALIZED, substr($data, 1), $arr);
                case 'E':
                    return self::DecodeData(self::CT_ENCODED, substr($data, 1), $arr);
                case 'Z':
                    return self::DecodeData(self::CT_COMPRESSED, substr($data, 1), $arr);
            }
        }

        switch($content_type) {
            case self::CT_JSON:
                return @json_decode($data, $arr);
            case self::CT_SERIALIZED:
                return @unserialize($data);
            case self::CT_ENCODED:
                return @unserialize(base64_decode($data));
            case self::CT_COMPRESSED:
                return unserialize(gzuncompress(base64_decode($data)));
            default:
                return null;
        }
    }

    public static function EncodeData($content_type, $data, $add_enctype = false)
    {
        switch($content_type) {
            case self::CT_JSON:
                return ($add_enctype ? 'J' : '') . json_encode($data);
            case self::CT_SERIALIZED:
                return ($add_enctype ? 'S' : '') . serialize($data);
            case self::CT_ENCODED:
                return ($add_enctype ? 'E' : '') . base64_encode(serialize($data));
            case self::CT_COMPRESSED:
                return ($add_enctype ? 'Z' : '') . base64_encode(gzcompress(serialize($data)));
            default:
                return null;
        }
    }

    public static function StrToE32($str)
    {
        $hex = '';
        for($i = 0; $i < strlen($str); $i++)
            $hex .= sprintf('%02x', ord($str{$i}));
        return self::HexToE32($hex);
    }

    public static function E32ToStr($data)
    {
        $hex = self::E32ToHex($data);
        $str = '';
        for($i = 0; $i < strlen($hex); $i = $i + 2)
            $str .= chr(hexdec(substr($hex, $i, 2)));
        return $str;
    }

    public static function ArrayWalk($arr, $procs)
    {
        $ret = array();
        foreach($arr as $n => $v) {
            foreach($procs as $f => $p) {
                if(is_callable($p) && array_key_exists($f, $v))
                    $v[$f] = $p($v[$f]);
            }
            $ret[$n] = $v;
        }
        return $ret;
    }


    /**
     * Проверяет входит ли значение $value в диапазон $range
     * @param $value - mixed значение
     * @param $range - array(2) of mixed
     * @param null $cmpFunc - callable function($a,$b) return $a>b 1 $a==$b 0 else -1
     * @return bool|null
     * @internal param mixed $value
     * @internal param array $range
     */
    static public function Between($value, $range, $cmpFunc = null)
    {
        if(!is_scalar($value) && !is_callable($cmpFunc))
            return null;

        if(self::ArrayGet(0, $range) == 0 || self::ArrayGet(1, $range) == 0)
            return null;

        if(is_callable($cmpFunc))
            if($cmpFunc($value, $range[0]) >= 0 && $cmpFunc($value, $range[1]) <= 0)
                return true;
            else
                return false;

        if($range[0] >= $value && $range[1] <= $value)
            return true;
        else
            return false;

    }

    /**
     * Получение битовой маски для значений (все значения должны быть целыми числами)
     * @params int
     * @return int
     */
    static public function CreateBitMask()
    {
        $mask = 0;
        for($i = 0; $i < func_num_args(); $i++) {
            $mask |= 1 << (int)func_get_arg($i);
        }
        return $mask;
    }

    /**
     * Проверяет битовую маску
     * @param $mask - маска
     * @param $val - значение, которое требуется проверить
     * @return bool
     */
    static public function CheckBitMask($mask, $val)
    {
        return (bool)($mask & 1 << $val);
    }

    public static function GetTableFieldByID($table, $id_field, $id_val, $ret_field)
    {
        $arr = &self::$cache;
        foreach(array($table, $id_field, $ret_field) as $p) {
            if(!isset($arr[$p])) $arr[$p] = array();
            $arr = &$arr[$p];
        }
        if(!isset($arr[$id_val])) {
            self::DB()->where($id_field, $id_val);
            $arr[$id_val] = self::DB()->getValue($table, $ret_field);
        }
        return $arr[$id_val];
    }

    public static function CreateShortName($full_name)
    {
        $pp = explode(' ', $full_name);
        if(sizeof($pp) >= 3) {
            $r = $pp[0] . " " . mb_substr($pp[1], 0, 1) . ". " . mb_substr($pp[2], 0, 1) . ".";
            unset($pp[0]);
            unset($pp[1]);
            unset($pp[2]);
            return trim($r . " " . implode(" ", $pp));
        } else {
            return $full_name;
        }
    }

    public static function GetTableDict($table, $id, $value)
    {
        $res = self::DB()->arrayBuilder()->get($table, null, array($id, $value));
        $arr = array();
        foreach($res as $r)
            $arr[$r[$id]] = $r[$value];
        return $arr;
    }

    public static function Modules()
    {
        if(empty(self::$modules)) {
            $path = ROOT_PATH . '/modules/';
            $dir = dir($path);
            while($item = $dir->read()) {
                if(is_dir($path . $item) && $item{0} != '.') {
                    if(is_file($path . $item . '/module.php')) {
                        require_once($path . $item . '/module.php');
                        $class = $item . '\\Module';
                        if(class_exists($class)) {
                            self::$modules[$item] = array(
                                'name'   => $item,
                                'path'   => $path . $item . '/',
                                'module' => new $class,
                            );
                        }
                    }
                }
            }
            $dir->close();
        }
        return self::$modules;
    }
}

