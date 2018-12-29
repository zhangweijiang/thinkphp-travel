<?php


namespace app\index\controller;
use app\index\model\Itinerary as ItineraryModel;
use app\index\validate\ItineraryCreate;

class Itinerary extends BaseController
{

    /**
     * 行程单列表页面
     * @return mixed
     */
    public function index() {
        //即将出发
        $itinerary = model('itinerary')->limit(3)->order('id desc')->select();
        $pay = 0;
        foreach($itinerary as &$v){
            $v['day']= floor((strtotime($v['end_time'])-strtotime($v['start_time']))/86400)+1;
            $v['day_go'] = floor((strtotime($v['start_time'])-time())/86400)+1;
            $v['member_count'] = model('itinerary_member')->where(['itinerary_id'=>$v['id']])->count();
            $day = model('itinerary_day')->where(['itinerary_id'=>$v['id']])->select();
            $v['place'] = $day;
            $v['members'] = model('itinerary_member')->alias('a')->join('member b','a.member_id=b.id')->where(['itinerary_id'=>$v['id'],'a.status'=>1])->select();
            foreach($day as $vv){
                $pay+=$vv['pay'];
                $pay+=model('itinerary_accommodation')->where(['day_id'=>$vv['id']])->find()['pay'];
            }
            $v['price'] = $pay;

        }
        $this->assign('itinerary',$itinerary);

        //值得期待
        $itinerary1 = model('itinerary')->limit(10)->order('id desc')->select();
        $pay = 0;
        foreach($itinerary1 as &$v){
            $v['member'] = model('member')->where(['id'=>$v['member_id']])->find();
            $v['day']= floor((strtotime($v['end_time'])-strtotime($v['start_time']))/86400)+1;
            $v['day_go'] = floor((strtotime($v['start_time'])-time())/86400)+1;
            $v['member_count'] = model('itinerary_member')->where(['itinerary_id'=>$v['id']])->count();
            $day = model('itinerary_day')->where(['itinerary_id'=>$v['id']])->select();
            $v['place'] = $day;
            foreach($day as $vv){
                $pay+=$vv['pay'];
                $pay+=model('itinerary_accommodation')->where(['day_id'=>$vv['id']])->find()['pay'];
            }
            $v['price'] = $pay;

        }
        $this->assign('itinerary1',$itinerary1);


        //刚刚创建
        $itinerary2 = model('itinerary')->limit(3)->order('start_time desc')->select();
        $pay = 0;
        foreach($itinerary2 as &$v){
            //创建人
            $v['member'] = model('member')->where(['id'=>$v['member_id']])->find();
            $v['day']= floor((strtotime($v['end_time'])-strtotime($v['start_time']))/86400)+1;
            $v['day_go'] = floor((strtotime($v['start_time'])-time())/86400)+1;
            $v['member_count'] = model('itinerary_member')->where(['itinerary_id'=>$v['id']])->count();
            $day = model('itinerary_day')->where(['itinerary_id'=>$v['id']])->select();
            $v['place'] = $day;
            foreach($day as $vv){
                $pay+=$vv['pay'];
                $pay+=model('itinerary_accommodation')->where(['day_id'=>$vv['id']])->find()['pay'];
            }
            $v['price'] = $pay;

        }
        $this->assign('itinerary2',$itinerary2);
        return $this->fetch();
    }

    /**
     * 行程单发布页面
     * @return mixed
     */
    public function add() {

        if(request()->isPost()){
            $validate = new ItineraryCreate;
            $result = $validate->batch()->check($_POST);

            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $data = $_POST;
                $data['member_id'] = session('user.id');
                $id = model('itinerary')->insertGetId($data);

                $res = model('itinerary_member')->save(['member_id'=>session('user.id'),'status'=>1,'itinerary_id'=>$id]);

                $this->redirect('itinerary/edit?id='.$id);
            }

        }
        return $this->fetch();
    }


    /**
     * 行程单发布编辑页面
     * @return mixed
     */
    public function edit($id) {

        $result = model('itinerary')->where(['id'=>$id])->find();
        $day = floor((strtotime($result['end_time'])-strtotime($result['start_time']))/86400);
        $this->assign('day',$day+1);
        $this->assign('start_time',$result['start_time']);
        $this->assign('end_time',$result['end_time']);
        $this->assign('id',$id);
        return $this->fetch();
    }

    public function lists() {
        return $this->fetch();
    }

    /**
     * 保存发布行程
     */
    public function save() {
        $itinerary_day_model = model('itinerary_day');
        $itinerary_accommodation_model = model('itinerary_accommodation');
        $itinerary_id = $_POST['id'];
        $condition = array();
        $condition1 = array();
        for($i=0;$i<count($_POST['title']);$i++){
            //行程
            $condition[$i]['title'] = $_POST['title'][$i];
            $condition[$i]['description'] = $_POST['description'][$i];
            $condition[$i]['pay'] = $_POST['pay'][$i];
            $condition[$i]['itinerary_id'] = $itinerary_id;
            $condition[$i]['day'] = $i+1;
            //住宿
            $condition1[$i]['title'] = $_POST['title1'][$i];
            $condition1[$i]['description'] = $_POST['description1'][$i];
            $condition1[$i]['pay'] = $_POST['pay1'][$i];
        }
        for($i=0;$i<count($condition);$i++){
            $int=$itinerary_day_model->insertGetId($condition[$i]);
            $condition1[$i]['day_id'] = $int;
            $int1 =  $itinerary_accommodation_model->insertGetId($condition1[$i]);
        }
        if($int>0&&$int1>0){
            $this->success('发布行程成功','Itinerary/detail?id='.$itinerary_id);
        }
    }

    /**
     * 保存编辑行程
     */
    public function save1() {
        $itinerary_day_model = model('itinerary_day');
        $itinerary_accommodation_model = model('itinerary_accommodation');
        $itinerary_id = $_POST['id2'];
        $condition = array();
        $condition1 = array();
        for($i=0;$i<count($_POST['title']);$i++){
            //行程
            $condition[$i]['id'] = $_POST['id'][$i];
            $condition[$i]['title'] = $_POST['title'][$i];
            $condition[$i]['description'] = $_POST['description'][$i];
            $condition[$i]['pay'] = $_POST['pay'][$i];
            $condition[$i]['itinerary_id'] = $itinerary_id;
            $condition[$i]['day'] = $i+1;
            //住宿
            $condition1[$i]['id'] = $_POST['id1'][$i];
            $condition1[$i]['title'] = $_POST['title1'][$i];
            $condition1[$i]['description'] = $_POST['description1'][$i];
            $condition1[$i]['pay'] = $_POST['pay1'][$i];
        }

        for($i=0;$i<count($condition);$i++){
            $itinerary_day_model->update($condition[$i],$condition[$i]['id']);
            $itinerary_accommodation_model->update($condition1[$i],$condition1[$i]['id']);
        }

        $this->success('编辑行程成功','Itinerary/detail?id='.$itinerary_id);

    }

    /**
     * 行程单展示页面
     * @param $id
     * @return mixed
     */
    public function detail($id) {

        $itinerary_day_model = model('itinerary_day');
        $itinerary_accommodation_model = model('itinerary_accommodation');
        $result = model('itinerary')->where(['id'=>$id])->find();
        $click = model('itinerary')->save(['click_num'=>$result['click_num']+1],['id'=>$id]);
        $click =model('itinerary')->where(['id'=>$id])->find();
        $day_list=$itinerary_day_model->where(['itinerary_id'=>$id])->select();
        $itinerary = array();
        $itinerary = $day_list;
        foreach($day_list as $v){
            static $i = 0;
            $accommodation_list = $itinerary_accommodation_model->where(['day_id'=>$v['id']])->find();
            $itinerary[$i]['title1'] = $accommodation_list['title'];
            $itinerary[$i]['pay1'] = $accommodation_list['pay'];
            $itinerary[$i]['description1'] = $accommodation_list['description'];
            $i++;
        }
        $members = model('itinerary_member')->alias('a')->join('member b','a.member_id=b.id')->where(['a.itinerary_id'=>$id,'a.status'=>1])->select();
        $report_type=model('report_type')->select();



        $this->assign('members',$members);
        $this->assign('click',$click);
        $this->assign('result',$result);
        $this->assign('itinerary',$itinerary);
        $this->assign('start_time',$result['start_time']);
        $this->assign('end_time',$result['end_time']);
        $this->assign('id',$id);
        $this->assign('report_type',$report_type);
        return $this->fetch();
    }

    /**
     * 编辑页面
     * @param $id
     * @return mixed
     */
    public function edit1($id) {
        $itinerary_day_model = model('itinerary_day');
        $itinerary_accommodation_model = model('itinerary_accommodation');
        $result = model('itinerary')->where(['id'=>$id])->find();
        //$day = floor((strtotime($result['end_time'])-strtotime($result['start_time']))/86400);
        $day_list=$itinerary_day_model->where(['itinerary_id'=>$id])->select();
        $itinerary = array();
        $itinerary = $day_list;
        foreach($day_list as $v){
            static $i = 0;
            $accommodation_list = $itinerary_accommodation_model->where(['day_id'=>$v['id']])->find();
            $itinerary[$i]['title1'] = $accommodation_list['title'];
            $itinerary[$i]['pay1'] = $accommodation_list['pay'];
            $itinerary[$i]['description1'] = $accommodation_list['description'];
            $itinerary[$i]['id1'] = $accommodation_list['id'];

            $i++;

        }
        //$this->assign('day',$day+1);
        $this->assign('itinerary',$itinerary);
        $this->assign('start_time',$result['start_time']);
        $this->assign('end_time',$result['end_time']);
        $this->assign('id',$id);
        return $this->fetch();
    }

    /**
     * 参与行程
     */
    public function join($id) {
        $itinerary_member_model = model('itinerary_member');
        $notice_model = model('notice');
        if ($this->isLogin() !== false) {
            $data['member_id'] = session('user.id');
            $data['itinerary_id'] = $id;
            $result = $itinerary_member_model->where(['member_id'=>$data['member_id'],'itinerary_id'=>$data['itinerary_id']])->find();
            if(!empty($result)){
                return json(['flag' => 2]);
            }else{
                $int = $itinerary_member_model->insertGetId($data);
                $nickname=model('member')->where(['id'=>session('user.id')])->find()['nickname'];
                $itinerary_result=model('itinerary')->where(['id'=>$id])->find();
                $condition['type']=0;
                $condition['member_id'] = session('user.id');
                $condition['user_id']=$itinerary_result['member_id'];
                $condition['title'] = "<span style='color:blue'>".$nickname."</span>".'想要参与"'.$itinerary_result['title'].'"行程!';
                $notice_model->save($condition);
                if($int>0){
                    return json(['flag' => 1]);
                }else{
                    return json(['flag' => 0]);
                }
            }
        }else{
            return json(['flag' => 0]);
        }

    }


    /**
     * 判断是否参与行程
     */
    public function join1($id) {
        $itinerary_member_model = model('itinerary_member');
        if ($this->isLogin() !== false) {
            $data['member_id'] = session('user.id');
            $data['itinerary_id'] = $id;
            $result = $itinerary_member_model->where(['member_id'=>$data['member_id'],'itinerary_id'=>$data['itinerary_id']])->find();
            if(!empty($result)){
                return json(['flag' => 1]);
            }else{
                return json(['flag' => 2]);
            }

        }else{
            return json(['flag' => 0]);
        }

    }

    /**
     * 举报
     */
    public function report($report_type,$type,$itinerary_id,$content) {
        $condition['report_type'] = $report_type;
        $condition['type'] = 1;//1表示行程，2表示游记
        $condition['member_id'] = session('user.id');
        $condition['reported_id'] = $itinerary_id;
        $condition['content'] = $content;
        model('report')->save($condition);
        return json(['flag'=>true]);

    }


}