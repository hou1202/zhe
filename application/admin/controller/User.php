<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/13
 * Time: 16:02
 */
namespace app\admin\controller;


use app\admin\validate\UserAddValidate;
use app\admin\validate\UserUpdateValidate;
use app\common\controller\CommController;
use app\common\controller\ReturnJson;
use app\admin\model\User as UserModel;

/*
 * 用户信息管理
 **/
class User extends CommController
{
    /*
     * @ userList   用户列表
     * */
    public function userList(){
        $user = new UserModel();
        $userCount = $user -> getCountUser();
        $userList = $user -> getUserForList();
        return $userList -> items() ? view('user_list',['List' => $userList , 'Count' => $userCount]) : ReturnJson::ReturnA('未查询到相关数据信息...');
    }

    /*
     * @ userAdd    新建用户
     * */
    public function userAdd(){
        if($this->request->isPost()){
            $data = $this -> request -> post();
            $validate = new UserAddValidate();
            if($validate -> check($data)){
                $user = new UserModel();
                $data['invite'] = $this -> randInvite(6);
                return $user->allowField(true)->save($data) ? ReturnJson::ReturnJ("用户数据创建成功...","success","/user/userList") : ReturnJson::ReturnJ("用户数据创建失败，请重新提交...","false");
            }else{
                return ReturnJson::ReturnJ($validate -> getError(),"false");
            }
        }else{
            return view('user_add');
        }
    }

    /*
     * @ userUpdate    修改用户
     * */
    public function userUpdate(){
        //修改提交信息
        if($this -> request -> isPost()){
            $data = $this -> request -> Post();
            if(isset($data['id']) && empty($data['id'])){
                return ReturnJson::ReturnJ('无效的数据操作...','false','/user/userList');
            }
            $validate = new UserUpdateValidate();
            if($validate -> check($data)){

                if(empty($data['password'])){
                    unset($data['password']);
                }
                $user = new UserModel();
                return $user -> save($data,['id' => $data['id']]) ? ReturnJson::ReturnJ('数据更新成功...','success','/user/userList') : ReturnJson::ReturnJ('数据更新失败，请重新操作...','false');
            }
            return ReturnJson::ReturnJ($validate -> getError(),'false');
        }

        //获取、展示修改信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $user = new UserModel();
            $getOne = $user -> getOneUserInfoById($id);
            return $getOne ?  view('user_update',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的用户信息...","#/user/user_list");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }

    }

    /*
    * @ userCat    查看用户
    * */
    public function userCat(){
        //获取、展示修改信息
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $user = new UserModel();
            $getOne = $user -> getOneUserInfoById($id);
            return $getOne ?  view('user_cat',['getOne' => $getOne]) : ReturnJson::ReturnH("未获取到相应的用户信息...","#/user/user_list");
        }else{
            ReturnJson::ReturnA("无效的修改操作...");
        }

    }

    /*
    * @ userCat    删除用户
    * */
    public function userDel(){
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $this -> request -> get('id');
            $user = new UserModel();
            return $user -> where('id',$id) -> delete() ? ReturnJson::ReturnJ("已成功删除此用户信息...") : ReturnJson::ReturnJ($user -> getError(),"false");
        }
        return ReturnJson::ReturnJ("非法的数据提交信息!","false");
    }

    /*
    * @ userSearch    搜索用户
    * */
    public function userSearch()
    {

        if ($this->request->isGet()  && isset($_GET['keyword'])){
            $post_data = $this->request->get('keyword');
            if ($post_data=='') {
                return ReturnJson::ReturnA('关键字不能为空，请重新搜索！');
            } else {
                $user = new UserModel();
                $List = $user->getSearchUserByKeyword($post_data);
                if (empty($List->items())) {
                    return ReturnJson::ReturnA('未查询到相关数据，请重新搜索！');
                } else {
                    return view('user_list' , ['List' => $List , 'Count' => $user->getCountSearchUserByKeyword($post_data)]);
                }
            }
        }else{
            return ReturnJson::ReturnA('非法数据操作!');
        }

    }

    /*
     * @randInvite 生成随机邀请码
     * @length 生成邀请码的位数
     */
    protected function randInvite($length){
        $pattern = '1234567890ABCDEFGHJKLMNPQRSTUVWXYZ';
        $key = null;
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,33)};    //生成php随机数
        }
        return $key;
    }




}