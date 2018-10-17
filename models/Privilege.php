<?php 
namespace models;
class Privilege{

    // 第一步连接数据库
   public $pdo = null;
   public function __construct(){
       // 连接数据库
       $this->pdo = new \PDO("mysql:host=localhost;dbname=ksblog",'root','');
       $this->pdo->exec("SET NAMES utf8");
   }
   public function prepare($sql)
    {
        return $this->pdo->prepare($sql);        
    }
    // 非预处理执行SQL
    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }

    // 获取最新添加的记录的ID
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
   
     public function insert($pri_name, $url_path,$parent_id)
    {
        $sql = "INSERT INTO privilege(pri_name,url_path,parent_id) VALUES(?,?,?)";

        // echo $sql;die;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $pri_name, 
            $url_path, 
            $parent_id
        ]);
    }
    public function update($pri_name, $url_path,$parent_id,$id)
    {
        $sql = "UPDATE privilege SET pri_name=?,url_path=?,parent_id=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        // var_dump($stmt);
        $stmt->execute([
            $pri_name, $url_path,$parent_id,$id
        ]);
    }
    public function findAll($options=[]){
        $_option = [
            'fields' => '*',
            'where' => 1,
            'order_by' => 'id',
            'order_way' => 'desc',
            'per_page'=>5,

        ];
            // 拼接查询字段的sql语句
            if(isset($_POST['keywords']) && $_POST['keywords']){
                $_option['where'] .= "  AND (pri_name LIKE '%{$_POST['keywords']}%')";
            }
             
        // var_dump($_option['where']);
        // 合并用户的配置
        if($options)
        {
            $_option = array_merge($_option, $options);
        }

        /**
         * 翻页
         */
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        // var_dump($page);
        $offset = ($page-1)*$_option['per_page'];
        $sql = "SELECT {$_option['fields']}
                 FROM privilege
                 WHERE {$_option['where']} 
                 ORDER BY {$_option['order_by']} {$_option['order_way']} 
                 LIMIT $offset,{$_option['per_page']}";
        // var_dump($sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll( \PDO::FETCH_ASSOC );

        /**
         * 获取总的记录数
         */
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM privilege WHERE {$_option['where']}");
        $stmt->execute();
        $count = $stmt->fetch( \PDO::FETCH_COLUMN );
        // var_dump($count);
        $pageCount = ceil($count/$_option['per_page']);
        // var_dump($pageCount);
        $page_str = '';
            for($i=1;$i<=$pageCount;$i++)
            {
                $page_str .= '<a href="?page='.$i.'">'.$i.'</a> ';
                
            }
        // }
        // echo "<pre>";
        // var_dump($data);
        // var_dump($page_str);
        return [
            'data' => $data,
            'page' => $page_str,
        ];
    }

    public function findOne($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM privilege WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch( \PDO::FETCH_ASSOC );
    }
    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM privilege WHERE id=?");
        $stmt->execute([$id]);
    }

    // 取出上一级的分类
    public function getCat($parent_id=0){
        return $this->findAll([
            'where'=>"parent_id=$parent_id"
        ]);
    }

    // 递归排序
    public function tree(){
        $data  = $this->findAll([
            'pre_page'=>99999
        ]);
        // 递归排序
        return $this->_tree($data[data]);
    }

    public function _tree($data,$parend_id=0,$level=0){
        // 先定义一个排序好的分类
        $_ret = [];
        foreach($data as $v){
            if($v['parend_id']==$parend_id){
                $v['level'] = $level;
                $_ret = $v;
                $this->_tree($data,$v['id'],$level+1);
            }

        }
        return $_ret;
    }

}



?>