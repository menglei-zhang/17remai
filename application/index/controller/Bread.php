<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */  
namespace app\index\controller;

class Bread extends Base
{
	public function index()
	{
        if($this->request->isPost()){
          	$data = input('post.');
			$data['uId'] = $this->userId;
          	$result = model('Bread')->resList($data);

          	return json($result);
        }else{
          	return  json(echoArr(500, '非法请求'));
        }
	}
}