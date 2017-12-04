<?php
class gradebook_course_API{
	public function __construct(){
		add_action('wp_ajax_course', array($this, 'course'));											
	}
	
/*********************************
* Use the following template to extend api
*
*	public function name_of_api(){
*		global $wpdb;
*   	$wpdb->show_errors();  		
*		if (!gradebook_check_user_role('administrator')){	
*			echo json_encode(array("status" => "Not Allowed."));
*			die();
*		}   		
*		switch ($_SERVER['REQUEST_METHOD']){
*			case 'DELETE' :  
*	  			echo json_encode(array('delete'=>'deleting'));
*	  			break;
*	  		case 'PUT' :
*	  			echo json_encode(array('put'=>'putting'));
*				break;
*	  		case 'UPDATE' :
*				echo json_encode(array("update" => "updating"));				
*				break;
*	  		case 'PATCH' :
*				echo json_encode(array("patch" => "patching"));				
*				break;
*	  		case 'GET' :
*				echo json_encode(array("get" => "getting"));	
*				break;
*	  		case 'POST' :				
*				echo json_encode(array("post" => "posting"));		  		
*				break;
*	  	}
*	  	die();
*	}
*********************************/


/*************************
*
*   course api
*
**************************/

	public function course(){
  		global $wpdb, $oplb_gradebook_api;
                
                $wpdb->show_errors();  	   		  	
		
                $params = $oplb_gradebook_api->oplb_gradebook_get_params();
                $id = $gbid = $params['gbid'];
                
                //user check - only instructors allowed, except for GET requests
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor' 
                        && $params['method'] !== 'GET' 
                        && $params['method'] !== 'POST') {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                //for POST requests, the course doesn't exist yet, so we have to do a more generic user check
                } else if ($params['method'] === 'POST'
                            && $oplb_gradebook_api->oplb_gradebook_get_user_role() !== 'instructor' ) {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                } else if($params['method'] === 'GET'
                            && $oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor'
                            && $oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'student') {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                }
                
                //nonce check
                if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
                    echo json_encode(array("status" => "Authentication error."));
                    die();
                }
                
		switch ($params['method']){
			case 'DELETE' : 
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_courses",array('id'=>$id));
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_assignments",array('gbid'=>$gbid));
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells",array('gbid'=>$gbid));  
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_users",array('gbid'=>$gbid));  	  			
	  			echo json_encode(array('delete_course'=>'Success'));
	  			break;
	  		case 'PUT' :  					
   				$wpdb->update("{$wpdb->prefix}oplb_gradebook_courses", array( 
   					'name' => $params['name'], 'school' => $params['school'], 'semester' => $params['semester'], 
   					'year' => $params['year']),
					array('id' => $gbid)
				);   
                                $courseDetails = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = {$gbid}", ARRAY_A);
   				echo json_encode($courseDetails);
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :	  		
                                $courseDetails = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = {$gbid}" , ARRAY_A);	
   				echo json_encode($courseDetails);		
				break;
	  		case 'POST' :		
                                $user = wp_get_current_user();
				$wpdb->insert("{$wpdb->prefix}oplb_gradebook_courses", 
		    		array('name' => $params['name'], 
		    			'school' => $params['school'], 
		    			'semester' => $params['semester'], 
		    			'year' => $params['year']), 
					array('%s', '%s', '%s', '%d') 
				);
				$gbid = $wpdb -> insert_id;
                                $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", 
		    		array('uid' => $user->ID,'gbid' => $gbid, 'role' => 'instructor'), 
					array('%d', '%d', '%s') 
				);	
				global $oplb_gradebook_api;
				$user = $oplb_gradebook_api -> oplb_gradebook_get_user($user->ID, $gbid);			
				$course = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = $gbid", ARRAY_A);
				$course['id']=intval($course['id']);
				$course['year']=intval($course['year']);				
				echo json_encode(array('course'=>$course, 'user'=>$user));
				die();					
				break;
	  	}
	  	die();
	}
}	
?>