<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/2/5
 * Time: 15:53
 */

namespace app\admin\controller;


use app\admin\model\TaoOrder;
use app\admin\model\User;
use app\common\controller\CommController;
use app\admin\model\Order as OrderModel;
use app\common\controller\MoneyLog;
use app\common\controller\ReturnJson;
use app\common\controller\SetBaseData;
use think\Config as ThinkConfig;
use app\admin\validate\OrderValidate;
use app\common\controller\NoticeInfo;

class Order extends CommController
{
    /*
     * @orderList       用户提交订单列表
     * */
    public function orderList(){
        $order =  new OrderModel();
        $Count = $order ->getCountOrder();
        $List = $order->getOrderList();
        return $List -> items() ? view('order_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
     * @orderUpdate       用户提交订单修改
     * */
    public function orderUpdate(){
        //修改提交信息
        if($this -> request -> isPost()){
            $data = $this -> request -> Post();
            //var_dump($data);die;
            if(isset($data['id']) && empty($data['id'])){
                return ReturnJson::ReturnJ('无效的数据操作...','false','/order/orderList');
            }
            //数据验证
            $validate = new OrderValidate();
            if(!$validate -> check($data)){
                return ReturnJson::ReturnJ($validate -> getError(),'false');
            }
            //数据处理
            $order =  new OrderModel();
            $msg = '数据更新成功...';
            if($data['state'] == 1){
                $bonusState = $order -> getOrderBonusStateById($data['id']);
                if($bonusState){
                //奖金未发放，发放奖金
                    $user = new User();
                    //更新用户资金
                    $user -> setUserMoney($bonusState['user_id'],$bonusState['bonus']);
                    //生成资金日志
                    MoneyLog::BonusLog($bonusState['user_id'],0,$bonusState['bonus']);
                    //生成消息通知
                    NoticeInfo::BonusNoticeInfo($bonusState['user_id'],$bonusState['order_id'],$bonusState['bonus']);
                    if(!empty($bonusState['pid'])){
                        //判断是否有邀请人
                        $user -> setUserMoney($bonusState['pid'],$bonusState['invitation_bonus']);
                        MoneyLog::BonusLog($bonusState['pid'],0,$bonusState['invitation_bonus'],$bonusState['user_id']);
                        NoticeInfo::RebateNoticeInfo($bonusState['pid'],$bonusState['invitation_bonus']);
                    }
                    $msg = '用户数据更新成功，并成功发放用户资金...';
                    $data['bonus_state'] = time();
                }
            }elseif($data['state'] == 2){
                $bonusState = $order -> getOrderBonusStateById($data['id']);
                NoticeInfo::RejectNoticeInfo($bonusState['user_id'],$bonusState['order_id']);
            }
            return $order -> updateOrderStateById($data['id'],$data) ? ReturnJson::ReturnJ($msg,'success','/order/orderList') : ReturnJson::ReturnJ('数据更新失败，请重新操作...','false');



        }

        //获取、展示修改信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $order =  new OrderModel();
            $getOne = $order -> getOrderInfoById($id);
            return $getOne ?  view('order_update',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的新闻数据信息...","#/order/order_list");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }
    }

    /*
     * @taoOrderCensor      订单审查
     * */
    public function taoOrderCensor(){
        if($this -> request -> isPost()){
            $data = $this -> request -> post();
            //var_dump($data);die;
            if(!$data['orderId']){
                return $this ->jsonFail('非法的数据信息...');
            }
            //审核订单
            $tao =  new TaoOrder();
            $getOne = $tao -> getTaoOrderForCensorById($data['orderId']);
            if(!$getOne){
                return $this ->jsonFail('暂未找到相关的订单数据信息...');
            }
            //var_dump($getOne);die;
            $insert = array();
            $insert['goods_id'] = $getOne['goods_id'];
            $insert['name'] = $getOne['name'];
            $insert['type'] = $getOne['type'];
            $insert['price'] = $getOne['price'];
            $insert['real_price'] = $getOne['real_price'];
            $insert['num'] = $getOne['num'];
            $insert['commission'] = $getOne['commission'];
            $insert['build_time'] = $getOne['build_time'];
            $insert['order_state'] = $getOne['order_state'];
            $insert['bonus'] = SetBaseData::setGoodsBonus($getOne['commission'],true,null,ThinkConfig::get('bonus_ratio'));
            $insert['invitation_bonus'] = SetBaseData::setOrderRebate($insert['bonus']);
            //var_dump($insert['invitation_bonus']);die;

            //审核更新
            $order = new OrderModel();
            $censor = $order -> updateOrderForCensorByOrderId($data['orderId'],$insert);
            //var_dump($censor);die;
            if($censor){
                return $this ->jsonFail('订单审查更新成功,请查看订单信息...');
            }else{
                return $this ->jsonFail('订单审查更新失败，请重新操作...');
            }
        }
    }

    /*
     * @orderDel        用户订单删除
     * $id              用户订单ID
     * */
     public function orderDel($id){
         if(isset($_GET['id']) && !empty($_GET['id'])){
             $id = $this -> request -> get('id');
             $order =  new OrderModel();
             return $order -> delOrder($id) ? ReturnJson::ReturnJ("已成功删除此信息...") : ReturnJson::ReturnJ($order -> getError(),"false");
         }
         return ReturnJson::ReturnJ("非法的数据提交信息!","false");
     }

    /*
     * @taoOrderList        淘宝客订单列表
     * */
    public function taoOrderList(){
        $tao =  new TaoOrder();
        $Count = $tao -> getCountTaoOrder();
        $List = $tao -> getTaoOrderList();
        return $List -> items() ? view('order/tao_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
     * orderCat         查看淘宝客订单详情
     * */
    public function orderCat(){
        //获取展示信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $tao =  new TaoOrder();
            $getOne = $tao -> getTaoOrderInfoById($id);
            return $getOne ?  view('tao_cat',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的商品信息...","#/order/taoOrderList");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }
    }

    /*
      * @ orderSearch    搜索提交订单
      * */
    public function orderSearch(){
        if ($this->request->isGet()  && isset($_GET['keyword'])){
            $post_data = $this->request->get('keyword');
            if ($post_data=='') {
                return ReturnJson::ReturnA('关键字不能为空，请重新搜索！');
            } else {
                $order = new OrderModel();
                $List = $order->getSearchOrderByKeyword($post_data);
                $Count = $order -> getCountSearchOrder($post_data);
                return $List -> items() ? view('order_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnA('未查询到相关数据信息...');

            }
        }else{
            return ReturnJson::ReturnA('非法数据操作!');
        }

    }

    /*
      * @ taoSearch    搜索淘宝客订单
      * */
    public function taoSearch(){
        if ($this->request->isGet()  && isset($_GET['keyword'])){
            $post_data = $this->request->get('keyword');
            if ($post_data=='') {
                return ReturnJson::ReturnA('关键字不能为空，请重新搜索！');
            } else {
                $tao =  new TaoOrder();
                $List = $tao->getSearchTaoOrderByKeyword($post_data);
                $Count = $tao -> getCountSearchTaoOrder($post_data);
                return $List -> items() ? view('order/tao_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnA('未查询到相关数据信息...');

            }
        }else{
            return ReturnJson::ReturnA('非法数据操作!');
        }

    }

    /*
     * @pendingList       用户提交订单待处理列表
     * */
    public function pendingList(){
        $order =  new OrderModel();
        $Count = $order ->getCountPending();
        $List = $order->getPendingList();
        return $List -> items() ? view('pending_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnH('未查询到相关数据信息...','#/order/orderList');
    }

    /*
     * @obsoleteList       用户提交订单超时列表
     * */
    public function obsoleteList(){
        $order =  new OrderModel();
        $Count = $order ->getCountObsolete();
        $List = $order->getObsoleteList();
        return $List -> items() ? view('obsolete_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnH('未查询到相关数据信息...','#/order/orderList');
    }



}