<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;

abstract class AbstractService
{
    /** @var Filesystem */
    protected $fileSystem;

    /** @var string */
    protected $puzzleInput;

    /**
     * AbstractService constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->fileSystem  = $filesystem;
        if (!$this->fileSystem->exists($this->getPuzzleInputPath() . $this->getPuzzleInputFile())) {
            return;
        }

        $this->puzzleInput = $this->fileSystem->get(
            $this->getPuzzleInputPath() . $this->getPuzzleInputFile()
        );
    }

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

    /**
     * Get the name of the file that contains the puzzle input
     *
     * @return mixed
     */
    abstract protected function getPuzzleInputFile(): string;
}