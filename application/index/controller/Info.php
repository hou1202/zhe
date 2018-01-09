<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/9
 * Time: 10:45
 */
namespace app\index\controller;

use app\common\controller\CommController;

class Info extends CommController
{
    public function info(){
        return $this -> fetch('personal/info');
    }
}