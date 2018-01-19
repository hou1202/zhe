<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/15
 * Time: 10:29
 */
namespace app\admin\controller;

use think\Controller;
use app\common\controller\ReturnJson;
use app\admin\model\Goods as GoodsModel;

class Goods extends Controller
{

    /*
     * @ goodsList   商品列表
     * */
    public function goodsList(){
        $goods = new GoodsModel();
        $goodsCount = $goods -> getCountGoods();
        $goodsList = $goods -> getGoodsForList();
        return $goodsList -> items() ? view('goods_list',['List' => $goodsList , 'Count' => $goodsCount , 'AllDel' => 0]) : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
    * @ goodsCat    查看商品
     * $id  商品goods_id
    * */
    public function goodsCat(){
        //获取、展示修改信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $goods = new GoodsModel();
            $getOne = $goods -> getOneGoodsInfoByGoodsId($id);
            return $getOne ?  view('goods_cat',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的商品信息...","#/goods/goods_list");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }

    }

    /*
    * @ goodsDel    商品删除
    * */
    public function goodsDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $goods = new GoodsModel();
            return $goods -> delGoodsById($id) ? ReturnJson::ReturnJ("已成功删除此商品信息...") : ReturnJson::ReturnJ($goods -> getError(),"false");
        }
        return ReturnJson::ReturnJ("非法的数据提交信息!","false");
    }

    /*
    * @ goodsOverDue    查看过期商品列表
    * */
    public function goodsOverDue(){
        $goods = new GoodsModel();
        $goodsCount = $goods -> getCountOverDueGoods();
        $goodsList = $goods -> getOverDueGoodsByTime();
        return $goodsList -> items() ? view('goods_list',['List' => $goodsList , 'Count' => $goodsCount,'AllDel' => 1]) : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
    * @ goodsDelAllOverDue    删除全部过期商品
    * */
    public function goodsDelAllOverDue(){
        $goods = new GoodsModel();
        return $goods -> delAllOverGoods() ? ReturnJson::ReturnH("过期优惠券清除成功...","#/goods/goodsList") : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
   * @ goodsSearch    搜索商品
   * */
    public function goodsSearch()
    {

        if ($this->request->isGet()  && isset($_GET['keyword'])){
            $post_data = $this->request->get('keyword');
            if ($post_data=='') {
                return ReturnJson::ReturnA('关键字不能为空，请重新搜索！');
            } else {
                $goods = new GoodsModel();
                $List = $goods->getSearchGoodsByKeyword($post_data);
                if (empty($List->items())) {
                    return ReturnJson::ReturnA('未查询到相关数据，请重新搜索！');
                } else {
                    return view('goods_list' , ['List' => $List , 'Count' => $goods->getCountSearchGoodsByKeyword($post_data),'AllDel' => 0]);
                }
            }
        }else{
            return ReturnJson::ReturnA('非法数据操作!');
        }

    }
}