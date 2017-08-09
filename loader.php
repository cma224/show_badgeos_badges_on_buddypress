<?php
/*
Plugin Name: Show BadgeOS Badges on bbPress
Description: This plugin shows the (configured) badges earned by posters in BBPress.
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
    
        // If BadgeOS is unavailable, deactivate our plugin
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );

        add_action('admin_init',array($this,'hideUserRoles'));
        
        // Display configured BadgeOS badges in bbPress
		add_action( 'bbp_theme_after_user_profile', array( $this, 'arnedBadgesAfterReplyAuthorDetails' ) );
		
		add_action( 'bbp_theme_after_reply_author_details', array( $this, 'earnedBadges' ) );
		
		// Include our other plugin files
		add_action( 'init', array( $this, 'includes' ) );
     
    }

    public function earnedBadges($reply_id) {
        
        global $user_ID;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( !empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; };

		//user must be logged in to view earned badges and points
	
        $user_id = bbp_get_reply_author_id( $reply_id );
		
	    // Admins and mentors
	    if(badgeos_has_user_earned_achievement(2430, $user_id))
			echo '<span class="admin-rank"><br>Admin</span>';
		
		if(badgeos_has_user_earned_achievement(2428, $user_id))
			echo '<span class="mentor-rank"><br>Mentor</span>';
		
	    // Badges in progress	
	    
	    // Start badging as freshman/sophomore/junior/senior dependency
		if(badgeos_has_user_earned_achievement(2243, $user_id) && !badgeos_has_user_earned_achievement(2420, $user_id))
			echo '<span class="ft-ceo-rank"><br><br>FT CEO<br>[in Progress]<br><br></span>';
		
		if(badgeos_has_user_earned_achievement(2240, $user_id)
	    && !badgeos_has_user_earned_achievement(2009, $user_id))
			echo '<span class="ft-director-rank"><br>FT Director<br>[in Progress]<br><br></span>';
		
		if(badgeos_has_user_earned_achievement(2235, $user_id) && !badgeos_has_user_earned_achievement( 2012, $user_id))
			echo '<span class="associate-rank"><br>FT Associate<br>[in Progress]<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2231, $user_id) && ! badgeos_has_user_earned_achievement( 2010, $user_id))
			echo '<span class="recruit-rank"><br>Recruit<br>[in Progress]<br><br></span>';
		
		if((badgeos_has_user_earned_achievement( 2008, $user_id)
			|| badgeos_has_user_earned_achievement( 2009, $user_id) ) && ! badgeos_has_user_earned_achievement( 2013, $user_id))
			echo '<span class="ceo-rank"><br>CEO<br>[in Progress]<br><br></span>';
		
		if((badgeos_has_user_earned_achievement( 2010, $user_id)
		 ) && ! badgeos_has_user_earned_achievement( 2011, $user_id))
			echo '<span class="associate-rank"><br>Associate<br>[in Progress]<br><br></span>';
		
		if(((badgeos_has_user_earned_achievement( 2011, $user_id)
		 )||badgeos_has_user_earned_achievement( 2012, $user_id) ) && ! badgeos_has_user_earned_achievement( 2008, $user_id))
			echo '<span class="associate-rank"><br>Director<br>[in Progress]<br><br></span>';
		
	    // Earned badges
		if(badgeos_has_user_earned_achievement( 2013, $user_id))
			echo '<span class="ceo-rank">CEO<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2420, $user_id))
			echo '<span class="ft-ceo-rank">FT CEO<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2008, $user_id))
			echo '<span class="director-rank">Director<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2009, $user_id))
			echo '<span class="ft-director-rank">FT Director<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2011, $user_id))
			echo '<span class="associate-rank">Associate<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2012, $user_id))
			echo '<span class="ft-associate-rank">FT Associate<br><br></span>';
		
		if(badgeos_has_user_earned_achievement( 2010, $user_id))
			echo '<span class="recruit-rank">Recruit<br><br></span>';
	
	}
    
    public function includes() {
        // Include files
        
        // Add files individually later on in like so:
        //require_once( $this->directory_path . '/includes/sample.php' );
        
    }
    
    public function activate() {
        // Fun activation stuff
    }
    
    public function deactivate() {
        // Fun deactivation stuff
    }
    
    public static function meets_requirements() {
        
        // class_exists checks that BadgeOS, BuddyPress, and bbPress are all ACTIVE
        if(class_exists('BadgeOS') && class_exists('BuddyPress') && class_exists('bbPress') && (wp_get_current_user=='inadvance' || wp_get_current_user=='cabello')
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