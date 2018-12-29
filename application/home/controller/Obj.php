<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 23:25
 */

namespace app\home\controller;


use think\Controller;

class Obj extends Controller
{
    public function index(){

        return $this->fetch();
    }
}