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
use think\Controller;
use app\common\controller\ReturnGoodsList;

class Index extends Controller
{
    public function index(){
        $goods = new Goods();
       if($this->request->post()){
           $start = $this->request->post('startNum');
           $List = $goods->getIndexListCoupon($start);
           $data = ReturnGoodsList::indexGoodsListByResult($List);
           echo json_encode($data,JSON_UNESCAPED_UNICODE);
       }else{
           $Perfect = $goods ->getIndexPerfectCoupon();
           $Hot = $goods ->getIndexHotCoupon();
           $List = $goods ->getIndexListCoupon();
           return $this -> fetch('index/index',['Perfect'=>$Perfect,'Hot'=>$Hot,'List'=>$List]);
       }

    }






}