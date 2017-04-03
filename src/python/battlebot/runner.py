import time
import requests

class Runner:
    STATE_WAITING = 0;
    STATE_RUNNING = 1;
    STATE_DONE = 2;


    def __init__(self, base_url, username, bot, game_id, difficulty):
        self.base_url = base_url
        self.username = username
        self.game_id = None if game_id == "None" or game_id == -1 or game_id == None else game_id
        self.difficulty = difficulty

        # Start a session using requests
        self.session = requests.session()

        # Dynamically load the bot
        module = __import__('bots', globals=globals())
        bot_class = getattr(module, bot)
        self.bot = bot_class()

    def run(self):

        # Log in
        if self.login() == False:
            return
        print 'Logged in as', self.username

        # Find/create a game
        if self.game_id == None and self.find_game() == False:
            return
        print 'Using game', self.game_id

        # Join the game
        if self.join_game() == False:
            return
        print 'Joined game', self.game_id

        # Run the bot
        game_state = self.get_game_state()
        turn = game_state['turn']
        while game_state['state'] != self.STATE_DONE:

            # Wait for the game to start
            while game_state['state'] == self.STATE_WAITING:
                time.sleep(1);
                game_state = self.get_game_state()
                print 'Waiting for players...'

            # Wait for all players to move
            while game_state['turn'] < turn:
                time.sleep(0.02);
                game_state = self.get_game_state()

            # Make a move
            action = self.bot.move(game_state)
            if self.move(action):
                turn += 1

            time.sleep(0.02);
            game_state = self.get_game_state()


        print 'Game ended'

    def move(self, action):
        response = self.session.post(self.base_url + '/games/' + str(self.game_id) + '/moves', json = { 'action': action })

        if response.status_code != 200:
            print 'Error moving: ' + response.text
            return False
        
        return True

    def get_game_state(self):
        response = self.session.get(self.base_url + '/games/' + str(self.game_id))

        if response.status_code != 200:
            print 'Error getting game state: ' + response.text
            return None

        return response.json()

    def login(self):
        response = self.session.post(self.base_url + '/users', json = { 'username': self.username })

        if response.status_code != 200:
            print 'Error logging in: ' + response.text
            return False
        
        return True

    def find_game(self):
        response = self.session.post(self.base_url + '/games', json = { 'difficulty': self.difficulty })

        if response.status_code != 200:
            print 'Error finding game: ' + response.text
            return False

        game = response.json()
        self.game_id = game['id']
        
        return True

    def join_game(self):
        response = self.session.post(self.base_url + '/games/' + str(self.game_id) + '/players')

        if response.status_code != 200:
            print 'Error joining game: ' + response.text
            return False

        player = response.json()
        self.bot.set_player(player)
        
        return True