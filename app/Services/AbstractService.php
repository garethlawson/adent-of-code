<?php

namespace App\Services;

abstract class AbstractService
{
    /**
     * Get the path where the puzzle input is stored
     *
     * @return string
     */
    public function getPuzzleInputPath()
    {
        return resource_path(
            'assets' . DIRECTORY_SEPARATOR . 'puzzle_input' . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR
        );
    }
}