<?php

use Q48academy\Gdpr\GdprGoogleAnalytics;

class WpGdprGaPlugin
{
	const SLUG = 'wp-gdpr-ga';
	const PAGE = 'wp-gdpr-ga';
	const SETTING = 'wp-gdpr-ga-setting';
	const SETTING_OPTIONS = 'wp_gdpr_ga_options';
	const SETTING_SECTION_GA = 'wp_gdpr_ga_section_ga';
	const SETTING_FIELD_PILL = 'wp_gdpr_ga_field_pill';

	const SETTING_FIELD_KEY_DIR = 'wp_gdpr_ga_field_key_dir';
	const SETTING_FIELD_KEY_DIR_DEFAULT = 'etc/.keys';
	const SETTING_FIELD_LOG_DIR = 'wp_gdpr_ga_field_log_dir';
	const SETTING_FIELD_LOG_DIR_DEFAULT = 'var/log';
	const SETTING_FIELD_GA_ID 	= 'wp_gdpr_ga_field_ga_id';
	const SETTING_FIELD_GA_ID_DEFAULT = 'UA-XXXXXXXXX-1';


	public static function settingsInit(){

		// register a new setting for page
		// @see https://developer.wordpress.org/reference/functions/register_setting/
		// register_setting( string $option_group, string $option_name, array $args = array() )
		register_setting( self::SETTING, self::SETTING_OPTIONS );

		// register a new section in the page
		// @see https://developer.wordpress.org/reference/functions/add_settings_section/
		// add_settings_section( string $id, string $title, callable $callback, string $page )
		add_settings_section(
			self::SETTING_SECTION_GA,
			'Setting Title',
			['WpGdprGaPlugin','sectionGa'],
			self::PAGE
		);

		// register a new field in the "wporg_section_developers" section, inside the "wporg" page
		// @see https://developer.wordpress.org/reference/functions/add_settings_field/
		// add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() )
		add_settings_field(
			self::SETTING_FIELD_PILL, // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			'Field Title',
			['WpGdprGaPlugin','fieldPill'],
			self::PAGE,
			self::SETTING_SECTION_GA,
			[
				'label_for' => self::SETTING_FIELD_PILL,
				'class' => 'wporg_row',
				'wporg_custom_data' => 'custom',
			]
		);

		add_settings_field(
			self::SETTING_FIELD_GA_ID, // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			'Google Analytics ID',
			['WpGdprGaPlugin','fieldGaId'],
			self::PAGE,
			self::SETTING_SECTION_GA,
			[
				'label_for' => self::SETTING_FIELD_GA_ID,
				'class' => 'wporg_row',
				'wporg_custom_data' => 'custom',
			]
		);

		add_settings_field(
			self::SETTING_FIELD_KEY_DIR, // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			'Key Directory',
			['WpGdprGaPlugin','fieldKeyDir'],
			self::PAGE,
			self::SETTING_SECTION_GA,
			[
				'label_for' => self::SETTING_FIELD_KEY_DIR,
				'class' => 'wporg_row',
				'wporg_custom_data' => 'custom',
			]
		);

		add_settings_field(
			self::SETTING_FIELD_LOG_DIR, // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			'Log Directory',
			['WpGdprGaPlugin','fieldLogDir'],
			self::PAGE,
			self::SETTING_SECTION_GA,
			[
				'label_for' => self::SETTING_FIELD_LOG_DIR,
				'class' => 'wporg_row',
				'wporg_custom_data' => 'custom',
			]
		);
	}

	public static function optionsPage(){
		add_submenu_page(
			'tools.php',
			'WP GDPR Google Analytics',
			'WP GDPR Google Analytics Options',
			'manage_options',
			self::PAGE,
			['WpGdprGaPlugin','optionsPageHtml']
		);
	}

	public static function optionsPageHtml(){
		// check user capabilities
		if (!current_user_can('manage_options')) {
			return;
		}

		// add error/update messages

        // check setup
        $gdprGa = self::getGdprGa();
		$setupErrors = $gdprGa->verify();
		if(!empty($setupErrors)){
			foreach($setupErrors as $msg => $msgType){
				add_settings_error( 'wp_gdpr_ga_messages', 'wp_gdpr_ga_message', $msg, $msgType );
			}
        }


		// check if the user have submitted the settings
		// wordpress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'wp_gdpr_ga_messages', 'wp_gdpr_ga_message', 'Settings Saved', 'updated' );
		}

		// show error/update messages
		settings_errors( 'wp_gdpr_ga_messages' );
		?>
		<div class="wrap">

			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wporg"
				settings_fields( self::SETTING );

				// output setting sections and their fields
				// (sections are registered for "wporg", each field is registered to a specific section)
				do_settings_sections( self::PAGE );

				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>

           <?php if(!empty($setupErrors)): ?>
                <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                    <input type="hidden" name="action" value="wp_gdpr_install_keys">
					<?php submit_button( 'Install Keys' ); ?>
                </form>
           <?php endif; ?>

        </div>
		<?php
	}


	/**
	 * custom option and settings:
	 * callback functions
	 */

	// developers section cb

	// section callbacks can accept an $args parameter, which is an array.
	// $args have the following keys defined: title, id, callback.
	// the values are defined at the add_settings_section() function.
	public static function sectionGa( $args ){


		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', self::PAGE ); ?></p>
		<?php


	}

	// pill field cb

	// field callbacks can accept an $args parameter, which is an array.
	// $args is defined at the add_settings_field() function.
	// wordpress has magic interaction with the following keys: label_for, class.
	// the "label_for" key value is used for the "for" attribute of the <label>.
	// the "class" key value is used for the "class" attribute of the <tr> containing the field.
	// you can add custom key value pairs to be used inside your callbacks.
	public static function fieldPill( $args ){

// get the value of the setting we've registered with register_setting()
		$options = get_option( self::SETTING_OPTIONS );
		#var_dump($options);
		// output the field
		?>
		<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['wporg_custom_data'] ); ?>"
				name="<?php echo self::SETTING_OPTIONS ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
		>
			<option value="red" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'red pill', 'wporg' ); ?>
			</option>
			<option value="blue" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'blue', false ) ) : ( '' ); ?>>
				<?php esc_html_e( 'blue pill', 'wporg' ); ?>
			</option>
		</select>
		<p class="description">
			<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'wporg' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'wporg' ); ?>
		</p>

		<?php


	}

	public static function fieldGaId( $args ){

		self::showTextField( $args ,self::SETTING_FIELD_GA_ID_DEFAULT , 'Put your GA ID here.');

	}

	public static function fieldKeyDir( $args ){

		self::showTextField( $args ,self::SETTING_FIELD_KEY_DIR_DEFAULT , 'Put your Key Dir here.');

	}

	public static function fieldLogDir( $args ){

		self::showTextField( $args ,self::SETTING_FIELD_LOG_DIR_DEFAULT , 'Put your Log Dir here.');

	}

	private static function showTextField( $args , $field, $default){

		// get the value of the setting we've registered with register_setting()
		$options = get_option( self::SETTING_OPTIONS );
		// output the field
		?>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
			   data-custom="<?php echo esc_attr( $args['wporg_custom_data'] ); ?>"
			   name="<?php echo self::SETTING_OPTIONS ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
			   value="<?php echo isset( $options[ $args['label_for'] ] ) ? (  $options[ $args['label_for'] ] ) : ( $default ); ?>"
		/>
		<p class="description">
			<?php esc_html_e( $descr, 'wporg' ); ?>
		</p>
		<?php
	}


	public static function getGaId(){
		$options = get_option( self::SETTING_OPTIONS );

		if (
			empty($options[self::SETTING_FIELD_GA_ID])
			or (self::SETTING_FIELD_GA_ID_DEFAULT == $options[self::SETTING_FIELD_GA_ID] )
		){
			#var_dump($options);
		}else{
			return $options[self::SETTING_FIELD_GA_ID];
		}
	}

	private static function getGdprGa(){

		$options = get_option( self::SETTING_OPTIONS );

		// get Dirs
		$rootDir = dirname(__DIR__);
		$keyDir = $rootDir . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . '.keys';
		$logDir = $rootDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log';

		if ( !empty($options[self::SETTING_FIELD_KEY_DIR]) ){
			$keyDir = $options[self::SETTING_FIELD_KEY_DIR];
		}
		if ( !empty($options[self::SETTING_FIELD_LOG_DIR]) ){
			$logDir = $options[self::SETTING_FIELD_LOG_DIR];
		}

		return new GdprGoogleAnalytics($keyDir, $logDir);

	}

	public static function installKeys(){
		$gdprGa = self::getGdprGa();
		$gdprGa->generateKeys();
	}

    public static function trackPageImpression(){

		$gaId = self::getGaId();
		if (empty($gaId) or self::SETTING_FIELD_GA_ID_DEFAULT == $gaId){

			return false;

		}

		$gdprGa = self::getGdprGa();

		if ( empty($gdprGa) ){

        }else{

			$data = [
				'tid' => $gaId,
				'uip' => $gdprGa->getAnonIp(),
				'ua' => $gdprGa->getAnonUserAgent(),
				'dr' => $gdprGa->getAnonReferrer(),
				'dl' => $gdprGa->getAnonDocumentLocationUrl(),
				'dt' => esc_html( get_the_title() ),
			];

			$result = $gdprGa->track( $data );
			var_dump ($data);
			echo "<pre>$result</pre>";

        }


	}

}