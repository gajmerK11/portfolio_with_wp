<?php
/**
 * About Me content on the user profile.
 *
 * The About section describes the site owner, so its content lives on a user
 * (Users > Profile) instead of a CPT or a public Page — no /about URL, no meta
 * box leaking onto other screens. The front section reads from the designated
 * "About user" (Customizer > About Me), defaulting to the first administrator.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID of the user whose profile feeds the About section.
 *
 * @return int
 */
function portfolio_about_user_id() {
	$id = (int) get_theme_mod( 'portfolio_about_user', 0 );
	if ( ! $id ) {
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);
		$id = $admins ? (int) $admins[0] : 0;
	}
	return (int) apply_filters( 'portfolio_about_user_id', $id );
}

/** Load the media uploader on profile screens. */
function portfolio_about_profile_media( $hook ) {
	if ( in_array( $hook, array( 'profile.php', 'user-edit.php' ), true ) ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'portfolio_about_profile_media' );

/** Render the About Me fields on the profile screen. */
function portfolio_about_profile_fields( $user ) {
	$uid = $user->ID;

	$name     = get_user_meta( $uid, 'portfolio_about_name', true );
	$location = get_user_meta( $uid, 'portfolio_about_location', true );
	$lead1    = get_user_meta( $uid, 'portfolio_about_lead1', true );
	$desc1    = get_user_meta( $uid, 'portfolio_about_desc1', true );
	$lead2    = get_user_meta( $uid, 'portfolio_about_lead2', true );
	$desc2    = get_user_meta( $uid, 'portfolio_about_desc2', true );
	$label    = get_user_meta( $uid, 'portfolio_about_link_label', true );
	$url      = get_user_meta( $uid, 'portfolio_about_link_url', true );
	$g1title  = get_user_meta( $uid, 'portfolio_about_group1_title', true );
	$g2title  = get_user_meta( $uid, 'portfolio_about_group2_title', true );
	$g1       = get_user_meta( $uid, 'portfolio_about_group1_icons', true );
	$g2       = get_user_meta( $uid, 'portfolio_about_group2_icons', true );
	if ( ! is_array( $g1 ) ) {
		$g1 = array();
	}
	if ( ! is_array( $g2 ) ) {
		$g2 = array();
	}
	?>
	<h2><?php esc_html_e( 'About Me Section', 'portfolio' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Content for the About Me section on the portfolio front page.', 'portfolio' ); ?></p>
	<?php wp_nonce_field( 'portfolio_about_save', 'portfolio_about_nonce' ); ?>
	<table class="form-table portfolio-about-fields" role="presentation">
		<tr>
			<th><label for="portfolio_about_name"><?php esc_html_e( 'Name', 'portfolio' ); ?></label></th>
			<td><input type="text" id="portfolio_about_name" name="portfolio_about_name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" placeholder="e.g. Brice Clain"></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_location"><?php esc_html_e( 'Location', 'portfolio' ); ?></label></th>
			<td><input type="text" id="portfolio_about_location" name="portfolio_about_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" placeholder="e.g. Trois-Rivières (Quebec)"></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_lead1"><?php esc_html_e( 'Lead heading 1', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_lead1" name="portfolio_about_lead1" rows="2" class="large-text" placeholder="I design and develop websites…"><?php echo esc_textarea( $lead1 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_desc1"><?php esc_html_e( 'Description 1', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_desc1" name="portfolio_about_desc1" rows="3" class="large-text"><?php echo esc_textarea( $desc1 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_lead2"><?php esc_html_e( 'Lead heading 2', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_lead2" name="portfolio_about_lead2" rows="2" class="large-text" placeholder="I also specialize in creating your brand image…"><?php echo esc_textarea( $lead2 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_desc2"><?php esc_html_e( 'Description 2', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_desc2" name="portfolio_about_desc2" rows="3" class="large-text"><?php echo esc_textarea( $desc2 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_link_label"><?php esc_html_e( 'Link label', 'portfolio' ); ?></label></th>
			<td><input type="text" id="portfolio_about_link_label" name="portfolio_about_link_label" value="<?php echo esc_attr( $label ); ?>" class="regular-text" placeholder="Discover my journey (LinkedIn)"></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_link_url"><?php esc_html_e( 'Link URL', 'portfolio' ); ?></label></th>
			<td><input type="url" id="portfolio_about_link_url" name="portfolio_about_link_url" value="<?php echo esc_attr( $url ); ?>" class="regular-text" placeholder="https://linkedin.com/in/…"></td>
		</tr>

		<?php
		portfolio_about_icon_group_row( __( 'Skill group 1', 'portfolio' ), 'group1', $g1title, $g1, 'e.g. Graphic and UX/UI design' );
		portfolio_about_icon_group_row( __( 'Skill group 2', 'portfolio' ), 'group2', $g2title, $g2, 'e.g. Web development' );
		?>
	</table>

	<style>
	.portfolio-about-fields .pf-image-preview { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
	.portfolio-about-fields .pf-image-item { position:relative; width:64px; height:64px; border:1px solid #dcdcde; border-radius:6px; overflow:hidden; background:#f6f7f7; }
	.portfolio-about-fields .pf-image-item img { width:100%; height:100%; object-fit:contain; display:block; padding:6px; box-sizing:border-box; }
	.portfolio-about-fields .pf-image-remove { position:absolute; top:2px; right:2px; width:20px; height:20px; line-height:18px; border:none; border-radius:50%; background:rgba(0,0,0,.6); color:#fff; cursor:pointer; font-size:14px; }
	</style>

	<script>
	( function () {
		var wrap = document.querySelector( '.portfolio-about-fields' );
		if ( ! wrap ) return;

		function idsOf( input ) { return input.value ? input.value.split( ',' ).filter( Boolean ) : []; }
		function makeItem( id, url ) {
			var item = document.createElement( 'div' );
			item.className = 'pf-image-item';
			item.setAttribute( 'data-id', id );
			item.innerHTML = '<img src="' + url + '" alt=""><button type="button" class="pf-image-remove" aria-label="Remove">&times;</button>';
			return item;
		}

		wrap.addEventListener( 'click', function ( e ) {
			var addBtn = e.target.closest( '.pf-media-add' );
			if ( addBtn ) {
				e.preventDefault();
				if ( typeof wp === 'undefined' || ! wp.media ) return;
				var input   = document.getElementById( addBtn.getAttribute( 'data-target' ) );
				var preview = document.getElementById( addBtn.getAttribute( 'data-preview' ) );
				var frame = wp.media( { title: 'Select Icons', button: { text: 'Use these icons' }, multiple: 'add', library: { type: 'image' } } );
				frame.on( 'select', function () {
					var sel = frame.state().get( 'selection' ).toJSON();
					var current = idsOf( input );
					sel.forEach( function ( att ) {
						if ( current.indexOf( String( att.id ) ) !== -1 ) return;
						current.push( String( att.id ) );
						var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
						preview.appendChild( makeItem( att.id, url ) );
					} );
					input.value = current.join( ',' );
				} );
				frame.open();
				return;
			}
			if ( e.target.classList.contains( 'pf-image-remove' ) ) {
				var item  = e.target.closest( '.pf-image-item' );
				var box   = item.closest( '.pf-image-preview' );
				var field = box.parentNode.querySelector( 'input[type=hidden]' );
				var id    = item.getAttribute( 'data-id' );
				field.value = idsOf( field ).filter( function ( x ) { return x !== id; } ).join( ',' );
				item.remove();
			}
		} );
	} )();
	</script>
	<?php
}
add_action( 'show_user_profile', 'portfolio_about_profile_fields' );
add_action( 'edit_user_profile', 'portfolio_about_profile_fields' );

/** One skill-icon group row (title + multi-image repeater). */
function portfolio_about_icon_group_row( $legend, $key, $title, $icons, $placeholder ) {
	$target  = 'portfolio_about_' . $key . '_icons';
	$preview = 'pf_' . $key . '_preview';
	?>
	<tr>
		<th><label for="portfolio_about_<?php echo esc_attr( $key ); ?>_title"><?php echo esc_html( $legend ); ?></label></th>
		<td>
			<input type="text" id="portfolio_about_<?php echo esc_attr( $key ); ?>_title" name="portfolio_about_<?php echo esc_attr( $key ); ?>_title"
				value="<?php echo esc_attr( $title ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $placeholder ); ?>">
			<p class="description" style="margin-top:10px;"><?php esc_html_e( 'Icons', 'portfolio' ); ?></p>
			<input type="hidden" id="<?php echo esc_attr( $target ); ?>" name="<?php echo esc_attr( $target ); ?>"
				value="<?php echo esc_attr( implode( ',', array_map( 'absint', $icons ) ) ); ?>">
			<div id="<?php echo esc_attr( $preview ); ?>" class="pf-image-preview">
				<?php foreach ( $icons as $icon_id ) : ?>
					<?php $thumb = wp_get_attachment_image_url( (int) $icon_id, 'thumbnail' ); ?>
					<?php if ( $thumb ) : ?>
						<div class="pf-image-item" data-id="<?php echo esc_attr( (int) $icon_id ); ?>">
							<img src="<?php echo esc_url( $thumb ); ?>" alt="">
							<button type="button" class="pf-image-remove" aria-label="<?php esc_attr_e( 'Remove', 'portfolio' ); ?>">&times;</button>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button pf-media-add" data-target="<?php echo esc_attr( $target ); ?>" data-preview="<?php echo esc_attr( $preview ); ?>">
				<?php esc_html_e( '+ Add Icons', 'portfolio' ); ?>
			</button>
		</td>
	</tr>
	<?php
}

/** Save the About Me profile fields. */
function portfolio_about_save_profile( $user_id ) {
	if ( ! isset( $_POST['portfolio_about_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_about_nonce'] ) ), 'portfolio_about_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	$text = array( 'portfolio_about_name', 'portfolio_about_location', 'portfolio_about_link_label', 'portfolio_about_group1_title', 'portfolio_about_group2_title' );
	foreach ( $text as $key ) {
		portfolio_about_save_meta( $user_id, $key, isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}

	$area = array( 'portfolio_about_lead1', 'portfolio_about_desc1', 'portfolio_about_lead2', 'portfolio_about_desc2' );
	foreach ( $area as $key ) {
		portfolio_about_save_meta( $user_id, $key, isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}

	$url = isset( $_POST['portfolio_about_link_url'] ) ? esc_url_raw( wp_unslash( $_POST['portfolio_about_link_url'] ) ) : '';
	portfolio_about_save_meta( $user_id, 'portfolio_about_link_url', $url );

	foreach ( array( 'portfolio_about_group1_icons', 'portfolio_about_group2_icons' ) as $key ) {
		$ids = array();
		if ( isset( $_POST[ $key ] ) && '' !== $_POST[ $key ] ) {
			$raw = explode( ',', sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			$ids = array_values( array_filter( array_map( 'absint', $raw ) ) );
		}
		if ( $ids ) {
			update_user_meta( $user_id, $key, $ids );
		} else {
			delete_user_meta( $user_id, $key );
		}
	}
}
add_action( 'personal_options_update', 'portfolio_about_save_profile' );
add_action( 'edit_user_profile_update', 'portfolio_about_save_profile' );

/** Update the meta when non-empty, otherwise delete it. */
function portfolio_about_save_meta( $user_id, $key, $value ) {
	if ( '' !== trim( (string) $value ) ) {
		update_user_meta( $user_id, $key, $value );
	} else {
		delete_user_meta( $user_id, $key );
	}
}
