<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// curl请求
function sendCURL($url, $json = false, $ssl = false){
    // 初始化
    $cl = curl_init();
    // 设置抓取的url
    curl_setopt($cl, CURLOPT_URL, $url);
    if($json){
        // 设置post方式提交
        curl_setopt($cl, CURLOPT_POST, 1);
        // 设置post提交数据
        curl_setopt($cl, CURLOPT_POSTFIELDS, $json);
    }
    // 1 检查服务器SSL证书中是否存在一个公用名(common name)。译者注：公用名(Common Name)一般来讲就是填写你将要申请SSL证书的域名 (domain)或子域名(sub domain)。
    // 2 检查公用名是否存在，并且是否与提供的主机名匹配
    curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, $ssl);
    // 禁用后CURL将终止从服务端进行验证
    curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, $ssl);
    // 设置获取的信息以文件流的形式返回，而不是直接输出
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
    // 执行命令
    $res = curl_exec($cl);
    // 关闭URL请求
    curl_close($cl);
    return $res;
}

/**
 * 发起HTTPS请求
 */
function curl_post($url,$data,$header,$post=1)
{
    //初始化curl
    $ch = curl_init();
    //参数设置
    $res= curl_setopt ($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, $post);
    if($post)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    $result = curl_exec ($ch);
    //连接失败
    if($result == FALSE){
        if($this->BodyType=='json'){
            $result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
        } else {
            $result = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><Response><statusCode>172001</statusCode><statusMsg>网络错误</statusMsg></Response>";
        }
    }

    curl_close($ch);
    return $result;
}

// 输出验证数据格式
function echoArr($code, $msg, $data = []){
    return [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data
    ];
}

// 用户密码加密
function encryption($pass){
    return md5(config('passConfig') . $pass);
}

// 获取所有控制器名
function getControllers($dir) {
    $pathList = glob($dir . '/*.php');
    $res = [];
    foreach($pathList as $key => $value) {
        $res[] = basename($value, '.php');
    }
    return $res;
}

//  获取某个控制器的方法名的函数,方法过滤父级Base控制器的方法，只保留自己的
function getActions($className, $base='\app\admin\controller\Base') {
    $methods = get_class_methods(new $className());
    $baseMethods = get_class_methods(new $base());
    $res = array_diff($methods, $baseMethods);
    return $res;
}

// 截取字符串
function interceptStr($str, $max = 60){
    $old_str = $str;
    $str = mb_substr($old_str, 0, $max, 'utf-8');
    if(mb_strlen($old_str, 'utf-8') > $max){
        $str .= '....';
    }

    return $str;
}

// 搜索条件
function search($data, $str){
    $res = [
        [$str, 'between time', [$data['start'],$data['end']]]
    ];

    return $res;
}

// 生成唯一订单号
function random_num($uid = 1){
    $num = substr(uniqid(), 7, 13);
    $str = '';
    for($i = 0; $i < strlen($num); $i++){
        $str .= ord($num[$i]);
    }
    $str = date('Ymd'). $uid . substr($str, 0, 10) .  str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

    return $str;
}

// 分类级别处理
function cateList($data, $pid = 0, $level = 1){
    $arr = [];

    foreach($data as $v){
        if($v['pid'] == $pid){
            $v['level'] = $level;
            $arr[] = $v;
            $temp = cateList($data, $v['id'], $level + 1);
            $arr = array_merge($arr, $temp);
        }
    }

    return $arr;
}

// 单个分类级别处理
function cateFind($data, $pid = 0, $id, $level = 1){
    $arr = [];

    foreach($data as $v){
        if($id == $v['id']){
            if($v['pid'] == $pid){
                $v['level'] = $level;
                $arr[] = $v;
                $temp = cateList($data, $v['id'], $level + 1);
                $arr = array_merge($arr, $temp);
            }
        }
    }

    return $arr;
}

// 获取中文字符拼音首字母
function getFirstCharter($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312//TRANSLIT',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return '';
}

// 读取excel的内容
function read_excel($filename)
{
    //设置excel格式
    $reader = PHPExcel_IOFactory::createReader('Excel2007');
    //载入excel文件
    $excel = $reader->load($filename);
    //读取第一张表
    $sheet = $excel->getSheet(0);
    //获取总行数
    $row_num = $sheet->getHighestRow();
    //获取总列数
    $col_num = $sheet->getHighestColumn();

    $data = []; //数组形式获取表格数据
    for($col='A';$col<=$col_num;$col++)
    {
        //从第二行开始，去除表头（若无表头则从第一行开始）
        for($row=2;$row<=$row_num;$row++)
        {
            $data[$row-2][] = $sheet->getCell($col.$row)->getValue();
        }
    }
    return $data;
}

// 获取时分秒
function time2second($seconds){
    $seconds = (int)$seconds;
    if( $seconds<86400){//如果不到一天
        $format_time = gmstrftime('%H时%M分%S秒', $seconds);
    }else{
        $time = explode(' ', gmstrftime('%j %H %M %S', $seconds));//Array ( [0] => 04 [1] => 14 [2] => 14 [3] => 35 )
//        $format_time = ($time[0]-1).'天'.$time[1].'时'.$time[2].'分'.$time[3].'秒';
        $format_time = ($time[0]-1).'天'.$time[1].'时';
    }
    return $format_time;
}

// 根据地址获取经纬度
function getLatng($prepAddr){
    $geocode=file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=$prepAddr&output=json&ak=HgY3BTUXWeGekbcGXlFq0lt4bVuKs62w");
    $output= json_decode($geocode,true);
    $latng = $output['result']['location'];

    return $latng;
}


/**
 *  计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1  起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2  终点纬度
 * @param  Int     $unit       单位 1:米 2:公里
 * @param  Int     $decimal    精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI /180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if($unit==2){
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);
}
