<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/19
 * Time: 17:07
 */
namespace app\admin\model;


use think\Model;

class Banner extends Model
{
    protected static $tableName = 'think_banner';

    protected $autoWriteTimestamp = true;


    //取值状态显示
    protected function getStateAttr($value){
        $state = [0 => '关闭',1 => '启用'];
        return $state[$value];
    }

    //取值类型显示
    protected function getTypeAttr($value){
        $state = [0 => '首页滚动Banner'];
        return $state[$value];
    }

    //取值链接形式显示
    protected function getShapeAttr($value){
        $state = [0 => '无链接',1 => '内部链接',2 => '接口链接',];
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
   * @getBannerForList Banner列表显示
   * */
    public function getBannerForList(){
        return $this -> field('id,title,img,type,shape,link,state,sort,create_time')
            //-> order('l.id DESC')
            -> paginate(10,false,['path' => '/admin/main#/banner/bannerList' ]);
    }


    /*
     * @ getBannerCount   统计Banner数量
     * */
    public function getBannerCount(){
        return $this -> count('id');
    }

    /*
     * @saveBanner      保存Banner数据信息
     * $data            提交数据信息
     * $id              存在时，为更新；不存在时为新建
     * */
    public function saveBanner($data,$id = null){
        if($id == null){
            return $this -> allowField(true) ->save($data);
        }else{
            return $this-> allowField(true) -> save($data,['id' => $id]);

        }
    }

    /*
     * @ getBannerById     获取指定Banner信息
     * $id                  Banner  ID
     * */
    public function getBannerById($id){
        return $this -> where('id',$id) -> find();
    }

    /*
     * @delBannerById       删除指定Banner信息
     * $id                  Banner  ID
     * */
    public function delBannerById($id){
        return $this -> where('id',$id) -> delete();
    }







}