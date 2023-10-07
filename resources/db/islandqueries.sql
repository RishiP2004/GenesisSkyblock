-- #!db
-- #{ islands
-- # { init
CREATE TABLE IF NOT EXISTS islands (
	id VARCHAR(16) PRIMARY KEY,
	name VARCHAR(18),
    type VARCHAR(16),
	leader VARCHAR(16),
	members TEXT,
	roles TEXT,
	spawn TEXT,
	defaultRole VARCHAR(18) DEFAULT "Member",
	locked INT DEFAULT 0,
	upgrades TEXT,
	value INT,
    power INT,
	xp INT,
	level INT,
	stats TEXT
)
-- # }
-- # { register
-- #    :id string
-- #    :name string
-- #    :leader string
-- #    :type string
INSERT INTO islands (
	id,
	name,
	leader,
	type
) VALUES (
	:id,
	:name,
	:leader,
	:type
)
-- # }

-- # { get
-- # 	:key string
SELECT * FROM islands WHERE id = :key OR name = :key;
-- # }

-- # { top
SELECT name, value, leader, members FROM islands;
-- # }

-- # { getAll
SELECT * FROM islands;
-- # }

-- # { update
-- #    :name string
-- #    :leader string
-- #    :members string
-- #    :roles string
-- #    :spawn string
-- #    :defaultRole string
-- #    :locked int
-- #    :upgrades string
-- # 	:value int
-- #    :power int
-- # 	:xp int
-- #    :level int
-- #    :stats string
-- #    :id string
UPDATE islands SET name = :name, leader = :leader, members = :members, roles = :roles, spawn = :spawn, defaultRole = :defaultRole, locked = :locked, upgrades = :upgrades, value = :value, power = :power, xp = :xp, level = :level, stats = :stats WHERE id = :id;
-- # }

-- # { delete
-- # 	  :id string
DELETE FROM islands WHERE id = :id;
-- # }

-- #}