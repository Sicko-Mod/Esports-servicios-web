<?php
/**
 * Tema hijo de Astra para Jaguares E-Sports.
 *
 * Un tema hijo permite personalizar Astra sin tocar sus archivos. Cuando
 * Astra se actualiza, estos cambios sobreviven. Nunca editen el tema
 * Astra directamente.
 *
 * @package Jaguares
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const JAGUARES_VERSION = '1.0.0';

/**
 * Carga el CSS del tema hijo después del de Astra.
 *
 * La versión sale del archivo modificado, así el navegador de los
 * visitantes agarra los cambios sin tener que limpiar caché.
 */
function jaguares_estilos(): void {
	$ruta = get_stylesheet_directory() . '/assets/jaguares.css';

	wp_enqueue_style(
		'jaguares-fuentes',
		'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600&display=swap',
		[],
		null
	);

	wp_enqueue_style(
		'jaguares',
		get_stylesheet_directory_uri() . '/assets/jaguares.css',
		[ 'astra-theme-css', 'jaguares-fuentes' ],
		file_exists( $ruta ) ? (string) filemtime( $ruta ) : JAGUARES_VERSION
	);
}

/**
 * Precarga el origen de las fuentes: se ven antes en conexiones lentas.
 */
function jaguares_preconectar(): void {
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'jaguares_preconectar', 1 );
add_action( 'wp_enqueue_scripts', 'jaguares_estilos', 15 );

/**
 * Deja usar los shortcodes del plugin dentro de los widgets.
 */
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Ancho de contenido para las plantillas de juego y torneo.
 */
function jaguares_ancho_contenido(): void {
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1100;
	}
}
add_action( 'after_setup_theme', 'jaguares_ancho_contenido', 0 );

/**
 * Muestra el roster automáticamente al final de la página de cada juego,
 * para no tener que pegar el shortcode a mano en cada una.
 */
function jaguares_roster_automatico( string $contenido ): string {
	if ( ! is_singular( 'jg_juego' ) || ! in_the_loop() || ! is_main_query() ) {
		return $contenido;
	}

	if ( has_shortcode( $contenido, 'jg_roster' ) ) {
		return $contenido;
	}

	return $contenido . do_shortcode( '[jg_roster]' );
}
add_filter( 'the_content', 'jaguares_roster_automatico' );

/**
 * Quita la fecha de publicación de juegos y torneos: no aporta nada y
 * hace ver el contenido más viejo de lo que es.
 */
function jaguares_limpiar_meta( $partes ) {
	if ( ! is_singular( [ 'jg_juego', 'jg_torneo' ] ) ) {
		return $partes;
	}

	// Astra pasa un array en unas versiones y una cadena en otras.
	return is_array( $partes ) ? [] : '';
}
add_filter( 'astra_single_post_meta', 'jaguares_limpiar_meta' );

/**
 * Texto del pie de página.
 */
function jaguares_pie(): string {
	return sprintf(
		'Club de Deportes Electrónicos · Universidad Americana · %s',
		esc_html( (string) gmdate( 'Y' ) )
	);
}
add_filter( 'astra_footer_copyright', 'jaguares_pie' );

/**
 * El archivo automático de torneos ocupaba la URL /torneos/ y dejaba sin
 * efecto la página «Torneos». Gana la página, que trae el shortcode con
 * los próximos y el historial.
 */
function jaguares_sin_archivo_torneos( array $args, string $tipo ): array {
	if ( 'jg_torneo' === $tipo ) {
		$args['has_archive'] = false;
	}

	return $args;
}
add_filter( 'register_post_type_args', 'jaguares_sin_archivo_torneos', 10, 2 );


/**
 * Texto del pie en el constructor de pie de pagina de Astra 4.
 * El filtro astra_footer_copyright de arriba se quedo viejo; este es el
 * que usa la version actual del tema padre.
 */
function jaguares_pie_astra4( $valor ) {
	return sprintf(
		'Club de Deportes Electrónicos · Universidad Americana · %s',
		esc_html( (string) gmdate( 'Y' ) )
	);
}
add_filter( 'astra_get_option_footer-copyright-editor', 'jaguares_pie_astra4' );
add_filter( 'astra_get_option_copyright-text', 'jaguares_pie_astra4' );
