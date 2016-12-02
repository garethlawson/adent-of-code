<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
use Illuminate\Filesystem\Filesystem;

class DayTwoService extends AbstractService
{
    /** Direction constants */
    const DIRECTION_UP    = 'U';
    const DIRECTION_RIGHT = 'R';
    const DIRECTION_DOWN  = 'D';
    const DIRECTION_LEFT  = 'L';

    /** @var \Illuminate\Support\Collection */
    protected $keypad;

    /** @var \Illuminate\Support\Collection */
    protected $actualKeypad;

    /** @var string */
    protected $input;

    /** @var int */
    protected $row;

    /** @var int */
    protected $column;

    /** @var Filesystem */
    protected $fileSystem;

    /** @var \Illuminate\Support\Collection */
    protected $directionMethodMap;

    /**
     * aocDayTwoService constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->fileSystem = $filesystem;
        $puzzleInputPath = $this->getPuzzleInputPath() . 'day2.txt';
        $this->input = $this->fileSystem->get($puzzleInputPath);

        // Puzzle 1 keypad: assumed keypad
        $this->keypad = collect([
            1 => collect([1 => 1, 2 => 2, 3 => 3]),
            2 => collect([1 => 4, 2 => 5, 3 => 6]),
            3 => collect([1 => 7, 2 => 8, 3 => 9]),
        ]);

        // Puzzle 2 keypad: actual keypad
        $this->actualKeypad = collect([
            1 => collect([3 => 1]),
            2 => collect([2 => 2, 3 => 3, 4 => 4]),
            3 => collect([1 => 5, 2 => 6, 3 => 7, 4 => 8, 5 => 9]),
            4 => collect([2 => 'A', 3 => 'B', 4 => 'C']),
            5 => collect([3 => 'D']),
        ]);

        // Initial keypad position: 5
        $this->row    = 2;
        $this->column = 2;

        // Map directions to methods
        $this->directionMethodMap = collect([
            self::DIRECTION_UP    => 'moveUp',
            self::DIRECTION_DOWN  => 'moveDown',
            self::DIRECTION_LEFT  => 'moveLeft',
            self::DIRECTION_RIGHT => 'moveRight',
        ]);
    }

    /**
     * Determine the code to get into the bathroom
     *
     * @param bool $actual
     * @return mixed
     */
    public function determineBathroomCode(bool $actual = false)
    {
        // For the second puzzle, replace the keypad with the actual keypad and reset the start position
        if ($actual) {
            $this->keypad = $this->actualKeypad;
            $this->row    = 3;
            $this->column = 1;
        }

        // Process the input line by line and reduce to a single text value
        return collect(explode(PHP_EOL, $this->input))->map(function ($line) {
            return $this->processLine($line);
        })->reduce(function ($carry, $key) {
            return $carry . $key;
        });
    }

    /**
     * Process each line and return the key that we end on for each line
     *
     * @param string $line
     * @return mixed
     */
    protected function processLine(string $line)
    {
        // Iterate over each character in the line and call the relevant move method
        collect(str_split($line))->each(function ($char) {
            call_user_func([$this, $this->directionMethodMap->get($char)]);
        });

        // Return the value of the key at the current position after processing the line
        return $this->keypad->get($this->row)->get($this->column);
    }

    /**
     * Move up on the keypad
     */
    protected function moveUp()
    {
        // Make sure a key exists above the current position
        if (!$this->keyExists($this->row - 1, $this->column)) {
            return;
        }

        $this->row--;
    }

    /**
     * Move down on the keypad
     */
    protected function moveDown()
    {
        // Make sure a key exists below the current position
        if (!$this->keyExists($this->row + 1, $this->column)) {
            return;
        }

        $this->row++;
    }

    /**
     * Move right on the keypad
     */
    protected function moveRight()
    {
        // Make sure a key exists right of the current position
        if (!$this->keyExists($this->row, $this->column + 1)) {
            return;
        }

        $this->column++;
    }

    /**
     * Move left on the keypad
     */
    protected function moveLeft()
    {
        if (!$this->keyExists($this->row, $this->column - 1)) {
            return;
        }

        $this->column--;
    }

    /**
     * Make sure a key exists at the given row and column
     *
     * @param int $row
     * @param int $column
     * @return bool
     */
    protected function keyExists(int $row, int $column): bool
    {
        if (empty($this->keypad->get($row))) {
            return false;
        }

        if (empty($this->keypad->get($row)->get($column))) {
            return false;
        }

        return true;
    }
}