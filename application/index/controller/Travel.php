<?php


namespace app\index\controller;

use app\index\model\Member as MemberModel;
use app\index\model\Travel as TravelModel;
use app\index\model\TravelComment as TravelCommentModel;
use app\index\validate\Travel as TravelValidate;
use think\Db;

class Travel extends BaseController
{

    /**
     * 游记列表页
     * @return mixed
     */
    public function index()
    {
        $travelList = model('travel')->where('status', 1)->paginate(10, false);
        foreach ($travelList as $item) {
            $item['viewNum'] = Db::name('travel_view')->where('travel_id', $item['id'])->count();
            $item['commentNum'] = Db::name('travel_comment')->where('travel_id', $item['id'])->count();
        }

        $travelNew = Db::name('travel')->limit(0, 3)->where('status', 1)->order('create_time desc')->select();


        $page = $travelList->render();
        $this->assign('travelList', $travelList);
        $this->assign('travelNew', $travelNew);
        $this->assign('page', $page);

        return $this->fetch();
    }

    public function travelListByPage()
    {

    }


    /**
     * 发布游记页面
     * @return mixed
     */
    public function add()
    {

        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        if (request()->isPost()) {
            $validate = new TravelValidate;
            $travel = new TravelModel;
            $result = $validate->batch()->check($_POST);

            $this->assign('travel', $_POST);

            //对用户注册数据进行校验
            if ($result === false) {//数据错误,输出错误信息
                dump($validate->getError());
                $this->assign('validate', $validate->getError());
            } else {
                $flag = $travel->add(session('user.id'), $_POST);
                if ($flag > 0) {
                    $this->success("发布成功", 'travel/detail?id=' . $flag);
                } else {
                    switch ($flag) {
                        case -1:
                            $error = '发布失败！';
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
     * 游记编辑页面
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }

        $validate = new TravelValidate;
        $travelModel = new TravelModel;
        $travel = model('travel')->where('id', $id)->find();
        if (request()->isPost()) {

            $result = $validate->batch()->check($_POST);

            $this->assign('travel', $_POST);

            //对用户注册数据进行校验
            if ($result === false) {//数据错误,输出错误信息
                $this->assign('validate', $validate->getError());
            } else {
                $flag = $travelModel->edit($_POST,$id);

                if ($flag > 0) {
                    $this->success("更新成功", 'travel/detail?id=' . $flag);
                } else {
                    switch ($flag) {
                        case -1:
                            $error = '更新失败！';
                            break;
                        default:
                            $error = '未知错误！';
                            break;
                    }
                    $this->assign("error", $error);
                }
            }
        }
        $this->assign("travel", $travel);

        return $this->fetch();
    }

    /**
     * 游记详情
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {

        $travel = model('travel')->where('id', $id)->find();
        if ($travel) {
            if (session('user.id')) {
                $data['member_id'] = session('user.id');
                $data['member_ip'] = request()->ip();
                $data['travel_id'] = $id;
                if (!Db::name('travel_view')->where($data)->find()) {
                    model('travel_view')->save($data);
                }
                $travelLike = Db::name('travel_like')->where(['member_id' => session('user.id'), 'travel_id' => $id])->find();
                if ($travelLike) {
                    $travel["like"] = 1;
                }
            }

            $travel['viewNum'] = Db::name('travel_view')->where('travel_id', $id)->count();
            $travel['likeNum'] = Db::name('travel_like')->where('travel_id', $id)->count();
            $travel['commentNum'] = Db::name('travel_comment')->where('travel_id', $id)->count();

            $comments = array();
            $travel['comments'] = $this->CommentList($id, 0, $comments, 0, NULL);

            $member = model('member')->where('id', $travel['author'])->find();
            $travel['author_name'] = $member["nickname"];
            $travel['author_avatar'] = $member["avatar"];

            $travel['tags'] = explode(',', $travel['tags']);

            $travelNew = Db::name('travel')->limit(3)->order('create_time desc')->where('status', 1)->select();

            $report_type=model('report_type')->select();
            $this->assign('report_type',$report_type);

            $this->assign('travelNew', $travelNew);
            $this->assign('travel', $travel);
        } else {
            $this->error("参数错误！", "travel/list");
        }

        return $this->fetch();
    }

    /**
     * 游记评论
     * @param $travel_id
     * @param $comment_pid
     */
    public function comment($travel_id, $comment_pid)
    {
        $data["travel_id"] = $travel_id;
        $data["id"] = $comment_pid;
        if ($this->isLogin() === false) {//未登录
            $this->error("还没登录，请先登录..", 'login/index');
        }
        if (request()->isPost()) {
            $travelComment = new TravelCommentModel;
            if ($comment_pid == 0) {
                $travelComment->comment(0, $travel_id, session('user.id'), 0, $_POST["content"]);
            } else {
                $pcomment = Db::name('travel_comment')->where($data)->find();
                $travelComment->comment($pcomment["id"], $travel_id, session('user.id'), $pcomment["member_id"], $_POST["content"]);
            }
        }

        $this->redirect('travel/detail?id=' . $travel_id);
    }


    /**
     * 游记无限评论列表
     * @param int $pid
     * @param array $commentList
     * @param int $spac
     * @param null $pauthor
     * @return array
     */
    public function CommentList($travel_id, $pid = 0, &$commentList = array(), $spac = 0, $pauthor = NULL)
    {
        $member = new MemberModel;
        static $i = 0;
        $spac = $spac + 1;//初始为1级评论
        $pauthor = $pauthor;
        $List = Db::name('travel_comment')->where(['pid' => $pid, 'travel_id' => $travel_id])->select();
        foreach ($List as $k => $v) {
            $commentList[$i]['level'] = $spac;//评论层级
            $commentList[$i]['member_id'] = $v['member_id'];
            $res1 = $member->field('nickname,avatar')->where('id', $v['member_id'])->find();
            $commentList[$i]['nickname'] = $res1['nickname'];
            $commentList[$i]['avatar'] = $res1['avatar'];
            $commentList[$i]['id'] = $v['id'];
            $commentList[$i]['pid'] = $v['pid'];//此条评论的父id
            $commentList[$i]['content'] = $v['content'];
            $commentList[$i]['create_time'] = $v['create_time'];
            $commentList[$i]['member_pid'] = $pauthor;//此条评论是回复谁的
            if ($pauthor != 0) {
                $res2 = $member->field('nickname,avatar')->where('id', $pauthor)->find();
                $commentList[$i]['pnickname'] = $res2['nickname'];
                $commentList[$i]['pavatar'] = $res2['avatar'];
            }
            $i++;
            $this->CommentList($travel_id, $v['id'], $commentList, $spac, $v['member_id']);
        }

        return $commentList;
    }

    /**
     * 点击喜爱游记
     * @param $id
     * @return \think\response\Json
     */
    public function travelLike($id)
    {
        if (request()->isPost()) {
            if ($this->isLogin() !== false) {
                $data["member_id"] = session('user.id');
                $data["travel_id"] = $_POST["id"];
                if (!Db::name('travel_like')->where($data)->find()) {
                    model('travel_like')->save($data);

                    return json(['flag' => true]);
                } else {
                    return json(['flag' => false]);
                }
            } else {
                return json(['flag' => false]);
            }
        } else {
            return json(['flag' => false]);
        }

    }

    /**
     * 上传封面
     * @return mixed|\think\response\Json
     */
    public function uploadCover()
    {

        if (request()->isPost()) {
            $file = request()->file('avatar_file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/' . session('user.id'));
            if ($info) {
                $url = '/upload/' . session('user.id') . '/' . $info->getSaveName();

                return json(['result' => $url]);
            } else {
                echo $file->getError();
            }
        }
    }

    /**
     * 举报
     * @param $report_type
     * @param $type
     * @param $report_id
     * @param $content
     * @return \think\response\Json
     */
    public function report($report_type,$type,$report_id,$content) {
        $condition['report_type'] = $report_type;
        $condition['type'] = $type;//1表示行程，2表示游记
        $condition['member_id'] = session('user.id');
        $condition['reported_id'] = $report_id;
        $condition['content'] = $content;
        model('report')->save($condition);
        return json(['flag'=>true]);

    }

}