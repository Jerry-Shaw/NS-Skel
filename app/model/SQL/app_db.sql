CREATE TABLE IF NOT EXISTS `app_company`
(
    `co_id`      INT(10) UNSIGNED    NOT NULL COMMENT 'Company ID',
    `co_name`    VARCHAR(32)         NOT NULL COMMENT 'Company Name',
    `co_status`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status code',
    `co_comment` VARCHAR(256)        NOT NULL DEFAULT '' COMMENT 'Custom comment',
    `add_time`   INT(10) UNSIGNED    NOT NULL COMMENT 'Add time',
    PRIMARY KEY `co_id` (`co_id`)
) DEFAULT CHARSET = utf8mb4 COMMENT 'Company registry';

CREATE TABLE IF NOT EXISTS `app_register`
(
    `app_id`      INT(10) UNSIGNED    NOT NULL COMMENT 'App ID',
    `app_name`    VARCHAR(16)         NOT NULL COMMENT 'App Name',
    `app_status`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status code',
    `app_comment` VARCHAR(256)        NOT NULL DEFAULT '' COMMENT 'Custom comment',
    `co_id`       INT(10) UNSIGNED    NOT NULL COMMENT 'Company ID',
    `add_time`    INT(10) UNSIGNED    NOT NULL COMMENT 'Add time',
    PRIMARY KEY `app_id` (`app_id`),
    KEY `co_id` (`co_id`),
    KEY `app_status` (`app_status`)
) DEFAULT CHARSET = utf8mb4 COMMENT 'App registry';

CREATE TABLE IF NOT EXISTS `app_channel`
(
    `ch_id`       INT(10) UNSIGNED    NOT NULL COMMENT 'Channel ID',
    `ch_name`     VARCHAR(32)         NOT NULL COMMENT 'App Name',
    `app_id`      INT(10) UNSIGNED    NOT NULL COMMENT 'App ID',
    `app_key`     CHAR(24)            NOT NULL COMMENT 'App Key',
    `app_secret`  CHAR(32)            NOT NULL COMMENT 'App Secret',
    `app_status`  TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Status code',
    `app_comment` VARCHAR(256)        NOT NULL DEFAULT '' COMMENT 'Custom comment',
    `add_time`    INT(10) UNSIGNED    NOT NULL COMMENT 'Add time',
    PRIMARY KEY `ch_id` (`ch_id`),
    UNIQUE KEY `app_key` (`app_key`),
    KEY `app_id` (`app_id`)
) DEFAULT CHARSET = utf8mb4 COMMENT 'App channel';

CREATE TABLE IF NOT EXISTS `app_stats`
(
    `id`        INT(10) UNSIGNED NOT NULL COMMENT 'ID',
    `app_id`    INT(10) UNSIGNED NOT NULL COMMENT 'App ID',
    `app_key`   CHAR(24)         NOT NULL COMMENT 'App Key',
    `app_pv`    INT(10) UNSIGNED NOT NULL COMMENT 'App Page View',
    `app_uv`    INT(10) UNSIGNED NOT NULL COMMENT 'App Unique Visitor',
    `app_apa`   INT(10) UNSIGNED NOT NULL COMMENT 'App Active Payment Account',
    `app_dau`   INT(10) UNSIGNED NOT NULL COMMENT 'App Daily Active Users',
    `app_dec`   INT(10) UNSIGNED NOT NULL COMMENT 'App Daily Engagement Count',
    `app_dnu`   INT(10) UNSIGNED NOT NULL COMMENT 'App Daily New Users',
    `app_pcu`   INT(10) UNSIGNED NOT NULL COMMENT 'App Peak Concurrent Users',
    `app_day_1` INT(10) UNSIGNED NOT NULL COMMENT 'App Day 1 Retention Ratio',
    `app_day_3` INT(10) UNSIGNED NOT NULL COMMENT 'App Day 3 Retention Ratio',
    `app_day_7` INT(10) UNSIGNED NOT NULL COMMENT 'App Day 7 Retention Ratio',
    `add_time`  INT(10) UNSIGNED NOT NULL COMMENT 'Stats time',
    PRIMARY KEY `id` (`id`),
    UNIQUE KEY `app_id_key` (`app_id`, `app_key`)
) DEFAULT CHARSET = utf8mb4 COMMENT 'App stats data';
