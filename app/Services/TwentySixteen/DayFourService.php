<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class DayFourService extends AbstractService
{
    const ALPHABET               = 'abcdefghijklmnopqrstuvwxyz';
    const NO_LETTERS_IN_ALPHABET = 26;

    /** @var Collection */
    protected $alphabet;

    /** @var Collection */
    protected $roomNameParts;

    /** @var int */
    protected $sectorId;

    /** @var string */
    protected $checksum;

    /**
     * DayFourService constructor.
     *
     * @param Filesystem $filesystem
     * @throws FileNotFoundException
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
        if (empty($this->puzzleInput)) {
            throw new FileNotFoundException("No puzzle input was found for this day");
        }

        $this->alphabet = collect(str_split(self::ALPHABET));
        $this->roomNameParts = collect();
    }

    /**
     * Get the puzzle answer and return the answer for part 1 or part 2 based on the value of the boolean parameter
     *
     * @param bool $part2
     * @return int
     */
    public function getPuzzleAnswer(bool $part2): int
    {
        // Part 1 of the puzzle
        if (!$part2) {
            return collect(explode(PHP_EOL, $this->puzzleInput))->filter(function ($encryptedRoomName) {
                return $this->isValidRoom($encryptedRoomName);
            })->reduce(function ($carry, $encryptedRoomName) {
                $this->splitRoomNameIntoParts($encryptedRoomName);
                return $carry + $this->sectorId;
            });
        }

        // Part 2 of the puzzle
        collect(explode(PHP_EOL, $this->puzzleInput))->filter(function ($encryptedRoomName) {
            return $this->isValidRoom($encryptedRoomName);
        })->first(function ($encryptedRoomName) {
            return $this->getNorthPoleStorageSectorId($encryptedRoomName);
        });

        return $this->sectorId;
    }

    /**
     * Check if this is a valid room or not
     *
     * @param string $encryptedRoomName
     * @return bool
     */
    protected function isValidRoom(string $encryptedRoomName): bool
    {
        $this->splitRoomNameIntoParts($encryptedRoomName);
        $generatedChecksum = $this->getGeneratedChecksum();
        return $this->checksum == $generatedChecksum;
    }

    /**
     * Split the given room name into its various parts for solving the puzzle
     *
     * @param string $encryptedRoomName
     */
    protected function splitRoomNameIntoParts(string $encryptedRoomName)
    {
        $roomNameParts = collect(explode('-', $encryptedRoomName));
        list($this->sectorId, $this->checksum) = $this->getSectorAndChecksum($roomNameParts->pop());
        $this->roomNameParts = $roomNameParts;
    }

    /**
     * Get the sector ID and checksum from the given string
     *
     * @param string $roomSectorAndChecksum
     * @return array
     */
    protected function getSectorAndChecksum(string $roomSectorAndChecksum)
    {
        preg_match("/^([0-9]+)\[(.*)\]$/", $roomSectorAndChecksum, $matches);
        list($wholeMatch, $sector, $checksum) = $matches;
        return [$sector, $checksum];
    }

    /**
     * Generate a checksum from the room name parts as per the puzzle instructions
     *
     * @return string
     */
    protected function getGeneratedChecksum(): string
    {
        // Reduce the parts into one long string of letters
        $letters = $this->roomNameParts->reduce(function ($carry, $part) {
            return $carry . $part;
        });

        // Split the letters and rank them, then sort them, take the top 5 and reduce them into a single checksum
        return collect(str_split($letters))->reduce(function ($rankedLetters, $letter) {
            /** @var Collection $rankedLetters */
            $letterCount = 0;
            if (!empty($rankedLetters->get($letter))) {
                $letterCount = $rankedLetters->get($letter)->get('letterCount');
            }

            $rankedLetters->put($letter, collect([
                'letter' => $letter, 'letterCount' => $letterCount + 1
            ]));

            return $rankedLetters;
        }, collect())->sort(function ($a, $b) {
            /** @var Collection $a */
            /** @var Collection $b */
            // The number of times the letter occurs in the string is equal, so order alphabetically
            if ($a->get('letterCount') === $b->get('letterCount')) {
                return $a->get('letter') < $b->get('letter') ? -1 : 1;
            }

            // Order by the number of times the letter occurs in the string
            return $a->get('letterCount') > $b->get('letterCount') ? -1 : 1;
        })->take(5)
            ->reduce(function ($carry, $letter) {
            /** @var Collection $letter */
            return $carry . $letter->get('letter');
        });
    }

    /**
     * Get the sector ID for where the North Pole items are being stored
     *
     * @param string $encryptedRoomName
     * @return bool
     */
    protected function getNorthPoleStorageSectorId(string $encryptedRoomName)
    {
        // Split the name into parts
        $this->splitRoomNameIntoParts($encryptedRoomName);

        // Decrypt the room name
        $roomName = $this->roomNameParts->reduce(function ($wholeName, $word){
            return $wholeName . " " . $this->getDecryptedWord($word);
        });

        // If lowercase the room name is "northpole object storage", end the iteration
        if (strpos(strtolower($roomName), "northpole object storage") !== false) {
            return true;
        }

        // Return false to continue iterating over room names
        return false;
    }

    /**
     * Decrypt each word in the room name
     *
     * @param string $word
     * @return string
     */
    protected function getDecryptedWord(string $word): string
    {
        // Split into letters and decrypt each letter
        return collect(str_split($word))->reduce(function ($word, $letter) {
            $currentLetterPosition = $this->alphabet->search($letter);
            $lettersToRotate       = intval($this->sectorId) % self::NO_LETTERS_IN_ALPHABET;
            $newLetterPosition     = $currentLetterPosition + $lettersToRotate;

            // The letter position is off the end of the alphabet, restart at the beginning
            if ($newLetterPosition >= self::NO_LETTERS_IN_ALPHABET) {
                $newLetterPosition -= self::NO_LETTERS_IN_ALPHABET;
            }

            return $word . $this->alphabet->get($newLetterPosition);
        });
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getPuzzleInputFile(): string
    {
        return 'day4.txt';
    }
}