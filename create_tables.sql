CREATE TABLE IF NOT EXISTS `Permission`
(
    `id`            int          NOT NULL AUTO_INCREMENT,
    `internal_name` varchar(100) NOT NULL UNIQUE,
    `description`   text         NOT NULL,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `Category`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(256) NOT NULL UNIQUE,
    `parent_id` int DEFAULT NULL,

    KEY (`parent_id`),
    FOREIGN KEY (`parent_id`) REFERENCES `Category` (`id`) ON DELETE CASCADE,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `User`
(
    `id`            int           NOT NULL AUTO_INCREMENT,
    `username`      varchar(256)  NOT NULL UNIQUE,
    `email`         varchar(256)  NOT NULL UNIQUE,
    `password_hash` varbinary(60) NOT NULL,
    `last_login`    datetime      NULL,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `Tag`
(
    `id`   int          NOT NULL AUTO_INCREMENT,
    `name` varchar(256) NOT NULL UNIQUE,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `Blog_Entry`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `title`       varchar(256) NOT NULL UNIQUE,
    `author_id`   int          NOT NULL,
    `content`     text         NOT NULL,
    `published`   datetime     NOT NULL,
    `edited`      datetime     NOT NULL,
    `category_id` int          NOT NULL,

    PRIMARY KEY (`id`),
    KEY (`author_id`),
    KEY (`category_id`),
    FOREIGN KEY (`author_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `Category` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `Entry_Comment`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `author_id` int          NOT NULL,
    `entry_id`  int          NOT NULL,
    `published` datetime     NOT NULL,
    `edited`    datetime     NOT NULL,
    `text`      varchar(256) NOT NULL,

    PRIMARY KEY (`id`),
    KEY (`author_id`),
    FOREIGN KEY (`author_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
    KEY (`entry_id`),
    FOREIGN KEY (`entry_id`) REFERENCES `Blog_Entry` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `Entry_To_Permission`
(
    `entry_id`      int NOT NULL,
    `permission_id` int NOT NULL,

    PRIMARY KEY (`entry_id`, `permission_id`),
    KEY (`entry_id`),
    FOREIGN KEY (`entry_id`) REFERENCES `Blog_Entry` (`id`) ON DELETE CASCADE,
    KEY (`permission_id`),
    FOREIGN KEY (`permission_id`) REFERENCES `Permission` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `Entry_To_Tag`
(
    `tag_id`   int NOT NULL,
    `entry_id` int NOT NULL,

    PRIMARY KEY (`tag_id`, `entry_id`),
    KEY (`tag_id`),
    FOREIGN KEY (`tag_id`) REFERENCES `Tag` (`id`) ON DELETE CASCADE,
    KEY (`entry_id`),
    FOREIGN KEY (`entry_id`) REFERENCES `Blog_Entry` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `Entry_View`
(
    `user_id`  int      NOT NULL,
    `entry_id` int      NOT NULL,
    `date`     datetime NOT NULL,

    PRIMARY KEY (`user_id`, `entry_id`),
    KEY (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
    KEY (`entry_id`),
    FOREIGN KEY (`entry_id`) REFERENCES `Blog_Entry` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `User_To_Permission`
(
    `user_id`       int NOT NULL,
    `permission_id` int NOT NULL,

    PRIMARY KEY (`user_id`, `permission_id`),
    KEY (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE,
    KEY (`permission_id`),
    FOREIGN KEY (`permission_id`) REFERENCES `Permission` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `Entry_Image_Attachment`
(
    `id`       int           NOT NULL AUTO_INCREMENT,
    `entry_id` int           NOT NULL,
    `url`      VARCHAR(1024) NOT NULL,

    PRIMARY KEY (`id`),
    KEY (`entry_id`),
    FOREIGN KEY (`entry_id`) REFERENCES `Blog_Entry` (`id`) ON DELETE CASCADE
);