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
        //var_dump( get_headers('http://www.dwntme.com/h.Zb0Z5MD'),true);
        //curl的百度百科
        $url = 'http://www.dwntme.com/h.Zb0Z5MD';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
// 不需要页面内容
        curl_setopt($ch, CURLOPT_NOBODY, 1);
// 不直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 返回最后的Location
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_exec($ch);
        $info = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        var_dump('真实url为：'.$info) ;

        return $this -> fetch('index/test');

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