<?php
namespace app\home\controller;

use think\captcha\Captcha;
use think\Config;
use think\Controller;
use think\Request;

class Index extends Controller
{
    public function index()
    {
        session('abc','123');

        return $this->fetch();
    }

    public function index2(){
        return $this->fetch();
    }
}
