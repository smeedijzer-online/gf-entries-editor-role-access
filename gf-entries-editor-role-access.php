<?php
/*
Plugin Name: Gravity Forms Entries Editor Role Access
Plugin URI: https://github.com/smeedijzer-online/gf-entries-editor-role-access
Description: Give users with the Editor role the Gravity Forms capabilities to control 'entries' (view, edit, delete, export) and 'entry notes' (view, edit).
Author: Smeedijzer Internet
Author URI: https://smeedijzer.net
Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GF_Entries_Editor_Role_Access' ) ) {
	class GF_Entries_Editor_Role_Access {

		private static $_instance;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->setup_actions();
		}

		/**
		 * @return GF_Entries_Editor_Role_Access
		 */
		public static function instance(): \GF_Entries_Editor_Role_Access {
			if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof self ) ) {
				self::$_instance = new GF_Entries_Editor_Role_Access();
			}
			return self::$_instance;
		}

		/**
		 * Setting up Hooks
		 */
		public function setup_actions() {
			if (class_exists('GFForms')) {
				// Register activation hook
				register_activation_hook( __FILE__,  [$this, 'activate'] );
				// Remove help submenu
				add_action( 'admin_menu', [$this, 'remove_submenu_gf_help'], 999 );
			} else {
				// Add admin notice
				add_action( 'admin_notices',  [$this, 'gf_not_installed_or_activated'] );
			}

			//Deactivate Plugin remove role
			register_deactivation_hook( __FILE__, [$this, 'deactivate' ] );
		}

		/**
		 * Activate callback: Runs during plugin activation.
		 * Add all Gravity Forms capabilities to Editor role.
		 *
		 * @access public
		 * @return void
		 */
		public static function activate() {
			$role = get_role( 'editor' );
			$role->add_cap( 'gravityforms_view_entries' );
			$role->add_cap( 'gravityforms_edit_entries' );
			$role->add_cap( 'gravityforms_delete_entries' );
			$role->add_cap( 'gravityforms_view_entry_notes' );
			$role->add_cap( 'gravityforms_edit_entry_notes' );
			$role->add_cap( 'gravityforms_export_entries' );
		}

		/**
		 * Deactivate callback Runs during plugin deactivation.
		 * Remove Gravity Forms capabilities from Editor role.
		 *
		 * @access public
		 * @return void
		 */
		public static function deactivate() {
			$role = get_role( 'editor' );
			$role->remove_cap( 'gravityforms_view_entries' );
			$role->remove_cap( 'gravityforms_edit_entries' );
			$role->remove_cap( 'gravityforms_delete_entries' );
			$role->remove_cap( 'gravityforms_view_entry_notes' );
			$role->remove_cap( 'gravityforms_edit_entry_notes' );
			$role->remove_cap( 'gravityforms_export_entries' );
		}

		/*
		 * Remove Gravity Forms help submenu item (admin.php?page=gf_help).
		 *
		 * @access public
		 * @return void
		 */
		public static function remove_submenu_gf_help() {
			if( !current_user_can( 'gravityforms_create_form' ) ) {
				remove_submenu_page( 'gf_entries', 'gf_help' );
			}
		}

		/**
		 * No Gravity Forms plugin installed or enabled
		 *
		 * @access public
		 * @return void
		 */
		public static function gf_not_installed_or_activated() {
			echo '<div class="error"><p>' . __( "You must have Gravity Forms activated in order to use this plugin.", 'gf-entries-editor-role-access' ) . '</p></div>';
		}
	}

	// instantiate the plugin class
	add_action('plugins_loaded', array ( new GF_Entries_Editor_Role_Access ));
}
