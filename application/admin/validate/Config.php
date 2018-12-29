<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/13
 * Time: 15:57
 */

namespace app\admin\validate;


use think\Validate;

class Config extends Validate
{
    protected $rule = [
        'name' => 'require|unique',
        'title' => 'require',
    ];
    protected $message = [
        'name.require' => '标识不能为空',
        'name.unique' => '该标识已存在',
        'title.require' => '名称不能为空',
    ];
}