<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/27
 * Time: 13:28
 */

namespace app\admin\controller;


use think\Controller;
use think\Config as ThinkConfig;
use app\common\controller\ReturnJson;
use app\api\controller\TopClient;
use app\api\controller\request\TbkUatmFavoritesGetRequest;
use app\admin\model\Favorites as FavoritesModel;
use app\admin\validate\FavoritesValidate;

class Favorites extends Controller
{
    /*
    * @ favoritesList    选品库列表
    * */
    public function favoritesList()
    {
        $c = new TopClient;
        $c->appkey = ThinkConfig::get('T_AppKey');
        $c->secretKey = ThinkConfig::get('T_AppSecret');
        $req = new TbkUatmFavoritesGetRequest;
        $req->setPageNo("1");
        $req->setPageSize("20");
        $req->setFields("favorites_title,favorites_id,type");
        $req->setType("-1");
        $resp = $c->execute($req);
        return $resp->results->tbk_favorites ? view('favorites_list', ['List' => $resp->results->tbk_favorites]) : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
    * @ bannerList    选品图列表
    * */
    public function bannerList()
    {
        $favorites = new FavoritesModel();
        $count = $favorites->getCountFavorites();
        $newsList = $favorites->getFavoritesBannerList();
        return $newsList->items() ? view('banner_list', ['List' => $newsList, 'Count' => $count]) : ReturnJson::ReturnH('未查询到相关数据信息...', '#/favorites/favoritesAdd');
    }

    /*
    * @ favoritesAdd    新建选品图
    * */
    public function favoritesAdd()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $validate = new FavoritesValidate();
            if ($validate->check($data)) {
                $favorites = new FavoritesModel();
                return $favorites->saveFavorites($data) ? ReturnJson::ReturnJ("数据创建成功...", "success", "/favorites/bannerList") : ReturnJson::ReturnJ("数据创建失败，请重新提交...", "false");
            } else {
                return ReturnJson::ReturnJ($validate->getError(), "false");
            }
        } else {
            return view('favorites_add');
        }

    }


    /*
    * @ favoritesUpdate    修改选品图
    * */
    public function favoritesUpdate(){
        //修改提交信息
        if($this -> request -> isPost()){
            $data = $this -> request -> Post();
            if(isset($data['id']) && empty($data['id'])){
                return ReturnJson::ReturnJ('无效的数据操作...','false','/favorites/bannerList');
            }
            $validate = new FavoritesValidate();
            if($validate -> check($data)){
                $favorites = new FavoritesModel();
                return $favorites -> saveFavorites($data,$data['id']) ? ReturnJson::ReturnJ('数据更新成功...','success','/favorites/bannerList') : ReturnJson::ReturnJ('数据更新失败，请重新操作...','false');
            }
            return ReturnJson::ReturnJ($validate -> getError(),'false');
        }

        //获取、展示修改信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $favorites = new FavoritesModel();
            $getOne = $favorites -> getOneFavoritesByGoodsId($id);
            //var_dump($getOne);die;
            return $getOne ?  view('favorites_update',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的数据信息...","#/favorites/bannerList");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }
    }

    /*
    * @ favoritesDel    删除选品图
    * */
    public function favoritesDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $favorites = new FavoritesModel();
            return $favorites -> delFavoritesById($id) ? ReturnJson::ReturnJ("已成功删除此数据信息...") : ReturnJson::ReturnJ($favorites -> getError(),"false");
        }
        return ReturnJson::ReturnJ("非法的数据提交信息!","false");
    }
}