ALTER TABLE `ib_item_templates`
ADD COLUMN `baseValue` INT NOT NULL DEFAULT 0 COMMENT 'base value for price before any modifiers.'
AFTER `charimage`;

UPDATE ib_item_templates SET baseValue = attr1 WHERE attr1 > 0;
UPDATE ib_item_templates SET baseValue = 100 WHERE baseValue > 100;