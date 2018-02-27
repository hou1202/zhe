<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 15:35
 */

namespace app\index\controller;


use think\Controller;
use think\Db;
use app\common\controller\ReturnJson;
use app\common\controller\ReturnGoodsList;
use app\common\controller\ApiDataHandle;
use app\common\controller\SetBaseData;

class Convert extends Controller {

    public function convert(){
        /*$verifyCode = rand(100000, 999999);
        $text = '【折金券】您的验证码为：' . $verifyCode . '，请在10分钟内完成验证';
        $objectUrl = 'https://dx.ipyy.net/smsJson.aspx?action=send&userid=&account='
            . ThinkConfig::get('sms_account')
            . '&password='
            . ThinkConfig::get('sms_password')
            . '&mobile='
            . 18297905431
            . '&content=' . urlencode($text) . '&sendTime=&extno=';
        $results = json_decode($objectUrl);
        try {
            $results = file_get_contents($objectUrl);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
        var_dump(json_decode($results) -> returnstatus);die;*/
        return $this -> fetch('convert/convert');
    }

    /*选品图列表*/
    public function selection(){
        $result = Db::table("think_favorites") -> field('f_id,thumbnail')->where('state',1)->order('sort DESC')->select();
        return $this -> fetch('convert/selection',['List'=>$result]);
    }

    /*选品库产品列表*/
    public function selectionList(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $data = $this -> request -> get();
            if(isset($data['startNum'])){
                $goodsList = ApiDataHandle::getFavoritesGoodsById($data['id'],$data['startNum']);
                $returnRes = ReturnGoodsList::favoritesGoodsListByResult($goodsList);
                echo json_encode($returnRes,JSON_UNESCAPED_UNICODE);
            }else{
                $goodsList = ApiDataHandle::getFavoritesGoodsById($data['id']);
                //var_dump($goodsList);die;
                return $this -> fetch('convert/selection-goods',['List'=>$goodsList]);
            }
        }else{
            return ReturnJson::ReturnA("未获取到相应的产品信息...");
        }

    }


    /*选品库商品详情*/
    public function selectionDetails(){
        if($this->request->isGet()){
            $data = $this -> request -> get();
            //加入产品奖金数据
            $data['bonus'] = SetBaseData::setGoodsBonus($data['price'],false,$data['ratio']);
            //生成淘口令
            $command = ApiDataHandle::getTaoCommand($data['coupon_url'],$data['banner'],$data['name']);
            //获取推荐产品，随机获取1-20页内的10个产品
            $recommend = ApiDataHandle::getCouponGoodsByCat($data['category'],rand(1,20),10);
            return $this -> fetch('convert/selection_details',['getOne'=>$data,'Command'=>$command,'Recom'=>$recommend]);
        }
    }
}