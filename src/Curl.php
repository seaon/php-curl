<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/11/17
 * Time: 15:59
 */

namespace Sio;

// use \Sio\Exception\BadFunctionException;

class Curl
{
    private $ch     = null;
    public $timeout = 10000;
    public $method  = 'GET';
    public $url  = '';
    public $data  = [];
    public $header  = [];

    public function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new BadFunctionException("not found curl extension");
        }
        $this->ch = curl_init();
    }

    /**
     * @return void
     */
    public function reset() {
      $this->ch && curl_close($this->ch);
      $this->ch = curl_init();
    }

    /**
     * @param type $this->url 地址
     * @param type $fields 附带参数，可以是数组，也可以是字符串
     * @param type $httpHeaders header头部，数组形式
     * @return boolean
     */
    public function execute()
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, $this->timeout); //设置curl超时秒数

        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->method);
        if (!empty($this->header)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);
        }
        if (!empty($this->data)) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->buildData());
        }

        $this->checkHttps();

        $result = curl_exec($this->ch);
        $errno  = curl_errno($this->ch);

        $code = 0;
        $msg  = '';
        $data = $result;
        if ($errno !== 0) {
            $code = $errno;
            $msg  = curl_error($this->ch);
            $data = [];
        }

        curl_close($this->ch);
        return [
            'code' => $code,
            'msg'  => $msg,
            'data' => $result
        ];
    }

    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    private function buildData()
    {
        $data = $this->data;
        if (!is_array($data)) {
            return '';
        }

        if ($this->isJson()) {
            return json_encode($data);
        }

        $fields = http_build_query($data, '', '&');;

        return $fields;
    }

    public function isJson()
    {
        foreach ($this->header as $key => $value) {
            if (stripos($value, "json") !== false) {
                return true;
            }
        }

        return false;
    }

    public function header($header)
    {
        $this->header = array_merge($this->header, $header);
        return $this;
    }

    public function timeout(int $time)
    {
        $this->timeout = $time * 1000;
        return $this;
    }

    public function timeoutMs(int $time)
    {
        $this->timeout = $time;
        return $this;
    }

    public function proxy($host, $port, $username = '', $password = '', $type = 'http')
    {
        if (strlen($host) > 0 && strlen($proxyPort) > 0) {
            curl_setopt($this->ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($this->ch, CURLOPT_PROXY, $host);
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $port);
            curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

            if ($username != '') {
                curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $username . ":" . $password);
            }
        }

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;
        return $this;
    }

    public function checkHttps()
    {
        if (stripos($this->url, "https://") !== false) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        }
    }

    public function setopt(array $opts)
    {
        foreach ($opts as $option => $value) {
            curl_setopt($this->ch, $option, $value);
        }
    }

    public function get()
    {
        return $this->execute();
    }

    public function post()
    {
        $this->method = 'POST';
        return $this->execute();
    }

    public function put()
    {
        $this->method = 'PUT';
        return $this->execute();
    }

    public function delete()
    {
        $this->method = 'DELETE';
        return $this->execute();
    }
}
