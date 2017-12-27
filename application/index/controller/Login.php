<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 16:23
 */
namespace app\index\controller;


use think\Controller;

class Login extends Controller
{
    public function attestation(){
        return $this -> fetch ('login/attestation');
    }
}