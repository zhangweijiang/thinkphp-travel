<?php

namespace app\index\validate;


use think\Validate;

class Profile extends Validate
{
    protected $rule = [ //验证规则
        'nickname' => 'require|max:16',
        'sex'      => 'require',
        'birthday' => 'date',
        'mobile'   => ['regex' => '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#'],
        'qq'       => 'max:13',
    ];

    protected $message = [ //错误提示信息
        'nickname.require' => '昵称不能为空',
        'nickname.max'     => '昵称长度为16个字符以内',
        'sex.require'      => '性别不能为空',
        'birthday.date'    => '生日时间格式错误',
        'mobile.regex'     => '手机格式错误',
        'qq.max'           => 'QQ号码位数错误',
    ];

}