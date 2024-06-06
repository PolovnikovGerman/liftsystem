<?php

class Migration_post_folders extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'folder_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            )
        ),
        array(
            'postbox_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Link to Post Box'
            )
        ),
        array(
            'folder_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 250,
                'null' => true,
                'comment' => 'Post Folder Name'
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('postbox_folders');
    }

    public function down() {
        $this->dbforge->drop_table('post_folders');
    }

}