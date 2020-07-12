<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/7
 * Time: 9:55
 */

namespace app\admin\controller;

use app\admin\validate\Banner as Vali;

class Banner extends Base
{
	/**
	 * @return banner图列表
	 */
	public function banner()
	{
		$data = model('Banner')->order('id ASC')->paginate(10);

		$this->assign('list', $data);
		return $this->fetch();
	}

    

	/**
	 * @return banner图添加/修改
	 */
	public function banner_form()
	{
		if($this->request->isAjax()){
			$data = input('post.');
			
			$result = model('Banner')->editBanner($data);
            if($result['code'] == 1){
                return $this->success($result['msg'], url('banner/banner'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], ['token' => $this->request->token()]);
            }
		}else{
			$id = input('id', 0);
			$res = null;
			if($id){
				$res = model('Banner')->resList($id);
			}

			$this->assign('id', $id);
			$this->assign('res', $res);
			return $this->fetch();
		}
	}


	/**
     * @return banner图删除
     */
    public function banner_del()
    {
    	if($this->request->isAjax()){
	        $data = input('post.');
	        $result = model('Banner')->whereIn('id', $data['id'])->delete();
	        if($result){
	            return echoArr(1, '删除成功');
	        }else{
	            return echoArr(0, '删除失败', $result->getError());
	        }
	    }else{
            return $this -> error('非法请求');
        }
    }


    /**
     * @return banner图状态修改
     */
    public function banner_status()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Banner')->editStatus($data);
            if($result['code'] == 1){
                return $this->success($result['msg'], url('banner/banner'), ['token' => $this->request->token()]);
            }else{
                return $this->error($result['msg'], '', ['token' => $this->request->token()]);
            }
        }else{
            return $this->error('非法请求');
        }
    }


	/**
     * @return banner图片上传
     */
    public function goods_upload()
    {
        $file = input('file.img');
        $num = 1;

        if (!$file) {
            $num = 0;
            $file = input('file.file');
        }

        $msg = $this->upload($file);
        if (!$msg['code']) {
            return $this->error($msg['msg']);
        } else {
            return echoArr($num, $msg['msg'], ['img' => $msg['data']['img'], 'src' => config('imgRoute') . $msg['data']['img']]);
        }
    }


    /**
     * @return 商城设置列表
     */
    public function config()
    {
        $list = model('Config')->paginate(10);
        $domain = $this->request->domain() . config('imgRoute');

        $this->assign('list', $list);
        $this->assign('domain', $domain);
        return $this->fetch();
    }


    /**
     * @return 配置修改
     */
    public function config_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');
            $result = model('Config')->operation($data);
            if(!$result['code']){
                return $this->error($result['msg'], '', ['token' => $this->request->token()]);
            }else{
                return $this->success($result['msg'], url('Banner/config', ['type' => $result['data']['type']]), ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id', 0);
            $result = model('Config')->find($id);

            $this->assign('result', $result);
            return $this->fetch();
        }
    }


    /**
     * @return 网站logo上传
     */
    public function config_upload()
    {
        $file = request()->file('img');

        $data = input('post.');
        $msg = $this -> upload($file);
        if(!$msg['code']){
            return $this -> error($msg['msg'], '', ['token' => $this -> request -> token()]);
        }else{
            $data['value'] = $msg['data']['img'];
            $result = model('Config') -> operation($data);

            if(!$result['code']){
                return $this -> error($result['msg'], url('MallConfig/config'), ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], '', ['img' => $data['value'], 'token' => $this -> request -> token()]);
            }
        }
    }

}