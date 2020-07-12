<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/12
 * Time: 10:55
 */

namespace app\index\model;


use think\Model;

class Plugin extends Model
{
    /**
     * 微信配置
     */
    public function wxConfig(){
        $result = $this->where('code', 'weixin')->where('type', 'payment')->value('config_value');
        $config = unserialize($result);
        return $config;
    }
}