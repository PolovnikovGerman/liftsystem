<?php

class Migration_alter_orders extends CI_Migration {

    public function up() {
        $fields = array(
            'print_date' => array('type' => 'INT', 'constraint' => 14,'null' => TRUE, 'after' => 'shipdate','comment' => 'Print Schedule Date'),
            'print_user' => array('type' => 'INT', 'constraint' => 11,'null' => TRUE, 'after' => 'print_date','comment' => 'Print Assign User'),
            'print_ready' => array('type' => 'INT', 'constraint' => 14,'null' => FALSE, 'default' => 0,  'after' => 'print_user', 'comment' => 'Print Ready Date'),
            'shipping_ready' => array('type' => 'INT', 'constraint' => 14,'null' => FALSE, 'default' => 0,  'after' => 'print_ready', 'comment' => 'Shipping Ready Date'),
            'print_finish' => array('type' => 'INT', 'constraint' => 14,'null' => TRUE, 'after' => 'print_user','comment' => 'Print Job Finish Date'),
        );
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (print_user) REFERENCES users(user_id) on update cascade on delete set null');
        $this->dbforge->add_column('ts_orders', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_orders', 'print_user');
        $this->dbforge->drop_column('ts_orders', 'print_date');
        $this->dbforge->drop_column('ts_orders', 'print_finish');
        $this->dbforge->drop_column('ts_orders', 'print_ready');
        $this->dbforge->drop_column('ts_orders', 'shipping_ready');
    }

}