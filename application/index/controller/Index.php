<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 14:24
 */

namespace app\index\controller;
use app\admin\controller\ReturnJson;
use app\index\model\Goods;
use app\common\controller\CommController;
use app\common\controller\ReturnGoodsList;
use think\Db;

class Index extends CommController
{
    public function index(){
        $goods = new Goods();
       if($this->request->post()){
           $start = $this->request->post('startNum');
           $List = $goods->getIndexListCoupon($start);
           $data = ReturnGoodsList::indexGoodsListByResult($List);
           echo json_encode($data,JSON_UNESCAPED_UNICODE);
       }else{
           $nav = Db::table('think_nav') -> field('title,img,key') -> where('state',1) -> order('sort DESC') ->limit(10) -> select();
           $banner = Db::table('think_banner') -> field('title,img,shape,link') -> where('state',1) -> where('type',0) -> order('sort DESC') -> select();
           $Perfect = $goods ->getIndexPerfectCoupon();
           $Hot = $goods ->getIndexHotCoupon();
           $List = $goods ->getIndexListCoupon();
           return $this -> fetch('index/index',['Perfect'=>$Perfect,'Hot'=>$Hot,'List'=>$List,'Nav'=>$nav,'Banner'=>$banner]);
       }

    }

    public function web(){
        return $this->fetch('index/web');
    }

    //生成用户访问记录信息
    public function visitRecordIp(){
        if($this->request->isPost()){

            $data=$this->request->post();
            $request = $this->request->instance();
            $data['ip'] = $request -> ip();
            $data['address'] = $this -> peggingIp($request -> ip());
            $data['create_time'] = time();
            $visit = Db::name('visit')->insert($data);
            if($visit){
                return $this->jsonSuccess(true);
            }else{
                return $this->jsonFail(false);
            }

        }

    }

    protected function peggingIp($ip){
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip='.$ip);
        if(empty($res)){ return $address='未知'; }
        $jsonMatches = array();
        preg_match('#{.+?}#', $res, $jsonMatches);
        if(!isset($jsonMatches[0])){ return $address='未知'; }
        $json = json_decode($jsonMatches[0], true);
        if(isset($json['ret']) && $json['ret'] == 1){
            $json['ip'] = $ip;
            unset($json['ret']);
        }else{
            return false;
        }
        $address = $json['country'].$json['province'].$json['city']
            .$json['district'].$json['isp'].$json['type'].$json['desc'];
        return $address;
    }






}