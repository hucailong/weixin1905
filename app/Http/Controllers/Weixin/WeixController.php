<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeixController extends Controller
{

    /**
     * 接入微信服务器
     */
    public function wechat()
    {
        $token = '20010506h20011104zysys9999';       //开发提前设置好的 token
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){        //验证通过
            echo $echostr;
        }else{
            die("not ok");
        }
    }

    /**
     * 获取服务器推送
     */
    public function send(){
        //获取access_token 写入日志
        $log_filename = 'wx.log';
        $xml = file_get_contents("php://input");
//        $xml = json_encode($_POST);
        $data = date('Y-m-d H:i:s').$xml;
        file_put_contents($log_filename,$data);
//        echo 111;
    }

//    public function getUserInfo(){
//        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'';
//    }

}
