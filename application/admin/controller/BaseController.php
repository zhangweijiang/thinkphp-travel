<?php


namespace app\admin\controller;


use extend\Auth;
use think\Controller;
use think\Db;

class BaseController extends Controller
{

    /**
     * 基础控制器构造方法
     * BaseController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        /* 读取数据库中的配置 */
        $config = cache('db_config_data');
        if (!$config) {
            $configModel = model('Config');
            $config = $configModel->lists();
            cache('db_config_data', $config);
        }
        config($config);




    }

    /**
     * 后台控制器初始化
     */
    public function _initialize()
    {
        //获取当前用户ID
        if (defined('UID')) {
            return;
        }
        define('UID', self::isLogin());
        if (!UID) { //还没登录，跳转到登录页面
            $this->redirect('Login/index');
        }
        //是否是超级管理员
        /*define('IS_ROOT', is_administrator());
        if (!IS_ROOT && config('admin_allow_ip')) {
            //检查访问的IP地址
            if (!in_array(request()->ip(), explode(',', config('admin_allow_ip')))) {
                $this->error('403:禁止访问');
            }
        }
        //检测系统权限
        if (!IS_ROOT) {
            $access = $this->accessControl();
            if ($access === false) {
                $this->error('403:禁止访问');
            } elseif ($access === NULL) {
                //检测访问权限
                $rule = strtolower('/' . $this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
                if (!$this->checkRule($rule)) {
                    $this->error('未授权访问');
                } else {
                    //检测分类(暂不扩展)
                }
            }
        }

        $this->assign('__MENU__', $this->getMenus());*/

    }

    /**
     * 检查用户是否登录
     * @return bool|mixed
     */
    public static function isLogin()
    {
        $admin = session('admin');
        if (empty($admin)) {
            return false;
        } else {
            return session('admin_sign') == dataAuthSign($admin) ? $admin['id'] : false;
        }

    }


    /**
     * 用户登入
     * @param array $user 用户信息
     * @return bool
     */
    public static function login($admin)
    {
        if (empty($admin)) {
            return false;
        }
        session('admin', $admin);
        session('admin_sign', dataAuthSign($admin));

        return true;
    }

    /**
     * 用户登出
     */
    public static function logout()
    {
        session('admin', NULL);
        session('admin_sign', NULL);
    }



    /**
     * action访问控制,在 登录成功状态下 执行的第一项权限检测(系统权限检测)
     * @return bool|null 返回值必须使用 `===` 进行判断
     *   返回false, 不允许任何人访问(仅超级管理员可访问)
     *   返回true, 允许任何管理员访问,无需执行节点权限检测
     *   返回null, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl()
    {
        $allow = config('allow_visit'); //允许任何管理员访问的url
        $deny = config('deny_visit'); //只允许超级管理员访问的url
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false; //非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }

        return NULL; //需要继续检测节点权限
    }

    final protected function checkRule($rule, $type=AuthRule::rule_url, $mode='url') {
        static $Auth = NULL;
        if(!$Auth) {
            $Auth = new Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)) {
            return false;
        }
        return true;
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model $model 模型名或模型实例
     * @param array $where where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order 排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean $field 单表模型用不到该参数,要用在多表join时为field()方法指定参数
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists($model, $where = array(), $order = '', $field = true)
    {
        $options = array();
        $REQUEST = (array)input('request.');
        if (is_string($model)) {
            $model = Db::name($model);
        }
        $pk = $model->getPk();

        if ($order === NULL) {
            //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk . ' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);

        if (empty($where)) {
            $where = array('status' => array('egt', 0));
        }
        if (!empty($where)) {
            $options['where'] = $where;
        }


        $total = $model->where($options['where'])->count();

        if (isset($REQUEST['r'])) {
            $listRows = (int)$REQUEST['r'];
        } else {
            $listRows = config('list_rows') > 0 ? config('list_rows') : 10;
        }
        // 分页查询
        $list = $model->where($options['where'])->order($order)->field($field)->paginate($listRows);

        // 获取分页显示
        $page = $list->render();
        // 模板变量赋值
        // $this->assign('list', $list);
        $this->assign('_page', $page);
        $this->assign('_total', $total);
        if ($list && !is_array($list)) {
            $list = $list->toArray();
        }

        return $list['data'];
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array $data 修改的数据
     * @param array $where 查询时的where()方法的参数
     * @param array|boolean $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                    url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     */
    final protected function editRow($model, $data, $where, $msg = false)
    {
        $id = input('id/a');
        if (!empty($id)) {
            $id = array_unique($id);
            $id = is_array($id) ? implode(',', $id) : $id;
            //如存在id字段，则加入该条件
            $fields = db()->getTableFields(array('table' => config('database.prefix') . $model));

            if (in_array('id', $fields) && !empty($id)) {
                $where = array_merge(array('id' => array('in', $id)), (array)$where);
            }
        }

        $msg = array_merge(array('success' => '操作成功！', 'error' => '操作失败！', 'url' => '', 'ajax' => var_export(Request()->isAjax(), true)), (array)$msg);

        if (db($model)->where($where)->update($data) !== false) {
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        } else {
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }
}