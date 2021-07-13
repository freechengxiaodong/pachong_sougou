<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use stdClass;
use DB;

class IndexController extends Controller
{
    public function index($key){
        //关键词
        $keyword = $key;
        //url字符编码
        $keyword = urlencode($keyword);
        //拼接请求地址
        $origin_url = 'https://pic.sogou.com/napi/pc/searchList';
        $mode = 1;
        $start = 0;
        $len = 50;
        $img[][] = new stdClass();
        $flag = 0;
        for ($start;$start<=200;$start += 50){
            $page_url = json_decode($this->get_url($origin_url,$mode,$start,$len,$keyword));
            $data = json_decode($this->get_data($page_url));
            //获取有效图片的远程地址
            foreach ($data as $k => $v){
                if( $v -> oriPicUrl != NULL){
                    if($v->title){
                        echo '第'.$flag.'条'.'&nbsp;&nbsp;'.$v->title;
                    }else{
                        echo '第'.$flag.'条'.'&nbsp;&nbsp;无title信息';
                    }
                    DB::table('img')->insert([
                        'title' => $v->title,
                        'href' => $v->oriPicUrl
                    ]);
                   // $img[$flag]['href'] = $v->oriPicUrl;
                    $flag++;
                }
            }
        }
        dd('ok');
    }
    public function get_url($origin_url,$mode,$start,$len,$keyword){
        $url = $origin_url.'?mode='.$mode.'&start='.$start.'&xml_len='.$len.'&query='.$keyword;
        return json_encode($url);
    }
    public function get_data($url){
        //测试guzzle
        $client = new Client();
        $res = $client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 20, //超时时间（秒）
        ]);
        $res->getStatusCode(); // 获得接口反馈状态码
        $body = $res->getBody(); //获得接口返回的主体对象
        $body = $body->getContents(); //获得主体内容
        $data = json_decode($body);
        return json_encode($data->data->items);
    }
}
