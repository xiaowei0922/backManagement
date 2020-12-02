<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        // $userId = Session::get('userId');
        // if (!$userId) {
        //     echo json_encode(['code' => 401, 'msg' => '用户未登录', 'data' => []]);die;
        // }
        // $user = User::getUser($userId);
        // if (!$user) {
        //     echo json_encode(['code' => 401, 'msg' => '用户未登录', 'data' => []]);die;
        // }
        // $permissionList = json_decode(Cache::get('rolePermission_' . $user->role_id), 1);
        // if (!$permissionList) {
        //     $permissionList = Permission::getPermissionPath($user->role_id);
        //     Cache::set('rolePermission_' . $user->role_id, json_encode($permissionList));
        // }
        // $request = request();
        // $url     = $request->baseUrl();
        // if (!in_array($url, $permissionList)) {
        //     echo json_encode(['code' => 403, 'msg' => '无权限', 'data' => []]);die;
        // }
    }
}
