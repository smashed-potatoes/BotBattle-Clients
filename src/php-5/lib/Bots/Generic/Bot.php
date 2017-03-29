<?php namespace BotBattleClient\Bots\Generic;

/**
* Base class for a bot
*/
abstract class Bot {

    /**
    * Possible bot actions
    */
    const ACTION_NONE = 0;
    const ACTION_LEFT = 1;
    const ACTION_RIGHT = 2;
    const ACTION_UP = 3;
    const ACTION_DOWN = 4;

    protected $playerId;

    /**
    * The main bot function, called each turn
    * @return int The action to take, one of Bot::ACTION_NONE, Bot::ACTION_LEFT, Bot::ACTION_RIGHT, Bot::ACTION_UP, Bot::ACTION_DOWN
    */
    public abstract function move($state);

    /**
    * Set the player that represents the bot in the game
    */
    public function setPlayer($player) {
        $this->playerId = $player['id'];
    }
}