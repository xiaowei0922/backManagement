<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\Brand;
use think\Request;

class Brands extends Base
{
    public function index(Request $request)
    {
        $data      = $request->param();
        $page      = $data['pageIndex'] ?? 1;
        $pageSize  = $data['pageSize'] ?? 20;
        $where     = Brand::getWheres($data);
        $brandList = Brand::where($where)->limit($pageSize)->page($page)->select();
        $total     = Brand::where($where)->count();
        return successRes(['total' => $total, 'data' => $brandList]);
    }

    public function saveBrand(Request $request)
    {
        $data = $request->param();
        if (!$data) {
            return errorRes('参数有误');
        }
        $brand = Brand::savebrand($data);
        if ($brand !== false) {
            return successRes($brand);
        }
        return errorRes('保存失败');
    }

    public function delBrand($id)
    {
        $res = Brand::destroy($id);
        if ($res === false) {
            return errorRes('删除失败');
        } else {
            return successRes();
        }
    }

    public function detail($id)
    {
        return successRes(['brandInfo' => Brand::detail($id)]);
    }
}
