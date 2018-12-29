<?php


namespace app\index\validate;


use think\Validate;

class Password extends Validate
{
    protected $rule = [ //验证规则
        'password'   => 'require|min:6|max:16',
        'repassword' => 'require|confirm:password',
    ];

    protected $message = [ //错误提示信息
        'password.require'   => '密码不能为空',
        'password.min'       => '密码为6~16位字符',
        'password.max'       => '密码为6~16位字符',
        'repassword.require' => '请再次输入密码',
        'repassword.confirm' => '两次输入密码不一致',
    ];
}