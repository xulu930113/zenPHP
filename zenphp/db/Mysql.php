<?php
/**
 *
 * @package    Db
 * @author     Shaun.Xu
 * @since      2017/4/20 11:31
 */

namespace zenphp\db;

/**
 * Class Db
 * @package zenphp\db
 */
abstract class Mysql
{
    /**
     * 执行sql语句(针对 INSERT, UPDATE 以及DELET)
     * exec
     * @author Shaun.Xu
     * @access public
     * @param null  $sql
     * @param array $execData
     * @return bool
     */
    abstract public function exec($sql = null , $execData = array());

    /**
     * 执行添加数据
     * insert
     * @author Shauns.xu
     * @param $table            //表名
     * @param $insertData       //添加的数据 array()格式
     * @return bool
     */
    abstract public function insert();

    /**
     * 执行替换数据
     * replace
     * @param $table            //表名
     * @param $insertData       //添加的数据 array()格式
     * @return bool
     */
    abstract public function replace();

    /**
     * 数据修改
     * @author Shauns.xu
     * @param $table        //表名
     * @param $upData       //修改的数据
     * @param $where        //修改的条件
     * @return bool
     */
    abstract public function update();

    /**
     * 删除数据
     * delete
     * @author Shauns.xu
     * @param $table        //表名
     * @param $where        //删除数据的条件   array格式
     * @return bool
     */
    abstract public function delete();

    /**
     * 开启事务
     * transaction
     * @author Shauns.xu
     * @return void
     */
    abstract public function transaction();

    /**
     * 提交事务
     * commit
     * @author Shauns.xu
     * @return void
     */
    abstract public function commit();

    /**
     * 事务回滚
     * rollback
     * @author Shauns.xu
     * @return void
     */
    abstract public function rollback();

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
    abstract public function setTable($table);

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
    abstract public function setField($fieldList);

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
    abstract public function setData($setData);

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
    abstract public function setJoin($table , $on , $type = "left" );

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
    abstract public function setWhere($whereStr , $param = array());

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
    abstract public function setGroup($field);

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
    abstract public function setOrder($order);

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
    abstract public function setLimit($start , $num = 0);

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
    abstract public function select($sql = null , $bindParam = array());

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
    abstract public function find($sql = null , $bindParam = array());

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
    abstract public function count($sql = null , $bindParam = array());

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
    abstract public function getSql($sql = null , $bindParam = array());

    /**
     * 执行Sql,返回查询结果及
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
    abstract public function query($sql = null , $bindParam = array());

    /**
     * 返回一条数据
     * fetch_array
     * @author Shaun.Xu
     * @access public
     * @param $query
     * @return mixed
     */
    abstract public function fetchArray($query);

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
    abstract public function close($query = null);
}