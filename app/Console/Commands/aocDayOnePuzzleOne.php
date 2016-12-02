<?php

namespace App\Console\Commands;

use App\Services\aocDayOneService;
use Illuminate\Console\Command;

class aocDayOnePuzzleOne extends Command
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
    protected $signature = 'aoc:1-1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /** @var aocDayOneService */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @param aocDayOneService $service
     */
    public function __construct(aocDayOneService $service)
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

    /**
     * Recalculate coordinates
     *
     * @param string $direction
     * @param int $blocks
     */
    protected function recalculateCoordinates(string $direction, int $blocks)
    {
        $newDirection = $this->adjustCompass($direction);
        switch ($newDirection) {
            case self::DIRECTION_NORTH:
                $this->vertical += $blocks;
                break;
            case self::DIRECTION_EAST:
                $this->horizontal += $blocks;
                break;
            case self::DIRECTION_SOUTH:
                $this->vertical -= $blocks;
                break;
            case self::DIRECTION_WEST:
                $this->horizontal -= $blocks;
                break;
        }

        if ($this->visitedCoords->contains($this->vertical . "," . $this->horizontal) && empty($this->easterBunnyHqDistance)) {
            $this->easterBunnyHqDistance = abs($this->vertical) + abs($this->horizontal);
        }

        $this->visitedCoords->push($this->vertical . "," . $this->horizontal);
    }

    /**
     * Adjust the compass: find the direction we are now going in
     *
     * @param string $direction
     * @return mixed
     */
    protected function adjustCompass(string $direction)
    {
        return $this->direction = $this->directionMatrix->get($this->direction)->get($direction);
    }
}
