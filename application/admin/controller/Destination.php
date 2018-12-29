<?php


namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Controller;
use think\Exception;

class Destination extends BaseController
{
   public function add(){
       return $this->fetch();
   }
   public function save(){


           // 获取表单上传文件
           $file = request()->file('file');
           $cover = request()->file('cover');
           if(empty($file)){
               $this->error('请上传文件','/admin/destination/add');exit;
           }
            if(empty($cover)){
                $this->error('请上传封面图片','/admin/destination/add');exit;
            }



            // 移动到框架应用根目录/public/uploads/ 目录下
           $info = $file->validate(['size'=>2097152,'ext'=>'pdf'])->move(ROOT_PATH . 'public' . DS . 'uploads');
           $info1 = $cover->validate(['size'=>2097152,'ext'=>'jpg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
            // 成功上传后 获取上传信息
                $file=$info->getSaveName();
            }else{
                // 上传失败获取错误信息
                $this->error('文件-'.$file->getError(),'/admin/destination/add');exit;
            }
           if($info1){
               // 成功上传后 获取上传信息
               $cover=$info1->getSaveName();
           }else{
               // 上传失败获取错误信息
               $this->error('封面图片-'.$cover->getError(),'/admin/destination/add');exit;
           }
       $condition['description'] = $_POST['description'];
       $condition['name'] = $_POST['name'];
       $destination_id = model('destination')->insertGetId($condition);

       $condition1['destination_id'] = $destination_id;
       $condition1['file'] = $file;
       $condition1['cover'] = $cover;
       $condition['description'] = $_POST['description1'];
       $int = model('destination_strategy')->insertGetId($condition1);
       if($int>0){
           $this->redirect('/admin/destination/add');
       }
   }



}