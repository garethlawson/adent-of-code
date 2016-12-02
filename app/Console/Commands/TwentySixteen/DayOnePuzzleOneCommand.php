<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DayOneService;
use Illuminate\Console\Command;

class DayOnePuzzleOneCommand extends Command
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
    protected $signature = 'aoc2016:1-1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $shortestDistance  = $this->service->findEasterBunnyHq1();
        $shortestDistance2 = $this->service->findEasterBunnyHq2();
        $this->info("The shortest path to the destination is $shortestDistance blocks.");
        $this->info("Easter bunny HQ is actually $shortestDistance2 blocks away.");

        return true;
    }
}
