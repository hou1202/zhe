<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/3
 * Time: 11:04
 */
namespace app\index\controller;

use app\common\controller\CommController;
use think\Controller;
use think\Db;
use think\Hook;
use think\Cookie;
use think\Session;
use app\index\model\User;
use app\index\model\Carry as CarryModel;
use app\index\model\Notice;
use app\common\controller\ReturnJson;
use app\index\validate\CarryValidate;


class Carry extends CommController
{
    public function index()
    {
        Hook::listen('CheckAuth', $params);
        $uid = Cookie::get('user');
        $user = new User();
        $result = $user -> getUserInfoByMobile($uid);
        if (!$result) {
            return ReturnJson::ReturnA("未查找到你们账户信息，请重新确认账户，或注册...");
        }
        return $this->fetch('personal/carry', ['Carry' => $result]);
    }

    /*
     * @carryApply() 处理提现申请
     * */
    public function carryApply()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $carryVal = new CarryValidate();
            if ($carryVal->check($data)) {
                if ($data['code'] != Session::get('draw_' . $data['phone'])) {
                    return $this ->jsonFail('您的验证码信息有误，请重新确认...');
                }
                //var_dump(1);
                //实例化用户User  Model
                $user = new User();
                $userResult = $user->field('balance')->where('id', $data['uid'])->find();
                if ($data['money'] > $userResult['balance']) {
                    return $this ->jsonFail('您的所兑换的淘币数已超过所拥有的淘币数，请重新确认...');
                }

                $carryModel = new CarryModel();
                $carryResult = $carryModel->allowField(true)->save($data);
                if ($carryResult) {
                    //清除短信Session
                    Session::delete('draw_' . $data['phone']);
                    //更新验证码记录
                    Db::table('think_log_verify')->where('phone=' . $data['phone'] . ' AND type=2 AND verify=' . $data['code'])->update(['status' => 1, 'e_time' => date('Y-m-d H:i:s')]);
                    //更新用户数据
                    $user->where('id', $data['uid'])->where('phone', $data['phone'])->setDec('balance', $data['money']);
                    //创建通知信息
                    $notice = new Notice();
                    $notice -> CarryNoticeInfo($data['uid'], $data['money']);
                    return $this->jsonSuccess('您已成功提交淘币兑换申请...','/index/personal/personal');
                    //$this->redirect('personal/personal');
                } else {
                    return $this ->jsonFail('您的申请出现了一些小毛病，请重新操作...');
                }

            } else {
                return $this ->jsonFail($carryVal->getError());
            }
        } else {
            return ReturnJson::ReturnA("非正常提现操作，请重新操作...");
            //return $this ->jsonFail('非正常提现操作，请重新操作...');
        }

    }

}