<?php

    namespace app\index\controller;
	use think\Controller;
    class Uplode extends Controller
    {
    
        public function img_upload(){
        	$file = request()->file('file');
	        $info = $file->validate(['ext'=>'jpeg,jpg,png'])->move(env('root_path') . 'public' . '/' . 'upload');
	        if($info){
	        	// 'size'=>1024*1024*2,
	        	// str_replace('\\', '/', $path . '/' . $info->getSaveName());
	            $url = 'https://'.$_SERVER['SERVER_NAME'].'/'.'upload/'. $info->getSaveName();
	            return json(echoArr(1, '上传成功', $url));
	        }else{
	            // 上传失败获取错误信息
	            return json(echoArr(0, $file->getError()));
	        }
        }
        /**
         * 将Base64图片转换为本地图片并保存
         * @param  $base64_image_content 要保存的Base64
         * @param  $path 要保存的路径
         */
        function base64_image_content($base64_image_content){
            $path= "../public/upload/";
            if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
                //后缀 
                $type = $result[2];
                //创建文件夹，以年月日
                $new_file = $path.date('Ymd',time())."/";
                if(!file_exists($new_file)){
                    //检查是否有该文件夹，如果没有就创建，并给予最高权限
                    mkdir($new_file, 0700);
                }
                $new_file = $new_file.time().".{$type}";	//图片名以时间命名
                //保存为文件
                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                    //返回这个图片的路径
                    $url = str_replace('../public','https://bread.mimanduo.xyz',$new_file);
                    return $url;
                }else{
                    return json(echoArr(500,'上传失败'));
                }
            }else{
                return json(echoArr(500,'上传失败'));
            }
        }
    }

