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
        if (!self::deleteOutDateMeeting()) {
            $this->error('删除过期会议失败');
        }
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
        $room = self::getRoomList();
        $this->assign('room', $room);
        $this->assign('list', $list);
        return view();
    }

    /**
     * 添加页面和添加操作
     * @return mixed
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
            if (!self::isInUse($post)) {
                if (session('?inUsePost')) {
                    session('inUsePost', null);
                }
                if (empty($post['id'])) {
                    $newMeeting = Db::name('meeting')->order('id desc')->select();
                    $id = $newMeeting['0']['id'];
                    $post['id'] = $id;
                }
                session('inUsePost', $post);
                $this->success('此会议室该时间段被占用', url('edit'));
            }
            $this->success('新增成功', url('index'));
        } else {
            $roomList = self::getRoomList();
            $this->assign('roomList', $roomList);
            return view('edit');
        }
    }

    /**
     * 编辑页面和编辑操作
     * @return mixed
     */

    public function edit()
    {
        if (session('?inUsePost')) {
            $post = session('inUsePost');
            session('inUsePost', null);
            $roomList = self::getRoomList();
            $this->assign('roomList', $roomList);
            $this->assign('info', $post);
            return view();
        } else if ($this->request->isPost()) {
            $Meet = new Meeting();
            $post = $this->request->post();
            $start_time = $post['start_time'];
            $post['start_time'] = strtotime($start_time);
            if (!self::isInUse($post)) {
                if (session('?inUsePost')) {
                    session('inUsePost', null);
                }
                session('inUsePost', $post);
                $this->success('此会议室该时间段被占用', url('edit'));
            }
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
     * 快速禁用
     * @return json
     */
    public function setStatus()
    {
        $id = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && Db::name('meeting')->where('id', $id)->update(['status' => $status]) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }

    /**
     * 删除会议
     * @return json
     */

    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('meeting')->where('id', $id)->setField('status', 2) !== false) {
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

    /**
     * 获取会议室名称列表
     * @return array
     */
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
        $model = Db::name('meeting');
        if (isset($post['id']) && !empty($post['id'])) {
            $id = $post['id'];
            $model = $model->where('id', 'NEQ', $id);
        }
        $list = $model->where('status', '1')->where('room', $post['room'])->select();
        if (empty($list)) {
            return true;
        }
        foreach ($list as $value) {
            $start_t = $value['start_time'];
            $end_t = $start_t + $value['use_time'] * 60 * 60;
            if ($end_time <= $end_t && $end_time >= $start_t) {
                return false;
            }
            if ($start_time <= $end_t && $start_time >= $start_t) {
                return false;
            }
        }
        return true;
    }

    /**
     * 将过期的会议下架
     * @return bool
     */
    public function deleteOutDateMeeting()
    {
        $date = time();
        $list = Db::name('meeting')->where('status', '1')->select();
        foreach ($list as $value) {
            $id = $value['id'];
            if ($value['start_time'] <= $date) {
                if (Db::name('meeting')->where('id', $id)->setField('status', 0) == false) {
                    return false;
                }
            }
        }
        return true;
    }
}
