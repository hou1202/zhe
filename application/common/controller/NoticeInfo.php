<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/3
 * Time: 17:30
 */
namespace app\common\controller;
use think\Db;

class NoticeInfo
{
    static function CarryNoticeInfo($id,$money){
        $noticeInfo['title'] = '淘币兑换申请通知';
        $noticeInfo['content'] = '您已于 '.date("Y/m/d").' 发起淘币兑换申请，兑换数量：'.$money.' 淘币。小二正在积极处理中（24小时内），请您耐心等待！感谢您对折金券平台的信任！';
        $noticeInfo['uid'] = $id;
        return Db::table('think_notice') -> insert($noticeInfo);
    }
}