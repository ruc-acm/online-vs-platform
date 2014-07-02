#!/usr/bin/python

FETCH_QUERY = "SELECT executionRecord.id, executionRecord.status, user1.id AS attackerUserId, sc1.language AS attackerLanguage, sc1.code AS attackerCode, user2.id AS defenderUserId, sc2.language AS defenderLanguage, sc2.code AS defenderCode FROM executionRecord, program AS p1 , program AS p2 , sourceCode AS sc1 , sourceCode AS sc2 , user AS user1 , user AS user2 WHERE executionRecord.attackerId = p1.id AND p1.id = sc1.programId AND executionRecord.defenderId = p2.id AND p2.id = sc2.programId AND p1.userId = user1.id AND p2.userId = user2.id"

import MySQLdb

while True: