<?php
namespace app\admin\controller;

use app\admin\controller\CommonController;
use app\admin\model\Room;
use think\Db;
use think\Request;

class RoomController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return view();
    }

    public function lists()
    {
        $model = Db::name('room')->where('status', 'in', [0, 1]);
        $rooms = $model->order('id desc')->paginate(10);
        $list = $rooms->getCollection()->toArray();
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
            $Room = new Room();
            $post = $this->request->post();
            if ($Room->validate(true)->allowField(true)->save($post) === false) {
                $this->error($Room->getError());
            }
            $this->success('新增成功', url('index'));
        } else {
            return view('edit');
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $Room = new Room();
            $post = $this->request->post();
            if ($Room->validate(true)->isUpdate(true)->allowField(true)->save($post) === false) {
                $this->error($Room->getError());
            }
            $this->success('修改成功', url('index'));
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('会议室不存在');
            }
            $info = Room::get($id);
            $this->assign('info', $info);
            return view();
        }
    }

    /**
     * 设置状态
     * @return mixed
     */
    public function setStatus()
    {
        $id = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && (new Room())->where('id', $id)->update(['status' => $status]) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }

    /**
     * 删除
     * @return json
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('room')->where('id', $id)->setField('status', 2) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }
}
