<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\admin\model\GoodsType;
use think\Request;

class GoodsTypes extends Base
{
    public function index(Request $request)
    {
        $data          = $request->param();
        $page          = $data['pageIndex'] ?? 1;
        $pageSize      = $data['pageSize'] ?? 20;
        $where         = GoodsType::getWheres($data);
        $goodsTypeList = GoodsType::where($where)->limit($pageSize)->page($page)->select();
        $total         = GoodsType::where($where)->count();
        return successRes(['total' => $total, 'data' => $goodsTypeList]);
    }

    public function saveGoodsType(Request $request)
    {
        $data = $request->param();
        if (!$data) {
            return errorRes('参数有误');
        }
        $goodsType = GoodsType::savegoodsType($data);
        if ($goodsType !== false) {
            return successRes($goodsType);
        }
        return errorRes('保存失败');
    }

    public function delGoodsType($id)
    {
        $pidCount = GoodsType::getCountByPid($id);
        if ($pidCount > 0) {
            return errorRes('请先删除子级分类');
        }
        $res = GoodsType::destroy($id);
        if ($res === false) {
            return errorRes('删除失败');
        } else {
            return successRes();
        }
    }

    public function detail($id)
    {
        return successRes(['goodsTypeInfo' => GoodsType::detail($id)]);
    }
}
