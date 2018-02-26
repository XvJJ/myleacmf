<?php
namespace app\admin\Model;

use think\Model;

class Meeting extends Model
{
    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 自动完成
     * @var array
     */
    protected $insert = ['create_aid'];

    /**
     * 设置操作人
     * @return mixed
     */
    protected function setCreateAidAttr()
    {
        return session('admin.id');
    }
}
