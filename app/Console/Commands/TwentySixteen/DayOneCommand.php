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
    protected $signature = 'aoc2016:1 {part2?}';

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
        // Switch to get the answer to part 2 of the puzzle.
        // Implemented by adding anything as an argument to the command
        $part2 = !empty($this->argument('part2'));

        $shortestDistance  = $this->service->findEasterBunnyHq($part2);
        $this->info("The shortest path to the destination is $shortestDistance blocks.");
        return true;
    }
}
