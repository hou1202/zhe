<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/2/1
 * Time: 16:54
 */

namespace app\common\controller;
use think\Config as ThinkConfig;

class SetBaseData
{

    /*@setGoodsBonus    设置用户奖金
    *state              boolean型
     *      true时，表示传递的$price为佣金金额，
     *      false时，表示传递的$price为商品原价格
    *$price             商品原价格/商品佣金金额
    *$ratio            $state为false(商品原价格)时，商品的佣金比率
    *$rebate             商品返佣比率,默认为配置比例
    *return             返回商品奖金
    * */
    static function setGoodsBonus($price,$state=true,$ratio=null,$rebate=null){
        if($rebate==null){
            $rebate = ThinkConfig::get('bonus_ratio');
        }
        $bonus = null;
        if($state){
            $bonus = $price*$rebate;
        }else{
            $bonus = $price*($ratio/100)*$rebate;
        }
        if($bonus < 0.01){
            $bonus = 0.01;
        }
        return sprintf('%.2f', $bonus);
    }

    /*@setOrderRebate    设置用户返利金
    *$bonus             用户商品订单奖金
    *$ratio             商品返利比率,默认为配置比例
    *return             返回商品奖金
    * */
    static function setOrderRebate($bonus,$ratio=null){
        if($ratio==null){
            $ratio = ThinkConfig::get('invite_ratio');
        }
        $rebate = null;
        $rebate = $bonus*$ratio;
        if($rebate < 0.01){
            $rebate = 0.01;
        }
        return sprintf('%.2f', $rebate);
    }

    /*@setCouponInfo    过滤原始优惠券信息，得出优惠金额
    *$data             原始优惠券信息
    *return            优惠券金额
    * */
    static function setCouponInfo($data){
        $arrayPrice = array();
        preg_match_all('/\d+/',$data,$arrayPrice);
        return $arrayPrice[0][1];
    }

}