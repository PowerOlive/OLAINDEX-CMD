<?php


namespace Swoft\Cli\Models;


use App\Service\Core\Constants;
use Swoft\Stdlib\Helper\ArrayHelper;

class Client
{
    /**
     * @var integer
     */
    public $account_type = 1;

    /**
     * @var string
     */
    public $client_id;
    /**
     * @var string
     */
    public $client_secret;
    /**
     * @var string
     */
    public $redirect_uri;
    /**
     * @var string
     */
    public $authorize_url;
    /**
     * @var string
     */
    public $authorize_endpoint;
    /**
     * @var string
     */
    public $token_endpoint;
    /**
     * @var string
     */
    public $graph_endpoint;
    /**
     * @var string
     */
    public $api_version;
    /**
     * @var string
     */
    public $scopes;

    /**
     * Client constructor.
     * @param array $array
     */
    public function __construct($array = [])
    {
        $this->loadConfig($array);
    }

    /**
     * @param $array
     */
    public function loadConfig($array): void
    {
        $account_type = (int)ArrayHelper::get($array, 'account_type', 1);
        $config = [];
        if ($account_type === Constants::ACCOUNT_COM) {
            $config = [
                'account_type' => $account_type,
                'client_id' => ArrayHelper::get($array, 'client_id'),
                'client_secret' => ArrayHelper::get($array, 'client_secret'),
                'redirect_uri' => ArrayHelper::get($array, 'redirect_uri'),
                'authorize_url' => Constants::AUTHORITY_URL,
                'authorize_endpoint' => Constants::AUTHORIZE_ENDPOINT,
                'token_endpoint' => Constants::TOKEN_ENDPOINT,
                'graph_endpoint' => Constants::REST_ENDPOINT,
                'api_version' => Constants::API_VERSION,
                'scopes' => Constants::SCOPES
            ];
        }

        if ($account_type === Constants::ACCOUNT_CN) {
            $config = [
                'account_type' => $account_type,
                'client_id' => ArrayHelper::get($array, 'client_id'),
                'client_secret' => ArrayHelper::get($array, 'client_secret'),
                'redirect_uri' => ArrayHelper::get($array, 'redirect_uri'),
                'authorize_url' => Constants::AUTHORITY_URL_21V,
                'authorize_endpoint' => Constants::AUTHORIZE_ENDPOINT_21V,
                'token_endpoint' => Constants::TOKEN_ENDPOINT_21V,
                'graph_endpoint' => Constants::REST_ENDPOINT_21V,
                'api_version' => Constants::API_VERSION,
                'scopes' => Constants::SCOPES
            ];
        }
        foreach ($config as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
}
