--- PostgreSQL
DELETE FROM groups;
ALTER SEQUENCE groups_id_seq RESTART;
UPDATE groups SET id = DEFAULT;

DELETE FROM users;
ALTER SEQUENCE users_id_seq RESTART;
UPDATE users SET id = DEFAULT;

--- SQLite
DELETE FROM groups;
delete from sqlite_sequence where name='groups';
DELETE FROM users;
delete from sqlite_sequence where name='users';