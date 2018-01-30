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
use app\api\controller\request\TbkDgItemCouponGetRequest;
use app\api\controller\request\TbkTpwdCreateRequest;
use app\api\controller\dmain\GenPwdIsvParamDto;
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

    /*选品图列表*/
    public function selection(){
        $result = Db::table("think_favorites") -> field('f_id,thumbnail')->where('state',1)->order('sort DESC')->select();
        //var_dump($result);die;
        return $this -> fetch('convert/selection',['List'=>$result]);
    }

    /*选品库产品列表*/
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

    /*接口访问选品库产品*/
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
        $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume,shop_title,zk_final_price_wap,tk_rate,status,type,click_url,category,coupon_click_url,coupon_info");
        $resp = $c->execute($req);

        if(empty($resp->results)){
            $List = NULL;
        }else{
            foreach($resp->results->uatm_tbk_item as $value){
                if(isset($value->coupon_info)){
                    $arrayPrice = array();
                    preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                    $value->coupon_info = $arrayPrice[0][1];
                }else{
                    $value->coupon_info = 0;
                    $value->coupon_click_url = $value->click_url;
                }
            }
            $List = $resp->results->uatm_tbk_item;
        }
        return $List;
    }

    /*生成淘口令*/
    protected function getTaoCommand($url,$logo,$title){
        /*访问接口taobao.tbk.tpwd.create*/
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkTpwdCreateRequest;
        $req->setText($title);
        $req->setUrl($url);
        $req->setLogo($logo);
        $resp = $c->execute($req);
        return $resp->data->model;
    }

    /*推荐产品*/
    protected function recomendGoods($cat){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkDgItemCouponGetRequest;
        $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setCat($cat);
        $req->setPageSize('10');
        $req->setPageNo(rand(1,20));
        $resp = $c->execute($req);
        if(empty($resp->results)){
            $List = NULL;
        }else{
            foreach($resp->results->tbk_coupon as $value){
                    $arrayPrice = array();
                    preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                    $value->coupon_info = $arrayPrice[0][1];
                    $value->coupon_price = $value->zk_final_price-$value->coupon_info;
                    if($value->coupon_price < 0){
                        $value->coupon_price=0;
                    };
            }
            $List = $resp->results->tbk_coupon;
        }
        return $List;
        //return $resp->results->tbk_coupon;
        //var_dump($resp->results->tbk_coupon);die;
    }

    /*选品库商品详情*/
    public function selectionDetails(){
        if($this->request->isGet()){
            $data = $this -> request -> get();
            $command = $this -> getTaoCommand($data['coupon_url'],$data['banner'],$data['name']);
            $recom = $this -> recomendGoods($data['category']);
            //var_dump($wirelessCommand);die;
            return $this -> fetch('convert/selection_details',['getOne'=>$data,'Command'=>$command,'Recom'=>$recom]);
        }
    }
}