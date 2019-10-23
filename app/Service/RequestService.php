<?php


namespace Swoft\Cli\Service;


use App\Service\Core\Constants;
use Swlib\Saber;

class RequestService
{
    /**
     * @var string $accessToken
     */
    protected $accessToken = '';
    /**
     * @var string $baseUrl
     */
    protected $baseUrl = '';
    /**
     * @var string $apiVersion
     */
    protected $apiVersion = '';
    /**
     * The endpoint to call
     *
     * @var string string
     */
    protected $endpoint = '';
    /**
     * An array of headers to send with the request
     *
     * @var array $headers
     */
    protected $headers = [];
    /**
     * The body of the request (optional)
     *
     * @var string $requestBody
     */
    protected $requestBody;
    /**
     * The type of request to make ("GET", "POST", etc.)
     *
     * @var string $requestType
     */
    protected $requestType = '';
    /**
     * The timeout, in seconds
     *
     * @var string
     */
    protected $timeout;
    /**
     * @var $response
     */
    protected $response;
    /**
     * @var $responseHeaders
     */
    protected $responseHeaders;
    /**
     * @var $responseError
     */
    protected $responseError;
    /**
     * @var bool $error
     */
    public $error = false;

    /**
     * @param $accessToken
     *
     * @return $this
     */
    public function setAccessToken($accessToken): self
    {
        $this->accessToken = $accessToken;
        $this->headers['Authorization'] = 'Bearer ' . $this->accessToken;
        return $this;
    }

    /**
     * @param $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @param $version
     *
     * @return $this
     */
    public function setApiVersion($version): self
    {
        $this->apiVersion = $version;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @return mixed
     */
    public function getResponseError()
    {
        return $this->responseError;
    }

    /**
     * 构造 microsoft graph 请求
     *
     * @param string $method
     * @param $param
     * @param bool $needToken
     * @param string $attachBody
     * @return RequestService
     */
    public function request(string $method, $param, $needToken = true, $attachBody = ''): RequestService
    {
        if (is_array($param)) {
            [$endpoint, $requestHeaders, $timeout] = $param;
            $this->endpoint = $endpoint;
            $this->headers = $requestHeaders ?? $this->headers;
            $this->requestBody = $attachBody ?? '';
            $this->timeout = $timeout ?? Constants::DEFAULT_TIMEOUT;
        } else {
            $this->endpoint = $param;
            $this->timeout = Constants::DEFAULT_TIMEOUT;
            $this->requestBody = $attachBody ?? '';
        }
        $headers = $this->headers;
        $this->headers = array_merge([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken,
        ], $headers);
        if (!$needToken) {
            unset($this->headers['Authorization']);
        }
        if (stripos($this->endpoint, 'http') !== 0) {
            $this->endpoint = $this->apiVersion . $this->endpoint;
        }
        $this->requestType = strtoupper($method);

        if ($this->baseUrl) {
            $saber = Saber::create([
                'base_uri' => $this->baseUrl,
                'headers' => $this->headers,
                'useragent' => 'ISV|OLAINDEX|OLAINDEX/9.9.9',
                'retry_time' => Constants::DEFAULT_RETRY,
                'timeout' => Constants::DEFAULT_TIMEOUT,
            ]);
        } else {
            $saber = Saber::create([
                'headers' => $this->headers,
                'useragent' => 'ISV|OLAINDEX|OLAINDEX/9.9.9',
                'retry_time' => Constants::DEFAULT_RETRY,
                'timeout' => Constants::DEFAULT_TIMEOUT,
            ]);
        }
        $params = [
            $this->endpoint
        ];
        try {
            /* @var $response Saber\Response */
            $response = call_user_func_array([$saber, $this->requestType], $params);
        }catch (\Exception $e) {
            $this->responseError = collect([
                'errno' => $e->getCode(),
                'message' => $e->getMessage(),
            ])->toJson();
            $this->error = true;
        }

        $this->responseHeaders = collect($response->getHeaders())->toJson();
        $this->response = collect($response->getBody())->toJson();
        return $this;
    }

}
