<?php
namespace app\admin\validate;

use think\Validate;

class Room extends Validate
{
    protected $rule = [
        'name|会议室名称'    => 'require|max:30',
        'address|会议室地址' => 'require|max:100',
        'remark|备注'     => 'max:200',
        'status|状态'     => 'require|in:0,1',
    ];
}
