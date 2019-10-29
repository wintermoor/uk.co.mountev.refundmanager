DROP TABLE IF EXISTS civicrm_credit_note;
CREATE TABLE IF NOT EXISTS `civicrm_credit_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `contribution_id` int(10) unsigned NOT NULL COMMENT 'FK to contribution',
  `credit_note_id` int(10) unsigned NOT NULL COMMENT 'FK to credit notes in contribution table',
  PRIMARY KEY ( `id` ),
  UNIQUE KEY `UI_contribution_creditnote` (`contribution_id`,`credit_note_id`),
  CONSTRAINT `FK_civicrm_credit_note_contribution_id` FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_civicrm_credit_note_credit_note_id` FOREIGN KEY (`credit_note_id`) REFERENCES `civicrm_contribution` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
