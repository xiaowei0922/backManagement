<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function crossDomain()
{
    header('content-type:application:json;charset=utf8');
    header('Access-Control-Allow-Origin:http://localhost:8080');
    header('Access-Control-Allow-Credentials:true');
    header('Access-Control-Allow-Methods:POST,GET');
    header('Access-Control-Allow-Headers:x-requested-with,content-type');
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
        exit;
    }
}

function successRes($data = [], $msg = '操作成功')
{
    return json(['code' => 200, 'msg' => $msg, 'data' => $data]);
}

function errorRes($msg = '操作失败', $code = 500, $data = [])
{
    return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
}

function createTree($arr = array(), $pid = 0, $idName = 'id', $pidName = 'pid', $childName = 'children')
{
    $tree = array();
    foreach ($arr as $k => $v) {
        if ($v[$pidName] == $pid) {
            $tmp = $arr[$k];
            unset($tmp['pid']);
            unset($arr[$k]);
            $child = createTree($arr, $v[$idName], $idName, $pidName, $childName);
            if ($child) {
                $tmp[$childName] = $child;
            }
            $tree[] = $tmp;
        }
    }
    return $tree;
}
