<?php

class Migration_update_basketlog extends CI_Migration {

    public function up() {
        $fields = array(
            'order_id' => array('type' => 'INT', 'constraint' => 11,'null' => TRUE,),
        );
        $this->dbforge->add_column('sb_basketchange_log', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('sb_basketchange_log', 'order_id');
    }

}