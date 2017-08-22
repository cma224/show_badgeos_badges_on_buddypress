<?php
/*
Plugin Name: Show BadgeOS Badges on bbPress
Description: This plugin shows the configured badges earned and in progress in BBPress underneath their respective names. CSS classes are also automatically created in accordance with the name of each specified badge.
Version: 1.0.0
Author: Cristian Abello
Author URI: mailto:cristian.abello@valpo.edu
License: GNU AGPL
*/

class Show_BadgeOS_Badges_on_bbPress{

    function __construct() {
        
        // Define plugin constants
		$this->basename       = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url  = plugins_url( dirname( $this->basename ) );
        
        // Run our activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    
        // If requirements are not met, deactivate our plugin
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );

        // Display configured BadgeOS badges in bbPress
		add_action( 'bbp_theme_after_user_profile', array( $this, 'earnedBadges' ) );
		
		add_action( 'bbp_theme_after_reply_author_details', array( $this, 'earnedBadges' ) );
		
		// Include our other plugin files
		add_action( 'init', array( $this, 'includes' ) );
     
    }

    public function earnedBadges($reply_id) {
        
        global $user_ID;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( !empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; };

        $user_id = bbp_get_reply_author_id( $reply_id );
		
	    // Admin and mentor badges
	    if(badgeos_has_user_earned_achievement(2430, $user_id))
			echo '<span class="admin-rank"><br>Admin</span>';
		
		if(badgeos_has_user_earned_achievement(2428, $user_id))
			echo '<span class="mentor-rank"><br>Mentor</span>';
	
	   $badgeHierarchy = array(
	    	// Array format: (Badge Earned ID#, Badge Earned Name, Badge in Progress Name, Badge in Progress ID#, Baseline Badge Boolean)
	       
	        array(2510,"Fast-Track CEO","",0,true),
	        array(2507,"CEO","",0,true),
	        array(2506,"Director","CEO",2507,true),
	        array(2509,"FT Director","CEO",2507,true),
	        array(2508,"FT Associate","Director",2506,true),
	        array(2011,"Associate","Director",2008,true),
	        array(2010,"Recruit","Associate",2011,true),
	        array(2520,"","Recruit",2011,false),
	        array(2521,"","FT Associate",2508,false),
	        array(2522,"","FT Director",2509,false),
	        array(2523,"","FT CEO",2510,false),
     
	   );
	   
	   // Badges in progress	
	   $rC = new requirementChecker($user_id);

	   foreach($badgeHierarchy as $value)
	    if($value[4])
	        $rC->badgeInProgress($value[0],$value[3],$value[2],$value[1]);
	    else
	        $rC->badgeCompleted($value[0],$value[2],$value[1]);
	   
	}
	

    public function includes() {
        // Include files
        require_once( $this->directory_path . '/includes/requirement-checker.php' );
    
        }
    
    public function activate() {
        // Fun activation stuff
    }
    
    public function deactivate() {
        // Fun deactivation stuff
    }
    
    public static function meets_requirements() {
        
        // class_exists checks that BadgeOS, BuddyPress, and bbPress are all ACTIVE
        if(class_exists('BadgeOS') && class_exists('BuddyPress') && class_exists('bbPress'))
            return true;
        else
            return false;
            
    }
    
    public function maybe_disable_plugin() {
        		
        	if ( ! $this->meets_requirements() ) {
    		    
    		    // Display our error(s)
    	
    			if(!class_exists('BadgeOS')){
    				echo '<div id="message" class="error">';
    				echo '<p>' . sprintf( __( 'This plugin requires BadgeOS and has been <a href="%s">deactivated</a>. Please install and activate BadgeOS and then reactivate this plugin.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
    				echo '</div>';
    			}
    				
    			if(!class_exists('BuddyPress')){
    				echo '<div id="message" class="error">';
    				echo '<p>' . sprintf( __( 'This plugin requires BuddyPress and has been <a href="%s">deactivated</a>. Please install and activate BuddyPress and then reactivate this plugin.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
    				echo '</div>';
    			}
    			
    			if(!class_exists('bbPress')){
    				echo '<div id="message" class="error">';
    				echo '<p>' . sprintf( __( 'This plugin requires bbPress and has been <a href="%s">deactivated</a>. Please install and activate bbPress and then reactivate this plugin.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
    				echo '</div>';
    			}
    			
    			if(wp_get_current_user!='inadvance' || wp_get_current_user!='cabello'){
    			    echo '<div id="message" class="error">';
    				echo '<p>' . sprintf( __( 'This plugin is for the sole use of the IN_Advance program and has been <a href="%s">deactivated</a>. If your website is running BadgeOS and would like a tailored version of this plugin, please contact <a href="mailto:cristian.abello@valpo.edu">Cristian Abello</a>.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
    				echo '</div>';
    			}
    
    		// Deactivate our plugin
    		deactivate_plugins( $this->basename );
    			
    		// Stop WordPress from displaying "Plugin Activated" message.
    		if ( isset( $_GET['activate'] ) ) 
                unset( $_GET['activate'] );
                
        }
    
    }

}

$GLOBALS['show_badgeos_badges_on_bbpress'] = new Show_BadgeOS_Badges_on_bbPress();

?>