<?php

class Migration_alt_orders extends CI_Migration {

    public function up() {
        $fields = array(
            'debtstatus_date' => array('type' => 'INT', 'constraint' => 14, 'null' => true, 'comment' => 'Debt Status Date'),
        );
        $this->dbforge->add_column('ts_orders', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_orders', 'debtstatus_date');
    }

}