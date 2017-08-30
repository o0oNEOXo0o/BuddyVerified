<?php
/**
 * BuddyVerified Groups
 *
 * @since 2.4.1
 * @package BuddyVerified
 */

/**
 * BuddyVerified Admin.
 *
 * @since 2.4.1
 */
class BV_Groups {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 2.4.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  2.4.1
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  2.4.1
	 * @return void
	 */
	public function hooks() {
	}
}

/**
 * [bp_verified_groups_admin_meta_boxes description]
 *
 * @since  2.4.1
 * @return void
 */
function bp_verified_groups_admin_meta_boxes() {

	add_meta_box(
	    'group_verified_id',
		__( 'Verify Group', 'buddyverified' ),
	    'bp_verified_group_metabox_markup',
	    get_current_screen()->id,
		'normal'
	);
}
add_action( 'bp_groups_admin_meta_boxes', 'bp_verified_groups_admin_meta_boxes' );

/**
 * [bp_verified_group_metabox_markup description]
 *
 * @since  2.4.1
 * @return void
 */
function bp_verified_group_metabox_markup() {
	global $bp, $wpdb;

	$group_id = is_admin() && isset( $_GET['gid'] ) ? wp_unslash( $_GET['gid'] ) : false;
	$meta = isset( $group_id ) ? groups_get_groupmeta( $group_id, 'group_verified' ) : '';

	$verified = isset( $meta['verified'] ) ? $meta['verified'] : false;
	$image = isset( $meta['image'] ) ? $meta['image'] : 0;

	?>
	<table id="bp-verified" cellspacing="3px" style="border-collapse: collapse;">
		<thead>
			<tr>
				<th></th>
			</tr>
		</thead>

		<tbody>
			<tr class="pad">
				<td style="vertical-align:middle">Verify Group:</td>
				<td><input type="checkbox" name="verified" value="1" <?php if ( $verified ) { echo 'checked="checked"'; } ?> /></td>
			</tr>
			<tr class="alt">
				<td style="vertical-align:middle">Badge:</td>
				<td style="vertical-align:middle">Choose image to display</td>
				<td><img src="<?php echo esc_url( VERIFIED_URL ); ?>/images/1.png"></td>
				<td><img src="<?php echo esc_url( VERIFIED_URL ); ?>/images/2.png"></td>
				<td><img src="<?php echo esc_url( VERIFIED_URL ); ?>/images/3.png"></td>
				<td><img src="<?php echo esc_url( VERIFIED_URL ); ?>/images/4.png"></td>
				<td><img src="<?php echo esc_url( VERIFIED_URL ); ?>/images/5.png"></td>
				<td><img src="<?php echo esc_url( VERIFIED_URL ); ?>/images/6.png"></td>
			</tr>
			<tr class="alt">
				<td></td>
				<td></td>
				<td><input type="radio" name="image" value="1" <?php if ( '1' === $image ) { echo 'checked="checked"'; } ?> /></td>
				<td><input type="radio" name="image" value="2" <?php if ( '2' === $image ) { echo 'checked="checked"'; } ?>/></td>
				<td><input type="radio" name="image" value="3" <?php if ( '3' === $image ) { echo 'checked="checked"'; } ?> /></td>
				<td><input type="radio" name="image" value="4" <?php if ( '4' === $image ) { echo 'checked="checked"'; } ?> /></td>
				<td><input type="radio" name="image" value="5" <?php if ( '5' === $image ) { echo 'checked="checked"'; } ?> /></td>
				<td><input type="radio" name="image" value="6" <?php if ( '6' === $image ) { echo 'checked="checked"'; } ?> /></td>
			</tr>
			<?php do_action( 'buddyverified_meta_box_fields', 'group', $group_id, $meta ); ?>
		</tbody>
	</table>

	<?php

}

/**
 * Buddyverified_save_group_metabox function.
 *
 * @since  2.4.1
 * @return void
 */
function buddyverified_save_group_metabox() {

	$group_id = is_admin() && isset( $_GET['gid'] ) ? $_GET['gid'] : false;

	$fields = array(
		'verified',
		'image',
	);

	$value = array();

	foreach ( $fields as $field ) {
		$key = $field;
		if ( isset( $_POST[ $key ] ) ) {

			switch ( $field ) {
				case 'image':
					$value[ $key ] = trim( $_POST[ $key ] )  ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : false;
				break;
				case 'verified':
						$value[ $key ] = trim( $_POST[ $key ] )  ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : false;
				break;
			}
		}
	}

	$value = apply_filters( 'bp_group_verified_arr', $value, $_POST );

	if ( ! empty( $value ) ) {
		$verify = isset( $value['verified'] ) ? $value['verified'] : '';
		groups_update_groupmeta( $group_id, 'bp_group_verified', $verify );
		groups_update_groupmeta( $group_id, 'group_verified', $value );
	}
}
add_action( 'bp_group_admin_edit_after', 'buddyverified_save_group_metabox', 5, 1 );

/**
 * Show badge before verfied group meta
 *
 * @since  2.4.1
 * @return mixed
 */
function bp_verified_group_badge() {
	echo bp_get_verified_group_badge();
}
add_action( 'bp_before_group_header_meta', 'bp_verified_group_badge' );

function bp_get_verified_group_badge() {

	$meta = bp_get_group_verified_meta( bp_get_group_id() );

	if ( isset( $meta['verified'] ) && $meta['verified'] ) {
		return bp_get_verified_image( $meta['image'] );
	}
	return;
}

/**
 * Returns veified group meta
 *
 * @since  2.4.1
 * @param  integer $group_id
 * @return array
 */
function bp_get_group_verified_meta( $group_id ) {
	return isset( $group_id ) ? groups_get_groupmeta( $group_id, 'group_verified' ) : '';
}

/**
 * Show badge before verfied group name
 *
 * @since  2.4.1
 * @param  string $group_name
 * @param  object $object
 * @return string
 */
function bp_show_verified_badge_groups( $group_name, $object ) {
	global $bp;

	if ( ! bp_is_directory() && ! bp_is_user() ) {
		return $group_name;
	}

	$group = isset( $object ) ? (int) $object->id : '';
	$verified_meta = bp_get_group_verified_meta( $group );

	if ( ! empty( $verified_meta ) ) {
		if ( isset( $verified_meta['verified'] ) && $verified_meta['verified'] ) {
			$group_name = '<span class="bp-verified-members">' . bp_get_verified_group_badge( $verified_meta['image'] ) . '</span>    ' . $group_name;
	  	}
	}

	return $group_name;
}
add_filter( 'bp_get_group_name', 'bp_show_verified_badge_groups', 10, 2 );

/**
 * Add verified column to groups admin list
 *
 * @since 2.4.1
 * @param array $columns
 * @return array
 */
function bp_verified_add_group_column( $columns ) {
	$columns['group_verified'] = _x( 'Verified', 'Groups verified column header',  'buddyverified' );

	return $columns;
}
add_filter( 'bp_groups_list_table_get_columns', 'bp_verified_add_group_column', 999 );

/**
 * Add badge to verified groups column
 *
 * @since 2.4.1
 * @param  string $retval
 * @param  string $column_name
 * @param  array  $item
 * @return void
 */
function apsa_column_content_group_id( $retval = '', $column_name, $item ) {

	if ( 'group_verified' !== $column_name ) {
		return $retval;
	}

	$group_meta = groups_get_groupmeta( $item['id'], 'group_verified' );

	if ( isset( $group_meta['image'] ) ) {
		echo bp_verified_group_badge( $group_meta['image'] );
	}
}
add_filter( 'bp_groups_admin_get_group_custom_column', 'apsa_column_content_group_id', 10, 3 );
