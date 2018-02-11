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

    //奖金发放状态显示
    protected function getBonusStateAttr($value){
        if($value == 0){
            return '未发放';
        }else{
            return '已发放';
        }
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
            ->where('o.is_del',0)
            ->order('o.id DESC')
            ->group('o.id')
            -> paginate(10,false,['path' => '/admin/main#/order/orderList' ]);
    }

    /*
     * @ getCountOrder   统计订单数量
     * */
    public function getCountOrder(){
        return $this -> where('is_del',0)
                    ->count('id');
    }

    /*
     * getOrderInfoById         通过ID获取指定用户信息
     * $id                      用户ID
     * */
    public function getOrderInfoById($id){
        return $this -> alias('o')
            -> field('u.user_name,p.user_name as p_user_name,o.id,o.order_id,o.goods_id,o.name,o.type,o.price,o.real_price,o.num,o.commission,o.bonus,o.build_time,o.invitation_bonus,o.state,o.order_state,o.bonus_state,o.create_time')
            ->join('think_user u','o.user_id = u.id','left')
            ->join('think_user p','o.pid = p.id','left')
            ->where('o.id',$id)
            ->where('o.is_del',0)
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
        return $this -> allowField(true) -> where('id',$id) -> update($data);
    }

    /*
     * @delOrder        用户订单删除
     * $id              用户订单ID
     * */
    public function delOrder($id){
        return $this -> where('id',$id) -> update(['is_del' => time()]);
    }

    /*
     * @getSearchOrderByKeyword         关键词搜索订单
     * */
    public function getSearchOrderByKeyword($key){
        return $this -> alias('o')
            -> field('u.user_name,p.user_name as p_user_name,o.id,o.order_id,o.bonus,o.invitation_bonus,o.order_state,o.commission,o.state,o.create_time')
            ->join('think_user u','o.user_id = u.id','left')
            ->join('think_user p','o.pid = p.id','left')
            ->where('o.order_id|u.user_name','like','%'.$key.'%')
            ->where('o.is_del',0)
            ->order('o.id DESC')
            ->group('o.id')
            -> paginate(10,false,['path' => '/admin/main#/order/orderList' ]);
    }

    /*
     * @ getCountOrder   统计搜索订单数量
     * */
    public function getCountSearchOrder($key){
        return $this -> alias('o')
                    -> field('o.id')
                    ->join('think_user u','o.user_id = u.id','left')
                    ->where('o.order_id|u.user_name','like','%'.$key.'%')
                    ->where('o.is_del',0)
                    ->group('o.id')
                    ->count('o.id');
    }

    /*
   * @getPendingList   待处理订单列表显示
   * */
    public function getPendingList(){
        return $this -> alias('o')
            -> field('u.user_name,p.user_name as p_user_name,o.id,o.order_id,o.bonus,o.invitation_bonus,o.order_state,o.commission,o.state,o.create_time')
            ->join('think_user u','o.user_id = u.id','left')
            ->join('think_user p','o.pid = p.id','left')
            ->where('o.is_del',0)
            ->where('o.state',0)
            ->where('o.order_state','订单结算')
            ->order('o.id DESC')
            ->group('o.id')
            -> paginate(10,false,['path' => '/admin/main#/order/pendingList' ]);
    }

    /*
     * @ getCountPending   统计待处理订单数量
     * */
    public function getCountPending(){
        return $this ->where('is_del',0)
            ->where('state',0)
            ->where('order_state','订单结算')
            ->count('id');
    }

    /*
     * @ getOrderBonusStateById   查询指定的奖金未支付订单
     * */
    public function getOrderBonusStateById($id){
        return $this -> field('id,user_id,order_id,bonus,pid,invitation_bonus') -> where('id',$id) -> where('bonus_state',0) -> find();
    }

    /*
  * @getObsoleteList   超时待处理订单列表显示
  * */
    public function getObsoleteList(){
        return $this -> alias('o')
            -> field('u.user_name,p.user_name as p_user_name,o.id,o.order_id,o.bonus,o.invitation_bonus,o.order_state,o.commission,o.state,o.create_time')
            ->join('think_user u','o.user_id = u.id','left')
            ->join('think_user p','o.pid = p.id','left')
            ->where('o.is_del',0)
            ->where('o.state',0)
            ->where('o.order_state',null)
            ->where('o.create_time','<',time()-7*60*60*24)
            ->order('o.id DESC')
            ->group('o.id')
            -> paginate(10,false,['path' => '/admin/main#/order/obsoleteList' ]);
    }

    /*
     * @ getCountObsolete   统计超时订单数量
     * */
    public function getCountObsolete(){
        return $this ->where('is_del',0)
            ->where('state',0)
            ->where('order_state',null)
            ->where('create_time','<',time()-7*60*60*24)
            ->count('id');
    }


}