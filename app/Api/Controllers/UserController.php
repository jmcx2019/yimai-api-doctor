<?php
/**
 * Created by PhpStorm.
 * User: lyx
 * Date: 16/4/21
 * Time: 上午9:39
 */

namespace App\Api\Controllers;

use App\Api\Requests\UserRequest;
use App\Api\Transformers\UserTransformer;
use App\Hospital;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Validator;
use JWTAuth;

class UserController extends BaseController
{
    public function update(UserRequest $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['error' => 'token_absent'], $e->getStatusCode());
        }

        if (isset($request['name']) && !empty($request['name'])) {
            $user->name = $request['name'];
        }
        if (isset($request['sex']) && !empty($request['sex'])) {
            // 1:男; 0:女
            $user->gender = $request['sex'];
        }
        if (isset($request['province']) && !empty($request['province'])) {
            $user->province_id = $request['province'];
        }
        if (isset($request['city']) && !empty($request['city'])) {
            $user->city_id = $request['city'];
        }
        if (isset($request['hospital']) && !empty($request['hospital'])) {
            $hospitalId = $request['hospital'];
            if (!is_numeric($request['hospital'])) {
                $hospitalId = $this->createNewHospital($request)->id;
            }
            $user->hospital_id = $hospitalId;
        }
        if (isset($request['department']) && !empty($request['department'])) {
            $user->dept_id = $request['department'];
        }
        if (isset($request['job_title']) && !empty($request['job_title'])) {
            $user->title = $request['job_title'];
        }
        if (isset($request['college']) && !empty($request['college'])) {
            $user->college_id = $request['college'];
        }
        if (isset($request['ID_number']) && !empty($request['ID_number'])) {
            $user->id_num = $request['ID_number'];
        }
        if (isset($request['tags']) && !empty($request['tags'])) {
            $user->tag_id_list = $request['tags'];
        }
        if (isset($request['personal_introduction']) && !empty($request['personal_introduction'])) {
            $user->profile = $request['personal_introduction'];
        }

        try {
            if ($user->save()) {
                return $this->response->item($user, new UserTransformer());
            } else {
                return $this->response->error('unknown error', 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /**
     * Create new hospital.
     * 
     * @param $request
     * @return string|static
     */
    public function createNewHospital($request)
    {
        //TODO: 缺少一个验证，需要查一下是否该省市下是否已经有该医院，如果有则返回已有医院的ID
        $data = [
            'province' => $request['province'],
            'city' => $request['city'],
            'name' => $request['hospital'],
            'status' => '未核实'
        ];

        try {
            return Hospital::create($data);
        } catch (\Exception $e) {
            return '';
        }
    }
}
