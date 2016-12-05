<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DayFourService;
use Illuminate\Console\Command;

class DayFourCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:4 {part2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve day four of Advent of Code';

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
     * @param DayFourService $service
     * @return mixed
     */
    public function handle(DayFourService $service)
    {
        // Switch to get the answer to part 2 of the puzzle.
        // Implemented by adding anything as an argument to the command
        $part2 = !empty($this->argument('part2'));
        $message = "The sum of the valid room sectors is: ";
        if ($part2) {
            $message = "The sector ID of the room where North Pole objects are stored is: ";
        }

        $this->info($message . $service->getPuzzleAnswer($part2));
        return true;
    }
}
