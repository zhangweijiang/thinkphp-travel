<?php


namespace app\admin\controller;


use think\Db;
use think\Request;

class Channel extends BaseController
{
    /**
     * 导航列表
     * @return mixed
     */
    public function index()
    {
        $pid = input('get.pid', 0);
        /* 获取频道列表 */
        $map = array('status' => array('gt', -1), 'pid' => $pid);
        $list = Db::name('Channel')->where($map)->order('sort asc,id asc')->select();
        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->assign('meta_title', '导航管理');


        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        return $this->fetch();
    }

    /**
     * 添加导航
     * @return mixed|void
     */
    public function add()
    {
        if (request()->isPost()) {
            $Channel = model('channel');
            $post_data = input();
            //自动验证
            $validate = validate('channel');
            if (!$validate->check($post_data)) {
                $this->error($validate->getError());
            }

            $data = $Channel->create($post_data);
            if ($data) {
                $this->success('新增成功', url('index'));
            } else {
                $this->error($Channel->getError());
            }
        } else {
            $pid = input('pid', 0);
            //获取父导航
            if (!empty($pid)) {
                $parent = Db::name('Channel')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info', NULL);
            $this->assign('meta_title', '新增导航');

            return $this->fetch();
        }
        return $this->fetch();
    }

    /**
     * 编辑导航
     * @param int $id
     * @return mixed
     */
    public function edit($id = 0)
    {
        if ($this->request->isPost()) {
            $postdata = Request::instance()->post();
            $Channel = Db::name("channel");
            $data = $Channel->update($postdata);
            if ($data !== false) {
                $this->success('编辑成功', url('index'));
            } else {
                $this->error('编辑失败');
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = Db::name('Channel')->find($id);

            if (false === $info) {
                $this->error('获取配置信息错误');
            }

            $pid = input('get.pid', 0);
            //获取父导航
            if (!empty($pid)) {
                $parent = Db::name('Channel')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }

            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑导航';

            return $this->fetch();
        }
    }

    /**
     * 删除导航
     */
    public function del()
    {
        $id = array_unique((array)input('id/a', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (Db::name('channel')->where($map)->delete()) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @return mixed
     */
    public function sort()
    {
        if (request()->isGet()) {
            $ids = input('ids');
            $pid = input('pid');

            //获取排序的数据
            $map = array('status' => array('gt', -1));
            if (!empty($ids)) {
                $map['id'] = array('in', $ids);
            } else {
                if ($pid !== '') {
                    $map['pid'] = $pid;
                }
            }
            $list = Db::name('Channel')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->assign('meta_title', '导航排序');

            return $this->fetch();
        } elseif (request()->isPost()) {
            $ids = input('ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = Db::name('Channel')->where(array('id' => $value))->setField('sort', $key + 1);
            }
            if ($res !== false) {
                $this->success('排序成功！');
            } else {
                $this->error('排序失败！');
            }
        } else {
            $this->error('非法请求！');
        }
    }


}