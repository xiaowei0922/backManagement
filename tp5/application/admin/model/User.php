<?php
namespace app\admin\model;

use think\captcha\Captcha;
use think\Model;
use think\Session;

class User extends Model
{
    protected $autoWriteTimestamp = true;
    private static $fieldList     = ['user_name', 'mobile', 'email', 'status', 'startTime', 'endTime', 'role_id'];

    public static function upload()
    {
        // 获取表单上传文件
        $file = request()->file('avatar');
        if (!$file) {
            return ['code' => 1, 'msg' => '文件不存在'];
        }
        $filePath = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'user';
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size' => 2 * 1024 * 1024, 'ext' => 'jpg,jpeg,png,gif'])->rule('uniqid')->move($filePath);
        if ($info) {
            return ['code' => 0, 'msg' => 'uploads' . DS . 'user/' . $info->getFilename()];
        } else {
            // 上传失败获取错误信息
            return ['code' => 2, 'msg' => $file->getError()];
        }
    }

    public static function saveUser($data)
    {
        if (!empty($data['password'])) {
            $data['password'] = md5($data['password']);
        }
        if (empty($data['id'])) {
            $userModle = new User($data);
            $user      = $userModle->allowField(true)->save();
        } else {
            $userModle = User::get($data['id']);
            if (!empty($data['avatar'])) {
                @unlink($userModle->avatar);
            }
            $user = $userModle->allowField(true)->save($data);
        }
        return $user;
    }

    public static function getWheres($data)
    {
        $where = [];
        foreach ($data as $field => $value) {
            if (!$value || !in_array($field, self::$fieldList)) {
                continue;
            }
            switch ($field) {
                case 'user_name':
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

    public static function login($data)
    {
        $user = self::get(['user_name' => $data['user_name'], 'password' => md5($data['password'])]);
        if (!$user) {
            return false;
        }
        Session::set('userId', $user->id);
        return $user;
    }

    public static function check_verify($code, $id = '')
    {
        $captcha = new Captcha();
        return $captcha->check($code, $id);
    }

    public static function getUser($id)
    {
        return self::get($id);
    }

}
