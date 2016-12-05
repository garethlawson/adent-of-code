<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class DayOneService extends AbstractService
{
    /** Direction constants */
    const DIRECTION_LEFT  = 'L';
    const DIRECTION_RIGHT = 'R';
    const DIRECTION_NORTH = 'N';
    const DIRECTION_EAST  = 'E';
    const DIRECTION_SOUTH = 'S';
    const DIRECTION_WEST  = 'W';

    /** HTML Start position */
    const HTML_POSITION_START = 400;

    /** @var \Illuminate\Support\Collection */
    protected $directions;

    /** @var string */
    protected $direction;

    /** @var \Illuminate\Support\Collection */
    protected $directionMatrix;

    /** @var int */
    protected $vertical;

    /** @var int */
    protected $horizontal;

    /** @var \Illuminate\Support\Collection */
    protected $visitedCoordinates;

    /** @var \Illuminate\Support\Collection */
    protected $directionMethodMap;

    /** @var int */
    protected $easterBunnyHqActualDistance;

    /** @var string */
    protected $pathHtml = '';

    /**
     * aocDayOneService constructor.
     *
     * @param Filesystem $filesystem
     * @throws FileNotFoundException
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
        if (empty($this->puzzleInput)) {
            throw new FileNotFoundException("No puzzle input was found for today");
        }

        // Initialise directions
        $this->directions = collect(explode(", ", $this->puzzleInput));

        // Initialise visited coordinates for tracking the coordinates of each block that is passed
        $this->visitedCoordinates = collect([
            [0, 0],
        ]);

        // Intialise current coordinates and direction
        $this->vertical   = 0;
        $this->horizontal = 0;

        $this->direction = self::DIRECTION_NORTH;

        // Initialise the direction matrix
        $this->directionMatrix = collect([
            self::DIRECTION_NORTH => collect([
                self::DIRECTION_LEFT  => self::DIRECTION_WEST,
                self::DIRECTION_RIGHT => self::DIRECTION_EAST,
            ]),
            self::DIRECTION_EAST  => collect([
                self::DIRECTION_LEFT  => self::DIRECTION_NORTH,
                self::DIRECTION_RIGHT => self::DIRECTION_SOUTH,
            ]),
            self::DIRECTION_SOUTH => collect([
                self::DIRECTION_LEFT  => self::DIRECTION_EAST,
                self::DIRECTION_RIGHT => self::DIRECTION_WEST,
            ]),
            self::DIRECTION_WEST  => collect([
                self::DIRECTION_LEFT  => self::DIRECTION_SOUTH,
                self::DIRECTION_RIGHT => self::DIRECTION_NORTH,
            ]),
        ]);

        // Map directions to the method that will execute the movement
        $this->directionMethodMap = collect([
            self::DIRECTION_NORTH => 'moveNorth',
            self::DIRECTION_EAST  => 'moveEast',
            self::DIRECTION_SOUTH => 'moveSouth',
            self::DIRECTION_WEST  => 'moveWest',
        ]);
    }

    /**
     * Find the Easter Bunny HQ
     *
     * @param bool $actual
     * @return int
     */
    public function findEasterBunnyHq(bool $actual): int
    {
        // Iterate over directions
        $this->directions->each(function ($move) use ($actual) {
            // Get direction and distance from $move
            $direction = substr($move, 0, 1);
            $blocks    = intval(substr($move, 1, strlen($move)));

            $this->recalculateCoordinates($direction, $blocks);

            // Part 2 of the puzzle
            if ($actual && isset($this->easterBunnyHqActualDistance)) {
                return false;
            }

            return true;
        });

        // Part 2 of the puzzle
        if ($actual) {
            return $this->easterBunnyHqActualDistance;
        }

        // Part 1 of the puzzle
        return $this->getShortestDistanceInBlocks();
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
        for ($distance = 1; $distance <= $blocks; $distance++) {
            $this->move($newDirection);
        }
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

    protected function move(string $direction)
    {
        $method = $this->directionMethodMap->get($direction);
        call_user_func([$this, $method]);
        $this->registerVisitedCoordinates();
    }

    /**
     * Move one block North
     */
    protected function moveNorth()
    {
        $this->vertical--;
    }

    /**
     * Move one block East
     */
    protected function moveEast()
    {
        $this->horizontal++;
    }

    /**
     * Move one block South
     */
    protected function moveSouth()
    {
        $this->vertical++;
    }

    /**
     * Move one block West
     */
    protected function moveWest()
    {
        $this->horizontal--;
    }

    /**
     * Register the coordinates of each visited block until we get to the first place we have been before
     */
    protected function registerVisitedCoordinates()
    {
        // We have already found the first place we have been before. Don't waste time and resources tracking.
        if (!empty($this->easterBunnyHqActualDistance)) {
            return;
        }

        // This is the first place we're visiting twice, save the shortest distance to here
        if ($this->visitedCoordinates->contains([$this->vertical, $this->horizontal])) {
            $this->easterBunnyHqActualDistance = $this->getShortestDistanceInBlocks();
            return;
        }

        // Add the coordinates of this block to the visited coordinates
        $this->visitedCoordinates->push([$this->vertical, $this->horizontal]);
    }

    /**
     * The shortest distance in block to the current coordinates
     *
     * @return int
     */
    protected function getShortestDistanceInBlocks(): int
    {
        return abs($this->vertical) + abs($this->horizontal);
    }

    /**
     * @return string
     */
    public function getPathHtml()
    {
        return $this->pathHtml;
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getPuzzleInputFile(): string
    {
        return 'day1.txt';
    }
}