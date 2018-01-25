<?php
namespace app\admin\controller;

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
            $Meeting         = new Meeting();
            $post            = $this->request->post();
            $post['content'] = $_POST['content'];
            if ($Meeting->validate(true)->allowField(true)->save($post) === false) {
                $this->error($Article->getError());
            }
            $this->success('新增成功', url('index'));
        } else {
            return view('edit');
        }
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            # code...
        }
    }

    public function del()
    {

    }
}
