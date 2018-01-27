<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/27
 * Time: 15:31
 */

namespace app\admin\model;


use think\Model;

class Favorites extends Model
{
    //自动写入创建及修改的时间戳
    protected $autoWriteTimestamp = true;

    //获取器自动处理state字段
    public function getStateAttr($value){
        $state = [0 => '关闭' , 1 => '启用'];
        return $state[$value];
    }


    //取值时间状态设置
    public function getCreateTimeAttr($value){
        return date('Y-m-d' , $value);
    }


    //按id统计数量
    public function getCountFavorites(){
        return $this -> field('id')
            ->count();
    }

    /*
     * @getFavoritesBannerList 列表显示
     * */
    public function getFavoritesBannerList(){
        return $this -> field('id,title,f_id,f_name,thumbnail,sort,state,create_time')
            -> order('sort DESC')
            -> paginate(10,false,['path' => '/admin/main#/favorites/bannerList' ]);
    }

    /*
     * @saveFavorites       保存选品图数据
     * */
    public function saveFavorites($data,$id=null){
        if($id){
            return $this -> allowField(true)->save($data,$id);
        }else{
            return $this -> allowField(true)->save($data);
        }
    }

    /*
     * @ getOneFavoritesByGoodsId  获取指定选品图信息
     * $id      商品GOODS_ID
     * */
    public function getOneFavoritesByGoodsId($id){
        return $this -> where('id',$id) -> find();
    }


    /*
     *@ delFavoritesById  删除指定选品图
     * @id      选品图ID
     * */
    public function delFavoritesById($id){
        return $this -> where('id',$id) -> delete();
    }
}