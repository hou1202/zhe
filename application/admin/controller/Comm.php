<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 14:27
 */

namespace app\admin\controller;


use think\Controller;

class Comm extends Controller
{
    //form表单
    public function form1()
    {
        return view("form/form1");
    }

    public function form2()
    {
        return view("form/form2");
    }

    public function form3()
    {
        return view("form/form3");
    }

    public function form4()
    {
        return view("form/form4");
    }

    public function form5()
    {
        return view("form/form5");
    }

    public function form6()
    {
        return view("form/form6");
    }

    public function form7()
    {
        return view("form/form7");
    }

    public function form8()
    {
        return view("form/form8");
    }

    //table表格
    public function table1()
    {
        return view("table/table1");
    }

    public function table2()
    {
        return view("table/table2");
    }

    //widget
    public function widget1()
    {
        return view("widget/widget1");
    }

    public function widget2()
    {
        return view("widget/widget2");
    }

    //page
    public function page1()
    {
        return view("page/page1");
    }

    public function page2()
    {
        return view("page/page2");
    }

    //fragment
    public function fragment1()
    {
        return view("fragment/fragment1");
    }

    public function fragment2()
    {
        return view("fragment/fragment2");
    }

}