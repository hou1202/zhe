<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/13
 * Time: 16:07
 */
namespace app\admin\model;


use think\Model;

class User extends Model
{
    public static $tableName = 'think_user';

    protected $autoWriteTimestamp = true;

    //密码MD5自动加密
    protected function setPasswordAttr($value){
        return md5($value);
    }

    //取值状态显示
    protected function getStateAttr($value){
        $state = [0 => '冻结',1 => '正常'];
        return $state[$value];
    }

    //取值时间显示
    protected function getCreateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    protected function getGradeAttr($value){
        $grade = [0 => '青铜小兵' , 1 => '白银连长', 2 => '黄金将军', 3 => '铂金元帅', 4 => '王者大帝'];
        return $grade[$value];
    }


    /*
     * @getUserForList 用户列表显示用户
     * */
    public function getUserForList(){
        return $this -> alias('l')
            -> field('r.user_name as p_id,l.id,l.user_name,l.portrait,l.phone,l.invite,l.balance,l.integral,l.state,l.create_time')
            ->join('think_user r','l.p_id = r.id','left')
            -> order('l.id DESC')
            ->group('l.id')
            -> paginate(10,false,['path' => '/admin/main#/user/userList' ]);
    }

    /*public function getUserForList(){
        return $this -> field('id,user_name,portrait,phone,p_id,invite,balance,integral,state,create_time')
            -> order('id DESC')
            -> paginate(10,false,['path' => '/admin/main#/user/userList' ]);
    }*/


    /*
     * @ getCountUser   统计注册用户数量
     * */
    public function getCountUser(){
        return $this -> count('id');
    }

    /*
     * @ getOneUserInfoById  获取指定用户信息
     * $id      用户ID
     * */
    public function getOneUserInfoById($id){
        return $this -> where('id',$id) -> find();
    }

    /*
     * @ getSearchUserByKeyword  通过关键词搜索用户
     * $keyword      关键词
     * */
    public function getSearchUserByKeyword($keyword){
        return $this -> field('id,user_name,portrait,phone,invite,p_id,balance,integral,state,create_time')
            -> where('id','=',$keyword)
            -> whereOr('phone','like','%'.$keyword.'%')
            -> whereOr('user_name','like','%'.$keyword.'%')
            -> whereOr('invite','like','%'.$keyword.'%')
            -> order('id DESC')
            -> paginate(10,false,['path' => '/admin/main#/user/userList' ]);
    }

    /*
     * @ getCountSearchUserByKeyword  统计通过关键词搜索用户
     * $keyword      关键词
     * */
    public function getCountSearchUserByKeyword($keyword){
        return $this -> field('id')
            -> where('id','=',$keyword)
            -> whereOr('phone','like','%'.$keyword.'%')
            -> whereOr('user_name','like','%'.$keyword.'%')
            -> whereOr('invite','like','%'.$keyword.'%')
            ->count();
    }

}