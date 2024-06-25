<?php

class Migration_update_orders extends CI_Migration {

    public function up() {
        $fields = array(
            'debt_status' => array(
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => true,
                'comment' => 'Debt Conversation Status'
            )
        );
        $this->dbforge->add_column('ts_orders', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_orders', 'debt_status');
    }

}