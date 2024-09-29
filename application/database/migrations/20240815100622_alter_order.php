<?php

class Migration_alter_orders extends CI_Migration {

    public function up() {
        $fields = array(
            'print_finish' => array('type' => 'INT', 'constraint' => 14,'null' => FALSE, 'default' => 0, 'after' => 'shipping_ready', 'comment' => 'Print Job Completed Date'),
            'shipped_date' => array('type' => 'INT', 'constraint' => 14,'null' => FALSE, 'default' => 0, 'after' => 'print_finish', 'comment' => 'Shipped Date'),
        );
        $this->dbforge->add_column('ts_orders', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_orders', 'shipped_date');
        $this->dbforge->drop_column('ts_orders', 'print_finish');
    }

}