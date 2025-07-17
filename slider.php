<?php
/**
 * Plugin Name:       Slider
 * Description:       Slider block with post selection and Owl Carousel.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            You
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       slider
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register the block using metadata from block.json
function slider_block_init() {
	register_block_type( __DIR__ . '/build/slider', [
		'render_callback' => 'slider_render_callback',
	] );
}
add_action( 'init', 'slider_block_init' );

// Dynamic render callback
function slider_render_callback( $attributes ) {
	if ( empty( $attributes['selectedPosts'] ) || ! is_array( $attributes['selectedPosts'] ) ) {
		return '';
	}

	// Enqueue Owl Carousel assets
	wp_enqueue_style( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', [], null );
	wp_enqueue_script( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', [ 'jquery' ], null, true );

	ob_start();
	?>
	<?php
		$bg_color = ! empty( $attributes['backgroundColor'] ) ? esc_attr($attributes['backgroundColor'] ) : '';
		$text_color = ! empty($attributes['textColor']) ? esc_attr($attributes['textColor']): '';
		$arrow_color = ! empty($attributes['arrowColor']) ? esc_attr($attributes['arrowColor']): '';
	?>

	<div class="owl-carousel my-slider <?= 'bg-'.$bg_color?> <?= 'arrow-color-'.$arrow_color?>">
		<?php
		$query = new WP_Query( [
			'post__in' => $attributes['selectedPosts'],
			'orderby'  => 'post__in',
		] );

		while ( $query->have_posts() ) : $query->the_post(); ?>
			<div class="slide">
				<a href="<?= get_permalink();?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'medium' ); ?>
					<?php endif; ?>
					<h3 class="<?= 'text-'.$text_color ?>"><?php the_title(); ?></h3>
				</a>
			</div>
		<?php endwhile;
		wp_reset_postdata();
		?>
	</div>

	<script>
		jQuery(document).ready(function($){
			$('.my-slider').owlCarousel({
				items: 1,
				loop: true,
				nav: <?php echo ! empty( $attributes['arrowShow'] ) ? 'true' : 'false'; ?>,
				dots: false
			});
		});
	</script>

	<?php
	return ob_get_clean();
}
