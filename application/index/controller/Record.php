<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/18
 * Time: 11:11
 */  
namespace app\index\controller;

class Record extends Base
{
	public function index()
	{
		$data['uId'] = $this->userId;
		
		$result = model('Record')->resList($data);
      
        return json($result);
	}
}