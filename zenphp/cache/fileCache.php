<?php
namespace zenphp\cache;

define('FOPEN_WRITE_CREATE_DESTRUCTIVE','wb');
define('FOPEN_WRITE_CREATE','ab');
define('DIR_WRITE_MODE', 0777);

/**
 * 文件缓存
 * Class fileCache
 * @package core、
 */
class fileCache extends Cache{
    /**
     * 缓存路径
     * @access private
     * @var string
     */
    private $_cache_path;

    /**
     * @var string
     */
    private $_cache_path_base;

    private $json;

    /**
     * 解析函数，设置缓存过期实践和存储路径
     * FileCache constructor.
     *
     * @param      $cache_path
     * @param bool $json
     */
    public function __construct($cache_path , $json=true)
    {
        $this->_cache_path_base = $cache_path;
        $this->json = $json;
        $this->_checkDir($this->_cache_path_base);
    }

    /**
     * 选择缓存目录
     * select
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param string $db
     *
     * @return void
     */
    public function select($db = ""){
        $this->_cache_path = $this->_cache_path_base."/".$db;
        $this->_checkDir($this->_cache_path);
    }

    /**
     * 设置缓存
     * @access public
     * @param  string $key 缓存的唯一键
     * @param  string $data 缓存的内容
     * @param  int $expire 缓存过期时间 0 永不过期
     * @return bool
     */
    public function set($key, $data,$expire=0)
    {
        if($this->json){
            $value = is_array($data) ? json_encode($data) : $data;
        }else{
            $value = $data;
        }
        $file = $this->_file($key);
        return $this->write_file($file, $value,$expire);
    }

    /**
     * 获取缓存
     * @access public
     * @param  string $key 缓存的唯一键
     * @return mixed
     */
    public function get($key) {
        $file = $this->_file($key);
        /** 文件不存在或目录不可写 */
        if (!file_exists($file) || !$this->is_really_writable($file)) {
            return false;
        }
        /** 缓存没有过期，仍然可用 */
        if ( time() < filemtime($file) ) {
            $data = $this->read_file($file);
            foreach ($data as $val){
                if(!$val){
                    return false;
                }
                if($this->json){
                    return json_decode($val , true);
                }else{
                    return $data;
                }
            }
        }else{
            @unlink($file);
        }
        /** 缓存过期，删除之 */
        return FALSE;
    }

    /**
     * 判断缓存是否存在
     * exists
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return bool
     */
    public function exists($key){
        $file = $this->_file($key);
        /** 文件不存在或目录不可写 */
        if (!file_exists($file) || !$this->is_really_writable($file)) {
            return false;
        }
        /** 缓存过期，仍然可用 */
        if ( time() < filemtime($file) ) {
            return true;
        }
        @unlink($file);
        return false;
    }

    /**
     * 删除缓存文件
     * del
     * @author Shaun.Xu
     * @access public
     * @param $key
     * @return bool
     */
    public function del($key){
        $file = $this->_file($key);
        if (!file_exists($file) || !$this->is_really_writable($file)) {
            return true;
        }
        /** 删除缓存文件 */
        @unlink($file);
    }

    /**
     * 清楚缓存
     * clear
     * @author Shauns.xu
     * @param $path
     * @return void
     */
    public function flushDB($db) {
        $path = $this->_cache_path_base."/".$db."/";
        $file = scandir($path);
        foreach ($file as $v) {
            @unlink($path.$v);
        }
    }

    /**
     * 清楚所有缓存
     * clearAll
     * @author Shauns.xu
     * @return void
     */
    public function flushAll(){
        $file = scandir($this->_cache_path_base);
        foreach ($file as $v) {
            @unlink($this->_cache_path_base."/".$v);
        }
    }

    /**
     * 读取文件
     * read_file
     * @author Shauns.xu
     * @param $file
     * @return bool|string
     */
    private function read_file($file) {
        if ( ! file_exists($file)) {
            yield FALSE;
        }else{
            if ( ! $fp = @fopen($file, 'r+')) {
                yield FALSE;
            }else{
                flock($fp, LOCK_SH);//读取之前加上共享锁
                if (filesize($file) > 0) {
                    yield fread($fp, filesize($file));
                }
                flock($fp, LOCK_UN);//释放锁
                fclose($fp);
            }
        }

    }

    /**
     * 写入文件
     * write_file
     * @author Shauns.xu
     * @param        $path
     * @param        $data
     * @param        $expire
     * @param string $mode
     *
     * @return bool
     */
    private function write_file($path, $data,$expire = 0, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE) {
        if ( ! $fp = @fopen($path, $mode)) {
            return FALSE;
        }
        flock($fp, LOCK_EX);
        fwrite($fp, $data);
        flock($fp, LOCK_UN);
        fclose($fp);
        if($expire<=0)$expire=31536000; // 1 year
        $expire += time();
        return @touch($path,$expire);
    }

    /**
     * 兼容各平台判断文件是否有写入权限
     * is_really_writable
     * @author Shauns.xu
     * @param $file
     * @return bool
     */
    private function is_really_writable($file) {
        if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE) {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/').'/'.md5(rand(1,100));
            if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, DIR_WRITE_MODE);
            @unlink($file);
            return TRUE;
        } elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE) {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

    /**
     * 设置缓存目录
     * _checkDir
     * @author Shaun.Xu
     * @access public
     * @return void
     */
    private function _checkDir($path){
        if(!is_dir($path)){
            @mkdir($path,0777,true);
        };
    }

    /**
     * 缓存文件名
     * @access public
     * @param  string $key
     * @return string
     */
    private function _file($key) {
        return $this->_cache_path."/".md5($key);
    }
}