<?php
require './db.class.php';

class RemoveImagePortfolio extends DB {

    function portfolio_remove() {
        if(!empty($_REQUEST['pp_id'])){
            $where = array('pp_id' => (int)$_REQUEST['pp_id']);
            
            $get_image = $this->get_record_where('provider_portfolio', $where);
            if(!empty($get_image)){
                unlink(dirname(__FILE__) . "/../uploads/portfolio/" . $get_image[0]['pp_image']);
                $remove = $this->delete_record('provider_portfolio', $where);
                
                $portfolio_list = array();
                $where_p = array('pp_provider' => (int)$get_image[0]['pp_provider']);
                $get_portfolio = $this->get_record_where('provider_portfolio', $where_p);
                if(!empty($get_portfolio)){
                    foreach ($get_portfolio as $value) {
                        $value['pp_image'] = $this->baseurl.UPLOADS.'/portfolio/'.$value['pp_image'];
                        unset($value['pp_datetime']);
                        $portfolio_list[] = $value;
                    }
                }
                
                $response = array('status' => 'true', 'message' => 'Image successfully removed', 'portfolio' => $portfolio_list);
            } else {
                $response = array('status' => 'false', 'message' => 'Image not found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$rm_port_img = new RemoveImagePortfolio();
$rm_port_img->portfolio_remove();