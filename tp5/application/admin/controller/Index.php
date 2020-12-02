<?php
namespace app\admin\controller;

use app\admin\model\Permission;
use app\admin\model\User;
use think\captcha\Captcha;
use think\Controller;
use think\Request;
use think\Session;
use think\view;

class Index extends Controller
{
    public function index()
    {
        return successRes($permissionList);
    }

    public function login(Request $request)
    {
        $view   = new View();
        $data   = $request->param();
        $result = '';
        if ($data) {
            $result = $this->validate($data, [
                'user_name|用户名' => 'require|max:25',
                'captcha|验证码'   => 'require',
                'password|密码'   => 'require',
            ]);
            if ($result !== true) {
                return errorRes($result);
            }
            if (!User::check_verify($data['captcha'])) {
                return errorRes('验证码错误');
            }
            $user = User::login($data);
            if (!$user) {
                return errorRes('用户名或密码错误');
            }
            $permissionList = Permission::getPermissionPath($user->role_id);
            return successRes(['user' => $user, 'permissions' => $permissionList]);
        }
    }

    public function logout()
    {
        Session::set('userId', '');
        return successRes();
    }

    public function verify()
    {
        $config = [
            // 验证码字体大小
            'fontSize' => 30,
            // 验证码位数
            'length'   => 3,
            // 关闭验证码杂点
            // 'useNoise' => false,
            // 随机背景
            // 'useImgBg' => true;
            // 中文验证码
            // 'useZh' => true;
        ];
        $captcha = new Captcha($config);

        return $captcha->entry();
    }
}
