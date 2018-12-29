<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/19
 * Time: 21:02
 */

namespace app\admin\model;

use think\Model;

class Admin extends Model
{

    /**
     * 登录
     * @param $username
     * @param $password
     * @return int
     */
    public function login($username, $password)
    {
        //获取用户数据
        $admin = $this->where('username', $username)->field(true)->find()->toArray();
        //判断用户是否存在
        if ($admin) {
            //判断用户可用状态
            if ($admin['status']) {
                //验证用户密码
                if (sha256($password) === $admin['password']) {//密码正确
                    //记录登录session
                    session('admin', $admin);
                    session('admin_sign', dataAuthSign($admin));
                    return $admin['id']; //登录成功，返回用户ID
                } else {
                    return -2; //用户密码错误
                }
            } else {
                return -3; //用户被禁用
            }
        } else {
            return -1; //用户不存在
        }
    }

    /*//password属性修改器
    protected function setPasswordAttr($value)
    {
        return md5($value);
    }*/


    //status属性读取器
    /*protected function getStatusAttr($value)
    {
        $status = [-1 => '<span class="label label-warning">删除</span>', 0 => '<span class="label label-danger">禁用</span>', 1 => '<span class="label label-primary">正常</span>', 2 => '<span class="label label-info">待审核</span>'];
        return $status[$value];
    }*/
}