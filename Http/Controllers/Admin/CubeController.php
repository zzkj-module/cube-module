<?php
// @author liming
namespace Modules\Cube\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Cube\Http\Controllers\Controller;
use Modules\Cube\Http\Requests\Admin\CubeEditRequest;
use Modules\Cube\Entities\Applet;
use Modules\Cube\Entities\Cube;

class CubeController extends Controller
{
    /**
     * 分页列表
     */
    public function list()
    {
        $styleArr = Cube::getStyleArr();
        return view('cubeview::admin.cube.list', compact('styleArr'));
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];

        $name = $request->input('name');
        if($name != "") $where[] = ["name", "like", "%{$name}%"];
        $style = $request->input("style");
        if($style != "") $where[] = ["style", "=", $style];

        //获取总条数
        $count = Cube::where($where)->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = Cube::where($where)
            ->offset($offset)
            ->limit($pagesize)
            ->orderBy("id", "desc")
            ->get();
        foreach ($list as &$item){
            $item['style_txt'] = Cube::getStyleArr()[$item->style];
        }
        return $this->success(compact('list', 'count'));
    }

    /**
     * 新增|编辑魔方信息
     * @param $id
     */
    public function edit(CubeEditRequest $request)
    {
        if($request->isMethod('post')) {
            $request->check();
            $data = $request->post();
            if(isset($data["id"])){
                $info = Cube::where("id",$data["id"])->first();
                if(!$info) return $this->failed('数据不存在');
            }else{
                $info = new Cube();
            }

            $info->name = $data["name"];
            $info->style = $data["style"];
            $data["data"] = $data["data"] ?? [];
            $infoData = [];
            foreach ($data["data"] as $item){
                $item["open_type"] = $item["open_type"] ?? "";
                $item["pic"] = $item["pic"] ?? "";
                if(!file_exists($item["pic"])) return $this->failed('上传的魔方图片不存在');
                $item["route"] = $item["route"] ?? "";
                $infoData[] = $item;
            }

            $info->data = json_encode($infoData, JSON_UNESCAPED_UNICODE);
            if(!$info->save()) return $this->failed('操作失败');
            return $this->success();
        } else {
            $id = $request->input('id') ?? 0;
            $styleArr = Cube::getStyleArr();
            if($id > 0){
                $info = Cube::where('id',$id)->first();
                $title = "编辑魔方";
                $info->data = json_decode($info->data, true);
            }else{
                $info = new Cube();
                $title = "新增魔方";
            }
            $domain = Cube::getDomain();
            if(Schema::hasTable("applet")){
                $applet = Applet::orderBy("id")->get()->toArray();
            }else{
                $applet = [];
            }
            foreach ($applet as &$item){
                $item["params"] = json_decode($item["params"], true);
            }
            return view('cubeview::admin.cube.edit', compact('info', 'title', 'styleArr', 'domain', 'applet'));
        }
    }

    /**
     * 删除魔方
     */
    public function del(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            $info = Cube::where('id', $id)->first();
            if (!$info) return $this->failed("数据不存在");
            if(!$info->delete()) return $this->failed("操作失败");
            return $this->success();
        }
        return $this->failed('请求出错.');
    }
}
