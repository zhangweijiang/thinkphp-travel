<?php


namespace app\admin\controller;

use app\admin\model\AuthGroup as AuthGroupModel;
use think\Controller;
use think\Exception;

class AuthGroup extends BaseController
{
    public function index()
    {
        try {
            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != NULL && $_REQUEST['keyword'] != '') {
                $list = AuthGroupModel::where('title', 'like', "%" . $_GET['keyword'] . "%")->order('id');
                $keyword = $_REQUEST['keyword'];
                $this->assign('keyword', $keyword);
                if (!is_array($list)) {
                    $list = $list->paginate(10, false, ['query' => request()->param()]);
                } else {
                    echo '<script>alert("用户组不存在");</script>';//测试
                }
            } else {
                $list = AuthGroupModel::paginate(10, false);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        return $this->fetch();
    }

    //测试用
    public function getList()
    {
        $list = AuthGroupModel::paginate(1, true);

        return $list;
    }



    public function add()
    {
        if (request()->isPost()) {
            $authGroup['title'] = $_POST['name'];
            $authGroup['rules'] = $_POST['rules'];
            if ($result = AuthGroupModel::create($authGroup)) {
                $this->success('新增用户组成功', 'index');
            } else {
                $this->error($result->getError());
            }
        }

        return $this->fetch();
    }

    /**
     * 批量添加权限组信息(测试用)
     * @return string
     */
    public function addList()
    {
        $authGroup = new AuthGroupModel;
        $list = [
            ['name' => '超级管理员', 'rules' => '123456'],
            ['name' => '系统运维员', 'rules' => '123456'],
            ['name' => '系统测试员', 'rules' => '123456'],
            ['name' => '文章总编', 'rules' => '123456'],
        ];
        if ($authGroup->saveAll($list)) {
            return '管理员批量新增成功';
        } else {
            return $authGroup->getError();
        }
    }

    public function edit($id)
    {
        if ($authGroup = $_POST) {
            $authGroup['id'] = $id;
            if (false !== ($result = AuthGroupModel::update($authGroup))) {
                $this->success('更新用户组成功', 'index');
            } else {
                return $result->getError();
            }
        }

        $authGroup = AuthGroupModel::where('id', $id)->find();
        $this->assign('authGroup', $authGroup);

        return $this->fetch();
    }

    public function delete($id)
    {
        $result = AuthGroupModel::destroy($id);
        if ($result) {
            $this->success('删除用户组成功', 'index');
        } else {
            $this->error('删除的用户组不存在');
        }
    }
}