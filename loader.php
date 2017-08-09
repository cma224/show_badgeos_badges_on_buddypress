<?php
/*
Plugin Name: Show BadgeOS Badges on bbPress
Description: This plugin shows the badges earned by topic authors in BBPress.
Version: 1.0.0
Author: Cristian Abello
Author URI: mailto:cristian.abello@valpo.edu
License: GNU AGPL
*/

class BadgeOS_bbPress_Extension{

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
		add_action( 'init', array( $this, 'textdomain' ) );
	
		add_action( 'bbp_theme_after_user_profile', array( $this, 'arnedBadgesAfterReplyAuthorDetails' ) );
		add_action( 'bbp_theme_after_reply_author_details', array( $this, 'earnedBadgesAfterReplyAuthorDetails' ) );
		
		// Include our other plugin files
		add_action( 'init', array( $this, 'includes' ) );
       

    }

        
    public function earnedBadgesAfterReplyAuthorDetails($reply_id) {
       // global $user_ID;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( !empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; };

		//user must be logged in to view earned badges and points
		if ( is_user_logged_in() ) {
$user_id = bbp_get_reply_author_id( $reply_id );
			//display user's points if widget option is enabled
			if(badgeos_has_user_earned_achievement( 223, $user_id)){
				echo 'asdf';
			}
		
           
			}

	}
    	
    	
    public function textdomain() {
		load_plugin_textdomain( 'bbp_private_replies', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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
    
    		// Deactivate our plugin
    		deactivate_plugins( $this->basename );
    			
    		// Stop WordPress from displaying "Plugin Activated" message.
    		if ( isset( $_GET['activate'] ) ) 
                unset( $_GET['activate'] );
                
        }
    
    }

}

$GLOBALS['badgeos_bbpress_extension'] = new BadgeOS_bbPress_Extension();

?>