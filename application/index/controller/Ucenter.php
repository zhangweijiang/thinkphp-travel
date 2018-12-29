<?php

namespace app\index\controller;

use app\index\validate\Profile as ProfileValidate;
use app\index\validate\Password as PasswordValidate;
use app\index\validate\Edit as EditValidate;
use app\index\model\Member as MemberModel;
use think\Db;
use think\File;

class Ucenter extends BaseController
{
    /**
     * 个人中心主页
     * @param $id
     * @return mixed
     */
    public function index($id)
    {

        if ($id == session('user.id')) {
            $isMe = 1;
        } else {
            $isMe = 0;
        }
        $member = model('member')->where('id', $id)->find();

        $travels = model('travel')->where(['author' => $id, 'status' => 1])->select();
        foreach ($travels as $item) {
            $item['viewNum'] = model('travel_view')->where('travel_id', $item['id'])->count();
            $item['likeNum'] = model('travel_like')->where('travel_id', $item['id'])->count();
            $item['commentNum'] = model('travel_comment')->where('travel_id', $item['id'])->count();
        }
        $travelNum = model('travel')->where(['author' => $id, 'status' => 1])->count();
        $itinerarys = model('itinerary')->alias('a')->join('itinerary_member b','a.id=b.itinerary_id')->where(['b.member_id'=>$id,'a.status'=>1])->select();
        foreach ($itinerarys as $item1) {
            $item1['likeNum'] = model('itinerary_like')->where('itinerary_id', $item1['id'])->count();
            $item1['commentNum'] = model('itinerary_comment')->where('itinerary_id', $item1['id'])->count();
        }

        $itineraryNum = model('itinerary')->where(['member_id' => $id, 'status' => 1])->count();
        $itineraryNum += model('itinerary_member')->where(['member_id' => $id, 'status' => 1])->count();

        $this->assign('travelNum', $travelNum);
        $this->assign('itineraryNum', $itineraryNum);
        $this->assign('travels', $travels);
        $this->assign('itinerarys', $itinerarys);
        $this->assign('member', $member);
        $this->assign('isMe', $isMe);

        return $this->fetch();

    }

    /**
     * 个人信息设置页面
     * @return mixed
     */
    public function profile()
    {
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        $member = model('Member')->where('id', session('user.id'))->find();
        if (request()->isPost()) {
            $validate = new ProfileValidate;
            $profile = new MemberModel;

            $result = $validate->batch()->check($_POST);
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $flag = $profile->updateProfile(session('user.id'), $_POST);
                if ($flag > 0) {
                    $this->success("更新成功！", "ucenter/profile");
                } else {
                    switch ($flag) {
                        case -1:
                            $this->error("用户不存在，请重新登录...", "logout");
                            break;
                        case -2:
                            $error = '更新失败!';
                            break;
                        default:
                            $error = '未知错误！';
                            break;
                    }
                    $this->assign("error", $error);
                }
            }

        }
        $this->assign('member', $member);

        return $this->fetch();
    }

    /**
     * 账号修改页面
     * @return mixed
     */
    public function edit()
    {
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        if (request()->isPost()) {
            $validate = new EditValidate;
            $edit = new MemberModel;

            $result = $validate->batch()->check($_POST);
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $flag = $edit->updateEmail(session('user.id'), $_POST);
                if ($flag > 0) {
                    $this->success("修改成功！请重新登录...", "logout");
                } else {
                    switch ($flag) {
                        case -1:
                            $this->error("用户不存在，请重新登录...", "logout");
                            break;
                        case -2:
                            $error = '修改失败!';
                            break;
                        case -3:
                            $error = '密码错误!';
                            break;
                        default:
                            $error = '未知错误！';
                            break;
                    }
                    $this->assign("error", $error);
                }
            }

        }

        return $this->fetch();
    }

    /**
     * 修改密码页面
     * @return mixed
     */
    public function password()
    {
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        if (request()->isPost()) {
            $validate = new PasswordValidate;
            $password = new MemberModel;

            $result = $validate->batch()->check($_POST);
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $flag = $password->updatePassword(session('user.id'), $_POST);
                if ($flag > 0) {
                    $this->success("修改成功！请重新登录...", "logout");
                } else {
                    switch ($flag) {
                        case -1:
                            $this->error("用户不存在，请重新登录...", "logout");
                            break;
                        case -2:
                            $error = '修改失败!';
                            break;
                        default:
                            $error = '未知错误！';
                            break;
                    }
                    $this->assign("error", $error);
                }
            }

        }

        return $this->fetch();
    }

    /**
     * 头像修改页面
     * @return mixed
     */
    public function avatar()
    {

        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        if (request()->isPost()) {
            $avatar = request()->file('avatar');
            $url = ROOT_PATH . 'public' . DS . 'uploads/user/' . session('user.id');
            $info = $avatar->validate(['ext' => 'jpg,jpeg,png'])->rule('md5')->move($url);
            if ($info) {
                $filename = $info->getSaveName();
                echo $filename;
                $member = new MemberModel;
                $result = $member->allowField(true)->isUpdate(true)->save(['avatar' => $url . '/' . $filename], ['id' => session('user.id')]);
                if ($result) {
                    $user = session('user');
                    $user["avatar"] = $url . '/' . $filename;
                    session('user', $user);
                    session('user_sign', dataAuthSign($user));
                } else {
                    $this->error($member->getError(), "ucenter/avatar");
                }

            } else {
                // 上传失败获取错误信息
                $this->error($avatar->getError(), "ucenter/avatar");
            }

        }

        return $this->fetch();
    }

    /**
     * 头像上传页面
     * @return mixed
     */
    public function uploadAvatar()
    {
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }

        return $this->fetch();
    }

    /**
     * 保存头像
     * @return mixed
     */
    public function saveAvatar()
    {
        if (request()->isPost()) {
            $file = request()->file('avatar_file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/' . session('user.id'));
            if ($info) {
                $url = '/upload/' . session('user.id').'/'.$info->getSaveName();
                $user =session('user');
                $user['avatar'] = $url;
                model('member')->save($user,$user['id']);
                session('user', $user);
                session('user_sign', dataAuthSign($user));

                return json(['result'=> $url]);
            } else {

                echo $file->getError();
            }
        }

        return $this->fetch();
    }


    /**
     * 照片墙页面
     * @param $id
     * @return mixed
     */
    public function photo($id)
    {

        $member = model('member')->where('id', $id)->find();

        if ($id == session('user.id')) {
            $isMe = 1;
            $itinerarys = model('itinerary')->where(['id' => $id, 'status' => 1])->find();
        } else {
            $isMe = 0;
            $itinerarys = model('itinerary')->where(['id' => $id, 'status' => 1, 'view_status' => 1])->find();
        }

        $photos = model('itinerary_photo')->where('member_id', $id)->where('itinerary_id', 'in', $itinerarys)->paginate(16, false);

        $page = $photos->render();


        $this->assign('member', $member);
        $this->assign('photos', $photos);
        $this->assign('page', $page);
        $this->assign('isMe', $isMe);

        return $this->fetch();
    }

}