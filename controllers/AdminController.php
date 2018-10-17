<?php 
namespace controllers;
class AdminController{

    public function index(){
        $model = new \models\Admin;
        $data = $model->findAll();
        // var_dump($data);
        view('admin.index',$data);
    }
    // 显示增加角色页面
    public function insert(){
        $model = new \models\Role;
        $data = $model->findAll();
        view('admin.insert',$data);
    }

    // 处理插入数据
    public function doinsert()
    {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $model = new \models\Admin;
        $model->insert($username,$password);
        redirect('/admin/index');
    }
    public function edit()
    {
        $model = new \models\Admin;
        $data = $model->findOne($_GET['id']);
        $roleId = $model->getPriIds($_GET['id']);



        
        $primodels = new \models\Role;
        $priData =$primodels->findAll();
        // echo "<pre>";
        // var_dump($data['id'],$roleId);
        // var_dump($priData['data']);
        // die;
        view('admin.edit', [
            'data' => $data,
            'pridata'=>$priData['data'],
            'roleId'=>$roleId
        ]);
    }
    public function doedit()
    {
        $role_name = $_POST['role_name'];
        $id = $_GET['id'];
        $model = new \models\Admin;
        $model->update($role_name,$id);
        redirect('/admin/index');
    }

    public function delete()
    {
        $model = new \models\Admin;
        $model->delete($_GET['id']);
        redirect('/admin/index');
    }


}


?>