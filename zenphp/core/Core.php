<?php
/**
 *
 * @package    core
 * @author     Shaun.Xu
 * @since      2018/1/12 16:56
 */

namespace zenphp\core;


use zenphp\cache\fileCache;
use zenphp\cache\redisCache;

class Core
{
    protected $thisApp;         //项目目录
    protected $thisModule;      //执行的Module
    private $lodeConfigArr;     //加载的配置文件

    function __construct()
    {
        $callClass = get_called_class();
        $callClassArr = $this->classAnalysis($callClass);
        $this->thisModule = $callClassArr[1];
        $this->thisApp = $callClassArr[0];
    }

    /**
     * 解析class结构
     * calssAnalysis
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $class
     *
     * @return array
     */
    private function classAnalysis($class){
        $classExp = explode('\\',$class);
        $classArr = array();
        foreach ($classExp as $val){
            if(!empty($val) && $val != null && $val != ""){
                $classArr[] = $val;
            }
        }
        return $classArr;
    }

    /**
     * 获取IP地址
     * getIP
     *
     * @author Shaun.Xu
     * @access public
     * @return array|false|string
     */
    public function getIP(){
        if (getenv("HTTP_CLIENT_IP")){
            $ip = getenv("HTTP_CLIENT_IP");
        } else if(getenv("HTTP_X_FORWARDED_FOR")){
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if(getenv("REMOTE_ADDR")){
            $ip = getenv("REMOTE_ADDR");
        }else{
            $ip = "Unknow";
        }
        return $ip;
    }

    /**
     * 获取数据
     * input
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $var
     *
     * @return bool
     */
    public function input($var){
        if(isset($_GET[$var])){
            return $_GET[$var];
        }elseif(isset($_POST[$var])){
            return $_POST[$var];
        }else{
            return false;
        }
    }

    /**
     * 返回数据
     * returnData
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param int   $code       错误码
     * @param array $data       返回数据数据
     * @param int   $type       返回数据类型--0：json,1：xml
     *
     * @return void
     */
    public function returnData($code = 0 , $data = array(),  $type = 0){
        $errorcodes = $this->lodeConfig("errorcode");
        $msg = "";
        if(isset($errorcodes[$code])){
            $msg = $errorcodes[$code];
        }
        $result["code"] = $code;
        $result["msg"] = $msg;
        $result["data"] = $data;
        if($type == 0){
            $returnData = json_encode($result);
        }else{
            $returnData = $this->returnXml($result);
        }
        exit($returnData);
    }

    /**
     * 返回XML数据
     * returnXml
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param     $arr
     * @param int $dom
     * @param int $item
     *
     * @return string
     */
    private function returnXml($arr,$dom=0,$item=0){
        if (!$dom){
            $dom = new \DOMDocument("1.0");
        }
        if(!$item){
            $item = $dom->createElement("root");
            $dom->appendChild($item);
        }
        foreach ($arr as $key=>$val){
            $itemx = $dom->createElement(is_string($key)?$key:"item");
            $item->appendChild($itemx);
            if (!is_array($val)){
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);

            }else {
                $this->returnXml($val,$dom,$itemx);
            }
        }
        return $dom->saveXML();
    }

    /**
     * 加载配置文件
     * lodeConfig
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $file
     *
     * @return mixed
     */
    protected function lodeConfig($file){
//        parse_ini_file();
        $localPath = ROOT."/".$this->thisApp."/".$this->thisModule."/Config/".$file.".inc.php";
        $path = ROOT."/Config/".$file.".inc.php";
        if(isset($this->lodeConfigArr[$path])){
            $config = $this->lodeConfigArr[$path];
        }else{
            if(!file_exists($path)){
                $config = array();
            }else{
                $config = require_once($path);
                $this->lodeConfigArr[$path] = $config;
            }
        }
        if(isset($this->lodeConfigArr[$localPath])){
            $localConfig = $this->lodeConfigArr[$localPath];
        }else{
            if(!file_exists($localPath)){
                $localConfig = array();
            }else{
                $localConfig = require_once($localPath);
                $this->lodeConfigArr[$localPath] = $localConfig;
            }
        }
        return array_merge($config , $localConfig);
    }

    /**
     * 得到缓存对象
     * getCache
     *
     * @author Shaun.Xu
     * @access public
     *
     * @return fileCache|redisCache
     */
    public function getCache(){
        $config = $this->lodeConfig('config');
        $cacheConfig = $config['cache'];
        if(strtolower($cacheConfig['type']) == "file"){
            $path = ROOT."/".$cacheConfig['path'];
            $cacheOBJ = new fileCache($path);
            $cacheOBJ->select($this->thisModule);
        }else{
            $cacheOBJ = new redisCache($this->thisModule , $cacheConfig['host'] , $cacheConfig['port']);
        }
        return $cacheOBJ;
    }
}