<?php


namespace app\index\validate;


use think\Validate;

class ItineraryCreate extends Validate
{
    protected $rule = [ //验证规则
        'title'      => 'require',
        'start_time' => 'require|date',
        'end_time'   => 'require|date',
    ];

    protected $message = [ //错误提示信息
        'email.require'      => '请填写行程单名称',
        'start_time.require' => '请选择行程开始时间',
        'start_time.date'    => '时间格式错误',
        'end_time.require'   => '请选择行程结束时间',
        'end_time.date'      => '时间格式错误',
    ];
}