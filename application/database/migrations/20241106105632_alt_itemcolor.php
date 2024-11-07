<?php

class Migration_alt_itemcolor extends CI_Migration {

    public function up() {
        $fields = array(
            'shipping_ready' => array('type' => 'INT', 'constraint' => 14, 'null' => FALSE, 'default' => 0, 'comment' => 'Include to Ready to Ship'),
        );
        $this->dbforge->add_column('ts_order_itemcolors', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_order_itemcolors', 'shipping_ready');
    }

}