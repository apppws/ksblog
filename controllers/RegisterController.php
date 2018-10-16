<?php 

namespace controllers;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
class RegisterController{

        public function index(){
            view('register.register');
        }
        public function doregister(){
            // 接收表单
            $email = $_POST['email'];
            $password =$_POST['password'];
            // var_dump($email,$password);
            $passwordTo = $_POST['repassword'];
            $code = $_POST['code'];
            $model = new \models\User;
            if($password==$passwordTo){
                echo "密码一致";
                // 调用模型 插入到数据库中
                if($email!='' || $password!=''){
                    $model->insert($email,$password);
                    redirect('/login/login');
                }else{
                    echo " 不能为空";
                    back();
                }  
            }else{
                echo("<script> alert('两次密码输入不一致') </script>");
                back();
            }
            // 2. 插入到数据库中
            $user = new User;
            $ret = $user->insert($email, $password);
            if(!$ret)
            {
                die('注册失败！');
            }
            // 3. 发邮件
            $mail = new \libs\Email;
            $content = "恭喜您 ，注册成功！";
            // 从邮箱地址中取出姓名 
            $name = explode('@', $email);
            $from = [$email, $name[0]];
            // 发邮件
            $mail->send('注册成功',$content, $from);

            echo 'ok';
        }

        public function sendcode(){
            // 生成6位随机数
            $email = $_GET['email'];
            
            $code = rand(10000,99999);
            // 缓存时的名字
            $name = 'code'.$email;
            // var_dump($name);
            $_SESSION['code'] = $code;
            $config = [
                'accessKeyId'    => 'LTAIRFzYI935tz2L',
                'accessKeySecret' => 'iaNH3QUvwpqP2Fry0bECPmqPHvyNZW',
            ];
          
            $client  = new Client($config);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($email);
            $sendSms->setSignName('彭文双sns项目');
            $sendSms->setTemplateCode('SMS_135043697');
            $sendSms->setTemplateParam(['code'=>$code]);
          
            // 发送
            print_r($client->execute($sendSms));
        }
    






    }

?>