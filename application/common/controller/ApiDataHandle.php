<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/2/1
 * Time: 14:23
 */

namespace app\common\controller;
use think\Config as ThinkConfig;
use app\api\controller\TopClient;
use app\api\controller\request\TbkUatmFavoritesItemGetRequest;
use app\api\controller\request\TbkDgItemCouponGetRequest;
use app\api\controller\request\TbkTpwdCreateRequest;
use app\api\controller\request\TbkCouponGetRequest;
use app\api\controller\request\TbkItemInfoGetRequest;
use app\api\controller\request\WirelessShareTpwdQueryRequest;

class ApiDataHandle
{
    /*@getTaoCommand    生成淘口令
    *访问接口taobao.tbk.tpwd.create
     * $url     生成淘口令链接
     * $logo    生成链接LOGO
     * $title   生成链接标题
     * @return  淘口令字符串（￥5GF2R5HR￥）
    */
    static function getTaoCommand($url,$logo,$title){
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

    /*@getRecommendGoods    获取优惠券产品,通过产品类目ID，访问《好券清单API》接口，随机获取1-20页内的10个产品
    *访问接口taobao.tbk.dg.item.coupon.get
    *$cat       产品类目ID
    *$return List
    *   如果没有获取到数据，则返回List为null
    *   如果获取到数据，则返回数据对象
    */
    static function getCouponGoodsByCat($cat,$pageNo=1,$pageSize=20){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkDgItemCouponGetRequest;
        $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setCat($cat);
        $req->setPageSize($pageSize);
        $req->setPageNo($pageNo);
        $resp = $c->execute($req);
        if(empty($resp->results)){
            $List = NULL;
        }else{
            foreach($resp->results->tbk_coupon as $value){
                //对coupon_info优惠券金额字段进行处理
                $arrayPrice = array();
                preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                $value->coupon_info = $arrayPrice[0][1];
                //加入券后金额字段coupon_price，并对字段进行处理
                $value->coupon_price = $value->zk_final_price-$value->coupon_info;
                if($value->coupon_price < 0){
                    $value->coupon_price=0;
                };
            }
            $List = $resp->results->tbk_coupon;
        }
        return $List;
    }

    /*@getSearchGoodsByKey    通过关键词搜索优惠券产品，访问《好券清单API》接口，
    *访问接口taobao.tbk.dg.item.coupon.get
    *$key           关键词
    *$pageNo        分页加载起始页，默认从第一页开始
    *$pageSize      分页加载每页数据量，默认每页加载20条数据
    *$return List
    *   如果没有获取到数据，则返回List为null
    *   如果获取到数据，则返回数据对象
    */
    static function getCouponGoodsByKey($key,$pageNo=1,$pageSize=20){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkDgItemCouponGetRequest;
        $req->setAdzoneId(ThinkConfig::get('A_zoneId'));
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setQ($key);
        $req->setPageSize($pageSize);
        $req->setPageNo($pageNo);
        $resp = $c->execute($req);
        if(empty($resp->results)){
            $List = NULL;
        }else{
            foreach($resp->results->tbk_coupon as $value){
                //对coupon_info优惠券金额字段进行处理
                $arrayPrice = array();
                preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                $value->coupon_info = $arrayPrice[0][1];
                //加入券后金额字段coupon_price，并对字段进行处理
                $value->coupon_price = $value->zk_final_price-$value->coupon_info;
                if($value->coupon_price < 0){
                    $value->coupon_price=0;
                };
            }
            $List = $resp->results->tbk_coupon;
        }
        return $List;
    }

    /*@getFavoritesGoodsById        获取淘宝联盟选品库的宝贝信息,通过选品库ID
    *访问接口taobao.tbk.uatm.favorites.item.get
    *$id            选品库ID
    *$pageNo        分页加载起始页，默认从第一页开始
    *$pageSize      分页加载每页数据量，默认每页加载20条数据
    *$return List
    *   如果没有获取到数据，则返回List为null
    *   如果获取到数据，则返回数据对象
    */
    static function getFavoritesGoodsById($id,$pageNo=1,$pageSize=20){
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
                //判断产品状态，如果产品状态为0，则清除掉此条数据
                if($value->status == 0){
                    unset($value);
                }else{
                    //判断产品是否有优惠券信息或优惠券信息是否有效，并进行数据处理
                    if(isset($value->coupon_info) && $value->coupon_info != '无'){
                        //对coupon_info优惠券金额字段进行处理
                        $arrayPrice = array();
                        preg_match_all('/\d+/',$value->coupon_info,$arrayPrice);
                        $value->coupon_info = $arrayPrice[0][1];
                    }else{
                        $value->coupon_info = 0;
                        $value->coupon_click_url = $value->click_url;
                    }
                    //加入券后金额字段coupon_price，并对字段进行处理
                    $value->coupon_price = $value->zk_final_price-$value->coupon_info;
                    if($value->coupon_price < 0){
                        $value->coupon_price=0;
                    };
                }

            }
            $List = $resp->results->uatm_tbk_item;
        }
        return $List;
    }


    /*getSimpleGoodsInfoById        获取精简版商品详情，通过商品ID
     *访问接口taobao.tbk.item.info.get
     * $id          商品ID
     *return
     *   如果没有获取到数据，则返回false
     *   如果获取到数据，则返回数据对象
     * */
    static function getSimpleGoodsInfoById($id){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkItemInfoGetRequest;
        $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume,cat_leaf_name,cat_name");
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setNumIids($id);
        $resp = $c->execute($req);
        if($resp->results != null && isset($resp->results->n_tbk_item)){
            return $resp->results->n_tbk_item[0];
        }else{
            return false;
        }
    }

    /*@analysisWirelessCommand      解析淘口令内容
     * $data            淘口令
     *return            解析完成的内容
     * */
    static function analysisWirelessCommand($data){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new WirelessShareTpwdQueryRequest;
        $req->setPasswordContent($data);
        $resp = $c->execute($req);
        return $resp;
    }





    static function test(){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkCouponGetRequest;
        $req->setItemId("535981610055");
        $resp = $c->execute($req);
        return $resp;
    }


}