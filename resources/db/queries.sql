-- #!db
-- #{ player

-- # { init
CREATE TABLE IF NOT EXISTS player (
    xuid VARCHAR(16) PRIMARY KEY,
    registerDate VARCHAR(18),
    username VARCHAR(16),
    ip VARCHAR(15),
	mobCoins BIGINT DEFAULT 0,
    money BIGINT DEFAULT 0,
    essence BIGINT DEFAULT 0,
    uluru BIGINT DEFAULT 0,
    rank VARCHAR(16) DEFAULT "Member",
    permissions TEXT,
    island VARCHAR(100) DEFAULT null,
    kills BIGINT DEFAULT 0,
    deaths BIGINT DEFAULT 0,
    killStreak BIGINT DEFAULT 0,
    slotCredits INT DEFAULT 0,
    jackpotWins INT DEFAULT 0,
	jackpotEarnings BIGINT DEFAULT 0,
    homes TEXT,
    levels TEXT,
	kitCds TEXT,
	rewardTime TEXT
)
-- # }

-- # { get
-- # 	:key string
SELECT * FROM player WHERE xuid = :key OR username = :key;
-- # }

-- # { allSortable
SELECT money, jackpotWins, jackpotEarnings, username FROM player;
-- # }

-- # { getAll
SELECT * FROM player;
-- # }

-- # { register
-- #    :xuid string
-- #    :registerDate string
-- #    :username string
-- #    :ip string
INSERT INTO player (
    xuid,
    registerDate,
    username,
    ip
) VALUES (
    :xuid,
    :registerDate,
    :username,
    :ip
)
-- # }

-- # { update
-- #    :username string
-- #    :ip string
-- #    :mobCoins int
-- #    :money int
-- #    :essence int
-- #    :uluru int
-- #    :rank string
-- #    :permissions string
-- #    :island string
-- # 	:kills int
-- # 	:deaths int
-- # 	:killStreak int
-- #    :slotCredits int
-- #    :jackpotWins int
-- #    :jackpotEarnings int
-- #    :homes string
-- #    :levels string
-- #    :kitCds string
-- #    :rewardTime string
-- #    :xuid string
UPDATE player SET username = :username, ip = :ip, mobCoins = :mobCoins, money = :money, essence = :essence, uluru = :uluru, rank = :rank, permissions = :permissions, island = :island, kills = :kills, deaths = :deaths, killStreak = :killStreak, slotCredits = :slotCredits, jackpotWins = :jackpotWins, jackpotEarnings = :jackpotEarnings, homes = :homes, levels = :levels, kitCds = :kitCds, rewardTime = :rewardTime WHERE xuid = :xuid;
-- # }

-- # { delete
-- # 	  :xuid string
DELETE FROM player WHERE xuid = :xuid;
-- # }
-- #}

-- #{ server

-- # { init
CREATE TABLE IF NOT EXISTS server (
	voteGoal INT
)
-- # }

-- # { get
SELECT * FROM server;
-- # }

-- # { update
-- #    :voteGoal string
UPDATE server SET voteGoal = :voteGoal;
-- # }

-- # { delete
DELETE FROM server;
-- # }
-- #}

-- #{ coinflips

-- # { init
CREATE TABLE IF NOT EXISTS coinflips (
	player VARCHAR(48) NOT NULL PRIMARY KEY,
	color VARCHAR(30) NOT NULL,
	amount INTEGER NOT NULL,
	used BOOLEAN DEFAULT 'false' NOT NULL
);
-- # }

-- # { update
-- #    :player string
-- #    :color string
-- #    :amount int
-- #    :used string
INSERT OR IGNORE INTO coinflips (
    player,
    color,
    amount,
    used
) VALUES (
    :player,
    :color,
    :amount,
    :used
)
-- # }

-- # { remove
-- #    :player string
DELETE FROM coinflips WHERE player = :player;
-- # }

-- # { getAll
SELECT * FROM coinflips;
-- # }
-- #}