<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/8
 * Time: 10:33
 */
namespace app\index\controller;



use app\common\controller\CommController;

class Order extends CommController
{
    public function orderPush(){
        return $this -> fetch('order/order-push');
    }

    public function orderVerify(){
        return $this -> fetch('order/order-verify');
    }

    public function orderFinish(){
        return $this -> fetch('order/order-finish');
    }
}