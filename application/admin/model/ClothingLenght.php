<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2018/11/19
 * Time: 16:09
 */

namespace app\admin\model;


use think\Model;

use app\admin\validate\ClothingLenght as Vali;
use app\admin\model\IntelligentAmount as a;
class ClothingLenght extends Model
{
    public function addFind($data)
    {
        $data ['time']= time();
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(0, $validate->getError());
        }
        $status = $this->save($data);
        if($status){
            return echoArr(1, '操作成功', $data['status']);
        }else{
            return echoArr(0, $this->getError());
        }
    }
}