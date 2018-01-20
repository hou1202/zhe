<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/19
 * Time: 17:06
 */
namespace app\admin\controller;


use app\admin\validate\BannerValidate;
use think\Controller;
use app\admin\model\Banner as BannerModel;
use app\common\controller\ReturnJson;

class Banner extends Controller
{
    public function bannerList(){
        $banner = new BannerModel();
        $Count = $banner -> getBannerCount();
        $List = $banner -> getBannerForList();
        return $List -> items() ? view('banner_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnH('未查询到相关数据信息...','#/banner/bannerAdd');
    }

    public function bannerAdd(){
        if($this->request->isPost()){
            $data = $this -> request -> post();
            $validate = new BannerValidate();
            if($validate -> check($data)){
                $banner = new BannerModel();
                return $banner -> saveBanner($data) ? ReturnJson::ReturnJ("数据创建成功...","success","/banner/bannerList") : ReturnJson::ReturnJ("新闻创建失败，请重新提交...","false");
            }else{
                return ReturnJson::ReturnJ($validate -> getError(),"false");
            }
        }else{
            return view('banner_add');
        }

    }

    public function bannerUpdate(){
        //修改提交信息
        if($this -> request -> isPost()){
            $data = $this -> request -> Post();
            if(isset($data['id']) && empty($data['id'])){
                return ReturnJson::ReturnJ('无效的数据操作...','false','/banner/bannerList');
            }
            $validate = new BannerValidate();
            if($validate -> check($data)){
                $banner = new BannerModel();
                return $banner -> saveBanner($data,$data['id']) ? ReturnJson::ReturnJ('数据更新成功...','success','/banner/bannerList') : ReturnJson::ReturnJ('数据更新失败，请重新操作...','false');
            }
            return ReturnJson::ReturnJ($validate -> getError(),'false');
        }

        //获取、展示修改信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $banner = new BannerModel();
            $getOne = $banner -> getBannerById($id);
            return $getOne ?  view('banner_update',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的数据信息...","#/banner/bannerList");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }

    }


    public function bannerDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $banner = new BannerModel();
            return $banner -> delBannerById($id) ? ReturnJson::ReturnJ("已成功删除此信息...") : ReturnJson::ReturnJ($banner -> getError(),"false");
        }
        return ReturnJson::ReturnJ("非法的数据提交信息!","false");
    }
}