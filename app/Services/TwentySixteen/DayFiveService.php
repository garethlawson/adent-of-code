<?php

namespace App\Services\TwentySixteen;

use App\Services\AbstractService;
use Illuminate\Filesystem\Filesystem;

class DayFiveService extends AbstractService
{
    const LOOP_LIMIT                = 30000000;
    const HASH_MATCH                = '00000';
    const PASSWORD_LENGTH           = 8;
    const HASH_CHARACTER_POSITION_1 = 5;
    const HASH_CHARACTER_POSITION_2 = 6;

    /**
     * DayFiveService constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
    }

    /**
     * Return the decrypted password to get through the door
     *
     * @param bool $part2
     * @return string
     */
    public function getPuzzleAnswer(bool $part2): string
    {
        $password    = [];
        $loopCounter = 0;
        // Iterate and increment a counter by one until we have an 8 letter password
        // or the LOOP_LIMIT is reached (prevent infinite loop)
        while (count($password) < self::PASSWORD_LENGTH && $loopCounter < self::LOOP_LIMIT) {
            // Generate an MD5 hash and increment the counter
            $hash = md5($this->puzzleInput . $loopCounter);
            $loopCounter++;

            // Make sure the hash meets the criteria, otherwise skip it
            if (!$this->isValidHash($hash, $part2)) {
                continue;
            }

            // For part 2, make sure we don't already have a letter in the relevant position
            if ($part2 && isset($password[intval($hash[self::HASH_CHARACTER_POSITION_1])])) {
                continue;
            }

            // Add the character to the password
            $this->addCharacterToPassword($password, $hash, $part2);
        }

        // Sort the array of letters by key and implode it to return the password as a string
        ksort($password);
        return implode($password);
    }

    /**
     * Check that the hash meets the given criteria for containing a password character
     *
     * @param string $hash
     * @param bool $part2
     * @return bool
     */
    protected function isValidHash(string $hash, bool $part2 = false): bool
    {
        // Part 2
        if ($part2) {
            return substr($hash, 0, self::HASH_CHARACTER_POSITION_1) === self::HASH_MATCH
                && preg_match("/^[0-7]$/", $hash[self::HASH_CHARACTER_POSITION_1]);
        }

        // Part 1
        return substr($hash, 0, self::HASH_CHARACTER_POSITION_1) === self::HASH_MATCH;
    }

    /**
     * Add a the relevant character from the hash to the relevant position in the password array
     *
     * @param array $password
     * @param string $hash
     * @param bool $part2
     * @return int
     */
    protected function addCharacterToPassword(array &$password, string $hash, bool $part2 = false)
    {
        // Part 2
        if ($part2) {
            return $password[intval($hash[self::HASH_CHARACTER_POSITION_1])] = $hash[self::HASH_CHARACTER_POSITION_2];
        }

        // Part 1
        return array_push($password, $hash[self::HASH_CHARACTER_POSITION_1]);
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function getPuzzleInputFile(): string
    {
        return 'day5.txt';
    }
}