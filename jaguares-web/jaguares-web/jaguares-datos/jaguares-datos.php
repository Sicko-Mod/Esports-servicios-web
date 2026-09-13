<?php
/**
 * Plugin Name:       Jaguares Datos
 * Description:       Juegos, roster público y torneos del club Jaguares E-Sports. Solo datos públicos: nunca guarda CIF, teléfonos ni fechas de nacimiento.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Club Jaguares E-Sports - UAM
 * Text Domain:       jaguares
 *
 * Va como plugin y no dentro del tema a propósito: si algún día cambian
 * de tema, el roster y los torneos siguen existiendo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const JG_VERSION = '1.0.0';

/* -------------------------------------------------------------------------
 * 1. Tipos de contenido
 * ---------------------------------------------------------------------- */

function jg_registrar_tipos(): void {

	register_post_type(
		'jg_juego',
		[
			'labels'       => [
				'name'               => 'Juegos',
				'singular_name'      => 'Juego',
				'add_new_item'       => 'Agregar juego',
				'edit_item'          => 'Editar juego',
				'not_found'          => 'Todavía no hay juegos registrados.',
				'menu_name'          => 'Jaguares',
			],
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-games',
			'menu_position'=> 26,
			'supports'     => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
			'rewrite'      => [ 'slug' => 'juegos' ],
			'show_in_rest' => true,
		]
	);

	register_post_type(
		'jg_miembro',
		[
			'labels'       => [
				'name'          => 'Roster',
				'singular_name' => 'Integrante',
				'add_new_item'  => 'Agregar integrante',
				'edit_item'     => 'Editar integrante',
				'not_found'     => 'Todavía no hay integrantes en el roster.',
			],
			'public'       => true,
			'has_archive'  => false,
			'show_in_menu' => 'edit.php?post_type=jg_juego',
			'supports'     => [ 'title', 'thumbnail' ],
			'rewrite'      => [ 'slug' => 'roster' ],
			'show_in_rest' => true,
		]
	);

	register_post_type(
		'jg_torneo',
		[
			'labels'       => [
				'name'          => 'Torneos',
				'singular_name' => 'Torneo',
				'add_new_item'  => 'Agregar torneo',
				'edit_item'     => 'Editar torneo',
				'not_found'     => 'Todavía no hay torneos publicados.',
			],
			'public'       => true,
			'has_archive'  => true,
			'show_in_menu' => 'edit.php?post_type=jg_juego',
			'supports'     => [ 'title', 'editor', 'thumbnail' ],
			'rewrite'      => [ 'slug' => 'torneos' ],
			'show_in_rest' => true,
		]
	);

	register_taxonomy(
		'jg_tier',
		[ 'jg_miembro' ],
		[
			'labels'            => [
				'name'          => 'Niveles',
				'singular_name' => 'Nivel',
			],
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => [ 'slug' => 'nivel' ],
		]
	);
}
add_action( 'init', 'jg_registrar_tipos' );

/**
 * Crea los niveles y los juegos iniciales la primera vez.
 */
function jg_activar(): void {
	jg_registrar_tipos();

	$niveles = [
		'tier-1'  => [ 'Tier 1', 'Equipo que representa a la universidad' ],
		'tier-2'  => [ 'Tier 2', 'Segundo equipo' ],
		'miembro' => [ 'Miembro', 'Integrante del grupo' ],
	];

	foreach ( $niveles as $slug => $datos ) {
		if ( ! term_exists( $slug, 'jg_tier' ) ) {
			wp_insert_term(
				$datos[0],
				'jg_tier',
				[ 'slug' => $slug, 'description' => $datos[1] ]
			);
		}
	}

	// "Warzone y Redsec" es un solo grupo, no dos.
	$juegos = [ 'Valorant', 'Warzone y Redsec', 'Overwatch', 'Fight Games', 'Marvel Rivals' ];

	foreach ( $juegos as $orden => $juego ) {
		$existe = get_page_by_path( sanitize_title( $juego ), OBJECT, 'jg_juego' );
		if ( ! $existe ) {
			wp_insert_post(
				[
					'post_title'  => $juego,
					'post_name'   => sanitize_title( $juego ),
					'post_type'   => 'jg_juego',
					'post_status' => 'publish',
					'menu_order'  => $orden,
				]
			);
		}
	}

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'jg_activar' );

function jg_desactivar(): void {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'jg_desactivar' );

/* -------------------------------------------------------------------------
 * 2. Campos personalizados
 * ---------------------------------------------------------------------- */

/**
 * Definición de los campos de cada tipo. Todo lo que aparece aquí es
 * información pública. Nada de CIF, teléfono ni fecha de nacimiento.
 */
function jg_campos( string $tipo ): array {
	$campos = [
		'jg_juego'   => [
			'jg_dia'      => [ 'label' => 'Día de reunión', 'tipo' => 'select', 'opciones' => [
				''          => 'Sin definir',
				'lunes'     => 'Lunes',
				'martes'    => 'Martes',
				'miercoles' => 'Miércoles',
				'jueves'    => 'Jueves',
				'viernes'   => 'Viernes',
				'sabado'    => 'Sábado',
				'domingo'   => 'Domingo',
			] ],
			'jg_hora'     => [ 'label' => 'Hora de reunión', 'tipo' => 'time' ],
			'jg_lugar'    => [ 'label' => 'Lugar', 'tipo' => 'text', 'ayuda' => 'Aula, laboratorio o enlace de Discord.' ],
			'jg_capitan'  => [ 'label' => 'Capitán', 'tipo' => 'text', 'ayuda' => 'Gamer tag del capitán de este semestre.' ],
			'jg_activo'   => [ 'label' => 'Grupo activo este semestre', 'tipo' => 'checkbox' ],
		],
		'jg_miembro' => [
			'jg_juego_slug' => [ 'label' => 'Juego', 'tipo' => 'juego' ],
			'jg_nombre_publico' => [
				'label' => 'Nombre real',
				'tipo'  => 'text',
				'ayuda' => 'Opcional y solo con permiso expreso de la persona. Dejalo vacío si es menor de edad.',
			],
		],
		'jg_torneo'  => [
			'jg_juego_slug'  => [ 'label' => 'Juego', 'tipo' => 'juego' ],
			'jg_fecha'       => [ 'label' => 'Fecha de inicio', 'tipo' => 'date' ],
			'jg_fecha_fin'   => [ 'label' => 'Fecha de cierre', 'tipo' => 'date' ],
			'jg_modalidad'   => [ 'label' => 'Modalidad', 'tipo' => 'select', 'opciones' => [
				'individual' => 'Individual',
				'equipo'     => 'Por equipo',
			] ],
			'jg_abierto'     => [ 'label' => 'Abierto a personas externas', 'tipo' => 'checkbox' ],
			'jg_inscripcion' => [ 'label' => 'Enlace de inscripción', 'tipo' => 'url' ],
		],
	];

	return $campos[ $tipo ] ?? [];
}

function jg_agregar_cajas(): void {
	foreach ( [ 'jg_juego', 'jg_miembro', 'jg_torneo' ] as $tipo ) {
		add_meta_box(
			'jg_detalles',
			'Detalles',
			'jg_pintar_caja',
			$tipo,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'jg_agregar_cajas' );

function jg_pintar_caja( WP_Post $post ): void {
	$campos = jg_campos( $post->post_type );
	if ( ! $campos ) {
		return;
	}

	wp_nonce_field( 'jg_guardar_' . $post->ID, 'jg_nonce' );

	echo '<p class="jg-aviso" style="background:#FFF4E0;border-left:4px solid #C8862B;padding:10px 14px;margin:0 0 18px;">';
	echo 'Este sitio es público. No ingresés acá número de carnet (CIF), teléfono, correo, fecha de nacimiento ni datos de tutores.';
	echo '</p>';

	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( $campos as $clave => $campo ) {
		$valor = get_post_meta( $post->ID, $clave, true );
		printf(
			'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td>',
			esc_attr( $clave ),
			esc_html( $campo['label'] )
		);

		switch ( $campo['tipo'] ) {
			case 'checkbox':
				printf(
					'<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>',
					esc_attr( $clave ),
					checked( $valor, '1', false )
				);
				break;

			case 'select':
				printf( '<select id="%1$s" name="%1$s">', esc_attr( $clave ) );
				foreach ( $campo['opciones'] as $op_valor => $op_texto ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $op_valor ),
						selected( $valor, $op_valor, false ),
						esc_html( $op_texto )
					);
				}
				echo '</select>';
				break;

			case 'juego':
				$juegos = get_posts(
					[
						'post_type'      => 'jg_juego',
						'posts_per_page' => -1,
						'orderby'        => 'menu_order title',
						'order'          => 'ASC',
					]
				);
				printf( '<select id="%1$s" name="%1$s">', esc_attr( $clave ) );
				echo '<option value="">Sin asignar</option>';
				foreach ( $juegos as $juego ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $juego->post_name ),
						selected( $valor, $juego->post_name, false ),
						esc_html( $juego->post_title )
					);
				}
				echo '</select>';
				break;

			default:
				printf(
					'<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" class="regular-text">',
					esc_attr( $campo['tipo'] ),
					esc_attr( $clave ),
					esc_attr( $valor )
				);
		}

		if ( ! empty( $campo['ayuda'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $campo['ayuda'] ) );
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

function jg_guardar( int $post_id ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['jg_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['jg_nonce'] ) ), 'jg_guardar_' . $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$tipo   = get_post_type( $post_id );
	$campos = jg_campos( (string) $tipo );

	foreach ( $campos as $clave => $campo ) {
		if ( 'checkbox' === $campo['tipo'] ) {
			update_post_meta( $post_id, $clave, isset( $_POST[ $clave ] ) ? '1' : '' );
			continue;
		}

		$bruto = isset( $_POST[ $clave ] ) ? wp_unslash( $_POST[ $clave ] ) : '';
		$valor = is_string( $bruto ) ? $bruto : '';

		$valor = match ( $campo['tipo'] ) {
			'url'   => esc_url_raw( $valor ),
			'juego' => sanitize_title( $valor ),
			default => sanitize_text_field( $valor ),
		};

		update_post_meta( $post_id, $clave, $valor );
	}
}
add_action( 'save_post', 'jg_guardar' );

/* -------------------------------------------------------------------------
 * 3. Consultas
 * ---------------------------------------------------------------------- */

function jg_traer_juegos( bool $solo_activos = true ): array {
	$args = [
		'post_type'      => 'jg_juego',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	];

	if ( $solo_activos ) {
		$args['meta_query'] = [
			[
				'key'   => 'jg_activo',
				'value' => '1',
			],
		];
	}

	return get_posts( $args );
}

function jg_traer_roster( string $juego_slug ): array {
	$miembros = get_posts(
		[
			'post_type'      => 'jg_miembro',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_query'     => [
				[
					'key'   => 'jg_juego_slug',
					'value' => $juego_slug,
				],
			],
		]
	);

	$por_nivel = [ 'tier-1' => [], 'tier-2' => [], 'miembro' => [] ];

	foreach ( $miembros as $miembro ) {
		$terminos = wp_get_post_terms( $miembro->ID, 'jg_tier', [ 'fields' => 'slugs' ] );
		$nivel    = ( is_array( $terminos ) && $terminos ) ? $terminos[0] : 'miembro';

		if ( ! isset( $por_nivel[ $nivel ] ) ) {
			$nivel = 'miembro';
		}

		$por_nivel[ $nivel ][] = $miembro;
	}

	return $por_nivel;
}

function jg_dia_legible( string $slug ): string {
	$dias = [
		'lunes'     => 'Lunes',
		'martes'    => 'Martes',
		'miercoles' => 'Miércoles',
		'jueves'    => 'Jueves',
		'viernes'   => 'Viernes',
		'sabado'    => 'Sábado',
		'domingo'   => 'Domingo',
	];

	return $dias[ $slug ] ?? '';
}

function jg_hora_legible( string $hora ): string {
	if ( '' === $hora ) {
		return '';
	}

	$marca = strtotime( $hora );

	return false === $marca ? '' : date_i18n( 'g:i a', $marca );
}

/* -------------------------------------------------------------------------
 * 4. Shortcodes
 * ---------------------------------------------------------------------- */

/**
 * [jg_juegos] - Los grupos activos, con su horario y capitán.
 */
function jg_sc_juegos( $atts ): string {
	$atts = shortcode_atts( [ 'todos' => 'no' ], $atts, 'jg_juegos' );

	$juegos = jg_traer_juegos( 'si' !== $atts['todos'] );

	if ( ! $juegos ) {
		return '<p class="jg-vacio">Todavía no hay grupos activos. Volvé a revisar al inicio del semestre.</p>';
	}

	$salida = '<ul class="jg-juegos">';

	foreach ( $juegos as $juego ) {
		$dia     = jg_dia_legible( (string) get_post_meta( $juego->ID, 'jg_dia', true ) );
		$hora    = jg_hora_legible( (string) get_post_meta( $juego->ID, 'jg_hora', true ) );
		$lugar   = (string) get_post_meta( $juego->ID, 'jg_lugar', true );
		$capitan = (string) get_post_meta( $juego->ID, 'jg_capitan', true );

		$salida .= '<li class="jg-juego">';
		$salida .= sprintf(
			'<h3 class="jg-juego__nombre"><a href="%s">%s</a></h3>',
			esc_url( (string) get_permalink( $juego ) ),
			esc_html( $juego->post_title )
		);

		$salida .= '<dl class="jg-juego__datos">';

		if ( $dia || $hora ) {
			$salida .= '<dt>Se reúne</dt><dd>' . esc_html( trim( $dia . ' ' . $hora ) ) . '</dd>';
		}
		if ( $lugar ) {
			$salida .= '<dt>Lugar</dt><dd>' . esc_html( $lugar ) . '</dd>';
		}
		if ( $capitan ) {
			$salida .= '<dt>Capitán</dt><dd>' . esc_html( $capitan ) . '</dd>';
		}

		$salida .= '</dl></li>';
	}

	return $salida . '</ul>';
}
add_shortcode( 'jg_juegos', 'jg_sc_juegos' );

/**
 * [jg_roster juego="valorant"] - El roster de un juego, ordenado por nivel.
 */
function jg_sc_roster( $atts ): string {
	$atts = shortcode_atts( [ 'juego' => '' ], $atts, 'jg_roster' );

	$slug = sanitize_title( (string) $atts['juego'] );

	if ( '' === $slug ) {
		$actual = get_post();
		if ( $actual instanceof WP_Post && 'jg_juego' === $actual->post_type ) {
			$slug = $actual->post_name;
		}
	}

	if ( '' === $slug ) {
		return '<p class="jg-vacio">Indicá el juego. Por ejemplo: [jg_roster juego="valorant"]</p>';
	}

	$roster = jg_traer_roster( $slug );

	if ( ! array_filter( $roster ) ) {
		return '<p class="jg-vacio">El roster de este grupo todavía no está publicado.</p>';
	}

	$titulos = [
		'tier-1'  => [ 'Tier 1', 'Representa a la universidad' ],
		'tier-2'  => [ 'Tier 2', 'Segundo equipo' ],
		'miembro' => [ 'Miembros', '' ],
	];

	$salida = '<div class="jg-roster">';

	foreach ( $roster as $nivel => $integrantes ) {
		if ( ! $integrantes ) {
			continue;
		}

		$salida .= sprintf( '<section class="jg-nivel jg-nivel--%s">', esc_attr( $nivel ) );
		$salida .= '<h3 class="jg-nivel__titulo">' . esc_html( $titulos[ $nivel ][0] ) . '</h3>';

		if ( $titulos[ $nivel ][1] ) {
			$salida .= '<p class="jg-nivel__nota">' . esc_html( $titulos[ $nivel ][1] ) . '</p>';
		}

		$salida .= '<ul class="jg-nivel__lista">';

		foreach ( $integrantes as $integrante ) {
			$nombre = (string) get_post_meta( $integrante->ID, 'jg_nombre_publico', true );

			$salida .= '<li class="jg-integrante">';
			$salida .= '<span class="jg-integrante__tag">' . esc_html( $integrante->post_title ) . '</span>';

			if ( $nombre ) {
				$salida .= '<span class="jg-integrante__nombre">' . esc_html( $nombre ) . '</span>';
			}

			$salida .= '</li>';
		}

		$salida .= '</ul></section>';
	}

	return $salida . '</div>';
}
add_shortcode( 'jg_roster', 'jg_sc_roster' );

/**
 * [jg_torneos estado="proximos|pasados|todos"]
 */
function jg_sc_torneos( $atts ): string {
	$atts = shortcode_atts( [ 'estado' => 'proximos', 'cantidad' => '10' ], $atts, 'jg_torneos' );

	$hoy      = current_time( 'Y-m-d' );
	$cantidad = max( 1, (int) $atts['cantidad'] );

	$args = [
		'post_type'      => 'jg_torneo',
		'posts_per_page' => $cantidad,
		'meta_key'       => 'jg_fecha',
		'orderby'        => 'meta_value',
	];

	if ( 'pasados' === $atts['estado'] ) {
		$args['order']      = 'DESC';
		$args['meta_query'] = [
			[ 'key' => 'jg_fecha', 'value' => $hoy, 'compare' => '<', 'type' => 'DATE' ],
		];
	} elseif ( 'todos' !== $atts['estado'] ) {
		$args['order']      = 'ASC';
		$args['meta_query'] = [
			[ 'key' => 'jg_fecha', 'value' => $hoy, 'compare' => '>=', 'type' => 'DATE' ],
		];
	} else {
		$args['order'] = 'DESC';
	}

	$torneos = get_posts( $args );

	if ( ! $torneos ) {
		return 'pasados' === $atts['estado']
			? '<p class="jg-vacio">Todavía no hay torneos en el historial.</p>'
			: '<p class="jg-vacio">No hay torneos programados por ahora. Seguinos en redes para enterarte del próximo.</p>';
	}

	$salida = '<ul class="jg-torneos">';

	foreach ( $torneos as $torneo ) {
		$fecha       = (string) get_post_meta( $torneo->ID, 'jg_fecha', true );
		$juego_slug  = (string) get_post_meta( $torneo->ID, 'jg_juego_slug', true );
		$modalidad   = (string) get_post_meta( $torneo->ID, 'jg_modalidad', true );
		$abierto     = '1' === get_post_meta( $torneo->ID, 'jg_abierto', true );
		$inscripcion = (string) get_post_meta( $torneo->ID, 'jg_inscripcion', true );

		$juego = $juego_slug ? get_page_by_path( $juego_slug, OBJECT, 'jg_juego' ) : null;
		$marca = $fecha ? strtotime( $fecha ) : false;

		$salida .= '<li class="jg-torneo">';

		if ( false !== $marca ) {
			$salida .= sprintf(
				'<time class="jg-torneo__fecha" datetime="%s"><span class="jg-torneo__dia">%s</span><span class="jg-torneo__mes">%s</span></time>',
				esc_attr( $fecha ),
				esc_html( date_i18n( 'j', $marca ) ),
				esc_html( date_i18n( 'M', $marca ) )
			);
		}

		$salida .= '<div class="jg-torneo__cuerpo">';
		$salida .= '<h3 class="jg-torneo__nombre">' . esc_html( $torneo->post_title ) . '</h3>';

		$detalles = array_filter(
			[
				$juego instanceof WP_Post ? $juego->post_title : '',
				'equipo' === $modalidad ? 'Por equipo' : 'Individual',
				$abierto ? 'Abierto a externos' : 'Solo miembros del club',
			]
		);

		$salida .= '<p class="jg-torneo__meta">' . esc_html( implode( ' · ', $detalles ) ) . '</p>';

		if ( $torneo->post_content ) {
			$salida .= '<div class="jg-torneo__texto">' . wp_kses_post( wpautop( $torneo->post_content ) ) . '</div>';
		}

		if ( $inscripcion ) {
			$salida .= sprintf(
				'<a class="jg-boton" href="%s" rel="noopener">Inscribirme</a>',
				esc_url( $inscripcion )
			);
		}

		$salida .= '</div></li>';
	}

	return $salida . '</ul>';
}
add_shortcode( 'jg_torneos', 'jg_sc_torneos' );

/* -------------------------------------------------------------------------
 * 5. Recordatorio de privacidad en el escritorio
 * ---------------------------------------------------------------------- */

function jg_aviso_privacidad(): void {
	$pantalla = get_current_screen();

	if ( ! $pantalla || ! in_array( $pantalla->post_type, [ 'jg_juego', 'jg_miembro', 'jg_torneo' ], true ) ) {
		return;
	}

	if ( 'edit' !== $pantalla->base ) {
		return;
	}

	echo '<div class="notice notice-info"><p>';
	echo 'Todo lo que se guarda acá es visible para cualquiera en internet. El carnet, los teléfonos y los datos de tutores se manejan en el sistema de asistencia, nunca en este sitio.';
	echo '</p></div>';
}
add_action( 'admin_notices', 'jg_aviso_privacidad' );
