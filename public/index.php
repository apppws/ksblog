<?php 
    // 定义常量  根目录文件
    define('ROOT', __DIR__."/../");
    session_start();
    
    
    // var_dump(__DIR__);
    // 2 自动加载
    function autoLoadClass($class)
    {
        require ROOT . str_replace('\\','/', $class) . '.php';
    }

    // 注册加载函数
    spl_autoload_register('autoLoadClass');

    require ROOT.'vendor/autoload.php';

    
    // 解析路由
    // 命令行
    if(php_sapi_name()=='cli'){
        $controller = ucfirst($argv[1])."Controller";
         $action = $argv[2]; 
    }else{
        // 地址
        if(isset($_SERVER['PATH_INFO'])){
            $pathInfo = $_SERVER['PATH_INFO'];
            // var_dump($pathInfo);
            // 转成数组
            $pathInfo = explode('/',$pathInfo);
            // var_dump($pathInfo);
            $controller = ucfirst($pathInfo[1])."Controller";
            $action = $pathInfo[2];
        }
        else{
            // 否则就是默认
            $controller = "IndexController";
            $action = "index";
        }
    }

    // 为控制器添加一个命名空间 
    $fullController = '\controllers\\'.$controller;
    // 创建控制器对象
    $f = new $fullController;
    // 调用方法
    $f->$action();

    // 视图文件加载
    function view($file,$data=[]){
        // 判断里面是否有数据
        if($data){
            // 就解压数组变量
            extract($data);
        }
        // 拼接视图文件路径
        $path = str_replace('.','/',$file).'.html';
        // 加载
        require(ROOT."views/".$path);
    }
     // 跳转到任意一页
     function redirect($url){
        // 跳转
        header('location:'.$url);
        exit;
    }
    // 跳回上一个页面
    function back(){
        redirect($_SERVER['HTTP_REFERER']);
    }

    

?>