<?php
namespace app\index\controller;



class Index extends BaseController
{
    public function index()
    {
        $itinerary = model('itinerary')->select();
        $pay = 0;
        foreach($itinerary as &$v){
            $v['day']= floor((strtotime($v['end_time'])-strtotime($v['start_time']))/86400)+1;
            $v['member_count'] = model('itinerary_member')->where(['itinerary_id'=>$v['id']])->count();
            $day = model('itinerary_day')->where(['itinerary_id'=>$v['id']])->select();

            foreach($day as $vv){
                $pay+=$vv['pay'];
                $pay+=model('itinerary_accommodation')->where(['day_id'=>$vv['id']])->find()['pay'];
            }
            $v['price'] = $pay;

        }

      $this->assign('itinerary',$itinerary);
        return $this->fetch();
    }

}
