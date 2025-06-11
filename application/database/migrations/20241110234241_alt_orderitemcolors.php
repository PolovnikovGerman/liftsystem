<?php

class Migration_alt_orderitemcolors extends CI_Migration {

    public function up() {
        $fields = array(
            'inventory_color_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
                'comment' => 'Inventory Color ID'
            )
        );
        $this->dbforge->add_column('ts_order_itemcolors', $fields);
        $this->db->query(add_foreign_key('ts_order_itemcolors', 'inventory_color_id', 'ts_inventory_colors(inventory_color_id)', 'SET NULL', 'CASCADE'));
    }

    public function down() {
        $this->db->query(drop_foreign_key('ts_order_itemcolors', 'inventory_color_id'));
        $this->dbforge->drop_column('ts_order_items', 'inventory_color_id');
    }

}