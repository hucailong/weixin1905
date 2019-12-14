<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WeixController extends Controller
{

    protected $access_token;

    //获取access_token
    public function __construct()
    {
        $this ->access_token = $this->getAccessToken();
    }

     protected function getAccessToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'';
        $data_json = file_get_contents($url);
        file_put_contents('text.log',$data_json);
        $arr = json_decode($data_json,true);
        return $arr['access_token'];
    }
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
        $xml_str = file_get_contents("php://input");
        $data = date('Y-m-d H:i:s')."\n".$xml_str;
        file_put_contents($log_filename,$data,8);



        //处理xml数据
        $xml_obj = simplexml_load_string($xml_str);
        $event = $xml_obj ->Event;



        //扫描码获取用户信息
        if($event=='subscribe'){
            $openid = $xml_obj ->FromUserName;
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'';
            $user_info = file_get_contents($url);
            file_put_contents('wx_user.log',$user_info,8);
        }


//        确认消息类型
        $msg_type = $xml_obj->MsgType;
        $form_user = $xml_obj->ToUserName;
        $touser = $xml_obj->FromUserName;
        $createtime = time();

        //被动回复
        if ($msg_type == 'text'){
            $content = date('Y-m-d H:i:s').$xml_obj->Content;
            $response_text = '<xml>
<ToUserName><![CDATA['.$touser.']]></ToUserName>
<FromUserName><![CDATA['.$form_user.']]></FromUserName>
<CreateTime>'.$createtime.'</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA['.$content.']]></Content>
</xml>';
            echo $response_text;
        }


    }

    public function getUserInfo($access_token,$openid){
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'';
        //发送网络请求
        $pull = file_get_contents($url);
        //日志写入
        $logname = 'wx_user.log';
        file_put_contents($logname,$pull,8);
    }

}
