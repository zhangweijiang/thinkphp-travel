<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Controller;
use think\Exception;

class Report extends BaseController
{
    public function notice() {
        $report_list = model('report')->select();
        foreach($report_list as &$v){
            $v['member_name'] = model('member')->where(['id'=>$v['member_id']])->field('nickname')->find()['nickname'];
            $v['report_type'] = model('report_type')->where(['id'=>$v['report_type']])->field('name')->find()['name'];
            if($v['type']==2){
                $v['type1']='游记';
                $v['title'] = model('travel')->where(['id'=>$v['reported_id']])->find()['title'];
            }else if($v['type']==1){
                $v['type1']='行程';
                $v['title'] = model('itinerary')->where(['id'=>$v['reported_id']])->find()['title'];
            }

        }
        $this->assign('report_list',$report_list);
        return $this->fetch();
    }

    /**
     * 删除通知
     */
    public function delete($id) {
        model('report')->where(['id'=>$id])->delete();
        $this->redirect('/admin/report/notice');


    }


}