<?php
/**
 *
 * @package    Controller
 * @author     Shaun.Xu
 * @since      2018/1/12 16:32
 */

namespace zenphp\core;


class Controller extends Core
{
    private $smarty = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 模板引擎设置
     * setTemplate
     *
     * @author Shaun.Xu
     * @access public
     * @return null|\Smarty
     */
    private function setTemplate(){
        if($this->smarty == null){
            $this->smarty = new \Smarty();
            $templateDir = ROOT."/".$this->thisApp."/";
            $compileDir = ROOT."/data/tplCompile/";
            $cacheDir = ROOT."/data/tplCache/";
            $this->smarty->setTemplateDir($templateDir);
            $this->smarty->setCompileDir($compileDir);
            $this->smarty->setCacheDir($cacheDir);
        }
        return $this->smarty;
    }

    /**
     * 是否缓存
     * caching
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param bool $caching     //缓存状态，false|true，默认false
     *
     * @return void
     */
    public function caching($caching = false){
        $smarty = $this->setTemplate();
        $smarty->setCaching($caching);
    }

    /**
     * 向模板传值
     * assign
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param      $key         //key
     * @param null $val         //值
     * @param bool $nocache     //是否缓存
     *
     * @return void
     */
    public function assign($key , $val = null , $nocache = false){
        $smarty = $this->setTemplate();
        $smarty->assign($key , $val , $nocache);
    }

    /**
     * 设置模板
     * display
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param string $tplName
     *
     * @return void
     */
    public function display($tplName = null){
        if($tplName == null){
            $tplName = $this->thisModule."/View/".Controller_name."/".Action_name.".html";
        }else{
            $tplNameArr = explode('/',$tplName);
            if(count($tplNameArr)<= 2){
                $tplName = $this->thisModule."/View/".$tplName;
            }
        }
        $smarty = $this->setTemplate();
        $smarty->display($tplName);
    }

    /**
     * 取出模板输出内容
     * fetch
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param string $tplName       //模板名称
     *
     * @return mixed
     */
    public function fetch($tplName = ""){
        $smarty = $this->setTemplate();
        return $smarty->fetch($tplName);
    }
}