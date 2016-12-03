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
    protected $signature = 'aoc:3 {columns?}';

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
        $columns = !empty($this->argument('columns'));

        $this->info(
            "There are {$service->countPossibleTriangles($columns)} combinations that cannot create a triangle."
        );

        return true;
    }
}
