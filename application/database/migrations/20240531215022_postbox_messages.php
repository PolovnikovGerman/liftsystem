<?php

class Migration_postbox_messages extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'message_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'postbox_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Link with postbox',
            ),
            'created_time' => array(
                'type' => 'timestamp',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Added to system'
            ),
            'message_subject' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Message Subject',
            ),
            'message_subject' =>
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('postbox_messages');
    }

    public function down() {
        $this->dbforge->drop_table('postbox_messages');
    }

}