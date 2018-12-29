<?php

namespace app\index\validate;

use think\Validate;

//注册验证类
class Register extends Validate
{
    protected $rule = [ //验证规则
        'email'      => 'require|email',
        'nickname'   => 'require|max:16',
        'password'   => 'require|min:6|max:16',
        'repassword' => 'require|confirm:password',
    ];

    protected $message = [ //错误提示信息
        'email.require'      => '邮箱帐号不能为空',
        'email.email'        => '邮箱帐号格式不正确',
        'nickname.require'   => '昵称不能为空',
        'nickname.max'       => '昵称长度为16个字符以内',
        'password.require'   => '密码不能为空',
        'password.min'       => '密码为6~16位字符',
        'password.max'       => '密码为6~16位字符',
        'repassword.require' => '请再次输入密码',
        'repassword.confirm' => '两次输入密码不一致',
    ];

}