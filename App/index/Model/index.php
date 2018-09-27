<?php
namespace App\index\Model;

use zenphp\core\Model;

/**
 *
 * @package    index
 * @author     Shaun.Xu
 * @since      2018/1/22 15:57
 */
class index extends Model
{
    function test(){
        $insertData = array(
            'content' => "测试".time(),
        );
        $this->getCache()->set('key',$insertData,60);
        $data = $this->getDB()
            ->setTable('data as tb1')
            ->setField('tb1.`id`,tb1.`content`')
            ->setLimit(5,2)
            ->select();
        return $data;
    }
}