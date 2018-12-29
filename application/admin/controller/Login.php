<?php
namespace app\admin\controller;



use think\Controller;
use app\admin\model\Admin as AdminModel;
use app\admin\validate\Admin as AdminValidate;

class Login extends Controller
{
    public function index()
    {

        if (request()->isPost()) {
            $validate = new AdminValidate;
            $login = new AdminModel;
            $result = $validate->batch()->check($_POST);
            //保存用户名
            $this->assign('username', $_POST['username']);

            //对用户登录数据进行校验
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $uid = $login->login($_POST['username'], $_POST["password"]);
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
