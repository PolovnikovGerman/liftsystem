<?php

class Migration_postbox_messages extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'message_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'folder_id' => array(
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
            'message_from' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'comment' => 'From',
            ),
            'message_to' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'comment' => 'From',
            ),
            'message_date' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Message Date',
            ),
            'postmessage_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Message ID in email system'
            ),
            'message_text' => array(
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Message Text',
            ),
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (folder_id) REFERENCES postbox_folders(folder_id) on update cascade on delete set null');
        $this->dbforge->create_table('postbox_messages');
    }

    public function down() {
        $this->dbforge->drop_table('postbox_messages');
    }

}