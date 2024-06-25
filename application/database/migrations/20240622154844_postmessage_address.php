<?php

class Migration_postmessage_address extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'address_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'message_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Link to Post Message'
            ),
            'address_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => false,
                'comment' => 'Address Type',
            ),
            'address' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'comment' => 'Post Address'
            )
        ));
        $this->dbforge->add_key('address_id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (message_id) REFERENCES postbox_messages(message_id) on update cascade on delete set null');
        $this->dbforge->create_table('postmessage_address');
    }

    public function down() {
        $this->dbforge->drop_table('postmessage_address');
    }

}