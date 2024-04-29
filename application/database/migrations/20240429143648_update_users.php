<?php

class Migration_update_users extends CI_Migration {

    public function up() {
        $fields = array(
            'default_brand' => array('type' => 'VARCHAR', 'constraint' => '5', 'null' => false, 'default' => 'SB'),
        );
        $this->dbforge->add_column('users', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('users', 'default_brand');
    }

}