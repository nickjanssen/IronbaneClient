ALTER TABLE `ib_item_templates`
ADD COLUMN `basevalue` INT NOT NULL DEFAULT 0 COMMENT 'base value for price before any modifiers.'
AFTER `charimage`;

UPDATE ib_item_templates SET basevalue = attr1 WHERE attr1 > 0;
UPDATE ib_item_templates SET basevalue = 100 WHERE basevalue > 100;