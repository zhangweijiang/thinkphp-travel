<?php


namespace app\index\controller;


use app\index\Model\Message as MessageModel;
use think\Db;

class MessageCenter extends BaseController
{

    /**
     * 评论列表
     * @return mixed
     */
    public function comment()
    {

        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }

        $travelComments = Db::name('travel_comment')->alias('a')->join('think_travel t', 'a.travel_id = t.id')->join('think_member m', 'a.member_id = m.id')->where(['pid' => 0])->where('member_id','neq',session('user.id'))->where(function ($query) {
            $query->where('travel_id', 'in', function ($query) {
                $query->name('travel')->field('id')->where(['author' => session('user.id')]);
            });
        })->whereOr(function ($query) {
            $query->where(['member_pid' => session('user.id')]);
        })->field('a.id,a.member_id,a.travel_id,a.create_time,a.content,t.title,m.nickname,m.avatar')->order('a.create_time desc')->select();


        for ($i = 0; $i < sizeof($travelComments); $i++) {
            $res = Db::name('travel_comment')->update(['view_status' => 1,'id' => $travelComments[$i]['id']]);
        }

        $itineraryComments = Db::name('itinerary_comment')->alias('a')->join('think_itinerary t', 'a.itinerary_id = t.id')->join('think_member m', 'a.member_id = m.id')->where(['member_pid' => 0])->where(function ($query) {
            $query->where('itinerary_id', 'in', function ($query) {
                $query->name('itinerary')->field('id')->where(['member_id' => session('user.id')]);
            });
        })->whereOr(function ($query) {
            $query->where(['member_pid' => session('user.id')]);
        })->field('a.id,a.member_id,a.itinerary_id,a.create_time,a.content,t.title,m.nickname,m.avatar')->order('create_time desc')->select();

        for ($i = 0; $i < sizeof($itineraryComments); $i++) {
            $res = Db::name('travel_comment')->update(['view_status' => 1,'id' => $itineraryComments[$i]['id']]);
        }


        $this->assign('travelComments', $travelComments);
        $this->assign('itineraryComments', $itineraryComments);

        return $this->fetch();
    }

    /**
     * 通知列表
     * @return mixed
     */
    public function notice()
    {

        $notices = model('notice')->where(['user_id'=>session('user.id')])->select();
        foreach ($notices as $item){
            $res = Db::name('notice')->update(['view_status' => 1,'id' => $item["id"] ]);
        }


        $this->assign('notices',$notices);
        return $this->fetch();
    }

    /**
     * 私信列表
     * @return mixed
     */
    public function message()
    {
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        $messages = Db::name('message')->alias('a')->join('think_member b','a.member_id = b.id')->field('a.id,a.member_id,a.create_time,a.content,b.avatar,b.nickname')->where('receive_id',session('user.id'))->order('a.create_time desc')->select();
        foreach ($messages as $item){
            $res = Db::name('message')->update(['view_status' => 1,'id' => $item["id"] ]);
        }


        $this->assign('messages', $messages);
        return $this->fetch();
    }

    /**
     * 发送私信
     * @return \think\response\Json
     */
    public function sendMessage() {

        if(request()->isPost()){
            if($this->isLogin() === false){
                return json(['flag'=>false,'message'=>'还未登录，请先登录...']);
            }else{
                if($_POST["message"] == null || $_POST["message"] == ""){
                    return json(['flag'=>false,'message'=>'私信内容不能为空！']);
                }else {
                    $data["receive_id"] = $_POST['id'];
                    $data["member_id"] = session('user.id');
                    $data["content"]=$_POST['message'];

                    $message = model('message')->save($data);

                    return json(['flag'=>true,'message'=>'发送成功！']);
                }
            }
        }else{
            return json(['flag'=>false,'message'=>'表单提交失败！']);
        }

    }

    /**
     * 删除私信
     * @return \think\response\Json
     */
    public function delMessage(){

        if(request()->isPost()){
            if($message = model('message')->where($_POST)->find()){
                $res = model('message')->where($_POST)->delete();
                return json(['flag'=>true,'message'=>'删除成功！']);
            }else {
                return json(['flag'=>false,'message'=>'要删除的私信不存在！']);
            }
        }else{
            return json(['flag'=>false,'message'=>'表单提交失败！']);
        }
    }

}