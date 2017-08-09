<?php
/*
Plugin Name: Show BadgeOS Badges on BuddyPress
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
	
		add_action( 'bbp_theme_after_topic_author', array( $this, 'checkbox2' ) );
		
		// Include our other plugin files
		add_action( 'init', array( $this, 'includes' ) );
       

    }
    
        public function checkbox2(){
            echo do_shortcode('[badgeos_achievements_list type="badges" limit="10" show_filter="true" show_search="false" orderby="menu_order" order="ASC" user_id="bbp_get_topic_author_id()" wpms="false"]');
        }
    	public function checkbox() {
        global $user_ID;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( !empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; };

		//user must be logged in to view earned badges and points
		if ( is_user_logged_in() ) {

			//display user's points if widget option is enabled
			if ( $instance['point_total'] == 'on' ) {
				echo '<p class="badgeos-total-points">' . sprintf( __( 'My Total Points: %s', 'badgeos' ), '<strong>' . number_format( badgeos_get_users_points() ) . '</strong>' ) . '</p>';
			}

            $topic_author = bbp_get_topic_author_id();
			$achievements = badgeos_get_user_achievements(array('display'=>true,'user_id' => $topic_author ));

			if ( is_array( $achievements ) && ! empty( $achievements ) ) {

				$number_to_show = absint( $instance['number'] );
				$thecount = 0;

				wp_enqueue_script( 'badgeos-achievements' );
				wp_enqueue_style( 'badgeos-widget' );

				//load widget setting for achievement types to display
				$set_achievements = ( isset( $instance['set_achievements'] ) ) ? $instance['set_achievements'] : '';

				//show most recently earned achievement first
				$achievements = array_reverse( $achievements );

				echo '<ul class="widget-achievements-listing">';
				foreach ( $achievements as $achievement ) {

					//verify achievement type is set to display in the widget settings
					//if $set_achievements is not an array it means nothing is set so show all achievements
					if ( ! is_array( $set_achievements ) || in_array( $achievement->post_type, $set_achievements ) ) {

						//exclude step CPT entries from displaying in the widget
						if ( get_post_type( $achievement->ID ) != 'step' ) {

							$permalink  = get_permalink( $achievement->ID );
							$title      = get_the_title( $achievement->ID );
							$img        = badgeos_get_achievement_post_thumbnail( $achievement->ID, array( 50, 50 ), 'wp-post-image' );
							$thumb      = $img ? '<a class="badgeos-item-thumb" href="'. esc_url( $permalink ) .'">' . $img .'</a>' : '';
							$class      = 'widget-badgeos-item-title';
							$item_class = $thumb ? ' has-thumb' : '';

							// Setup credly data if giveable
							$giveable   = credly_is_achievement_giveable( $achievement->ID, $user_ID );
							$item_class .= $giveable ? ' share-credly addCredly' : '';
							$credly_ID  = $giveable ? 'data-credlyid="'. absint( $achievement->ID ) .'"' : '';

							echo '<li id="widget-achievements-listing-item-'. absint( $achievement->ID ) .'" '. $credly_ID .' class="widget-achievements-listing-item'. esc_attr( $item_class ) .'">';
							echo $thumb;
							echo '<a class="widget-badgeos-item-title '. esc_attr( $class ) .'" href="'. esc_url( $permalink ) .'">'. esc_html( $title ) .'</a>';
							echo '</li>';

							$thecount++;

							if ( $thecount == $number_to_show && $number_to_show != 0 ) {
								break;
							}

						}

					}
				}

				echo '</ul><!-- widget-achievements-listing -->';

			}

	}
    	}    
    public function textdomain() {
		load_plugin_textdomain( 'bbp_private_replies', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	


    
    public function jc_after_topic_author() {
     
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
    				echo '<div id="message" class="error">';
    			}
    				
    			if(!class_exists('BuddyPress')){
    				echo '<div id="message" class="error">';
    				echo '<p>' . sprintf( __( 'This plugin requires BuddyPress and has been <a href="%s">deactivated</a>. Please install and activate BuddyPress and then reactivate this plugin.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
    				echo '<div id="message" class="error">';
    			}
    			if(!class_exists('bbPress')){
    				echo '<div id="message" class="error">';
    				echo '<p>' . sprintf( __( 'This plugin requires bbPress and has been <a href="%s">deactivated</a>. Please install and activate bbPress and then reactivate this plugin.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
    				echo '<div id="message" class="error">';
    			}
    			
    		


    		echo '</div>';
    
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