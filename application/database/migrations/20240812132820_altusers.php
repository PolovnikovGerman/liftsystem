<?php

class Migration_altusers extends CI_Migration {

    public function up() {
        $fields = array(
        );
        $this->dbforge->add_field(array(
            'print_scheduler' => array('type' => 'INT', 'constraint' => 1, 'default' => 0, 'null' => false, 'comment' => 'Include to scheduler Users'),
        ));
        $this->dbforge->add_column('users', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('users', 'print_scheduler');
    }

}