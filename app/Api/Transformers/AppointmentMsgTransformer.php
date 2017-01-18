<?php
/**
 * Created by PhpStorm.
 * User: lyx
 * Date: 16/4/18
 * Time: 下午4:08
 */

namespace App\Api\Transformers;

class AppointmentMsgTransformer
{
    /**
     * @param $appointmentMsg
     * @return array
     */
    public static function transformerMsgList($appointmentMsg)
    {
        return [
            'id' => $appointmentMsg['id'],
            'appointment_id' => $appointmentMsg['appointment_id'],
            'text' => self::generateContent($appointmentMsg),
            'type' => $appointmentMsg['type'],
            'read' => $appointmentMsg['locums_read'],
            'time' => $appointmentMsg['created_at']->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @param $data
     * @return bool|string
     */
    public static function generateContent($data)
    {
        switch ($data->status) {
            /**
             * wait:
             * wait-0: 待代约医生确认
             * wait-1: 待患者付款
             * wait-2: 患者已付款，待医生确认
             * wait-3: 医生确认接诊，待面诊
             * wait-4: 医生改期，待患者确认
             * wait-5: 患者确认改期，待面诊
             */
            case 'wait-0':
                $retText = '患者' . $data['patient_name'] . '请求您代约';
                break;

            case 'wait-1':
                $retText = '您替' . $data['patient_name'] . '约诊' . $data['doctor_name'] . '医生的信息已发送至' . $data['patient_name'] . '，等待确认及支付。若12小时内未完成支付则约诊失效。';
                break;

            case 'wait-2':
                $retText = '患者' . $data['patient_name'] . '已付款。';
                break;

            case 'wait-3':
                break;

            case 'wait-4':
                break;

            case 'wait-5':
                break;

            /**
             * close:
             * close-1: 待患者付款
             * close-2: 医生过期未接诊,约诊关闭
             * close-3: 医生拒绝接诊
             */
            case 'close-1':
                break;

            case 'close-2':
                break;

            case 'close-3':
                $retText = '医生' . $data['doctor_name'] . '拒绝了接诊。';
                break;

            /**
             * cancel:
             * cancel-1: 患者取消约诊; 未付款
             * cancel-2: 医生取消约诊
             * cancel-3: 患者取消约诊; 已付款后
             * cancel-4: 医生改期之后,医生取消约诊;
             * cancel-5: 医生改期之后,患者取消约诊;
             * cancel-6: 医生改期之后,患者确认之后,患者取消约诊;
             * cancel-7: 医生改期之后,患者确认之后,医生取消约诊;
             */
            case 'cancel-2':
            case 'cancel-4':
            case 'cancel-7':
                $retText = '医生' . $data['doctor_name'] . '取消了约诊请求。';
                break;

            case 'cancel-1':
            case 'cancel-3':
            case 'cancel-5':
            case 'cancel-6':
                $retText = '患者' . $data['patient_name'] . '取消了约诊请求。';
                break;

            /**
             * completed:
             * completed-1:最简正常流程
             * completed-2:改期后完成
             */
            case 'completed-1':
                break;

            case 'completed-2':
                break;

            default:
                $retText = false;
                break;
        }

        return $retText;
    }
}
