<?php
/**
 * Template Name: Custom Page Template
 * Description: A Page Template.
 */
 
$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['footer_widget'] = Timber::get_widgets('footer_widget');

Timber::render( array('pages/'.$post->title.'.twig', 'pages/default.twig'), $context );