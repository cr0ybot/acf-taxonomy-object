<?php
/**
 * ACF 5
 */
class acf_field_taxonomy_object extends acf_field {

	function __construct() {

		$this->name = 'taxonomy-object';
		$this->label = __( 'Taxonomy Object', 'acf-taxonomy-object' );
		$this->category = 'relational';
		$this->defaults = array(
			'choices' => array(),
			'allow_null' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'object',
			'multiple' => 0,
		);
		$this->l10n = array(
			//'error'	=> __( 'Error! Please enter a higher value', 'acf-taxonomy-object' ),
		);

		parent::__construct();
	}

	function render_field_settings( $field ) {

		// choices : Allowed Taxonomies
		acf_render_field_setting( $field, array(
			'label' => __( 'Choose Allowed Taxonomies', 'acf-taxonomy-object' ),
			'instructions' => '',
			'type' => 'select',
			'name' => 'choices',
			'choices' => acf_get_pretty_taxonomies(),
			'multiple' => 1,
			'ui' => 1,
			'allow_null' => 1,
			'placeholder' => __( 'All Taxonomies', 'acf-taxonomy-object' )
		));

		 // taxonomy object, slug, or id
		acf_render_field_setting( $field, array(
			'label' => __( 'Return Format', 'acf' ),
			'instructions' => __( 'Specify the value returned', 'acf' ),
			'type' => 'radio',
			'name' => 'return_format',
			'choices' => array(
				'object' => __( 'Object','acf-taxonomy-object' ),
				'slug' => __( 'Slug','acf-taxonomy-object' ),
			),
			'layout' => 'horizontal'
		));

		// allow multiple
		acf_render_field_setting( $field, array(
			'label' => __( 'Select multiple taxonomies?', 'acf-taxonomy-object' ),
			'instructions' => '',
			'type' => 'true_false',
			'name' => 'multiple',
			'ui' => 1
		));

		// ui
		/*
		acf_render_field_setting( $field, array(
			'label' => __( 'Stylised UI', 'acf' ),
			'instructions' => '',
			'name' => 'ui',
			'type' => 'true_false',
			'ui' => 1,
		));
		*/
	}

	function render_field( $field ) {
		$taxonomies = array();
		$taxonomy_terms = acf_get_taxonomy_terms();
		$selected_taxonomies = array();

		$slugs = ! empty( $field['choices'] ) ? $field['choices'] : array_keys( acf_get_pretty_taxonomies() );

		// select taxonomies
		foreach ( $slugs as $slug ) { // only use allowed taxonomies
			$taxonomies[ $slug ] = get_taxonomy( $slug );
		}
		foreach ( $taxonomies as $taxonomy ) {
			$selected_taxonomies[ $taxonomy->name ] = $taxonomy->label;
		}

		$field['choices'] = $selected_taxonomies;

		// add empty value (allows '' to be selected)
		if ( empty( $field['value'] ) ){
			$field['value'] = '';
		}

		// placeholder
		if ( empty( $field['placeholder'] ) ) {
			$field['placeholder'] = __( 'Select', 'acf-taxonomy-object' );
		}

		// allow null
		if ( $field['allow_null'] && ! $field['multiple'] ) {
			$prepend = array( '' => '- ' . $field['placeholder'] . ' -' );
			$field['choices'] = $prepend + $field['choices'];
		}

		// vars
		$atts = array(
			'id' => $field['id'],
			//'class' => $field['class'] . ' js-multi-taxonomy-object2',
			'class' => $field['class'],
			'name' => $field['name'],
			'data-ui' => $field['ui'],
			'data-ajax' => $field['ajax'],
			'data-placeholder' => $field['placeholder'],
			'data-allow_null' => $field['allow_null'],
			'data-multiple' => $field['multiple']
		);

		// multiple
		if( $field['multiple'] ) {
			$atts['multiple'] = 'multiple';
			$atts['size'] = 5;
			$atts['name'] .= '[]';
		}

		// ui
		if ( $field['ui'] ) {
			$atts['disabled'] = 'disabled';
			$atts['class'] .= ' acf-hidden';
		}

		// special atts
		foreach ( array( 'readonly', 'disabled' ) as $k ) {
			if ( ! empty( $field[$k] ) ) {
				$atts[$k] = $k;
			}
		}

		// hidden input
		if ( $field['ui'] ) {
			$v = $field['value'];

			if( $field['multiple'] ) {
				$v = implode( '||', $v );
			}
			else {
				$v = acf_maybe_get($v, 0, '');
			}

			acf_hidden_input( array(
				'id' => $field['id'] . '-input',
				'name' => $field['name'],
				'value' => $v
			) );
		}
		elseif ( $field['multiple'] ) {
			acf_hidden_input( array(
				'id' => $field['id'] . '-input',
				'name' => $field['name']
			) );
		}

		// vars
		$els = array();
		$choices = array();

		// loop through values and add them as options
		if ( ! empty( $field['choices'] ) ) {

			foreach ( $field['choices'] as $k => $v ) { // allowed taxonomies

				if ( is_array( $v ) ) {

					// optgroup
					$els[] = array(
						'type' => 'optgroup',
						'label' => $k
					);

					if ( ! empty( $v ) ) {

						foreach ( $v as $k2 => $v2 ) {

							$strip_v2_hyphen = preg_replace( '#-\s?#', '', $v2 ); // Child categories have hyphens before the name, we need to remove them in order to match them

							preg_match( '#(?::)(.*)#', $k2, $matches ); // originally returns 'taxonomy:term-slug' this removes 'taxonomy:'

							$els[] = array(
								'type' => 'option',
								'value' => $matches[1],
								'label' => $v2,
								'selected' => $slct = ( $matches[1] == $field['value'] ? 'selected': '' )
							);

							$choices[] = $k2;
						}
					}

					$els[] = array( 'type' => '/optgroup' );
				}
				else { // return value = taxonomy slug
					$els[] = array(
						'type' => 'option',
						'value' => $k,
						'label' => $v,
						'selected' => $slct = ( $k == $field['value'] ? 'selected': '' )
					);
					$choices[] = $k;
				}
			}
		}

		// null
		if( $field['allow_null'] ) {
			array_unshift( $els, array(
				'type' => 'option',
				'value' => '',
				'label' => '- ' . $field['placeholder'] . ' -'
			) );
		}

		// construct html
		echo '<select ' . acf_esc_attr( $atts ) . '>';
		$this->walk( $field['choices'], $field['value'] );
		echo '</select>';
	}

	/**
	 * walk
	 *
	 * Output option HTML
	 */
	function walk( $choices, $values ) {

		// bail early if no choices
		if( empty( $choices ) ) return;

		// loop
		foreach( $choices as $k => $v ) {

			// optgroup
			if( is_array( $v ) ){

				// optgroup
				echo '<optgroup label="' . esc_attr( $k ) . '">';

				// walk
				$this->walk( $v, $values );

				// close optgroup
				echo '</optgroup>';

				// break
				continue;
			}

			// vars
			$search = html_entity_decode( $k );
			$pos = array_search( $search, $values );
			$atts = array( 'value' => $k );

			// validate selected
			if( $pos !== false ) {
				$atts['selected'] = 'selected';
				$atts['data-i'] = $pos;
			}

			// option
			echo '<option ' . acf_esc_attr( $atts ) . '>' . $v . '</option>';
		}
	}

	function input_admin_enqueue_scripts() {

		// bail ealry if no enqueue
		if ( ! acf_get_setting('enqueue_select2') ) return;

		// globals
		global $wp_scripts, $wp_styles;

		// vars
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$major = acf_get_setting( 'select2_version' );
		$version = '';
		$script = '';
		$style = '';

		// attempt to find 3rd party Select2 version
		// - avoid including v3 CSS when v4 JS is already enququed
		if ( isset( $wp_scripts->registered['select2'] ) ) {
			$major = (int) $wp_scripts->registered['select2']->ver;
		}

		if ( $major == 4 ) {
			// v4
			$version = '4.0';
			$script = acf_get_dir( "assets/inc/select2/4/select2.full{$min}.js" );
			$style = acf_get_dir( "assets/inc/select2/4/select2{$min}.css" );
		}
		else {
			// v3
			$version = '3.5.2';
			$script = acf_get_dir( "assets/inc/select2/3/select2{$min}.js" );
			$style = acf_get_dir( "assets/inc/select2/3/select2.css" );
		}

		// enqueue
		wp_enqueue_script( 'select2', $script, array( 'jquery' ), $version );
		wp_enqueue_style( 'select2', $style, '', $version );
	}

	/**
	 * load_value()
	 *
	 * This filter is applied to the $value after it is loaded from the db
	 */
	function load_value( $value, $post_id, $field ) {

		// ACF4 null
		if( $value === 'null' ) return false;

		return $value;
	}

	/**
	 * update_field
	 *
	 * This filter is appied to the $field before it is saved to the database
	 */
	function update_field( $field ) {

		// decode choices (convert to array)
		$field['choices'] = acf_decode_choices( $field['choices'] );
		//$field['default_value'] = acf_decode_choices( $field['default_value'], true );

		// return
		return $field;
	}


	/**
	 * update_value
	 *
	 * This filter is appied to the $value before it is updated in the db
	 */
	function update_value( $value, $post_id, $field ) {

		// validate
		if( empty( $value ) ) {
			return $value;
		}

		// array
		if( is_array( $value ) ) {
			// save value as strings, so we can clearly search for them in SQL LIKE statements
			$value = array_map( 'strval', $value );
		}

		return $value;
	}

	/**
	 * translate_field
	 *
	 * This function will translate field settings
	 */
	function translate_field( $field ) {

		// translate
		$field['choices'] = acf_translate( $field['choices'] );

		return $field;
	}

	/**
	 * format_value
	 *
	 * This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*/
	function format_value( $value, $post_id, $field ) {

		// array
		if( acf_is_array( $value ) ) {

			foreach( $value as $i => $v ) {
				$value[ $i ] = $this->format_value_single( $v, $post_id, $field );
			}
		}
		else {
			$value = $this->format_value_single( $value, $post_id, $field );
		}

		return $value;
	}

	function format_value_single( $value, $post_id, $field ) {

		// bail ealry if is empty
		if( acf_is_empty( $value ) ) return $value;

		// vars
		//$label = acf_maybe_get( $field['choices'], $value, $value );

		if ( $field['return_format'] == 'object' ) {
			$value = get_taxonomy( $value );
		}

		return $value;
	}
}


// create field
new acf_field_taxonomy_object();

?>
