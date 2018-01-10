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

    /*
     * @getIndexPerfectCoupon() 首页精选好券20个
     * */
    public function getIndexPerfectCoupon(){
        return $this ->field('id,goods_id,name,banner,price,coupon_money')
                    -> order('commission DESC')
                    -> limit(20)
                    ->select();
    }

    /*
     * @getIndexHotCoupon() 首页爆款好券20个
     * */
    public function getIndexHotCoupon(){
        return $this ->field('id,goods_id,name,banner,price,coupon_money')
            -> order('sales DESC')
            -> limit(20)
            ->select();
    }

    /*
     * @getIndexListCoupon() 首页推荐好券50个
     * */
    public function getIndexListCoupon(){
        return $this ->field('id,goods_id,name,banner,price,coupon_money,sales')
            //-> order('sales DESC')
            -> limit(50)
            ->select();
    }

    /*
    * @getGoodsDetailsById()  产品详情
    *  $id     产品goods_id
    * */
    public function getGoodsDetailsById($id){
        return $this -> field('id,goods_id,name,banner,price,coupon_money,sales,commission,coupon_url,coupon_extend')
                    ->where('goods_id',$id)
                    ->find();
    }


    /*
    * @getRecommendGoodsById()  产品详情页面下推荐产品
    *  $id     产品goods_id
    * */
    public function getRecommendGoodsById($id){
        $class = $this->getClassById($id);
        return $this-> alias('l')
            ->field('id,goods_id,name,banner,price,coupon_money')
            ->where('class',$class['class'])
            ->order('commission DESC')
            ->limit(rand(1,50),8)
            ->select();
    }
    //@ getClassById 辅助于getRecommendGoodsById
    protected function getClassById($id){
        return $this-> field('class') -> where('goods_id',$id)->find();
    }


}