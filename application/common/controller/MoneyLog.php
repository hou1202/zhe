<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/2/10
 * Time: 16:34
 */

namespace app\common\controller;
use think\Db;

class MoneyLog
{

    protected static $logTable = 'think_log_bonus';

    static public function BonusLog($id,$type,$money,$sid=null){
        $data = [
            'uid' => $id,
            'type' => $type,
            'money' => $money,
            'c_time' => time(),
        ];
        if($sid != null){
            $data['sid'] = $sid;
        }
        Db::table(static::$logTable) -> insert($data);
    }
}