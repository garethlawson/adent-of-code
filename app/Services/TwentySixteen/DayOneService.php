<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
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

    /** HTML line constant */
    const HTML_LINE_VERTICAL   = '<div class="hidden line" style="position: absolute; width: 1px; height: %spx; top: %spx; left: %spx; background: #000;"></div>';
    const HTML_LINE_HORIZONTAL = '<div class="hidden line" style="position: absolute; width: %spx; height: 1px; top: %spx; left: %spx; background: #000;"></div>';

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

    /** @var int */
    protected $easterBunnyHqDistance;

    /** @var string */
    protected $pathHtml = '';

    /**
     * aocDayOneService constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        // Initialise directions
        $puzzleInputPath = $this->getPuzzleInputPath() . 'day1.txt';
        $this->directions = collect(explode(", ", $filesystem->get($puzzleInputPath)));

        // Initialise visited coordinates
        $this->visitedCoordinates = collect([
            '0,0',
        ]);

        // Intialise current coordinates and direction
        $this->vertical   = 0;
        $this->horizontal = 0;

        $this->direction = self::DIRECTION_NORTH;

        // Initialise the direction matrix
        $this->directionMatrix = collect([
            self::DIRECTION_NORTH => collect([
                self::DIRECTION_LEFT => self::DIRECTION_WEST,
                self::DIRECTION_RIGHT => self::DIRECTION_EAST,
            ]),
            self::DIRECTION_EAST => collect([
                self::DIRECTION_LEFT => self::DIRECTION_NORTH,
                self::DIRECTION_RIGHT => self::DIRECTION_SOUTH,
            ]),
            self::DIRECTION_SOUTH => collect([
                self::DIRECTION_LEFT => self::DIRECTION_EAST,
                self::DIRECTION_RIGHT => self::DIRECTION_WEST,
            ]),
            self::DIRECTION_WEST => collect([
                self::DIRECTION_LEFT => self::DIRECTION_SOUTH,
                self::DIRECTION_RIGHT => self::DIRECTION_NORTH,
            ]),
        ]);

        $this->pathHtml = '<div style="position: absolute; top: 400px; left:400px; width:4px; height: 4px; border-radius:2px; background: #666;"></div>';
    }

    /**
     * Find the Easter Bunny HQ part 1
     *
     * @return number
     */
    public function findEasterBunnyHq1()
    {
        $this->directions->each(function ($move) {
            $direction = substr($move, 0, 1);
            $blocks    = intval(substr($move, 1, strlen($move)));

            $this->recalculateCoordinates($direction, $blocks);
        });

        $this->pathHtml .= '<div style="position: absolute; top: ' . (self::HTML_POSITION_START + $this->vertical * 1.5) . 'px;'
            . ' left: '. (self::HTML_POSITION_START + $this->horizontal * 1.5) .
            'px; width:4px; height: 4px; border-radius:2px; background: green;"></div>';
        return $shortestDistance = abs($this->vertical) + abs($this->horizontal);
    }

    /**
     * @return string
     */
    public function getPathHtml()
    {
        return $this->pathHtml;
    }

    /**
     * Find the Easter Bunny HQ part 1
     *
     * @return number
     */
    public function findEasterBunnyHq2()
    {
        return $this->easterBunnyHqDistance;
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
        $htmlLineLength = $blocks * 1.5;
        $top = $this->vertical * 1.5;
        $left = $this->horizontal * 1.5;
        switch ($newDirection) {
            case self::DIRECTION_NORTH:
                $this->pathHtml .= sprintf(
                    self::HTML_LINE_VERTICAL,
                    $htmlLineLength,
                    ($top + self::HTML_POSITION_START) - $htmlLineLength,
                    $left + self::HTML_POSITION_START
                );
                $this->vertical -= $blocks;
                break;
            case self::DIRECTION_EAST:
                $this->pathHtml .= sprintf(
                    self::HTML_LINE_HORIZONTAL,
                    $htmlLineLength,
                    $top + self::HTML_POSITION_START,
                    $left + self::HTML_POSITION_START
                );
                $this->horizontal += $blocks;
                break;
            case self::DIRECTION_SOUTH:
                $this->pathHtml .= sprintf(
                    self::HTML_LINE_VERTICAL,
                    $htmlLineLength,
                    $top + self::HTML_POSITION_START,
                    $left + self::HTML_POSITION_START
                );
                $this->vertical += $blocks;
                break;
            case self::DIRECTION_WEST:
                $this->pathHtml .= sprintf(
                    self::HTML_LINE_HORIZONTAL,
                    $htmlLineLength,
                    $top + self::HTML_POSITION_START,
                    ($left + self::HTML_POSITION_START) - $htmlLineLength
                );
                $this->horizontal -= $blocks;
                break;
        }

        if ($this->visitedCoordinates->contains($this->vertical . "," . $this->horizontal) && empty($this->easterBunnyHqDistance)) {
            $this->easterBunnyHqDistance = abs($this->vertical) + abs($this->horizontal);
        }

        $this->visitedCoordinates->push($this->vertical . "," . $this->horizontal);
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

    /**
     * @inheritdoc
     * @return string
     */
    protected function getPuzzleInputFile(): string
    {
        return 'day1.txt';
    }
}