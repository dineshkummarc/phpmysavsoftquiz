<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	 function __construct()
	 {
	   parent::__construct();
	   $this->load->database();
	   $this->load->model("user_model");
	    $this->load->model("quiz_model");
	   $this->lang->load('basic', $this->config->item('language'));
		if($this->db->database ==''){
		redirect('install');	
		}
		 
		 
		 
		
		
	 }

	public function index()
	{
		
		$this->load->helper('url');
		if($this->session->userdata('logged_in')){
			$logged_in=$this->session->userdata('logged_in');
			if($logged_in['su']=='1'){
				redirect('dashboard');
			}else{
				redirect('quiz');	
			}
			
		}
		
		
		
		$data['title']=$this->lang->line('login');
		$data['recent_quiz']=$this->quiz_model->recent_quiz('5');
		
		$this->load->view('header_login',$data);
		$this->load->view('login',$data);
		$this->load->view('footer',$data);
	}
	
	public function resend()
	{
		
		
		 $this->load->helper('url');
		if($this->input->post('email')){
		$status=$this->user_model->resend($this->input->post('email'));
		$this->session->set_flashdata('message', $status);
		redirect('login/resend');
		}
		
		
		$data['title']=$this->lang->line('resend_link');
		 
		$this->load->view('header',$data);
		$this->load->view('resend',$data);
		$this->load->view('footer',$data);
	}
	
	
 
	
	
	
	
	
		public function pre_registration()
	{
	$this->load->helper('url');
		$data['title']=$this->lang->line('select_package');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$this->load->view('header',$data);
		$this->load->view('pre_register',$data);
		$this->load->view('footer',$data);
	}

	
		public function registration($gid='0')
	{
	$this->load->helper('url');
		$data['gid']=$gid;
		$data['title']=$this->lang->line('register_new_account');
		// fetching group list
		$data['group_list']=$this->user_model->group_list();
		$this->load->view('header',$data);
		$this->load->view('register',$data);
		$this->load->view('footer',$data);
	}

	
	public function verifylogin($p1='',$p2=''){
		
		if($p1 == ''){
		$username=$this->input->post('email');
		$password=$this->input->post('password');
		}else{
		$username=urldecode($p1);
		$password=urldecode($p2);
		}
<<<<<<< HEAD

		// check xem co đang bị khóa k, có thì redirect và thông báo
		if($this->session->tempdata('penalty')){
			$this->load->helper('url');
			$this->session->set_flashdata('message', 'Your account is locked');
			redirect('login');
		} else {
			// không bị xóa, bắt đầu xác nhận đang nhập
			$status=$this->user_model->login($username,$password);
			if ((int)$status['status'] === 0){
				// sai pass check biến attempt để đếm số lần login
				$attempt = $this->session->userdata('attempt'); // get số lần login trong session
				$attempt++; // tăng biến
				$this->session->set_userdata('attempt', $attempt); // lưu số lần login vào session
				//$this->load->helper('url');
				//redirect('login'); // to voi thu them vao nhung van khong chuyen
				if ((int)$attempt >= 5) {

					// nếu attempt > or = 2 thì set biến penalty 300s để lock tài khoản 300s
					$this->session->set_tempdata('penalty', true, 300); //set the name of the sess var to 'penalty, the value will be true and will expire within 5 minutes (expressed in sec.)
					$this->load->helper('url');
					$this->session->set_flashdata('message', $status['message']);
					redirect('login');

				}
				$this->load->helper('url');
				$this->session->set_flashdata('message', 'Login fail');
				redirect('login');
			} elseif($status['status']=='1'){
				// đăng nhập thành công, gán lại biến attempt = 0
				$this->session->set_userdata('attempt', 0);
				$this->load->helper('url');
				// row exist fetch userdata
				$user=$status['user'];
				
				
					
					// user assigned to paid group now validate expiry date.
					if($user['subscription_expired'] <= time()){
						// eubscription expired, redirect to payment page
						
						redirect('payment_gateway/subscription_expired/'.$user['uid']);
						
					}
					
				$user['base_url']=base_url();
				// creating login cookie
				$this->session->set_userdata('logged_in', $user);
				// redirect to dashboard
				if($user['su']=='1'){
				redirect('dashboard');
					
				}else{
					$burl=$this->config->item('base_url').'index.php/quiz';
				header("location:$burl");
				}
			}else if($status['status']=='0'){
				
				// invalid login
				// try to auth wp
				if($this->config->item('wp-login')){
				
							if($this->authentication($username, $password)){
						
							$this->verifylogin($username, $password);
							}else{
							$this->load->helper('url');
							$this->session->set_flashdata('message', $status['message']);
				$burl=$this->config->item('base_url');
				header("location:$burl");
							}
					}else{
					
					$this->load->helper('url');
					$this->session->set_flashdata('message', $status['message']);
				redirect('login');
					}
					
				
			}else if($status['status']=='2'){
							$this->load->helper('url');

				
				// email not verified
				$this->session->set_flashdata('message', $status['message']);
				redirect('login');
			}else if($status['status']=='3'){
							$this->load->helper('url');

				
				// email not verified
				$this->session->set_flashdata('message', $status['message']);
				redirect('login');
			}
=======
		 $status=$this->user_model->login($username,$password);
		if($status['status']=='1'){
			$this->load->helper('url');
			// row exist fetch userdata
			$user=$status['user'];
			
			
			// validate if user assigned to paid group
			if($user['price'] > '0'){
				
				// user assigned to paid group now validate expiry date.
				if($user['subscription_expired'] <= time()){
					// eubscription expired, redirect to payment page
					
					redirect('payment_gateway/subscription_expired/'.$user['uid']);
					
				}
				
			}
			$user['base_url']=base_url();
			// creating login cookie
			$this->session->set_userdata('logged_in', $user);
			// redirect to dashboard
			if($user['su']=='1'){
			 redirect('dashboard');
				 
			}else{
				$burl=$this->config->item('base_url').'index.php/quiz';
			 header("location:$burl");
			}
		}else if($status['status']=='0'){
			 
			// invalid login
			// try to auth wp
			if($this->config->item('wp-login')){
			 
		                if($this->authentication($username, $password)){
		               
		                 $this->verifylogin($username, $password);
		                }else{
		                 $this->load->helper('url');
		                 $this->session->set_flashdata('message', $status['message']);
			 $burl=$this->config->item('base_url');
			 header("location:$burl");
		                }
		        }else{
		        
		        $this->load->helper('url');
		        $this->session->set_flashdata('message', $status['message']);
			redirect('login');
		        }
		        
			
		}else if($status['status']=='2'){
                        $this->load->helper('url');

			 
			// email not verified
			$this->session->set_flashdata('message', $status['message']);
			redirect('login');
		}else if($status['status']=='3'){
                        $this->load->helper('url');

			 
			// email not verified
			$this->session->set_flashdata('message', $status['message']);
			redirect('login');
>>>>>>> savsoftquiz_v4.0_advance-master/master
		}
		
		
		
<<<<<<< HEAD
		
=======
>>>>>>> savsoftquiz_v4.0_advance-master/master
	}
	
	
	
	
		
	function verify($vcode){
		$this->load->helper('url');	 
		 if($this->user_model->verify_code($vcode)){
			 $data['title']=$this->lang->line('email_verified');
		   $this->load->view('header',$data);
			$this->load->view('verify_code',$data);
		  $this->load->view('footer',$data);

			}else{
			 $data['title']=$this->lang->line('invalid_link');
		   $this->load->view('header',$data);
			$this->load->view('verify_code',$data);
		  $this->load->view('footer',$data);

			}
	}
	
	
	
	
	function forgot(){
	$this->load->helper('url');
			if($this->input->post('email')){
			$user_email=$this->input->post('email');
			 if($this->user_model->reset_password($user_email)){
				$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('password_updated')." </div>");
						
			}else{
				$this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('email_doesnot_exist')." </div>");
						
			}
			redirect('login/forgot');
			}
			
  
			$data['title']=$this->lang->line('forgot_password');
		   $this->load->view('header',$data);
			$this->load->view('forgot_password',$data);
		  $this->load->view('footer',$data);

	
	}
	
<<<<<<< HEAD
=======
	
>>>>>>> savsoftquiz_v4.0_advance-master/master
		public function insert_user()
	{
		
		
		 $this->load->helper('url');
		$this->load->library('form_validation');
<<<<<<< HEAD
		$this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]');
		$this->form_validation->set_rules('studentCode', 'Student Code', 'required|is_unique[users.student_code]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|alpha_numeric');
=======
		$this->form_validation->set_rules('email', 'Email', 'required|is_unique[savsoft_users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required');
>>>>>>> savsoftquiz_v4.0_advance-master/master
          if ($this->form_validation->run() == FALSE)
                {
                     $this->session->set_flashdata('message', "<div class='alert alert-danger'>".validation_errors()." </div>");
					redirect('login/registration/');
                }
                else
                {
					if($this->user_model->insert_user_2()){
                        if($this->config->item('verify_email')){
						$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('account_registered_email_sent')." </div>");
						}else{
							$this->session->set_flashdata('message', "<div class='alert alert-success'>".$this->lang->line('account_registered')." </div>");
						}
						}else{
						    $this->session->set_flashdata('message', "<div class='alert alert-danger'>".$this->lang->line('error_to_add_data')." </div>");
						
					}
					redirect('login/registration/');
                }       

	}
	
	
	
	
	function verify_result($rid){
		$this->load->helper('url');
		$this->load->model("result_model");
		
			$data['result']=$this->result_model->get_result($rid);
	if($data['result']['gen_certificate']=='0'){
		exit();
	}
	
	
	$certificate_text=$data['result']['certificate_text'];
	$certificate_text=str_replace('{email}',$data['result']['email'],$certificate_text);
	$certificate_text=str_replace('{first_name}',$data['result']['first_name'],$certificate_text);
	$certificate_text=str_replace('{last_name}',$data['result']['last_name'],$certificate_text);
	$certificate_text=str_replace('{percentage_obtained}',$data['result']['percentage_obtained'],$certificate_text);
	$certificate_text=str_replace('{score_obtained}',$data['result']['score_obtained'],$certificate_text);
	$certificate_text=str_replace('{quiz_name}',$data['result']['quiz_name'],$certificate_text);
	$certificate_text=str_replace('{status}',$data['result']['result_status'],$certificate_text);
	$certificate_text=str_replace('{result_id}',$data['result']['rid'],$certificate_text);
	$certificate_text=str_replace('{generated_date}',date('Y-m-d',$data['result']['end_time']),$certificate_text);
	
	$data['certificate_text']=$certificate_text;
	  $this->load->view('view_certificate_2',$data);
	 

	}
	
	
	
	function authentication ($user, $pass){
                  global $wp, $wp_rewrite, $wp_the_query, $wp_query;

                  if(empty($user) || empty($pass)){
                    return false;
                  }else{
                    require_once($this->config->item('wp-path'));
                    $status = false;
                    $auth = wp_authenticate($user, $pass );
                    if( is_wp_error($auth) ) {      
                      $status = false;
                    } else {
                    
<<<<<<< HEAD
                    // if username already exist in users
                    $this->db->where('wp_user',$user);
                    $query=$this->db->get('users');
=======
                    // if username already exist in savsoft_users
                    $this->db->where('wp_user',$user);
                    $query=$this->db->get('savsoft_users');
>>>>>>> savsoftquiz_v4.0_advance-master/master
                    if($query->num_rows()==0){
                    $userdata=array(
                    'password'=>md5($pass),
                    'wp_user'=>$user,
                    'su'=>0,
                    'gid'=>$this->config->item('default_group')                  
                    
                    );
<<<<<<< HEAD
                    $this->db->insert('users',$userdata);
=======
                    $this->db->insert('savsoft_users',$userdata);
>>>>>>> savsoftquiz_v4.0_advance-master/master
                    
                    }
                    
                    
                      $status = true;
                    }
                    return $status;
                  } 
        }
        
        
        public function commercial(){
        $this->load->helper('url');
		
       $data['title']=$this->lang->line('files_missing');
		   $this->load->view('header',$data);
			$this->load->view('files_missing',$data);
		  $this->load->view('footer',$data);
        }



		 // super admin code login controller 
	public function superadminlogin(){
	$this->load->helper('url');
			$logged_in=$this->session->userdata('logged_in_super_admin');
			if($logged_in['su']!='3'){
				exit('permission denied');
				
			}
			
		$user=$this->user_model->admin_login();
		$user['base_url']=base_url();
		 $user['super']=3;
		$this->session->set_userdata('logged_in', $user);
		redirect('dashboard');
	}
	
	
}
