<?php


namespace app\admin\controller;


use think\Db;

class AuthManager extends BaseController
{
    /**
     * 权限管理首页
     * @return mixed
     */
    public function index()
    {
        $list = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
        $list = int_to_string($list);

        $this->assign('list', $list);
        $this->assign('use_ip', true);
        $this->assign('meta_title', '权限管理');

        return $this->fetch();
    }

    /**
     * 创建管理员用户组
     * @return mixed
     */
    public function createGroup()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => NULL, 'id' => NULL, 'description' => NULL, 'rules' => NULL)); //排除notice信息
        }
        $this->assign('meta_title', '新增用户组');

        return $this->fetch('editgroup');
    }

    /**
     * 编辑管理员用户组
     * @return mixed
     */
    public function editGroup() {
        $auth_group = Db::name('auth_group')->where(array('module'=>'admin','type'=>AuthGroup::TYPE_ADMIN))->find((int)input('id'));

        $this->assign('auth_group',$auth_group);
        $this->assign('meta_title','编辑用户组');

        return $this->fetch();
    }
}