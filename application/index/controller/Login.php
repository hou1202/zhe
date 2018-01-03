<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:23
 */
namespace app\index\controller;


use app\common\controller\ReturnJson;
use app\index\validate\UserRegisterValidate;
use app\index\validate\UserLoginValidate;
use app\index\model\User;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Db;


class Login extends Controller
{
    public function attestation(){
        $invitation = null;

        if($this -> request -> get()){
            if(isset($_GET['invitation']) && !empty($_GET['invitation'])){
                $invitation = $_GET['invitation'];
            }
        }
        return $this -> fetch ('login/attestation',['invitation' => $invitation]);

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
                    return ReturnJson::ReturnA("您的验证码信息有误，请重新确认...");
                }

                //实例化用户User  Model
                $user = new User();

                //判断手机号是否注册
                if($user -> field('id') -> where('phone',$data['phone']) -> find()){
                    return ReturnJson::ReturnA("您的帐号已注册，请登录...");
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
                if($user -> allowField(true) -> save($data)){
                    //清除短信Session
                    Session::delete('login_'.$data['phone']);
                    Cookie::set('user',$data['phone']);
                    //更新验证码记录
                    Db::table('think_log_verify')->where('phone='.$data['phone'].' AND type=0 AND verify='.$data['code'])->update(['status'=>1, 'e_time'=>date('Y-m-d H:i:s')]);

                    return $this -> redirect('personal/personal');
                }else{
                    return ReturnJson::ReturnA("注册出现了一些小故障，请重新操作...");
                }

                /*var_dump(Session::get('login_'.$data['phone']));
                var_dump($data);*/
            }else{
                return ReturnJson::ReturnA($userVal->getError());
            }

        }else{
            return $this -> redirect('index/index');
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
                $result = $user ->field('id')
                                -> where('phone',$data['phone'])
                                -> where('password',md5($data['password']))
                                -> find();
                if($result){
                    Cookie::set('user',$data['phone']);
                    return $this -> redirect('personal/personal');
                }else{
                    return ReturnJson::ReturnA("您登录的用户暂不存在，请先注册...");
                }

            }else{
                return ReturnJson::ReturnA($userVal->getError());
            }
        }else{
            return $this -> redirect('index/index');
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
}