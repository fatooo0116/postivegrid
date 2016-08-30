<?php

class Email_Post_update{
	var $defaults;

	var $left_post;
	var $right_post;

	var $text_diff;

	const ADMIN_PAGE = 'email_post_changes';
	const OPTION_GROUP = 'email_post_changes';
	const OPTION = 'email_post_changes';

        	static function init() {
        		static $instance = null;
        		if ( $instance )
        			return $instance;

        		$class = __CLASS__;
        		$instance = new $class;
        		return $instance;
        	}



          function __construct() {
            $this->defaults = apply_filters( 'email_post_changes_default_options', array(
              'enable'     => 1,
              'users'      => array(),
              'emails'     => array( get_option( 'admin_email' ) ),
              'post_types' => array( 'post', 'page' ),
              'drafts'     => 0,
            ) );

            add_action( 'post_updated', array( $this, 'post_updated' ), 10, 3 );
            add_filter( 'wp_mail_content_type', array( $this, 'wpdocs_set_html_mail_content_type' )  );

          }
          function wpdocs_set_html_mail_content_type() {
              return 'text/html';
          }

          // The meat of the plugin
        	function post_updated( $post_id, $post_after, $post_before ) {

            // Transitioning from an Auto Draft to Published shouldn't result in a notification.
            if ( $post_before->post_status === 'auto-draft' && $post_after->post_status === 'publish' )
              return;

            // If we're purely saving a draft, and don't have the draft option enabled, skip. If we're transitioning one way or the other, send a notification.
            if ( 0 == $options['drafts'] && in_array( $post_before->post_status, array( 'draft', 'auto-draft' ) ) && 'draft' == $post_after->post_status )
              return;

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
              return;
            }



            // Grab the meta data
        		$the_author = get_the_author_meta( 'display_name', get_current_user_id() ); // The revision
        		$the_title = get_the_title( $this->right_post->ID ); // New title (may be same as old title)
        		$the_date = gmdate( 'j F, Y \a\t G:i \U\T\C', strtotime( $this->right_post->post_modified_gmt . '+0000' ) ); // Modified time
        		$the_permalink = esc_url( get_permalink( $this->right_post->ID ) );
        		$the_edit_link = esc_url( get_edit_post_link( $this->right_post->ID ) );


            $head_sprintf = __( 'User %s made the changes to<br/>  %s %s on %s' );

            $blogname = html_entity_decode( get_option( 'blogname' ), ENT_QUOTES, $charset );
            $title = html_entity_decode( $the_title, ENT_QUOTES, $charset );

            $post_type = get_post_type($post_id);


      			// HTML
      			$html_diff_head  = '<h2>' . sprintf( __( 'Post Type: %s' ), $post_type) . "</h2>\n";
      			$html_diff_head .= '<h2>'.sprintf( $head_sprintf,
      				esc_html( $the_author ),
      				sprintf( _x( '&#8220;%s&#8221; [%s]', '1 = link, 2 = "edit"' ),
      					"<a href='$the_permalink'>" . esc_html( $the_title ) . '</a>',
      					"<a href='$the_edit_link'>" . __( 'edit' ) . '</a>'
      				),
      				$this->right_post->post_type,
      				$the_date
      			).'</h2>'  ;



            $option_email = get_option("pg_option_email");

            $title = get_the_title($post_id);

        			wp_mail(
        				$option_email,
        				sprintf( __( '[%s] Post Update: %s' ), $blogname, $title),
        				$html_diff_head
        			);
        	}

}
