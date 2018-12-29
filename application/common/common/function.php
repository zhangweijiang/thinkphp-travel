<?php
use think\PHPMailer;
/**
 * 数字签名认证
 * @param string $data 被认证的数据
 * @return string 签名
 */
function dataAuthSign($data)
{
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名

    return $sign;
}

/**
 * 随机生成密码函数
 * @param int $length 密码长度
 * @return string
 */
function generate_password( $length = 8 ) {
    // 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}