<?php 
namespace models;
class Role{

    // 第一步连接数据库
   public $pdo = null;
   public $data;
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
   
     public function insert($role_name)
    {
        $sql = "INSERT INTO role(role_name) VALUE(?)";

        // echo $sql;die;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $role_name
        ]);
        $this->data['id']= $this->lastInsertId();

        $this->_after_write();
    }
    public function update($role_name,$id)
    {
        $sql = "UPDATE role SET role_name=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        // var_dump($stmt);
        $stmt->execute([
            $role_name,$id
        ]);
        $this->_after_write();
    }

    public function findAll($options=[]){
        $_option = [
            'fields' => 'a.*,GROUP_CONCAT(c.pri_name) pri_name',
            'where' => 1,
            'order_by' => 'id',
            'order_way' => 'desc',
            'per_page'=>5,
            'join'=>' a LEFT JOIN role_privilege b ON a.id = b.role_id LEFT JOIN privilege c ON b.pri_id = c.id',
            'groupby'=>'group by a.id'

        ];
            // 拼接查询字段的sql语句
            if(isset($_POST['keywords']) && $_POST['keywords']){
                $_option['where'] .= "  AND (role_name LIKE '%{$_POST['keywords']}%')";
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
                 FROM role
                 {$_option['join']}
                 WHERE {$_option['where']} 
                 {$_option['groupby']}
                 ORDER BY {$_option['order_by']} {$_option['order_way']} 
                 LIMIT $offset,{$_option['per_page']}";
        // var_dump($sql);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll( \PDO::FETCH_ASSOC );

        /**
         * 获取总的记录数
         */
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM role WHERE {$_option['where']}");
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
        $stmt = $this->pdo->prepare("SELECT * FROM role WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch( \PDO::FETCH_ASSOC );
    }
    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM role WHERE id=?");
        $stmt->execute([$id]);
    }

    public function _after_write(){
        
        // 判断是否修改
        $id  = isset($_GET['id']) ? $_GET['id']: $this->data['id'];
        // 删除原权限
        $stmt = $this->pdo->prepare("DELETE FROM role_privilege WHERE role_id=?");
        $stmt->execute([
            $id
        ]);
        
        // 添加和修改执行的
        $stmt = $this->pdo->prepare("INSERT INTO role_privilege(pri_id,role_id) VALUES(?,?)");
        // 循环所勾选的 pri_id 
        foreach($_POST['pri_id'] as $v){    
            $stmt->execute([
                $v,
                $id
            ]);
        }       
    } 

    // 取出这个角色所拥有的权限ID
    public function getPriIds($roleId){
        $stmt = $this->pdo->prepare("SELECT pri_id FROM role_privilege WHERE role_id=?");
        $stmt->execute([$roleId]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // var_dump($data);
        // die;
        $ret = [];
        foreach($data as $k=>$v){
            $ret[] = $v['pri_id'];
        }
        // 返回一维数组
        return $ret;
    }

}



?>