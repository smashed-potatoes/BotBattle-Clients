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
function getArg($index, $default = null) {
    if ($_SERVER['argc'] > $index) {
        return $_SERVER['argv'][$index];
    }
    return $default;
}

if ($_SERVER['argc'] === 1) {
    echo "Usage:   ". $_SERVER['SCRIPT_FILENAME'] ." <username> [bot_class] [game_id] [difficulty]\n";
    echo "Example: ". $_SERVER['SCRIPT_FILENAME'] ." Bob RandomBot\n";
    exit(0);
}

// The API URL
$url        = 'http://www.battle.smashedtatoes.com/api/';

// Get the passed settings
$username   = getArg(1, 'Random');
$bot        = getArg(2, 'RandomBot');
$gameId     = getArg(3, null);
$difficulty = getArg(4, 1);

$gameId = ($gameId !== null) && ($gameId !== "null") ? intval($gameId) : null;
$bot = "\\BotBattleClient\\Bots\\$bot";

// Start a bot runner
$runner = new Runner($url, $username, $difficulty, $bot, $gameId);
$runner->run();
