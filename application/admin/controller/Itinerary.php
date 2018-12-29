<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Controller;
use think\Exception;

class Itinerary extends BaseController
{
    /**
     * 查看行程
     */
    public function index(){
        $itinerary_list = model('itinerary')->select();
        $this->assign('itinerary_list',$itinerary_list);
        return $this->fetch();
    }

    /**
     * 删除行程
     */
    public function delete($id) {
        model('itinerary')->where(['id'=>$id])->delete();
        $this->redirect('/admin/itinerary/index');


    }

    /**
     * 举报行程
     * status状态为2
     */
    public function report($id) {
        model('itinerary')->update(['status'=>2],['id'=>$id]);
        $this->redirect('/admin/itinerary/index');


    }


}