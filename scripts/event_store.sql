CREATE TABLE `event_store` (
  `no` BIGINT(29) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `aggregate_id` VARCHAR(36) NOT NULL,
  `aggregate_type` VARCHAR(150) NOT NULL,
  `payload` JSON,
  `occurred_on` DATETIME NOT NULL,
  `version` BIGINT(29) NOT NULL DEFAULT 1,
  PRIMARY KEY (no),
  UNIQUE KEY `id_aggregate_id_type_name_version` (`name`, `aggregate_id`, `aggregate_type`, `version`),
  KEY `id_aggregate_id` (`aggregate_id`),
  KEY `id_aggregate_type` (`aggregate_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
