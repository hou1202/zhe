<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 15:35
 */

namespace app\index\controller;


use think\Controller;
use think\Config as ThinkConfig;
use think\Log;

class Convert extends Controller {

    public function convert(){
        /*$verifyCode = rand(100000, 999999);
        $text = '【折金券】您的验证码为：' . $verifyCode . '，请在10分钟内完成验证';
        $objectUrl = 'https://dx.ipyy.net/smsJson.aspx?action=send&userid=&account='
            . ThinkConfig::get('sms_account')
            . '&password='
            . ThinkConfig::get('sms_password')
            . '&mobile='
            . 18297905431
            . '&content=' . urlencode($text) . '&sendTime=&extno=';
        $results = json_decode($objectUrl);
        try {
            $results = file_get_contents($objectUrl);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
        var_dump(json_decode($results) -> returnstatus);die;*/
        return $this -> fetch('convert/convert');
    }
}