<?php

namespace app\index\controller;

use app\index\validate\Register as RegisterValidate;
use app\index\model\Member as MemberModel;
use think\Controller;
use think\Request;

//注册控制器
class Register extends BaseController
{
    //注册
    public function index()
    {
        if ($this->isLogin() !== false) {//已登录-直接跳转到网站主页面
            $this->redirect('index/index');
        }
        if (Request::instance()->isPost()) {
            $validate = new RegisterValidate;
            $member = new MemberModel;
            $result = $validate->batch()->check($_POST);

            //保存邮箱和昵称
            $this->assign('email', $_POST['email']);
            $this->assign('nickname', $_POST['nickname']);

            //对用户注册数据进行校验
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $flag = $member->register($_POST["email"], $_POST["nickname"], $_POST["password"]);
                if ($flag > 0) { //注册成功
//                    $this->success("注册成功", "index/index");
                    $this->redirect("login/index");
                } else {
                    switch ($flag) {
                        case -1:
                            $error = '用户已存在，请重新输入邮箱!';
                            break;
                        default:
                            $error = '未知错误！';
                            break;
                    }
                    $this->assign("error", $error);
                }
            }
        }



        return $this->fetch();
    }
}