<?php
/**
 * Created by PhpStorm.
 * User: lyx
 * Date: 16/4/20
 * Time: 下午2:27
 */

namespace App\Api\Requests;

use App\Http\Requests\Request;

/**
 * Class HospitalRequest
 * @package App\Api\Requests
 */
class HospitalRequest extends Request
{
    /**
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
        return [
            'inviter' => 'required|digits_between:8,8|exists:app_doctors,dp_code'
        ];
    }

    
    //TODO 啥都没做还
    
    /**
     * @return array
     */
    public function messages()
    {
        return [
            'required' => ':attribute不能为空',
            'digits_between' => ':attribute必须为:min位长的数字',
            'exists' => '无法识别邀请人'
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'inviter' => '邀请码'
        ];
    }

    /**
     * @param array $errors
     * @return mixed
     */
    public function response(array $errors)
    {
        return response()->json(['message' => current($errors)[0]], 403);
    }
}
