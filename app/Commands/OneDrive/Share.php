<?php

namespace App\Commands\OneDrive;

use App\Helpers\OneDrive;
use App\Helpers\Tool;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Share extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'share {remote : Remote Path}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create Download Link';

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $this->call('refresh:token');
        $this->info('Please waiting...');
        $remote = $this->argument('remote');
        $_remote = OneDrive::responseToArray(OneDrive::pathToItemId(Tool::getRequestPath($remote)));
        $remote_id = $_remote['code'] === 200 ? array_get($_remote, 'data.id') : exit('Remote Path Abnormal');
        $share = OneDrive::createShareLink($remote_id);
        $response = OneDrive::responseToArray($share);
        if ($response['code'] === 200) {
            $direct = str_replace('15/download.aspx', '15/guestaccess.aspx', $response['data']['redirect']);
            $this->info("Success! Share Link:\n{$direct}");
        } else {
            $this->warn("Failed!\n{$response['msg']}");
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
