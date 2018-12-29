<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Exception;

class Admin extends BaseController
{
    /**
     * 管理员列表首页
     * @return mixed|string
     */
    public function index()
    {
        try {
            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != NULL && $_REQUEST['keyword'] != '') {
                $list = model('admin')->where('username|email|nickname', 'like', "%" . $_GET['keyword'] . "%")->order('id');
                $keyword = $_REQUEST['keyword'];
                $this->assign('keyword', $keyword);
                if (!is_array($list)) {
                    $list = $list->paginate(10, false, ['query' => request()->param()]);
                } else {
                    echo '<script>alert("不存在");</script>';//测试
                }
            } else {
                $list = model('admin')->paginate(10, false);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $page = $list->render();
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('list', $list);
        $this->assign('page', $page);

        return $this->fetch();
    }

    /**
     * 添加管理员信息
     * @return mixed
     */
    public function add()
    {
        if (request()->isPost()) {
            $admin['username'] = $_POST['username'];
            $admin['password'] = $_POST['password'];
            $admin['nickname'] = $_POST['name'];
            $admin['email'] = $_POST['email'];
            if ($result = AdminModel::create($admin)) {
                $this->success('新增成功', 'index');
            } else {
                $this->error($result->getError());
            }
        }

        return $this->fetch();
    }

    /**
     * 批量添加管理员信息(测试用)
     * @return string
     */
    public function addList()
    {
        $admin = new AdminModel;
        $list = [
            ['username' => 'gallons', 'password' => '123456', 'name' => '加仑', 'email' => 'gallons@qq.com', 'reg_time' => date('Y-m-d H:i:s', time())],
            ['username' => 'turned', 'password' => '123456', 'name' => '执笔', 'email' => 'turned@qq.com', 'reg_time' => date('Y-m-d H:i:s', time())],
            ['username' => 'kevin', 'password' => '123456', 'name' => '凯文', 'email' => 'kevin@qq.com', 'reg_time' => date('Y-m-d H:i:s', time())],
            ['username' => 'vn', 'password' => '123456', 'name' => '薇恩', 'email' => 'vn@qq.com', 'reg_time' => date('Y-m-d H:i:s', time())],
        ];
        if ($admin->saveAll($list)) {
            return '管理员批量新增成功';
        } else {
            return $admin->getError();
        }
    }

    /**
     * 编辑管理员信息
     * @param $id 管理员id
     * @return string
     */
    public function edit($id)
    {

        if ($admin = $_POST) {
            $admin['id'] = $id;
            if (false !== ($result = AdminModel::update($admin))) {
                $this->success('更新用户成功', 'index');
            } else {
                return $result->getError();
            }
        }

        $admin = AdminModel::where('id', $id)->find();
        $this->assign('admin', $admin);

        return $this->fetch();
    }

    /**
     * 删除管理员
     * @param $id 管理员id
     */
    public function delete($id)
    {
        $result = AdminModel::destroy($id);
        if ($result) {
            $this->success('删除用户成功', 'index');
        } else {
            $this->error('删除的用户不存在');
        }
    }

    /**
     * 修改状态
     * @param $id
     * @param $status
     * @return string
     */
    public function statusChange($id, $status)
    {
        if ($status == 0) {
            $status = 1;
        } else {
            $status = 0;
        }
        $admin['id'] = $id;
        $admin['status'] = $status;
        if (false === $result = AdminModel::update($admin)) {
            return $result->getError();
        }
        $this->redirect("index");
    }
}