<?php

namespace App\Console\Commands;

use App\Jobs;
use App\Device\Device;
use App\Jobs\DiscoverDeviceJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiscoverDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netman:discoverDevice {--ip=} {--id=} {--username=} {--password=}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Device Discover device by ip address. Username and password can be passed in optional';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = $this->options();
        if(!$options['id'] && !$options['ip'])
        {
            throw new \Exception('No IP or ID specified!');
        }
        Log::info('DiscoverDeviceCommand', ['DiscoverDeviceJob' => 'starting', 'device_id' => $options['id'],'device_ip' => $options['ip']]);   // Log device to the log file.
        $result = DiscoverDeviceJob::dispatch($options);		// Create a scan job for each device in the database
        return $result;
    }
}
