<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/15
 * Time: 14:09
 */
namespace app\admin\controller;


use think\Controller;
use app\admin\model\Visit as VisitModel;
use app\common\controller\ReturnJson;
use think\Validate;

class Visit extends Controller
{

    /*
      * @ analyseList   访问用户列表
      * */
    public function analyseList(){
        $visit = new VisitModel();
        $Count = $visit -> getAnalyseCount();
        $List = $visit -> getAnalyseForList();
        return $List -> items() ? view('analyse_list',['List' => $List , 'Count' => $Count ]) : ReturnJson::ReturnA('未查询到相关数据信息...');

    }

    /*
     * @ analyseCountForIp    IP统计用户访问
     * */
    public function analyseCountForIp()
    {
        if (isset($_GET['ip']) && !empty($_GET['ip'])) {
            $id = $this->request->get('ip');
            $visit = new VisitModel();
            $List = $visit->getAnalyseByIp($id);
            $Count = $visit->getCountByIp($id);
            return $List -> items() ? view('appoint_list',['List' => $List , 'Count' => $Count ]) : ReturnJson::ReturnA('未查询到相关数据信息...');
        } else {
            ReturnJson::ReturnA("无效的修改操作...");
        }
    }



}