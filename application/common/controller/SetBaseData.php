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
    *state              boolean型，为true时，表示传递的$price为佣金金额，为false时，表示传递的$price为商品原价格
    *$price             商品原价格/商品佣金金额
    *$ratio             商品返佣比率,默认为配置比例
    *return             返回商品奖金
    * */
    static function setGoodsBonus($price,$state=true,$ratio=null){
        if($ratio==null){
            $ratio = ThinkConfig::get('bonus_ratio');
        }
        $bonus = null;
        if($state){
            $bonus = $price*$ratio;
        }else{
            $bonus = $price*($ratio/100)*$ratio;
        }
        if($bonus < 0.01){
            $bonus = 0.01;
        }
        return sprintf('%.2f', $bonus);
    }

    /*@setOrderRebate    设置用户返利金
    *$bonus             用户商品订单资金
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

}