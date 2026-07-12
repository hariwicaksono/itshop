<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateReviewsTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Drop the existing unique key on order_id if it exists
        // This allows multiple reviews per order (one per product)
        $db->query("ALTER TABLE `reviews` DROP INDEX `order_id`");
        
        // Add composite unique key on (order_id, product_id)
        $db->query("ALTER TABLE `reviews` ADD UNIQUE KEY `unique_order_product` (`order_id`, `product_id`)");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Drop the composite unique key
        $db->query("ALTER TABLE `reviews` DROP INDEX `unique_order_product`");
        
        // Re-add the unique key on order_id only
        $db->query("ALTER TABLE `reviews` ADD UNIQUE KEY `order_id` (`order_id`)");
    }
}
