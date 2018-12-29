<?php

namespace app\admin\controller;

use think\Db;


/**
 * Class Config 后台配置控制器
 * @package app\admin\controller
 */
class Config extends BaseController
{

    /**
     * 配置管理
     * @return mixed
     */
    public function index()
    {
        $map = array('status' => 1);
        $group = input('group');
        $name = input('name');
        if (isset($group)) {
            $map['group'] = input('group', 0);
        }
        if (isset($name)) {
            $map['name'] = array('like', '%' . $name . '%');
        }
        $list = $this->lists('Config', $map, 'sort,id');
        //记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('group', config('config_group_list'));
        $this->assign('group_id', input('get.group', 0));
        $this->assign('list', $list);
        $this->assign('meta_title', '配置管理');

        return $this->fetch();
    }

    /**
     * 新增配置
     * @return mixed
     */
    public function add()
    {
        if (request()->isPost()) {//获取提交的表单
            $config = model('Config');//Config模型实例化
            $data = request()->Post();//获取post表单的数据
            $data = $config->create($data);//添加数据
            if ($data) {//判断数据是否添加成功
                cache('db_config_data', NULL);//缓存清空
                $this->success('新增成功', url('index'));//提示信息并跳转页面
            } else {
                $this->error($config->getError());//提示信息
            }
        } else {
            $this->assign('meta_title', '新增配置');//定义变量
            $this->assign('info', NULL);

            return $this->fetch('edit');//页面渲染
        }
    }

    /**
     * 编辑配置
     * @param int $id
     * @return mixed
     */
    public function edit($id)
    {
        if (request()->isPost()) {
            $config = model('Config');
            $data = request()->Post();
            $update = $config->allowField(true)->update($data);
            if ($update) {
                cache('db_config_data', NULL);
//                action_log('update_config', 'config', $data['id']);
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error($config->getError());
            }
        } else {
            $info = array();
            //获取数据
            $info = Db::name('config')->field(true)->find($id);

            if (false === $info) {
                $this->error('获取配置信息错误');
            }
            $this->assign('meta_title', '编辑配置');
            $this->assign('info', $info);

            return $this->fetch();
        }
    }

    /**
     * 批量保存配置
     * @param $config
     */
    public function save($config)
    {
        if ($config && is_array($config)) {
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                Db::name('config')->where($map)->setField('value', $value);
            }
        }
        cache('db_config_data', NULL);
        $this->success('保存成功');
    }

    /**
     * 删除配置
     */
    public function delete()
    {
        $id = array_unique((array)input('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据');
        }

        $map = array('id' => array('in', $id));
        $result = Db::name('config')->where($map)->delete();
        if ($result) {
            cache('db_config_data', NULL);
            //记录行为
//            action_log('update_config', 'config', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 获取某个标签的配置参数
     */
    public function group() {
        $id = input('id', 1);
        $type = config('config_group_list');
        $list = Db::name('config')->where(array('status' => 1, 'group' => $id))->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->assign('meta_title', $type[$id].'设置');

        return $this->fetch();
    }

    /**
     * 配置排序
     * @return mixed
     */
    public function sort(){
        if(request()->isGet()) {
            $ids = input('ids');

            //获取排序的数据
            $map = array('status' => array('gt', -1));
            if(!empty($ids)) {
                $map['id'] = array('in',$ids);
            } elseif(input('group')) {
                $map['group'] = input('group');
            }
            $list = Db::name('config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list',$list);
            $this->assign('meta_title','配置排序');

            return $this->fetch();
        } elseif(request()->isPost()) {
            $ids = input('ids');
            $ids = explode(',', $ids);
            foreach($ids as $key => $value) {
                $res = Db::name('config')->where(array('id' => $value))->setField('sort', $key + 1);
            }
            if($res !== false) {
                $this->success('排序成功', Cookie('__forward__'));
            }else {
                $this->error('排序失败');
            }
        } else {
            $this->error('非法请求');
        }
    }


}