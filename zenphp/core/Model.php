<?php
/**
 *
 * @package    ${NAME}
 * @author     Shaun.Xu
 * @since      2018/1/19 10:58
 */

namespace zenphp\core;

use zenphp\db\PDOMysql;
use zenphp\db\mysqliMysql;

class Model extends Core
{
    private $DbOBJ = null;

    /**
     * 得到数据库操作对象
     * getDB
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param null $dbname
     *
     * @return mysqliMysql|PDOMysql
     */
    public function getDB($dbname = null){
        $key = $dbname == null ? $this->thisModule : $this->thisModule."_".$dbname;
        if(!isset($this->DbOBJ[$key])){
            $config = $this->lodeConfig('config');
            $dbConfig = $dbname == null ? $config['mysql'] : $config[$dbname];
            if(strtolower($dbConfig['type']) == "pdo"){
                $this->DbOBJ[$key] = new PDOMysql($dbConfig['host'] , $dbConfig['username'] , $dbConfig['password'] , $dbConfig['database'] , $dbConfig['port'] , $dbConfig['charset'] , $dbConfig['tablepre']);
            }else{
                $this->DbOBJ[$key] = new mysqliMysql($dbConfig['host'] , $dbConfig['username'] , $dbConfig['password'] , $dbConfig['database'] , $dbConfig['port'] , $dbConfig['charset'] , $dbConfig['tablepre']);
            }
        }
        return $this->DbOBJ[$key];
    }
}