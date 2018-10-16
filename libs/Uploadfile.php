<?php

namespace libs;
class Uploadfile{
    // 单例模式
    private function _construct(){}
    // 不允许克隆
    private function _clone(){}
    // 保存唯一的对象、
    private static $obj = null;
    // 设置一个公开的方法
    public static function file(){
        if(self::$obj===null){
            // 生成一个对象
            self::$obj = new self;
        }
        return self::$obj;
    }

    // 定义属性
    private $root = ROOT.'public/uploads/'; //一级目录
    private $ext = ['image/jpeg','image/jpg','image/png','image/bmp']; //允许上传的扩展名
    private $maxSize = 1024*1024*1.8;   //文件最大的限度
    
    private $file;  // 保存用户上传的图片信息
    private $subDir;

     // 上传图片
    // 参数一、表单中文件名
    // 参数二、保存到的二级目录
    public function upload($name, $subdir)
    {
        // 把用户图片的信息保存到属性上
        $this->file = $_FILES[$name];
        $this->subDir = $subdir;

        if(!$this->checkType())
        {
            die('图片类型不正确！');
        }

        if(!$this->checkSize())
        {
            die('图片尺寸不正确！');
        }

        // 创建目录
        $dir = $this->makeDir();

        // 生成唯一的名字
        $name = $this->makeName();

        // 移动图片
        move_uploaded_file($this->file['tmp_name'], $this->root.$dir.$name);

        // 返回二级目录开始的路径
        return $dir.$name;    // avatar/20180809/1.png
    }

    // 创建目录
    private function makeDir()
    {
        $dir = $this->subDir . '/' . date('Ymd');
        if(!is_dir($this->root . $dir))
            mkdir($this->root . $dir, 0777, TRUE);  // 循环创建所有目录及子目录

        return $dir.'/';
    }

    // 生成唯一的名字
    private function makeName()
    {
        $name = md5( time() . rand(1,9999) );
        $ext = strrchr($this->file['name'], '.');
        return $name . $ext;
    }

    private function checkType()
    {
        return in_array($this->file['type'], $this->ext);
    }

    private function checkSize()
    {
        return $this->file['size'] < $this->maxSize;
    }   
}


?>