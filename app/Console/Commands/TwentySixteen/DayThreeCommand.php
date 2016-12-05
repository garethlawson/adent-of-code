<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DayThreeService;
use Illuminate\Console\Command;

class DayThreeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:3 {part2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve day three of Advent of Code';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param DayThreeService $service
     * @return mixed
     */
    public function handle(DayThreeService $service)
    {
        // Switch to get the answer to part 2 of the puzzle.
        // Implemented by adding anything as an argument to the command
        $part2 = !empty($this->argument('part2'));

        $this->info(
            "There are {$service->countPossibleTriangles($part2)} combinations that are possible triangles."
        );

        return true;
    }
}
