<?php
namespace app\admin\validate;

use think\Validate;

class Meeting extends Validate
{
    protected $rule = [
        'title|会议标题' => 'require|max:64',
        'date|日期' => 'require',
        'start_time|开始时间' => 'require',
        'end_time|结束时间' => 'require',
        'remark|备注' => 'max:200',
        'status|状态' => 'require|in:0,1',
    ];
}
