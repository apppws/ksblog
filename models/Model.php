<?php
namespace models;

use PDO;

/**
 * 所有模型的父模型，
 * 在这里实现所有表的：添加、修改、删除、查询翻页等功能
 */

 class Model
 {
    protected $_db;

    // 操作的表名，值由子类设置
    protected $table;
    // 表单中的数据，值由控制器设置
    public $data;    

    /**
     * $data = [
     *   'title'=>'xxxx',
     *   'content' => 'xxxx',
     *   'is_show'=> 'y'
     * ];
     */

    public function __construct()
    {
        $this->_db = \libs\DB::make();
    }

    /**
     * 钩子函数
     */

    protected function _before_write(){}
    protected function _after_write(){}
    protected function _before_delete(){}
    protected function _after_delete(){}

    public function insert()
    {
        $this->_before_write();

        $keys=[];
        $values=[];
        $token=[];
        foreach($this->data as $k => $v)
        {
            $keys[] = $k;
            $values[] = $v;
            $token[] = '?';
        }
        $keys = implode(',', $keys);
        $token = implode(',', $token);   // ?,?,?,?
        $sql = "INSERT INTO {$this->table}($keys) VALUES($token)";

        // echo $sql;die;
        $stmt = $this->_db->prepare($sql);
        $stmt->execute($values);
        $this->data['id'] = $this->_db->lastInsertId();
        // var_dump($this->data['id']);
        // die;
        $this->_after_write();
    }

     // 接收表单中的数据
    public function fill($data)
    {
        // 判断是否在白名单中
        foreach($data as $k => $v)
        {
            if(!in_array($k, $this->fillable))
            {
                unset($data[$k]);
            }
        }
        $this->data = $data;
    }

    public function update($id)
    {
        $this->_before_write();

        $set = [];
        $token = [];

        foreach($this->data as $k => $v)
        {
            $set[] = "$k=?";
            $values[] = $v;
            $token[] = '?';
        }

        $set = implode(',', $set);

        $values[] = $id;

        $sql = "UPDATE {$this->table} SET $set WHERE id=?";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute($values);
        $this->_after_write();
    }

    public function delete($id)
    {
        $this->_before_delete();
        $stmt = $this->_db->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        $this->_after_delete();
    }

    public function findAll($options = [])
    {
        $_option = [
            'fields' => '*',
            'where' => 1,
            'order_by' => 'id',
            'order_way' => 'desc',
            'per_page'=>20,
            'join'=>'',
            'groupby'=>''

        ];

        // 合并用户的配置
        if($options)
        {
            $_option = array_merge($_option, $options);
        }

        /**
         * 翻页
         */
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page-1)*$_option['per_page'];
        
        $sql = "SELECT {$_option['fields']}
                 FROM {$this->table}
                 {$_option['join']}
                 WHERE {$_option['where']} 
                 {$_option['groupby']}
                 ORDER BY {$_option['order_by']} {$_option['order_way']} 
                 LIMIT $offset,{$_option['per_page']}";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll( PDO::FETCH_ASSOC );

        /**
         * 获取总的记录数
         */
        $stmt = $this->_db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$_option['where']}");
        $stmt->execute();
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        $pageCount = ceil($count/$_option['per_page']);

        $page_str = '';
        if($pageCount>1)
        {
            for($i=1;$i<=$pageCount;$i++)
            {
                $page_str .= '<a href="?page='.$i.'">'.$i.'</a> ';
            }
        }
        

        return [
            'data' => $data,
            'page' => $page_str,
        ];
    }

    public function findOne($id)
    {
        $stmt = $this->_db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }
    /**
     * 树形图的显示
     */
    public function tree(){
        // 先取出所有权限
        $data = $this->findAll([
            'per_page'=>999999
        ]);
        // echo '<pre>';
        // var_dump($data['data'][1]['url_path']);

        // foreach($data['data'] as $k=>$v){
        //     $urls =  $v['url_path']."<br>";
        // };
        // die;
        // 递归重新排序
        $ret = $this->_tree($data['data']);
        return $ret;
    }
    // 排序                 数据   父类id      第几级
    public function _tree($data,$parent_id=0,$level=0){
        // 先定义一个排序好的数组
        static $_ret = [];
        foreach($data as $v){
            // 判断
            if($v['parent_id']==$parent_id){
                // 并标记他的级别
                $v['level'] = $level;
                // 并放到排序好的数组
                $_ret[]=$v;
                // 在从新排序找子分类   
                // 参数  1 数据 2 父类id  3 级别+1
                $this->_tree($data,$v['id'],$level+1);
            }
        }
        return $_ret;
    }
 }