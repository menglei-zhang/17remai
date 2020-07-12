<?php
    namespace app\index\controller;
	use think\Cache;
	use think\Controller;
    class Wm
    {
        // 获取access_token
        public function getCode()
        {
            $client_id = "98880D71FFF77925E491E222F3790949";
            $client_secret = "BE3178FF0DA31430963B932B8C6985B3";
            $redirect_uri = "http://bread.mimanduo.xyz/index.php/index/Wm/getCode";
            $code=request()->param('code');
            $url = "https://dopen.weimob.com/fuwu/b/oauth2/token?code={$code}&grant_type=authorization_code&client_id={$client_id}&client_secret={$client_secret}&redirect_uri={$redirect_uri}";
            $response = json_decode(sendCURL($url,true), true);
			cache('WmToken', $response['access_token'], 7200);
			cache('refresh_token', $response['refresh_token']);
        }
        // 通过授权获取的refresh_token，可用来刷新access token
        public function refreshToken()
        {
            $client_id = "98880D71FFF77925E491E222F3790949";
            $client_secret ="BE3178FF0DA31430963B932B8C6985B3";
            $refresh_token = cache('refresh_token');
            $url = "https://dopen.weimob.com/fuwu/b/oauth2/token?grant_type=refresh_token&client_id={$client_id}&client_secret={$client_secret}&refresh_token={$refresh_token}";
            $response = json_decode(sendCURL($url,true), true);
			cache('WmToken', $response['access_token'], 7200);
            return "成功";
        }
    }

