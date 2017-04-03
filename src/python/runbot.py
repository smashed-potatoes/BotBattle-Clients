import argparse

from battlebot.runner import Runner

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Run a bot against the BotBattle game')
    parser.add_argument('username', help='The user to log in as')
    parser.add_argument('bot', default='RandomBot', nargs='?', help='The name of the bot class to run')
    parser.add_argument('game_id', default=None, nargs='?', help='The ID of the game to join (use -1 to find a game)')
    parser.add_argument('difficulty', default=1, nargs='?', help='The difficulty of game to join')

    args = parser.parse_args()
    if (args.username == None):
        parser.print_help()
        exit(0)

    api_url = 'http://10.0.0.10/api/'

    runner = Runner(base_url=api_url, username=args.username, bot=args.bot, game_id=args.game_id, difficulty=args.difficulty)
    runner.run()