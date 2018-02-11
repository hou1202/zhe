<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/8
 * Time: 10:33
 */
namespace app\index\controller;



use app\common\controller\CommController;
use app\common\controller\ReturnJson;
use think\Hook;
use think\Cookie;
use app\index\model\User;
use think\Validate;
use app\index\model\Order as OrderModel;

class Order extends CommController
{
    /*提交订单*/
    public function orderPush(){
        Hook::listen('CheckAuth',$params);
        if($this -> request -> isPost()){
            //用户信息判断
            $uid = Cookie::get('user');
            $user = new User();
            $userInfo = $user -> getUserInfoByMobile($uid);
            if(!$userInfo){
                return $this ->jsonFail('用户信息有误，请重新操作...');
            }
            //提交信息验证
            $data = $this -> request -> post();
            $rule = [
              'order_id' => 'require|number|length:18'
            ];
            $msg = [
                'order_id.require' => '提交订单编号不得为空...',
                'order_id.number' => '订单编号格式有误，请重新确认...',
                'order_id.length' => '订单编号格式有误，请重新确认...',

            ];
            $validate =  new Validate($rule,$msg);
            if(!$validate->check($data)) {
                return $this ->jsonFail($validate->getError());
            }
            $data['user_id'] = $userInfo['id'];
            $data['pid'] = $userInfo['p_id'];
            $order = new OrderModel();
            if($order->getOrderInfoByOrderId($data['order_id'])){
                return $this ->jsonFail('您的订单号已经提交，管理员正在审核中，请等待...');
            }
            if(!$order -> saveOrderDate($data)){
                return $this ->jsonFail('您的提交出现了一些小毛病，请重新操作...');
            }
            return $this->jsonSuccess('您已成功提交淘币兑换申请...','/index/order/orderVerify');
        }else{
            return $this -> fetch('order/order-push');
        }
    }

    /*审核订单*/
    public function orderVerify(){
        Hook::listen('CheckAuth',$params);
        $uid = Cookie::get('user');
        $user = new User();
        $userInfo = $user -> getUserIdByMobile($uid);
        if(!$userInfo){
            return ReturnJson::ReturnA('用户信息有误，请重新操作...');
        }
        $order = new OrderModel();
        $verify = $order -> getUserOrderById($userInfo['id'],0);
        return $this -> fetch('order/order-verify',['Verify' => $verify]);
    }

    /*订单完成*/
    public function orderFinish(){
        Hook::listen('CheckAuth',$params);
        $uid = Cookie::get('user');
        $user = new User();
        $userInfo = $user -> getUserIdByMobile($uid);
        if(!$userInfo){
            return ReturnJson::ReturnA('用户信息有误，请重新操作...');
        }
        $order = new OrderModel();
        $verify = $order -> getUserOrderById($userInfo['id'],1);
        return $this -> fetch('order/order-finish',['Verify' => $verify]);
    }
}