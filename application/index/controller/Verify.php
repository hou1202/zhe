<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 13:34
 */

namespace app\index\controller;

use app\common\controller\IndexController;
use app\common\model\VerifyModel;
use app\index\model\User;
use think\Config as ThinkConfig;

class Verify extends IndexController
{
    const TYPE_LOGIN = 0;       //注册
    const TYPE_EDIT_PASSWD = 1;     //忘记密码
    const TYPE_WITHDRAW = 2;     //提现


    public static $phoneVerify = [
        self::TYPE_LOGIN => 'login_',
        self::TYPE_EDIT_PASSWD => 'epass_',
        self::TYPE_WITHDRAW => 'draw_',
    ];

    /**
     * 获取验证码
     * @return array
     */
    public function get()
    {
        $data = $this->request->post();
        //var_dump($data);
        //检查手机号
        if (isset($data['phone']) && preg_match('/^1[23465789]{1}\d{9}$/', $data['phone']) == 0) {
            return $this->jsonFail('手机号格式不正确');
        }
        $verify_fix = isset($data['type']) ? self::$phoneVerify[(int)$data['type']] : null;
        if ($verify_fix == null) {
            return $this->jsonFail('验证码类型错误');
        }
        //其它的验证规则
        switch ($data['type']) {
            case self::TYPE_LOGIN:
                $user = new User();
                $userInfo = $user->getUserInfoByMobile($data['phone']);
                if (!empty($userInfo)) {
                    return $this->jsonFail('该手机号已经注册过了');
                };
                break;
            case self::TYPE_EDIT_PASSWD || self::TYPE_WITHDRAW:
                $user = new User();
                $userInfo = $user->getUserInfoByMobile($data['phone']);
                if (!$userInfo) {
                    return $this->jsonFail('该用户不存在');
                }
                break;
            default:
                return $this->jsonFail('发送失败');
        }

        $verify = new VerifyModel();

        $data['ip'] = $this->request->ip();
        if($verify->send($data['type'], $data['phone'], $data)) {
            return $this->jsonSuccess('发送成功');
        } else {
            return $this->jsonFail('发送失败');
        }
    }

}
