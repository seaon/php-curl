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
    private $ch = null;
    public $timeout = 10;

    public function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new BadFunctionException("not found curl extension");
        }
        $this->ch = curl_init();
    }

    /**
     * @param type $this->url 地址
     * @param type $fields 附带参数，可以是数组，也可以是字符串
     * @param type $httpHeaders header头部，数组形式
     * @return boolean
     */
    public function execute($fields = '', $userAgent = '', $httpHeaders = '')
    {
        // curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout); //设置curl超时秒数

        if ('post' == $this->method) {
            curl_setopt($this->ch, CURLOPT_POST, true);
            if (is_array($fields)) {
                $sets = array();
                foreach ($fields as $key => $val) {
                    $sets[] = $key . '=' . urlencode($val);
                }
                $fields = implode('&', $sets);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
        } elseif ('put' == $this->method) {
            curl_setopt($this->ch, CURLOPT_PUT, true);
        }

        $this->checkHttps();

        return $this->exec();
    }

    public function header($header)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
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

    public function timeout(int $time, $unit = 'second')
    {
        if ($unit == 'second') {
            curl_setopt($this->ch, CURLOPT_TIMEOUT, $time);
        } else {
            curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, $time);
        }

        return $this;
    }

    public function checkHttps()
    {
        if (stripos($this->url, "https://") !== false) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        }
    }

    private function exec()
    {
        $result = curl_exec($this->ch);
        $errno = curl_errno($this->ch);

        if ($errno !== 0) {
            $result = array($errno, curl_error($this->ch));
        }

        curl_close($this->ch);
        return $result;
    }

    public function sendData($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data)
        ]);
    }

    public function setopt(array $opts)
    {
        foreach ($opts as $option => $value) {
            curl_setopt($this->ch, $option, $value);
        }
    }

    public function get()
    {
        
    }

    public function post()
    {
        
    }

    public function put()
    {
        
    }

    public function delete()
    {
        
    }

    public function options()
    {
        
    }

    public function trace()
    {
        
    }

    public function head()
    {
        
    }

    public function patch()
    {
        
    }

}
