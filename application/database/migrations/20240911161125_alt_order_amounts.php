<?php

class Migration_alt_order_amounts extends CI_Migration {

    public function up() {
        $fields = array(
            'order_itemcolor_id' => array('type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'order_id','comment' => 'Order Item color'),
        );
        $this->dbforge->add_field('CONSTRAINT amount_itemcolor_fk FOREIGN KEY (order_itemcolor_id) REFERENCES ts_order_itemcolors(order_itemcolor_id) on update cascade on delete cascade');
        $this->dbforge->add_column('ts_order_amounts', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('ts_order_amounts', 'order_itemcolor_id');
    }

}