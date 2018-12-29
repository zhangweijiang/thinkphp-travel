<?php
namespace app\admin\controller;



use think\Controller;

class Index extends BaseController
{
    public function index()
    {
        return $this->fetch();
    }

    public function dashboard(){
        return $this->fetch();
    }

    //退出登录
    public function loginOut(){
        session('admin',null);
        session('admin_sign',null);
        $this->redirect('login/index');
    }
}
