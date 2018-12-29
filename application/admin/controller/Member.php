<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Controller;
use think\Exception;

class Member extends BaseController
{
    /**
     * 查看用户
     */
    public function index(){
        $member_list = model('member')->select();
        $this->assign('member_list',$member_list);
        return $this->fetch();
    }

    /**
     * 删除用户
     */
    public function delete($id) {
        model('member')->where(['id'=>$id])->delete();
        $this->redirect('/admin/member/index');


    }

    /**
     * status正常为0，禁用为1
     */
    public function report($id,$status) {
        if($status==1){
            model('member')->update(['status'=>0],['id'=>$id]);
            $this->redirect('/admin/member/index');
        }else if($status==0){
            model('member')->update(['status'=>1],['id'=>$id]);
            $this->redirect('/admin/member/index');
        }



    }


}