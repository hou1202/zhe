<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/2/6
 * Time: 11:05
 */

namespace app\admin\model;


use think\Model;

class TaoOrder extends Model
{

    public static $tableName = 'think_tao_order';

    //取值时间显示
    protected function getCreateTimeAttr($value){
        return $value;
    }


    /*
    * @getTaoOrderList   淘宝客订单列表显示
    * */
    public function getTaoOrderList(){
        return $this-> field('order_id,name,num,real_price,ratio_commission,commission,subsidy,est_effect,order_state,build_time,settle_time')
            ->order('settle_time DESC')
            -> paginate(10,false,['path' => '/admin/main#/order/taoOrderList' ]);
    }

    /*
     * @ getCountTaoOrder   统计淘宝客订单数量
     * */
    public function getCountTaoOrder(){
        return $this -> count('order_id');
    }

    /*
     * @ getTaoOrderInfoById   获取指定淘宝客订单信息
     * @id                      淘宝客订单ID
     * */
    public function getTaoOrderInfoById($id){
        return $this -> where('order_id',$id) -> find();
    }

    /*
    * @ getTaoOrderForCensorById   获取指定淘宝客订单信息
    * @id                      淘宝客订单ID
    * */
    public function getTaoOrderForCensorById($id){
        return $this -> field('order_id,goods_id,name,type,price,real_price,num,commission,build_time,order_state')
                    -> where('order_id',$id)
                    -> find();
    }

    /*
     * @getSearchTaoOrderByKeyword         关键词搜索淘宝客订单
     * */
    public function getSearchTaoOrderByKeyword($key){
        return $this -> field('order_id,name,num,real_price,ratio_commission,commission,subsidy,est_effect,order_state,build_time,settle_time')
            ->where('order_id|name','like','%'.$key.'%')
            -> paginate(10,false,['path' => '/admin/main#/order/taoOrderList' ]);
    }

    /*
     * @ getCountOrder   统计搜索淘宝客订单数量
     * */
    public function getCountSearchTaoOrder($key){
        return $this -> where('order_id|name','like','%'.$key.'%') -> count('order_id');
    }

    //insertTaoOrder    导入数据，插入新订单信息
    public function insertTaoOrder($data){
        return $this -> insert($data);
    }

    //updateTaoOrder    导入数据，更新已有订单信息
    public function updateTaoOrder($id,$data){
        return $this -> where('order_id',$id) -> update($data);
    }



















}