<?php declare(strict_types=1);

namespace Swoft\Cli\Command;

use Swoft\Cli\Service\AuthorizeService;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandArgument;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Stdlib\Helper\ArrayHelper;

/**
 * This is description for the command group
 *
 * @Command(name="account",coroutine=true)
 */
class AccountCommand
{
    /**
     * Bind Account
     *
     * @CommandMapping("bind")
     *
     * @param Input $input
     * @param Output $output
     * @throws \ErrorException
     *
     */
    public function bind(Input $input, Output $output): void
    {
        $output->info('Start.');
        $account_type = $output->checkbox('Please choose account type', ['cn', 'com']);
        $client_id = $output->ask(' Client Id');
        $client_secret = $output->ask('Client Secret');
        $redirect_uri = $output->ask('Redirect Uri');
        $account = [
            'account_type' => $account_type,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
        ];
        $authorization_url = AuthorizeService::init()->bind($account)->getAuthorizeUrl();
        $output->success("Please copy this link to the browser to open. \n {$authorization_url}");
        $code = $output->ask("Please enter the code you got: \n");

        $token = AuthorizeService::init()->bind($account)->getAccessToken($code);
        $token = json_decode($token, true);
        $access_token = ArrayHelper::getValue($token, 'access_token');
        $refresh_token = ArrayHelper::getValue($token, 'refresh_token');
        $expires = ArrayHelper::getValue($token, 'expires_in') !== 0 ? time() + ArrayHelper::getValue($token, 'expires_in') : 0;
        $data = array_merge($account, [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'access_token_expires' => date('Y-m-d H:i:s', $expires),
        ]);
        $saveFile = file_put_contents('data.json', json_encode($data));
        $saveFile ? $output->success('Success.') : $output->error('Failed.');
    }

    /**
     * Refresh Account AccessToken
     *
     * @CommandMapping("refresh")
     *
     * @CommandOption("forece", short="f", type="integer", default=0,
     *     desc="Forece Refresh Token"
     * )
     *
     * @param Input $input
     * @param Output $output
     *
     */
    public function refresh(Input $input, Output $output)
    {
        $res = file_get_contents('data.json');
        $account = json_decode($res, true);
        $output->aList($account);

    }
}
