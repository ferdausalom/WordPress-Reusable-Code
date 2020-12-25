<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Logo showing code call
if (function_exists('the_custom_logo')) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo = wp_get_attachment_image_src($custom_logo_id);
}
if ($logo):
    echo $logo[0];
endif;

// Custo post query call
global $post;
$ourCurrentPage = get_query_var('paged');
$wp_query = new WP_Query(array(
    'post_type'      => 'teacher',
    'posts_per_page' =>9,
    'order'          => 'ASC',
    'paged'          => $ourCurrentPage
));
if ($wp_query->have_posts()):
while($wp_query->have_posts()): $wp_query->the_post();
    $email       = get_post_meta( $post->ID, '_email_meta_value_key', true );
    $thumb       = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium');
    echo get_permalink();
//  Content gose here
endwhile;
else:
    // <h3 class="alert alert-warning all_error"><?php _e( 'Sorry, no Teacher found. Please add Teacher.' );</h3>
endif;

// Customizer call
echo get_theme_mod('about_principal');

// Post thumbnail call
the_post_thumbnail('', ['img-responsive', 'title' => get_the_title()]);

// Site url call
echo site_url();

// Navigation call
if ( has_nav_menu( 'primary' ) ):
wp_nav_menu(
    array(
        'container' => 'div',
        // 'container_class' =>'collapse navbar-collapse',
        'container_id' => 'dropdown',
        // 'menu_class' => 'navbar-nav ml-auto mr-auto',
        'theme_location' => 'primary',
    )
);

else:
// <ul>
// <li class="active"><a href="#">Home</a></li>
// <li><a href="#">About</a></li>
// </ul>
endif;

// Call back
if (function_exists('the_breadcrumb')) the_breadcrumb();

// Pagination call after query
knfm_pagination($wp_query);



// Below code for FUNCTIONS.PHP

/**
 * Register and Enqueue Styles.
 */
function knfm_register_styles() {

    $theme_version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'knfm-app-style', get_template_directory_uri() . '/assets/css/app.css', null, $theme_version, 'all' );

}
add_action( 'wp_enqueue_scripts', 'knfm_register_styles' );

/**
 * Register and Enqueue Scripts.
 */
function knfm_register_scripts() {

    $theme_version = wp_get_theme()->get( 'Version' );
    wp_enqueue_script( 'knfm-tickerNews', get_template_directory_uri() . '/assets/js/jquery.tickerNews.min.js', array(), $theme_version, true );

}
add_action( 'wp_enqueue_scripts', 'knfm_register_scripts' );

/**
 * Register navigation menus uses wp_nav_menu in five places.
 */
function ferdaus_flipmarto_menus() {

	$locations = array(
		'primary'  => __( 'Primary Menu', 'knfm' ),
	);
	register_nav_menus( $locations );
}
add_action( 'init', 'ferdaus_flipmarto_menus' );

/* EXCERPT */
function get_excerpt($limit){
    $excerpt = get_the_content();
    $excerpt = preg_replace(" ([.*?])",'',$excerpt);
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, $limit);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
    $excerpt = $excerpt.' ... <a class="btn btn-xs btn-primary" href="'.get_the_permalink().'">Read More</a>';
    return $excerpt;
}

function wpb_change_title_text( $title ){
    $screen = get_current_screen();
// Teacher title text
    if  ( 'teacher' == $screen->post_type ) {
        $title = 'Teacher name';
    }
    return $title;
}
add_filter( 'enter_title_here', 'wpb_change_title_text' );

// Date array value come from event meta box
function get_month_name($date_array){
    if ($date_array[1] == 1) {
        echo 'JAN';
    } elseif($date_array[1] == 2) {
        echo 'FEB';
    } elseif($date_array[1] == 3) {
        echo 'MAR';
    } elseif($date_array[1] == 4) {
        echo 'APR';
    } elseif($date_array[1] == 5) {
        echo 'MAY';
    } elseif($date_array[1] == 6) {
        echo 'JUN';
    } elseif($date_array[1] == 7) {
        echo 'JUL';
    } elseif($date_array[1] == 8) {
        echo 'AUG';
    } elseif($date_array[1] == 9) {
        echo 'SEP';
    } elseif($date_array[1] == 10) {
        echo 'OCT';
    } elseif($date_array[1] == 11) {
        echo 'NOV';
    } else {
        echo 'DEC';
    }
}



/*=============================================
                BREADCRUMBS
=============================================*/

//  to include in functions.php
function the_breadcrumb()
{
    $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = '&raquo;'; // delimiter between crumbs
    $home = 'Home'; // text for the 'Home' link
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb

    global $post;
    $homeLink = get_bloginfo('url');
    if (is_home() || is_front_page()) {
        if ($showOnHome == 1) {
            echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
        }
    } else {
        echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                echo get_category_parents($thisCat->parent, true, ' ' . $delimiter . ' ');
            }
            echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
        } elseif (is_search()) {
            echo $before . 'Search results for "' . get_search_query() . '"' . $after;
        } elseif (is_day()) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('d') . $after;
        } elseif (is_month()) {
            echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            echo $before . get_the_time('F') . $after;
        } elseif (is_year()) {
            echo $before . get_the_time('Y') . $after;
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
                if ($showCurrent == 1) {
                    echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                }
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, true, ' ' . $delimiter . ' ');
                if ($showCurrent == 0) {
                    $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                }
                echo $cats;
                if ($showCurrent == 1) {
                    echo $before . get_the_title() . $after;
                }
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo get_category_parents($cat, true, ' ' . $delimiter . ' ');
            echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
            if ($showCurrent == 1) {
                echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
            }
        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1) {
                echo $before . get_the_title() . $after;
            }
        } elseif (is_page() && $post->post_parent) {
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_post($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo $breadcrumbs[$i];
                if ($i != count($breadcrumbs)-1) {
                    echo ' ' . $delimiter . ' ';
                }
            }
            if ($showCurrent == 1) {
                echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
            }
        } elseif (is_tag()) {
            echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . 'Articles posted by ' . $userdata->display_name . $after;
        } elseif (is_404()) {
            echo $before . 'Error 404' . $after;
        }
        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                echo ' (';
            }
            echo __('Page') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                echo ')';
            }
        }
        echo '</div>';
    }
} // end the_breadcrumb()

/**
 *Custom pagination
 */
function knfm_pagination($wp_query){

    if ( $wp_query->max_num_pages <= 1 ) return;

    $big = 999999999; // need an unlikely integer

    $pages = paginate_links( array(
        'base'    	=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'  	=> '?paged=%#%',
        'current' 	=> max( 1, get_query_var('paged') ),
        'total'   	=> $wp_query->max_num_pages,
        'type'    	=> 'array',
        'prev_next' => false,
        // 'prev_text' => __('Previous'),
        // 'next_text' => __('Next'),
    ) );

    if( is_array( $pages ) ) {
        $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
        echo '<ul class="pagination-center">';
        foreach ( $pages as $page ) {
                echo "<li class='page-item'>$page</li>";
        }
    echo '</ul>';
    }
}

// REMOVE SCRIPT VERSION
function _remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

// Disable use XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false');

// disable pingbacks
add_filter( 'xmlrpc_methods', function( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
} );

// REMOVE WP EMOJI
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// remove x-pingback HTTP header
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

function my_login_logo_one() {
?>
    <style type="text/css">
    body {
        background: #0a2a30 !important;
    }
    body.login div#login h1 a {
    background-image: url(http://knfm.test/wp-content/uploads/2020/12/logo-90.png);
    padding-bottom: 30px;
    width: 100%;
    background-size: auto;
    }
    .login form {
        box-shadow: 0 5px 9px rgba(0,0,0,.13) !important;
    }
    .login #nav {
        display: none !important;
    }
    </style>
<?php
}
add_action( 'login_enqueue_scripts', 'my_login_logo_one' );

