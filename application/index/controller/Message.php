<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/3
 * Time: 9:42
 */
namespace app\index\controller;


use think\Controller;
use think\Db;
use app\common\controller\ReturnJson;

class Message extends Controller
{
    public function index(){
        if($this -> request -> isGet()){
            if(isset($_GET['type']) && !empty($_GET['type'])){
                $type = $this -> request -> get('type');
                $result = Db::table('think_message') -> field('id,title,content')
                                                     -> where('type',$type)
                                                     -> find();
                if($result){
                    return $this -> fetch('other/message',['Message'=>$result]);
                }else{
                    return ReturnJson::ReturnA("您看查看的信息有误，请重试...");
                }
            }else{
                return ReturnJson::ReturnA("您看查看的信息有误，请重试...");
            }
        }else{
            return ReturnJson::ReturnA("您看查看的信息有误，请重试...");
        }
    }
}