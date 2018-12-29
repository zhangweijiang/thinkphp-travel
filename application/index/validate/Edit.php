<?php


namespace app\index\validate;


use think\Validate;

class Edit extends Validate
{
    protected $rule = [ //校验规则
        'email'        => 'require|email|unique:member,email',
        'password'     => 'require|min:6|max:16',
    ];

    protected $message = [ //错误提示信息
        'email.unique'        => '该邮箱帐号已存在！',
        'email.require'        => '邮箱帐号不能为空',
        'email.email'          => '邮箱帐号格式错误',
        'password.require'     => '密码不能为空',
        'password.min'         => '密码为6~16位字符',
        'password.max'         => '密码为6~16位字符',
    ];
}