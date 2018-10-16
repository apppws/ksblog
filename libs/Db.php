<?php
namespace libs;

class DB
{
    private static $_obj = null;
    private function __clone(){}

    private $_pdo;
    private function __construct(){
        // 连接数据库
        $this->_pdo = new \PDO('mysql:host=127.0.0.1;dbname=ksblog', 'root', '');
        // 设置编码
        $this->_pdo->exec('SET NAMES utf8');
        $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_SILENT);
    }

    // 返回唯一的对象
    public static function make()
    {
        if(self::$_obj === null)
        {
            self::$_obj = new self;
        }
        return self::$_obj;
    }

    public function prepare($sql)
    {
        return $this->_pdo->prepare($sql);        
    }
    // 非预处理执行SQL
    public function exec($sql)
    {
        return $this->_pdo->exec($sql);
    }

    // 获取最新添加的记录的ID
    public function lastInsertId()
    {
        return $this->_pdo->lastInsertId();
    }
}