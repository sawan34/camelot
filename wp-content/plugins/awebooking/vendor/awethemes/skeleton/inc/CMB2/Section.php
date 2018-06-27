<?php
namespace Skeleton\CMB2;

class Section extends Tabable {
	/**
	 * Panel in which to show the section, making it a sub-section.
	 *
	 * @var string
	 */
	public $panel = '';

	/**
	 * CMB2 fields for this section.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Set panel where this section will belong to.
	 *
	 * @param  Panel|string $panel Panel ID or a Panel object.
	 * @return $this
	 */
	public function as_child_of( $panel ) {
		if ( $panel instanceof Panel ) {
			$panel = $panel->id;
		}

		$this->panel = $panel;
		return $this;
	}

	/**
	 * Add a field to this section of the CMB2.
	 *
	 * @param  array $field CMB2 field config array.
	 * @return string|false
	 */
	public function add_field( array $field ) {
		$field['section'] = $this->id;
		return $this->cmb2->add_field( $field );
	}

	/**
	 * Add a group field to the section.
	 *
	 * @param  array    $id       Group ID.
	 * @param  callable $callback Group builder callback.
	 * @return \Skeleton\CMB2\Group
	 */
	public function add_group( $id, $callback = null ) {
		$group = $this->cmb2->add_group( $id, $callback );

		$group->set_property( 'section', $this->id );

		return $group;
	}

	/**
	 * Add a "row" to the section.
	 *
	 * @param  string $id_or_args Row ID or row args.
	 * @param  array  $fields     An array of fields.
	 * @return int
	 */
	public function add_row( $id_or_args, array $fields = [] ) {
		$field_args = is_array( $id_or_args ) ? $id_or_args : array( 'id' => $id_or_args );

		$field_args['section'] = $this->id;

		return $this->cmb2->add_row( $field_args, $fields );
	}
}
