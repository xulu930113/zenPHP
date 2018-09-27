<?php
/**
 *
 * @package    ${NAME}
 * @author     Shaun.Xu
 * @since      2018/1/19 17:18
 */

return array(
    "cache" => array(
        'type' => 'file',
        'path' => 'data/Cache',
        'port' => '6379'
    ),
    "test_mysql" => array(               //数据库配置
        'type' => 'mysqli',
        'host' => '192.168.196.99',
        'port' => '3306',
        'username' => 'root',
        'password' => 'zz123asd',
        'database' => 'test',
        'charset' => 'utf8',
        'tablepre' => 'tbl_'
    ),
    "mysql" => array(               //数据库配置
        'type' => 'mysqli',
        'host' => '127.0.0.1',
        'port' => '3306',
        'username' => 'root',
        'password' => '123456',
        'database' => 'test',
        'charset' => 'utf8',
        'tablepre' => 'tbl_'
    ),
    "mysqlPDO" => array(               //数据库配置
        'type' => 'pdo',
        'host' => '127.0.0.1',
        'port' => '3306',
        'username' => 'root',
        'password' => '123456',
        'database' => 'test',
        'charset' => 'utf8',
        'tablepre' => 'tbl_'
    ),

);