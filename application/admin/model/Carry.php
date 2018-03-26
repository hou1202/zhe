<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 16:10
 */
namespace app\admin\model;

use think\Model;

class Carry  extends Model
{
    //自动写入创建及修改的时间戳
    protected $autoWriteTimestamp = true;

    protected $readonly = ['id','uid','phone','money','alipay'];

    // 时间字段取出后的默认时间格式
    protected $dateFormat = 'Y-m-d';

    protected function getStateAttr($value){
        $state = [0 => '待审核' , 1 => '审核通过' , 2 => '驳回申请'];
        return $state[$value];
    }

    protected function getGrantAttr($value){
        $grant = [0 => '未发放' , 1 => '已发放'];
        return $grant[$value];
    }



    // 提现列表
    public function getCarryList(){
        return $this -> alias('c')
                    ->field('u.user_name as uid,c.id,c.phone,c.money,c.alipay,c.state,c.grant,c.create_time,c.update_time')
                    ->join('think_user u','c.uid=u.id','left')
                    ->order('c.id DESC')
                    ->group('c.id')
                    -> paginate(10,false,['path' => '/admin/main#/carry/carryList' ]);
    }

    //按id统计 提现数量
    public function getCountCarry(){
        return $this ->field('id') ->count();
    }


    //获取指定 提现内容
    //联合user表查询
    public function getOneCarry($id){
        return $this -> alias('c')
                    ->field('u.user_name as uid,c.id,c.phone,c.money,c.alipay,c.state,c.grant,c.remark,c.create_time,c.update_time')
                    ->join('think_user u','c.uid=u.id','left')
                    ->where('c.id',$id)
                    ->group('c.id')
                    ->limit(1)
                    ->find();
    }

    //获取提现内容
    //字段：id、money
    public function getGivenCarry($id){
        return $this -> field('id,uid,money,remark,alipay,create_time') -> where('id',$id) -> limit(1) -> find();
    }


    //更新 提现内容
    public function updateCarry($id,$data){
        return $this -> allowField(true) -> where('id',$id) -> update($data);
    }


}