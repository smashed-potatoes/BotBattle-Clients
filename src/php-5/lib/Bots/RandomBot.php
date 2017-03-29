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
    public function move($state) {
        $actions = array(
            Bot::ACTION_NONE,
            Bot::ACTION_LEFT,
            Bot::ACTION_RIGHT,
            Bot::ACTION_UP,
            Bot::ACTION_DOWN
        );

        return  $actions[array_rand($actions)];
    }
}