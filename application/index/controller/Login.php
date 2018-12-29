<?php

namespace app\index\controller;

use app\index\model\Member as MemberModel;
use app\index\validate\Login as LoginValidate;
class Login extends BaseController
{
    public function index()
    {
        if ($this->isLogin() !== false) {//已登录-直接跳转到网站主页面
            $this->redirect('index/index');
        }
        if (request()->isPost()) {
            $validate = new LoginValidate;
            $login = new MemberModel;
            $result = $validate->batch()->check($_POST);
            //保存用户名
            $this->assign('email', $_POST['email']);

            //对用户登录数据进行校验
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $uid = $login->login($_POST['email'], $_POST["password"]);
                if ($uid > 0) { //登录成功
                    $this->redirect('index/index'); //跳转至网站主页面
//                    $this->success('登录成功','index/index');
                } else {
                    switch ($uid) {
                        case -1:
                            $error = '用户不存在！';
                            break; //系统级别禁用
                        case -2:
                            $error = '密码错误！';
                            break;
                        case -3:
                            $error = '该用户被禁用！';
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