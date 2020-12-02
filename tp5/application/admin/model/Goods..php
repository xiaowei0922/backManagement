<?php
namespace app\admin\model;

use think\Model;

class Goods extends Model
{
    protected $autoWriteTimestamp = true;
    private static $fieldList     = ['name', 'status', 'startTime', 'endTime'];

    public static function saveBrand($data)
    {
        if (empty($data['id'])) {
            $model = new Goods();
        } else {
            $model = Goods::get($data['id']);
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

    public static function detail($id)
    {
        return self::get($id);
    }
}
