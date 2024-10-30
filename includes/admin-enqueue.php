<?php

class InteractivePromoAdmin
{
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles() {
        if ($this->eb_is_edit_page()) {
            // hover effects
            $hover_style = "assets/css/hover-effects.css";
            wp_enqueue_style(
                'hover-effects-style',
                plugins_url($hover_style, __FILE__),
                array(),
                filemtime(INTERACTIVE_PROMO_DIR."/$hover_style"),
                'all'
            );
        }
    }

    /**
     * eb_is_edit_page 
     * function to check if the current page is a post edit page
     * 
     * @author Ohad Raz <admin@bainternet.info>
     * 
     * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
     * @return boolean
     */
    public function eb_is_edit_page($new_edit = null) {
        global $pagenow;
        //make sure we are on the backend
        if (!is_admin()) return false;

        
        if($new_edit == "edit")
            return in_array( $pagenow, array( 'post.php',  ) );
        elseif($new_edit == "new") //check for new post page
            return in_array( $pagenow, array( 'post-new.php' ) );
        else //check for either new or edit
            return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
    }
}

new InteractivePromoAdmin();
