<?php 
namespace controllers;
class RoleController{

    // 显示角色页面
    public function index(){
         // 添加权限列表
         $model = new \models\Role;
        $data =  $model->findAll();
        view('role.index',$data);
    }
    // 显示增加角色页面
    public function insert(){
        $model = new \models\Privilege;
        $data = $model->tree();
        view('role.insert',[
            'data'=>$data
        ]);
    }

    // 处理插入数据
    public function doinsert()
    {
        $role_name = $_POST['role_name'];
        $model = new \models\Role;
        $model->insert($role_name);
        redirect('/role/index');
    }
    public function edit()
    {
        $model = new \models\Role;
        $data = $model->findOne($_GET['id']);
        $primodels = new \models\Privilege;
        $priData = $primodels->tree();
        // var_dump($priData);
        // die;
        $priId = $model->getPriIds($_GET['id']);
        // var_dump($priId);
        view('role.edit', [
            'data' => $data,
            'pridata'=>$priData,
            'priId'=>$priId
        ]);
    }
    public function doedit()
    {
        $role_name = $_POST['role_name'];
        $id = $_GET['id'];
        $model = new \models\Role;
        $model->update($role_name,$id);
        redirect('/role/index');
    }

    public function delete()
    {
        $model = new \models\Role;
        $model->delete($_GET['id']);
        redirect('/role/index');
    }

}

?>