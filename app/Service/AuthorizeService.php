<?php


namespace Swoft\Cli\Service;


use App\Service\Core\Constants;
use Swlib\Http\Stream;
use Swlib\SaberGM;
use Swoft\Cli\Models\Client;
use Swoft\Stdlib\Helper\ArrayHelper;
use Tightenco\Collect\Support\Collection;

class AuthorizeService
{
    /**
     * @var AuthorizeService
     */
    private static $instance;
    /**
     * @var $account
     */
    private $account;

    /**
     * @return AuthorizeService
     */
    public static function init(): AuthorizeService
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * AuthorizeService constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param $account
     * @return $this
     */
    public function bind($account): AuthorizeService
    {
        $this->account = $account;
        return $this;
    }

    /**
     * OneDrive 授权请求
     * @param $form_params
     * @return \Psr\Http\Message\StreamInterface|\Swlib\Http\StreamInterface
     */
    private function request($form_params)
    {
        $client = new Client($this->account);
        $form_params = array_merge([
            'client_id' => $client->client_id,
            'client_secret' => $client->client_secret,
            'redirect_uri' => $client->redirect_uri,
        ], $form_params);
        if (ArrayHelper::getValue($this->account, 'account_type', 0) === Constants::ACCOUNT_CN) {
            $form_params = ArrayHelper::set(
                $form_params,
                'resource',
                $client->graph_endpoint
            );
        }
        try {
            $response = SaberGM::post($client->authorize_url . $client->token_endpoint, $form_params, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'useragent' => 'ISV|OLAINDEX|OLAINDEX/9.9.9',
                'retry_time' => Constants::DEFAULT_RETRY,
                'timeout' => Constants::DEFAULT_TIMEOUT,
            ]);
        } catch (\Exception $e) {
            throw new $e($e->getCode() . '\n' . $e->getMessage());
        }
        return $response->getBody();
    }

    /**
     * 获取授权登录地址
     *
     * @param $state
     * @return string
     */
    public function getAuthorizeUrl($state = ''): string
    {
        $client = new Client($this->account);
        $values = [
            'client_id' => $client->client_id,
            'redirect_uri' => $client->redirect_uri,
            'scope' => $client->scopes,
            'response_type' => 'code',
        ];
        if ($state) {
            $values = ArrayHelper::set($values, 'state', $state);
        }
        $query = http_build_query($values, '', '&', PHP_QUERY_RFC3986);
        $authorization_url = $client->authorize_url . $client->authorize_endpoint . "?{$query}";
        return $authorization_url;
    }

    /**
     * 请求获取access_token
     * @param $code
     * @return \Psr\Http\Message\StreamInterface|\Swlib\Http\StreamInterface
     */
    public function getAccessToken($code)
    {
        $form_params = [
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        return $this->request($form_params);
    }

    /**
     * 请求刷新access_token
     * @param $existingRefreshToken
     * @return \Psr\Http\Message\StreamInterface|\Swlib\Http\StreamInterface
     */
    public function refreshAccessToken($existingRefreshToken)
    {
        $form_params = [
            'refresh_token' => $existingRefreshToken,
            'grant_type' => 'refresh_token',
        ];
        return $this->request($form_params);
    }

    /**
     * 防止实例被克隆（这会创建实例的副本）
     */
    private function __clone()
    {
    }

}
