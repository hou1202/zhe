<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:02
 */

namespace app\index\controller;


use think\Controller;

class Goods extends Controller {

    public function goodsBlock(){
        return $this -> fetch('goods/block-goods');
    }

    public function goodsStrip(){
        return $this -> fetch('goods/strip-goods');
    }

    public function goodsDetails(){
        return $this -> fetch('goods/details');
    }
}