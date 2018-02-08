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
        return view();
    }

    /**
     * 会议列表
     */

    public function lists()
    {
        $model = Db::name('meeting')->where('status', 'in', [0, 1]);
        $meetings = $model->order('id desc')->paginate(10);
        $list = $meetings->getCollection()->toArray();
        // $user = Db::name('admin')->where('status', 1)->column('nickname');
        // $room = Db::name('room')->column('name');
        $room = self::getRoomList();
        $this->assign('room', $room);
        $this->assign('list', $list);
        // $this->assign('user', $user);
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
            if (!self::isInUse($post)) {
                $roomList = self::getRoomList();
                $this->assign('roomList', $roomList);
                $this->assign('info', $post);
                // return view('edit');
                $this->warning('此会议室该时间段被占用', url('edit'));
            }
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

    /***
     *  编辑页面和编辑操作
     * @return mixed
     */

    public function edit()
    {
        if ($this->request->isPost()) {
            $Meet = new Meeting();
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
            $info = Meeting::get($id);
            $roomList = self::getRoomList();
            $this->assign('roomList', $roomList);
            $this->assign('info', $info);
            return view();
        }

    }

    /**
     * 设置状态
     */
    public function setStatus()
    {
        $id = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && (new Meeting())->where('id', $id)->update(['status' => $status]) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }

    /**
     * 删除会议
     */

    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('room')->where('id', $id)->setField('status', 2) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');

    }

    /**
     * 获取会议室列表
     * @return array
     */
    public function getRoomList()
    {
        $list = Db::name('room')->where('status', 'in', [0, 1])->select();
        return $list;
    }
    public function getRoomNameList()
    {
        $list = Db::name('room')->column('id', 'name');
        return $list;
    }

    /**
     * 判断该会议室在该时间段是否被占用
     * @return bool
     */
    public function isInUse($post)
    {
        $start_time = $post['start_time'];
        $end_time = $start_time + $post['use_time'] * 60 * 60;
        $list = Db::name('meeting')->where('status', '1')->where('room', $post['room'])->select();
        if (empty($list)) {
            return true;
        }
        foreach ($list as $value) {
            $start_t = $value['start_time'];
            $end_t = $start_t + $value['use_time'] * 60 * 60;
            if ($end_time < $end_t && $end_time > $start_t) {
                return false;
            }
            if ($start_time < $end_t && $start_time > $start_t) {
                return false;
            }
        }
        return true;
    }
}
