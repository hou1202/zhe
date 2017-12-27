<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26
 * Time: 10:50
 */

namespace app\admin\controller;

use think\Validate;

class NewsValidate extends Validate
{
    protected $rule = [
        'title' => 'require|max:90',
        'thumbnail' => 'require',
        'info' => 'require|max:90',
        'content' => 'require',
    ];

    protected $message = [
        'title.require' => '标题内容不得为空...',
        'title.max' => '标题最大为得超过30位...',
        'thumbnail.require' => '缩略图不得为空...',
        'info.require' => '副标题不得为空...',
        'info.max' => '副标题长度最大不得超过30位...',
        'content.require' => '正文内容不得为空...',
    ];

}