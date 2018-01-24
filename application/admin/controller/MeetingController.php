<?php
namespace app\admin\controller;

use app\admin\model\Meeting;
use think\Db;

class MeetingController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if ($this->request->isAjax) {
            $keyword = $this->request->post('keyword', '', 'trim');

            $model = Db::name('meeting')->where('status', 'in', '0,1');
            if ($keyword) {
                $model->where('title', 'like', '%d' . $keyword . '%');
            }

            $list = $model->order('id desc')->paginate(10);
            return view('index-list', [
                'list' => $list,
            ]);
        } else {
            return view();
        }
    }

    /***
    *   添加页面和添加操作 
    *   @return mixed
    */
    public function add()
    {
        if ($this->request->isPost()) {
            # code...
        }
    }
}
