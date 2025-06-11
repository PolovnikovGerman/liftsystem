<?php

class Migration_alt_vendor_parner extends CI_Migration {

    public function up() {
        $fields = array(
            'partner' => array('type' => 'INT', 'constraint' => 1, 'null' => false, 'default' => 0, 'comment' => 'Flag of important vendor - 1 - important'),
        );
        $this->dbforge->add_column('vendors', $fields);
        $this->db->set('partner',1);
        $this->db->where_in('vendor_name', array('Ariel','Alpi','Mailine','Pinnacle','Hit','Bella','Asher','Bonnie','DHL','UPS','INTERNAL'));
        $this->db->update('vendors');
    }

    public function down() {
        $this->dbforge->drop_column('ts_order_amounts', 'partner');
    }

}