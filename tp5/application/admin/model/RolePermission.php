<?php
namespace app\admin\model;

use think\Cache;
use think\Model;

class RolePermission extends Model
{
    public static function saveRolePermission($data)
    {
        self::destroy(['role_id' => $data['role_id']]);
        Cache::set('rolePermission_' . $data['role_id'], '');
        $rolePermission = new self;
        if (empty($data['permissionList'])) {
            return true;
        }
        foreach ($data['permissionList'] as $pid) {
            $list[] = ['role_id' => $data['role_id'], 'permission_id' => $pid];
        }
        return $rolePermission->saveAll($list);

    }

    public static function getList()
    {
        $list = self::field('id,parent_id pid,name label')
            ->order('sort_num', 'desc')
            ->select();
        $list = collection($list)->toArray();
        $tree = createTree($list, 0);
        return $tree;
    }

    public static function getPermByRoleId($roleId)
    {
        return self::where(['role_id' => $roleId])->column('permission_id');
    }
}
