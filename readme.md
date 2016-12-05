# Advent of Code 2016
My solutions built using PHP and Laravel 5.3.

Prerequisites:
* PHP >= 7.0 (scalar type hinting is used)
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Composer
* NPM

## Installation
After cloning the git repository, move into the directory where it was cloned.
Then, from the command line run the following
* composer install
* npm install
* gulp

## Getting solutions
From the command line run:
* php artisan aoc:1

Replace 1 with the relevant number for the day of the puzzle.
To get the solution to the second part of a puzzle just add an argument to the command like this: 
* php artisan aoc:1 part2