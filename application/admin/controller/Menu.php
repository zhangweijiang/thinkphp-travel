<?php


namespace app\admin\controller;

use think\Db;

/**
 * Class Menu 后台菜单控制器
 * @package app\admin\controller
 */
class Menu extends BaseController
{

    /**
     * 后台菜单首页
     * @return mixed
     */
    public function index()
    {
        $pid = input('pid', 0);
        if ($pid) {
            $data = Db::name('menu')->where('id', $pid)->field(true)->find();
            $this->assign('data', $data);
        }
        
        $title = trim(input('title'));
        $all_menu = Db::name('menu')->column('id,title');

        $map['pid'] = $pid;
        if ($title) {
            $map['title'] = array('like', '%' . $title . '%');
        }
        $list = Db::name('menu')->where($map)->field(true)->order('sort asc,id asc')->select();

        int_to_string($list, array('hide' => array(1 => '是', 0 => '否'), 'is_dev' => array(1 => '是', 0 => '否')));
        if ($list) {
            foreach ($list as &$key) {
                if ($key['pid']) {
                    $key['up_title'] = $all_menu[$key['pid']];
                }
            }
            $this->assign('list', $list);
        }

        //记录当前页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('meta_title', '菜单列表');

        return $this->fetch();
    }

    /**
     * 新增后台菜单
     * @return mixed
     */
    public function add()
    {
        if (request()->isPost()) {
            $Menu = model('Menu');
            $post_data = request()->post();
            $validate = validate('Menu');
            if (!$validate->check($post_data)) {
                $this->error($validate->getError());
            }
            $data = $Menu->create($post_data);
            if ($data) {
                session('admin_menu_list', NULL);
                $this->success('新增成功', Cookie('__forward__'));
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $this->assign('info', array('pid' => input('pid')));
            $menus = Db::name('menu')->field(true)->select();
            $menus = model('tree')->toFormatTree($menus);
            $menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);

            $this->assign('menus', $menus);
            $this->assign('meta_title', '新增菜单');

            return $this->fetch();
        }
    }

    /**
     * 编辑后台菜单
     * @param int $id 菜单id
     * @return mixed
     */
    public function edit($id = 0)
    {
        if (request()->isPost()) {
            $Menu = model('Menu');
            $post_data = request()->post();
            $validate = validate('Menu');
            if (!$validate->check($post_data)) {
                $this->error($validate->getError());
            }
            $data = $Menu->update($post_data);
            if ($data) {

                session('admin_menu_list', NULL);

                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $info = array();
            //获取数据
            $info = Db::name('menu')->field(true)->find($id);
            //下拉列表
            $menus = Db::name('menu')->field(true)->select();
            $menus = model('tree')->toFormatTree($menus);
            $menus = array_merge(array(0 => array('id' => 0, 'title_show' => '顶级菜单')), $menus);
            $this->assign('menus', $menus);
            if (false === $info) {
                $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->assign('meta_title', '编辑后台菜单');

            return $this->fetch();
        }
    }

    /**
     * 删除后台菜单
     */
    public function delete()
    {
        $id = array_unique((array)input('id/a', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据');
        }

        $map = array('id', array('in' => $id));

        $result = Db::name('menu')->where($map)->delete();
        if ($result) {
            session('admin_menu_list', NULL);
            //记录行为
//            action_log('delete_menu','Menu',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 修改菜单可视状态
     * @param integer $id 菜单id
     * @param integer $value 可视状态
     */
    public function toggleHide($id, $value = 1)
    {
        session('admin_menu_list', NULL);
        $this->editRow('Menu', array('hide' => $value), array('id' => $id));
    }

    /**
     * 修改菜单显示状态(是否仅在开发者模式显示)
     * @param integer $id 菜单id
     * @param integer $value 显示状态
     */
    public function toggleDev($id, $value = 1)
    {
        session('admin_menu_list', NULL);
        $this->editRow('Menu', array('is_dev' => $value), array('id' => $id));
    }

    /**
     * 批量导入后台菜单(测试用)
     * @return mixed
     */
    public function import()
    {
        if (request()->isPost()) {
            $tree = input('tree');
            $list = explode(PHP_EOL, $tree);
            $menuModel = Db::name('menu');

            if ($list == array()) {
                $this->error('请按格式填写批量导入的菜单，至少一个菜单');
            } else {
                $pid = input('pid');
                foreach ($list as $key => $value) {
                    $record = explode('|', $value);
                    if (count($record) == 2) {
                        $menuModel->insert(array(
                            'title'  => $record[0],
                            'url'    => $record[1],
                            'pid'    => $pid,
                            'sort'   => 0,
                            'hide'   => 0,
                            'tip'    => '',
                            'is_dev' => 0,
                            'group'  => '',
                        ));
                    }
                }
                session('admin_menu_list', NULL);
                $this->success('批量导入成功', url('index?pid=' . $pid));
            }
        } else {
            $this->assign('meta_title', '批量导入后台菜单');
            $pid = (int)input('pid');
            $data = Db::name('menu')->where('id', $pid)->field(true)->find();

            $this->assign('pid', $pid);
            $this->assign('data', $data);

            return $this->fetch();
        }

    }

    public function sort() {
        if(request()->isGet()) {
            $ids = input('ids');
            $pid = input('pid');

            //获取排序的数据
            $map = array('status' => array('gt',-1));
            if(!empty($ids)) {
                $map['id'] = array('in', $ids);
            } else {
                if($pid != '') {
                    $map['pid'] = $pid;
                }
            }

            $list = Db::name('menu')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->assign('meta_title', '菜单排序');
        } elseif(request()->isPost()) {
            $ids = input('ids');
            $ids = explode(',',$ids);
            foreach ($ids as $key => $value) {
                $res = Db::name('menu')->where('id',$value)->setField('sort',$key + 1);
            }
            if($res !== false) {
                session('admin_menu_list',NULL);
                $this->success('排序成功');
            } else {
                $this->error('排序失败');
            }
        } else {
            $this->error('非法请求');
        }
    }

}