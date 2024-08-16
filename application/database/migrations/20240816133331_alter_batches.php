<?php

class Migration_alter_batches extends CI_Migration {

    public function up() {
        $fields = array(
            'brand' => array('type' => 'VARCHAR', 'constraint' => 5, 'null' => true, 'comment' => 'Brand for Manual payments'
            )
        );
        $this->dbforge->add_column('ts_order_batches', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_order_batches', 'brand');
    }

}