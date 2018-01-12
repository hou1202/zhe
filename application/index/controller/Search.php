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

class Search extends CommController
{
    public function searchApi(){
        if($this->request->isPost()){
            $key = $this->request->post();
            if(isset($key['keyword']) && !empty($key['keyword'])){

                /*$cli = new TopClient();
                $cli -> appkey = ThinkConfig::get('T_AppKey');
                $cli -> secretKey = ThinkConfig::get('T_AppSecret');
                $req = new TbkItemGetRequest();
                $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
                $req->setQ($key['keyword']);
                $req->setSort("tk_rate_des");
                $resp = $cli->execute($req);*/
                $c = new TopClient;
                $c->appkey = ThinkConfig::get('T_AppKey');
                $c->secretKey = ThinkConfig::get('T_AppSecret');
                $req = new TbkDgItemCouponGetRequest;
                $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
                $req->setQ($key['keyword']);
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
                }
                return $this -> fetch('search/search',['List'=>$List]);

            }else{
                return $this -> jsonFail('请输入你所要查找商品的名称...');
            }
        }
    }

    protected function showSearch($data){
        return $this -> fetch('search/search',['List'=>$data]);
    }
}