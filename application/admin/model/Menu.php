<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/10
 * Time: 20:17
 */

namespace app\admin\model;


use think\Model;

/**
 * Class Menu 菜单模型
 * @package app\admin\model
 */
class Menu extends Model
{
    protected $autoWriteTimestamp = false;
    protected $auto = ['title'];
    //新增
    protected $insert = ['status' => 1];

    //title属性修改器
    protected function setTitleAttr($value, $data)
    {
        return htmlspecialchars($value);
    }

}