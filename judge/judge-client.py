#!/usr/bin/python

from subprocess32 import Popen, PIPE
from threading import Timer
from judge_ext import Judge
import sys


class UserProgram:
    def __init__(self, path):
        self.path = path
        self.process = None
        self.timeout = 1.0
        self.timer = None
        self.alive = True

    def execute(self):
        if not self.path:
            raise ValueError("Path cannot be empty.")
        self.process = Popen(self.path, stdin=PIPE, stdout=PIPE, stderr=PIPE)

    def start_timer(self):
        self.timer = Timer(self.timeout, lambda: self.kill_process())

    def stop_timer(self):
        self.timer.cancel()
        self.timer = None

    def read_line(self):
        return self.process.stdout.readline()

    def write_line(self, line):
        self.process.stdout.write(line)

    def kill_process(self):
        self.process.kill()
        self.alive = False


class Match:
    def __init__(self):
        self.started = False
        self.winner = None

    def end(self, winner):
        self.winner = winner
        print "Competition ended and the winner is %s"%winner
        quit()


match = Match()
attacker = UserProgram(sys.argv[1])
defender = UserProgram(sys.argv[2])

attacker_judge = Judge("attacker")
defender_judge = Judge("defender")


def run_step(attacker, attacker_judge, defender, defender_judge):
    try:
        result = attacker_judge.before_write()
        if result:
            match.end(attacker_judge.player_name if attacker_judge.victorious() else defender_judge.player_name)
        lines = attacker_judge.on_write()
        for line in lines:
            print >> attacker.process.stdin, line
        attacker.start_timer()
        line = attacker.read_line()
        attacker.stop_timer()
        result = attacker_judge.after_read(line.strip())
        if result:
            match.end(attacker_judge.player_name if attacker_judge.victorious() else defender_judge.player_name)
    except Exception as e:
        attacker.kill_process()
        raise e

attacker.execute()
defender.execute()
attacker_judge.started()
defender_judge.started()

while True:
    run_step(attacker, attacker_judge, defender, defender_judge)
    run_step(defender, defender_judge, attacker, attacker_judge)