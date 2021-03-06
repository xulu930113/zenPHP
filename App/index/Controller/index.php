<?php
namespace App\index\Controller;

use zenphp\core\Controller;

/**
 *
 * @package    index
 * @author     Shaun.Xu
 * @since      2018/1/12 16:39
 */

class index extends Controller
{

    public function index(){
        $model = new \App\index\Model\index();
        $data = $model->test();
//        print_r($data);
//        exit;
//        $this->assign("key","Hello World！");
        $this->assign("data",$data);
        $this->display();
    }

    public function qrcode(){
        $model = new \App\index\Model\index();
        $name = $phone = $tell = $email = $username = $company = $position = "";
        $name = "徐;鲁;;";
        $phone = "15208186082";
        $tell = "028-45784578";
        $email = "test@test.com";
        $username = "Shaun.Xu";
        $company = "超凡知识产权股份有限公司";
        $position = "后端工程师";
        $adr = "成都市天府三街199号";
        $data = $model->qrcode($name , $phone , $tell , $email , $username , $company , $position , $adr);
        //$name , $phone = "" , $tell = "" , $email = "" , $username = "" , $company = "" , $position = ""
        $this->assign("qrcode" , "http://www.zenphp.com/".$data);
        $this->display("index/index.html");
    }
}