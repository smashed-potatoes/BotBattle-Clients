<?php namespace BotBattleClient\Bots;

use BotBattleClient\Bots\Generic\Bot;

/**
* A bot that randomly moves
*/
class RandomBot extends Bot {

    /**
    * The main bot function, called each turn
    * @return int A random direction - Bot::ACTION_NONE, Bot::ACTION_LEFT, Bot::ACTION_RIGHT, Bot::ACTION_UP, Bot::ACTION_DOWN
    */
    public function move(array $state) : int {
        $actions = [
            Bot::ACTION_NONE => 0,
            Bot::ACTION_LEFT => 1,
            Bot::ACTION_RIGHT => 2,
            Bot::ACTION_UP => 3,
            Bot::ACTION_DOWN => 4
        ];

        return  $actions[array_rand($actions)];
    }
}