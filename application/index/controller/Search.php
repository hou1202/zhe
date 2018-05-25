<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/11
 * Time: 17:05
 */
namespace app\index\controller;


use app\api\controller\request\TbkDgItemCouponGetRequest;
use app\api\controller\TopClient;
use app\common\controller\CommController;
use think\config as ThinkConfig;
use app\api\controller\request\WirelessShareTpwdQueryRequest;
use app\api\controller\request\TbkItemInfoGetRequest;
use app\api\controller\request\WirelessShareTpwdCreateRequest;
use app\api\controller\dmain\GenPwdIsvParamDto;
use app\common\controller\ReturnJson;
use app\common\controller\ApiDataHandle;
use app\common\controller\SetBaseData;


use app\common\controller\ReturnGoodsList;

class Search extends CommController
{
    /*
     * @searchApi       普通产品搜索优惠券
     * */
    public function searchApi(){
        if($this->request->isGet()){
            $data = $this->request->get();
            if(isset($data['keyword']) && !empty($data['keyword'])){
                if(isset($data['startNum'])){
                    //获取优惠券产品
                    $goodsList = ApiDataHandle::getCouponGoodsByKey($data['keyword'],$data['startNum']);
                    //分页加载数据组装
                    $returnRes = ReturnGoodsList::searchGoodsListByResult($goodsList);
                    echo json_encode($returnRes,JSON_UNESCAPED_UNICODE);
                }else{
                    $goodsList = ApiDataHandle::getCouponGoodsByKey($data['keyword']);
                    return $this -> fetch('search/search',['List'=>$goodsList]);
                }


            }else{
                return $this -> jsonFail('请输入你所要查找商品的名称...');
            }
        }
    }

    /*x
     * @getTaoGoodApiData 访问API，辅助于普通产品搜索searchApi
     * $key               搜索关键字
     * $pageNo            分布搜索起始页
     * $pageSize          分布搜索数量
     * */
    protected function getTaoGoodApiData($key,$pageNo=1,$pageSize=20){
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


    /*
     * @commandSearchApi        淘口令搜索
     * */
    public function commandSearchApi(){
        if($this->request->isPost()){
            $data = $this->request->post();
            //var_dump($data);die;
            if(isset($data['command']) && !empty($data['command'])){
                //解析淘口令
                $analysis = ApiDataHandle::analysisWirelessCommand($data['command']);
                if($analysis -> suc){
                    //分析URL
                    $gid = $this ->explainUrlGetId($analysis->url);
                    //获取商品详情（简单版）
                    $result = ApiDataHandle::getSimpleGoodsInfoById($gid);
                    if($result){
                        $goodsList = ApiDataHandle::getCouponGoodsByKey($result->title,1,20);
                        if($goodsList && count($goodsList)<20){
                            $num = 19-count($goodsList);
                            $cat=$goodsList['0']->category;
                            $catList = ApiDataHandle::getCouponGoodsByCat($cat,1,$num);
                            $goodsList =array_merge($goodsList,$catList);
                        }
                        return $this -> fetch('search/analysis_search',['State'=>true,'getOne'=>$result,'List'=>$goodsList]);
                    }else{
                        return $this -> fetch('search/analysis_search',['State'=>false]);
                    }
                }else{
                    return $this -> fetch('search/analysis_search',['State'=>false]);
                }

            }else{
                return $this -> jsonFail('请输入你所要查找商品的名称...');
            }
        }
    }

    /*
     * 淘口令产品，产品详情
     * */
    public function commandSearchDetail(){
        if($this->request->isGet()){

            $data = $this -> request -> get();
            //var_dump($data);die;
            //加入产品奖金数据
            $data['bonus'] = SetBaseData::setGoodsBonus($data['price'],false,$data['ratio']);
            //生成淘口令
            $command = ApiDataHandle::getTaoCommand($data['coupon_url'],$data['banner'],$data['name']);
            //获取推荐产品，随机获取1-20页内的10个产品
            $recommend = ApiDataHandle::getCouponGoodsByCat($data['category'],rand(1,20),10);
            return $this -> fetch('convert/selection_details',['getOne'=>$data,'Command'=>$command,'Recom'=>$recommend]);
        }
    }

    /*x
     * @getAnalysisCommandApi       解析淘口令，辅助于淘口令搜索commandSearchApi
     * $data                        淘口令
     * @return                      返回淘口令解析结果
     * $resp->suc                   用于判断解析成功或失败：true/false
     * */
    protected function getAnalysisCommandApi($data){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new WirelessShareTpwdQueryRequest;
        $req->setPasswordContent($data);
        $resp = $c->execute($req);
        //var_dump($resp);die;
        return $resp;

    }

    /*
     * @explainUrlGetId         解析URL，获取ID
     * $url                     所需解析的URL
     * @returm                  产品id
     * */
    protected function explainUrlGetId($url){
        preg_match_all("/(\w+=\w+)(#\w+)?/i",$url,$match);
        //var_dump($match);die;
        $result = explode("=",$match[0][4]);
        return$result[1];
    }

    /*
     * @getDetailsByIdApi       通过产品ID获取产品优惠详情
     * $id                      产品ID
     * @return                  产品详情
     * */
    protected function getDetailsByIdApi($id){
        //var_dump($id);die;
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkItemInfoGetRequest;
        $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume");
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setNumIids($id);
        $resp = $c->execute($req);
        //var_dump($resp);die;
        if($resp->results != null){
            //var_dump($resp->results->n_tbk_item[0]);die;
            return $resp->results->n_tbk_item[0];
        }else{
            return false;
        }
        //return $resp->results->n_tbk_item[0];
    }

    /*
     * 淘口令产品解析后生成淘口令
     * */
    protected function setCommandByAnalysisCommand($url,$logo,$text){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new WirelessShareTpwdCreateRequest;
        $tpwd_param = new GenPwdIsvParamDto;
        $tpwd_param->logo=$logo;
        $tpwd_param->url=$url;
        $tpwd_param->text=$text;
        //$tpwd_param->user_id="24234234234";
        $req->setTpwdParam(json_encode($tpwd_param));
        $resp = $c->execute($req);
        return $resp;
    }




}