<?php
namespace zenphp\db;

use mysqli;
use mysqli_sql_exception;
/**
 *
 * @package    Action
 * @author     Shauns.xu
 * @since      2016/5/26 15:54
 */
class mysqliMysql extends Mysql
{
    private $mysqli;                //mysqli连接池
    private $tablepre = null;       //表前缀
    public $last_insert_id = 0;     //最后插入的ID
    private $table = null;          //操作的数据表
    private $fields = null;         //查询字段
    private $joins = null;          //连接表
    private $wheres = null;         //查询条件
    private $groups = null;         //分组条件
    private $orders = null;         //排序条件
    private $limitStart = 0;        //limit初始值
    private $limitNum = 0;          //取值条数
    private $bindParam = null;      //绑定参数
    private $bindSetParam = null;   //添加、替换、修改是需要绑定的值
    private $setData = null;        //添加、替换、修改的值
    private $sql = null;            //执行的Sql

    /**
     * 自动加载
     * PDOMysql constructor.
     *
     * @param null $host
     * @param null $user
     * @param null $password
     * @param null $dbname
     * @param null $port
     * @param null $encode
     * @param null $tablepre
     */
    public function __construct($host = null , $user = null , $password = null , $dbname = null , $port = null , $encode = null , $tablepre = null) {
        $this->mysqli = new mysqli($host,$user,$password,$dbname);
        if (mysqli_connect_errno()){
            die('连接失败：'. mysqli_connect_error());
        }
        $this->tablepre = $tablepre;
    }

    /**
     * 执行sql语句(针对 INSERT, UPDATE 以及DELET)
     * exec
     * @author Shaun.Xu
     * @access public
     * @param null  $sql
     * @param array $execData
     * @return bool
     */
    public function exec($sql = null , $execData = array())
    {
        if ( empty($sql) ) return false;
        $stmt = $this->mysqli->prepare($sql);
        call_user_func_array(array($stmt,'bind_param') , $this->bindParam($execData));
        $stmt->execute();
        $stmt->close();
        $this->last_insert_id = $this->mysqli->insert_id;
        $this->free();
        return $this->mysqli->affected_rows;
    }

    /**
     * 执行添加数据
     * insert
     * @author Shauns.xu
     * @param $table            //表名
     * @param $insertData       //添加的数据 array()格式
     * @return bool
     */
    public function insert(){
        $sql = $this->assembleSql('insert');
        $result = self::exec($sql , $this->bindSetParam);
        return $result;
    }

    /**
     * 执行替换数据
     * replace
     * @param $table            //表名
     * @param $insertData       //添加的数据 array()格式
     * @return bool
     */
    public function replace(){
        $sql = $this->assembleSql('replace');
        $result = self::exec($sql , $this->bindSetParam);
        return $result;
    }

    /**
     * 数据修改
     * @author Shauns.xu
     * @param $table        //表名
     * @param $upData       //修改的数据
     * @param $where        //修改的条件
     * @return bool
     */
    public function update(){
        $sql = $this->assembleSql('update');
        $setParam = array_merge($this->bindSetParam , $this->bindParam);
        $result = self::exec($sql , $setParam);
        return $result;
    }

    /**
     * 删除数据
     * delete
     * @author Shauns.xu
     * @param $table        //表名
     * @param $where        //删除数据的条件   array格式
     * @return bool
     */
    public function delete(){
        $sql = $this->assembleSql('delete');
        $result = self::exec($sql , $this->bindParam);
        return $result;
    }

    /**
     * 开启事务
     * transaction
     * @author Shauns.xu
     * @return void
     */
    public function transaction(){
        $this->mysqli->begin_transaction();
    }

    /**
     * 提交事务
     * commit
     * @author Shauns.xu
     * @return void
     */
    public function commit(){
        $this->mysqli->commit();
    }

    /**
     * 事务回滚
     * rollback
     * @author Shauns.xu
     * @return void
     */
    public function rollback(){
        $this->mysqli->rollBack();
    }

    /**
     * 设置表
     * setTable
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $table
     *
     * @return $this
     */
    public function setTable($table){
        if($this->table == null){
            $this->table = array();
        }
        $this->table[] = $this->tableName($table);
        return $this;
    }

    /**
     * 设置查询字段
     * setField
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $fieldList
     *
     * @return $this
     */
    public function setField($fieldList){
        $this->fields = $fieldList;
        return $this;
    }

    /**
     * 设置数据，适用于添加、替换、修改
     * setData
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $setData          //设置的数据  array('fiele'=>'value')
     *
     * @return $this
     */
    public function setData($setData){
        $this->setData = $setData;
        return $this;
    }

    /**
     * 连表
     * setJoin
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param        $table         //连接表
     * @param        $on            //连接关系
     * @param string $type          //连接方式
     *
     * @return $this
     */
    public function setJoin($table , $on , $type = "left" ){
        if($this->joins == null){
            $this->joins = array();
        }
        $tableName = $this->tableName($table);
        $this->joins[] = $type." join ".$tableName." on ".$on;
        return $this;
    }

    /**
     * 设置条件
     * setWhere
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param       $whereStr               //where条件
     * @param array $param                  //条件参数
     *
     * @return $this
     */
    public function setWhere($whereStr , $param = array()){
        if($this->wheres == null){
            $this->wheres = array();
        }
        $this->wheres[] = $whereStr;
        if(!is_array($param)) $param = array($param);
        if(count($param)>0){
            if($this->bindParam == null) $this->bindParam = array();
            $this->bindParam = array_merge($this->bindParam , $param);
        }
        return $this;
    }

    /**
     * 设置分组
     * setGroup
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $field
     *
     * @return $this
     */
    public function setGroup($field){
        if($this->groups == null){
            $this->groups = array();
        }
        $this->groups[] = $field;
        return $this;
    }

    /**
     * 排序
     * setOrder
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $order
     *
     * @return $this
     */
    public function setOrder($order){
        if($this->orders == null){
            $this->orders = array();
        }
        $this->orders[] = $order;
        return $this;
    }

    /**
     * 设置limit
     * setLimit
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param     $start
     * @param int $num
     *
     * @return $this
     */
    public function setLimit($start , $num = 0){
        if($num == 0){
            $num = $start;
            $start = 0;
        }
        $this->limitStart = $start;
        $this->limitNum = $num;
        return $this;
    }

    /**
     * 查询
     * select
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param null  $sql            //直接执行的Sql
     * @param array $bindParam      //绑定的参数
     *
     * @return array
     */
    public function select($sql = null , $bindParam = array()){
        if($sql == null){
            $sql = $this->assembleSql();
        }else{
            $this->bindParam = $bindParam;
        }
        $stmt = $this->mysqli->prepare($sql);
        if(count($this->bindParam)>0){
            call_user_func_array(array($stmt,'bind_param') , $this->bindParam($this->bindParam));
        }
        $stmt->execute();
        $data = array();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        $stmt->close();
        $this->free();
        return $data;
    }

    /**
     * 取一条数据
     * find
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param null  $sql
     * @param array $bindParam
     *
     * @return array
     */
    public function find($sql = null , $bindParam = array()){
        if($sql == null){
            $this->setLimit(1);
            $sql = $this->assembleSql();
        }else{
            $this->bindParam = $bindParam;
        }
        $stmt = $this->mysqli->prepare($sql);
        if(count($this->bindParam)>0){
            call_user_func_array(array($stmt,'bind_param') , $this->bindParam($this->bindParam));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        $this->free();
        return $data;
    }

    /**
     * 查询条数
     * count
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param null  $sql
     * @param array $bindParam
     *
     * @return int
     */
    public function count($sql = null , $bindParam = array()){
        if($sql == null){
            $sql = $this->assembleSql('select',1);
        }else{
            $this->bindParam = $bindParam;
        }
        $stmt = $this->mysqli->prepare($sql);
        if(count($this->bindParam)>0){
            call_user_func_array(array($stmt,'bind_param') , $this->bindParam($this->bindParam));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $this->free();
        $size = 0;
        if(!empty($row) && count($row)>0){
            foreach ($row as $val){
                $size = intval($val);
                break;
            }
        }
        return $size;
    }

    /**
     * 返回Sql
     * getSql
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param null  $sql
     * @param array $bindParam
     *
     * @return null
     */
    public function getSql($sql = null , $bindParam = array()){
        if($sql == null){
            $bindParam = array();
            if($this->bindSetParam != null && count($this->bindSetParam)>0){
                $bindParam = $this->bindSetParam;
            }
            if($this->bindParam != null && count($this->bindParam)>0){
                $bindParam = array_merge($bindParam , $this->bindParam);
            }
        }else{
            $this->sql = $sql;
        }
        if(is_array($bindParam)){
            foreach($bindParam as $k=>$v){
                if(strpos($this->sql,'?')){
                    $sql = substr($this->sql,0,strpos($this->sql,'?')+1);
                    if(strpos($sql,'like ?')){
                        $this->sql = preg_replace("/\?/","'".$v."'",$this->sql,1);
                    }else{
                        $this->sql = preg_replace("/\?/",$v,$this->sql,1);
                    }
                }
            }
        }
        return $this->sql;
    }

    /**
     * 执行Sql,返回查询结果集
     * query
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param null  $sql
     * @param array $bindParam
     *
     * @return bool|\mysqli_result
     */
    public function query($sql = null , $bindParam = array()){
        if($sql == null){
            $sql = $this->assembleSql();
            $bindParam = $this->bindParam;
        }
        $stmt = $this->mysqli->prepare($sql);
        if(count($bindParam)>0){
            call_user_func_array(array($stmt,'bind_param') , $this->bindParam($bindParam));
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    /**
     * 返回一条数据
     * fetch_array
     * @author Shaun.Xu
     * @access public
     * @param $query
     * @return mixed
     */
    public function fetchArray($query){
        return $query->fetch_assoc();
    }

    /**
     * 关闭查询----使用query查询后必须调用
     * 起到释放内存的作用
     * close
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $query    查询结果集
     *
     * @return void
     */
    public function close($query = null){
        if($query != null){
            try{
                $query->close();
            }catch (mysqli_sql_exception $e){
            }
        }
        $this->free();
    }

    /**
     * 清理资源
     */
    public function __destruct(){
        $this->free();
        $this->mysqli->close();
        $this->mysqli         = null;
        $this->tablepre = null;       //表前缀
        $this->last_insert_id = 0;     //最后插入的ID
        $this->sql = null;            //执行的Sql
        $this->last_insert_id = 0;
    }

    /**
     * 预处理引用参数
     * bindParam
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param        $params        //需要处理的字符串
     * @param string $id            //主键
     * @param int    $type          //0表示主键放在最后，1表示主键放在最前
     *
     * @return array
     */
    private function bindParam($params,$id='',$type=0){
        $arrays = array();
        if($params){
            if(is_array($params)){
                $arrays[] = $this->paramType($params,$id,$type);
                if($type ==1  && $id){
                    $arrays[] = &$id;
                }
                foreach($params as $k=>$v){
                    $arrays[] = &$params[$k]; //注意此处的引用
                }
            }
        }else{
            $arrays[] = $this->paramType($params,$id,$type);
            if($type ==1  && $id){
                $arrays[] = &$id;
            }
        }
        if($type ==0  && $id){
            $arrays[] = &$id;
        }
        return $arrays;
    }

    /**
     * 获取参数类型
     * paramType
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param     $param            //当前数组
     * @param     $id               //主键ID
     * @param int $type             //0表示主键放在最后，1表示主键放在最前
     *
     * @return string               //返回类型长字符串
     */
    private function paramType($param,$id,$type=0){
        $arr = '';
        if($type ==1 && $id){
            $arr.='i';
        }
        if(is_array($param)){
            foreach($param as $k=>$v){
                if (ctype_digit((string)$v)){
                    $arr.= ($v <= PHP_INT_MAX) ? 'i' : 's';
                }else if (is_numeric($v)){
                    $arr = $arr.'d';
                }else{
                    $arr .='s';
                }
            }
        }
        if($type ==0 && $id){
            $arr.='i';
        }
        return $arr;
    }

    /**
     * 拼装Sql
     * assembleSql
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param string $type          //拼装类型
     * @param int    $count         //是否为统计
     *
     * @return string
     */
    private function assembleSql($type = "select" , $count = 0){
        $sqlArr[] = $type;
        if($type == "select"){
            if($count == 0){
                $sqlArr[] = $this->fields == null ? "*" : $this->fields;
            }else{
                $sqlArr[] = "count(*) as iSize";
            }
        }
        if(in_array($type , array('select','delete','update'))){
            $sqlArr[] = "from ".implode(',',$this->table);
            if($this->joins != null && count($this->joins)>0){
                foreach ($this->joins as $val){
                    $sqlArr[] = $val;
                }
            }
        }else{
            $sqlArr[] = "INTO ".$this->table[0];
        }
        if(in_array($type , array('insert','replace'))){    //添加数据
            if($this->setData != null && count($this->setData)>0) {
                $insertField = "";
                $insertValue = "";
                foreach ($this->setData as $key => $val){
                    $key = str_replace('`','',$key);
                    $insertField .= empty($insertField) ? '`'.$key.'`' : ',`'.$key.'`';
                    $insertValue .= empty($insertValue) ? '?' : ',?';
                    $this->bindSetParam[] = $val;
                }
                $sqlArr[] = "(".$insertField.")";
                $sqlArr[] = "VALUES(".$insertValue.")";
            }
        } elseif ($type == 'update'){                   //修改数据
            if($this->setData != null && count($this->setData)>0) {
                $updateDate = "";
                foreach ($this->setData as $key => $val){
                    $key = str_replace('`','',$key);
                    $keyArr = explode('.',$key);
                    $filed = count($keyArr)>1 ? $keyArr[0].".`".$keyArr[1]."`" : "`".$keyArr[1]."`";
                    $updateDate .= empty($updateDate) ? $filed."=?" : ",".$filed."=?";
                    $this->bindSetParam[] = $val;
                }
                $sqlArr[] = "SET ".$updateDate;
            }
        }
        if(in_array($type , array('select','delete','update'))){
            if($this->wheres != null && count($this->wheres)>0){    //拼装where条件
                $sqlArr[] = "WHERE ".implode(' AND ',$this->wheres);
            }
            if($type == "select"){
                if($this->groups != null && count($this->groups)>0){    //拼装group
                    $sqlArr[] = "GROUP BY ".implode(',',$this->groups);
                }
            }
            if($this->orders != null && count($this->orders)>0){    //拼装order
                $sqlArr[] = "ORDER BY ".implode(',',$this->orders);
            }
            if($this->limitNum > 0){    //拼装limit
                $sqlArr[] = "LIMIT ".$this->limitStart.",".$this->limitNum;
            }
        }
        $this->sql = implode(' ',$sqlArr);
        return $this->sql;
    }

    /**
     * 生成表名称
     * tableName
     *
     * @author Shaun.Xu
     * @access public
     *
     * @param $name
     *
     * @return string
     */
    private function tableName($name){
        if(strpos($name,$this->tablepre) === 0)
            return $name;
        else
            return $this->tablepre.$name;
    }

    private function free(){
        $this->table = null;          //操作的数据表
        $this->fields = null;         //查询字段
        $this->joins = null;          //连接表
        $this->wheres = null;         //查询条件
        $this->groups = null;         //分组条件
        $this->orders = null;         //排序条件
        $this->limitStart = 0;        //limit初始值
        $this->limitNum = 0;          //取值条数
        $this->bindParam = null;      //绑定参数
        $this->bindSetParam = null;   //添加、替换、修改是需要绑定的值
        $this->setData = null;        //添加、替换、修改的值
    }
}