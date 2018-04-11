<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/13
 * Time: 16:07
 */
namespace app\admin\model;


use think\Model;

class Goods extends Model
{
    public static $tableName = 'think_goods';

    //优惠券金额处理
    protected function getCouponMoneyAttr($value){
        $arrayPrice = array();
        preg_match_all('/\d+/',$value,$arrayPrice);
        if(count($arrayPrice[0]) > 1){
            if($arrayPrice[0][1] == '00'){
                return $arrayPrice[0][0];
            }else{
                return $arrayPrice[0][1];
            }
        }else{
            return $arrayPrice[0][0];
        }

    }

    /*
     * @getGoodsForList 商品列表显示
     * */
    public function getGoodsForList(){
        return $this -> field('id,goods_id,name,banner,class,price,ratio,commission,type,coupon_money,coupon_start,coupon_end,coupon_url')
            -> order('goods_id DESC')
            -> paginate(10,false,['path' => '/admin/main#/goods/goodsList' ]);
    }

    /*
     * @ getCountGoods   统计商品数量
     * */
    public function getCountGoods(){
        return $this -> count('goods_id');
    }

    /*
     * @ getOneGoodsInfoByGoodsId  获取指定商品信息
     * $id      商品GOODS_ID
     * */
    public function getOneGoodsInfoByGoodsId($id){
        return $this -> where('goods_id',$id) -> find();
    }

    /*
     *@ delGoodsById  删除指定商品
     * @id      商品GOODS_ID
     * */
    public function delGoodsById($id){
        return $this -> where('goods_id',$id) -> delete();
    }

    /*
     * @ getOverDueGoodsByTime  获取过期商品信息
     *
     * */
    public function getOverDueGoodsByTime(){
        return $this -> field('id,goods_id,name,banner,class,price,ratio,commission,type,coupon_money,coupon_start,coupon_end,coupon_url')
            -> where('coupon_end','<',date('Y-m-d',time()))
            -> order('coupon_end DESC')
            -> paginate(10,false,['path' => '/admin/main#/goods/goodsList' ]);
    }

    /*
    * @ getCountOverDueGoods   统计过期商品数量
    * */
    public function getCountOverDueGoods(){
        return $this -> where('coupon_end','<',date('Y-m-d',time())) -> count('goods_id');
    }

    /*
     *@ delAllOverGoods  删除全部过期商品
     * */
    public function delAllOverGoods(){
        return $this -> where('coupon_end','<',date('Y-m-d',time())) -> delete();
    }

    /*
     * @ getSearchGoodsByKeyword  通过关键词搜索商品
     * $keyword      关键词
     * */
    public function getSearchGoodsByKeyword($keyword){
        return $this -> field('id,goods_id,name,banner,class,price,ratio,commission,type,coupon_money,coupon_start,coupon_end,coupon_url')
            -> where('goods_id','like','%'.$keyword.'%')
            -> whereOr('name','like','%'.$keyword.'%')
            -> order('goods_id DESC')
            -> paginate(10,false,['path' => '/admin/main#/goods/goodsList' ]);
    }

    /*
     * @ getCountSearchUserByKeyword  统计通过关键词搜索商品
     * $keyword      关键词
     * */
    public function getCountSearchGoodsByKeyword($keyword){
        return $this -> field('goods_id')
            -> where('goods_id','like','%'.$keyword.'%')
            -> whereOr('name','like','%'.$keyword.'%')
            ->count();
    }

}