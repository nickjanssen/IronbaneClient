ALTER TABLE `ib_items` ADD COLUMN `value` INT UNSIGNED NOT NULL DEFAULT 0  AFTER `slot` , 
ADD COLUMN `data` TEXT NULL  AFTER `value`;

UPDATE ib_items i SET value = (SELECT basevalue FROM ib_item_templates t WHERE t.id = i.template);