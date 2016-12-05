<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class DayThreeService extends AbstractService
{
    const NO_TRIANGLE_SIDES = 3;

    /**
     * DayThreeService constructor.
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
    }

    /**
     * Count how many three side combinations can create a triangle from rows
     *
     * @param bool $columns
     * @return int
     */
    public function countPossibleTriangles(bool $columns): int
    {
        // Part 1 of the puzzle
        if (!$columns) {
            return collect(explode(PHP_EOL, $this->puzzleInput))->filter(function ($line) {
                $sides = $this->getSidesFromLine($line);
                return $this->isTriangle($sides);
            })->count();
        }

        // Part 2 of the puzzle
        return collect(explode(PHP_EOL, $this->puzzleInput))->chunk(self::NO_TRIANGLE_SIDES)
            ->map(function ($threeLines) {
                /** @var Collection $threeLines */
                return $this->reduceToColumns($threeLines);
            })->flatMap(function ($threeColumns) {
                /** @var Collection $threeColumns */
                return $threeColumns->filter(function ($column) {
                    return $this->isTriangle($column);
                });
            })->count();
    }

    /**
     * Reduce the rows of three into groups of three numbers extracted from each column by descending the column
     *
     * @param Collection $threeLines
     * @return mixed
     */
    protected function reduceToColumns(Collection $threeLines)
    {
        $initial = collect([]);
        return $threeLines->reduce(function ($columns, $line) {
            /** @var Collection $sides */
            $sides = $this->getSidesFromLine($line);
            if ($sides->count() != self::NO_TRIANGLE_SIDES) {
                return $columns;
            }

            /** @var Collection $columns */
            if ($columns->isEmpty()) {
                return $sides->transform(function ($side) {
                    return collect([$side]);
                });
            }

            return $columns->transform(function ($column, $offset) use ($sides) {
                /** @var Collection $column */
                return $column->push($sides->offsetGet($offset));
            });
        }, $initial);
    }

    /**
     * Get a collection of side from a single line
     *
     * @param string $line
     * @return Collection
     */
    protected function getSidesFromLine(string $line)
    {
        $sides = preg_replace("/[\s]+/", ",", trim($line));
        return collect(explode(",", $sides))->transform(function ($side) {
            return intval($side);
        });
    }

    /**
     * Check if the three side lengths can create a triangle
     *
     * @param Collection $sides
     * @return bool
     */
    protected function isTriangle(Collection $sides): bool
    {
        if ($sides->count() != self::NO_TRIANGLE_SIDES) {
            return false;
        }

        list($side1, $side2, $side3) = $sides->toArray();
        return ($side1 + $side2 > $side3) && ($side1 + $side3 > $side2) && ($side2 + $side3 > $side1);
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getPuzzleInputFile(): string
    {
        return 'day3.txt';
    }
}