<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/10
 * Time: 13:48
 */
namespace app\index\model;


use think\Model;

class Goods extends Model
{
    public static $tableName = 'think_goods';

    protected function getCommissionAttr($value){
        return $value = sprintf("%1\$.2f",$value*0.3);
    }

    protected function getTypeAttr($value){
        $type = ['淘宝' => '/static/index/images/b-logo.png','天猫' => '/static/index/images/t-logo.png'];
        return $type[$value];
    }

    /*
     * @getIndexPerfectCoupon() 首页精选好券20个
     * */
    public function getIndexPerfectCoupon(){
        return $this ->field('id,goods_id,name,banner,price,sales,type,coupon_money')
                    -> order('commission DESC')
                    -> limit(20)
                    ->select();
    }

    /*
     * @getPerfectCoupon() 精选好券列表
     * */
    public function getPerfectCoupon(){
        return $this ->field('id,goods_id,name,banner,price,type,sales,coupon_money')
            -> order('commission DESC')
            -> limit(1,200)
            ->select();
    }


    /*
     * @getIndexHotCoupon() 首页爆款好券20个
     * */
    public function getIndexHotCoupon(){
        return $this ->field('id,goods_id,name,banner,price,type,sales,coupon_money')
            -> order('sales DESC')
            -> limit(20)
            ->select();
    }

    /*
     * @getHotCoupon() 爆款好券列表
     * */
    public function getHotCoupon(){
        return $this ->field('id,goods_id,name,banner,price,sales,type,coupon_money')
            -> order('sales DESC')
            -> limit(1,200)
            ->select();
    }

    /*
     * @getIndexListCoupon() 首页推荐好券50个
     * */
    public function getIndexListCoupon(){
        return $this ->field('id,goods_id,name,banner,price,coupon_money,sales,type,sales')
            //-> order('sales DESC')
            -> limit(50)
            ->select();
    }

    /*
    * @getGoodsDetailsById()  产品详情
    *  $id     产品goods_id
    * */
    public function getGoodsDetailsById($id){
        return $this -> field('id,goods_id,name,banner,detail_url,price,type,tao_url,coupon_money,sales,commission,coupon_url,coupon_extend')
                    ->where('goods_id',$id)
                    ->find();
    }


    /*
    * @getRecommendGoodsById()  产品详情页面下推荐产品
    *  $id     产品goods_id
    * */
    public function getRecommendGoodsById($id){
        $class = $this->getClassById($id);
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
                    -> where('class',$class['class'])
                    -> order('commission DESC')
                    -> limit(rand(1,50),8)
                    -> select();
    }
    //@ getClassById 辅助于getRecommendGoodsById
    protected function getClassById($id){
        return $this-> field('class') -> where('goods_id',$id)->find();
    }



    /*
     * @getGoodsListByTime() 今日优惠券列表
     * */
    public function getGoodsListByTime(){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
                    -> order('coupon_start DESC')
                    -> limit(1,200)
                    -> select();
    }

    /*
     * @getGoodsListByTmall() 天猫优惠券列表
     * */
    public function getGoodsListByTmall(){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
                    -> where('type','天猫')
                    -> limit(1,200)
                    -> select();
    }


    /*
     * @getGoodsListByCouponMoney() 大额优惠券列表
     * */
    public function getGoodsListByCouponMoney(){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
                    -> order('coupon_money DESC')
                    -> limit(1,200)
                    -> select();
    }

    /*
     * @getGoodsListByNine() 9.9优惠券列表
     * */
    public function getGoodsListByNine(){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
                    ->where('(price-coupon_money) < 10')
                    -> limit(1,200)
                    -> select();
    }

    /*
     * @getGoodsListByRatio() 高奖金优惠券列表
     * */
    public function getGoodsListByRatio(){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
            -> order('ratio DESC')
            -> limit(1,200)
            -> select();
    }

    /*
     * @getCouponSquare() 广场
     * */
    public function getCouponSquare(){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
            -> limit(rand(1,500),500)
            -> select();
    }

    /*
     * @getCouponNav() 导航优惠券
     * */
    public function getCouponNav($nav){
        return $this -> field('id,goods_id,name,banner,price,type,sales,coupon_money')
            -> where('class',$nav)
            -> limit(1,200)
            -> select();
    }




}