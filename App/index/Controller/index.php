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
}