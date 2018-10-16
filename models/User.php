<?php 
    // 连接数据库
    namespace models;
    class User{

        // 第一步连接数据库
        public $pdo = null;
        public function __construct(){
            // 连接数据库
            $this->pdo = new \PDO("mysql:host=localhost;dbname=ksblog",'root','');
            $this->pdo->exec("SET NAMES utf8");
        }
        public function insert($email,$password){
            $stmt  =$this->pdo->prepare("INSERT INTO users(email,password) VALUES(?,?)");
            $stmt->execute([$email,$password]);
            
        }
        public function login($email, $password)
        {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email=? AND password=?');
            $stmt->execute([
                $email,
                $password
            ]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            // var_dump($user);
            if($user)
            {
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                return TRUE;
            }
            else
                return FALSE;
        }

        public function fill($email){
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email=?');
            $stmt->execute([$email]);
            return  $stmt->fetchAll();
        }
        

    }

?>