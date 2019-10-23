<?php declare(strict_types=1);

namespace Swoft\Cli\Command;

use Swoft\Cli\Service\AuthorizeService;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandArgument;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Show;
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
     * this is description for the command
     *
     * @CommandMapping("bind")
     *
     * @param Input $input
     * @param Output $output
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
        $output->aList($account);
        $authorization_url = AuthorizeService::init()->bind($account)->getAuthorizeUrl();
        $output->info("Please copy this link to the browser to open. \n {$authorization_url}");
        $code = $output->ask("Please enter the code you got after logging in. \n");

        $token = AuthorizeService::init()->bind($account)->getAccessToken($code);
        // todo: 解析
        $access_token = ArrayHelper::getValue($token, 'access_token');
        $refresh_token = ArrayHelper::getValue($token, 'refresh_token');
        $expires = ArrayHelper::getValue($token, 'expires_in') !== 0 ? time() + ArrayHelper::getValue($token, 'expires_in') : 0;
        $account = collect($account);
        $data = [
            'account_type' => $account->get('account_type'),
            'client_id' => $account->get('client_id'),
            'client_secret' => $account->get('client_secret'),
            'redirect_uri' => $account->get('redirect_uri'),
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'access_token_expires' => date('Y-m-d H:i:s', $expires),
        ];

        Show::aList($data);
    }

//    /**
//     * this is description for the command
//     *
//     * @CommandMapping(alias="bind")
//     * @CommandArgument("arg0", type="string", desc="this is argument description")
//     *
//     * @CommandOption("opt0", short="s", type="integer", default=3,
//     *     desc="Interval time for watch files, unit is seconds"
//     * )
//     *
//     * @param Input $input
//     * @param Output $output
//     *
//     */
//    public function refresh(Input $input, Output $output)
//    {
//
//    }
}
