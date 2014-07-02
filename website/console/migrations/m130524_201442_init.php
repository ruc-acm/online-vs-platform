<?php

use yii\db\Schema;

class m130524_201442_init extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
            $query = <<< 'EOD'

CREATE TABLE IF NOT EXISTS executionRecord (
  id int(11) NOT NULL AUTO_INCREMENT,
  attackerId int(11) NOT NULL,
  defenderId int(11) NOT NULL,
  winner tinyint(4) NOT NULL DEFAULT '0',
  replay longtext,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY attackerId (attackerId),
  KEY defenderId (defenderId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- relation executionRecord:
--   attackerId
--       program -> id
--   defenderId
--       program -> id
--

-- --------------------------------------------------------

--
-- structure 'game'
--

CREATE TABLE IF NOT EXISTS game (
  id int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  displayName varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- structure 'program'
--

CREATE TABLE IF NOT EXISTS program (
  id int(11) NOT NULL AUTO_INCREMENT,
  userId int(11) NOT NULL,
  gameId int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  stability int(11) NOT NULL,
  lastCreated datetime NOT NULL,
  PRIMARY KEY (id),
  KEY userId (userId,gameId),
  KEY userId_2 (userId),
  KEY gameId (gameId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- relation program:
--   userId
--       user -> id
--   gameId
--       game -> id
--

-- --------------------------------------------------------

--
-- structure 'sourceCode'
--

CREATE TABLE IF NOT EXISTS sourceCode (
  programId int(11) NOT NULL AUTO_INCREMENT,
  `language` int(11) NOT NULL,
  `code` longtext NOT NULL,
  PRIMARY KEY (programId)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- relation sourceCode:
--   programId
--       program -> id
--

-- --------------------------------------------------------

--
-- structure 'user'
--

CREATE TABLE IF NOT EXISTS `user` (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  authKey varchar(32) NOT NULL,
  passwordHash varchar(255) NOT NULL,
  passwordResetToken varchar(255) DEFAULT NULL,
  email varchar(255) NOT NULL,
  role smallint(6) NOT NULL DEFAULT '10',
  `status` smallint(6) NOT NULL DEFAULT '10',
  lastCreated datetime NOT NULL,
  lastUpdated datetime NOT NULL,
  lastLogin datetime NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- structure 'userProfile'
--

CREATE TABLE IF NOT EXISTS userProfile (
  userId int(11) NOT NULL,
  nickName varchar(255) DEFAULT NULL,
  PRIMARY KEY (userId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- relation userProfile:
--   userId
--       user -> id
--

-- --------------------------------------------------------

--
-- structure 'userScore'
--

CREATE TABLE IF NOT EXISTS userScore (
  userId int(11) NOT NULL,
  gameId int(11) NOT NULL,
  rating int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (userId,gameId),
  KEY userId (userId),
  KEY gameId (gameId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- relation userScore:
--   userId
--       user -> id
--   gameId
--       game -> id
--

--
-- CONSTRAINTS
--

--
-- CONSTRAINT executionRecord
--
ALTER TABLE executionRecord
  ADD CONSTRAINT executionRecord_ibfk_1 FOREIGN KEY (attackerId) REFERENCES program (id) ON DELETE CASCADE,
  ADD CONSTRAINT executionRecord_ibfk_2 FOREIGN KEY (defenderId) REFERENCES program (id) ON DELETE CASCADE;

--
-- CONSTRAINT program
--
ALTER TABLE program
  ADD CONSTRAINT program_ibfk_1 FOREIGN KEY (userId) REFERENCES `user` (id) ON DELETE CASCADE,
  ADD CONSTRAINT program_ibfk_2 FOREIGN KEY (gameId) REFERENCES game (id) ON DELETE CASCADE;

--
-- CONSTRAINT sourceCode
--
ALTER TABLE sourceCode
  ADD CONSTRAINT sourceCode_ibfk_1 FOREIGN KEY (programId) REFERENCES program (id) ON DELETE CASCADE;

--
-- CONSTRAINT userProfile
--
ALTER TABLE userProfile
  ADD CONSTRAINT userProfile_ibfk_1 FOREIGN KEY (userId) REFERENCES `user` (id) ON DELETE CASCADE;

--
-- CONSTRAINT userScore
--
ALTER TABLE userScore
  ADD CONSTRAINT userScore_ibfk_1 FOREIGN KEY (userId) REFERENCES `user` (id) ON DELETE CASCADE,
  ADD CONSTRAINT userScore_ibfk_2 FOREIGN KEY (gameId) REFERENCES game (id) ON DELETE CASCADE;

EOD;
            $this->execute($query);
            return;
        }

        $this->createTable(
            '{{%user}}',
            [
                'id' => Schema::TYPE_PK,
                'username' => Schema::TYPE_STRING . ' NOT NULL',
                'authKey' => Schema::TYPE_STRING . '(32) NOT NULL',
                'passwordHash' => Schema::TYPE_STRING . ' NOT NULL',
                'passwordResetToken' => Schema::TYPE_STRING,
                'email' => Schema::TYPE_STRING . ' NOT NULL',
                'role' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
                'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
                'lastCreated' => Schema::TYPE_DATETIME . ' NOT NULL',
                'lastUpdated' => Schema::TYPE_DATETIME . ' NOT NULL',
            ],
            $tableOptions
        );
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%executionRecord}}');
        $this->dropTable('{{%userScour}}');
        $this->dropTable('{{%userProfile}}');
        $this->dropTable('{{%sourceCode}}');
        $this->dropTable('{{%program}}');
        $this->dropTable('{{%game}}');
    }
}
