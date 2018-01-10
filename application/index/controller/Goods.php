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
    * */
    public function goodsDetails(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $goods = new GoodModel();
            $getOne = $goods -> getGoodsDetailsById($id);
            $Recommend = $goods ->getRecommendGoodsById($id);
            return $getOne ?  view('goods/details',['getOne' => $getOne,'Recommend' => $Recommend]) : ReturnJson::ReturnA("未获取到相应的产品信息...");
        }else{
            return ReturnJson::ReturnA("无效的修改操作...");
        }
    }
}