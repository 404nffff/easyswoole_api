<?php 
/**
 * Redis 键值记录.
 * User: w
 * Date: 2020-04-13
 * Time: 9:19
 */

namespace App\Utils;

use EasySwoole\Component\Singleton;

class Curl
{

    //单例
    use Singleton;

    /**
     * 开启curl post请求
     * @param string $url URL
     * @param string $jsonData  json 字符串
     * @param array  $header
     * 
     * @return array 响应主体Content
     */
    public function post($url, $jsonData, $header = [])
    {
        if (empty($header)){
            $header = ['Expect:'];
        }
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length:' . strlen($jsonData);
       
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //解决数据包大不能提交
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话
        return $tmpInfo; // 返回数据
    }

}
