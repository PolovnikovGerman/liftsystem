<?php

class Migration_postbox_attachments extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'attachment_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'message_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Link to message',
            ),
            'attachment_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'comment' => 'Attachment Name',
            ),
            'attachment_link' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'comment' => 'Attachment Local Copy',
            ),
            'attachment_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'comment' => 'Attachment Type',
            )
        ));
        $this->dbforge->add_key('attachment_id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (message_id) REFERENCES postbox_messages(message_id) on update cascade on delete set null');
        $this->dbforge->create_table('postbox_attachments');
    }

    public function down() {
        $this->dbforge->drop_table('postbox_attachments');
    }

}