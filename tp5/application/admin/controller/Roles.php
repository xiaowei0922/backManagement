<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\Role;
use app\admin\model\RolePermission;
use think\Request;

class Roles extends Base
{

    public function index(Request $request)
    {
        crossDomain();
        $data     = $request->param();
        $page     = $data['pageIndex'] ?? 1;
        $pageSize = $data['pageSize'] ?? 20;
        $where    = Role::getWheres($data);
        $roleList = Role::where($where)->limit($pageSize)->page($page)->select();
        $total    = Role::where($where)->count();
        return successRes(['total' => $total, 'data' => $roleList]);
    }

    public function saveRole(Request $request)
    {
        crossDomain();
        $data = $request->param();
        if (!$data) {
            return errorRes('参数有误');
        }
        $role = Role::saveRole($data);
        if ($role !== false) {
            return successRes($role);
        }
        return errorRes('保存失败');
    }

    public function delRole($id)
    {
        crossDomain();
        $res = Role::destroy($id);
        if ($res === false) {
            return errorRes('删除失败');
        } else {
            return successRes();
        }
    }

    public function getRoleIdNameList()
    {
        crossDomain();
        return successRes(Role::getRoleIdNameList());
    }

    public function detail($id)
    {
        crossDomain();
        return successRes(['roleInfo' => Role::detail($id), 'permIdList' => RolePermission::getPermByRoleId($id)]);
    }
}
