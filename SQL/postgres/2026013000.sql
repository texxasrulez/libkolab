CREATE TABLE kolab_cache_dav_note (
    folder_id integer NOT NULL
        REFERENCES kolab_folders (folder_id) ON DELETE CASCADE ON UPDATE CASCADE,
    uid varchar(512) NOT NULL,
    etag varchar(128) NOT NULL,
    created timestamp with time zone DEFAULT NULL,
    changed timestamp with time zone DEFAULT NULL,
    data text NOT NULL,
    tags text NOT NULL,
    words text NOT NULL,
    PRIMARY KEY(folder_id, uid)
);
