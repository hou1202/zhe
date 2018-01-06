<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/5
 * Time: 18:06
 */
namespace app\index\controller;

use app\common\controller\CommController;
use think\Hook;
use think\Cookie;
use app\index\model\User;
use app\common\controller\ReturnJson;

class Friends extends CommController
{
    public function friends(){
        Hook::listen('CheckAuth', $params);
        $uid = Cookie::get('user');
        $user = new User();
        $result = $user-> getUserInfoByMobile($uid);
        if (!$result) {
            return ReturnJson::ReturnA("未查找到你们账户信息，请重新确认账户，或注册...");
        }
        $friendsInfo = $user ->getInviteUserByPId($result['id']);
        //var_dump($friendsInfo);die;
        return $this -> fetch('personal/friends',['FriendsInfo'=>$friendsInfo,'UsrId'=>$result['id']]);
    }

    public function friendsCapitalTotal(){
        if($this -> request -> isPost()){
            $data = $this -> request -> post();
            var_dump($data);die;
        }
    }
}