<?php

namespace App\Console\Commands;

use App\Services\aocDayTwoService;
use Illuminate\Console\Command;

class aocDayTwoPuzzleOneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:2-1 {actual?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param aocDayTwoService $service
     * @return mixed
     */
    public function handle(aocDayTwoService $service)
    {
        // Switch for retrieving the code on the actual keypad: puzzle 2.
        // If the parameter is empty, the code for the assumed keypad is returned
        $actual = !empty($this->argument('actual'));

        // Call the service and echo the result on the console
        $this->info($service->determineBathroomCode($actual));
        return true;
    }
}
