<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DayFiveService;
use Illuminate\Console\Command;

class DayFiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:5 {part2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve day five of Advent of Code';

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
     * @param DayFiveService $service
     * @return mixed
     */
    public function handle(DayFiveService $service)
    {
        // Switch to get the answer to part 2 of the puzzle.
        // Implemented by adding anything as an argument to the command
        $part2 = !empty($this->argument('part2'));

        $this->info("The decrypted password is: {$service->getPuzzleAnswer($part2)}");
        return true;
    }
}
