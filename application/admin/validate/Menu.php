<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/10
 * Time: 21:59
 */

namespace app\admin\validate;


use think\Validate;

class Menu extends Validate
{
    // 验证规则
    protected $rule = [
        ['title', 'require', '标题必须填写'],
        ['url', 'require', '链接必须填写'],

    ];
}