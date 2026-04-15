CREATE TABLE kolab_cache_dav_note (
  folder_id INTEGER NOT NULL,
  uid VARCHAR(512) NOT NULL,
  etag VARCHAR(128) NOT NULL,
  created DATETIME DEFAULT NULL,
  changed DATETIME DEFAULT NULL,
  data TEXT NOT NULL,
  tags TEXT NOT NULL,
  words TEXT NOT NULL,
  PRIMARY KEY(folder_id, uid)
);
