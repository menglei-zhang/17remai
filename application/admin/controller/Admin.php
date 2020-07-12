<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/9/7
 * Time: 9:55
 */

namespace app\admin\controller;

use think\paginator\driver\Bootstrap;

class Admin extends Base
{
    /**
     * 后台登录
     */
    public function login()
    {
        if($this->request->isAjax()){
            $data = $this->request->post();

            // 验证数据格式
            $result = $this->validate($data, 'AdminUser.login');
            if(true !== $result){
                // 验证失败 输出错误信息
                return $this->error($result, '', ['token' => $this->request->token()]);
            }

            $result = model('AdminUser')->login($data);
            if($result['code']){
                return $this->success($result['msg'], url('index/index'));
            }else{
                return $this->error($result['msg'], '', ['token' => $this->request->token()]);
            }
        }else{
            $login_name = model('Config')->where('key', 'admin_login_name')->value('value');
            
          	$this->assign('login_name', $login_name);
            return $this->fetch();
        }
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        session(null);
        return $this->success('退出登录', 'admin/login');
    }

    /**
     * 用户管理
     */
    public function user()
    {
        $search_key = input();
        unset($search_key['/admin/admin/user_html']);

        $list = model('AdminUser') -> resList($search_key);

        unset($search_key['page']);
        if(!$search_key){
            $search_key = null;
        }

        $this -> assign('list', $list);
        $this -> assign('search_key', $search_key);

        return $this -> fetch();
    }

    public function user_form(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $result = model('AdminUser') -> operation($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('admin/user'), ['token' => $this -> request -> token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('AdminUser') -> resFind($id);
            }

            // 查询所有角色
            $roles = model('AdminRole') -> order('id asc') -> select();

            $this -> assign('roles', $roles);
            $this -> assign('res', $res);
            $this -> assign('id', $id);
            return $this -> fetch();
        }
    }

    public function user_del(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $res = model('AdminUser') -> del($data);
            if($res['code']){
                return $this -> success($res['msg'], url('admin/user'), ['token' => $this -> request -> token()]);
            }else{
                return $this -> error($res['msg'], '', ['token' => $this -> request -> token()]);
            }
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    public function user_status(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $result = model('AdminUser') -> editStatus($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('admin/user'), ['token' => $this -> request -> token(), 'status' => $result['data']]);
            }
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    /**
     * 角色管理
     */
    public function role(){
        $search_key = input();
        unset($search_key['/admin/admin/role_html']);

        $list = model('AdminRole') -> resList($search_key);

        unset($search_key['page']);
        if(!$search_key){
            $search_key = null;
        }

        $this -> assign('list', $list);
        $this -> assign('search_key', $search_key);

        return $this -> fetch();
    }

    public function role_form(){
        if($this -> request -> isAjax()){
            $data = input('post.');
            $data['create_time'] = time();

            $result = model('AdminRole') -> operation($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('admin/role'), ['token' => $this -> request -> token()]);
            }
        }else{
            $id = input('id', 0);
            $res = null;
            if($id){
                $res = model('AdminRole') -> resFind($id);
            }

            // 查询规则和权限
            $result = model('AdminRole') -> rule();

            $this -> assign('result', $result);
            $this -> assign('id', $id);
            $this -> assign('res', $res);
            return $this -> fetch();
        }
    }

    public function role_status(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $result = model('AdminRole') -> editStatus($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('admin/rule'), ['token' => $this -> request -> token(), 'status' => $result['data']]);
            }
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    public function role_del(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $res = model('AdminRole') -> del($data);
            if($res['code']){
                return $this -> success($res['msg'], url('admin/role'), ['token' => $this -> request -> token()]);
            }else{
                return $this -> error($res['msg'], '', ['token' => $this -> request -> token()]);
            }
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    /**
     * 权限管理
     */
    public function rule(){
        // 权限管理数据
        $list = model('AdminRule') -> resList();
        $this -> assign('list', $list);

        // 权限分类数据
        $classifyList = model('AdminClassify') -> select();
        $this -> assign('classifyList', $classifyList);

        // 控制器
        $controllers = getControllers('../application/admin/controller');
        $this -> assign('controllers', $controllers);

        return $this -> fetch();
    }

    public function rule_actions(){
        if($this -> request -> isAjax()){
            $controller = input('controller');
            $actions = [];
            if($controller){
                $actions =  getActions('app\admin\controller' . '\\' . $controller);
            }

            return $this -> success('', '', ['actions' => $actions]);
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    public function rule_form(){
        if($this -> request -> isAjax()){
            $data = input('post.');
            $result = model('AdminRule') -> operation($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('admin/rule'), ['token' => $this -> request -> token()]);
            }
        }else{
            $id = input('id', 0);
            $result = null;
            if($id){
                $result = model('AdminRule') -> resFind($id);

                // 所有规则和当前规则
                $result['classifys'] = model('AdminClassify') -> select();

                // 当前控制器和方法
                $result['controller'] = explode('@', $result['controller']);

                // 所有控制器
                $result['controllers'] = getControllers('../application/admin/controller');

                // 当前控制器的所有方法
                $result['actions'] =  getActions('app\admin\controller' . '\\' . $result['controller'][0]);
            }

            $this -> assign('result', $result);
            $this -> assign('id', $id);
            return $this -> fetch();
        }
    }

    public function rule_del(){
        if($this -> request -> isAjax()){
            $data = input('post.');

            $res = model('AdminRule') -> del($data);
            if($res['code']){
                return $this -> success($res['msg'], url('admin/rule'), ['token' => $this -> request -> token()]);
            }else{
                return $this -> error($res['msg'], '', ['token' => $this -> request -> token()]);
            }
        }else{
            return $this -> error('非法请求', url('index/index'));
        }
    }

    /**
     * 权限分类
     */
    public function classify(){
        $list = model('AdminClassify') -> order('id desc') -> paginate(10);
        $this -> assign('list', $list);
        return $this -> fetch();
    }

    public function classify_form(){
        if($this -> request -> isAjax()){
            $data = input('post.');
            $result = model('AdminClassify') -> operation($data);
            if(!$result['code']){
                return $this -> error($result['msg'], '', ['token' => $this -> request -> token()]);
            }else{
                return $this -> success($result['msg'], url('admin/classify'), ['token' => $this -> request -> token()]);
            }
        }else{
            $id = input('id', 0);
            $result = null;
            if($id){
                $result = model('AdminClassify') -> find($id);
            }

            $this -> assign('result', $result);
            $this -> assign('id', $id);
            return $this -> fetch();
        }
    }


    /**
     * 日志记录
     */
    public function log(){
        $list = $this->log_cat();
        // 倒序
        rsort($list);

        // 分页处理
        $data = $list;
        $curPage = input('page') ? input('page') : 1; //当前第x页，有效值为：1,2,3,4,5...
        $listRow = 10; //每页2行记录

        $showData = array_chunk($data, $listRow, true);
        $showData = $showData[$curPage - 1];
        $showData = array_slice($data, ($curPage - 1) * $listRow, $listRow, true);

        $p = Bootstrap::make($showData, $listRow, $curPage, count($data), false, [
            'var_page' => 'page',
            'path'     => url('admin/log'),//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);
        $p->appends($_GET);
        
        $this->assign('list', $p);

        return $this -> fetch();
    }


}