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
use think\Validate;

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
                $rule = [
                    'user_name' => 'require|max:30',
                ];
                $message = [
                    'user_name.require' => '设置用户名不得为空...',
                    'user_name.max' => '用户名最大长度不得超过10个字...',
                ];
                return $this -> infoEditComm($rule,$message,$data,'user_name');
            }elseif(isset($data['alipay'])){
                $rule = [
                    'alipay' => 'require|max:30',
                ];
                $message = [
                    'alipay.require' => '支付宝帐户不得为空...',
                    'alipay.max' => '支付宝帐户最大长度不得超过30个字...',
                ];
                return $this -> infoEditComm($rule,$message,$data,'alipay');
            }elseif(isset($data['real_name'])){
                $rule = [
                    'real_name' => 'require|max:30',
                ];
                $message = [
                    'real_name.require' => '真实姓名不得为空...',
                    'real_name.max' => '真实姓名最大长度不得超过10个字...',
                ];
                return $this -> infoEditComm($rule,$message,$data,'real_name');
            }else{
                return $this -> jsonFail('您所提交的信息有误...');
            }
        }else{
            return $this -> jsonFail('非正确的信息提交方式...');
        }
    }

    protected function infoEditComm($rule,$message,$data,$field){
        $val = new Validate($rule,$message);
        if($val->check($data)){
            $user = new User();
            $user -> setFieldById($data['id'],$field,$data[$field]);
            return $this -> jsonSuccess('资料更新成功','/index/info/info');
        }else{
            return $this -> jsonFail($val->getError());
        }
    }
}