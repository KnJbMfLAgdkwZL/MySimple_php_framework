<?php

class Application
{
    public function run()
    {
        //Loading the default page
        if (empty($_GET)) {
            $siteController = new SiteController();
            $siteController->index();
            return;
        }

        $arr = $this->getRequest();
        if (isset($arr)) {
            $classController = '';
            $method = '';

            if (isset($arr['controller'])) {
                $classController = $arr['controller'];
            }
            if (isset($arr['method'])) {
                $method = $arr['method'];
            }

            $classController = $this->findController($classController);
            if ($classController) {
                if (method_exists($classController, $method)) {

                    $classController = new $classController();
                    if (in_array($method, $classController->allowed)) {
                        $classController->$method();
                    }

                    return;
                }
            }
        }

        //Loading the 404 page
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
        $siteController = new SiteController();
        $siteController->page404();
    }

    private function getRequest()
    {

        if (!empty($_GET)) {
            $get = $this->secureVar($_GET);
            if (isset($get['r'])) {
                $r = $get['r'];
                $r = mb_split('/', $r);
                if (isset($r[0])) {
                    if (isset($r[1])) {
                        return ['controller' => $r[0], 'method' => $r[1]];
                    }
                }
            }
        }
        return false;
    }

    private function findController($controller)
    {
        $controller = ucfirst($controller);
        $controller .= 'Controller';

        $s = DIRECTORY_SEPARATOR;
        if ($this->findFile(__DIR__ . "{$s}..{$s}controllers", "{$controller}.php")) {
            if (class_exists($controller)) {
                return $controller;
            }
        }
        return false;
    }

    private function findFile($path, $name)
    {
        $name = mb_strtolower($name);
        $mass = scandir($path);
        foreach ($mass as $v) {
            $v = mb_strtolower($v);
            if ($v == $name) {
                return true;
            }
        }
        return false;
    }

    public function secureVar($var)
    {
        if (is_array($var)) {
            foreach ($var as $k => &$v) {
                $v = strip_tags($v);
                $v = htmlspecialchars($v, ENT_QUOTES);
                $v = stripslashes($v);
                $v = trim($v);
            }
        } else {
            $v = &$var;
            $v = strip_tags($v);
            $v = htmlspecialchars($v, ENT_QUOTES);
            $v = stripslashes($v);
            $v = trim($v);
        }
        return $var;
    }

}