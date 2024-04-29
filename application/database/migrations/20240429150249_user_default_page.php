<?php

class Migration_user_default_page extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'user_page_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'brand' => array(
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => TRUE,
            ),
            'page_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            )

        ));
        $this->dbforge->add_key('user_page_id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (user_id) REFERENCES users(user_id) on update cascade on delete cascade');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (page_id) REFERENCES menu_items(menu_item_id) on update cascade on delete cascade');
        $this->dbforge->create_table('user_default_page');
    }

    public function down() {
        $this->dbforge->drop_table('user_default_page');
    }

}