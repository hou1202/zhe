<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/15
 * Time: 14:09
 */
namespace app\admin\controller;


use think\Controller;
use app\admin\model\Nav as NavModel;
use app\common\controller\ReturnJson;
use app\admin\validate\NavValidate;

class Nav extends Controller
{

    /*
      * @ navList   导航信息列表
      * */
    public function navList(){
        $nav = new NavModel();
        $Count = $nav -> getNavCount();
        $List = $nav -> getNavForList();
        return $List -> items() ? view('nav_list',['List' => $List , 'Count' => $Count]) : ReturnJson::ReturnH('未查询到相关数据信息...', '#/nav/navAdd');

    }

    /*
      * @ navAdd   新建导航信息
      * */
    public function navAdd(){
        if($this->request->isPost()){
            $data = $this -> request -> post();
            $validate = new NavValidate();
            if($validate -> check($data)){
                $nav = new NavModel();
                return $nav -> saveNav($data) ? ReturnJson::ReturnJ("数据创建成功...","success","/nav/navList") : ReturnJson::ReturnJ("新闻创建失败，请重新提交...","false");
            }else{
                return ReturnJson::ReturnJ($validate -> getError(),"false");
            }
        }else{
            return view('nav_add');
        }

    }

    /*
     * @ navUpdate    修改导航信息
     * */
    public function navUpdate()
    {
        //修改提交信息
        if ($this->request->isPost()) {
            $data = $this->request->Post();
            //var_dump($data);die;
            if (isset($data['id']) && empty($data['id'])) {
                return ReturnJson::ReturnJ('无效的数据操作...', 'false', '/nav/navList');
            }
            $validate = new NavValidate();
            if ($validate->check($data)) {
                $nav = new NavModel();
                return $nav -> saveNav($data,$data['id']) ? ReturnJson::ReturnJ('数据更新成功...', 'success', '/nav/navList') : ReturnJson::ReturnJ('数据更新失败，请重新操作...', 'false');
            }
            return ReturnJson::ReturnJ($validate->getError(), 'false');
        }

        //获取、展示修改信息
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $this->request->get('id');
            $nav = new NavModel();
            $getOne = $nav->getNavById($id);
            return $getOne ? view('nav_update', ['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的数据信息...", "#/nav/nav_list");
        } else {
            ReturnJson::ReturnA("无效的修改操作...");
        }
    }


    /*
   * @ navDel    删除导航信息
   * */
    public function navDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $nav = new NavModel();
            return $nav -> delNavById($id) ? ReturnJson::ReturnJ("已成功删除此数据信息...") : ReturnJson::ReturnJ($nav -> getError(),"false");
        }
        return ReturnJson::ReturnJ("非法的数据提交信息!","false");
    }



}