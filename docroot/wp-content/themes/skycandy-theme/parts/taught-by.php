<?php
$instructors = $wpdb->get_results("
  SELECT post_id FROM $wpdb->postmeta 
  WHERE meta_value = $post->ID
  AND meta_key = 'sky_candy_classes_taught'
");
if ($instructors) {
	?>
	<h3>Class Taught By</h3>
	<ul class="instructors">
	<?php
	foreach($instructors as $instructor) {
		$post_object = get_post( $instructor->post_id );
		//$thumbnail = get_the_post_thumbnail( $instructor->post_id, 'medium');
		$permalink = get_permalink( $post_object->ID ); ?>
		<li class="instructor_info"><a href="<?php echo $permalink; ?>"><?php echo $post_object->post_title; ?></a></li>
		<?php
	}
	?>
	</ul>
	<?php
}
?>