<?php
namespace app\admin\controller;

use app\admin\model\Meeting;
use think\Db;
use think\Request;

class MeetingController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        // if ($this->request->isAjax) {
        //     $keyword = $this->request->post('keyword', '', 'trim');

        //     $model = Db::name('meeting')->where('status', 'in', '0,1');
        //     if ($keyword) {
        //         $model->where('title', 'like', '%d' . $keyword . '%');
        //     }

        //     $list = $model->order('id desc')->paginate(10);
        //     return view('index-list', [
        //         'list' => $list,
        //     ]);
        // } else {
        return view();
        // }
    }

    public function lists()
    {
        $model = DB::name('meeting')->where('status', 'in', [0, 1]);
        $meetings = $model->order('id desc')->paginate(10);
        $list = $meetings->getCollection()->toArray();
        $this->assign('list', $lists);
        return view();
    }

    /***
     *   添加页面和添加操作
     *   @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $Meeting = new Meeting();
            $post = $this->request->post();
            $start_time = $post['start_time'];
            $post['start_time'] = strtotime($start_time);
            if ($Meeting->validate(true)->allowField(true)->save($post) === false) {
                $this->error($Meeting->getError());
            }
            $this->success('新增成功', url('index'));
        } else {
            $roomList = self::getRoomList();
            $this->assign('roomList', $roomList);
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

    /**
     * 获取会议室列表
     * @return array
     */
    public function getRoomList()
    {
        $model = Db::name('room')->where('status', 'in', [0, 1]);
        $rooms = $model->order('id desc')->paginate();
        $list = $rooms->getCollection()->toArray();
        return $list;
    }
}
