<?php


namespace app\index\controller;

use app\index\model\Member as MemberModel;
use think\Db;

class Forget extends BaseController
{
    public function index()
    {

        if (request()->isPost()) {

            $member = new MemberModel;
            $result = $member->where('email', $_POST["email"])->find();

            if ($result) {
                $code = generate_password(8);
                $toemail = $_POST["email"];
                $subject = "约伴旅游网站【忘记密码】邮件";
                $body = "您好，我们已对您在约伴旅游网站上对应邮箱帐号的密码进行重置，重置的密码为：" . $code . "。请及时登录修改密码！";
                if ($this->send_email($toemail, $subject, $body)) {
                    $res = Db::name('member')->where('email', $_POST["email"])->update(['password'=>sha256($code)]);
                    if ($res) {
                        $this->success("重置密码成功！", "login/index");
                    } else {
                        $this->success("重置密码失败！");
                    }

                } else {
                    $error = '邮件发送失败！';
                }

            } else {
                $error = '该邮箱帐号不存在，请重新输入！';
            }

            $this->assign('error', $error);
        }

        return $this->fetch();
    }
}