<?php

class Migration_alt_itemcolors extends CI_Migration {

    public function up() {
        $fields = array(
            'print_date' => array('type' => 'INT', 'constraint' => 14, 'null' => FALSE, 'default' => 0, 'comment' => 'Include to Completed Print Job'),
            'print_completed' => array('type' => 'INT', 'constraint' => 1, 'null' => FALSE, 'default' => 0, 'comment' => 'Full Completed Print Job'),
        );
        $this->dbforge->add_column('ts_order_itemcolors', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_order_itemcolors', 'print_date');
        $this->dbforge->drop_column('ts_order_itemcolors', 'print_completed');
    }

}