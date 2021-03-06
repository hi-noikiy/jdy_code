<?php
/**
 * Created by PhpStorm.
 * User: caelyn
 * Date: 16/5/25
 * Time: 18:07
 */
namespace App\Http\Logics\Pay;

use App\Http\Dbs\OrderDb;
use App\Http\Logics\Logic;
use App\Http\Models\Common\NationalModel;
use App\Http\Models\Pay\RechargeModel;
use Session;

class JdOnlineLogic extends Logic{

    /**
     * 提交京东网银支付订单数据
     * @param $cash
     * @param $orderId
     * @param $bankCode
     * @param $from
     * @return array
     */
    public function submit($cash,$orderId,$bankCode,$from){
        $param = [
            'method'=>'encrypt',
            'driver'=>'JdOnline',
            'order_id'=>$orderId,
            'notify_url'=>NationalModel::createNoticeUrl('JdOnline'),
            'bank_code'=>$bankCode,
            'cash'=>$cash,
            'return_url'=>NationalModel::createReturnUrl('JdOnline',$from),
        ];
        $rechargeModel = new RechargeModel();
        $result = $rechargeModel->payService($param);
        return $result;
    }

    /**
     * 京东支付同步页面跳转处理方法
     * @return bool
     */
    public function toReturn($request,$from = ''){
        $param = [
            'method'=>'decrypt',
            'driver'=>'JdOnline',
        ];
        //Session::put('payCash',$request['v_amount']);
        $param = array_merge($param,$request);
        
        return \App\Http\Logics\Pay\RechargeLogic::returnDecrypt($param);
        /*
        $rechargeModel = new RechargeModel();
        $result = $rechargeModel->payService($param);

        $return = [
            'status' => false,
            'cash'  => 0
        ];

        if(isset($result['trade_status']) &&
            $result['trade_status'] == OrderDb::TRADE_SUCCESS){

            $return['status'] = true;

            $orderId = $result['order_id'];

            $orderInfo = OrderModel::getOrderInfo($orderId);

            if($orderInfo){

                $return['cash'] = $orderInfo['cash'];
            }
        }

        return $return;
        */
    }

    /**
     * 京东支付异步通知页面方法
     */
    public function toNotice($request){
        $param = [
            'method'=>'decrypt',
            'driver'=>'JdOnline',
        ];
        $param = array_merge($param,$request);
        $rechargeModel = new RechargeModel();
        $result = $rechargeModel->payService($param);
        //接收回调数据后的订单处理
        if($result['trade_status']==OrderDb::TRADE_SUCCESS){
            $orderDone = $rechargeModel->paySuccess($result['order_id'],$result['trade_no']);
        }else{
            $orderDone = $rechargeModel->payFail($result['order_id'],$result['msg']);
        }
        //通知状态
        if($orderDone)
            return 'ok';
        else
            return 'error';
    }


    /**
     * 查单
     * @param $orderId
     * @return array
     */
    public function search($orderId){
        $param = [
            'method'=>'search',
            'driver'=>'JdOnline',
            'no_order'=>$orderId,
        ];
        $rechargeModel = new RechargeModel();
        $result = $rechargeModel->payService($param);
        return $result;
    }


}