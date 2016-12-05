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
    protected $signature = 'aoc2016:2 {part2?}';

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
        // Switch to get the answer to part 2 of the puzzle.
        // Implemented by adding anything as an argument to the command
        $part2 = !empty($this->argument('part2'));

        // Call the service and echo the result on the console
        $this->info("The code to get into the bathroom is: {$service->determineBathroomCode($part2)}");
        return true;
    }
}
