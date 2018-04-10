<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/15
 * Time: 10:29
 */
namespace app\admin\controller;

use app\common\controller\CommController;
use app\common\controller\ReturnJson;
use app\admin\model\Goods as GoodsModel;

use think\Loader;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Cell;

class Goods extends CommController
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

    /*
    * @uploadGoodExcel       上传优惠券产品数据
    * */
    public function uploadGoodExcel(){
        //引入文件（把扩展文件放入vendor目录下，路径自行修改）
        Loader::import('PHPExcel.PHPExcel');
        Loader::import('PHPExcel.PHPExcel.PHPExcel_IOFactory');
        Loader::import('PHPExcel.PHPExcel.PHPExcel_Cell');

        //获取表单上传文件
        $file = request()->file('excel');
        $info = $file->validate(['ext' => 'xlsx,xls'])->move(ROOT_PATH . 'public' . DS . 'upload' . DS . 'TaoBao');

        //数据为空返回错误
        if(empty($info)){

            return $this->jsonFail('导入数据失败...');
        }

        //获取文件名
        $exclePath = $info->getSaveName();
        //上传文件的地址
        $filename = ROOT_PATH . 'public' . DS . 'upload' . DS . 'TaoBao'. DS . $exclePath;
        var_dump($filename);
        unlink($filename);
        die;

        //判断截取文件
        $extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );

        //区分上传文件格式
        if($extension == 'xlsx') {
            $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');
        }else if($extension == 'xls'){
            $objReader =\PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($filename, $encode = 'utf-8');
        }else{
            return $this->jsonFail('上传EXCEL文件类型有误...');
        }

        //转换为数组格式
        $excel_array = $objPHPExcel->getsheet(0)->toArray();
        //删除第一个数组(标题);
        array_shift($excel_array);
        //数据逐条写入数据库
        $data = [];
        foreach($excel_array as $k=>$v) {

            $data['build_time'] = $v[0];
            $data['click_time'] = $v[1];
            $data['name'] = $v[2];
            $data['goods_id'] = $v[3];
            $data['wangwang'] = $v[4];
            $data['seller_name'] = $v[5];
            $data['num'] = $v[6];
            $data['price'] = $v[7];
            $data['order_state'] = $v[8];
            $data['type'] = $v[9];
            $data['ratio_collect'] = $v[10];
            $data['ratio_divided'] = $v[11];
            $data['real_price'] = $v[12];
            $data['est_effect'] = $v[13];
            $data['settle_price'] = $v[14];
            $data['est_collect'] = $v[15];
            $data['settle_time'] = $v[16];
            $data['ratio_commission'] = $v[17];
            $data['commission'] = $v[18];
            $data['ratio_subsidy'] = $v[19];
            $data['subsidy'] = $v[20];
            $data['type_subsidy'] = $v[21];
            $data['trade_plat'] = $v[22];
            $data['third'] = $v[23];
            $data['order_id'] = $v[24];
            $data['class'] = $v[25];
            $data['source_id'] = $v[26];
            $data['source_name'] = $v[27];
            $data['zone_id'] = $v[28];
            $data['zone_name'] = $v[29];

            $taoOrder = new TaoOrder();
            if( $taoOrder -> getTaoOrderInfoById($data['order_id'])){
                //该淘订单已经存在,则更新订单信息
                $taoOrder -> updateTaoOrder($data['order_id'],$data);
            }else{
                //该淘订单尚未存在，则插入订单信息
                $taoOrder -> insertTaoOrder($data);
            }
        }

        /*
        //批量插入
        foreach($excel_array as $k=>$v) {
            if(empty(Db::name('excel_shop')->where(['goods_id'=>$v[0]])->value('name'))){
                $data['build_time'] = $v[0];
                $data['click_time'] = $v[1];
                $data['name'] = $v[2];
            }
        }
        Db::name('excel_shop')->insertAll($city); //批量插入数据
        */
        unlink($filename);
        return $this->jsonSuccess('导入数据成功...');


    }
}