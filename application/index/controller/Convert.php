<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 15:35
 */

namespace app\index\controller;


use think\Controller;
use think\Config as ThinkConfig;
use think\Log;
use think\Db;
use app\common\controller\ReturnJson;
use app\api\controller\TopClient;
use app\api\controller\request\TbkUatmFavoritesItemGetRequest;
use app\common\controller\ReturnGoodsList;

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

    public function selection(){
        $result = Db::table("think_favorites") -> field('f_id,thumbnail')->where('state',1)->order('sort DESC')->select();
        //var_dump($result);die;
        return $this -> fetch('convert/selection',['List'=>$result]);
    }

    public function selectionList(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $data = $this -> request -> get();
            if(isset($data['startNum'])){
                $goodsList = $this -> getFavoritesTaoApi($data['id'],$data['startNum']);
                $returnRes = ReturnGoodsList::favoritesGoodsListByResult($goodsList);
                echo json_encode($returnRes,JSON_UNESCAPED_UNICODE);
            }else{
                $goodsList = $this -> getFavoritesTaoApi($data['id']);
                //var_dump($goodsList);die;
                return $this -> fetch('convert/selection-goods',['List'=>$goodsList]);
            }

            //return $getOne ?  $this -> fetch('goods/details',['getOne' => $getOne,'Recommend' => $Recommend]) : ReturnJson::ReturnA("未获取到相应的产品信息...");
        }else{
            return ReturnJson::ReturnA("未获取到相应的产品信息...");
        }

    }

    protected function getFavoritesTaoApi($id,$pageNo=1,$pageSize=20){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkUatmFavoritesItemGetRequest;
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setPageSize($pageSize);
        $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
        $req->setFavoritesId($id);
        $req->setPageNo($pageNo);
        $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");
        $resp = $c->execute($req);
        //var_dump($resp->results->uatm_tbk_item);die;
        if(empty($resp->results)){
            $List = NULL;
        }else{
            if(isset($resp->results->uatm_tbk_item->coupon_info)){
                foreach($resp->results->uatm_tbk_item as $value){
                    $arrayPrice = array();
                    preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                    $value->coupon_info = $arrayPrice[0][1];
                }
            }else{
                foreach($resp->results->uatm_tbk_item as $value){
                    $value->click_url = $value->item_url;
                    $value->coupon_info = 0;
                }
            }
            $List = $resp->results->uatm_tbk_item;
        }
        return $List;
    }
}