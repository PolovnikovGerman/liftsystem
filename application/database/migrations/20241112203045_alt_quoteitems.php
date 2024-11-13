<?php

class Migration_alt_quoteitems extends CI_Migration {

    public function up() {
        $fields = array(
            'inventory_item_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
                'comment' => 'Inventory item ID'
            )
        );
        $this->dbforge->add_column('ts_quote_items', $fields);
        $this->db->query(add_foreign_key('ts_quote_items', 'inventory_item_id', 'ts_inventory_items(inventory_item_id)', 'SET NULL', 'CASCADE'));
    }

    public function down() {
        $this->db->query(drop_foreign_key('ts_quote_items', 'inventory_item_id'));
        $this->dbforge->drop_column('ts_quote_items', 'inventory_item_id');
    }

}