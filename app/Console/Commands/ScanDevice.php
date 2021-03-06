<?php

namespace App\Console\Commands;

use App\Jobs;
use App\Device\Device;
use App\Jobs\ScanDeviceJob;
use Illuminate\Console\Command;

class ScanDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netman:scanDevice {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Device by ID';

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
        $arguments = $this->arguments();
        $id = $arguments['id'];
        if(!$id)
        {
            throw new \Exception('No ID specified!');
        }

        \Log::info('ScanDeviceCommand', ['ScanDeviceJob' => 'starting', 'device_id' => $id]);   // Log device to the log file.
        $result = ScanDeviceJob::dispatch($id);		// Create a scan job for each device in the database
        return $result;

    }

}
