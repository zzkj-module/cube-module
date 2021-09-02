@extends('admin.public.header')
@section('title',$title)
@section('listcontent')
    <style>
        .picInfo{
            overflow: hidden;
            margin-bottom: 30px;
        }
        .picInfo-left{
            width: 200px;
            float: left;
        }
        .picInfo-button{
            float: left;
        }
        .picInfo-small-url{
            float: left;
            width: 450px;
            position: relative;
            min-height: 38px;
        }
        .picInfo-small-url input:first-of-type{
            display: inline;
            width: 400px;
            position: absolute;
            padding-left: 90px;
        }
        .picInfo-small-url .share-span{
            position: absolute;
            display: block;
            line-height: 36px; color: #464a4c;
            padding: 0 13px;
            border: 1px solid #e6e6e6;
            background-color: #eceeef;
            border-radius: 2px;
        }
        .picInfo-small-url .share-span:hover{
            cursor:pointer
        }
        .attrPicDiv{
            position: relative;
        }
        .attrPic{
            width: 92px;
            height: 38px;
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
        }
        .showAttrImage{
            margin-top: 10px;
        }
    </style>
    <div id="appletList" style="display: none">
        <div class="layui-form layuimini-form">
            <div class="appletInfo"></div>
            <div class="appletParams"></div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-normal" id="appletBtn" lay-submit lay-filter="appletBtn">确认</button>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-form layuimini-form">
        @if(isset($info->id))
        <input type="hidden" name="id" value="{{$info->id}}" />
        @endif

        @foreach($styleArr as $k => $style)
        <div class="layui-form-item">
            <label class="layui-form-label">{{$style}}</label>
            <div class="layui-input-block">
                <img style="width: 100%; border: 1px solid rgb(238, 238, 238);" src="{{ asset('modules/cube/img/img-cube-demo-'.$k.'.png') }}" alt="{{$style}}" />
                <div style="font-size: 10px; color: #31708f; display: @if($k > 0) none @endif">单图的图片高度不限定，高度根据原图比例自动调整。</div>
            </div>
        </div>
        @endforeach

        <div class="layui-form-item">
            <label class="layui-form-label required">魔方名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" lay-reqtext="魔方名称不能为空" placeholder="请输入魔方名称" value="{{$info->name ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">魔方图片</label>
            <div class="layui-input-block">
                <div id="picList">
                    @if(isset($info->data))
                    @foreach($info->data as $i => $pic)
                    <div class="picInfo">
                        <div class="picInfo-left">
                            <div class="layui-btn attrPicDiv">选择图片<input type="file" class="attrPic"/></div>
                            <div class="showAttrImage"><img src="{{$domain}}/{{$pic["pic"]}}" style="width: 150px; height: 150px;"></div>
                            <input class="goods-info-pic" type="hidden" name="data[{{$i}}][pic]" value="{{$pic["pic"]}}">
                        </div>
                        <div class="picInfo-small-url">
                            <input type="text" name="data[{{$i}}][route]" value="{{$pic["route"]}}" class="layui-input layui-disabled routeClass" disabled />
                            <input type="hidden" name="data[{{$i}}][open_type]" value="{{$pic["open_type"]}}" class="layui-input layui-disabled open_typeClass" disabled />
                            <a href="javascript:void(0)" class="share-span">选择链接</a>
                        </div>
                        <div class="picInfo-button">
                            <a href="javascript:void(0)" class="layui-btn layui-btn-danger">删除</a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="addPicInfo">
                    <a href="javascript:void(0)" class="layui-btn">新增</a>
                </div>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">魔方样式</label>
            <div class="layui-input-block" id="style"></div>
        </div>

        <div class="hr-line"></div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" id="saveBtn" lay-submit lay-filter="saveBtn">保存</button>
            </div>
        </div>

    </div>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['iconPickerFa', 'form', 'layer', 'upload'], function () {
            var iconPickerFa = layui.iconPickerFa,
                form = layui.form,
                layer = layui.layer,
                upload = layui.upload,
                $ = layui.$;
            var styleObj = eval('<?php echo json_encode($styleArr);?>')
            var oldStyle = "{{$info->style ?? 0}}";
            var appletObj = eval('<?php echo json_encode($applet);?>');
            setAddPicInfoCss();

            // 动态选择链接
            var layerIndex;
            var shareSpanThis;
            $(document).on("click", ".share-span", function () {
                shareSpanThis = $(this);
                let appletSelectDiv = '<div class="layui-form-item">';
                appletSelectDiv += '<label class="layui-form-label">可选链接</label>';
                appletSelectDiv += '<div class="layui-input-block">';
                appletSelectDiv += '<select lay-filter="appletSelect">';
                appletSelectDiv += '<option value="0">请选择链接</option>';
                for(let k in appletObj){
                    appletSelectDiv += '<option value="'+appletObj[k].id+'">'+appletObj[k].name+'</option>';
                }
                appletSelectDiv += '</select>';
                appletSelectDiv += '</div>';
                appletSelectDiv += '</div>';
                $("#appletList .layui-form .appletInfo").html(appletSelectDiv);
                $("#appletList .layui-form .appletParams").html("");
                form.render();

                layerIndex = layer.open({
                    title: '选择链接',
                    type: 1,
                    shade: 0.2,
                    maxmin:true,
                    skin:'layui-layer-lan',
                    shadeClose: true,
                    area: ['90%', '80%'],
                    content: $("#appletList"),
                });
            })

            //动态监听 选择链接 提交
            form.on('submit(appletBtn)', function(data){
                $("#appletBtn").addClass("layui-btn-disabled");
                $("#appletBtn").attr('disabled', 'disabled');
                let field = data.field;
                let open_type = "";
                if(field.hasOwnProperty("open_type")) open_type = field.open_type;
                let route = "";
                if(field.hasOwnProperty("route")) route = field.route;
                if(open_type == "navigate" || open_type == 'wxapp' || open_type == 'tel'){
                    route = "/" + route;
                    $.each(field, function (i, v){
                        if(i != "open_type" && i != "route"){
                            if(route.indexOf("?")!=-1){
                                route += "&" + i + "=" + v;
                            }else{
                                route += "?" + i + "=" + v;
                            }
                        }
                    })
                }

                shareSpanThis.parents(".picInfo-small-url").find(".routeClass").val(route);
                shareSpanThis.parents(".picInfo-small-url").find(".open_typeClass").val(open_type);

                $("#appletBtn").removeClass("layui-btn-disabled");
                $("#appletBtn").removeAttr('disabled');
                layer.close(layerIndex);
            });

            // 动态获取下拉框
            form.on("select(appletSelect)", function (data) {
                let params;
                let id = data.value;
                $("#appletList .layui-form .appletParams").html("");
                form.render();
                for (let i in appletObj){
                    if(id == appletObj[i].id){
                        let info = "";
                        info += '<div class="layui-form-item">';
                        info += '<label class="layui-form-label">打开方式</label>';
                        info += '<div class="layui-input-block">';
                        info += '<input type="text" name="open_type" value="'+appletObj[i].open_type+'" class="layui-input layui-disabled" disabled />';
                        info += '</div>';
                        info += '</div>';

                        info += '<div class="layui-form-item">';
                        info += '<label class="layui-form-label">跳转地址</label>';
                        info += '<div class="layui-input-block">';
                        info += '<input type="text" name="route" value="'+appletObj[i].route+'" class="layui-input layui-disabled" disabled />';
                        info += '</div>';
                        info += '</div>';

                        for(let k in appletObj[i].params){
                            info += '<div class="layui-form-item">';
                            info += '<label class="layui-form-label">'+appletObj[i].params[k].name+'</label>';
                            info += '<div class="layui-input-block">';
                            if(appletObj[i].params[k].is_value == 1){
                                info += '<input type="text" name="'+appletObj[i].params[k].name+'" value="'+appletObj[i].params[k].value+'" class="layui-input" />';
                            }else{
                                info += '<input type="text" name="'+appletObj[i].params[k].name+'" value="'+appletObj[i].params[k].value+'" class="layui-input layui-disabled" disabled />';
                            }
                            if(appletObj[i].params[k].desc.length > 0) {
                                info += '<div style="font-size: 10px; color: #636c72;">' + appletObj[i].params[k].desc + '</div>';
                            }
                            info += '</div>';
                            info += '</div>';
                        }

                        $("#appletList .layui-form .appletParams").html(info);
                        form.render();
                        break;
                    }
                }
            })

            // 动态删除
            $(document).on("click", ".picInfo-button", function () {
                $(this).parents(".picInfo").remove();
                setAddPicInfoCss();
            })

            function s4(){
                return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
            }
            function guid() {
                return (s4()+s4()+"-"+s4()+"-"+s4()+"-"+s4()+"-"+s4()+s4()+s4());
            }

            // 动态新增
            $(document).on("click", ".addPicInfo a", function () {
                if($(".picInfo").length >= 4){
                    layer.msg("最多只能上传4张图片",{icon: 2});
                    return false;
                }
                let picInfoLen = guid();
                let picInfoDiv = '<div class="picInfo">';

                picInfoDiv += '<div class="picInfo-left">';
                picInfoDiv += '<div class="layui-btn attrPicDiv">选择图片<input type="file" class="attrPic"/></div>';
                picInfoDiv += '<div class="showAttrImage"><img src="" style="width: 150px; height: 150px;"></div>';
                picInfoDiv += '<input class="goods-info-pic" type="hidden" name="data['+picInfoLen+'][pic]" value="">';
                picInfoDiv += '</div>';

                picInfoDiv += '<div class="picInfo-small-url">';
                picInfoDiv += '<input type="text" name="data['+picInfoLen+'][route]" value="" class="layui-input layui-disabled routeClass" disabled />';
                picInfoDiv += '<input type="hidden" name="data['+picInfoLen+'][open_type]" value="" class="layui-input layui-disabled open_typeClass" disabled />';
                picInfoDiv += '<a href="javascript:void(0)" class="share-span">选择链接</a>';
                picInfoDiv += '</div>';

                picInfoDiv += '<div class="picInfo-button">';
                picInfoDiv += '<a href="javascript:void(0)" class="layui-btn layui-btn-danger">删除</a>';
                picInfoDiv += '</div>';

                picInfoDiv += "</div>";
                $("#picList").append(picInfoDiv);
                setAddPicInfoCss();
            })
            function setAddPicInfoCss() {
                let picInfoLeng = $(".picInfo").length;
                if(picInfoLeng > 3){
                    $(".addPicInfo").css("display", "none");
                }else{
                    $(".addPicInfo").css("display", "");
                }

                let styleDiv = '';
                let newI = picInfoLeng >= 4 ? 2 : (picInfoLeng < 4 && picInfoLeng >= 2 ? 1 : 0)
                for(let i in styleObj){
                    if(i <= newI){
                        if(i == oldStyle) {
                            styleDiv += '<input type="radio" name="style" value="'+i+'" title="'+styleObj[i]+'" checked />'
                        }else{
                            styleDiv += '<input type="radio" name="style" value="'+i+'" title="'+styleObj[i]+'" />'
                        }
                    }
                }
                $("#style").html(styleDiv);
                form.render();
            }

            // 动态上传规格图片
            $(document).on("change", ".attrPic", function(){
                let _this = $(this);
                let fileData = new FormData();
                fileData.append("file", _this[0].files[0]);  // 或者document.getElementById("file")[0]
                fileData.append("name", _this.val());

                // 判断图片类型
                if(!/image\/\w+/.test(_this[0].files[0].type)){
                    layer.msg("请选择图片",{icon: 2});
                    return false;
                }
                // 判断图片大小
                if(_this[0].files[0].size > 1000*1024){
                    layer.msg("图片不能超过1000KB",{icon: 2});
                    return false;
                }
                $.ajax({
                    url:'/admin/upload/upload',
                    type:'post',
                    data:fileData,
                    contentType:false,
                    processData:false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(res){
                        console.log(res);
                        if(res.code==0){
                            var domain = window.location.host;
                            _this.parents(".attrPicDiv").nextAll(".showAttrImage").find("img").attr("src", "http://" + domain + "/" + res.data[0]);
                            _this.parents(".attrPicDiv").nextAll(".goods-info-pic").val(res.data[0]);
                        }else{
                            layer.msg(res.message,{icon: 2});
                            _this.parents(".attrPicDiv").nextAll(".showAttrImage").html('');
                            _this.parents(".attrPicDiv").nextAll(".goods-info-pic").val('');
                        }
                    },
                    error:function (data) {
                        layer.msg(res.message,{icon: 2});
                        _this.val("");
                    }
                });
            })

            //监听提交
            form.on('submit(saveBtn)', function(data){
                $("#saveBtn").addClass("layui-btn-disabled");
                $("#saveBtn").attr('disabled', 'disabled');
                $.ajax({
                    url:'/admin/cube/edit',
                    type:'post',
                    data:data.field,
                    dataType:'JSON',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(res){
                        if(res.code==0){
                            layer.msg(res.message,{icon: 1},function (){
                                parent.location.reload();
                            });
                        }else{
                            layer.msg(res.message,{icon: 2});
                            $("#saveBtn").removeClass("layui-btn-disabled");
                            $("#saveBtn").removeAttr('disabled');
                        }
                    },
                    error:function (data) {
                        layer.msg(res.message,{icon: 2});
                        $("#saveBtn").removeClass("layui-btn-disabled");
                        $("#saveBtn").removeAttr('disabled');
                    }
                });
            });
        });
    </script>
@endsection