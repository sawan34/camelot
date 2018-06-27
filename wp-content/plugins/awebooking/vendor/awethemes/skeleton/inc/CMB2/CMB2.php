<?php
namespace Skeleton\CMB2;

use WP_Error;
use CMB2 as CMB2Base;
use Skeleton\Support\Validator;
use Skeleton\Support\Multidimensional;

class CMB2 extends CMB2Base {
	/**
	 * CMB2 render instance.
	 *
	 * @var Render
	 */
	protected $render;

	/**
	 * Registered tabs, combine panels and sections.
	 *
	 * @var array
	 */
	protected $tabs = array();

	/**
	 * Registered instances of Panel.
	 *
	 * @var array
	 */
	protected $panels = array();

	/**
	 * Registered instances of Section.
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Validation errors.
	 *
	 * @var array|null
	 */
	protected $validate_errors;

	/**
	 * Get started.
	 *
	 * @param array   $config    Metabox config array.
	 * @param integer $object_id Optional object id.
	 */
	public function __construct( $config, $object_id = 0 ) {
		parent::__construct( $config, $object_id );

		$this->render = new Render( $this );
	}

	/**
	 * Generate a unique nonce for each registered meta_box.
	 *
	 * @return string
	 */
	public function nonce() {
		if ( ! $this->generated_nonce ) {
			$this->generated_nonce = sanitize_html_class( 'nonce_' . $this->cmb_id );
		}

		return $this->generated_nonce;
	}

	/**
	 * Add a field to the metabox.
	 *
	 * @param  array $field      Metabox field config array.
	 * @param  void  $deprecated Deprecated this argument.
	 * @return string|false
	 */
	public function add_field( array $field, $deprecated = 0 ) {
		if ( ! empty( $deprecated ) ) {
			_deprecated_argument( __CLASS__ . '::' . __FUNCTION__, '1.0', esc_html__( 'Set field priority instead of.', 'skeleton' ) );
		}

		return parent::add_field( $field );
	}

	/**
	 * Add a "row" to the metabox.
	 *
	 * @param  string $id_or_args Row ID or row args.
	 * @param  array  $fields     An array of fields.
	 * @return int
	 */
	public function add_row( $id_or_args, array $fields = [] ) {
		$field_args = is_array( $id_or_args ) ? $id_or_args : array( 'id' => $id_or_args );

		$field_args['type'] = 'row';
		$field_args['save_field'] = false;

		if ( $fields ) {
			$field_args['fields'] = $fields;
		}

		foreach ( $field_args['fields'] as $_field ) {
			$_field['parent_row'] = $field_args['id'];
			$this->add_field( $_field );
		}

		return $this->add_field( $field_args );
	}

	/**
	 * Add a group field to the metabox.
	 *
	 * @param  string   $id       Group ID.
	 * @param  callable $callback Group builder callback.
	 * @return Group
	 */
	public function add_group( $id, $callback = null ) {
		$id = $this->add_field( array(
			'id'   => $id,
			'type' => 'group',
		) );

		$builder = new Group( $this, $id );

		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, array( $builder ) );
		}

		return $builder;
	}

	/**
	 * Add a CMB2 panel.
	 *
	 * @param  Panel|string   $id   CMB2 Panel object, or panel ID.
	 * @param  array|callable $args Optional. Panel arguments or panel callback.
	 * @return Panel                The instance of the panel that was added.
	 */
	public function add_panel( $id, $args = array() ) {
		if ( $id instanceof Panel ) {
			$panel = $id;
		} else {
			$panel = $this->_set_tabable( new Panel( $this, $id ), $args );
		}

		$this->panels[ $panel->id ] = $panel;
		return $panel;
	}

	/**
	 * Retrieve a CMB2 panel.
	 *
	 * @param  string $id Panel ID to get.
	 * @return Panel|void Requested panel instance, if set.
	 */
	public function get_panel( $id ) {
		if ( isset( $this->panels[ $id ] ) ) {
			return $this->panels[ $id ];
		}
	}

	/**
	 * Returns true if a panel defined.
	 *
	 * @param  string $id Panel ID to check.
	 * @return boolean
	 */
	public function has_panel( $id ) {
		return ! is_null( $this->get_panel( $id ) );
	}

	/**
	 * Remove a CMB2 panel.
	 *
	 * @param string $id Panel ID to remove.
	 */
	public function remove_panel( $id ) {
		unset( $this->panels[ $id ] );
	}

	/**
	 * Add a CMB2 section.
	 *
	 * @param  Section|string $id   CMB2 Section object, or section ID.
	 * @param  array|callable $args Optional. Section arguments or section callback.
	 * @return Section              The instance of the section that was added.
	 */
	public function add_section( $id, $args = array() ) {
		if ( $id instanceof Section ) {
			$section = $id;
		} else {
			$section = $this->_set_tabable( new Section( $this, $id ), $args );
		}

		$this->sections[ $section->id ] = $section;
		return $section;
	}

	/**
	 * Retrieve a CMB2 section.
	 *
	 * @param  string $id   Section ID.
	 * @return Section|void The section, if set.
	 */
	public function get_section( $id ) {
		if ( isset( $this->sections[ $id ] ) ) {
			return $this->sections[ $id ];
		}
	}

	/**
	 * Returns true if a section defined.
	 *
	 * @param  string $id Section ID to check.
	 * @return boolean
	 */
	public function has_section( $id ) {
		return ! is_null( $this->get_section( $id ) );
	}

	/**
	 * Remove a CMB2 section.
	 *
	 * @param string $id Section ID to remove.
	 */
	public function remove_section( $id ) {
		unset( $this->sections[ $id ] );
	}

	/**
	 * Set tabable arguments or run a callback.
	 *
	 * @param Tabable        $tabable Tabable object.
	 * @param array|callable $setting Tabable arguments or a callable.
	 */
	protected function _set_tabable( Tabable $tabable, $setting ) {
		if ( empty( $setting ) ) {
			return $tabable;
		}

		if ( is_array( $setting ) ) {
			$tabable->set( $setting );
		} elseif ( is_callable( $setting ) ) {
			call_user_func_array( $setting, array( $tabable ) );
		}

		return $tabable;
	}

	/**
	 * Prepare panels, sections, and fields.
	 *
	 * For each, check if required related components exist,
	 * whether the user has the necessary capabilities,
	 * and sort by priority.
	 */
	public function prepare_controls() {
		$fields = Tabable_Stack::make( $this->prop( 'fields' ) )->toArray();
		foreach ( $fields as $id => $field ) {
			if ( ! isset( $field['section'] ) || ! isset( $this->sections[ $field['section'] ] ) ) {
				continue;
			}

			$this->sections[ $field['section'] ]->fields[] = $field;
		}

		$this->set_prop( 'fields', $fields );

		// Prepare sections.
		$sections = array();
		foreach ( Tabable_Stack::make( $this->sections ) as $section ) {
			if ( ! $section->check_capabilities() ) {
				continue;
			}

			// Re-build fields index by priority.
			$section->fields = Tabable_Stack::make( $section->fields )->toArray();

			if ( ! $section->panel ) {
				// Top-level section.
				$sections[ $section->id ] = $section;
			} else {
				// This section belongs to a panel.
				if ( isset( $this->panels [ $section->panel ] ) ) {
					$this->panels[ $section->panel ]->sections[ $section->id ] = $section;
				}
			}
		}

		$this->sections = $sections;

		// Prepare panels.
		$panels = array();
		foreach ( Tabable_Stack::make( $this->panels ) as $panel ) {
			if ( ! $panel->check_capabilities() ) {
				continue;
			}

			// Re-build sections index by priority.
			$panel->sections = Tabable_Stack::make( $panel->sections )->toArray();
			$panels[ $panel->id ] = $panel;
		}

		$this->panels = $panels;

		// Sort panels and top-level sections together.
		$tabs = array_merge( $this->panels, $this->sections );
		$this->tabs = Tabable_Stack::make( $tabs )->toArray();
	}

	/**
	 * Get the registered tabs.
	 *
	 * @return array
	 */
	public function tabs() {
		return $this->tabs;
	}

	/**
	 * Get the registered panels.
	 *
	 * @return array
	 */
	public function panels() {
		return $this->panels;
	}

	/**
	 * Get the registered sections.
	 *
	 * @return array
	 */
	public function sections() {
		return $this->sections;
	}

	/**
	 * Return a unique name for transient.
	 *
	 * @param  string $append Optional append string.
	 * @return string
	 */
	public function transient_id( $append = '' ) {
		return $this->cmb_id . '_' . $this->object_type() . $append;
	}

	/**
	 * Return a ruleset for validation.
	 *
	 * @return array
	 */
	public function get_rules() {
		$labels  = array();
		$ruleset = array();

		foreach ( $this->prop( 'fields' ) as $args ) {
			if ( in_array( $args['type'], array( 'title', 'group' ) ) ) {
				continue;
			}

			$field = $this->get_field( $args );
			if ( ! $field->prop( 'save_field' ) ) {
				continue;
			}

			if ( ! empty( $args['validate'] ) ) {
				$id = $args['id'];
				$ruleset[ $id ] = $args['validate'];

				if ( isset( $args['validate_label'] ) ) {
					$labels[ $id ] = $args['validate_label'];
				} else {
					$labels[ $id ] = isset( $args['name'] ) ? $args['name'] : $id;
				}
			}
		}

		return array( $ruleset, $labels );
	}

	/**
	 * Prepare fields validate.
	 */
	public function prepare_validate() {
		$errors = get_transient( $this->transient_id( '_errors' ) );

		if ( $errors ) {
			$this->validate_errors = $errors;

			delete_transient( $this->transient_id( '_errors' ) );
		}
	}

	/**
	 * Return validation errors property.
	 *
	 * @return array|null
	 */
	public function get_errors() {
		return $this->validate_errors;
	}

	/**
	 * Determine if the data passes the validation.
	 *
	 * @return bool
	 */
	public function passes() {
		return ! $this->get_errors();
	}

	/**
	 * Determine if the data fails the validation.
	 *
	 * @return bool
	 */
	public function fails() {
		return ! $this->passes();
	}

	/**
	 * Process and save form fields.
	 */
	public function process_fields() {
		$this->pre_process();

		// Remove the show_on properties so saving works.
		$this->prop( 'show_on', array() );

		// Save field ids of those that are updated.
		$this->updated = array();

		// Validate before saving data.
		list( $rules, $labels ) = $this->get_rules();

		$validator = new Validator( $this->data_to_save, $rules );
		$validator->labels( $labels );

		// Run validations.
		$validated = $validator->validate();
		$this->validate_errors = $validator->errors();

		// Loop through fields and process saving data.
		foreach ( $this->prop( 'fields' ) as $field_args ) {
			$field_id = $field_args['id'];

			// Fake $this->data_to_save for support multi-dimensional.
			// If see a multidimensional-like, just add a parsed data with name
			// same as field ID. So we can easy access to multi-dimensional data.
			if ( strpos( $field_id, '[' ) ) {
				$this->data_to_save[ $field_id ] = Multidimensional::find( $this->data_to_save, $field_id, '' );
			}

			// Validate field by callback if need.
			if ( ! empty( $field_args['validate_cb'] ) && is_callable( $field_args['validate_cb'] ) ) {
				$this->_validate_cb( $field_args['validate_cb'], $field_args );
			}

			// Only process field if not seeing any errors.
			if ( empty( $this->validate_errors[ $field_id ] ) ) {
				$this->process_field( $field_args );
			}
		}

		// Set errors message to transient if fails.
		if ( $this->fails() ) {
			set_transient( $this->transient_id( '_errors' ), $this->validate_errors, 30 );
		}
	}

	/**
	 * Run validate by callback a field.
	 *
	 * @param  callable $callback   //.
	 * @param  array    $field_args //.
	 * @return void
	 */
	protected function _validate_cb( $callback, $field_args ) {
		$id = $field_args['id'];
		$value = isset( $this->data_to_save[ $id ] ) ? $this->data_to_save[ $id ] : null;

		$validity = new WP_Error();
		call_user_func_array( $callback, array( $validity, $value, $this ) );

		if ( is_wp_error( $validity ) && $validity->errors ) {
			$this->add_validation_error( $id, $validity->get_error_messages() );
		}
	}

	/**
	 * Add a validation error.
	 *
	 * @param string $id            Field ID.
	 * @param string $error_message Error message.
	 */
	public function add_validation_error( $id, $error_message ) {
		if ( isset( $this->validate_errors[ $id ] ) ) {
			$this->validate_errors[ $id ] = array_merge( $this->validate_errors[ $id ], $error_message );
		} else {
			$this->validate_errors[ $id ] = $error_message;
		}

		return $this;
	}

	/**
	 * Add a field array to a fields array in desired position.
	 *
	 * @param array   $field    Metabox field config array.
	 * @param array   $fields   Array (passed by reference) to append the field (array) to.
	 * @param integer $position Optionally specify a position in the array to be inserted.
	 */
	protected function _add_field_to_array( $field, &$fields, $position = 0 ) {
		if ( ! isset( $field['priority'] ) ) {
			$field['priority'] = 10;
		}

		if ( ! isset( $field['render_row_cb'] ) ) {
			$field['render_row_cb'] = array( $this->render, 'render_field' );
		}

		$fields[ $field['id'] ] = $field;
	}

	/**
	 * Render a repeatable group.
	 *
	 * @param  array $args     Array of field arguments for a group field parent.
	 * @return CMB2_Field|null Group field object.
	 */
	public function render_group( $args ) {
		return $this->render->render_group( $args );
	}

	/**
	 * Loops through and displays fields.
	 *
	 * @param int    $object_id   Object ID.
	 * @param string $object_type Type of object being saved. (e.g., post, user, or comment).
	 */
	public function show_form( $object_id = 0, $object_type = '' ) {
		$this->prepare_controls();

		$this->prepare_validate();

		$this->render_form_open( $object_id, $object_type );

		if ( ! empty( $this->tabs ) ) {
			$this->before_display_form();

			// Display CMB2 as tabs.
			$this->render->display();

		} else {
			// Loop through fields and call render each field.
			foreach ( $this->prop( 'fields' ) as $field_args ) {
				$this->render_field( $field_args );
			}
		}

		$this->render_form_close( $object_id, $object_type );
	}

	/**
	 * Renders a field based on the field type.
	 *
	 * @param  array $field_args A field configuration array.
	 * @return mixed CMB2_Field object if successful.
	 */
	public function render_field( $field_args ) {
		$field = $this->get_field( $field_args );

		// Don't render the column field.
		if ( ! empty( $field_args['parent_row'] ) ) {
			return $field;
		}

		switch ( $field_args['type'] ) {
			case 'row':
				$row_classes = $field->row_classes();
				if ( $field->prop( 'flex_columns' ) ) {
					$row_classes .= ' cmb2-flex-' . $field->prop( 'flex_columns' ) . '-columns';
				}

				echo '<div class="cmb2-flex-row ' . esc_attr( $row_classes ) . '">';
				foreach ( $field->args( 'fields' ) as $_field ) {
					$this->get_field( $_field )->render_field();
				}
				echo '</div>';
				break;

			default:
				$field = parent::render_field( $field_args );
				break;
		}

		return $field;
	}

	/**
	 * Get CMB2 render instance.
	 */
	public function get_render() {
		return $this->render;
	}

	/**
	 * Hook to custom render display.
	 *
	 * @return void
	 */
	public function before_display_form() {}
}
