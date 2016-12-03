<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DayOneService;
use Illuminate\Console\Command;

class DayOneCommand extends Command
{
    const DIRECTION_LEFT  = 'L';
    const DIRECTION_RIGHT = 'R';
    const DIRECTION_NORTH = 'N';
    const DIRECTION_EAST  = 'E';
    const DIRECTION_SOUTH = 'S';
    const DIRECTION_WEST  = 'W';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc2016:1 {actual?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve day one of Advent of Code';

    /** @var DayOneService */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @param DayOneService $service
     */
    public function __construct(DayOneService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Switch for retrieving the code on the actual keypad: puzzle 2.
        // If the parameter is empty, the code for the assumed keypad is returned
        $actual = !empty($this->argument('actual'));

        $shortestDistance  = $this->service->findEasterBunnyHq($actual);
        $this->info("The shortest path to the destination is $shortestDistance blocks.");
        return true;
    }
}
