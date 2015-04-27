<?php
/**
 * Smart_Custom_Fields_Controller_Editor
 * Version    : 1.0.2
 * Author     : Takashi Kitajima
 * Created    : September 23, 2014
 * Modified   : March 16, 2015
 * License    : GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class Smart_Custom_Fields_Controller_Editor extends Smart_Custom_Fields_Controller_Base {

	/**
	 * __construct
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post'     , array( $this, 'save_post' ) );
	}

	/**
	 * 投稿画面にカスタムフィールドを表示
	 *
	 * @param string $post_type
	 * @param WP_Post $post
	 */
	public function add_meta_boxes( $post_type, $post ) {
		$settings = SCF::get_settings( $post );
		foreach ( $settings as $Setting ) {
			add_meta_box(
				SCF_Config::PREFIX . 'custom-field-' . $Setting->get_id(),
				$Setting->get_title(),
				array( $this, 'display_meta_box' ),
				$post_type,
				'normal',
				'default',
				$Setting->get_groups()
			);
		}
	}

	/**
	 * 投稿画面のカスタムフィールドからのメタデータを保存
	 * 
	 * @param int $post_id
	 */
	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ){
			return;
		}
		if ( !isset( $_POST[SCF_Config::NAME] ) ) {
			return;
		}

		$this->save( $_POST, get_post( $post_id ) );
	}

	/**
	 * メタデータの取得
	 * 
	 * @param int $post_id
	 * @return array
	 */
	protected function _get_all_meta( $post_id ) {
		return get_post_meta( $post_id );
	}

	/**
	 * デフォルト値を取得するための条件
	 *
	 * @param mixed $default
	 * @param int $index
	 * @param int $post_id
	 * @return bool
	 */
	protected function default_value_conditions( $default, $index, $post_id ) {
		$post_status = get_post_status( $post_id );
		if ( !SCF::is_empty( $default ) && ( $post_status === 'auto-draft' || is_null( $index ) ) ) {
			return true;
		}
		return false;
	}
}
