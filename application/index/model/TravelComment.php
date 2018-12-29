<?php


namespace app\index\Model;


use think\Model;

class TravelComment extends Model
{
    public function comment($pid,$travel_id,$member_id,$member_pid,$content){
        $data['pid'] = $pid;
        $data['travel_id'] = $travel_id;
        $data['member_id'] = $member_id;
        $data['member_pid'] = $member_pid;
        $data['content'] = $content;

        $this->save($data);
    }
}