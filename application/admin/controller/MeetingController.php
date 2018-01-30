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
        $model = Db::name('meeting')->where('status', 'in', [0, 1]);
        $meetings = $model->order('id desc')->paginate(10);
        $list = $meetings->getCollection()->toArray();
        $user = Db::name('admin')->where('status',1)->order('id desc')->select();
        // $timeList = array();
        // foreach ($list as $key) {
        //     $start_time = date('Y-m-d h:i', $key['start_time']);
        //     array_push($timeList, $start_time);
        // }
        $this->assign('lists', $lists);
        $this->assign('lists',$user);
        $this->assign('lists', $timeList);
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
            $Meet = new Meet();
            $post = $this->request->post();
            if ($Meet->validate(true)->isUpdate(true)->allowField(true)->save($post) === false) {
                $this->error($Meet->getError());
            }
            $this->success('修改成功', url('index'));
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('会议不存在');
            }
            $info = Meet::get($id);
            $this->assign('info', $info);
            return view();
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
        $list = Db::name('room')->where('status', 'in', [0, 1])->order('id desc')->select();
        return $list;
    }
}
