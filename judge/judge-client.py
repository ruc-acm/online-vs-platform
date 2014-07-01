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
        self.tle = False

    def execute(self):
        if not self.path:
            raise ValueError("Path cannot be empty.")
        self.process = Popen(self.path, stdin=PIPE, stdout=PIPE, stderr=PIPE)

    def start_timer(self):
        self.timer = Timer(self.timeout, lambda: self.time_limit_exceeded())
        self.timer.start()

    def stop_timer(self):
        self.timer.cancel()
        self.timer = None

    def read_line(self):
        return self.process.stdout.readline()

    def write_line(self, line):
        self.process.stdout.write(line)

    def kill_process(self):
        try:
            self.process.kill()
        except Exception:
            pass

    def time_limit_exceeded(self):
        self.tle = True
        self.kill_process()


class Match:
    def __init__(self):
        self.started = False
        self.winner = None
        self.reason = None

    def end(self, winner):
        global attacker, defender
        self.winner = winner
        print >> sys.stderr, "Competition ended and the winner is %s" % winner
        print >> sys.stderr, "Reason is %s" % self.reason
        attacker.kill_process()
        defender.kill_process()
        quit()

    def time_limit_exceeded(self):
        self.reason = "TLE"

    def defeat(self, return_code=1):
        if return_code == 1:
            self.reason = "Finished"
        elif return_code == 2:
            self.reason = "Illegal Movement"
        elif return_code == 3:
            self.reason = "Illegal Output"
        else:
            self.reason = "Unknown"

    def crashed(self):
        self.reason = "Crashed"


match = Match()
attacker = UserProgram(sys.argv[1])
defender = UserProgram(sys.argv[2])

attacker_judge = Judge("attacker")
defender_judge = Judge("defender")


def run_step(attacker, attacker_judge, defender, defender_judge):
    global match
    try:
        result = attacker_judge.before_write()
        if result:
            match.defeat(result)
            match.end(attacker_judge.player_name if attacker_judge.victorious() else defender_judge.player_name)
        lines = attacker_judge.on_write()
        for line in lines:
            print >> attacker.process.stdin, line
        attacker.start_timer()
        line = attacker.read_line()
        if not line:
            if attacker.process.poll():
                raise Exception('Process ended.')
        attacker.stop_timer()
        result = attacker_judge.after_read(line.strip())
        if result:
            match.defeat(result)
            match.end(attacker_judge.player_name if attacker_judge.victorious() else defender_judge.player_name)
    except Exception as e:
        match.crashed() if attacker.process.returncode != -9 else match.time_limit_exceeded()
        match.end(defender_judge.player_name)


attacker.execute()
defender.execute()
attacker_judge.started()
defender_judge.started()

while True:
    run_step(attacker, attacker_judge, defender, defender_judge)
    run_step(defender, defender_judge, attacker, attacker_judge)
