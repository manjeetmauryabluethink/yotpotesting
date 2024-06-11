<?php

namespace Yotpo\Loyalty\Lib\Http\Client;

class Curl extends \Magento\Framework\HTTP\Client\Curl
{
    /**
     * Make request.
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     *
     * @return void
     */
    protected function makeRequest($method, $uri, $params = [])
    {
        $this->_ch = curl_init();

        $this->_headers['Expect'] = '';

        $this->curlOption(CURLOPT_URL, $uri);
        if ($method === 'POST') {
            $this->curlOption(CURLOPT_POST, 1);
            $this->setPostParams($params);
        } elseif ($method === 'GET') {
            $this->curlOption(CURLOPT_HTTPGET, 1);
        } else {
            $this->curlOption(CURLOPT_CUSTOMREQUEST, $method);
        }

        if (count($this->_headers)) {
            $heads = [];
            foreach ($this->_headers as $k => $v) {
                $heads[] = $k . ': ' . $v;
            }
            $this->curlOption(CURLOPT_HTTPHEADER, $heads);
        }

        if (count($this->_cookies)) {
            $cookies = [];
            foreach ($this->_cookies as $k => $v) {
                $cookies[] = "{$k}={$v}";
            }
            $this->curlOption(CURLOPT_COOKIE, implode(";", $cookies));
        }

        if ($this->_timeout) {
            $this->curlOption(CURLOPT_TIMEOUT, $this->_timeout);
        }

        if ($this->_port != 80) {
            $this->curlOption(CURLOPT_PORT, $this->_port);
        }

        $this->curlOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlOption(CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);

        if (count($this->_curlUserOptions)) {
            foreach ($this->_curlUserOptions as $k => $v) {
                $this->curlOption($k, $v);
            }
        }

        $this->_headerCount = 0;
        $this->_responseHeaders = [];
        $this->_responseBody = curl_exec($this->_ch);
        $err = curl_errno($this->_ch);
        if ($err) {
            $this->doError(curl_error($this->_ch));
        }

        curl_close($this->_ch);
    }

    /**
     * Prepare and set post params.
     *
     * @param array $params
     *
     * @return void
     */
    protected function setPostParams(array $params)
    {
        $contentType = null;
        if (!empty($this->_headers['Content-Type'])) {
            $contentType = $this->_headers['Content-Type'];
        }

        switch ($contentType) {
            case 'application/json':
                $params = json_encode($params);
                $this->curlOption(CURLOPT_POSTFIELDS, $params);
                $this->_headers['Content-Length'] = strlen($params);
                break;
            default:
                $this->curlOption(CURLOPT_POSTFIELDS, http_build_query($params));
        }
    }
}
