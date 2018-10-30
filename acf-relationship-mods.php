<?php
/*Plugin Name: ACF Relationship Mods
Description: Modifies the main posts table.
Version: 1.0.1
License: GPLv2
GitHub Plugin URI: https://github.com/aaronaustin/acf-relationship-mods
*/

//This mod adds columns for acf fields start_date and end_date
//CUSTOMIZE COLUMNS IN LISTING
// Add the custom columns to the main post type:

//acf field add column
// filter for every field
add_filter('acf/fields/relationship/result', 'my_relationship_result', 10, 4);
function my_relationship_result( $title, $post, $field, $post_id ) {
  // load a custom field from this $object and show it in the $result
  $initialDate = formatDate(get_field('start_date', $post->ID));
  $start_date = strlen($initialDate) > 0 ? '('.$initialDate.')' : '';
  $category = get_the_category($post->ID);
  $category = strtoupper($category[0]->slug);
  // append to title
  $newTitle = '<div>'.$title.'</div> <div><small>'.$category.' '.$start_date.'</small></div>';
  return $newTitle;
}

function formatDate($timestamp, $time = false) {
  $format = $time ? "Y-m-d | g:i a" : "Y-m-d";
  if ($timestamp){
    return date($format, strtotime($timestamp));
  }
}

add_action('admin_head', 'registerACFRelationshipCss');
function registerACFRelationshipCss(){
  $src = plugins_url('style.css',__FILE__ );
  $handle = "customAdminCss";
  wp_enqueue_style($handle, $src, array(), false, false);
}

//post_object field type edits
function my_post_object_result( $title, $post, $field, $post_id ) {
    $initialDate = formatDate(get_field('start_date', $post->ID));
    $start_date = strlen($initialDate) > 0 ? $initialDate : '';

    // add post type to each result
    $category = get_the_category($post->ID);
    $category = substr(strtoupper($category[0]->slug),0,1);

    $if_category = $category ? true : false;
    $if_start_date = $start_date ? true : false;

    $category_span = ' <span class="category">' . $category .  '</span>';
    $start_date_span = ' <span class="date">' . $start_date .  '</span>';

    
    $title = ($if_category ? $category_span : '') . $title . ($if_start_date ? $start_date_span : '');

    return $title;

    function formatDate($timestamp, $time = false) {
      $format = $time ? "Y-m-d | g:i a" : "Y-m-d";
      if ($timestamp){
        return date($format, strtotime($timestamp));
      }
    }

}

add_filter('acf/fields/post_object/result', 'my_post_object_result');

//add styles and scripts.  Load values for js vars.
function acf_relationship_mods() {
    wp_register_script( 'acf_relationship_mods_script', plugins_url('acf-mods.js',__FILE__ ));
    wp_enqueue_script('acf_relationship_mods_script');
    wp_localize_script( 'acf_relationship_mods_script', 'postCategory', array( 'category' => get_the_category($_GET['post']), ) );
    wp_localize_script( 'acf_relationship_mods_script', 'eventStart', array( 'date' => date('Y/m/d/', strtotime(get_field('start_date',$_GET['post']))), ) );
    wp_localize_script( 'acf_relationship_mods_script', 'postDate', array( 'date' => date('Y/m/d/', strtotime(get_post_field('post_date',$_GET['post']))), ) );
    wp_localize_script( 'acf_relationship_mods_script', 'postTitle', array( 'title' => get_the_title($_GET['post']), ) );
}

add_action( 'admin_init','acf_relationship_mods');




function my_acf_update_value($post_id) {
  $pathField = get_field_object('path', $post_id);
  $category_id = $_POST['post_category'][1];
  $category = get_the_category_by_ID($category_id);
  $title = sanitize_title($_POST['post_title']);
  $date = $category == 'Event' ? date('Y/m/d/', strtotime(get_field('start_date'))) : $_POST['aa'] .'/'. $_POST['mm'] .'/'. $_POST['jj'] .'/';
  $value = $date . $title;

  update_field($pathField['key'], $value, $post_id);
    
}

// acf/update_value/name={$field_name} - filter for a specific field based on it's name
add_filter('acf/save_post', 'my_acf_update_value', 20);

?>
