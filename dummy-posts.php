<?php
/**
 * Plugin Name: Dummy Posts
 * Author: Micah Ernst
 * Description: Quick and easy way to add test content to your site. Dummy content can easily be removed once testing is complete.
 * Version: 1.0
 */

class DummyPosts {

	/**
	 * Constructor
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Register our submenu page
	 */
	function admin_menu() {
		add_submenu_page( 'tools.php', 'Dummy Posts', 'Dummy Posts', 'manage_options', 'dummy-posts', array( $this, 'submenu_page' ) );
	}

	/**
	 * The submenu page with our options
	 */
	function submenu_page() {

		// get the post types
		$post_types = get_post_types();

		// nav menus
		$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

		if( isset( $_POST['submit'] ) ) {
			$this->generate_posts();
		}

		?>
		<div class="wrap">
		
			<h2>Dummy Posts</h2>
			
			<form action="<?php echo admin_url( 'tools.php?page=dummy-posts' ); ?>" method="post">

				<p>
					<label>Post type to create</label>
					<select name="type">
						<?php foreach( $post_types as $type ) : ?>
							<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p>
					<label>Nav Menu for Posts</label>
					<select name="menu">
						<option value="0">None</option>
						<?php foreach( $nav_menus as $menu ) : ?>
							<option value="<?php echo intval( $menu->term_id ); ?>"><?php echo esc_html( $menu->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			
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
					<input type="text" name="title" placeholder="Dummy Posts #1"/>
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

	/**
	 * Generate the dummy posts based on the selected options
	 */
	function generate_posts() {

		$max = isset( $_POST['numposts'] ) ? intval( $_POST['numposts'] ) : 20;

		// get images to attribute to posts
		if( !empty( $_POST['rand_img'] ) ) {

			$images = get_posts( array(
				'post_type' => 'attachment',
				'posts_per_page' => 50
			));

		}
		
		// get categories to attribute to posts
		if( !empty( $_POST['rand_cat'] ) ) {

			$cats = get_categories( array(
				'exclude' => 1,
				'hide_empty' => 0 
			));

		}

		$tracker = 0;
		
		// create the posts
		for( $i = 1; $i <= $max; $i++ ) {

			$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc blandit egestas nibh, id venenatis nisi aliquet ut. Nunc tincidunt euismod magna in iaculis. Aliquam at dapibus risus. Donec cursus sodales ipsum, ac faucibus ante adipiscing sit amet. Phasellus felis nisi, egestas interdum fermentum nec, cursus at augue. Sed tincidunt placerat aliquet. Nunc lacinia metus ac mauris sodales id feugiat mi pellentesque. Vestibulum sed est at quam facilisis vestibulum.';
			
			$post = array(
				'post_title' => 'Dummy Post #'.$i,
				'post_status' => 'publish',
				'post_content' => $text,
				'post_type' => $_POST['type'],
			);
			
			// give post a random category
			if( isset( $cats ) && is_array( $cats ) ) {

				shuffle( $cats );
				$post['post_category'] = array( $cats[0]->term_id );
			}
			
			// create post
			$post_id = wp_insert_post( $post );

			// add the post to a menu
			if( isset( $_POST['type'] ) && $_POST['type'] == 'nav_menu_item' && isset( $_POST['menu'] ) ) {
				wp_set_object_terms( $post_id, intval( $_POST['menu'] ), 'nav_menu' );
			}
			
			// give post a random image
			if( isset( $images ) && is_array( $images ) ) {

				shuffle( $images );
				update_post_meta( $post_id, '_thumbnail_id', $images[0]->ID );
			}

			// add a meta value to help us delete later
			update_post_meta( $post_id, 'dummy_post', 1 );

			if( $post_id ) {
				$tracker++;
			}
		}

		if( $tracker ) {
			echo $tracker . ' posts were created.';
		} else {
			echo 'Failed to create posts.';
		}

	}
}
$GLOBALS['dummy_posts'] = new DummyPosts();