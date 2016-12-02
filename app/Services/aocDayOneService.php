<?php

namespace App\Services;

class aocDayOneService
{
    /** Direction contants */
    const DIRECTION_LEFT  = 'L';
    const DIRECTION_RIGHT = 'R';
    const DIRECTION_NORTH = 'N';
    const DIRECTION_EAST  = 'E';
    const DIRECTION_SOUTH = 'S';
    const DIRECTION_WEST  = 'W';

    /** HTML line constant */
    const HTML_LINE_VERTICAL   = '<div class="hidden" style="position: absolute; width: 1px; height: %spx; top: %spx; left: %spx; background: #000;"></div>';
    const HTML_LINE_HORIZONTAL = '<div class="hidden" style="position: absolute; width: %spx; height: 1px; top: %spx; left: %spx; background: #000;"></div>';

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
    protected $visitedCoords;

    /** @var int */
    protected $easterBunnyHqDistance;

    /** @var string */
    protected $pathHtml = '';

    /**
     * aocDayOneService constructor.
     */
    public function __construct()
    {
        // Initialise directions
        $this->directions = collect([
            'R3', 'L5', 'R2', 'L1', 'L2', 'R5', 'L2', 'R2', 'L2', 'L2', 'L1', 'R2', 'L2', 'R4', 'R4', 'R1', 'L2', 'L3',
            'R3', 'L1', 'R2', 'L2', 'L4', 'R4', 'R5', 'L3', 'R3', 'L3', 'L3', 'R4', 'R5', 'L3', 'R3', 'L5', 'L1', 'L2',
            'R2', 'L1', 'R3', 'R1', 'L1', 'R187', 'L1', 'R2', 'R47', 'L5', 'L1', 'L2', 'R4', 'R3', 'L3', 'R3', 'R4',
            'R1', 'R3', 'L1', 'L4', 'L1', 'R2', 'L1', 'R4', 'R5', 'L1', 'R77', 'L5', 'L4', 'R3', 'L2', 'R4', 'R5', 'R5',
            'L2', 'L2', 'R2', 'R5', 'L2', 'R194', 'R5', 'L2', 'R4', 'L5', 'L4', 'L2', 'R5', 'L3', 'L2', 'L5', 'R5',
            'R2', 'L3', 'R3', 'R1', 'L4', 'R2', 'L1', 'R5', 'L1', 'R5', 'L1', 'L1', 'R3', 'L1', 'R5', 'R2', 'R5', 'R5',
            'L4', 'L5', 'L5', 'L5', 'R3', 'L2', 'L5', 'L4', 'R3', 'R1', 'R1', 'R4', 'L2', 'L4', 'R5', 'R5', 'R4', 'L2',
            'L2', 'R5', 'R5', 'L5', 'L2', 'R4', 'R4', 'L4', 'R1', 'L3', 'R1', 'L1', 'L1', 'L1', 'L4', 'R5', 'R4', 'L4',
            'L4', 'R5', 'R3', 'L2', 'L2', 'R3', 'R1', 'R4', 'L3', 'R1', 'L4', 'R3', 'L3', 'L2', 'R2', 'R2', 'R2', 'L1',
            'L4', 'R3', 'R2', 'R2', 'L3', 'R2', 'L3', 'L2', 'R4', 'L2', 'R3', 'L4', 'R5', 'R4', 'R1', 'R5', 'R3']);

        // Initialise visited coordinates
        $this->visitedCoords = collect([
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

        $this->pathHtml .= '<div style="position: absolute; top: ' . $this->vertical . 'px; left: '. $this->horizontal .
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
        $htmlLineLength = $blocks * 1;
        $top = $this->vertical * 1;
        $left = $this->horizontal * 1;
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