#!/usr/bin/python

import tempfile
import os
import random
import string
import codecs
import shutil
import sys
import MySQLdb
from redis import Redis

from subprocess32 import Popen, PIPE


class CompileErrorException(Exception):
    pass


class JudgeClientException(Exception):
    pass


class ExecutionRecord:
    FETCH_QUERY = "SELECT executionRecord.id, executionRecord.status, user1.id AS attackerUserId, sc1.language AS attackerLanguage, sc1.code AS attackerCode, user2.id AS defenderUserId, sc2.language AS defenderLanguage, sc2.code AS defenderCode , attackerId, defenderId FROM executionRecord, program AS p1 , program AS p2 , sourceCode AS sc1 , sourceCode AS sc2 , user AS user1 , user AS user2 WHERE executionRecord.attackerId = p1.id AND p1.id = sc1.programId AND executionRecord.defenderId = p2.id AND p2.id = sc2.programId AND p1.userId = user1.id AND p2.userId = user2.id AND executionRecord.id = %s"
    UPDATE_QUERY = "UPDATE executionRecord SET status = %s , winner = %s , replay = %s , log = %s WHERE id = %s"
    UPDATE_STATUS_QUERY = "UPDATE executionRecord SET status = %s WHERE id = %s"

    TRANSACTION_BEGIN_QUERY = "BEGIN TRANSACTION"
    TRANSACTION_COMMIT_QUERY = "COMMIT"

    GET_MAX_SCORE_QUERY = "SELECT MAX(rating) FROM userScore"
    GET_USER_SCORE_QUERY = "SELECT rating FROM userScore WHERE userId = %s FOR UPDATE"
    CHECK_EXISTENT_QUERY = 'SELECT COUNT(*) FROM executionRecord, program WHERE executionRecord.id <> %s AND defenderId = %s AND attackerId = program.id AND program.userId = %s AND winner = 1 AND status <> 7'
    UPDATE_USER_SCORE_QUERY = "UPDATE userScore SET rating = rating + (%s) WHERE userId = %s"

    STATUS_PENDING = 0
    STATUS_RUNNING = 1
    STATUS_FINISHED = 2
    STATUS_RUNTIME_ERROR = 3
    STATUS_TLE = 4
    STATUS_ILLEGAL_MOVE = 5
    STATUS_BAD_FORMAT = 6
    STATUS_COMPILE_ERROR = 7
    STATUS_INTERNAL_ERROR = -1
    STATUS_REJECTED = -2

    WINNER_ATTACKER = 1
    WINNER_DEFENDER = 2

    def __init__(self, program_id):
        self.connection = None
        self.connection = MySQLdb.connect(
            db='ovs',
            user='ovs',
            passwd='online-vs-platform',
            charset='utf8',
            use_unicode=True
        )
        cur = self.connection.cursor()
        cur.execute(self.FETCH_QUERY, [program_id])
        result = cur.fetchone()
        cur.close()
        (self.id, self.status,
         self.attacker_id, self.attacker_lang, self.attacker_code,
         self.defender_id, self.defender_lang, self.defender_code, self.attacker_program_id,
         self.defender_program_id) = result
        self.log = []
        self.winner = 0
        self.replay = []

    def update_status(self, status):
        self.status = status
        cur = self.connection.cursor()
        cur.execute(self.UPDATE_STATUS_QUERY, (self.status, self.id))
        cur.close()
        self.connection.commit()

    def attacker_wins(self):
        self.winner = self.WINNER_ATTACKER

    def defender_wins(self):
        self.winner = self.WINNER_DEFENDER

    def ranking_change(self):
        if self.winner == self.WINNER_ATTACKER and self.status != ExecutionRecord.STATUS_COMPILE_ERROR:  # we don't count on compile errors
            cur = self.connection.cursor()
            cur.execute(self.CHECK_EXISTENT_QUERY, (self.id, self.defender_program_id, self.attacker_id,))
            result = cur.fetchone()
            if not result[0]:
                cur.execute(self.GET_USER_SCORE_QUERY, (self.attacker_id,))
                attacker_score = cur.fetchone()[0]
                cur.execute(self.GET_USER_SCORE_QUERY, (self.defender_id,))
                defender_score = cur.fetchone()[0]
                cur.execute(self.GET_MAX_SCORE_QUERY)
                max_score = cur.fetchone()[0]
                delta = min(((attacker_score - defender_score) / (1.0 * max_score)) ** 2, max_score / 4.0)
                delta = max(1, delta)
                cur.execute(self.UPDATE_USER_SCORE_QUERY, (delta, self.attacker_id,))
            cur.close()

    def save_to_database(self):
        cur = self.connection.cursor()
        cur.execute(self.UPDATE_QUERY,
                    (self.status, self.winner, '\n'.join(self.replay), '\n'.join(self.log), self.id))
        cur.close()
        self.connection.commit()


def __del__(self):
    if self.connection is not None:
        self.connection.close()


def random_string(length=32):
    return ''.join(random.choice(string.letters + string.digits) for i in range(length))


class Execution:
    COMPILE_TIMEOUT = 10

    def __init__(self, record):
        """

        :type record: ExecutionRecord
        """
        self.record = record
        self.base_dir = os.path.join(tempfile.gettempdir(), 'judge-' + random_string())
        os.mkdir(self.base_dir)
        self.log = []

    def __del__(self):
        shutil.rmtree(self.base_dir, ignore_errors=True)

    def run_compiler(self, language, filename, executable_name):
        args = ["g++" if language else "gcc", "-static", "-w", "-O2", filename, "-o",
                executable_name]
        self.log += ['Running: ' + ' '.join(args)]
        proc = Popen(args,
                     cwd=self.base_dir, stdin=PIPE, stdout=PIPE, stderr=PIPE)
        output = proc.communicate(timeout=self.COMPILE_TIMEOUT)
        self.log += [str(output[1])]
        if proc.poll() is None:
            try:
                self.log += ['Compile timeout.']
                proc.kill()
            except Exception:
                pass
        self.log += ["Compiler returns %d." % proc.returncode]
        if proc.returncode:
            raise CompileErrorException()

    def compile(self):
        try:
            attacker_code_path = os.path.join(self.base_dir,
                                              'attacker-' + random_string(8) + ('.cpp' if self.record.attacker_lang else '.c'))
            attacker_code_file = codecs.open(attacker_code_path, 'w', 'utf8')
            attacker_code_file.write(self.record.attacker_code)
            attacker_code_file.close()
            self.run_compiler(self.record.attacker_lang, os.path.basename(attacker_code_path), 'attacker')
        except Exception as e:
            self.record.defender_wins()
            raise e
        try:
            defender_code_path = os.path.join(self.base_dir,
                                              'defender-' + random_string(8) + ('.cpp' if self.record.defender_lang else '.c'))
            defender_code_file = codecs.open(defender_code_path, 'w', 'utf-8')
            defender_code_file.write(self.record.defender_code)
            defender_code_file.close()
            self.run_compiler(self.record.defender_lang, os.path.basename(defender_code_path), 'defender')
        except Exception as e:
            self.record.attacker_wins()
            raise e

    def copy_assets(self):
        if os.path.exists('./assets/'):
            path = os.path.realpath('./assets/')
            self.log += ['Copying assets files.']
            for file in os.listdir(path):
                file_path = os.path.join(path, file)
                if os.path.isfile(file_path):
                    target = os.path.join(self.base_dir, file)
                    self.log += ['Copying ' + file_path + ' to ' + target]
                    shutil.copyfile(file_path, target)
                    self.log += ['Assets files copied.']

    def run_judge_client(self):
        self.copy_assets()
        args = ["python", os.path.realpath('judge-client.py'), './attacker', './defender']
        self.log += ['Running: ' + ' '.join(args)]
        proc = Popen(args, cwd=self.base_dir, stdin=PIPE, stdout=PIPE, stderr=PIPE)
        output = proc.communicate()
        self.log += [str(output[1])]
        if proc.returncode:
            self.log += ["Judge client crashed with return code %d." % proc.returncode]
            raise JudgeClientException("judge client crashed.")
        result = output[0].split('\n')
        winner = result[0]
        if winner == "attacker":
            self.record.attacker_wins()
        elif winner == "defender":
            self.record.defender_wins()
        else:
            self.log += ["Judge client return unknown winner %s." % winner]
            raise JudgeClientException("unknown winner.")
        reason = result[1]
        if reason == "Finished":
            self.record.status = ExecutionRecord.STATUS_FINISHED
        elif reason == "IllegalMovement":
            self.record.status = ExecutionRecord.STATUS_ILLEGAL_MOVE
        elif reason == "IllegalOutput":
            self.record.status = ExecutionRecord.STATUS_BAD_FORMAT
        elif reason == "TLE":
            self.record.status = ExecutionRecord.STATUS_TLE
        elif reason == "Crashed":
            self.record.status = ExecutionRecord.STATUS_RUNTIME_ERROR
        else:
            self.log += ["Judge client return unknown reason %s." % reason]
            raise JudgeClientException("unknown reason.")
        self.record.replay = result[2:]

    def save_to_database(self):
        self.save_log()
        self.record.save_to_database()

    def save_log(self):
        self.record.log = self.log


def judge_by_id(program_id):
    record = ExecutionRecord(program_id)
    execution = Execution(record)
    try:
        execution.record.update_status(ExecutionRecord.STATUS_RUNNING)
        execution.compile()
        execution.run_judge_client()
    except CompileErrorException:
        execution.record.status = ExecutionRecord.STATUS_COMPILE_ERROR
        execution.log += ['Judge process terminated due to a compile error.']
    except Exception as e:
        execution.record.status = ExecutionRecord.STATUS_INTERNAL_ERROR
        execution.log += ['Exception caught in judge daemon.', e.message]
    finally:
        execution.record.ranking_change()
        execution.save_to_database()


def run_daemon():
    redis = Redis()
    while True:
        try:
            to_do = redis.brpop(['judge_queue'])
            program_id = to_do[1]
            judge_by_id(int(program_id))
        except Exception as e:
            print >> sys.stderr, e


os.environ['LANG'] = 'C'
os.environ['LD_LIBRARY_PATH'] = os.path.curdir
run_daemon()
