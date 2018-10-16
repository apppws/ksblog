<?php
namespace controllers;
class LoginController{
    public function login(){
        view('login.index');
    }
    public function dologin(){
        if(isset($_SESSION['num']) && $_SESSION['num']>2){
            die("用户名错了三次");
        }

        // 1 接收表单
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = new \models\User;
        $data = $user->fill($email);
        // echo "<pre>";
        // var_dump($data[0]['time']);
        // exit;
        if($user->login($email, $password))
        {
            if(isset($_SESSION['num'])){
                unset($_SESSION['num']);
            }
            echo "登录成功";
            redirect('/index/index');
        }
        else
        {
            if(isset($_SESSION['num'])){
                $_SESSION['num']=$_SESSION['num']+1;
                // var_dump($_SESSION['num']);
            }else{
                $_SESSION['num']=0;
            }
            // 如果登录失败就 保存到session中 失败一次+1  超过三次就提示
                die('用户名或者密码错误！');    
        }   
    }
}


?>