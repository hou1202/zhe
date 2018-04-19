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
     * @ analyseCountForIp    修改反馈信息
     * */
    public function analyseCountForIp()
    {

        //获取、展示修改信息
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


    /*
   * @ talkDel    删除反馈信息
   * */
    public function talkDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $talk = new TalkingModel();
            $data['isdel'] = time();
            return $talk -> updateTalkById($data,$id) ? ReturnJson::ReturnJ("已成功删除此数据信息...") : ReturnJson::ReturnJ($talk -> getError(),"false");
        }
        return ReturnJson::ReturnJ("非法的数据提交信息!","false");
    }


    /*
   * @ talkSearch    搜索反馈信息
   * */
    public function talkSearch()
    {

        if ($this->request->isGet()  && isset($_GET['keyword'])){
            $post_data = $this->request->get('keyword');
            if ($post_data=='') {
                return ReturnJson::ReturnA('关键字不能为空，请重新搜索！');
            } else {
                $talk = new TalkingModel();
                $List = $talk->getSearchTalkByKeyword($post_data);
                if (empty($List->items())) {
                    return ReturnJson::ReturnA('未查询到相关数据，请重新搜索！');
                } else {
                    return view('talk_list' , ['List' => $List , 'Count' => $talk->getCountSearchTalkByKeyword($post_data)]);
                }
            }
        }else{
            return ReturnJson::ReturnA('非法数据操作!');
        }

    }
}