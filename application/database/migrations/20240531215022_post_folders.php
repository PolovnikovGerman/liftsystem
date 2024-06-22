<?php

class Migration_post_folders extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'folder_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'postbox_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Link to Post Box'
            ),
            'folder_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'comment' => 'Post Folder Name'
            ),
            'folder_messages' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
                'comment' => '# of messages in folder'
            ),
        ));
        $this->dbforge->add_key('folder_id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (postbox_id) REFERENCES user_postboxes(postbox_id) on update cascade on delete set null');
        $this->dbforge->create_table('postbox_folders');
    }

    public function down() {
        $this->dbforge->drop_table('post_folders');
    }

}