<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/11
 * Time: 17:05
 */
namespace app\index\controller;


use app\api\controller\request\TbkItemGetRequest;
use app\api\controller\request\TbkDgItemCouponGetRequest;
use app\api\controller\TopClient;
use app\common\controller\CommController;
use think\config as ThinkConfig;

use app\common\controller\ReturnGoodsList;

class Search extends CommController
{
    public function searchApi(){
        if($this->request->isGet()){
            $data = $this->request->get();
            //var_dump($data);die;
            if(isset($data['keyword']) && !empty($data['keyword'])){
                //var_dump($data);die;

                /*$c = new TopClient;
                $c->appkey = ThinkConfig::get('T_AppKey');
                $c->secretKey = ThinkConfig::get('T_AppSecret');
                $req = new TbkDgItemCouponGetRequest;
                $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
                $req->setQ($data['keyword']);
                $req->setPlatform(ThinkConfig::get('W_Platform'));
                $req->setPageSize('100');
                $resp = $c->execute($req);

                if(empty($resp->results)){
                    $List = NULL;
                }else{
                    foreach($resp->results->tbk_coupon as $value){
                        $arrayPrice = array();
                        preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                        $value->coupon_info = $arrayPrice[0][1];
                    }
                    $List = $resp->results->tbk_coupon;
                }*/
                if(isset($data['startNum'])){
                    $goodsList = $this -> getTaoApiData($data['keyword'],$data['startNum']);
                    $returnRes = ReturnGoodsList::searchGoodsListByResult($goodsList);
                    echo json_encode($returnRes,JSON_UNESCAPED_UNICODE);
                }else{
                    $goodsList = $this -> getTaoApiData($data['keyword']);
                    return $this -> fetch('search/search',['List'=>$goodsList]);
                }


            }else{
                return $this -> jsonFail('请输入你所要查找商品的名称...');
            }
        }
    }

    protected function getTaoApiData($key,$pageNo=1,$pageSize=20){
        //API接口：taobao.tbk.dg.item.coupon.get (好券清单API【导购】)
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkDgItemCouponGetRequest;
        $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
        $req->setQ($key);
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setPageSize($pageSize);
        $req->setPageNo($pageNo);
        $resp = $c->execute($req);

        if(empty($resp->results)){
            $List = NULL;
        }else{
            foreach($resp->results->tbk_coupon as $value){
                $arrayPrice = array();
                preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                $value->coupon_info = $arrayPrice[0][1];
            }
            $List = $resp->results->tbk_coupon;
        }
        return $List;
    }

    protected function showSearch($data){
        return $this -> fetch('search/search',['List'=>$data]);
    }
}