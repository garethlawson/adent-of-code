<?php

namespace App\Console\Commands\TwentySixteen;

use App\Services\TwentySixteen\DaySixService;
use Illuminate\Console\Command;

class DaySixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aoc:6 {part2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve day six of Advent of Code';

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
     * @param DaySixService $service
     * @return mixed
     */
    public function handle(DaySixService $service)
    {
        // Switch to get the answer to part 2 of the puzzle.
        // Implemented by adding anything as an argument to the command
        $part2 = !empty($this->argument('part2'));

        $this->info("The decrypted message is: {$service->getPuzzleAnswer($part2)}");
        return true;
    }
}
