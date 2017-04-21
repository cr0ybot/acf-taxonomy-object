<?php
/**
 * ACF 5
 */
class acf_field_taxonomy_chooser extends acf_field {

	function __construct( $settings ) {

		$this->name = 'taxonomy-select';
		$this->label = __( 'Taxonomy Select', 'acf-taxonomy-select' );
		$this->category = 'relational';
		$this->defaults = array(
			'choices' => array(),
			'allow_null' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_value' => 0,
			'multiple' => 0,
		);
		$this->l10n = array(
			//'error'	=> __( 'Error! Please enter a higher value', 'acf-taxonomy-select' ),
		);
		$this->settings = $settings;

		parent::__construct();
	}

	function render_field_settings( $field ) {

		// choices : Allowed Taxonomies
		acf_render_field_setting( $field, array(
			'label' => __( 'Choose Allowed Taxonomies', 'acf-taxonomy-select' ),
			'instructions' => '',
			'type' => 'select',
			'name' => 'choices',
			'choices' => acf_get_pretty_taxonomies(),
			'multiple' => 1,
			'ui' => 1,
			'allow_null' => 1,
			'placeholder' => __( 'All Taxonomies', 'acf-taxonomy-select' )
		));

		 // taxonomy object, slug, or id
		acf_render_field_setting( $field, array(
			'label' => __( 'Return Value','acf-taxonomy-select' ),
			'instructions' => __( 'Specify the returned value on front end', 'acf-taxonomy-select' ),
			'type' => 'radio',
			'name' => 'return_value',
			'choices' => array(
				0 => __( 'Object','acf-taxonomy-select' ),
				1 => __( 'Slug','acf-taxonomy-select' ),
				2 => __( 'ID','acf-taxonomy-select' ),
			),
			'layout' => 'horizontal'
		));

		// allow multiple
		acf_render_field_setting( $field, array(
			'label' => __('Select multiple values?','acf-taxonomy-select'),
			'instructions' => '',
			'type' => 'radio',
			'name' => 'multiple',
			'choices' => array(
				1 => __('Yes','acf-taxonomy-select'),
				0 => __('No','acf-taxonomy-select'),
			),
			'layout' => 'horizontal'
		));
	}

	function render_field( $field ) {

		//$taxonomies = acf_get_array( $taxonomies );
		//$taxonomies = acf_get_pretty_taxonomies( $taxonomies );
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
			//$field['value']['cat'] = '';
		}

		// placeholder
		if ( empty( $field['placeholder'] ) ) {
			$field['placeholder'] = __( 'Select', 'acf-taxonomy-select' );
		}

		// vars
		$atts = array(
			'id' => $field['id'],
			'class' => $field['class'] . ' js-multi-taxonomy-select2',
			'name' => $field['name'],
			'data-ui' => $field['ui'],
			'data-ajax' => $field['ajax'],
			'data-placeholder' => $field['placeholder'],
			'data-allow_null' => $field['allow_null']
		);

		// hidden input
		if ( $field['ui'] ) {
			acf_hidden_input( array(
				'type' => 'hidden',
				'id' => $field['id'],
				'name' => $field['name'],
				'value' => implode( ',', $field['value'] )
			));
		}
		elseif ( $field['multiple'] ) {
			acf_hidden_input(array(
				'type'	=> 'hidden',
				'name'	=> $field['name'],
			));
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

							switch ( $field['return_value'] ) {
								case 0: // return value = taxonomy object
									foreach ( $terms as $key => $val ) {
										if ( $val->name == $strip_v2_hyphen ) {
											$els[] = array(
												'type' => 'option',
												'value' => $val,
												'label' => $v2,
												'selected' => $slct = ( $val->term_id == $field['value'] ? 'selected': '' )
											);
										}
									}
									break;
								case 1: // return value = taxonomy slug
									preg_match( '#(?::)(.*)#', $k2, $value ); // originally returns 'taxonomy:term-slug' this removes 'taxonomy:'

									$els[] = array(
										'type' => 'option',
										'value' => $value[1],
										'label' => $v2,
										'selected' => $slct = ( $value[1] == $field['value'] ? 'selected': '' )
									);
									break;
								case 2: // return value = taxonomy ID
									foreach ( $terms as $key => $val ) {
										if ( $val->name == $strip_v2_hyphen ) {
											$els[] = array(
												'type' => 'option',
												'value' => $val->term_id,
												'label' => $v2 ,
												'selected' => $slct = ( $val->term_id == $field['value'] ? 'selected': '' )
											);
										}
									}
									break;
							}
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

		// html
		echo '<select ' . acf_esc_attr( $atts ) . '>';

		// construct html
		if( !empty( $els ) ) {

			foreach( $els as $el ) {

				// extract type
				$type = acf_extract_var( $el, 'type' );

				if ( $type == 'option' ) {

					// get label
					$label = acf_extract_var( $el, 'label' );


					// validate selected
					if ( acf_extract_var( $el, 'selected') ) {
						$el['selected'] = 'selected';
					}
					echo acf_esc_attr( $el );
					echo '<option ' . acf_esc_attr( $el ) . '>' . $label . '</option>';

				}
				else {
					echo '<' . $type . ' ' . acf_esc_attr( $el ) . ' />';
				}
			}
		}

		echo '</select>';
	}

	/*
	function input_admin_enqueue_scripts() {

		$dir = plugin_dir_url( __FILE__ );

		// register & include JS
		wp_register_script( 'acf-input-taxonomy-select', $dir . 'asstes/js/input.js' );
		wp_enqueue_script( 'acf-input-taxonomy-select' );
	}
	*/

	//function input_admin_head() {}

	//function input_form_data( $args ) {}

	//function input_admin_footer() {}

	//function field_group_admin_enqueue_scripts() {}

	//function field_group_admin_head() {}

	/*
	function load_value( $value, $post_id, $field ) {
		return $value;
	}
	*/

	/*
	function update_value( $value, $post_id, $field ) {
		return $value;
	}
	*/

	/*
	function format_value( $value, $post_id, $field ) {
		// bail early if no value
		if ( empty($value) ) {
			return $value;
		}

		// apply setting
		if ( $field['font_size'] > 12 ) {
			// format the value
			// $value = 'something';
		}

		// return
		return $value;
	}
	*/

	/*
	function validate_value( $valid, $value, $field, $input ){
		// Basic usage
		if ( $value < $field['custom_minimum_setting'] ) {
			$valid = false;
		}

		// Advanced usage
		if( $value < $field['custom_minimum_setting'] ) {
			$valid = __('The value is too little!','acf-taxonomy-select'),
		}

		// return
		return $valid;
	}
	*/

	//function delete_value( $post_id, $key ) {}

	/*
	function load_field( $field ) {
		return $field;
	}
	*/

	/*
	function update_field( $field ) {
		return $field;
	}
	*/

	//function delete_field( $field ) {}
}


// create field
new acf_field_taxonomy_chooser();

?>
