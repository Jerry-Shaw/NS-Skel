CREATE TABLE IF NOT EXISTS `app_register`
(
    `app_id`      INT(10) UNSIGNED NOT NULL COMMENT 'App ID',
    `app_name`    VARCHAR(16)      NOT NULL COMMENT 'App Name',
    `app_company` VARCHAR(32)      NOT NULL COMMENT 'Company Name',
    `add_time`    INT(10) UNSIGNED NOT NULL COMMENT 'Add time',
    PRIMARY KEY `app_id` (`app_id`)
) DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `app_channel`
(
    `ch_id`       INT(10) UNSIGNED    NOT NULL COMMENT 'Channel ID',
    `app_key`     CHAR(24)            NOT NULL COMMENT 'App Key',
    `app_secret`  CHAR(32)            NOT NULL COMMENT 'App Secret',
    `app_id`      INT(10) UNSIGNED    NOT NULL COMMENT 'App ID',
    `app_status`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status code',
    `app_comment` VARCHAR(256)        NOT NULL DEFAULT '' COMMENT 'Custom comment',
    `add_time`    INT(10) UNSIGNED    NOT NULL COMMENT 'Add time',
    PRIMARY KEY `ch_id` (`ch_id`),
    UNIQUE KEY `app_key` (`app_key`),
    KEY `app_id` (`app_id`)
) DEFAULT CHARSET = utf8mb4;
