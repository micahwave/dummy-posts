<?php
/**
 * Plugin Name: Dummy Posts
 * Author: Micah Ernst
 * Description: Adds some dummy posts to your site picking a random category and random image from media library
 * Version: 1.0
 */
 
function dp_admin_menu() {
	add_submenu_page( 'tools.php', 'Dummy Posts', 'Dummy Posts', 'manage_options', 'dummy-posts', 'dp_submenu_page' );
}
add_action( 'admin_menu', 'dp_admin_menu' );

function dp_submenu_page() {

	if( isset( $_POST['submit'] ) ) {
		dp();
	}

	?>
	<div class="wrap">
	
		<h2>Dummy Posts</h2>
		
		<form action="<?php echo admin_url( 'tools.php?page=dummy-posts' ); ?>" method="post">
		
			<p>
				<input type="checkbox" name="rand_cat" value="1"/>
				<label>Randomly select an category for post</label>
			</p>
			
			<p>
				<input type="checkbox" name="rand_img" value="1"/>
				<label>Randomly select an image for post</label>
			</p>
			
			<p>
				<label>Dynamic Post Title</label><br/>
				<input type="text" name="title" placeholder="Sample Posts #1"/>
			</p>
			
			<p>
				<label>Number of Posts to Generate</label><br/>
				<select name="numposts">
					<?php for( $i = 0; $i <= 60; $i++ ) : ?>
					<?php if( $i % 5 == 0 && $i > 1 ) : ?>
					<option value="<?php echo intval( $i ); ?>"><?php echo intval( $i ); ?></option>
					<?php endif; ?>
					<?php endfor; ?>
				</select>
			</p>
				
			<input type="submit" name="submit" value="Generate Dummy Posts" class="button-primary"/>
			
		</form>
		
	</div>
	<?php
}

function dp() {

	$max = 20;

	$images = get_posts( array(
		'post_type' => 'attachment',
		'posts_per_page' => -1
	));
	
	$cats = get_categories( array(
		'exclude' => 1,
		'hide_empty' => 0 
	));
	
	for( $i = 1; $i <= $max; $i++ ) {
		$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc blandit egestas nibh, id venenatis nisi aliquet ut. Nunc tincidunt euismod magna in iaculis. Aliquam at dapibus risus. Donec cursus sodales ipsum, ac faucibus ante adipiscing sit amet. Phasellus felis nisi, egestas interdum fermentum nec, cursus at augue. Sed tincidunt placerat aliquet. Nunc lacinia metus ac mauris sodales id feugiat mi pellentesque. Vestibulum sed est at quam facilisis vestibulum.';
		
		$post = array(
			'post_title' => 'Sample Post #'.$i,
			'post_status' => 'publish',
			'post_content' => $text,
			'post_type' => 'post',
		);
		
		if( $cats && is_array( $cats ) ) {
			shuffle( $cats );
			$post['post_category'] = array( $cats[0]->term_id );
		}
		
		$post_id = wp_insert_post( $post );
		
		if( $images && is_array( $images ) ) {
			shuffle( $images );
			update_post_meta( $post_id, '_thumbnail_id', $images[0]->ID );
			update_post_meta( $post_id, 'dummy_post', 1 );
		}
		
		update_post_meta( $post_id, 'dp', 1 );
	}
}