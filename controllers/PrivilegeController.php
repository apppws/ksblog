<?php 
namespace controllers;

class PrivilegeController
{
    // 显示页面
    public function index()
    {
        $model = new \models\Privilege;
        $data = $model->findAll();
        // var_dump($data);
        view('privilege.index',$data);
    }
    // 显示页面
    public function insert()
    {
        view('privilege.insert');
    }
    // 处理插入数据
    public function doinsert()
    {
        $pri_name = $_POST['pri_name'];
        $url_path = $_POST['url_path'];
        $parent_id = $_POST['parent_id'];
        $model = new \models\Privilege;
        $model->insert($pri_name, $url_path, $parent_id);
        redirect('/privilege/index');
    }
    public function edit()
    {
        $model = new \models\Privilege;
        $data = $model->findOne($_GET['id']);
        // var_dump($data);
        // die;
        view('privilege.edit', [
            'data' => $data,
        ]);
    }
    public function doedit()
    {
        $pri_name = $_POST['pri_name'];
        $url_path = $_POST['url_path'];
        $parent_id = $_POST['parent_id'];
        $id = $_GET['id'];
        $model = new \models\Privilege;
        $model->update($pri_name, $url_path, $parent_id,$id);
        redirect('/privilege/index');
    }

    public function delete()
    {
        $model = new \models\Privilege;
        $model->delete($_GET['id']);
        redirect('/privilege/index');
    }

}





?>