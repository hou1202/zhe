<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 15:22
 */

namespace app\index\controller;
use think\Hook;

use think\Controller;

class Personal extends Controller {

    public function personal(){

        //检测用户登录状态
        //Hook::listen('CheckAuth',$params);
        return $this -> fetch('personal/personal');
    }
}