Database was created with:

CREATE TABLE IF NOT EXISTS User (
  id INTEGER PRIMARY KEY,
  akronym TEXT KEY,
  name TEXT,
  email TEXT,
  password TEXT,
  created DATETIME default (datetime('now'))
);