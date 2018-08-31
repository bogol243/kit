<?php

namespace Core {
    class Login extends \FacadeView
    {
        function Execute(\Facade $facade, $data)
        {
            $template = $this->CreateTemplate($facade, $data);
            if(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) == 'login') {
                if($this->doLogin()) {
                    header('Location:'.ROOT_URL);
                    return null;
                } else
                    $template->Set('error', true);
            }
            return $template->Execute();
        }

        private function doLogin()
        {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $passwd = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);
            $passwd = md5($passwd);

            $query = "SELECT * FROM user WHERE login='$username' AND password='$passwd'";
            $row = \Utils::DB()->query($query);

            if(count($row) >= 1) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_name'] = $row[0]['name'];
                $_SESSION['user_login'] = $row[0]['login'];
                $_SESSION['user_id'] = $row[0]['id'];
                return true;
            } else {
                return false;
            }
        }
    }
}