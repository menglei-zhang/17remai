<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/4
 * Time: 18:35
 */

namespace app\index\model;

use think\Model;
use app\index\validate\Users as Vali;
use app\index\model\Users;
use app\index\model\UserLevel;
use think\facade\Cache;
use app\index\model\BonusRecord;
use app\index\model\Order;
use app\component\controller\Ddysms;
use app\index\model\ServiceStore;
use app\index\controller\Wx;
use app\index\model\Record;
use app\index\model\Newpeople;

class Users extends Model
{
    /**
     * 创建用户，获取token
     */
    public function form($info)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('auth')->check($info)) {
            return echoArr(500, $validate->getError());
        }
        $temp['token'] = encryption($info['session_key'] . $info['openid']);
        $temp['wx_open_id'] = $info['openid'];
        // 判断用户是否存在
        $isUser = $this->where('wx_open_id', $info['openid'])->value('id');
        
        // 存放缓存
        Cache::set($temp['token'], $temp['wx_open_id'], 21600);
      
        // 不存在 则 存储token
        if (!$isUser) {
            return echoArr(800, '请先登录', ['token' => $temp['token']]);
        }

        return echoArr(200, '授权成功', ['token' => $temp['token']]);
    }
  

    /**
    * 完善用户信息
    */
    public function editUserInfo($data)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('userInfo')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        $data['user_info'] = json_decode($data['userInfo'], true);
        $temp['head_pic'] = $data['user_info']['avatarUrl'];
        $temp['sex'] = $data['user_info']['gender'];
        $temp['username'] = $data['user_info']['nickName'];
        // 微信配置信息
        $config['iv'] = $data['iv'];
        $config['encryptedData'] = $data['encryptedData'];
        $config['signature'] = $data['signature'];
        $temp['config'] = serialize($config);

        $temp['id'] = $data['user_id'];

        $result = $this->isUpdate(true)->save($temp);
        if(false === $result){
            return echoArr(500, '操作失败');
        }
        return echoArr(200, '操作成功');
    }

    /**
     * 发送短信验证码
     */
    public function sendCode($data)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('code')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        // 短信验证码
        $sms = new Ddysms();
        $sms->receive_phone = $data['telephone'];
        $res = $sms->aliSms();

        return $res;
    }

    /**
     * 切换账号
     */
    public function switchAccount($data)
    {
        // 验证
        if (!isset($data['telephone'])) {
            return echoArr(500, '没有该子账号');
        }
        if (!$data['telephone']) {
            return echoArr(500, '没有该子账号');
        }

        // 主账号切换子账号
        // 判断是否是当前主账号的子账号
        $where1 = [
            ['mobile', 'eq', $data['telephone']],
            ['pid', 'eq', $data['user_id']],
        ];
        $query = $this->where($where1);

        // 判断是否是当前子账号的主账号
        $pid = $this->where('id', $data['user_id'])->value('pid');
        if ($pid) {
            $where2 = [
                ['mobile', 'eq', $data['telephone']],
                ['id', 'eq', $pid],
            ];

            $query->whereOr($where2);
        }
        $isAccoutn = $query->field('id,open_id')->find();

        if ($isAccoutn) {
            // 生成token
            $openId = $this->where('id', $data['user_id'])->value('open_id');
            $token = encryption(uniqid() . $openId);

            // 存放缓存
            Cache::set($token, $isAccoutn['open_id'], 21600);

            return echoArr(200, '请求成功', ['token' => $token]);
        } else {
            return echoArr(500, '没有该子账号');
        }
    }

    /**
     * 用户基本信息
     */
    public function details($data, $domain)
    {
        $info = $this
            ->field('id,username as name,mobile as account,head_pic as avatar')
            ->order('id asc')
            ->select();

        return echoArr(200, '请求成功');
    }

    /**
     * 录入实名认证信息
     */
    public function certification($data)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('certification')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        // 完善信息
        $temp['id'] = $data['user_id'];
        unset($data['user_id']);
        $temp['real_name'] = serialize($data);

        $result = $this->allowField(true)->isUpdate(true)->save($temp);
        if (false === $result) {
            return echoArr(500, '请求失败');
        }

        return echoArr(200, '请求成功');
    }



    /**
     * 新人红包
     */
    public function redbag($data)
    {
        // 判断是否为领取还是查询
        if(!isset($data['receive'])){
            return echoArr(500, '参数错误');
        }

        // 领取红包
        $users = new Users();
        $record = new Record();
        $Newpeople = new Newpeople();

        // 新人红包
        $nId = 1;

        // 判断红包是否领取
        $status = 0;
        $result = $record -> where('user_id', $data['user_id']) -> where('nid', $nId) -> value('id');
        if($result){
            $status = 1;
        }

        // 此次请求为查询，则返回结果
        if(!$data['receive']){
            return echoArr(200, '请求成功', ['status' => $status, 'userId' => $data['user_id']]);
        }

        // 领取红包时，判断红包是否已被领取
        if($result){
            return echoArr(500, '红包已领取');
        }

        // 开启事务
      	$users -> startTrans();
        $record -> startTrans();
        $Newpeople -> startTrans();

        // 判断新人红包是否开启
        $bag = $Newpeople -> where('id', $nId) -> where('status', 1) -> find();
        if(!$bag){
            return echoArr(500, '此活动已结束');
        }

        // 新人红包数量是否足够
        if($bag['num'] == 0){
            return echoArr(500, '红包数量不足');
        }
		
      	// 用户余额变动 
		$user = $users->where('id', $data['user_id'])->field('user_money')->find();
      	$user['user_money'] += $bag['money'];
      	
      	$req = $user->allowField(true)->isUpdate(true)->save($user);
      	if(false === $req){
            $users -> rollback();
            return echoArr(500, '抱歉，此活动正在维护');
        }
      
        // 红包数据
        $temp = [
            'nid' => $nId,
            'trade_type' => $bag['name'],
            'user_money' => $bag['money'],
            'user_id' => $data['user_id'],
            'add_time' => time(),
        ];

        // 用户领取红包
        $result = $record -> isUpdate(false) -> save($temp);
        if(false === $result){
            $record -> rollback();
            return echoArr(500, '抱歉，此活动正在维护');
        }
      

        // 新人礼券数量变动
        $temp = [
            'id' => $nId,
            'num' => $bag['num'] - 1
        ];
        $result = $Newpeople -> isUpdate(true) -> save($temp);
        if(false === $result){
            $record -> rollback();
            $Newpeople -> rollback();

            return echoArr(500, '抱歉，此活动正在维护');
        }

      	$users -> commit();
        $record -> commit();
        $Newpeople -> commit();

        return echoArr(200, '领取成功');
    }
  
    //更改用户手机号
    public  function upMobil($mobile,$openid)
    {
      	// 清除之前账号的open_id
      	$result = $this -> where('wx_open_id', $openid) -> update(['wx_open_id' => '']);
      
        $res = [
          'mobile'=> $mobile,
          'wx_open_id' => $openid
        ];
      	$id = $this -> where('mobile', $mobile) -> value('id');
      	if($id) $res['id'] = $id;
      	$action = $id ? true : false;
      
        $data = $this -> isUpdate($action) -> save($res);
      	
        return $data;
    }

    public  function getUserFind($openid)
    {
         $data = $this->where('wx_open_id', $openid)->field('username as uName ,head_pic as uAvatar,mobile as uPhone')->find()->toArray();
         return $data;
    }

    public function getUserId ($openid)
    {
        $id = $this->where('wx_open_id', $openid)->field('id')->find()->toArray();
        return $id ;
    }
}