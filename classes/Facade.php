<?php

/**
 * Класс для формирования пользовательского интерфейса.
 *
 * @encoding UTF-8
 * @author   V.Ponomarev <sbnnlab@gmail.com>
 */
class Facade
{
    private $path;
    private $url;

    public static function Run($view, $data, $path, $url)
    {
        //$response = "<!-- Begin " . $view . " template -->\n";
        $response = self::Execute($view, $data, $path, $url);
        //$response .= "<!-- End " . $view . " template -->\n";
        echo($response);
    }

    public static function Execute($view, $data, $path, $url, $method = 'Execute')
    {
        $view = empty($view) ? 'core.index' : $view;
        $instance = new Facade($path, $url);
        $result = $instance->doExecute($view, $data, $method);
        return $result;
    }

    public function __construct($path, $url)
    {
        $this->path = $path;
        $this->url = $url;
    }

    public function ExecView($view, $data, $method = 'Execute')
    {
        return Facade::Execute($view, $data, $this->getPath(), $this->getUrl(), $method);
    }

    protected function loadView($view)
    {
        $vv = explode('.', $view);
        $module = $vv[0];
        unset($vv[0]);
        $view = implode('/', $vv);
        $v_file = $this->path . "/modules/" . $module . "/views/" . strtolower($view) . ".php";
        $v_object = null;
        if(is_file($v_file)) {
            /** @noinspection PhpIncludeInspection */
            require_once($v_file);
            $v_class = strtolower($module . '\\' . str_replace('.', '\\', $view));
            if(strpos(strtolower(';' . implode(';', get_declared_classes()) . ";"), ';' . $v_class . ';')) {
                $v_object = new $v_class();
            } else {
                echo "Class ".$v_class." not found";
            }
        }
        return $v_object;
    }

    protected function doExecute($view, $data, $method)
    {
        $v_object = $this->loadView($view);
        if(!is_object($v_object)) {
            $v_object = new FacadeDefaultView();
            $v_object->RequestedView = $view;
        }
        return $v_object->$method($this, $data);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getUrl()
    {
        return $this->url;
    }
}

class FacadeView
{

    protected function ProcessFormRequest()
    {
        $action = $_REQUEST['action'];
        if(!empty($action) && is_callable(__CLASS__, $action)) {
            return call_user_func_array(array($this, $action), array());
        }
        return null;
    }

    protected function generateId()
    {
        global $control_id;
        $control_id = intval($control_id) + 1;
        return $control_id;
    }

    public function Execute(Facade $facade, $data)
    {
        return "";
    }

    /**
     * @return Template
     */
    public function CreateTemplate(Facade $facade, $data, $template_name = null)
    {
        if($template_name == null) {
            $name = strtolower(get_class($this));
            $items = explode("\\", $name);
            $template_name = trim(implode('.', $items), '.');
        }
        return new Template($facade, $template_name, $data);
    }
}

class FacadeControlView extends FacadeView
{
    protected function selfName()
    {
        $className = get_called_class();
        $className = strtolower($className);
        $view = explode("_", $className);
        unset($view[count($view)]);
        $view = implode(".", $view);
        return $view;
    }

    protected function getDefaultData()
    {
        return array();
    }

    protected function doExecute(Template $template)
    {

    }

    public function Execute(Facade $facade, $data)
    {
        $template = $this->CreateTemplate($facade, null);
        $data = (isset($data['data'])) ? array_replace_recursive($this->getDefaultData(), $data['data']) : $this->getDefaultData();
        $template->Set('data', $data);
        $this->doExecute($template);
        return $template->Execute();
    }
}

class FacadeDefaultView extends FacadeView
{
    public $RequestedView;

    public function Execute(Facade $facade, $data)
    {
        $template = new Template($facade, $this->RequestedView, $data);
        return $template->Execute();
    }
}