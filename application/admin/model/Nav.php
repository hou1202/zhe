<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/19
 * Time: 17:07
 */
namespace app\admin\model;


use think\Model;

class Nav extends Model
{
    protected static $tableName = 'think_nav';

    protected $autoWriteTimestamp = true;


    //取值状态显示
    protected function getStateAttr($value){
        $state = [0 => '关闭',1 => '启用'];
        return $state[$value];
    }


    //取值创建时间显示
    protected function getCreateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    //取值修改时间显示
    protected function getUpdateTimeAttr($value){
        return date('Y-m-d',$value);
    }

    /*
   * @getNavForList Nav列表显示
   * */
    public function getNavForList(){
        return $this -> field('id,title,img,key,sort,state,create_time')
            -> paginate(10,false,['path' => '/admin/main#/nav/navList' ]);
    }


    /*
     * @ getNavCount   统计Nav数量
     * */
    public function getNavCount(){
        return $this -> count('id');
    }

    /*
     * @saveNav      保存Nav数据信息
     * $data            提交数据信息
     * $id              存在时，为更新；不存在时为新建
     * */
    public function saveNav($data,$id = null){
        if($id == null){
            return $this -> allowField(true) ->save($data);
        }else{
            return $this-> allowField(true) -> save($data,['id' => $id]);

        }
    }

    /*
     * @ getNavById     获取指定Nav信息
     * $id                  Nav  ID
     * */
    public function getNavById($id){
        return $this -> where('id',$id) -> find();
    }

    /*
     * @delNavById       删除指定Nav信息
     * $id                  Nav  ID
     * */
    public function delNavById($id){
        return $this -> where('id',$id) -> delete();
    }







}