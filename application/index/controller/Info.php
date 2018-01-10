<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/9
 * Time: 10:45
 */
namespace app\index\controller;

use app\common\controller\CommController;
use think\Db;
use think\Hook;
use think\Cookie;
use app\index\model\User;
use app\common\controller\ReturnJson;
use app\index\validate\CarryValidate;

class Info extends CommController
{
    /*
     * @info()个人信息展示页面
     * */
    public function info(){
        Hook::listen('CheckAuth', $params);
        $uid = Cookie::get('user');
        $user = new User();
        $result = $user -> getUserInfoByMobile($uid);
        if (!$result) {
            return ReturnJson::ReturnA("未查找到你们账户信息，请重新确认账户，或注册...");
        }
        return $this -> fetch('personal/info',['Info' => $result]);
    }

    /*
     *@infoEdit()个人信息修改提交页面
     *  */
    public function infoEdit(){
        if($this -> request -> isPost()){
            $data = $this->request->post();
            if(isset($data['user_name'])){
                if(empty($data['user_name'])){
                    return $this -> jsonSuccess('');
                }else{
                    //$ch
                }
                var_dump($data);die;
            }elseif(isset($data['alipay'])){
                var_dump($data);die;
            }else{
                return $this -> jsonFail('您所提交的信息有误...');
            }
        }else{
            return $this -> jsonFail('非正确的信息提交方式...');
        }
    }
}