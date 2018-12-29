<?php


namespace app\index\validate;

use think\Validate;

//登录验证类
class Login extends Validate
{
    protected $rule = [ //校验规则
        'email'        => 'require|email',
        'password'     => 'require|min:6|max:16',
        'captcha_code' => 'require|captcha',
    ];

    protected $message = [ //错误提示信息
        'email.require'        => '邮箱帐号不能为空',
        'email.email'          => '邮箱帐号格式错误',
        'password.require'     => '密码不能为空',
        'password.min'         => '密码为6~16位字符',
        'password.max'         => '密码为6~16位字符',
        'captcha_code.require' => '请输入验证码',
        'captcha_code.captcha' => '验证码错误',
    ];

}