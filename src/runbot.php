<?php
require_once 'vendor/autoload.php';

use BotBattleClient\Runner;

/**
* Gets a command line argument
* @param int $index The index of the argument
* @param mixed $default The default value to return
*
* @return mixed The value passed for the gien argument or the default value
*/
function getArg(int $index, $default = null) {
    if ($_SERVER['argc'] > $index) {
        return $_SERVER['argv'][$index];
    }
    return $default;
}

if ($_SERVER['argc'] === 1) {
    echo "Usage:   ". $_SERVER['SCRIPT_FILENAME'] ." <username> [game_id] [difficulty] [bot_class]\n";
    echo "Example: ". $_SERVER['SCRIPT_FILENAME'] ." Bob null 1 RandomBot\n";
    exit(0);
}

// The API URL
$url        = 'http://10.0.0.10/api/';

// Get the passed settings
$username   = getArg(1, 'Random');
$gameId     = getArg(2, null);
$difficulty = getArg(3, 1);
$bot        = getArg(4, 'RandomBot');

$gameId = ($gameId !== null) && ($gameId !== "null") ? intval($gameId) : null;
$bot = "\\BotBattleClient\\Bots\\$bot";

// Start a bot runner
$runner = new Runner($url, $username, $difficulty, $bot, $gameId);
$runner->run();