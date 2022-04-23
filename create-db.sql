CREATE TABLE IF NOT EXISTS `Permission`
(
    `id`            int NOT NULL AUTO_INCREMENT ,
    `internal_name` varchar(100) NOT NULL UNIQUE ,
    `description`   text NOT NULL ,

    PRIMARY KEY (`id`)
    );

CREATE TABLE IF NOT EXISTS `User`
(
    `id`            int NOT NULL AUTO_INCREMENT ,
    `username`      varchar(256) NOT NULL UNIQUE ,
    `email`         varchar(256) NOT NULL UNIQUE ,
    `password_hash` varbinary(60) NOT NULL ,
    `last_login`    datetime NULL ,

    PRIMARY KEY (`id`)
    );

CREATE TABLE IF NOT EXISTS `Tag`
(
    `id`   int NOT NULL ,
    `name` varchar(256) NOT NULL UNIQUE ,

    PRIMARY KEY (`id`)
    );

CREATE TABLE IF NOT EXISTS `Blog_Entry`
(
    `id`        int NOT NULL AUTO_INCREMENT ,
    `title`     varchar(256) NOT NULL UNIQUE ,
    `author_id` int NOT NULL ,
    `content`   text NOT NULL ,
    `published` datetime NOT NULL ,
    `edited`    datetime NOT NULL ,

    PRIMARY KEY (`id`),
    KEY `FK_26` (`author_id`),
    CONSTRAINT `FK_24` FOREIGN KEY `FK_26` (`author_id`) REFERENCES `User` (`id`)
    );

CREATE TABLE IF NOT EXISTS `Entry_Attachment`
(
    `id`       int NOT NULL AUTO_INCREMENT ,
    `type`     int NOT NULL ,
    `entry_id` int NOT NULL ,
    `url`      varchar(256) NOT NULL ,

    PRIMARY KEY (`id`),
    KEY `FK_74` (`entry_id`),
    CONSTRAINT `FK_72` FOREIGN KEY `FK_74` (`entry_id`) REFERENCES `Blog_Entry` (`id`)
    );

CREATE TABLE IF NOT EXISTS `Entry_To_Permission`
(
    `entry_id`      int NOT NULL ,
    `permission_id` int NOT NULL ,

    PRIMARY KEY (`entry_id`, `permission_id`),
    KEY `FK_43` (`entry_id`),
    CONSTRAINT `FK_41` FOREIGN KEY `FK_43` (`entry_id`) REFERENCES `Blog_Entry` (`id`),
    KEY `FK_50` (`permission_id`),
    CONSTRAINT `FK_48` FOREIGN KEY `FK_50` (`permission_id`) REFERENCES `Permission` (`id`)
    );

CREATE TABLE IF NOT EXISTS `Entry_To_Tag`
(
    `tag_id`   int NOT NULL ,
    `entry_id` int NOT NULL ,

    PRIMARY KEY (`tag_id`, `entry_id`),
    KEY `FK_62` (`tag_id`),
    CONSTRAINT `FK_60` FOREIGN KEY `FK_62` (`tag_id`) REFERENCES `Tag` (`id`),
    KEY `FK_66` (`entry_id`),
    CONSTRAINT `FK_64` FOREIGN KEY `FK_66` (`entry_id`) REFERENCES `Blog_Entry` (`id`)
    );

CREATE TABLE IF NOT EXISTS `Entry_View`
(
    `user_id`  int NOT NULL ,
    `entry_id` int NOT NULL ,
    `date`     datetime NOT NULL ,

    PRIMARY KEY (`user_id`, `entry_id`),
    KEY `FK_35` (`user_id`),
    CONSTRAINT `FK_33` FOREIGN KEY `FK_35` (`user_id`) REFERENCES `User` (`id`),
    KEY `FK_39` (`entry_id`),
    CONSTRAINT `FK_37` FOREIGN KEY `FK_39` (`entry_id`) REFERENCES `Blog_Entry` (`id`)
    );

CREATE TABLE IF NOT EXISTS `User_To_Permission`
(
    `user_id`       int NOT NULL ,
    `permission_id` int NOT NULL ,

    PRIMARY KEY (`user_id`, `permission_id`),
    KEY `FK_54` (`user_id`),
    CONSTRAINT `FK_52` FOREIGN KEY `FK_54` (`user_id`) REFERENCES `User` (`id`),
    KEY `FK_58` (`permission_id`),
    CONSTRAINT `FK_56` FOREIGN KEY `FK_58` (`permission_id`) REFERENCES `Permission` (`id`)
    );