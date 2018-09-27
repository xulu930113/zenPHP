<?php

/**
 *
 * @package    core
 * @author     Shaun.Xu
 * @since      2017/12/28 16:43
 */
class zenphp
{
    public static $rootPath         = '/';              //应用程序根路径
    public static $defaultModule    = "index";          //默认module
    public static $defaultControll  = "index";          //默认Controller
    public static $defaultAction    = "index";          //默认action
    public static $load_file        = array();          //加载的文件

    /**
     * 运行
     * run
     *
     * @author Shaun.Xu
     * @access public
     * @return void
     */
    public static function run(){
        $prepend = false;
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        }
        self::route();
    }

    /**
     * 自动加载类
     * autoload
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $class
     *
     * @return void
     */
    public static function autoload($class){
        if ($class) {
            if(!in_array($class , self::$load_file)){
                self::$load_file[] = $class;
                $file = str_replace('\\', '/', $class);
                $AppFile = ROOT."/".$file.".php";
                if(file_exists($AppFile)){
                    require_once($AppFile);
                }
            }
        }
    }

    /**
     * 路由设置
     * route
     *
     * @author Shaun.Xu
     * @access public
     * @return void
     */
    private static function route(){
        $fullUrl   = $_SERVER['REQUEST_URI'];
        $filename  = $_SERVER['SCRIPT_NAME'];
        $getArgs   = $_SERVER['QUERY_STRING'];
        $searchStr = array($filename,'?'.$getArgs,'index.php');
        $url       = str_replace($searchStr,'',$fullUrl);
        $url       = explode('/',$url);
        $urltemp   = array();
        foreach ( $url as $k => $v ) {
            if ( $v !=null ) {
                $urltemp[] = $v;
            }
        }
        if(empty($urltemp[0])) $urltemp[0] = self::$defaultModule ;
        if(empty($urltemp[1])) $urltemp[1] = self::$defaultControll;
        if(empty($urltemp[2])) $urltemp[2] = self::$defaultAction;
        if(!is_dir(PROJECT_DIR."/".$urltemp[0])){
            exit("未找到模块：".$urltemp[0]);
        }
        if(!file_exists(PROJECT_DIR."/".$urltemp[0]."/Controller/".$urltemp[1].".php")){
            exit("未找到控制器：".$urltemp[1]);
        }
        $controllerClassName = "\\".PROJECT."\\".$urltemp[0]."\\Controller\\".$urltemp[1];
        if(!class_exists($controllerClassName)){
            exit("未找到控制器：".$urltemp[1]);
        }
        define("Module_name",$urltemp[0]);
        define("Controller_name",$urltemp[1]);
        define("Action_name",$urltemp[2]);
        $controller = new $controllerClassName();
        if ( !method_exists($controller, $urltemp[2]) ){
            exit("未定义的操作：".$urltemp[2]);
        }
        call_user_func(array($controller,$urltemp[2]));
    }



}