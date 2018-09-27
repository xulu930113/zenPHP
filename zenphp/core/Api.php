<?php
namespace zenphp\core;

/**
 *
 * @package    api
 * @author     Shaun.Xu
 * @since      2017/12/28 15:45
 */
class Api extends Core
{

    function __construct()
    {
        parent::__construct();
        if(API_DEBUG){
            $this->tokenValidate();
        }
    }

    /**
     * 验证是否有权限查看
     * tokenValidate
     *
     * @author Shaun.Xu
     * @access public
     * @return void
     */
    private function tokenValidate(){
        $token = $this->input("token");
        $Controller = Controller_name;
        $authorizeData = $this->lodeConfig("authorize");
        if(isset($authorizeData)){
            if(isset($authorizeData) && count($authorizeData)>0 && isset($authorizeData[$Controller])){
                $thisAuthorize = $authorizeData[$Controller];
                if(isset($thisAuthorize["IP"])){
                    $ipArr = explode(',',$thisAuthorize["IP"]);
                    $ip = $this->getIP();
                    if(!in_array($ip , $ipArr)){
                        $this->returnData(10001);
                    }
                }
                if($thisAuthorize["key"] != $token){
                    $this->returnData(10002);
                }
            }
        }
    }
}