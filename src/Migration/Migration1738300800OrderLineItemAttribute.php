<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1738300800OrderLineItemAttribute extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1738300800;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `fyrst_order_line_item_attribute` (
                `id` BINARY(16) NOT NULL,
                `key` VARCHAR(255) NOT NULL,
                `type` VARCHAR(255) NOT NULL,
                `input_settings` JSON NULL,
                `position` INT(11) NULL DEFAULT 0,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `required` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq.fyrst_order_line_item_attribute.key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `fyrst_order_line_item_attribute_translation` (
                `fyrst_order_line_item_attribute_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `label` VARCHAR(255) NULL,
                `description` LONGTEXT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`fyrst_order_line_item_attribute_id`, `language_id`),
                CONSTRAINT `fk.fyrst_order_line_item_attribute_translation.attribute_id`
                    FOREIGN KEY (`fyrst_order_line_item_attribute_id`)
                    REFERENCES `fyrst_order_line_item_attribute` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.fyrst_order_line_item_attribute_translation.language_id`
                    FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive changes
    }
}
