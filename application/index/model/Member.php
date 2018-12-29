<?php


namespace app\index\Model;

use think\Model;
class Member extends Model
{

    //新增记录时自动完成字段
    protected $insert = ['status' => 1, 'sex' => 0];
    protected $type = [
        'birthday' => 'datetime:Y-m-d',
    ];

    /**
 * 登录
 * @param $email
 * @param $password
 * @return int
 */
    public function login($email, $password)
    {
        //获取用户数据
        $user = $this->where('email', $email)->field(true)->find()->toArray();
        //判断用户是否存在
        if ($user) {
            //判断用户可用状态
            if ($user['status']) {
                //验证用户密码
                if (sha256($password) === $user['password']) {//密码正确
                    //记录登录session
                    session('user', $user);
                    session('user_sign', dataAuthSign($user));

                    return $user['id']; //登录成功，返回用户ID
                } else {
                    return -2; //用户密码错误
                }
            } else {
                return -3; //用户被禁用
            }
        } else {
            return -1; //用户不存在
        }
    }

    /**
     * 注册
     * @param $email
     * @param $nickname
     * @param $password
     * @return int
     */
    public function register($email, $nickname, $password)
    {
        $data = array();
        //查询用户是否存在
        if ($user = $this->where('email', $email)->find()) {
            return -1; //用户已存在
        } else { //用户不存在
            $data["email"] = $email;
            $data["nickname"] = $nickname;
            $data["password"] = sha256($password);

            $this->save($data);

            return 1; //注册成功
        }
    }

    /**
     * 修改个人信息
     * @param $id
     * @param $post
     * @return int
     */
    public function updateProfile($id, $post)
    {
        if ($this->where('id', $id)->find()) {

            if ($this->allowField(true)->isUpdate(true)->save($post, ['id' => $id])) {

                $user = $this->where('id', $id)->find()->toArray();

                session('user', $user);
                session('user_sign', dataAuthSign($user));

                return 1; //更新成功
            } else {
                return -2; //更新失败
            }
        } else {
            return -1; //用户不存在
        }
    }

    /**
     * 修改密码
     * @param $id
     * @param $post
     * @return int
     */
    public function updatePassword($id, $post)
    {
        if ($user = $this->where('id', $id)->find()) {
            $post["password"] = sha256($post["password"]);
            if ($this->allowField(true)->isUpdate(true)->save($post, ['id' => $user["id"]])) {

                return 1; //修改成功
            } else {
                return -2; //修改失败
            }
        } else {
            return -1; //用户不存在
        }
    }

    /**
     * 修改账号
     * @param $id
     * @param $post
     * @return int
     */
    public function updateEmail($id, $post)
    {
        if ($user = $this->where(['id'=>$id])->find()) {
            if($user["password"] === sha256($post["password"])){

                $post["password"] = sha256($post["password"]);
                if ($this->allowField(true)->isUpdate(true)->save(['email'=>$post["email"]], ['id' => $user["id"]])) {

                    return 1; //修改成功
                } else {
                    return -2; //修改失败
                }
            }else {
                return -3; //密码错误
            }
        } else {
            return -1; //当前用户不存在
        }
    }

}