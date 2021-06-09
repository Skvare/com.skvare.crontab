ALTER TABLE civicrm_job ADD crontab_frequency varchar(255) COMMENT 'Crontab frequency pattern';
ALTER TABLE civicrm_job ADD crontab_offset varchar(10) COMMENT 'Crontab margin time in minutes';
ALTER TABLE civicrm_job ADD crontab_hour_range TINYINT(4) NULL DEFAULT NULL COMMENT 'Use Hour range to execute job';
