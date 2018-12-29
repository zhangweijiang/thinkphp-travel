<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Controller;
use think\Exception;

class Travel extends BaseController
{
    /**
     * 查看游记
     */
    public function index(){
        $travel_list = model('travel')->select();
        $this->assign('travel_list',$travel_list);
        return $this->fetch();
    }

    /**
     * 删除游记
     */
    public function delete($id) {
        model('travel')->where(['id'=>$id])->delete();
        $this->redirect('/admin/travel/index');


    }

    /**
     * 举报游记
     * status状态为2
     */
    public function report($id) {
        model('travel')->update(['status'=>2],['id'=>$id]);
        $this->redirect('/admin/travel/index');


    }


}