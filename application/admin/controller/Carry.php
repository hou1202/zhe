<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 15:57
 */

namespace app\admin\controller;


use app\admin\model\User;
use think\Controller;
use app\admin\model\Carry as CarryModel;
use app\common\controller\ReturnJson;
use app\common\model\Notice;


class Carry extends Controller
{
    // 提现列表
    public function carryList(){
        $carry = new CarryModel();
        $carryCount = $carry -> getCountCarry();
        $carryList = $carry -> getCarryList();
        return $carryList -> items() ? view('carry_list',['List' => $carryList , 'Count' => $carryCount]) : ReturnJson::ReturnH('未查询到相关数据信息...','#/carry/carryAdd');
    }

    public function carryUpdate(){
        //修改提交 提现
        if($this -> request -> isPost()){
            $data = $this -> request -> Post();
            if(isset($data['id']) && empty($data['id'])){
                return ReturnJson::ReturnJ('无效的数据操作...','false','/carry/carryList');
            }
            $carry = new CarryModel();
            $carryEdit = $carry -> updateCarry($data['id'],$data);
            if($carryEdit){
                //驳回申请状态处理
                $given = $carry -> getGivenCarry($data['id']);
                if($data['state'] == 2){
                    //返还用户提现申请，扣除金额
                    $user = new User();
                    $user -> setUserMoney($given['uid'],$given['money'],2);
                    //创建通知信息
                    Notice::NoticeCarryReject($given['uid'],$given['create_time'],$given['money'],$given['remark']);
                }
                if($data['state'] == 1){
                    Notice::NoticeCarrySuc($given['uid'],$given['create_time'],$given['money'],$given['alipay']);
                }
                return ReturnJson::ReturnJ('数据更新成功...','success','/carry/carryList');
            }else{
                return ReturnJson::ReturnJ('数据更新失败，请重新操作...','false');
            }

        }

        //获取、展示修改 提现
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $carry = new CarryModel();
            $getOne = $carry -> getOneCarry($id);
            return $getOne ?  view('carry_update',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的信息数据信息...","#/carry/carry_list");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }

    }







}