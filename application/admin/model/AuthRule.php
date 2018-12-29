<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/2
 * Time: 22:09
 */

namespace app\admin\model;


use think\Model;

class AuthRule extends Model
{
    const rule_url = 1;
    const rule_main = 2;

    //设置新增时自动完成的属性
//    protected $insert = ['type' => 1, 'status' => 1];
}