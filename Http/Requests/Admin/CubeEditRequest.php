<?php

namespace Modules\Cube\Http\Requests\Admin;

use Modules\Cube\Http\Requests\BaseRequest;

class CubeEditRequest extends BaseRequest
{
    /**
     * 判断用户是否有请求权限
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * 获取规则
     * @return string[]
     */
    public function newRules()
    {
        return [
            'id' => 'nullable|integer|min:1',
            'name' => 'required|string|min:1|max:100',
            'style' => 'required|integer|min:0|max:2',
            'data' => 'required|array',
        ];
    }

    /**
     * 获取自定义验证规则的错误消息
     * @return array
     */
    public function messages()
    {
        return [
//            'phone.regex' => "请输入正确的 :attribute",
        ];
    }

    /**
     * 获取自定义参数别名
     * @return string[]
     */
    public function attributes()
    {
        return [
            "name" => "魔方名称",
            "style" => "魔方样式",
            "data" => "魔方图片",
        ];
    }

    /**
     * 验证规则
     */
    public function check()
    {
        $validator = \Validator::make($this->all(), $this->newRules(), $this->messages(), $this->attributes());
        $error = $validator->errors()->first();
        if($error){
            return $this->resultErrorAjax($error);
        }
    }
}
