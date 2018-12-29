<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/18
 * Time: 15:02
 */
namespace app\admin\model;

use think\Db;
use think\Model;

class AuthGroup extends Model
{

    //设置新增时自动完成的属性
//    protected $insert = ['status' => 1];

    const TYPE_ADMIN = 1; //管理员用户组类型标识
    const MEMBER = 'member';
    const AUTH_GROUP_ACCESS = 'auth_group_access'; //关系表表名
    const AUTH_GROUP = 'auth_group'; //用户组表名

    /**
     * 返回用户组列表
     * 默认返回正常状态的管理员用户组列表
     * @param array $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getGroup($where = array())
    {
        $map = array('status' => 1, 'type' => self::TYPE_ADMIN, 'module' => 'admin');
        $map = array_merge($map, $where);

        return $this->where($map)->select();
    }

    /**
     * 把用户添加到用户组,支持批量添加用户到用户组
     * 示例: 把uid=1的用户添加到group_id为1,2的组 `AuthGroupModel->addToGroup(1,'1,2');`
     * @param Integer|array $uid 用户id
     * @param Integer|array $gid 用户组id
     * @return bool
     */
    public function addToGroup($uid, $gid)
    {
        $uid = is_array($uid) ? explode(',', $uid) : trim($uid, ',');
        $gid = is_array($gid) ? $gid : explode(',', trim($gid, ','));

        $Access = Db::name(self::AUTH_GROUP_ACCESS);

        $uid_arr = explode(',', $uid);
        $uid_arr = array_diff($uid_arr, array(config('user_administrator')));

        $add = array();
        foreach ($uid_arr as $u) {
            //先删除旧数据
            $Access->where(array('uid' => array('in', $u)))->delete();
            //判断用户id是否合法
            if (Db::name('admin')->field('id')->where('id', $u) === false) {
                $this->error = "编号为{$u}的用户不存在";

                return false;
            }
            foreach ($gid as $g) {
                if (is_numeric($u) && is_numeric($g)) {
                    $add[] = array('group_id' => $g, 'uid' => $u);
                }
            }
        }
        if (Db::name(self::AUTH_GROUP_ACCESS)->insertAll($add)) {

            return true;
        } else {
            $Access->error = '添加失败';

            return false;
        }

    }

    /**
     * 返回用户所属用户组信息
     * @param int $uid 用户id
     * @return array 用户所属的用户组 array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开')
     */
    static public function getUserGroup($uid) {
        static $groups = array();
        if(isset($groups[$uid])) {
            return $groups[$uid];
        }
        $user_groups = Db::name(self::AUTH_GROUP_ACCESS)->alias('a')->field('uid,group_id,title,description,rules')->join(self::AUTH_GROUP." g"," a.group_id=g.id")->where("a.uid='$uid' and g.status='1'")->select();
        $groups[$uid] = $user_groups ? $user_groups : array();

        return $groups[$uid];
    }


}