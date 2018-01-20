<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:02
 */

namespace app\index\controller;


use think\Controller;
use app\index\model\Goods as GoodModel;
use app\common\controller\ReturnJson;

class Goods extends Controller {

    public function goodsBlock(){
        return $this -> fetch('goods/block-goods');
    }

    public function goodsStrip(){
        return $this -> fetch('goods/strip-goods');
    }

    /*
    * @goodsDetails()  产品详情
     * $id 通过GET方式传过来的产品id
    * */
    public function goodsDetails(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $goods = new GoodModel();
            $getOne = $goods -> getGoodsDetailsById($id);
            $Recommend = $goods ->getRecommendGoodsById($id);
            return $getOne ?  $this -> fetch('goods/details',['getOne' => $getOne,'Recommend' => $Recommend]) : ReturnJson::ReturnA("未获取到相应的产品信息...");
        }else{
            return ReturnJson::ReturnA("未获取到相应的产品信息...");
        }
    }

    /*
     * @getAreaType 首页快捷专区进入类型判断
     * @type    类型
     *      1 =》 1号区域，今日新券
     *      2 =》 2号区域，高奖金券
     *      3 =》 3号区域，天猫好券
     *      4 =》 4号区域，大额好券
     *      5 =》 5号区域，9.9券
     * */
    public function getAreaType(){
        if(isset($_GET['type']) && !empty($_GET['type'])){
            $data = $this -> request -> get();
            //var_dump($type);die;
            $goods = new GoodModel();
            $goodsList = null;
            if($data['type'] == 1){
                if(isset($data['id'])){
                    $goodsList = $goods -> getGoodsListByTime($data['id'],$data['sort']);
                }else{
                    $goodsList = $goods -> getGoodsListByTime();
                }
            }elseif($data['type'] == 2){
                if(isset($data['id'])){
                    $goodsList = $goods -> getGoodsListByRatio($data['id'],$data['sort']);
                }else{
                    $goodsList = $goods -> getGoodsListByRatio();
                }
            }elseif($data['type'] == 3){
                if(isset($data['id'])){
                    $goodsList = $goods -> getGoodsListByTmall($data['id'],$data['sort']);
                }else{
                    $goodsList = $goods -> getGoodsListByTmall();
                }
            }elseif($data['type'] == 4){
                if(isset($data['id'])){
                    $goodsList = $goods -> getGoodsListByCouponMoney($data['id'],$data['sort']);
                }else{
                    $goodsList = $goods -> getGoodsListByCouponMoney();
                }
            }elseif($data['type'] == 5){
                if(isset($data['id'])){
                    $goodsList = $goods -> getGoodsListByNine($data['id'],$data['sort']);
                }else{
                    $goodsList = $goods -> getGoodsListByNine();
                }
            }
            return $this -> fetch('goods/area-goods',['List' => $goodsList]);
        }else{
            $this -> redirect('/index/goods/couponSquare');
        }
    }

    /*
     * @ todayCouponGoodsList() 今日优惠券列表
     * * */
    public function todayCouponGoodsList($goods){
        //$goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByTime();
        //return $this -> fetch('goods/block-goods',['List' => $goodsList]);

    }

    /*
    * @ tmallCouponGoodsList() 天猫专属优惠券列表
    * * */
    public function tmallCouponGoodsList(){
        $goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByTmall();
        return $this -> fetch('goods/block-goods',['List' => $goodsList]);

    }

    /*
    * @ moneyCouponGoodsList() 大额优惠券列表
    * * */
    public function moneyCouponGoodsList(){
        $goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByCouponMoney();
        return $this -> fetch('goods/block-goods',['List' => $goodsList]);

    }

    /*
    * @ nineCouponGoodsList() 9.9优惠券列表
    * * */
    public function nineCouponGoodsList(){
        $goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByNine();
        return $this -> fetch('goods/block-goods',['List' => $goodsList]);

    }

    /*
    * @ ratioCouponGoodsList() 高奖金优惠券列表
    * * */
    public function ratioCouponGoodsList(){
        $goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByRatio();
        return $this -> fetch('goods/block-goods',['List' => $goodsList]);

    }

    /*
    * @ perfectCouponGoodsList() 精选好券列表
    * * */
    public function perfectCouponGoodsList(){
        $goods = new GoodModel();
        $goodsList = $goods -> getPerfectCoupon();
        return $this -> fetch('goods/strip-goods',['List' => $goodsList]);

    }

    /*
    * @ HotCouponGoodsList() 爆款好券列表
    * * */
    public function HotCouponGoodsList(){
        $goods = new GoodModel();
        $goodsList = $goods -> getHotCoupon();
        return $this -> fetch('goods/strip-goods',['List' => $goodsList]);

    }

    /*
    * @ couponSquare() 券广场
    * * */
    public function couponSquare(){
        $goods = new GoodModel();
        $goodsList = $goods -> getCouponSquare();
        return $this -> fetch('other/square',['List' => $goodsList]);

    }

    /*
  * @ navCouponList() 导航优惠券列表
  * * */
    public function navCouponList(){
        if(isset($_GET['nav']) && !empty($_GET['nav'])){
            $data = $this -> request -> get();
            //var_dump($data);die;
            $goods = new GoodModel();
            if(isset($data['cond'])){
                $goodsList = $goods -> getCouponNav($data['nav'],$data['cond'],$data['sort']);
            }else{
                $goodsList = $goods -> getCouponNav($data['nav']);
            }

            return $goodsList ?  $this -> fetch('goods/nav-goods',['List' => $goodsList]) : ReturnJson::ReturnA("未获取到相应的产品信息...");
        }else{
            $this -> redirect('/index/goods/couponSquare');
        }

    }





    /*
     * @ goodsList() 产品列表
     * $type 产品列表形式
     *      默认为条状产品列表
     *      $type为空则为条状产品列表
     *      1=》 条状产品列表
     *      2=》 块状产品列表
     * * */
    public function goodsList(){
        if(isset($_GET['type']) && !empty($_GET['type'])){
            $type = $this -> request -> get('type');
            if($type == 1){

                return $this -> fetch('goods/strip-goods');
            }elseif($type == 2){

                return $this -> fetch('goods/block-goods');
            }else{

                return $this -> fetch('goods/strip-goods');
            }
        }else{
            return $this -> fetch('goods/strip-goods');
        }

    }
}