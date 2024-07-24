<?php

class Migration_order_trackings extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'tracking_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'created_at' => array(
                'type' => 'timestamp',
                'null' => true,
                'comment' => 'Time added to system'
            ),
            'created_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Added to system USER'
            ),
            'updated_at datetime default current_timestamp',
            'updated_by' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Updated USER'
            ),
            'order_itemcolor_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Link with Order Item Color'
            ),
            'qty' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
                'comment' => 'QTY in package'
            ),
            'trackdate' => array(
                'type' => 'INT',
                'constraint' => 14,
                'null' => true,
                'comment' => 'Date of tracking'
            ),
            'trackservice' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'comment' => 'Tracking Service - UPS, etc',
            ),
            'trackcode' => array(
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'comment' => 'Tracking Code',
            )
        ));
        $this->dbforge->add_key('tracking_id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (created_by) REFERENCES users(user_id) on update cascade on delete set null');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (updated_by) REFERENCES users(user_id) on update cascade on delete set null');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (order_itemcolor_id) REFERENCES ts_order_itemcolors(order_itemcolor_id) on update cascade on delete cascade');
        $this->dbforge->create_table('ts_order_trackings');
    }

    public function down() {
        $this->dbforge->drop_table('ts_order_trackings');
    }

}