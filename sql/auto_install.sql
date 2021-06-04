ALTER TABLE civicrm_job ADD crontab_frequency varchar(255) COMMENT 'Crontab frequency pattern';
ALTER TABLE civicrm_job ADD crontab_offset varchar(10) COMMENT 'Crontab margin time in minutes';
