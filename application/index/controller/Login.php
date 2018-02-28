<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:23
 */
namespace app\index\controller;


use app\common\controller\CommController;
use app\common\controller\ReturnJson;
use app\index\validate\UserRegisterValidate;
use app\index\validate\UserLoginValidate;
use app\index\model\User;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Db;


class Login extends CommController
{
    public function attestation(){
        if(Cookie::has('user')){
            $uid = Cookie::get('user');
            if(Db::table("think_user") -> where('phone',$uid) -> find()){
                $this -> redirect('personal/personal');
            }
        }else{
            $invitation = null;
            if($this -> request -> get()){
                if(isset($_GET['invitation']) && !empty($_GET['invitation'])){
                    $invitation = $_GET['invitation'];
                }
            }
            if(Cookie::has('invitation')){
                $invitation = Cookie::get('invitation');
            }
            return $this -> fetch('login/attestation',['invitation' => $invitation]);
        }
    }

    /*
     * @regitster() 用户注册
     * */
    public function register(){
        if ($this -> request -> isPost()) {
            $data = $this -> request -> post();
            $userVal = new UserRegisterValidate();
            if($userVal -> check($data)){
                //判断验证码
                /*if(!Session::has('login_'.$data['phone'])){
                    return ReturnJson::ReturnA("请获取有效验证码...");
                }*/
                if($data['code'] != Session::get('login_'.$data['phone'])){
                    return $this ->jsonFail('您的验证码信息有误，请重新确认...');
                }

                //实例化用户User  Model
                $user = new User();

                //判断手机号是否注册
                if($user -> field('id') -> where('phone',$data['phone']) -> find()){
                    return $this ->jsonFail('您的帐号已注册，请登录...');
                }

                /*
                 * @写入数据
                 * $data['user_name']   设置用户注册默认数据
                 * $data['portrait']    设置用户注册默认数据
                 * $data['invite']      设置用户注册邀请码
                 * */
                $data['user_name'] = '折金户'.rand(1000,9999);
                $data['portrait'] = '/static/index/images/default-portrait.jpg';
                $data['invite'] = $this -> randInvite(6);
                $p_id = $user -> getParentUserIdByInvite($data['p_invite']);
                if($p_id){
                    $data['p_id'] = $p_id['id'];
                }
                if($user -> allowField(true) -> save($data)){
                    //清除短信Session
                    Session::delete('login_'.$data['phone']);
                    //设置用户COOKIE，并设置保存时间7天
                    Cookie::set('user',$data['phone'],604800);
                    //更新验证码记录
                    Db::table('think_log_verify')->where('phone='.$data['phone'].' AND type=0 AND verify='.$data['code'])->update(['status'=>1, 'e_time'=>date('Y-m-d H:i:s')]);
                    return $this->jsonSuccess('登录成功','/index/personal/personal');
                    //$this -> redirect('personal/personal');
                }else{
                    return $this ->jsonFail('注册出现了一些小故障，请重新操作...');
                }

            }else{
                //return ReturnJson::ReturnA($userVal->getError());
                return $this ->jsonFail($userVal->getError());

            }

        }else{
            $this -> redirect('index/index');
        }
    }
    /*
     * @login()  用户登录
     * */
    public function login(){
        if($this -> request ->isPost()){
            $data = $this -> request -> post();
            $userVal = new UserLoginValidate();
            if($userVal -> check($data)){
                $user = new User();
                $result = $user -> getLoginUserInfoByPhone($data['phone'],$data['password']);
                if($result){
                    if($result['state'] == 0){
                        //return ReturnJson::ReturnA(1);
                        return $this ->jsonFail('您的帐号处于异常状态，无法登录，请联系管理员...');
                    }else{
                        //设置用户COOKIE，并设置保存时间7天
                        Cookie::set('user',$data['phone'],604800);
                        return $this->jsonSuccess('登录成功','/index/personal/personal');
                        //$this -> redirect('personal/personal');

                    }

                }else{
                    //return ReturnJson::ReturnA(2);
                    return $this ->jsonFail('您登录的账户信息有误，请核对后再登录...');
                }

            }else{
                //return ReturnJson::ReturnA($userVal->getError());
                return $this ->jsonFail($userVal->getError());
            }
        }else{
            $this -> redirect('index/index');
        }
    }

    /*
     * @randInvite 生成随机邀请码
     * @length 生成邀请码的位数
     */
    protected function randInvite($length){
        $pattern = '1234567890ABCDEFGHJKLMNPQRSTUVWXYZ';
        $key = null;
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,33)};    //生成php随机数
        }
        return $key;
    }

    public function share(){
        if($this -> request -> get()){
            if(isset($_GET['invitation']) && !empty($_GET['invitation'])){
                $invitation = $_GET['invitation'];
                Cookie::set('invitation',$invitation);
            }
        }
        return $this->fetch('share/share');
    }
}