<?php
namespace app\admin\validate;

use think\Validate;

class Meeting extends Validate
{
    protected $rule = [
        'title|会议标题' => 'require|max:64',
        'start_time|开始时间' => 'require',
        'use_time|占用时间' => 'require',
        'remark|备注' => 'max:200',
        'status|状态' => 'require|in:0,1',
    ];
}
