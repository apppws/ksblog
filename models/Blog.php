<?php 
namespace models;
class Blog{
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
    
     // 接收表单中的数据
     public function fill($data)
     {
         $this->data = $data;
     }
   
     public function insert($title,$user,$logo,$content,$blog_type1_id,$blog_type2_id,$blog_type3_id)
    {
        // var_dump($title,$user,$logo,$content,$blog_type1_id,$blog_type2_id,$blog_type3_id);
        $sql = "INSERT INTO blog(title,user,logo,content,cat1_id,cat2_id,cat3_id) VALUES(?,?,?,?,?,?,?)";
        // echo $sql;die;
        $stmt = $this->pdo->prepare($sql);
        // var_dump($stmt);
        $c = $stmt->execute([
            $title,
            $user,
            $logo,
            $content,
            $blog_type1_id,
            $blog_type2_id,
            $blog_type3_id
        ]);
        // var_dump($c);
        // die;
        $this->_after_write();
    }
    public function update($title,$user,$logo,$content,$id)
    {
        $sql = "UPDATE blog SET title=?,content=?,logo=?,content=? WHERE id=?";
        // var_dump($sql);
        $stmt = $this->pdo->prepare($sql);
        // var_dump($stmt);
        $stmt->execute([
            $title,$user,$logo,$content,$id
        ]);
        $this->_after_write();
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
                $_option['where'] .= "  AND (title LIKE '%{$_POST['keywords']}%')";
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
                 FROM blog
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
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM blog WHERE {$_option['where']}");
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
        $stmt = $this->pdo->prepare("SELECT * FROM blog WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch( \PDO::FETCH_ASSOC );
    }
    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM blog WHERE id=?");
        $stmt->execute([$id]);
    }

     // 添加 修改之后执行
     public function _after_write()
     {
         /**
          * 上传多张图片
          */
         // 1 调用上传图片libs  
         $uploader = \libs\Uploadfile::file();
         // 2 预处理
         $stmt = $this->pdo->prepare("INSERT INTO images(blog_id,path) VALUES (?,?)");
         // var_dump($stmt);
         // die;
         // 3 循环
         $_tmp = []; 
         // var_dump($_FILES);
         // die;
         foreach($_FILES['image']['name'] as $k=>$v){
             // 如果有图片并且上传成功时才处理图片
             if($_FILES['image']['error'][$k] == 0)
             {
                 // 拼出每张图片需要的数组
                 $_tmp['name'] = $v;
                 $_tmp['type'] = $_FILES['image']['type'][$k];
                 $_tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
                 $_tmp['error'] = $_FILES['image']['error'][$k];
                 $_tmp['size'] = $_FILES['image']['size'][$k];
 
                 // 把files 文件放到这个变量中
                 $_FILES['tmp']=$_tmp;
                 // 保存
                 $path = '/uploads/' . $uploader->upload('tmp','goods');
                 // var_dump($path);
                 // die;
                //  $id = lastInsertId();
                //  var_dump($id);
                 // 执行sql 语句
                 $stmt->execute([
                     1,
                     $path
                 ]);
             }
         }
 
    


        }
    }




?>