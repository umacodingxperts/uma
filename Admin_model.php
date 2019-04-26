<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : User_model (User Model)
 * User model class to get to handle user related data 
 * @author : Pankaj Saini
 * @version : 1.0
 * @since : 5 April 2019
 */
class Admin_model extends CI_Model
{
    public function addnewcategory()
    {
    	$category= ucfirst(strtolower($this->input->post('category')));
    	
    	$query = $this->db->get_where('tbl_categories', array('category_name' => $category));

        $count = $query->num_rows(); 
        if($count >= 1)
        {
        	return false;
        	
        }
        else
        {
        	$data = array(
				'category_name' => ucfirst(strtolower($this->input->post('category'))),
				'type' => 'product',
				'user_id' => $this->session->userdata('userId')
			    );
			return $this->db->insert('tbl_categories', $data);
        }
    }
    
    
    public function get_categories()
    {
        //$query = $this->db->query("select id,category_name from tbl_categories where parent_category_id = '0'");
        $query = $this->db->query("select id,category_name from tbl_categories");
			return $query->result();
    }
    
    public function addsubcategory()
    {
        	$data = array(
				'parent_category_id' => $this->input->post('parent_category'),
					'category_name' => ucfirst(strtolower($this->input->post('subcategory'))),
				'type' => 'product',
				'user_id' => $this->session->userdata('userId'),
				'created_at' => date("Y-m-d H:i:s")
			    );
			    return $this->db->insert('tbl_categories', $data);
        
    }
    public function get_sub_categories($id)
    {

        // $query = $this->db->query("select id,name from tbl_sub_categories where parent_category_id = '$id'");
         $query = $this->db->query("select id,category_name from tbl_categories where parent_category_id = '$id'");
         return $query->result();
		//print_r($query->result());
	    //die();
       
    }
    public function add_child_subcategory()
    {
        $data = array(
					'parent_category_id' => $this->input->post('sub'),
						'category_name' => ucfirst(strtolower($this->input->post('childcategory'))),
				'type' => 'product',
				'user_id' => $this->session->userdata('userId'),
				'created_at' => date("Y-m-d H:i:s")
			    );
			    return $this->db->insert('tbl_categories', $data);
    }
    
   public  function categoryListingCount($searchText = '')
    {
        //$this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.createdDtm, Role.role');
        //$this->db->from('tbl_users as BaseTbl');
        //$this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId','left');
        $this->db->select('id,category_name,parent_category_id,created_at,');
        $this->db->from('tbl_categories');
        if(!empty($searchText)) {
            $likeCriteria = "(category_name  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        //$this->db->where('BaseTbl.isDeleted', 0);
       // $this->db->where('BaseTbl.roleId !=', 1);
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
     public function categoryListing($searchText = '', $page, $segment)
    {
        $this->db->select('id,category_name,parent_category_id,created_at,');
        $this->db->from('tbl_categories');
        if(!empty($searchText)) {
            $likeCriteria = "(category_name  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        //$this->db->order_by('id', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        $result = $query->result();        
        return $result;
    }
    
    public  function getCategoryInfo($id)
    {
        $this->db->select('id, category_name, user_id, type, created_at');
        $this->db->from('tbl_categories');
        //$this->db->where('isDeleted', 0);
		//$this->db->where('roleId !=', 1);
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        return $query->row();
    }
    
    public function update_category($id)
    {
        $data = array(
            
				'category_name' => ucfirst(strtolower($this->input->post('name'))),
				'type' => 'product',
				'user_id' => $this->session->userdata('userId'),
				'created_at' => date("Y-m-d H:i:s")
			    );
        
        $this->db->where('id', $id);
        $this->db->update('tbl_categories', $data);
        
        return TRUE;
    }
    
    public function delete_category($id)
    {
        	$this->db->where('id', $id);
			$this->db->delete('tbl_categories');
			return true;
        
    }
    
    public function create_shipping()
    {
        $data = array(
				'driver_name' =>ucfirst(strtolower($this->input->post('driver'))) ,
				'pick_up_point' => $this->input->post('pickup'),
				'drop_off_point' => $this->input->post('drop'),
				'pick_up_time' =>date('Y-m-d H:i:s',strtotime($this->input->post('picktime'))),
				'drop_off_time' =>date('Y-m-d H:i:s',strtotime($this->input->post('droptime'))),
				'shipping_code' => $this->input->post('shipcode'),
			    'address' => $this->input->post('address'),
			    );
			    //print_r($data);
			    //die;
			    return $this->db->insert('tbl_shipping', $data);
    }
    
    public  function shippingListingCount($searchText = '')
    {
        $this->db->select('*');
        $this->db->from('tbl_shipping');
        if(!empty($searchText)) {
            $likeCriteria =  "(driver_name  LIKE '%".$searchText."%'
                            OR  pick_up_point  LIKE '%".$searchText."%'
                            OR  drop_off_point  LIKE '%".$searchText."%'
                            OR  address LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    public function shippingListing($searchText = '', $page, $segment)
    {
        $this->db->select('*');
        $this->db->from('tbl_shipping');
        if(!empty($searchText)) {
            $likeCriteria =  "(driver_name  LIKE '%".$searchText."%'
                            OR  pick_up_point  LIKE '%".$searchText."%'
                            OR  drop_off_point  LIKE '%".$searchText."%'
                            OR  address LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        $result = $query->result();        
        return $result;
    }
    
    public function check_username_exists($username)
    {
       	$query = $this->db->get_where('tbl_register', array('username' => $username));

		if(empty($query->row_array()))
		{
			return true;
		}
		else
		{
			return false;
		}
    }
    
    public function check_email_exists($email)
    {
		$query = $this->db->get_where('tbl_register', array('email' => $email));

		if(empty($query->row_array()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

    public function add_shop()
    {
        $data = array(
                 'store_type' => ucfirst(strtolower($this ->input->post('Store'))),
                'shop_name' => ucfirst(strtolower($this ->input->post('shopename'))),
                'company_name' =>ucfirst(strtolower($this ->input->post('companyname'))),
                'about_us' => $this ->input->post('about'),
                'mobile' => $this ->input->post('mobile'),
                'city' => $this ->input->post('city'),
                'website' => $this ->input->post('website'),
                'facebook_page' => $this ->input->post('fbpage'),
                'twitter_page' => $this ->input->post('twitter'),
                'google_plus_page' => $this ->input->post('gplus'),
                'instagram_page' => $this ->input->post('instagram'),
                'youtube_page' => $this ->input->post('youtube'),
                'created_at' => date("Y-m-d H:i:s")
                );
                //print_r($data);
                //die;
                return $this->db->insert('tbl_shop', $data);
    }
        
    
    
    
    
    
    
}
?>