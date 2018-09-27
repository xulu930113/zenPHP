<?php
/**
 *
 * @package    redisCache
 * @author     Shaun.Xu
 * @since      2017/4/19 17:37
 */

namespace zenphp\cache;

use Redis;
/**
 * Redis缓存
 * Class redisCache
 * @package core
 */
class redisCache extends Cache
{
    private $redis = array();

    private $model = null;

    /**
     * redisCache constructor.
     *
     * @param string $model     //对应模块
     * @param string $host      //host
     * @param string $port      //端口
     */
    function __construct($model = "" , $host = "127.0.0.1" , $port = "6379")
    {
        $this->model = $model;
        if(empty($this->redis) || !isset($this->redis[$model])){
            $this->redis[$model] = new Redis();
            $this->connect($host, $port);
        }
    }

    /**
     * 连接Redis
     * connect
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param string $host      //host
     * @param string $port      //端口
     *
     * @return void
     */
    private function connect($host, $port){
        $this->redis[$this->model]->connect($host, $port);
    }

    /**
     * 获取原生态 redis对象
     * getRedis
     * @author Shaun.Xu
     * @access public
     * @return \Redis
     */
    public function getRedis(){
        return $this->redis[$this->model];
    }

    /**
     * 设置缓存
     * set
     * @author Shaun.Xu
     * @access public
     * @param     $key
     * @param     $data
     * @param int $expire
     * @return bool
     */
    public function set($key, $data, $expire = 0)
    {
        $redis = $this->getRedis()->set($key, $data, $expire);
        return $redis;
    }

    /**
     * 获取存储值
     * get
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        // TODO: Implement get() method.
        return $this->getRedis()->get($key);
    }

    /**
     * 判断是否存在
     * exists
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return bool
     */
    public function exists($key){
        return $this->getRedis()->exists($key);
    }

    /**
     * 删除内容
     * del
     * @author Shaun.Xu
     * @access public
     * @param      $key
     * @return bool
     */
    public function del( $key){
        // TODO: Implement del() method.
        if($this->getRedis()->del( $key)>0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 选择DB
     * select
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param int $db
     *
     * @return void
     */
    public function select($db = 0){
        $this->getRedis()->SELECT($db);
    }

    /**
     * 从当前数据库移动到指定数据库
     * move
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param      $key         //key
     * @param int  $inDB        //移入的DB
     * @param null $outDB       //移出的DB
     *
     * @return bool
     */
    public function move($key , $inDB = 0 , $outDB = null){
        if($outDB !== null){
            $this->select($outDB);
        }
        if($this->getRedis()->MOVE($key , $inDB)){
            return true;
        }
        return false;
    }

    /**
     * 重命名缓存值
     * rename
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $oldName
     * @param $newName
     *
     * @return void
     */
    public function rename($oldName , $newName){
        $this->getRedis()->RENAME($oldName,$newName);
    }

    /**
     * 清楚所有缓存
     * flushAll
     * @author Shaun.Xu
     * @access public
     * @return void
     */
    public function flushAll(){
        $this->getRedis()->flushAll();
    }

    /**
     * 删除缓存DB内容
     * flushDB
     * @author Shaun.Xu
     * @access public
     * @return void
     */
    public function flushDB(){
        $this->getRedis()->flushDB();
    }
}