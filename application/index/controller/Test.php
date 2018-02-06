<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:02
 */

namespace app\index\controller;


use app\common\controller\CommController;
use app\common\controller\ApiDataHandle;

class Test extends CommController {



    /*
     * @ todayCouponGoodsList() 今日优惠券列表
     * * */
    public function index()
    {
        $goodsList = ApiDataHandle::test();
        var_dump($goodsList);die;

    }


}