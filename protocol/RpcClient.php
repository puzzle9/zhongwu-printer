<?php

namespace zhongwu\protocol;

use InvalidArgumentException;
use Exception;
use zhongwu\exceptions\BusinessException;
use zhongwu\exceptions\InvalidTimestampException;
use zhongwu\exceptions\UnauthorizedException;
use zhongwu\exceptions\ValidationFailedException;

class RpcClient
{
    public $app_id;
    public $app_secret;
    public $remote_url;

    public function __construct($app_id, $app_secret, $remote_url = 'http://api.zhongwuyun.com')
    {
        if (is_null($app_id)
            || is_null($app_secret)
            || is_null($remote_url)
        ) {

            throw new InvalidArgumentException("invalid construct parameters.");
        }

        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->remote_url = $remote_url;
    }

    /**
     * @desc
     * @param       $action
     * @param array $parameters
     * @param       $method
     * @return mixed
     * @throws BusinessException
     * @throws Exception
     * @throws InvalidTimestampException
     */
    public function call($action, array $parameters = array(), $method = 'post')
    {
        $protocol = array_merge(array(
            "appid" => $this->app_id,
            "timestamp" => time(),
        ), $parameters);

        $protocol['sign'] = $this->generate_signature($protocol);

        if ($method == 'post') {
            $result = $this->post($this->remote_url . "/" . $action, $protocol);

        } else {
            $result = $this->get($this->remote_url . "/" . $action, $protocol);

        }
        $response = json_decode($result);
        if (is_null($response)) {
            throw new Exception("invalid response.");
        }

        if (isset($response->errNum) && $response->errNum != 0) {
            switch ($response->errNum) {
                case 1:
                    throw new UnauthorizedException($response->errMsg);
                    break;
                case 2:
                    throw new InvalidTimestampException($response->errMsg);
                    break;
                case 3:
                    throw new ValidationFailedException($response->errMsg);
                    break;
                default:
                    throw new BusinessException($response->errMsg);
            }
        }

        return $response;
    }

    public function generate_signature($protocol)
    {
        $stringtoSigned = '';

        ksort($protocol);

        foreach ($protocol as $k => $v) {

            if (strlen($v) > 0) {
                $stringtoSigned .= $k . $v;
            }

        }

        $stringtoSigned .= $this->app_secret;

        return md5($stringtoSigned);
    }

    public function post($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        return $response;
    }

    public function get($url, $data)
    {
        if (strpos($url, "?") === false) {
            $url = $url . '?' . http_build_query($data);
        } else {
            $url = $url . http_build_query($data);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        return $response;
    }
}
