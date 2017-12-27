<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31
 * Time: 13:57
 */

namespace app\admin\model;
use think\Model;

class Need extends Model
{
    //开启创建时间戳自动写入
    protected $autoWriteTimestamp = true;

    //设置取值时间格式
   /* protected function getCreateTimeAttr($value){
        return date('Y-m-d' , $value);
    }*/

   //设置取值状态样式
    protected function getStateAttr($value){
        $state = [0 => '未读'  , 1 => '已读'];
        return $state[$value];
    }

    //搜索
    public function searchNeed($keyword){
        return $this -> where('id','eq',$keyword)
                     -> whereOr('name','like','%'.$keyword.'%')
                     -> whereOr('phone','like','%'.$keyword.'%')
                     -> order('create_time DESC')
                     -> paginate(5,false,['path' => '/admin/main#/need/needSearch?keyword='.$keyword]);
    }

    //搜索统计
    public function searchNeedCount($keyword){
        return $this -> where('id','eq',$keyword)
                     -> whereOr('name','like','%'.$keyword.'%')
                     -> whereOr('phone','like','%'.$keyword.'%')
                     -> count('id');
    }

}