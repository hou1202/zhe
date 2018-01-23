<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:02
 */

namespace app\index\controller;


use app\common\controller\CommController;
use app\index\model\Goods as GoodModel;
use app\common\controller\ReturnJson;
use app\common\controller\ReturnGoodsList;
use think\Page;

class Test extends CommController {



    /*
     * @ todayCouponGoodsList() 今日优惠券列表
     * * */
    public function index(){
        $goods = new GoodModel();
        $goodsList = $goods -> getGoodsListByTime();
        return $this -> fetch('index/test',['List' => $goodsList]);

    }
    public function test(){
        $goods = new GoodModel();
        $goodsList = $goods -> getTestGoods();
        $count = count($goodsList);
        //var_dump($count);
        return $this -> fetch('other/square',['List' => $goodsList,'Total'=>$count]);
        //return $this -> fetch('index/test');
    }




    public function articleList()
    {
        if($this->request->post()){
            $goods = new GoodModel();
            $start = $this->request->post('startNum');
            $List = $goods->limit($start, 5)->order('id asc')->select();
            $data = ReturnGoodsList::areaGoodsListByResult($List);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }else{
            $goods = new GoodModel();
            $count = $goods->count();
            $goodsList = $goods->limit(5)->order('id asc')->select();
            return $this -> fetch('index/test',['List' => $goodsList,'Total'=>$count]);
        }

    }

// ajax异步加载文章
    public function articleAjax() {
        /*$goods = new GoodModel();
        $start = $this->request->post('p');
        $List = $goods->limit($start, 5)->order('id asc')->select();
        //$this->ajaxReturn(array( 'result'=>$List,'status'=>1, 'msg'=>'获取成功！'));
        $data = array( 'result'=>$List,'status'=>1, 'msg'=>'获取成功！');
        return json_encode($data);*/
        $goods = new GoodModel();
        $start = $this->request->post('p');
        $List = $goods->limit($start, 5)->order('id asc')->select();
        //$this->ajaxReturn(array( 'result'=>$List,'status'=>1, 'msg'=>'获取成功！'));
        $data = array( 'result'=>$List);
        return json_encode($data);
    }


}