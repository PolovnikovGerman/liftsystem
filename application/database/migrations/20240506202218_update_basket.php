<?php

class Migration_update_basket extends CI_Migration {

    public function up() {

        $fields = array(
            'order_id' => array('type' => 'INT', 'constraint' => 11,'null' => TRUE,),
        );
        $this->dbforge->add_column('sb_baskets', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('sb_baskets', 'order_id');
    }

}