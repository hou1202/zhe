<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/15
 * Time: 14:10
 */

namespace app\admin\model;



use think\Model;

class Visit extends Model
{
    protected static $tableName = 'think_visit';

    protected $autoWriteTimestamp = true;




    //取值创建时间显示
    protected function getCreateTimeAttr($value){
        return date('Y-m-d H:i:s',$value);
    }

    //取值修改时间显示
    protected function getUpdateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    /*
    * @getAnalyseForList  访问数据列表
    * */
    public function getAnalyseForList(){
        return $this -> field('id,ip,address,create_time')
            ->order('id DESC')
            -> paginate(10,false,['path' => '/admin/main#/visit/analyseList' ]);
    }

    /*
     * @ getAnalyseCount   访问数据总量
     * */
    public function getAnalyseCount(){
        //今天
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        //$map = "'id','between',[$beginToday,$endToday]";
        $analyseCount['today'] = $this -> where('create_time','between',[$beginToday,$endToday]) -> count('id');
        //本周
        $beginWeek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
        $analyseCount['week'] = $this -> where('create_time','between',[$beginWeek,$endToday]) -> count('id');
        //本月
        $beginMonth=mktime(0,0,0,date('m'),1,date('Y'));
        $analyseCount['month'] = $this -> where('create_time','between',[$beginMonth,$endToday]) -> count('id');
        //本年
        $beginYear=mktime(0,0,0,1,1,date('Y'));
        $analyseCount['year'] = $this -> where('create_time','between',[$beginYear,$endToday]) -> count('id');
        //总计
        $analyseCount['total'] = $this -> count('id');
        return $analyseCount;
    }

    /*
    * @getAnalyseByIp  获取指定IP访问数据
    * */
    public function getAnalyseByIp($ip){
        return $this -> field('id,ip,address,create_time')
            ->where('ip',$ip)
            ->order('id DESC')
            -> paginate(10,false,['path' => '/admin/main#/visit/analyseList' ]);
    }

    /*
    * @getAnalyseByIp  获取指定IP访问数据
    * */
    public function getCountByIp($ip){
        return $this ->where('ip',$ip)
            -> count('id');
    }




}