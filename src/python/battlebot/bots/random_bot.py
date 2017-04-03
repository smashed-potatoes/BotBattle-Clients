import random

from bot import Bot

class RandomBot(Bot):
    def move(self, state):
        actions = [
            Bot.ACTION_NONE,
            Bot.ACTION_LEFT,
            Bot.ACTION_RIGHT,
            Bot.ACTION_UP,
            Bot.ACTION_DOWN
        ]
        return random.choice(actions)
