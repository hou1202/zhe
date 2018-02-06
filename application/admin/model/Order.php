<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/2/5
 * Time: 15:54
 */

namespace app\admin\model;


use think\Model;

class Order extends Model
{
    public static $tableName = 'think_order';

    protected $autoWriteTimestamp = true;


    //取值状态显示
    protected function getStateAttr($value){
        $state = [0 => '待审核',1 => '通过审核',2 => '驳回审核'];
        return $state[$value];
    }

    //取值时间显示
    protected function getCreateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    protected function getUpdateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    /*
    * @getOrderList   订单列表显示
    * */
    public function getOrderList(){
        return $this -> alias('o')
            -> field('u.user_name,p.user_name as p_user_name,o.id,o.order_id,o.bonus,o.invitation_bonus,o.order_state,o.commission,o.state,o.create_time')
            ->join('think_user u','o.user_id = u.id','left')
            ->join('think_user p','o.pid = p.id','left')
            ->where('o.is_del',null)
            ->whereOr('o.is_del','')
            /*-> order('l.id DESC')*/
            ->group('o.id')
            -> paginate(10,false,['path' => '/admin/main#/order/orderList' ]);
    }

    /*
     * @ getCountOrder   统计订单数量
     * */
    public function getCountOrder(){
        return $this -> where('is_del',null)
                    ->whereOr('is_del','')
                    ->count('id');
    }

    /*
     * getOrderInfoById         通过ID获取指定用户信息
     * $id                      用户ID
     * */
    public function getOrderInfoById($id){
        return $this -> alias('o')
            -> field('u.user_name,p.user_name as p_user_name,o.id,o.order_id,o.goods_id,o.name,o.type,o.price,o.real_price,o.num,o.commission,o.bonus,o.build_time,o.invitation_bonus,o.state,o.order_state,o.create_time')
            ->join('think_user u','o.user_id = u.id','left')
            ->join('think_user p','o.pid = p.id','left')
            ->where('o.id',$id)
            ->group('o.id')
            ->find();
    }

    /*
     * @updateOrderForCensorByOrderId       通过订单ID，审核更新订单信息
     * $id              淘宝客订单ID
     * $data            更新数据信息
     * */
    public function updateOrderForCensorByOrderId($id,$data){
        return $this -> allowField(true) -> where('order_id',$id) -> update($data);
    }


    /*
     * @updateOrderStateById       通过ID，审核更新订单状态信息
     * $id              订单ID
     * $data            更新数据信息
     * */
    public function updateOrderStateById($id,$data){
        return $this -> allowField(true) -> where('id',$id) -> update(['state'=>$data]);
    }

    /*
     * @delOrder        用户订单删除
     * $id              用户订单ID
     * */
    public function delOrder($id){
        return $this -> where('id',$id) -> update(['is_del' => time()]);
    }


}