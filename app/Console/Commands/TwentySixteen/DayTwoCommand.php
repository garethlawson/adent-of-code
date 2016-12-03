<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DayTwoService;
use Illuminate\Console\Command;

class DayTwoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc2016:2 {actual?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve day two of Advent of Code';

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
     * @param DayTwoService $service
     * @return mixed
     */
    public function handle(DayTwoService $service)
    {
        // Switch for retrieving the code on the actual keypad: puzzle 2.
        // If the parameter is empty, the code for the assumed keypad is returned
        $actual = !empty($this->argument('actual'));

        // Call the service and echo the result on the console
        $this->info($service->determineBathroomCode($actual));
        return true;
    }
}
