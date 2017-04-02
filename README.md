# BotBattle-Clients
Starter clients for the [BotBattle Project](https://github.com/smashed-potatoes/BotBattle)

## About
These are starter clients for the BotBattle project which is an API driven bot battle for exploring AI development, inspired by vindinium.

The purpose of the game is to create a bot that can outsmart the other players in a competition to navigate a grid of tiles and control the most gold resources for the most turns.

## Setup
The default setup is to run the project using vagrant. 
The network configuration is setup to work with the BotBattle vagrant box as well - they are on the same private network: `10.0.0.10` = server, `10.0.0.11` = client.

### PHP
All that is needed is to install the dependencies by running `composer install` in the `src/php` or `src/php-5` directory.

## Creating a bot
### PHP
A good starting point is to look at `RandomBot` implementation.
1. Create a new Bot class in `/lib/Bots` that extends `BotBattleClient\Bots\Generic\Bot`
1. Implement the `move` function
1. Update `runbot.php` to point to your BotBattle api (`$url = 'http://10.0.0.10/api/'`)
1. Run your bot
```bash
php runbot.php <username> <YourBotClass> [game_id|null] [difficulty(0-4)]
```
#### The `move` Function
The move function is where the core functionality of your bot goes. 
It is passed the current game state as an associative array on each turn (see the [Game State Example](https://github.com/smashed-potatoes/BotBattle-Clients#game-state-example) below) and must return what action to take.


#### Actions
| # | Action |
|---|--------|
| 0 | None |
| 1 | Left |
| 2 | Right |
| 3 | Up |
| 4 | Down |


### Game State Example
```json
{
  "id": 4,
  "board": {
    "id": 4,
    "width": 11,
    "height": 11,
    "tiles": [
      {
        "id": 364,
        "player": null,
        "type": 2,
        "x": 0,
        "y": 0
      },
      ...
    ],
    "healTiles": [
      {
        "id": 413,
        "player": null,
        "type": 3,
        "x": 4,
        "y": 5
      },
      ...
    ],
    "goldTiles": [
      {
        "id": 364,
        "player": null,
        "type": 2,
        "x": 0,
        "y": 0
      },
      ...
    ]
  },
  "players": [
    {
      "id": 5,
      "user": {
        "id": 5,
        "username": "Bob"
      },
      "x": 0,
      "y": 5,
      "health": 100,
      "points": 0
    },
    ...
  ],
  "maxPlayers": 2,
  "difficulty": 2,
  "state": 1,
  "turn": 0,
  "length": 500
}
```
