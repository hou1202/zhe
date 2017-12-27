<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 15:35
 */

namespace app\index\controller;


use think\Controller;

class Convert extends Controller {

    public function convert(){
        return $this -> fetch('convert/convert');
    }
}