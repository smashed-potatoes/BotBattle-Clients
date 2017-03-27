<?php namespace BotBattleClient;

use \GuzzleHttp\Client;
use \GuzzleHttp\Cookie\CookieJar;

/**
* Wrapper that runs a bot
*/
class Runner {

    const STATE_WAITING = 0;
    const STATE_RUNNING = 1;
    const STATE_DONE = 2;

    private $baseUrl;
    private $username;
    private $difficulty;
    private $client;
    private $bot;
    private $gameId;

    /**
    * Constructor
    * @param string $baseUrl The base API URL
    * @param string $username The username to use
    * @param int $difficulty The difficulty to run
    * @param string $botClass The Bot class to use
    * @param int $gameId The game to join
    */
    public function __construct(string $baseUrl, string $username, int $difficulty, string $botClass, int $gameId = null) {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->difficulty = $difficulty;
        $this->gameId = $gameId;

        $jar = new CookieJar();
        $this->client = new Client([
            'cookies' => true,
            'exceptions' => false
        ]);
        $this->log("Using bot: " . $botClass);
        $this->bot = new $botClass();
    }

    /**
    * Join a game and run the bot
    */
    public function run() {
        // Log in as the given user
        if (!$this->login()) {
            return;
        }
        $this->log("Logged in as " . $this->username);

        // Make sure we have a game
        if ($this->gameId == null && !$this->findGame()) {
            return;
        }
        $this->log("Using game " . $this->gameId);

        // Join the game
        if (!$this->joinGame()) {
            return;
        }
        $this->log("Joined game " . $this->gameId);

        // Run
        $gameState = $this->getGameState();
        $turn = $gameState['turn'];
        while ($gameState['state'] !== Runner::STATE_DONE) {

            // Wait for the game to start
            while ($gameState['state'] === Runner::STATE_WAITING) {
                $this->log("Waiting for players...");
                sleep(1);
                $gameState = $this->getGameState();
            }

            // Wait for all players to move
            while ($gameState['turn'] < $turn) {
                usleep(2000);
                $gameState = $this->getGameState();
            }

            // Make a move
            $action = $this->bot->move($gameState);
            if ($this->move($action)){
                $this->log("Move: $action");
                $turn++;
            }

            usleep(2000);
            $gameState = $this->getGameState();
        }

    }

    /**
    * Send a move to the server
    * @param int $action The move to send
    *
    * @return bool Whether sending was succesful or not
    */
    private function move(int $action) : bool {
        $response = $this->client->post($this->baseUrl . '/games/' . $this->gameId . '/moves', [
            'json' => ['action' => $action]
        ]);

        $code = $response->getStatusCode();
        if ($code !== 200) {
            $this->log("Error moving: $code - " . $response->getBody());
            return false;
        }

        $player = json_decode($response->getBody(), true);
        return true;
    }

    /**
    * Get the current game state
    * @return array|null The current game state
    */
    private function getGameState() {
        $response = $this->client->get($this->baseUrl . '/games/' . $this->gameId);

        $code = $response->getStatusCode();
        if ($code !== 200) {
            $this->log("Error getting game state in: $code - " . $response->getBody());
            return null;
        }

        return json_decode($response->getBody(), true);;
    }

    /**
    * Log in as the configured user
    * @return bool Whether logging in was succesful or not
    */
    private function login() : bool {
        $response = $this->client->post($this->baseUrl . '/users', [
            'json' => ['username' => $this->username]
        ]);

        $code = $response->getStatusCode();
        if ($code !== 200) {
            $this->log("Error logging in: $code - " . $response->getBody());
            return false;
        }

        return true;
    }

    /**
    * Find a game
    * @return bool Whether finding a game was succesful or not
    */
    private function findGame() : bool {
        $response = $this->client->post($this->baseUrl . '/games', [
            'json' => ['difficulty' => $this->difficulty]
        ]);

        $code = $response->getStatusCode();
        if ($code !== 200) {
            $this->log("Error finding game: $code - " . $response->getBody());
            return false;
        }

        $game = json_decode($response->getBody(), true);

        $this->gameId = $game['id'];
        return true;
    }

    /**
    * Join the selected game
    * @return bool Whether joining the game was succesful or not
    */
    private function joinGame() : bool {
        $response = $this->client->post($this->baseUrl . '/games/' . $this->gameId . '/players');

        $code = $response->getStatusCode();
        if ($code !== 200) {
            $this->log("Error joining game: $code - " . $response->getBody());
            return false;
        }

        $player = json_decode($response->getBody(), true);
        $this->bot->setPlayer($player);
        return true;
    }

    private function log($message) {
        echo "$message\n";
    }


}