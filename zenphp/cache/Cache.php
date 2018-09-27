<?php
/**
 *
 * @package    Cache
 * @author     Shaun.Xu
 * @since      2018/2/6 16:52
 */

namespace zenphp\cache;


abstract class Cache
{

    /**
     * 设置缓存
     * set
     * @author Shaun.Xu
     * @access public
     * @param     $key
     * @param     $data
     * @param int $expire
     * @return mixed
     */
    abstract public function set($key , $data , $expire=0);

    /**
     * 得到缓存内容
     * get
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return mixed
     */
    abstract public function get($key);

    /**
     * 删除缓存内容
     * del
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return mixed
     */
    abstract public function del($key);

    /**
     * 判断缓存是否存在
     * exists
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return mixed
     */
    abstract public function exists($key);

    /**
     * 删除所有缓存
     * flushAll
     * @author Shaun.Xu
     * @access public
     * @return mixed
     */
    abstract public function flushAll();
}