<?php

/**

 * Created by PhpStorm.

 * User: 林虎

 * Date: 2018/7/12

 * Time: 16:48

 */



namespace app\login\controller;



use app\validate\AccountValidate;

use app\model\Admin;

use think\Controller;

use app\libException\service\ParamException;
use think\Cookie;
use think\Session;
use app\base\controller\Base;
use app\base\controller\Wechat;
use LoginEncrypt\Ssecretary;//加密解密
/**
*model
*/
use app\model\Error_record as Error_record_model;
use app\model\Admin as Admin_model;
class Login extends Wechat

{


      public function wxlogin(){
        // // echo urlencode('https://qqcardcs.10088.cn/admin/cs/index');die;
        $param = request()->param();
        if(!empty($param)){
          if($param['state'] != Session::get('wxlogin_state')) //csrf验证
            {
                return '非法操作';
            }      
            $bGetUserToken_res = $this->bGetUserToken($param['code'],config('appid'),config('secret'));
            if(!empty($bGetUserToken_res['errcode'])){
              $this->cInsertErr($bGetUserToken_res,'code请求token');exit;//写入错误信息
            }
            $Admin_model = new Admin_model();
            $Ssecretary = new Ssecretary();
            $bGetUserInfo_res = $this->bGetUserInfo($bGetUserToken_res);
            $mGetAdminOne_res = $Admin_model->mGetAdminOne(['unionid'=>$bGetUserInfo_res['unionid']]);
            if(!empty($mGetAdminOne_res)){
              if(!empty($bGetUserInfo_res['errcode'])){
                $this->cInsertErr($bGetUserInfo_res,'请求用户数据');//写入错误信息
              }
              $res = (new Admin_model())->mInsertAdmin($bGetUserInfo_res); //插入用户信息 
              Cookie::set('openid',$Ssecretary->encrypt($bGetUserInfo_res['openid'],'wuye.10088.cn'),['prefix'=>'wy_','expire'=>7200]);
              $mSaveTime_res = $Admin_model->mSaveTime(['unionid'=>$bGetUserInfo_res['unionid']],['last_time'=>date('Y-m-d H:i:s')]);
              if(!$res){
                $data['errmsg'] = json_encode($bGetUserInfo_res);
                $this->cInsertErr($data,'插入用户信息失败');;//写入错误信息
              }  
              return $this->redirect('index/index/index');            
            }else{
              var_dump('抱歉您的微信未绑定任何账户');
            }           
        }

      }

      public function cInsertErr($data,$contmsg=''){
          $data['path_info'] = $_SERVER['PATH_INFO'];
          $data['create_time'] = date('Y-m-d H:i:s');
          $data['contmsg'] = $contmsg;
          return (new Error_record_model())->mInsertErrRecord($data);        
      }

      public function index()
      {
        if(empty(Cookie::get('wy_openid'))){
          $wxlogin_state = md5( explode(' ',microtime())[1].'wuye.10088.cn' );
          Session::set('wxlogin_state',$wxlogin_state); //csrf验证
            return $this->assign([
              'wxlogin_state'=>$wxlogin_state,
            ])->fetch();            
        }else{
          return $this->redirect('index/index/index');
        }
      }

}