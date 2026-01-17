CREATE TABLE IF NOT EXISTS `#__slt_comments` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL DEFAULT 1,
`ordering` INT(11)  NULL DEFAULT 0,
`created_by` INT(11)  NULL DEFAULT 0,
`modified_by` INT(11)  NULL DEFAULT 0,
`date_creation` DATETIME NULL DEFAULT NULL ,
`name_author` VARCHAR(255)  NOT NULL ,
`comment` MEDIUMTEXT NULL  DEFAULT "",
`id_content` INT NULL  DEFAULT 0,
`id_parent` INT(10)  NULL DEFAULT 0,
`likes` INT DEFAULT 0,
`dislikes` INT DEFAULT 0,
`uid` VARCHAR(255) NULL DEFAULT NULL,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__slt_comments_name_author` ON `#__slt_comments`(`name_author`);

CREATE INDEX `#__slt_comments_comment` ON `#__slt_comments`(`comment`);

CREATE INDEX `#__slt_comments_id_content` ON `#__slt_comments`(`id_content`);

CREATE INDEX `#__slt_comments_id_parent` ON `#__slt_comments`(`id_parent`);


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Comment','com_slt_comments.comment','{"special":{"dbtable":"#__slt_comments","key":"id","type":"CommentTable","prefix":"Joomla\\\\Component\\\\Slt_comments\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_slt_comments\/forms\/comment.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_slt_comments.comment')
) LIMIT 1;