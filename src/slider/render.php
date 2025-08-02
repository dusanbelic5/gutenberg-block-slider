<?php
/**
 * Dynamic render file for the Slider block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selected_posts = isset( $attributes['selectedPosts'] ) ? $attributes['selectedPosts'] : [];

if ( empty( $selected_posts ) || ! is_array( $selected_posts ) ) {
	return;
}

// Enqueue styles/scripts
wp_enqueue_style( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', [], null );
wp_enqueue_script( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', [ 'jquery' ], null, true );

$bg_color    = ! empty( $attributes['backgroundColor'] ) ? esc_attr( $attributes['backgroundColor'] ) : '';
$text_color  = ! empty( $attributes['textColor'] ) ? esc_attr( $attributes['textColor'] ) : '';
$arrow_color = ! empty( $attributes['arrowColor'] ) ? esc_attr( $attributes['arrowColor'] ) : '';
$unique_id   = ! empty( $attributes['uniqueId'] ) ? esc_attr( $attributes['uniqueId'] ) : uniqid( 'slider-' );

?>
<div 
	id="<?php echo esc_attr( $unique_id ); ?>"
	class="owl-carousel my-slider <?php echo 'bg-' . $bg_color . ' arrow-color-' . $arrow_color; ?>"
	data-slider-id="<?php echo esc_attr( $unique_id ); ?>"
	data-nav="<?php echo ! empty( $attributes['arrowShow'] ) ? 'true' : 'false'; ?>"
>
	<?php
	$query = new WP_Query( [
		'post__in' => $selected_posts,
		'orderby'  => 'post__in',
	] );

	while ( $query->have_posts() ) :
		$query->the_post();
		?>
		<div class="slide">
			<a href="<?php the_permalink(); ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium' ); ?>
				<?php endif; ?>
				<h3 class="<?php echo 'text-' . $text_color; ?>"><?php the_title(); ?></h3>
			</a>
		</div>
	<?php endwhile;
	wp_reset_postdata();
	?>
</div>

<script>
	jQuery(document).ready(function($){
		$('#<?php echo esc_js( $unique_id ); ?>').owlCarousel({
			items: 1,
			loop: true,
			nav: <?php echo ! empty( $attributes['arrowShow'] ) ? 'true' : 'false'; ?>,
			dots: false
		});
	});
</script>
