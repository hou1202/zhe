<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:02
 */

namespace app\index\controller;


use app\common\controller\CommController;
use app\index\model\Goods as GoodModel;
use app\common\controller\ReturnJson;

class Test extends CommController {



    /*
     * @ todayCouponGoodsList() 今日优惠券列表
     * * */
    public function index(){
        $goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByTime();
        return $this -> fetch('index/test',['List' => $goodsList]);

    }


}