<?php
namespace app\admin\model;

use app\admin\model\RolePermission;
use think\db;
use think\Model;

class Role extends Model
{
    protected $autoWriteTimestamp = true;
    private static $fieldList     = ['name', 'description', 'status', 'sort_num', 'startTime', 'endTime'];

    public static function saveRole($data)
    {
        if (empty($data['id'])) {
            $roleModel = new Role();
        } else {
            $roleModel = Role::get($data['id']);
        }
        Db::startTrans();
        try {
            $role = $roleModel->allowField(true)->save($data);
            if ($role === false) {
                Db::rollback();
                return false;
            }
            $res = RolePermission::saveRolePermission(['role_id' => $roleModel->id, 'permissionList' => $data['permIdList']]);
            if (!$res) {
                Db::rollback();
                return false;
            }
            Db::commit();
            return $roleModel;
        } catch (\Exception $e) {
            throw new \Exception($e, 1);
            Db::rollback();
        }

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

    public static function getRoleIdNameList()
    {
        return self::where(['status' => 1])
            ->column('id value,name label,status');
    }

    public static function getIdNameList()
    {
        return self::column('id,name,status');
    }

    public static function detail($id)
    {
        return self::get($id);
    }

}
