<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:11
 */

namespace app\admin\controller;

use think\Config;

class Users extends Base
{
    /**
     * 会员列表
     */
    public function user()
    {
        $search_key = input();
        unset($search_key['/admin/users/user_html']);

        $list = model('Users') -> resList($search_key);
        unset($search_key['page']);
        if(!$search_key){
            $search_key = null;
        }

        $this -> assign('search_key', $search_key);
        $this -> assign('list', $list);
        return $this -> fetch();
    }

    /**
     * 会员编辑
     */
    public function user_form()
    {
        if($this->request->isAjax()){
            $data = input('post.');

            $result = model('Users')->operation($data);
            if(!$result['code']){
                return $this->error($result['msg'], '', ['token' => $this->request->token()]);
            }else{
                return $this->success($result['msg'], url('users/user'), ['token' => $this->request->token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('Users')->find($id);
            }

            $this -> assign('res', $res);
            $this -> assign('id', $id);
            return $this -> fetch();
        }
    }

    /**
     * 会员删除
     */
    public function user_del()
    {
        if($this->request->isAjax()){
            $data = input('post.');

            $res = model('Users')->del($data);
            if($res['code']){
                return $this->success($res['msg'], url('Users/user'), ['token' => $this->request->token()]);
            }else{
                return $this->error($res['msg'], '', ['token' => $this->request->token()]);
            }
        }else{
            return $this->error('非法请求', url('index/index'));
        }
    }

    /**
     * 会员状态修改
     */
    public function user_status()
    {
        if($this->request->isAjax()){
            $data = input('post.');

            $result = model('Users')->editStatus($data);
            if(!$result['code']){
                return $this->error($result['msg'], '', ['token' => $this->request->token()]);
            }else{
                return $this->success($result['msg'], url('users/user'), ['token' => $this->request->token(), 'status' => $result['data']]);
            }
        }else{
            return $this->error('非法请求', url('index/index'));
        }
    }

}