<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/13
 * Time: 17:12
 */
namespace app\admin\validate;


use think\Validate;

class UserUpdateValidate extends Validate
{
    protected $rule = [
        'user_name' => 'require|max:30',
        'phone' => 'require|length:11|number',
        'password' => 'min:6|max:18',
        'portrait' => 'require',
        'alipay' => 'min:3',
        'reak_name' => 'min:6|max:18',
        'balance' => 'number',
        'integral' => 'number',
        'state' => 'require',
    ];

    protected $message = [
        'user_name.require' => '用户名称不得为空...',
        'user_name.max' => '用户名称最大为得超过10位...',
        'phone.require' => '手机号码格式不正确...',
        'phone.length' => '手机号码格式不正确...',
        'phone.number' => '手机号码格式不正确...',
        'password.max' => '用户密码不得超过18位...',
        'password.min' => '用户密码不得少于6位...',
        'portrait.require' => '用户头像不得为空...',
        'alipay.require' => '支付宝账户格式不正确...',
        'reak_name.min' => '真实姓名不得少于2位...',
        'reak_name.max' => '真实姓名不得超过6位...',
        'balance.number' => '余额为数字...',
        'integral.number' => '积分为数字...',
        'state.require' => '用户状态不得为空...',
    ];
}