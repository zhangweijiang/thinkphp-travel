<?php


namespace app\index\validate;


use think\Validate;

class Travel extends Validate
{
    protected $rule = [ //验证规则
        'title'            => 'require|max:200',
        'origin_name'      => 'require|max:50',
        'destination_name' => 'require|max:50',
        'description'      => 'require|max:500',
        'content'          => 'require',
        'tags'             => 'max:500',
    ];

    protected $message = [ //错误提示信息
        'title.require'            => '游记标题不能为空',
        'title.max'                => '游记标题长度不超过200个字符',
        'origin_name.require'      => '请输入出发地名称',
        'origin_name.max'          => '出发地名称长度不超过50个字符',
        'destination_name.require' => '请输入目的地名称',
        'destination_name.max'     => '目的地名称长度不超过50个字符',
        'description.require'      => '游记简介不能为空',
        'description.max'          => '游记简介长度不超过500个字符',
        'content.require'          => '游记内容不能为空',
        'tags.max'                 => '游记标签总长度不超过500个字符',
    ];

}