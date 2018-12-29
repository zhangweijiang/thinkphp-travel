<?php


namespace app\admin\controller;

use app\admin\model\AuthRule as AuthRuleModel;
use think\Controller;
use think\Exception;

class AuthRule extends BaseController
{
    public function index()
    {
        try {
            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != NULL && $_REQUEST['keyword'] != '') {
                $list = AuthRuleModel::where('name', 'like', "%" . $_GET['keyword'] . "%");
                $keyword = $_REQUEST['keyword'];
                $this->assign('keyword', $keyword);
                if (!is_array($list)) {
                    $list = $list->paginate(10, false, ['query' => request()->param()]);
                } else {
                    echo '<script>alert("该权限不存在");</script>';//测试
                }
            } else {
                $list = AuthRuleModel::paginate(10, false);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);

        return $this->fetch();
    }

    public function add()
    {
        if (request()->isPost()) {
            $authRule['name'] = $_POST['name'];
            $authRule['module'] = $_POST['module'];
            $authRule['controller'] = $_POST['controller'];
            $authRule['action'] = $_POST['action'];
            $authRule['level'] = $_POST['level'];
            $authRule['url'] = '/' . $authRule['module'] . '/' . $authRule['controller'] . '/' . $authRule['action'];
            if ($result = AuthRuleModel::create($authRule)) {
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
        $authRule = new AuthRuleModel;
        $list = [
            ['name' => '添加', 'url' => '/add/3/1', 'module' => 'add', 'controller' => '123456', 'action' => '123456', 'level' => 0],
            ['name' => '修改', 'url' => '/edit/3/1', 'module' => 'edit', 'controller' => '123456', 'action' => '123456', 'level' => 1],
            ['name' => '查询', 'url' => '/query/3/1', 'module' => 'query', 'controller' => '123456', 'action' => '123456', 'level' => 0],
            ['name' => '删除', 'url' => '/delete/3/1', 'module' => 'delete', 'controller' => '123456', 'action' => '123456', 'level' => 1],
        ];
        if ($authRule->saveAll($list)) {
            return '管理员批量新增成功';
        } else {
            return $authRule->getError();
        }
    }

    public function edit($id)
    {
        if ($authRule = $_POST) {
            $authRule['id'] = $id;
            if (false !== ($result = AuthRuleModel::update($authRule))) {
                $this->success('更新用户成功', 'index');
            } else {
                return $result->getError();
            }
        }

        $authRule = AuthRuleModel::where('id', $id)->find();
        $this->assign('authRule', $authRule);

        return $this->fetch();
    }

    public function delete($id)
    {
        $result = AuthRuleModel::destroy($id);
        if ($result) {
            $this->success('删除用户成功', 'index');
        } else {
            $this->error('删除的用户不存在');
        }
    }

    public function getTree($list, $parentId = 0)
    {
        $arr = array();
        foreach ($list as $item) {
            /*$item['name'] = '├' . $item['name'];
            for ($deep = 0; $item['level'] > $deep; $deep++) {
                $item['name'] = '|'.$item['name'];
            }
            $deep = 0;*/
            if($item['parent_id'] == $parentId){
                $arr += $item;
                $arr['son'] = $this->getTree($list,$arr['id']);
            }
        }

        return $arr;
    }

    //无限级分类(测试)
    function genTree5($items)
    {
        foreach ($items as $item)
            $items[$item['parent_id']]['condition'][$item['id']] = &$items[$item['id']];

        return isset($items[0]['condition']) ? $items[0]['condition'] : array();
    }

    //无限级分类(测试)
    function genTree9($items)
    {
        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item['parent_id']]))
                $items[$item['parent_id']]['condition'][] = &$items[$item['id']];
            else
                $tree[] = &$items[$item['id']];

        return $tree;

    }
}