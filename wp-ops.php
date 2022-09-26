<?php
/*
Plugin Name: WP Ops
Description: Pequeñas medidas de seguridad para WordPress.
Author: Sergio Cruz
Plugin URI: https://github.com/sergioccrr/wp-ops
Version: 1.0.0
*/


add_action('wp_loaded', '_wp_ops_main');

function _wp_ops_main() {
	/**
	 * Desactivar el editor de archivos (!!) del panel de administración
	 *
	 * @see https://developer.wordpress.org/apis/wp-config-php/#disable-the-plugin-and-theme-file-editor
	 */
	if (defined('DISALLOW_FILE_EDIT') !== true) {
		define('DISALLOW_FILE_EDIT', true);
	}


	/**
	 * Bloquear acceso a WP-JSON así como eliminar su meta tag
	 *
	 * Se aplica sólo a usuarios anónimos o que no tengan permisos de edición
	 * de posts, ya que el editor de bloques (aka Gutenberg) hace uso de esta API
	 */
	if (current_user_can('edit_posts') !== true) {
		add_filter('rest_api_init', '_wp_ops_send_404', PHP_INT_MAX);
		add_filter('rest_url', '__return_empty_string', PHP_INT_MAX);
	}


	/* Ocultar versión de Wordpress */
	add_filter('the_generator', '__return_empty_string', PHP_INT_MAX);


	/**
	 * Quitar meta tags del wlwmanifest y del XML-RPC. Esto sólo es maquillaje
	 *
	 * IMPORTANTE bloquear el acceso. Eso únicamente se puede desde el servidor
	 *
	 * Nota: las llamadas a remove_action deben ir sin prioridad o no funcionarán
	 */
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
}



function _wp_ops_send_404() {
	global $wp_query;
	$wp_query->set_404();

	status_header(404);
	nocache_headers();

	include get_query_template('404');
	exit;
}
