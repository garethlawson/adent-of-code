<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class DaySixService extends AbstractService
{
    /** @var Collection */
    protected $columnLetters;

    /** @var bool */
    protected $part2;

    /**
     * DaySixService constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);

        $this->columnLetters = collect();

        // Default to part 1
        $this->part2 = false;
    }

    /**
     * Return the decrypted message from Santa
     *
     * @param bool $part2
     * @return string
     */
    public function getPuzzleAnswer(bool $part2): string
    {
        // For part 2 of the puzzle, set a part2 property for access within a lambda
        $this->part2 = $part2;

        // Compile a Collection that contains columns each with a Collection indexed by letters with the number of
        // occurances of that letter as the value then map a new Collection that has been sorted by the most
        // common (part1) or the least common (part2) letter and take the first key (letter)
        return collect(explode(PHP_EOL, $this->puzzleInput))->reduce(function ($columnLetters, $line) {
            return $this->countColumnLetters($line, $columnLetters);
        }, collect())->map(function ($column) {
            return $this->sortColumnAndGetLetter($column);
        })->implode("");
    }

    /**
     * Count the number of times each letter occurs in each column
     *
     * @param string $line
     * @param Collection $columns
     * @return Collection
     */
    protected function countColumnLetters(string $line, Collection $columns): Collection
    {
        collect(str_split($line))->each(function ($letter, $column) use ($columns) {
            // Check if the column Collection has been created and if not create it
            $columnLetters = $columns->get($column, null);
            if (empty($columnLetters)) {
                $columnLetters = collect();
            }

            // Check if this letter has already been counted, and if not put zero at it's index
            $letterCount = $columnLetters->get($letter, null);
            if (empty($letterCount)) {
                $letterCount = 0;
            }

            // Increment the letter count and update the Collection to be returned by the calling lambda's reduce method
            $letterCount++;
            $columnLetters->put($letter, $letterCount);
            $columns->put($column, $columnLetters);
        });

        // Return the updated columns Collection
        return $columns;
    }

    /**
     * Get the most common (part1) or least common (part2) letter in each column. The letters are the keys, hence the
     * call to the keys() method, and the first key will be the most common letter for part 1 and the least common for
     * part 2 because the sorting functionality is adjusted based on which part of the puzzle we are solving
     *
     * @param Collection $column
     * @return string
     */
    protected function sortColumnAndGetLetter(Collection $column): string
    {
        return $column->sort(function ($a, $b) {
            // Letters occur the same number of times in the column
            if ($a === $b) {
                return 0;
            }

            // Sort letters
            return $this->sortLetters($a, $b);
        })->keys()->first();
    }

    /**
     * Sort the letters by most common (part1) or least common (part2)
     *
     * @param string $a
     * @param string $b
     * @return int
     */
    protected function sortLetters(string $a, string $b): int
    {
        // Part 1
        if (!$this->part2) {
            return $a < $b ? 1 : -1;
        }

        // Part 2
        return $a < $b ? -1 : 1;
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getPuzzleInputFile(): string
    {
        return 'day6.txt';
    }
}