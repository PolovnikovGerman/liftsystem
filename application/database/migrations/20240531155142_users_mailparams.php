<?php

class Migration_users_mailparams extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'postbox_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ),
            'mailbox' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ),
            'postbox_user' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ),
            'postbox_passwd' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('postbox_id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (user_id) REFERENCES users(user_id) on update cascade on delete set null');
        $this->dbforge->create_table('user_postboxes');
    }

    public function down() {
        $this->dbforge->drop_table('user_postboxes');
    }

}