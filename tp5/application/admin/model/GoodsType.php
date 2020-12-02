<?php
namespace app\admin\model;

use think\Model;

class GoodsType extends Model
{
    protected $autoWriteTimestamp = true;
    private static $fieldList     = ['name', 'parent_id', 'status', 'startTime', 'endTime'];

    public static function saveGoodsType($data)
    {
        if (empty($data['id'])) {
            $model = new GoodsType();
        } else {
            $model = GoodsType::get($data['id']);
        }
        $res = $model->allowField(true)->save($data);
        if ($res === false) {
            return false;
        }
        return $model;
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

    public static function getIdNameList()
    {
        return self::column('id,name,status');
    }

    public static function detail($id)
    {
        return self::get($id);
    }

    public static function getCountByPid($pid)
    {
        return self::where(['parent_id' => $pid])->count();
    }

}
