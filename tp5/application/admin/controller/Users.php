<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\Role;
use app\admin\model\User;
use think\Request;

class Users extends Base
{

    public function index(Request $request)
    {
        $data           = $request->param();
        $page           = $data['pageIndex'] ?? 1;
        $pageSize       = $data['pageSize'] ?? 20;
        $where          = User::getWheres($data);
        $userList       = User::where($where)->limit($pageSize)->page($page)->select();
        $total          = User::where($where)->count();
        $roleIdNameList = Role::getIdNameList();
        return json(['total' => $total, 'data' => ['userList' => $userList, 'roleIdNameList' => $roleIdNameList]]);
    }

    public function view($id)
    {
        return User::where('id', $id)
            ->field('id,role_id,user_name,mobile,email,avatar,status')
            ->find()->toJson();
    }

    public function saveUser(Request $request)
    {
        crossDomain();
        $data = $request->param();
        echo "<pre/>";
        print_r($data);
        print_r($_FILES);
        die;
        if (!$data) {
            return json(['error_code' => 403, 'msg' => '参数有误', 'data' => []]);
        }
        $avatarArr = User::upload();
        if ($avatarArr['code'] == 0) {
            $data['avatar'] = $avatarArr['msg'];
        } else if ($avatarArr['code'] == 2) {
            return json(['error_code' => 403, 'msg' => $avatarArr['msg'], 'data' => []]);
        } else {
            unset($data['avatar']);
        }
        $user = User::saveUser($data);
        if ($user) {
            return json(['error_code' => 200, 'msg' => '保存成功', 'data' => []]);
        }
        return json(['error_code' => 500, 'msg' => '保存失败', 'data' => []]);
    }

    public function delUser($id)
    {
        User::destroy($id);
        return json(['error_code' => 200, 'msg' => '删除成功', 'data' => []]);
    }
}
