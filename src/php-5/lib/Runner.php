<?php namespace BotBattleClient;

use Guzzle\Http\Client;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;


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
    public function __construct($baseUrl, $username, $difficulty, $botClass, $gameId = null) {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->difficulty = $difficulty;
        $this->gameId = $gameId;


        $this->client = new Client(array(
            'exceptions' => false
        ));
        // Create a new cookie plugin
        $cookiePlugin = new CookiePlugin(new ArrayCookieJar());

        // Add the cookie plugin to the client
        $this->client->addSubscriber($cookiePlugin);

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
    private function move($action) {
        $request = $this->client->post($this->baseUrl . '/games/' . $this->gameId . '/moves',
            array('content-type' => 'application/json'), 
            json_encode(array('action' => $action))
        );

        $response = $request->send();
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
        $request = $this->client->get($this->baseUrl . '/games/' . $this->gameId );
        
        $response = $request->send();
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
    private function login() {
        $request = $this->client->post($this->baseUrl . '/users', 
            array('content-type' => 'application/json'), 
            json_encode(array('username' => $this->username))
        );

        $response = $request->send();
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
    private function findGame() {
        $request = $this->client->post($this->baseUrl . '/games', 
            array('content-type' => 'application/json'), 
            json_encode(array('difficulty' => $this->difficulty))
        );

        
        $response = $request->send();
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
    private function joinGame() {
        $request = $this->client->post($this->baseUrl . '/games/' . $this->gameId . '/players');
        $response = $request->send();
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