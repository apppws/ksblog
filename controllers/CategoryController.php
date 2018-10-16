<?php 
namespace controllers;
class CategoryController{

    //   显示分类页面
    public function index(){
        $model = new \models\Category;
        $data = $model->findAll();
        view('category.category',$data);
    } 

     // 显示页面
     public function insert(){
           
        view('category.insert');
    }
    // 处理插入数据
    public function doinsert(){
        $blog_type_name = $_POST['blog_type_name'];
        $parent_id = $_POST['parent_id'];
        $path = $_POST['path'];
        // var_dump($_POST);
        $model = new \models\Category;
        $model->insert($blog_type_name,$parent_id,$path);
        redirect('/category/index');
    }
    public function edit(){
        $model = new \models\Category;
        $data=$model->findOne($_GET['id']);
        // var_dump($data);
        view('category.edit', [
            'data' => $data,    
        ]);
    }
    public function doedit(){
        $blog_type_name = $_POST['blog_type_name'];
        $parent_id = $_POST['parent_id'];
        $path = $_POST['path'];
        $model = new \models\Category;
        $model->update($blog_type_name,$parent_id,$path);
        redirect('/category/index');
    }

    public function delete(){
        $model = new \models\Category;
        $model->delete($_GET['id']);
        redirect('/category/index');
    }
}



?>