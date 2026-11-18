<?php
/**
 * Homepage. Every section is edited in Appearance → Customize → Hayabella: Homepage.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/usp' );
get_template_part( 'template-parts/home/rail', null, array( 'n' => 1 ) );
get_template_part( 'template-parts/home/split' );
get_template_part( 'template-parts/home/marquee' );
get_template_part( 'template-parts/home/rail', null, array( 'n' => 2 ) );
get_template_part( 'template-parts/home/campaign' );
get_template_part( 'template-parts/home/occasions' );
get_template_part( 'template-parts/home/rail', null, array( 'n' => 3 ) );
get_template_part( 'template-parts/home/story' );

// Content of the page set as the homepage (Settings → Reading), if it has any.
if ( 'page' === get_option( 'show_on_front' ) ) {
	while ( have_posts() ) {
		the_post();
		if ( '' !== trim( get_the_content() ) ) {
			echo '<section class="section"><div class="container entry-content">';
			the_content();
			echo '</div></section>';
		}
	}
}

get_template_part( 'template-parts/home/looks' );
get_template_part( 'template-parts/home/newsletter' );
get_template_part( 'template-parts/home/perks' );

get_footer();
