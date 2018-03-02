<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:02
 */

namespace app\index\controller;


use app\common\controller\ApiDataHandle;
use think\Controller;
use app\index\model\Goods as GoodModel;
use app\common\controller\ReturnJson;
use app\common\controller\ReturnGoodsList;

class Goods extends Controller {

    /*
    * @goodsDetails()  产品详情
     * $id 通过GET方式传过来的产品id
    * */
    public function goodsDetails(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $goods = new GoodModel();
            //获取产品信息
            $getOne = $goods -> getGoodsDetailsById($id);
            if(!$getOne){
                return ReturnJson::ReturnA("下手慢了，该优惠券已经被瓜分完毕，赶紧看看其他的吧...");
            }
            //检测优惠券
            $couponEffect = $this -> checkGoodsEffect($getOne->goods_id,$getOne->coupon_id,$getOne->check_effect);
            if(!$couponEffect){
                return ReturnJson::ReturnA("下手慢了，该优惠券已经被瓜分完毕，赶紧看看其他的吧...");
            }
            //获取推荐产品
            $Recommend = $goods ->getRecommendGoodsById($id);
            return $getOne ?  $this -> fetch('goods/details',['getOne' => $getOne,'Recommend' => $Recommend]) : ReturnJson::ReturnA("未获取到相应的产品信息...");
        }elseif($this->request->isPost()){
            //生成淘口令
            $data = $this -> request -> post();
            $returnRes = ApiDataHandle::getTaoCommand($data['url'],$data['logo'],$data['text']);
            echo json_encode($returnRes,JSON_UNESCAPED_UNICODE);
        }else{
            return ReturnJson::ReturnA("未获取到相应的产品信息...");
        }

    }

    /* @checkGoodsEffect         检测优惠券信息是否有效，并生成记录或删除该优惠券
     * $goods_id                 产品ID
     * $coupon_id                优惠券ID
     * $coupon_check             检测记录
     * return                    返回检测结果，有效true,无效false，并删除该优惠券
     *  */
    protected function checkGoodsEffect($goods_id,$coupon_id,$coupon_check){
        if(empty($coupon_check) || date('Y-m-d',$coupon_check) != date('Y-m-d',time())){
            //为空 或 检测时间记录非今天
            //接口检测优惠券信息
            $couponEffect =  ApiDataHandle::getCouponInfoById($goods_id,$coupon_id);
            $goods = new GoodModel();
            if(isset($couponEffect ->data)){
                //有效优惠券，生成检测记录
                $goods -> checkGoodsById($goods_id);
                return true;
            }else{
                //无效优惠券，删除产品记录
                $goods -> checkDelGoodsById($goods_id);
                return false;
            }
        }else{
            //检测时间记录为今天
            return true;
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
            switch($data['type']){
                case 1 :
                    return $this->areaExeDataList($data,'getGoodsListByTime');
                    break;
                case 2 :
                    return $this->areaExeDataList($data,'getGoodsListByRatio');
                    break;
                case 3 :
                    return $this->areaExeDataList($data,'getGoodsListByTmall');
                    break;
                case 4 :
                    return $this->areaExeDataList($data,'getGoodsListByCouponMoney');
                    break;
                case 5 :
                    return $this->areaExeDataList($data,'getGoodsListByNine');
                    break;
                default :
                    $this -> redirect('/index/goods/couponSquare');
                    break;
            }

        }else{
            $this -> redirect('/index/goods/couponSquare');
        }
    }

    /*
     * @ areaExeDataList用于辅助@ getAreaType
     * $data        get方式传过来的数据
     * $goods       实例化的GoodsModel类
     * $goodsModel  所需访问的GoodsModel类中的方法
     * */
    protected function areaExeDataList($data,$goodsModel){
        $goods = new GoodModel();
        $goodsList = null;
        if(isset($data['cond']) && !empty($data['cond'])){
            if(isset($data['startNum'])){
                $goodsList = $goods -> $goodsModel($data['cond'],$data['sort'],$data['startNum']);
                $data = ReturnGoodsList::areaBlockGoodsListByResult($goodsList);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }else{
                $goodsList = $goods -> $goodsModel($data['cond'],$data['sort']);
                return $this -> fetch('goods/area-goods',['List' => $goodsList]);
            }
        }else{
            $goodsList = $goods -> $goodsModel();
            if($goodsList){
                return $this -> fetch('goods/area-goods',['List' => $goodsList]);
            }else{
                $this -> redirect('/index/goods/couponSquare');
            }
        }
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
            $goods = new GoodModel();
            if(isset($data['cond']) && !empty($data['cond'])){

                if(isset($data['startNum'])){
                    $goodsList = $goods -> getCouponNav($data['nav'],$data['cond'],$data['sort'],$data['startNum']);
                    $data = ReturnGoodsList::stripGoodsListByResult($goodsList);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                }else{
                    $goodsList = $goods -> getCouponNav($data['nav'],$data['cond'],$data['sort']);
                    return $this -> fetch('goods/nav-goods',['List' => $goodsList]);
                }

            }else{
                $goodsList = $goods -> getCouponNav($data['nav']);
                return $this -> fetch('goods/nav-goods',['List' => $goodsList]);
            }

        }else{
            $this -> redirect('/index/goods/couponSquare');
        }

    }



}