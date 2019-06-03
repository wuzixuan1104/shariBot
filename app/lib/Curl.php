<?php

defined('MAPLE') || exit('此檔案不允許讀取！');

class Curl {
  protected $authHeaders, $userAgentHeader;

  public function __construct() {
    $this->authHeaders = [];
    // $this->authHeaders = ["Authorization: Bearer " . $channelToken];
    // $this->userAgentHeader = ['User-Agent: LINE-BotSDK-PHP/' . '3.1.0'];
  }
  
  public function get($url, $headers = []){
    return $this->sendRequest('GET', $url, $headers, []);
  }

  public function post($url, array $data = [], array $headers = []) {
    $headers = $headers ? $headers : ['Content-Type: application/json; charset=utf-8'];
    return $this->sendRequest('POST', $url, $headers, $data);
  }

  public function delete($url) {
    return $this->sendRequest('DELETE', $url, [], []);
  }

  private function getOptions($method, $headers, $reqBody) {
    $options = [
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_BINARYTRANSFER => true,
      CURLOPT_HEADER => true,
    ];
    if ($method === 'POST') {
      if (is_null($reqBody)) {
        // Rel: https://github.com/line/line-bot-sdk-php/issues/35
        $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
      } else {
        if (isset($reqBody['__file']) && isset($reqBody['__type'])) {
          $options[CURLOPT_PUT] = true;
          $options[CURLOPT_INFILE] = fopen($reqBody['__file'], 'r');
          $options[CURLOPT_INFILESIZE] = filesize($reqBody['__file']);
        } elseif (!empty($reqBody)) {
          $options[CURLOPT_POST] = true;
          $options[CURLOPT_POSTFIELDS] = http_build_query($reqBody);
        } else {
          $options[CURLOPT_POST] = true;
          $options[CURLOPT_POSTFIELDS] = $reqBody;
        }
      }
    }

    return $options;
  }

  private function sendRequest($method, $url, array $additionalHeader, $reqBody = null) {
    $headers = array_merge($this->authHeaders, $additionalHeader);
    $options = $this->getOptions($method, $headers, $reqBody);

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($errno)
      throw new CurlExecutionException($error);

    $httpStatus = $info['http_code'];
    $responseHeaderSize = $info['header_size'];

    $responseHeaderStr = substr($result, 0, $responseHeaderSize);
    $responseHeaders = [];
    foreach (explode("\r\n", $responseHeaderStr) as $responseHeader) {
      $kv = explode(':', $responseHeader, 2);
      count($kv) === 2 && $responseHeaders[$kv[0]] = trim($kv[1]);
    }

    $body = substr($result, $responseHeaderSize);

    isset($options[CURLOPT_INFILE]) && fclose($options[CURLOPT_INFILE]);

    $obj = new \stdClass();
    $obj->status = $httpStatus;
    $obj->body = $body;
    $obj->headers = $responseHeaders;
    $obj->isSucceeded = $httpStatus === 200;
    $obj->jsonBody = json_decode($body, true);
    return $obj;
  }
}