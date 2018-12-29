<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Controller;
use think\Exception;

class ReportType extends BaseController
{
    /**
     * 举报类型列表
     */
    public function index(){
       $report_type_list = model('report_type')->select();
        $this->assign('report_type_list',$report_type_list);
        return $this->fetch();
    }
    /**
     * 删除举报类型
     */
    public function delete($id) {
        model('report_type')->where(['id'=>$id])->delete();
        $this->redirect('/admin/report_type/index');


    }

    public function add($name,$content){
        $int = model('report_type')->insertGetId(['name'=>$name,'content'=>$content]);
        echo $int;

    }

    public function edit($name,$content,$id){
        $int = model('report_type')->update(['name'=>$name,'content'=>$content],['id'=>$id]);
        echo $int;

    }

    public function find($id){
        $result = model('report_type')->where(['id'=>$id])->find();
        echo json_encode($result);
    }


}