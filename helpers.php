<?php

use QuarkCMS\QuarkAdmin\Models\ActionLog;
use QuarkCMS\QuarkAdmin\Models\Picture;
use QuarkCMS\QuarkAdmin\Models\File;
use QuarkCMS\QuarkAdmin\Models\Config;
use App\Models\Category;
use App\Models\Sms;
use App\Models\Wechat;
use App\Models\Printer;
use App\User;
use App\Excels\Export;
use App\Excels\Import;
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use OSS\OssClient;
use OSS\Core\OssException;
use GuzzleHttp\Client as HttpClient;

/**
* 判断当前url是否被选中
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('get_url_activated')) {
    function get_url_activated($url,$activated = 'active')
    {
        $urlActiveStatus = '';
        if (!empty($url)) {
            
            $host = $_SERVER['HTTP_HOST'];
            $requestUri = $_SERVER['REQUEST_URI'];

            // https状态 todo
            $httpsStatus = web_config('SSL_OPEN');

            $httpsStatus == 1 ? $baseUrl = 'https://' : $baseUrl = 'http://';

            $getUrl = $baseUrl.$host.$requestUri;

            if($requestUri =='/' && $url == '/index/index') {
                $urlActiveStatus = $activated;
            } else {
                if(strpos($getUrl, $url) !== false) {
                    $urlActiveStatus = $activated;
                }
            }
        }

        return $urlActiveStatus;
    }
}

/**
 * Sioo希奥发送手机短信接口
 * @return string
 */
if(!function_exists('sioo_send_sms')) {
    function sioo_send_sms($phone,$content) {

        if(!preg_match("/^1[34578]\d{9}$/", $phone)) {
            return error('手机号错误！');
        }

        $uid = web_config('SIOO_UID');
        $password = web_config('SIOO_PASSWORD');

        if(empty($uid) || empty($password)) {
            return error('接口配置错误！');
        }

        // 转换内容类型
        $msg  = mb_convert_encoding($content,'GBK','utf-8');

        // 接口url
        $url = "https://submit.10690221.com/send/ordinarykv?uid="
        .$uid
        ."&password=".md5($password)
        ."&mobile=".$phone
        ."&msg=".$msg;

        $client = new HttpClient();

        $response = $client->request('GET', $url);

        $body = $response->getBody()->getContents();

        $result = json_decode($body,true);

        if ($result['code'] == 0) {
            return success('发送成功！');
        } else {
            return error($result['msg']);
        }
    }
}

/**
 * sms_post Alidayu发送手机短信接口
 * string $config = ['app_key' => '*****','app_secret' => '************',// 'sandbox' => true,  // 是否为沙箱环境，默认false;
 * string $signName = '积木云'
 * string $templateCode = 'SMS_70450333'
 * string $phone = '15076569633'
 * string $smsParam = [ 'number' => rand(100000, 999999)]
 */
if(!function_exists('alidayu_send_sms')) {
    function alidayu_send_sms($templateCode,$phone,$smsParam) {

        if(!preg_match("/^1[34578]\d{9}$/", $phone)) {
            return error('手机号错误！');
        }

        $config['app_key'] = web_config('ALIDAYU_APP_KEY');
        $config['app_secret'] = web_config('ALIDAYU_APP_SECRET');
        $signName = web_config('ALIDAYU_APP_SIGNNAME');

        if(empty($config['app_key']) || empty($config['app_secret']) || empty($signName)) {
            return error('接口配置错误！');
        }

        if(empty($templateCode)) {
            return error('模板代码不能为空！');
        }

        if(empty($smsParam)) {
            return error('短信参数不能为空！');
        }

        //执行发短信
        $client = new Client(new App($config));
        $request = new AlibabaAliqinFcSmsNumSend;

        $request->setRecNum($phone)
                ->setSmsParam($smsParam)
                ->setSmsFreeSignName($signName)
                ->setSmsTemplateCode($templateCode);

        $result = $client->execute($request);

        if ($result) {
            return success('发送成功！');
        } else {
            return error('发送失败！');
        }
    }
}

/**
 * 生成缩略图
 * @author tangtanglove
 * @param string $imagePath 图片路径
 * @param string $thumbPath 缩略图路径
 */
if(!function_exists('make_thumb')) {
    function make_thumb($imagePath,$thumbPath,$width,$height,$thumbType = 1)
    {
        if (empty($imagePath)) {
            return error('图片路径不能为空！');
        }

        if (empty($thumbPath)) {
            //如果不定义缩略图路径，则以thumb_+原图片名命名
            $list = explode('/', $imagePath);
            $key = count($list)-1;
            //定义缩略图名称
            $thumb_name = 'thumb_'.$width.'_'.$height.'_'.$list[$key];
            $thumbPath = str_replace($list[$key],'',$imagePath).$thumb_name;
        }

        if (is_file($imagePath)) {
            //不存在缩略图则创建
            if (!is_file($thumbPath)) {
                $image = \think\Image::open($imagePath);
                $image->thumb($width, $height,$thumbType)->save($thumbPath);
            }
            return $thumbPath;
        } else {
            return $imagePath;
        }
    }
}

/**
* 适应手机页面
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('mobile_adaptor')) {
	function mobile_adaptor($objects)
    {
        if($objects) {
            foreach ($objects as $key => $object) {

                if(isset($object['content'])) {
                    $preg_str = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";                    
                    preg_match_all($preg_str,$object['content'],$match);
                    
                    foreach ($match[1] as $key1 => $value) {
                        if(!strstr($value,"v.qq.com")) {
                            if(strpos($value,'../') !== false) {
                                $objects[$key]['cover_path'.$key1] = 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace('../','',$value);
                                if(web_config('SSL_OPEN') == 1) {
                                    $objects[$key]['cover_path'.$key1] = 'https://'.$_SERVER['HTTP_HOST'].'/'.str_replace('../','',$value);
                                }
                            } else {
                                $objects[$key]['cover_path'.$key1] = $value;
                            }
                        }
                    }

                    $preg_str1 = "/<[video|VIDEO|embed|EMBED|source|SOURCE].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
                    preg_match_all($preg_str1,$object['content'],$match1);
                    
                    foreach ($match1[1] as $key2 => $value2) {
                        if(strstr($value2,"v.qq.com")) {
                            $objects[$key]['video_path'.$key2] = $value2;
                        }
                    }
                }

                // 封面图
                if (isset($object['cover_id'])) {
                    // 获取文件url，用于外部访问
                    $objects[$key]['cover_path'] = get_picture($object['cover_id']);
                }

                // 多封面
                if (isset($object['cover_ids'])) {
                    // 获取文件url，用于外部访问
                    if(count(explode('[',$object['cover_ids']))>1) {
                        $coverIds = json_decode($object['cover_ids'], true);

                        foreach($coverIds as $coverKey => $coverId) {
                            $objects[$key]['cover_path'.$coverKey] = get_picture($coverId);
                        }
                    }
                }
            }

            // 生成手机图
            if (isset($objects['content'])) {
                $preg_str = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
                preg_match_all($preg_str,$objects['content'],$match);

                if($match[1]) {
                    foreach ($match[1] as $key => $value) {
                        if(strpos($value,'../') !== false) {
                            $objects['content'] = str_replace($value,'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace('../','',$value),$objects['content']);

                            if(web_config('SSL_OPEN') == 1) {
                                $objects['content'] = str_replace($value,'https://'.$_SERVER['HTTP_HOST'].'/'.str_replace('../','',$value),$objects['content']);
                            }
                        }
                    }
                }

                $preg_str1 = "/<[video|VIDEO|embed|EMBED|source|SOURCE].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
                preg_match_all($preg_str1,$objects['content'],$match1);
                
                foreach ($match1[1] as $key2 => $value2) {
                    $objects['video_path'.$key2] = $value2;
                }
            }

            // 多封面
            if (isset($objects['cover_ids']) && !empty($objects['cover_ids'])) {

                // 获取文件url，用于外部访问
                if(count(explode('[',$objects['cover_ids']))>1) {
                    $coverIds = json_decode($objects['cover_ids'], true);
                }

                if($coverIds) {
                    foreach($coverIds as $coverKey => $coverId) {
                        $url = get_picture($coverId);
                        $objects['cover_path'.$coverKey] = $url;
                    }
                }
            }

            // 单图
            if (isset($objects['cover_id']) && !empty($objects['cover_id'])) {

                // 获取文件url，用于外部访问
                $objects['cover_path'] = get_picture($objects['cover_id']);
            }
        }

        return $objects;
    }
}

/**
* 获取文章内视频
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('get_content_video')) {
	function get_content_video($content)
    {
        preg_match_all('/<[iframe|video|embed]*\s+src="([^"]*)"[^>]*>/is',$content,$match);
        return $match[1][0];
    }
}

/**
* 获取文章内图片url
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('get_content_picture')) {
	function get_content_picture($content)
    {
        preg_match_all("/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/",$content,$match);

        $result = '';

        foreach ($match[1] as $key => $value) {
            if(strpos($value,'../') !== false) {
                $baseUrl = 'http://';

                if (web_config('SSL_OPEN') == 1) {
                    $baseUrl = 'https://';
                }

                $result[$key] = $baseUrl.$_SERVER['HTTP_HOST'].'/'.str_replace('../','',$value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}

/**
* 获取分类名称
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('get_category')) {
	function get_category($id)
    {
        $category = Category::find($id);
        if(!empty($category)) {
            return $category->title;
        }
    }
}

/**
 * 字符串截取，支持中文和其他编码
 * static 
 * access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * return string
 */
if(!function_exists('msubstr')) {
	function msubstr($str, $start=0, $length, $charset="utf-8")
    {
        if(function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif(function_exists('iconv_substr')) {
            $slice = iconv_substr($str,$start,$length,$charset);
            if(false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
        }

        $strlen=mb_strlen($str);
        if($strlen>$length) {
            $slice = $slice.'...';
        }
        return $slice;
    }
}

/**
 * 过滤Emoji
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('filter_emoji')) {
    function filter_emoji($str)
    {
        $str = preg_replace_callback('/./u',function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },$str);

        return $str;
    }
}

/**
 * 过滤Emoji
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('filter_emoji')) {
    function filter_emoji($str)
    {
        $str = preg_replace_callback('/./u',function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },$str);

        return $str;
    }
}

/**
 * 返回公众号配置
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('wechat_config')) {
    function wechat_config($type = 'fwh')
    {
        switch ($type) {
            case 'dyh':

                // 订阅号
                $appid  = web_config('WECHAT_DYH_APPID');
                $secret = web_config('WECHAT_DYH_APPSECRET');
                $token  = web_config('WECHAT_DYH_TOKEN');
                $aesKey = web_config('WECHAT_DYH_ENCODINGAESKEY');
                break;
            case 'fwh':

                // 服务号
                $appid  = web_config('WECHAT_FWH_APPID');
                $secret = web_config('WECHAT_FWH_APPSECRET');
                $token  = web_config('WECHAT_FWH_TOKEN');
                $aesKey = web_config('WECHAT_FWH_ENCODINGAESKEY');
                break;
            case 'mp':
            
                // 小程序
                $appid  = web_config('WECHAT_MP_APPID');
                $secret = web_config('WECHAT_MP_APPSECRET');
                $token  = web_config('WECHAT_MP_TOKEN');
                $aesKey = web_config('WECHAT_MP_ENCODINGAESKEY');
                break;
            default:
                return error('请指定公众号类型！');
                break;
        }

        $config = false;

        if(!empty($appid) && !empty($secret)) {
            $config = [
                'debug'     => true,
                'app_id'    => $appid,
                'secret'    => $secret,
                'token'     => $token,
                'aes_key'   => $aesKey,
                'oauth' => [
                    'scopes'   => ['snsapi_userinfo'],
                    'callback' => url('wxLogin/callback'),
                ],
                'log' => [
                    'level' => 'debug',
                    'file'  => storage_path('/logs/easywechat/easywechat_'.date('Ymd').'.log'),
                ]
            ];
        }

        return $config;
    }
}

/**
 * 返回公众号配置
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('wechat_pay_config')) {
    function wechat_pay_config()
    {
        $getApiclientCertPath = '';
        $getApiclientKeyPath = '';

        $getApiclientCert = web_config('WECHAT_PAY_APICLIENT_CERT');
        $getApiclientKey  = web_config('WECHAT_PAY_APICLIENT_KEY');

        if(!empty($getApiclientCert) && !empty($getApiclientKey)) {
            $apiclientCertInfo = File::where('id',$getApiclientCert)->first();
            $apiclientKeyInfo = File::where('id',$getApiclientKey)->first();
            $getApiclientCertPath = str_replace("\\","/",storage_path('app\\'.$apiclientCertInfo['path']));
            $getApiclientKeyPath = str_replace("\\","/",storage_path('app\\'.$apiclientKeyInfo['path']));
        }

        $config = [
            'debug'     => true,
            'app_id'    => web_config('WECHAT_PAY_APP_ID'),
            'log' => [
                'level' => 'debug',
                'file'  => storage_path('/logs/easywechat/easywechat_'.date('Ymd').'.log'),
            ],
            'mch_id'             => web_config('WECHAT_PAY_MERCHANTID'),
            'key'                => web_config('WECHAT_PAY_KEY'),
            'cert_path'          => $getApiclientCertPath, // XXX: 绝对路径！！！！
            'key_path'           => $getApiclientKeyPath // XXX: 绝对路径！！！！
        ];

        return $config;
    }
}

/**
 * 返回微信app支付配置
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('wechat_app_pay_config')) {
    function wechat_app_pay_config()
    {
        $getApiclientCertPath = '';
        $getApiclientKeyPath = '';

        $getApiclientCert = web_config('WECHAT_APP_PAY_APICLIENT_CERT');
        $getApiclientKey  = web_config('WECHAT_APP_PAY_APICLIENT_KEY');

        if(!empty($getApiclientCert) && !empty($getApiclientKey)) {
            $apiclientCertInfo = File::where('id',$getApiclientCert)->first();
            $apiclientKeyInfo = File::where('id',$getApiclientKey)->first();
            $getApiclientCertPath = str_replace("\\","/",storage_path('app\\'.$apiclientCertInfo['path']));
            $getApiclientKeyPath = str_replace("\\","/",storage_path('app\\'.$apiclientKeyInfo['path']));
        }

        $config = [
            'debug'     => true,
            'app_id'    => web_config('WECHAT_APP_PAY_APP_ID'),
            'log' => [
                'level' => 'debug',
                'file'  => storage_path('/logs/easywechat/easywechat_'.date('Ymd').'.log'),
            ],
            'mch_id'             => web_config('WECHAT_APP_PAY_MERCHANTID'),
            'key'                => web_config('WECHAT_APP_PAY_KEY'),
            'cert_path'          => $getApiclientCertPath, // XXX: 绝对路径！！！！
            'key_path'           => $getApiclientKeyPath // XXX: 绝对路径！！！！
        ];

        return $config;
    }
}

/**
 * 返回微信小程序支付配置
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('wechat_mp_pay_config')) {
    function wechat_mp_pay_config()
    {
        $getApiclientCertPath = '';
        $getApiclientKeyPath = '';

        $getApiclientCert = web_config('WECHAT_MINIPROGRAMPAY_APICLIENT_CERT');
        $getApiclientKey  = web_config('WECHAT_MINIPROGRAMPAY_APICLIENT_KEY');

        if(!empty($getApiclientCert) && !empty($getApiclientKey)) {
            $apiclientCertInfo = File::where('id',$getApiclientCert)->first();
            $apiclientKeyInfo = File::where('id',$getApiclientKey)->first();
            $getApiclientCertPath = str_replace("\\","/",storage_path('app\\'.$apiclientCertInfo['path']));
            $getApiclientKeyPath = str_replace("\\","/",storage_path('app\\'.$apiclientKeyInfo['path']));
        }

        $config = [
            'debug'     => true,
            'app_id'    => web_config('WECHAT_MINIPROGRAMPAY_APP_ID'),
            'log' => [
                'level' => 'debug',
                'file'  => storage_path('/logs/easywechat/easywechat_'.date('Ymd').'.log'),
            ],
            'mch_id'             => web_config('WECHAT_MINIPROGRAMPAY_MERCHANTID'),
            'key'                => web_config('WECHAT_MINIPROGRAMPAY_KEY'),
            'secret'             => web_config('WECHAT_MINIPROGRAMPAY_SECRET'),
            'cert_path'          => $getApiclientCertPath, // XXX: 绝对路径！！！！
            'key_path'           => $getApiclientKeyPath // XXX: 绝对路径！！！！
        ];

        return $config;
    }
}

/**
* 创建订单号
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('create_order_no')) {
    function create_order_no()
    {
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

/**
* 判断是否为手机端
* @author tangtanglove <dai_hang_love@126.com>
*/
if(!function_exists('is_mobile')) {
    function is_mobile()
    {
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array ('nokia',  'sony','ericsson','mot',
                'samsung','htc','sgh','lg','sharp',
                'sie-','philips','panasonic','alcatel',
                'lenovo','iphone','ipod','blackberry',
                'meizu','android','netfront','symbian',
                'ucweb','windowsce','palm','operamini',
                'operamobi','openwave','nexusone','cldc',
                'midp','wap','mobile'
                );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字  
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }
}

/**
 * @param $url 请求网址
 * @param bool $params 请求参数
 * @param int $ispost 请求方式
 * @param bool $headers 请求头部
 * @param int $https https协议
 * @return bool|mixed
 */
if(!function_exists('curl')) {
    function curl($url, $params = false, $method = 'get', $headers = false, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }

        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, true);

            if (is_array($params)) {
                $params = http_build_query($params);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }

            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}

/**
 * 获取用户名
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('user')) {
    function user($uid = '',$field = 'name')
    {
        $result = '';
        if(empty($uid)) {
            $user = auth('web')->user();
        } else {
            $user = User::where('id',$uid)->first();
        }

        if(isset($user[$field])) {
            $result = $user[$field];
        }

        return $result;
    }
}

/**
 * 生成二维码
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('qrcode')) {
    function qrcode($text)
    {
        $qrCode = new QrCode($text);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        exit;
    }
}

/**
 * 发送邮件
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('send_email')) {
    function send_email($subject,$toEmail,$content)
    {
        config([
            'mail.host' => web_config('EMAIL_HOST'),
            'mail.port' => web_config('EMAIL_PORT'),
            'mail.from' => ['address' => web_config('EMAIL_USERNAME'),'name' => web_config('WEB_SITE_NAME')],
            'mail.username' => web_config('EMAIL_USERNAME'),
            'mail.password' => web_config('EMAIL_PASSWORD'),
            ]);
        Mail::raw($content, function ($message) use($toEmail, $subject) {
            $message ->to($toEmail)->subject($subject);
        });

        if(count(Mail::failures()) < 1){
            return true;
        }else{
            return false;
        }
    }
}

/**
 * 是否微信浏览器
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('is_wechat')) {
    function is_wechat()
    {
        // 微信中登录认证
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                return true;
            } else {
                return false;
            }
        }
    }
}

/**
 * 导出Excel
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('export')) {
    function export($fileName,$titles,$lists,$columnFormats = [])
    {
        $getTitles = [];
        $getLists  = [];

        if (!(count($titles) == count($titles, 1))) { // 标题为二维数组
            foreach ($titles as $key => $value) {
                $getTitles[] = $value['title'];
                $fileds[] = $value['filed'];
            }

            foreach ($lists as $key1 => $value1) {
                foreach ($fileds as $key2 => $value2) {
                    $rows[$value2] = $value1[$value2];
                }
                $getLists[$key1] = $rows;
            }
        } else {
            $getTitles = $titles;
            $getLists  = $lists;
        }

        $export = new Export($getLists,$getTitles,$columnFormats);

        return Excel::download($export,$fileName.'_'.date('YmdHis').'.xlsx');
    }
}

/**
 * 导入Excel
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('import')) {
    function import($fileId)
    {
        $file = File::where('id',$fileId)->first();

        $importData = Excel::toArray(new Import, storage_path('app/').$file['path']);

        $results = $importData[0];

        return $results;
    }
}

/**
 * 获取当前地理位置
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('address')) {
    function address($ip='', $latitude='', $longitude='') {

        $getAddress = [];
        if(!empty($ip)) {
            // 根据ip获取地理位置
            $address = curl('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
            $address = json_decode($address,true);
            if($address === false) {
                $getAddress = '';
            } else {
                $getAddress['country'] = $address['data']['country'];
                $getAddress['province'] = $address['data']['region'];
                $getAddress['city'] = $address['data']['city'];
                $getAddress['district'] = $address['data']['county'];
            }
        } elseif(!empty($latitude) && !empty($longitude)) {
            // 根据经纬度获取地理位置
            $address = curl('http://apis.map.qq.com/jsapi?qt=rgeoc&lnglat='.$longitude.'%2C'.$latitude);
            $address = mb_convert_encoding($address, "utf-8", "gb18030");
            $address = json_decode($address,true);
            if($address === false) {
                $getAddress = '';
            } else {
                $getAddress['country'] = $address['detail']['results'][0]['n'];
                $getAddress['province'] = $address['detail']['results'][0]['p'];
                $getAddress['city'] = $address['detail']['results'][0]['c'];
                $getAddress['district'] = $address['detail']['results'][1]['address_name'];
            }
        }

        return $getAddress;
    }
}

/**
 * 验证短信验证码是否合法
 * @author tangtanglove <dai_hang_love@126.com>
 */
if(!function_exists('validate_sms_code')) {
    function validate_sms_code($phone,$code) {

        if(empty($phone)) {
            return error('请先获取手机验证码！');
        }

        if(empty($code)) {
            return error('手机验证码不能为空！');
        }

        $sms = Sms::where('phone',$phone)->orderBy('id','desc')->first();

        // 判断验证码是否正确
        if($sms['code'] != $code) {

            // 更新错误次数
            Sms::where('id',$sms['id'])->increment('error_times');
            return error('手机验证码错误！');
        }

        // 验证码有效时间6分钟，最多允许6次错误
        if(((time() - strtotime($sms['created_at'])) > 3600) || ($sms['error_times'])>6) {
            return error('手机验证码已经失效，请重新获取！');
        }

        return success('验证成功！');
    }
}

/**
 * 获取易联云打印机Token
 * @param $grantType
 * @param $scope
 * @param $timesTamp
 * @param null $code
 * @return mixed
 */
if(!function_exists('get_printer_token')) {
    function get_printer_token($clientId,$clientSecret,$grantType, $scope, $timesTamp, $code = null)
    {
        $requestAll = [
            'client_id' => $clientId,
            'sign' => md5($clientId.$timesTamp.$clientSecret),
            'id' => Str::uuid(),
            'grant_type' => $grantType,
            'scope' => $scope,
            'code' => $code,
            'timestamp' => $timesTamp,
        ];
        
        $url = 'https://open-api.10ss.net/oauth/oauth';
        $params = http_build_query($requestAll);
        return curl($url, $params, 'post',0, 0);
    }
}

/**
 * 打印机
 * @param $printerId 打印机id
 * @param $originId 可以为订单号的id
 * @param $content 打印内容
 * @return mixed
 */
if(!function_exists('printer')) {
    function printer($printerId,$originId,$content)
    {
        $printer = Printer::where('id',$printerId)->first();

        if(empty($printer)) {
            return error('无此打印机配置信息！');
        }

        $machineCode    = $printer['machine_code'];
        $clientId       = $printer['client_id'];
        $clientSecret   = $printer['client_secret'];
        $accessToken    = $printer['access_token'];
        $refreshToken   = $printer['refresh_token'];
        $grantType      = 'client_credentials';  //自有模式(client_credentials) || 开放模式(authorization_code)
        $scope          = 'all';                 //权限
        $timesTamp      = time();                //当前服务器时间戳(10位)

        $getYlyAccessToken = Cache::get('yly_access_token');

        if(empty($getYlyAccessToken)) {

            // 获取access_token
            $tokenInfo = get_printer_token($clientId,$clientSecret,$grantType,$scope,$timesTamp);
            $tokenInfo = json_decode($tokenInfo,true);

            $data['access_token'] = $tokenInfo['body']['access_token'];
            $data['refresh_token'] = $tokenInfo['body']['refresh_token'];

            // 储存到缓存
            Cache::put('yly_access_token', $data, $tokenInfo['body']['expires_in']/60);

            // 赋值
            $accessToken = $tokenInfo['body']['access_token'];
        } else {
            // 赋值
            $accessToken = $getYlyAccessToken['access_token'];
        }

        $url = 'https://open-api.10ss.net/print/index';
        $requestAll = [
            'client_id' => $clientId,
            'sign' => md5($clientId.$timesTamp.$clientSecret),
            'id' => Str::uuid(),
            'machine_code' => $machineCode,
            'access_token' => $accessToken,
            'content' => $content,
            'origin_id' => $originId,
            'timestamp' => $timesTamp,
        ];

        $params = http_build_query($requestAll);

        $getResult = curl($url, $params, 'post',0, 0);

        $result = json_decode($getResult,true);

        if ($result['error'] == 0) {
            return success('操作成功！');
        } else {
            return error('操作失败！');
        }
    }
}

/**
* 将一个字符串部分字符用*替代隐藏
* @param string    $string   待转换的字符串
* @param int       $bengin   起始位置，从0开始计数，当$type=4时，表示左侧保留长度
* @param int       $len      需要转换成*的字符个数，当$type=4时，表示右侧保留长度
* @param int       $type     转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
* @param string    $glue     分割符
* @return string   处理后的字符串
*/
if(!function_exists('hideStr')) {
    function hideStr($string, $bengin=0, $len = 4, $type = 0, $glue = "@") {
        if (empty($string))
            return false;
        $array = array();
        if ($type == 0 || $type == 1 || $type == 4) {
            $strlen = $length = mb_strlen($string);
            while ($strlen) {
                $array[] = mb_substr($string, 0, 1, "utf8");
                $string = mb_substr($string, 1, $strlen, "utf8");
                $strlen = mb_strlen($string);
            }
        }
        if ($type == 0) {
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", $array);
        } else if ($type == 1) {
            $array = array_reverse($array);
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", array_reverse($array));
        } else if ($type == 2) {
            $array = explode($glue, $string);
            $array[0] = hideStr($array[0], $bengin, $len, 1);
            $string = implode($glue, $array);
        } else if ($type == 3) {
            $array = explode($glue, $string);
            $array[1] = hideStr($array[1], $bengin, $len, 0);
            $string = implode($glue, $array);
        } else if ($type == 4) {
            $left = $bengin;
            $right = $len;
            $tem = array();
            for ($i = 0; $i < ($length - $right); $i++) {
                if (isset($array[$i]))
                    $tem[] = $i >= $left ? "*" : $array[$i];
            }
            $array = array_chunk(array_reverse($array), $right);
            $array = array_reverse($array[0]);
            for ($i = 0; $i < $right; $i++) {
                $tem[] = $array[$i];
            }
            $string = implode("", $tem);
        }

        return $string;
    }
}

/**
* 获取手机号区域信息
*/
if(!function_exists('get_phone_location')) {
    function get_phone_location($phone,$type = 'baidu')
    {
        switch ($type) {
            case 'baidu':
                $url = "http://mobsec-dianhua.baidu.com/dianhua_api/open/location";
                $querys['tel'] = $phone;
                $phoneData = curl($url, $querys, 'get',false, true);
                $phoneData = json_decode($phoneData,true);

                $result['province'] = $phoneData['response'][$phone]['detail']['province'];
                $result['city'] = $phoneData['response'][$phone]['detail']['area'][0]['city'];
                $result['operator'] = $phoneData['response'][$phone]['detail']['operator'];
                break;
            case '360':
                $url = "https://cx.shouji.360.cn/phonearea.php";
                $querys['number'] = $phone;
                $phoneData = curl($url, $querys, 'get',false, true);
                $phoneData = json_decode($phoneData,true);

                $result['province'] = $phoneData['data']['province'];
                $result['city'] = $phoneData['data']['city'];
                $result['operator'] = $phoneData['data']['sp'];
                break;
            case 'taobao':
                $url = "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm";
                $querys['tel'] = $phone;
                $phoneData = curl($url, $querys, 'get',false, true);

                $phoneData = trim(explode('=',$phoneData)[1]);
                $phoneData = iconv('gbk','utf-8', $phoneData);
                $phoneData = str_replace("'",'"', $phoneData);
                $phoneData = preg_replace('/(\w+):/is', '"$1":', $phoneData);
                $phoneData = json_decode($phoneData,true);

                $result['province'] = $phoneData['province'];
                $result['city'] = '';
                $result['operator'] = $phoneData['catName'];
                break;
            default:
                $url = "http://mobsec-dianhua.baidu.com/dianhua_api/open/location";
                $querys['tel'] = $phone;
                $phoneData = curl($url, $querys, 'get',false, true);
                $phoneData = json_decode($phoneData,true);

                $result['province'] = $phoneData['response'][$phone]['detail']['province'];
                $result['city'] = $phoneData['response'][$phone]['detail']['area']['city'];
                $result['operator'] = $phoneData['response'][$phone]['detail']['operator'];
                break;
        }

        return $result;
    }
}

/**
* 腾讯云语音识别，$audioFile='http://www.website/1.mp3'，必须安装ffmpeg程序
*/
if(!function_exists('speech_recognition')) {
    function speech_recognition($audioFile,$ffmpeg = '')
    {
        //用户需要修改为自己腾讯云官网账号中的appid，secretid与secretKey
        $SecretId = web_config('TENCENTCLOUD_SECRET_ID');
        $secretKey = web_config('TENCENTCLOUD_SECRET_KEY');

        if(empty($ffmpeg)) {
            return error("请指定ffmpeg程序的绝对路径");
        }

        // 语音数据来源 0:语音url，1:语音数据bodydata
        $SourceType = 1;

        /**
         * 读取音频文件
         */
        $getFilePaths = explode('/',$audioFile);
        $fileName = $getFilePaths[count($getFilePaths)-1];
        
        $audioContent = file_get_contents($audioFile);

        if ($audioContent == FALSE) {
            return error("语音文件不存在！");
        }

        $fileNameWithoutExts = explode('.',$getFilePaths[count($getFilePaths)-1]);

        $fileNameWithoutExt = $fileNameWithoutExts[0];

        // 默认本地上传
        $uploadPath = 'uploads/files/'.$fileName;
        $getResult = Storage::disk('public')->put($uploadPath,$audioContent);

        if($getResult) {

            $path = 'public/'.$uploadPath;

            // 数据
            $realFilePath = storage_path('app/').$path;

            // '2>$1' 配置管道输出错误，方便调试
            $command = $ffmpeg.' -i '.$realFilePath.' -acodec pcm_s16le -ac 1 -ar 8000 '.storage_path('app/').'public/uploads/files/'.$fileNameWithoutExt.'.wav 2>&1';

            $status = shell_exec($command);
        }

        // 语音数据地址
        $URI = storage_path('app/').'public/uploads/files/'.$fileNameWithoutExt.'.wav';

        if (empty($secretKey)) {
            return error("secretKey不能为空！");
        }

        if (empty($SecretId)) {
            return error("SecretId不能为空！");
        }

        if (empty($URI)) {
            return error("URI不能为空！");
        }

        $params = array();
        $params['Action'] = 'SentenceRecognition';
        $params['SecretId'] = $SecretId;
        $params['Timestamp'] = time();
        $params['Nonce'] = substr($params['Timestamp'], 0, 4);
        $params['Version'] = '2018-05-22';
        $params['ProjectId'] = 0;
        $params['SubServiceType'] = 2;
        $params['EngSerViceType'] = '8k';
        $params['SourceType'] = $SourceType;

        if ($params['SourceType'] == 0) {
            $voice = $URI;
            $voice = urlencode($voice);
            $params['Url'] = $voice;
        } else if ($params['SourceType'] == 1) {
            $file_path = $URI;
            if (file_exists($file_path)) {
                $handle = fopen($file_path, "rb");
                $str = fread($handle, filesize($file_path));
                fclose($handle);
                $strlen = strlen($str);
                $str = base64_encode($str);
                $params["Data"] = $str;
                $params["DataLen"] = $strlen;
            } else {
                return error("文件不存在！");
            }
        }
        $params['VoiceFormat'] = 'wav';
        $params['UsrAudioKey'] =  substr(str_shuffle("QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm"), 26, 16);
    
        $tmpParam = array();

        ksort($params);

        foreach ($params as $key => $value) {
            array_push($tmpParam, str_replace("_", ".", $key) . "=" . $value);
        }

        $strParam = join("&", $tmpParam);
        $signStr = strtoupper('POST') . 'aai.tencentcloudapi.com/?' . $strParam;
    
        $sign = base64_encode(hash_hmac('sha1', $signStr, $secretKey, true));
    
        $params['Signature'] = $sign;
    
        $url = 'https://aai.tencentcloudapi.com';
        $headers = array("Host:aai.tencentcloudapi.com", "Content-Type:application/x-www-form-urlencoded", "charset=UTF-8");

        $result = curl($url, $params, $method = 'post', $headers);

        return json_decode($result,true);
    }
}

/**
 * web页面生成图片，必须安装phantomjs程序(https://github.com/ariya/phantomjs),windows下需要指定$phantomjs的绝对路径，如果截图出现乱码请安装相应的字体
 * html_to_image('https://www.taobao.com',750,null,'','D:\\Software\\phantomjs\\bin\\phantomjs');
 */
if(!function_exists('html_to_image')) {
    function html_to_image($source,$width = null,$height = null,$path = '',$phantomjs = '')
    {
        if(empty($source)) {
            return error("url路径不能为空！");
        }

        $conv = new \Anam\PhantomMagick\Converter();

        if(empty($phantomjs)) {
            return error("请指定phantomjs程序的绝对路径");
        }

        if(empty($path)) {
            $path = storage_path('app/').'public/converts/'.md5($source).'.jpg';
        }

        $conv->setBinary($phantomjs);

        if(!file_exists($path)) {

            if($width) {
                $conv->width($width);
            }

            if($height) {
                $conv->height($height);
            }

            $conv->source($source)
            ->toJpg()
            ->save($path);
        }

        return $path;
    }
}

/**
 * 通过远程url上传图片
 *
 * @param  Request  $request
 * @return Response
 */
if(!function_exists('download_picture_to_storage')) {
    function download_picture_to_storage($url)
    {
        $fileArray = explode('/',$url);

        if(!count($fileArray)) {
            return error('未读取到图片名称！');
        }

        $fileInfo = explode('.',$fileArray[count($fileArray)-1]);

        if(count($fileInfo)>=2) {
            $fileName = $fileInfo[0];
            $fileType = $fileInfo[1];

            $name = Str::random(40).'.'.$fileType;
        } else {
            $name = Str::random(40).'.png';
        }

        $ossOpen = web_config('OSS_OPEN');

        if($ossOpen == 'on') {
            $driver = 'oss';
        } else {
            $driver = 'local';
        }

        if(strpos($url,'https') !== false) {
            $https = 1;
        } else {
            $https = 0;
        }

        $content = curl($url,false,'get',false,$https);

        switch ($driver) {
            case 'oss':

                // 阿里云上传
                $accessKeyId = web_config('OSS_ACCESS_KEY_ID');
                $accessKeySecret = web_config('OSS_ACCESS_KEY_SECRET');
                $endpoint = web_config('OSS_ENDPOINT');
                $bucket = web_config('OSS_BUCKET');
                $myDomain = web_config('OSS_MYDOMAIN');
        
                try {
                    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        
                    // 如果设置自定义域名
                    if(!empty($myDomain)) {
                        // 查看CNAME记录。
                        $cnameConfig = $ossClient->getBucketCname($bucket);
        
                        $hasCname = false;
                        foreach ($cnameConfig as $key => $value) {
                            if($value['Domain'] == $myDomain) {
                                $hasCname = true;
                            }
                        }
        
                        // 未添加CNAME记录，则程序自动添加
                        if($hasCname === false) {
                            // 添加CNAME记录。
                            $ossClient->addBucketCname($bucket, $myDomain);
                        }
                    }
        
                } catch (OssException $e) {
                    print $e->getMessage();
                }
        
                $object = 'pictures/'.$name;
        
                // 上传到阿里云
                try {
                    $ossResult = $ossClient->putObject($bucket, $object, $content);
                } catch (OssException $e) {
                    $ossResult = $e->getMessage();
                    // 返回数据
                    return error('上传失败！');
                }
    
                // 数据
                $data['name'] = $name;
                $data['size'] = $ossResult['info']['size_upload'];
                $data['md5'] = md5($content);
    
                // 设置自定义域名，则文件url执行自定义域名
                if(!empty($myDomain)) {
                    $data['path'] = str_replace($bucket.'.'.$endpoint,$myDomain,$ossResult['info']['url']);
                    $data['path'] = str_replace('http','https',$data['path']);
                
                } else {
                    $data['path'] = $ossResult['info']['url'];
                    $data['path'] = str_replace('http','https',$data['path']);
                }
    
                // 插入数据库
                $picture = Picture::create($data);
                $pictureId = $picture->id;
    
                // 获取文件url，用于外部访问
                $url = $data['path'];
    
                // 获取文件大小
                $size = $ossResult['info']['size_upload'];
        
                $result['id'] = $pictureId;
                $result['name'] = $name;
                $result['url'] = $url;
                $result['size'] = $size;
        
                break;
            
            default:

                // 默认本地上传
                $uploadPath = 'uploads/pictures/'.$name;
                $getResult = Storage::disk('public')->put($uploadPath,$content);
                
                if($getResult) {
                    $path = 'public/'.$uploadPath;

                    // 数据
                    $data['name'] = $name;
                    $data['md5'] = md5_file(storage_path('app/').$path);
                    $data['path'] = $path;

                    // 插入数据库
                    $picture = Picture::create($data);
                    $pictureId = $picture->id;

                    // 获取文件url，用于外部访问
                    $url = Storage::url($path);

                    // 获取文件大小
                    $size = Storage::size($path);

                    $result['id'] = $pictureId;
                    $result['name'] = $name;
                    $result['url'] = asset($url);
                    $result['size'] = $size;

                } else {
                    return error('上传失败！');
                }

                break;
        }

        // 返回数据
        return success('上传成功！','',$result);
    }
}