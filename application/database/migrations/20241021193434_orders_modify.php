<?php

class Migration_orders_modify extends CI_Migration {

    public function up() {
        $fields = array(
            'debt_status' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
            ),
        );
        $this->dbforge->modify_column('ts_orders', $fields);
    }

    public function down() {
        // $this->dbforge->drop_table('orders_modify');
    }

}