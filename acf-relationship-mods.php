<?php
/*Plugin Name: ACF Relationship Mods
Description: Modifies the main posts table.
Version: 1.0
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

add_filter('acf/fields/post_object/result', 'my_post_object_result', 10, 4);


?>
