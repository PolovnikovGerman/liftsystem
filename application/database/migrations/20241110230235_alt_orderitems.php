<?php

class Migration_alt_orderitems extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('alt_orderitems');
    }

    public function down() {
        $this->dbforge->drop_table('alt_orderitems');
    }

}