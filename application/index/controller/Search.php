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
use app\api\controller\request\WirelessShareTpwdQueryRequest;
use app\api\controller\request\TbkItemInfoGetRequest;
use app\api\controller\request\WirelessShareTpwdCreateRequest;
use app\api\controller\request\TbkItemConvertRequest;
use app\api\controller\dmain\GenPwdIsvParamDto;
use app\common\controller\ReturnJson;


use app\common\controller\ReturnGoodsList;

class Search extends CommController
{
    /*
     * @searchApi       普通产品搜索优惠券
     * */
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
                    $goodsList = $this -> getTaoGoodApiData($data['keyword'],$data['startNum']);
                    $returnRes = ReturnGoodsList::searchGoodsListByResult($goodsList);
                    echo json_encode($returnRes,JSON_UNESCAPED_UNICODE);
                }else{
                    $goodsList = $this -> getTaoGoodApiData($data['keyword']);
                    return $this -> fetch('search/search',['List'=>$goodsList]);
                }


            }else{
                return $this -> jsonFail('请输入你所要查找商品的名称...');
            }
        }
    }

    /*
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

    /*protected function showSearch($data){
        return $this -> fetch('search/search',['List'=>$data]);
    }*/

    /*
     * @commandSearchApi        淘口令搜索
     * */
    public function commandSearchApi(){
        if($this->request->isPost()){
            $data = $this->request->post();
            //var_dump($data);die;
            if(isset($data['command']) && !empty($data['command'])){
                $analysis = $this -> getAnalysisCommandApi($data['command']);
                //var_dump($analysis);die;
                if($analysis -> suc){
                    $gid = $this ->explainUrlGetId($analysis->url);
                    //var_dump($gid);die;
                    $result = $this->getDetailsByIdApi($gid);

                    //var_dump($goodsList);
                    if($result){
                        $goodsList = $goodsList = $this -> getTaoGoodApiData($result->title,1,20);
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
            $data = $this->request->get();
            if(isset($data['id']) && !empty($data['id'])){

                $result = $this->getDetailsByIdApi($data['id']);
                if($result){
                    $command = $this -> setCommandByAnalysisCommand($result->item_url,$result->pict_url,$result->title);
                    return $this -> fetch('search/analysis_details',['getOne' => $result,'Command'=>$command->model]);
                }else{
                    return ReturnJson::ReturnA("未获取到相应的产品信息...");
                }


            }else{
                return $this -> jsonFail('产品信息有误，请重试...');
            }
        }

    }

    /*
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
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkItemInfoGetRequest;
        $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume");
        $req->setPlatform(ThinkConfig::get('W_Platform'));
        $req->setNumIids($id);
        $resp = $c->execute($req);
        //var_dump($resp);die;
        if($resp->results == null){
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

    /**/
    protected function setUrlExchange(){
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkItemConvertRequest;
        $req->setFields("num_iid,click_url");
        $req->setNumIids("123,456");
        $req->setAdzoneId("123");
        $req->setPlatform("123");
        $req->setUnid("demo");
        $req->setDx("1");
        $resp = $c->execute($req);
    }



    protected function getTaoOrdinApiData($key,$pageNo=1,$pageSize=20){

    }
}