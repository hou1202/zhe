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
use think\Db;
use app\index\model\User;
use app\common\controller\ReturnJson;
use think\Validate;

class Talking extends CommController
{
    public function talking(){

        Hook::listen('CheckAuth', $params);
        $uid = Cookie::get('user');
        $user = new User();
        $result = $user-> getUserInfoByMobile($uid);
        if (!$result) {
            return ReturnJson::ReturnA("未查找到你们账户信息，请重新确认账户，或注册...");
        }

        //接收提交反馈
        if($this->request->isPost()){
            $data = $this->request->post();
            //var_dump($data);
            $rule = [
                'content' => 'require'
            ];
            $msg = [
                'content.require' => '请填入您要吐槽的内容...'
            ];
            $valiTalk = new Validate($rule,$msg);
            if($valiTalk->check($data)){
                $data['uid'] = $result['id'];
                $data['create_time'] = time();
                Db::name('talking') -> insert($data);
                return $this->jsonSuccess("您的吐槽我们已成功接收，我们尽快查阅，感谢您的支持...");
            }else{
                return $this->jsonSuccess($valiTalk->getError());
            }
        }

        $talkList = Db::name('talking') -> where('uid',$result['id']) -> where('isdel',0) -> select();
        return $this -> fetch('personal/talking',['TList' => $talkList]);
    }


}