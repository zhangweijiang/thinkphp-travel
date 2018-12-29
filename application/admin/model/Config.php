<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/9
 * Time: 16:45
 */

namespace app\admin\model;


use think\Model;

class Config extends Model
{

    protected $insert = ['status' => 1];

    /**
     * 获取配置列表
     * @return array 配置数组
     */
    public function lists()
    {
        $map = array('status' => 1);
        $data = $this->where($map)->field('type,name,value')->select();

        $config = array();
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[$value['name']] = $this->parse($value['type'], $value['value']);
            }
        }

        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param integer $type 配置类型
     * @param string $value 配置值
     * @return array
     */
    public function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }

        return $value;
    }
}