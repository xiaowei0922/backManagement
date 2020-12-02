<?php
namespace app\admin\model;

use think\Model;

class Permission extends Model
{
    /** 类型 1-菜单 2-操作 */
    const TYPE_MENU   = 1;
    const TYPE_ACTION = 2;

    protected $autoWriteTimestamp = true;
    private static $fieldList     = ['name', 'parent_id', 'startTime', 'endTime', 'type'];
    public static function savePermission($data)
    {
        if (empty($data['id'])) {
            $permission = new Permission();
        } else {
            $permission = Permission::get($data['id']);
        }
        $saveRes = $permission->allowField(true)->save($data);
        if ($saveRes !== false) {
            return $permission;
        }
        return false;
    }

    public static function getWheres($data)
    {
        $where = [];
        foreach ($data as $field => $value) {
            if (!$value || !in_array($field, self::$fieldList)) {
                continue;
            }
            switch ($field) {
                case 'name':
                    $where[$field] = ['like', "%{$value}%"];
                    break;
                case 'startTime':
                    $where['create_time'] = ['egt', strtotime($value)];
                    break;
                case 'endTime':
                    $where['create_time'] = ['elt', strtotime($value)];
                    break;
                default:
                    $where[$field] = ['eq', $value];
                    break;
            }
        }
        if (!empty($data['startTime']) && !empty($data['endTime'])) {
            $where['create_time'] = ['between', [strtotime($data['startTime']), strtotime($data['endTime'])]];
        }
        return $where;
    }

    public static function getMenuList()
    {
        return self::where(['type' => self::TYPE_MENU])
            ->field('id value,name label')
            ->select();
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

    public static function getPermissionPath($roleId)
    {
        $permissionIdList = RolePermission::getPermByRoleId($roleId);
        $where            = [
            'id'   => ['in', $permissionIdList],
            'path' => ['neq', ''],
        ];
        return self::where($where)->column('path');
    }

}
