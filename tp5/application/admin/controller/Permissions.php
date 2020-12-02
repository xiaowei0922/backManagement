<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\Permission;
use think\Request;

class Permissions extends Base
{

    public function index(Request $request)
    {
        $data           = $request->param();
        $page           = $data['pageIndex'] ?? 1;
        $pageSize       = $data['pageSize'] ?? 20;
        $where          = Permission::getWheres($data);
        $permissionList = Permission::where($where)->limit($pageSize)->page($page)->select();
        $total          = Permission::where($where)->count();
        return successRes(['total' => $total, 'data' => $permissionList]);
    }

    public function savePermission(Request $request)
    {
        if (!$data) {
            return errorRes('参数有误');
        }
        $permission = Permission::savePermission($data);
        if ($permission !== false) {
            return successRes($permission);
        }
        return errorRes('保存失败');
    }

    public function getMenuList()
    {
        $menuList = Permission::getMenuList();
        return successRes($menuList);
    }

    public function delPermission($id)
    {
        $res = Permission::destroy($id);
        if ($res === false) {
            return errorRes('删除失败');
        } else {
            return successRes();
        }
    }

    public function getPermissionList()
    {
        $list = Permission::getList();
        return successRes($list);
    }
}
