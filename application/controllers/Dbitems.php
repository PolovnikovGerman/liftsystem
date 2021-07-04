<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for Vendor Center

class Dbitems extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');
    }

    // Items List
    public function itemlistsearch() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();

            $brand = ifset($postdata,'brand','ALL');
            $search = strtoupper(ifset($postdata, 'search', ''));
            $vendor = ifset($postdata,'vendor', '');
            $itemstatus = ifset($postdata, 'itemstatus', 0);

            $totals = $this->items_model->count_searchres($search, $brand, $vendor, $itemstatus);
            $mdata['totals'] = $totals;
            $mdata['totals_view'] = QTYOutput($totals).' Records';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemlistsdata() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $pagenum = ifset($postdata, 'offset', 0);
            $options = [];
            $options['limit'] = ifset($postdata, 'limit', 100);
            $options['offset'] = ($pagenum * $options['limit']);
            $options['order_by'] = ifset($postdata, 'order_by', 'item_number');
            $options['direct'] = ifset($postdata,'direction', 'asc');
            $options['brand'] = ifset($postdata,'brand','ALL');
            $options['search'] = strtoupper(ifset($postdata, 'search', ''));
            $options['vendor'] = ifset($postdata,'vendor', '');
            $options['itemstatus'] = ifset($postdata, 'itemstatus', 0);

            $res = $this->items_model->get_itemlists($options);
            $this->load->model('categories_model');
            $pageoptions = [
                'datas' => $res,
                'categories' => $this->categories_model->get_categories_list(),
                'brand' => $options['brand'],
            ];
            $mdata['content'] = $this->load->view('dbitems/itemslist_data_view', $pageoptions, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemlistcategory() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $this->load->model('itemcategory_model');
            $res = $this->itemcategory_model->update_itemlistcategory($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


}