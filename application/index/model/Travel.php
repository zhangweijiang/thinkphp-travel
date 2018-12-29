<?php


namespace app\index\Model;



use think\Model;

class Travel extends Model
{
    //新增记录时自动完成字段
    protected $insert = ['status' => 1];
    protected $type = [
        'create_time' => 'datetime:Y-m-d',
    ];


    public function add($uid,$post){
        $data = $post;
        $data["author"] = $uid;

        if($this->allowField(true)->save($data)){
            $travel = $this->getLastInsID("id");
            return $travel;//成功
        }else {
            return -1;//失败
        }

    }

    public function edit($post,$id){
        if($this->save($post,['id'=>$id])){
            $travel = $this->where("id",$id)->find()["id"];
            return $travel;//成功
        }else {
            return -1;//失败
        }


    }


}