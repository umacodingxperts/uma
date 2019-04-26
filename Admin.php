<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Login (LoginController)
 * Login class to control to authenticate user credentials and starts user's session.
 * @author : Pankaj Saini
 * @version : 1.0
 * @since : 5 April 2019
 */

class Admin extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->isLoggedIn();   
    }
    
    public function add_category()
    {
        $this->global['pageTitle'] = 'Tafa2na : Add-Categories';
        
        $this->loadViews("admin-panel/add-category", $this->global, NULL , NULL);
        
    }
    
    public function addnewcategory()
    {
      if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
           $this->form_validation->set_rules('category', 'Category Name', 'required');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->add_category();
            }
            else
            {
                
                $this->load->model('Admin_model');
                $result = $this->Admin_model->addnewcategory();
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'Category Created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Category already exist');
                }
                
                redirect('add_category');
            }
        }
    }
    
    public function add_sub_category()
    {
        $this->load->model('Admin_model');
        $data['category'] = $this->Admin_model->get_categories();
        $this->global['pageTitle'] = 'Tafa2na : Add-Sub-Categories';
        $this->loadViews("admin-panel/add-sub-category", $this->global, $data , NULL);
    }
    
    public function add_new_sub_category()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
           $this->form_validation->set_rules('parent_category', 'Parent Category Name', 'required');
           $this->form_validation->set_rules('subcategory', 'sub Category Name', 'required');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->add_sub_category();
            }
            else
            {
                $this->load->model('Admin_model');
                $result = $this->Admin_model->addsubcategory();
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'Sub Category created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Sub Category creation failed');
                }
                
                redirect('add_sub_category');
            }
        }
    }
    
    public function child_sub_category()
    {
        
        $this->load->model('Admin_model');
        $data['category'] = $this->Admin_model->get_categories();
        //$data['sub_category'] = $this->Admin_model->get_sub_categories();
        $this->global['pageTitle'] = 'Tafa2na : Add-Child-Sub-Categories';
        $this->loadViews("admin-panel/child-sub-category", $this->global, $data , NULL);
    }
    
    public function get_sub_category()
    {
        
        $id = $_POST['id'];
        $this->load->model('Admin_model');
        $categories_sub = $this->Admin_model->get_sub_categories($id);
        //print_r($categories_sub);
        
        echo json_encode($categories_sub);
        
        
    }
    
    public function add_child_sub_category()
    {
       if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
           $this->form_validation->set_rules('parent', 'Parent Category Name', 'required');
           $this->form_validation->set_rules('sub', 'sub Category Name', 'required');
           $this->form_validation->set_rules('childcategory', 'Child sub Category Name', 'required');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->child_sub_category();
            }
            else
            {
                $this->load->model('Admin_model');
                $result = $this->Admin_model->add_child_subcategory();
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'Child Sub Category created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Child Sub Category creation failed');
                }
                
                redirect('child_sub_category');
            }
        }
    }
    
    public function list_category()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {        
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            $this->load->model('Admin_model');
            $count = $this->Admin_model->categoryListingCount($searchText);

			$returns = $this->paginationCompress ( "categoryListing/", $count, 10 );

            $data['userRecords'] = $this->Admin_model->categoryListing($searchText, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'Tafa2na : Category Listing';
            
            $this->loadViews("admin-panel/list-category", $this->global, $data, NULL);
        }
    }
    
    public function editOldCategory($id = NULL)
    {
            $this->load->model('Admin_model');
            $data['catInfo'] = $this->Admin_model->getCategoryInfo($id);
            
            $this->global['pageTitle'] = 'Tafa2na : Edit Category';
            
            $this->loadViews("admin-panel/edit-old-category", $this->global, $data, NULL);
    }
    
    public function update_category()
    {
      if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
           $this->form_validation->set_rules('name', ' Category Name', 'required');
           $id = $this->input->post('id');
           
            if($this->form_validation->run() == FALSE)
            {
                $this->editOldCategory($id);
            }
            else
            {
                $this->load->model('Admin_model');
                $result = $this->Admin_model->update_category($id);
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', ' Category updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', ' Category updation failed');
                }
                
                redirect('Admin/list_category');
            }
        }
    }
    
    public function delete_category($id)
    {

        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('Admin_model');
                $result = $this->Admin_model->delete_category($id);
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', ' Category updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', ' Category updation failed');
                }
                
                redirect('Admin/list_category');
        }    
        
        
    }
    
    public function shipping()
    {
         $this->global['pageTitle'] = 'Tafa2na : Create Shipping';
         $this->loadViews("admin-panel/create-shipping", $this->global, NULL, NULL);
    }
    
    public function create_shipping()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
           $this->form_validation->set_rules('driver', ' Driver Name', 'required');
           $this->form_validation->set_rules('pickup', 'Pickup Point', 'required');
           $this->form_validation->set_rules('drop', ' Drop point', 'required');
           $this->form_validation->set_rules('picktime', 'Pickup Time', 'required');
           $this->form_validation->set_rules('droptime', ' Drop Time', 'required');
           $this->form_validation->set_rules('shipcode', 'Shipping Code', 'required');
           $this->form_validation->set_rules('address', ' Shipping Address', 'required');
           
           
            if($this->form_validation->run() == FALSE)
            {
                $this->shipping();
            }
            else
            {
                $this->load->model('Admin_model');
                $result = $this->Admin_model->create_shipping();
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', ' Shipping Created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', ' Shipping Creation  failed');
                }
                
                redirect('Admin/shipping');
            }
        }
        
    }
    
    public function list_shipping()
    {
        if($this->isAdmin() == TRUE)
        {
            $this->loadThis();
        }
        else
        {        
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            $this->load->model('Admin_model');
            $count = $this->Admin_model->shippingListingCount($searchText);

			$returns = $this->paginationCompress ( "shippingListing/", $count, 10 );

            $data['shippinglist'] = $this->Admin_model->shippingListing($searchText, $returns["page"], $returns["segment"]);
         $this->global['pageTitle'] = 'Tafa2na : List Shipping';
         
         $this->loadViews("admin-panel/list-shipping", $this->global, $data, NULL);
        }
    }
        

    
    
    
    
    
    
    
    
    
    
    
    
}

?>