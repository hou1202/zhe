<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13
 * Time: 14:24
 */

namespace app\index\controller;
use app\admin\controller\ReturnJson;
use app\admin\model\Background;
use app\admin\model\Need;
use app\admin\model\News;
use app\admin\model\Program;
use app\admin\model\Video;
use think\Controller;
use think\Validate;
use think\Cookie;

class Index extends Controller
{
    public function index(){
       return $this -> fetch('index/index');
    }


    public function about(){
        $background = new Background();
        $backgroundImg['banner'] = $background -> field('thumbnail') -> where('id',6) -> find();
        $backgroundImg['footer'] = $background -> field('thumbnail') -> where('id',4) -> find();
        return $this -> fetch('',['backgroundImg'=>$backgroundImg]);
    }

    public function program(){
        $program = new Program();
        $programList = $program -> field('id,title,info,thumbnail')
            -> where('state',1)
            -> order('create_time DESC')
            -> paginate(8,false,['path' => '/index/index/program']);
        $background = new Background();
        $backgroundImg['banner'] = $background -> field('thumbnail') -> where('id',5) -> find();
        $backgroundImg['footer'] = $background -> field('thumbnail') -> where('id',4) -> find();
        return $this -> fetch('',['backgroundImg'=>$backgroundImg,'programList'=>$programList]);
    }

    public function news(){
        $news = new News();
        $newsList = $news -> field('id,title,info,thumbnail')
            -> where('state',1)
            -> order('create_time DESC')
            -> paginate(8,false,['path' => '/index/index/news']);
        $background = new Background();
        $backgroundImg['banner'] = $background -> field('thumbnail') -> where('id',7) -> find();
        $backgroundImg['footer'] = $background -> field('thumbnail') -> where('id',4) -> find();
        return $this -> fetch('',['backgroundImg'=>$backgroundImg,'newsList'=>$newsList]);
    }

    /*
     * $_GET['id'] GET传值 所对应的主索引
     * $_GET['type']) GET传值 判断对应的details类型
     * type=1   项目信息详情
     * type=2   资讯信息详情
     */
    public function details(){
        if(isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['type']) && !empty($_GET['id'])){
            $data = $this -> request -> get();
            $background = new Background();
            $backgroundImg['footer'] = $background -> field('thumbnail') -> where('id',4) -> find();
            if($data['type']==2){
                $news = new News();
                $getOne = $news -> where('id',$data['id']) -> limit(1) -> find();
                $backgroundImg['banner'] = $background -> field('thumbnail') -> where('id',10) -> find();
            }elseif($data['type']==1){
                $program = new Program();
                $getOne = $program -> where('id',$data['id']) -> limit(1) -> find();
                $backgroundImg['banner'] = $background -> field('thumbnail') -> where('id',9) -> find();
            }else{
                return ReturnJson::ReturnA("无效的操作...");
            }
            return $this -> fetch('',['backgroundImg'=>$backgroundImg,'getOne'=>$getOne]);
        }else{
            return ReturnJson::ReturnA("无效的操作...");
        }
    }

    public function contact(){
        $background = new Background();
        $backgroundImg['footer'] = $background -> field('thumbnail') -> where('id',4) -> find();
        return $this -> fetch('',['backgroundImg'=>$backgroundImg]);
    }

    public function need(){
        if($this->request->isPost()){
            if(Cookie::has('requestNeed')){
                return ReturnJson::ReturnA('您的需求信息已经提交，请勿重复提交...');
            }
            $data = $this -> request ->post();
            $rule = [
                'name' => 'require|max:30',
                'phone' => 'require|number|length:11',
                'content' => 'require|max:240',
            ];
            $message = [
                'name.require' => '提交名称不得为空...',
                'name.max' => '提交名称最大不得超过10位...',
                'phone.require' => '联系方式不得为空...',
                'phone.number' => '联系方式格式不正确...',
                'phone.length' => '联系方式格式不正确...',
                'content.require' => '提交内容不得为空...',
                'content.max' => '提交内容最大不得超过80位...',
            ];
            $needValidate = new Validate($rule,$message);
            if($needValidate -> check($data)){
                $need = new Need();
                if($need ->save($data)){
                    Cookie::set('requestNeed','true',120);
                    $content='客户名称：'.$data['name'].';<br>联系方式：'.$data['phone'].';<br>需求信息：'.$data['content'];
                    //send_mail($content);
                    return ReturnJson::ReturnH('您提需求已经提交，100C人员将即时联系您...' , 'index/index');
                }else{
                    return ReturnJson::ReturnA('提交失败，请重新操作...');
                }
            }else{
                return ReturnJson::ReturnA($needValidate -> getError());
            }
        }else{
            $this -> redirect('index/index');
        }
    }


}