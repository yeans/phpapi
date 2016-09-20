<?php

namespace shihunguilai\phpapi\Http;

class response
{
    /**
     * 异步返回.
     *
     * @param array  $data
     * @param number $code
     * @param string $msg
     *                     2016-4-25-上午11:41:33
     */
    public static function noutJson(array $data, $code = 0, $msg = 'success')
    {
        self::out($data, $code, $msg, 'json', array('nout' => true));
    }

    /**
     * [outJson description].
     *
     * @param array  $data [description]
     * @param int    $code [description]
     * @param string $msg  [description]
     *
     * @return [type] [description]
     */
    public static function outJson(array $data, $code = 0, $msg = 'success')
    {
        self::out($data, $code, $msg, 'json');
        exit;
    }

    /**
     * [outJsonp description].
     *
     * @param array  $data [description]
     * @param int    $code [description]
     * @param string $msg  [description]
     *
     * @return [type] [description]
     */
    public static function outJsonp(array $data, $code = 0, $msg = 'success')
    {
        self::out($data, $code, $msg, 'jsonp');
        exit;
    }

    /**
     * @param array  $data
     * @param string $format json jsonp xml
     *                       2016-2-29-下午2:45:50
     */
    public static function out(array $data, $code = 0, $msg = 'success', $format = 'json', $ext = array())
    {
        self::filter_out_data($data);

        $ret['errorCode'] = $code;
        $ret['errorMsg'] = $msg;
        $ret['data'] = $data;

        if ($format == 'json') {
            echo  json_encode($ret);
        } elseif ($format == 'jsonp') {
            $jsonpCallback = empty($_GET['callback']) ? 'jsonpcallback' : $_GET['callback'];
            echo htmlspecialchars($jsonpCallback).'('.json_encode($ret).')';
        }

        if (isset($ext['nout']) && $ext['nout'] === true) {
            self::fastcgi_finish_fun();
        }

        return true;
    }

    public static function fastcgi_finish_fun()
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * 过滤掉   数据里 的 null  和 非 utf-8  编码的 数据.
     *
     * @param unknown $data
     *                      2016-2-29-下午3:13:59
     */
    public static function filter_out_data(&$data)
    {
        array_walk_recursive($data, array(__CLASS__, 'filter_fun'));
    }

    private static function filter_fun(&$value, $key)
    {
        if ($value === null) {
            $value = '';
        } elseif (is_string($value)) {
            if (!mb_check_encoding($value, 'utf-8')) {
                $value = '';
            }
        }
    }
}

//  $rsp = new \Common\Qesponse();
//  $rsp->out(200, 'ok',array('name'=>'及的咖啡接口的  ','info'=>array('t'=>time())),'json');
