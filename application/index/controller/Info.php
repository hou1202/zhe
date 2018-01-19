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
use think\File;

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
            }elseif(isset($data['portrait'])){
                $rule = [
                    'portrait' => 'require|max:255',
                ];
                $message = [
                    'portrait.require' => '头像不得为空...',
                    'portrait.max' => '头像上传地址有误，请重新上传...',
                ];
                return $this -> infoEditComm($rule,$message,$data,'portrait');
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

    /*图像上传*/
    public function uploader(){
        // 获取表单上传文件
        $files = request()->file('');
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                $path['name'] = DS . 'uploads/' . $info->getSavename();
            }else{
                // 上传失败获取错误信息
                return $this->error($file->getError()) ;
            }
        }
        echo json_encode($path);
    }
}