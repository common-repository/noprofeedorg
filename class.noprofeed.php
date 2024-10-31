<?php

class noprofeed_CLASS {

	/**
	 * main class for the noprofeed.org plugin
	 */
	var $version = NOP_VERSION;
//	var $db_version = NOP_DB_VERSION;
	var $plugin_name ='noprofeed.org';
	var $plugin_slug = 'npf-noprofeed';

	var $css;               // name of the css file
	var $js;                // name of the javascript file
	var $url;               // url to the plugin main folder
	var $locale;            // locale to be used for the translations
	var $help;              // help text to be shown in the contextual page
	var $dash_title;        // title of the plugin item in the dashboard
	var $dash_contents;     // contents of the plugin item in the dashboard
	var $main_plugin;       // main plugin file

	function plugin_init() {

		/**
		 * INITIALIZATIONS
		 */
		define('SAVE_BTN', __('Update Options', NOPROFEED_LOCALE ));
		define('SAVE_AND_RELOAD_BTN', __('Update Options and Reload The Feeds Cache', NOPROFEED_LOCALE ));
		define('ACTIVATE_BTN', __('Activate', NOPROFEED_LOCALE ));
		define('DEACTIVATE_BTN', __('Deactivate', NOPROFEED_LOCALE ));
		define('DEACTIVATE_FULL_BTN', __('Fully deactivate', NOPROFEED_LOCALE ));

		/**
		 * Plugin tables
		 * http://codex.wordpress.org/Creating_Tables_with_Plugins
		 */
		global $wpdb;

		$last_cache_update = (int)get_option('nop_wid_last_cache_update');

		if($_POST['btn']==SAVE_AND_RELOAD_BTN) {

			/**
			 * on request, recreate the cache
			 */
			$last_cache_update = 0;
		}

		/**
		 * ONCE A DAY LET'S REBUILD THE FEEDS CACHE
		 * at the end of the day we want fresh news, na?!
		 */
		if($last_cache_update < (time() - 86400)) {

			if((is_admin() && $_GET['page']=='noprofeed.org_settings')
				|| !is_admin()) {

				if($wpdb->get_var('SHOW TABLES LIKE \''.NOP_TABLE_CACHE.'\' ') == NOP_TABLE_CACHE) {

					/**
					 * gets the updated feeds from the server
					 */
					$sql = 'SELECT option_name '
						   .'FROM ' . $wpdb->base_prefix . 'options '
						   .'WHERE (option_name LIKE \'nop_wid_feedFilter_category_%\' '
								   .'OR option_name LIKE \'nop_wid_feedFilter_lang_%\' '
								   .'OR option_name LIKE \'nop_wid_feedFilter_region_%\') '
						   .'AND option_value = 1 '
					;
					$data = $wpdb->get_results($sql, ARRAY_A);

					$options = array();
					foreach($data as $key => $val) {

						$name = str_replace('nop_wid_feedFilter_','',$val['option_name']);
						$k = substr($name,0,1);

						switch(substr($name,0,3)) {

							case 'cat': $value = substr($name,9); break;
							case 'lan': $value = substr($name,5); break;
							case 'reg': $value = substr($name,7); break;

							default:
								$value = '';
						}

						$options[] = $k.'|'.$value;
					}

					$data = $this->get_feeds_update('/getfeedcache/'.'?u='.$_SERVER['SERVER_NAME']
													.'&f='.(int)get_option('nop_wid_feeds2show')
													.'&o='.urlencode(serialize($options))
					);

					/**
					 * write the cache
					 */
					$records = unserialize($data);

					if(is_array($records)) {

						/**
						 * did the sever reported any errors?
						 */
						if(isset($records['error'])) {

							if(is_admin()) {

								echo '<div class="feed_cache_builder_error">'
										.'<p>noprofeed.org &mdash; '
											. __( 'impossible to update the cache table, the server reported the following problems', NOPROFEED_LOCALE ) . ':<br />'
											. $records['error']
										.'</p>'
										.'<p>'
											.'<a href="/wp-admin/options-general.php?page=noprofeed.org_settings">'
												. __( 'Click here to change settings', NOPROFEED_LOCALE )
											.'</a><br />'
											.'<a href="http://noprofeed.org/faq" target="_blank">'
												. __( 'Click here for support', NOPROFEED_LOCALE )
											.'</a>'
										.'</p>'
									.'</div>';
							}
						}
						else {

							/**
							 * empty the cache table
							 */
							$sql = 'TRUNCATE `'.NOP_TABLE_CACHE.'` ';
							$result = $wpdb->query($sql);

							/**
							 * rebuild the cache table
							 */
							foreach($records as $key => $value) {

								$sql = 'INSERT INTO `'.NOP_TABLE_CACHE.'` '
										.'SET '

										.'feedID = '.(int)$value['feedID'].', '
										.'feedURL = \''.mysql_real_escape_string($value['feedURL']).'\', '
										.'siteTITLE = \''.mysql_real_escape_string($value['siteTITLE']).'\', '
										.'favicon = \''.mysql_real_escape_string($value['favicon']).'\', '
										.'siteURL = \''.mysql_real_escape_string($value['siteURL']).'\', '
										.'description = \''.mysql_real_escape_string($value['description']).'\', '
										.'language = \''.mysql_real_escape_string($value['language']).'\', '
										.'title = \''.mysql_real_escape_string($value['fd_title']).'\', '
										.'permalink = \''.mysql_real_escape_string($value['fd_permalink']).'\', '
										.'feedDATE = \''.mysql_real_escape_string($value['fd_date']).'\', '
										.'content = \''.mysql_real_escape_string($value['fd_content']).'\', '

										.'orgName = \''.mysql_real_escape_string($value['org_name']).'\', '
										.'orgURL = \''.mysql_real_escape_string($value['org_url']).'\', '
										.'orgTown = \''.mysql_real_escape_string($value['org_town']).'\', '
										.'orgCountry = \''.mysql_real_escape_string(substr($value['cou_name'],0,1).strtolower(substr($value['cou_name'],1))).'\' '
								;
								$result = $wpdb->query($sql);
							}

							if(is_admin() && $last_cache_update==0) {

								echo '<div class="feed_cache_builder_ok"><p>noprofeed.org &mdash; ' . __( 'cache table correctly updated!', NOPROFEED_LOCALE ) . '</p></div>'; //debug
							}

							update_option( 'nop_wid_last_cache_update', time() );
						}
					}
					else {

						if(is_admin()) {


							echo '<div class="feed_cache_builder_error">'
									.'<p>noprofeed.org &mdash; '
										. __( 'impossible to update the cache table, received incomplete data from the server!', NOPROFEED_LOCALE )
									.'</p>'
									.'<p><a href="http://noprofeed.org/faq" target="_blank" style="color:#ffffff;">'
											. __( 'Click here for support', NOPROFEED_LOCALE )
										.'</a></p>'
//.'$data['.$data.']' //debug
								.'</div>';
						}
					}
				}
				else {

					if(is_admin()) {

						echo '<div class="feed_cache_builder_error">'
								.'<p>noprofeed.org &mdash; '
									. __( 'impossible to create the cache table!', NOPROFEED_LOCALE )
								.'</p>'
								.'<p><a href="http://noprofeed.org/faq" target="_blank" style="color:#ffffff;">'
										. __( 'Click here for support', NOPROFEED_LOCALE )
									.'</a></p>'
//.'$data['.$data.']' //debug
							.'</div>';
					}
				}
			}
		}

		if(is_admin() && !file_exists(NOPROFEED_CSS_OVERRIDE_FILE) || !is_writable(NOPROFEED_CSS_OVERRIDE_FILE)) {

			echo '<div class="feed_cache_builder_error">'
					.'<p>noprofeed.org &mdash; '
						. __( 'the widget style customization file is missing or not writeable!', NOPROFEED_LOCALE )
						. '<br /><br />'. __( 'Please set 666 permissions on this file:', NOPROFEED_LOCALE )
				  		. '<br />'. str_replace( ABSPATH, '', NOPROFEED_CSS_OVERRIDE_FILE )
					.'</p>'
					.'<p><a href="http://noprofeed.org/faq" target="_blank" style="color:#ffffff;">'
							. __( 'Click here for support', NOPROFEED_LOCALE )
						.'</a></p>'
				.'</div>';
		}

		/**
		 * styling
		 */
		wp_register_style('myeasywp_common', MYEASY_CDN_CSS.'myeasywp.css', '', '20110723');   // common myeasy style

		if(strlen($this->css)>0) {

			wp_register_style($this->plugin_slug . '-style', $this->url . '/css/'.$this->css.'.css');
		}

		if(strlen($this->js)>0) {

			wp_register_script($this->plugin_slug . '-script', $this->url . '/js/'.$this->js.'.js');
		}

		wp_enqueue_style( 'myeasywp_common', MYEASY_CDN_CSS.'myeasywp.css', '', '20110723', 'screen' );

		if(strlen($this->css)>0) {

			wp_enqueue_style($this->plugin_slug . '-style', $this->url . '/css/'.$this->css.'.css', '', $this->version, 'screen');
		}

		if(strlen($this->js)>0) {

			wp_enqueue_script($this->plugin_slug . '-script', $this->url . '/js/'.$this->js.'.js', '', $this->version, false);
		}

		if(strlen($this->locale)>0) {

			/**
			 * 1.1
			 */
//			load_plugin_textdomain($this->locale, dirname(__FILE__), dirname(__FILE__) . '/langs/');

			$pageLang = '';
			if(function_exists('icl_get_languages')) {

				$languages = icl_get_languages('');

				foreach($languages as $l) {

					if($l['active']) {

						$pageLang = $l['language_code'] . '_' . strtoupper($l['language_code']);
					}
				}
			}
//echo '$pageLang['.$pageLang.']';

			if($pageLang != '') {

				load_textdomain( $this->locale, dirname(__FILE__) . '/langs/' . $pageLang . '.mo' );
			}
			else if(WPLANG != '') {

				load_textdomain( $this->locale, dirname(__FILE__) . '/langs/' . WPLANG . '.mo' );
			}
		}

		wp_enqueue_style($this->plugin_slug . '-style-override', $this->url . '/noprofeed-override.css', '', $this->version, 'screen');

		/**
		 * housekeeping
		 */
//		register_activation_hook($this->main_plugin, array($this, 'activate'));
//		register_deactivation_hook($this->main_plugin, array($this, 'deactivate'));

//echo 'main_plugin='.$this->main_plugin.'<br>';
//echo 'plugin_file_path='.$this->plugin_file_path.'<br>';
//echo strlen($this->css);
//echo $this->url;
	}

//	function activate() {
//
//		/* todo for some unknown reasons, deactivate works from here, activate not...
//		/* todo so I moved both hooks in the main file
//		 * everything you need to do when activating the plugin
//		 */
//	}

//	function deactivate() {
//
//		/**
//		 * everything you need to do when deactivating the plugin
//		 */
//	}

	function plugin_setup() {

//		/**
//		 * setting up css & js
//		 */
//
////echo '(<b>plugin_setup</b>:'. $this->plugin_name.')';
//
//		wp_enqueue_style( 'myeasywp_common', MYEASY_CDN_CSS.'myeasywp.css', '', '20111206', 'screen' );
//
//		if(strlen($this->css)>0) {
//
//			wp_enqueue_style($this->plugin_slug . '-style', $this->url . '/css/'.$this->css.'.css', '', $this->version, 'screen');
//		}
//
//		if(strlen($this->js)>0) {
//
//			wp_enqueue_script($this->plugin_slug . '-script', $this->url . '/js/'.$this->js.'.js', '', $this->version, false);
//		}


		/**
		 * adding the plugin entry in the settings menu
		 */
		$plugin_page = add_options_page( $this->plugin_name,                 // page title
										 $this->plugin_name,                 // menu title
										 'administrator',                    // access level
										 $this->plugin_name.'_settings',     // file
//										 $this->plugin_name.'_settings_page' // function
										 array($this, 'the_settings_page')   // function
										);

		if(function_exists('add_contextual_help') && strlen($this->help)>0) {

			/**
			 * contextual help
			 */
			add_contextual_help($plugin_page, $this->help);
		}

		if(strlen($this->dash_title)>0) {

			/**
			 * dashboard widget
			 */
			add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
		}
	}

	/**
	 * adding a link to the settings page in the plugin list page
	 */
	function register_plugin_settings($pluginfile) {

		add_action('plugin_action_links_' . basename(dirname($pluginfile)) . '/' . basename($pluginfile),
							array($this, 'plugin_settings'), 10, 1);

		add_filter('plugin_row_meta', $this->add_plugin_links, 10, 2);
	}

	function plugin_settings($links) {

		$settings_link = '<a href="options-general.php?page='.$this->plugin_name.'_settings'.'">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	function add_plugin_links($links, $file) {

		/**
		 * adding an info link in the plugin list page
		 */
		if($file == plugin_basename(__FILE__)) {

			$links[] = __( 'For further information please visit', NOPROFEED_LOCALE ) . ' <a href="'.SERVICE_SITE_URL.'">'.SERVICE_SITE_NAME.'</a>';
		}
		return $links;
	}

	/**
	 * adding the plugin own dashboard widget
	 */
	function add_dashboard_widget() {

		wp_add_dashboard_widget($this->plugin_slug, $this->dash_title, array($this, 'dashboard_widget_function'));
	}

	function dashboard_widget_function() {

		echo $this->dash_contents;
	}

	function the_settings_page() {

		/*
		 * the plugin settings page
		 */
		global $wpdb;

//$sql = 'SELECT count(*) as tu FROM `'.$wpdb->users.'` ';
//$rows = $wpdb->get_results( $sql );

		?><script type="text/javascript">var myeasyplugin = 'myeasynoprofit';</script><?php

		echo '<div class="wrap">'
				.'<div id="icon-options-general" class="icon32" style="background:url(http://myeasywp.com/service/img/icon.png);"><br /></div>'
				.'<h2>noprofeed.org: ' . __( 'Settings' ) . '</h2>'

//.$rows[0]->tu

//			.'</div>'
		;


		/**
		 * tables
		 */
		require_once('inc/shared-functions.php');


		/**
		 *	populate the input fields when the page is loaded
		 */
		$_INPUT['nop_wid_widthSizeDisplay_value'] = (int)$_POST['nop_wid_widthSizeDisplay_value'];
		$_INPUT['nop_wid_feedReadMore'] = (int)$_POST['nop_wid_feedReadMore'];
		$_INPUT['nop_wid_feedShowOrg'] = (int)$_POST['nop_wid_feedShowOrg'];
		$_INPUT['nop_wid_feedShowOrgDonate'] = (int)$_POST['nop_wid_feedShowOrgDonate'];
		$_INPUT['nop_wid_width'] = (int)$_POST['nop_wid_width'];
		$_INPUT['nop_wid_height'] = (int)$_POST['nop_wid_height'];
		$_INPUT['nop_wid_feeds2show'] = (int)$_POST['nop_wid_feeds2show'];
		$_INPUT['nop_wid_feedHeight'] = (int)$_POST['nop_wid_feedHeight'];

		if(isset($_POST['nop_wid_feedStyle_paper'])) {

			$_INPUT['nop_wid_feedStyle_paper'] = 1;
		}
		else {

			$_INPUT['nop_wid_feedStyle_paper'] = 0;
		}

		if(isset($_POST['nop_wid_feedStyle_3D'])) {

			$_INPUT['nop_wid_feedStyle_3D'] = 1;
		}
		else {

			$_INPUT['nop_wid_feedStyle_3D'] = 0;
		}

		foreach($langs as $key => $val) {

			$_INPUT['nop_wid_feedFilter_lang_'.$key] = $_POST['nop_wid_feedFilter_lang_'.$key];
		}

		foreach($categories as $key => $val) {

			$_INPUT['nop_wid_feedFilter_category_'.$key] = $_POST['nop_wid_feedFilter_category_'.$key];
		}

		foreach($regions as $key => $val) {

			$_INPUT['nop_wid_feedFilter_region_'.$key] = $_POST['nop_wid_feedFilter_region_'.$key];
		}

		$_INPUT['nop_wid_feedStyle_textcolor'] = $_POST['nop_wid_feedStyle_textcolor'];
		$_INPUT['nop_wid_feedStyle_linkscolor'] = $_POST['nop_wid_feedStyle_linkscolor'];
		$_INPUT['nop_wid_feedStyle_bgcolor'] = $_POST['nop_wid_feedStyle_bgcolor'];
		$_INPUT['nop_wid_feedStyle_bordcolor'] = $_POST['nop_wid_feedStyle_bordcolor'];


		/**
		 * set the default values
		 */
		if((int)$_INPUT['nop_wid_widthSizeDisplay_value']==0) {

			$_INPUT['nop_wid_widthSizeDisplay_value'] = 200;
		}

		if(!isset($_POST['nop_wid_feedReadMore']) && strlen(get_option( 'nop_wid_feedReadMore' ))==0) {

			$_INPUT['nop_wid_feedReadMore'] = 1;
		}

		if(!isset($_POST['nop_wid_feedShowOrg']) && strlen(get_option( 'nop_wid_feedShowOrg' ))==0) {

			$_INPUT['nop_wid_feedShowOrg'] = 1;
		}

		if(!isset($_POST['nop_wid_feedShowOrgDonate']) && strlen(get_option( 'nop_wid_feedShowOrgDonate' ))==0) {

			$_INPUT['nop_wid_feedShowOrgDonate'] = 1;
		}

		if(!isset($_POST['nop_wid_feedStyle_paper']) && strlen(get_option( 'nop_wid_feedStyle_paper' ))==0) {

			$_INPUT['nop_wid_feedStyle_paper'] = 1;
		}

		if(!isset($_POST['nop_wid_feedStyle_3D']) && strlen(get_option( 'nop_wid_feedStyle_3D' ))==0) {

			$_INPUT['nop_wid_feedStyle_3D'] = 1;
		}

		if(!isset($_POST['nop_wid_feedStyle_textcolor'])) {

			$_INPUT['nop_wid_feedStyle_textcolor'] = '666666';
		}

		if(!isset($_POST['nop_wid_feedStyle_linkscolor'])) {

			$_INPUT['nop_wid_feedStyle_linkscolor'] = '346AA8';
		}

		if(!isset($_POST['nop_wid_feedStyle_bgcolor'])) {

			$_INPUT['nop_wid_feedStyle_bgcolor'] = 'DED9D2';
		}

		if(!isset($_POST['nop_wid_feedStyle_bordcolor'])) {

			$_INPUT['nop_wid_feedStyle_bordcolor'] = 'DED9D2';
		}

		if(count($_POST)==0) {

			/**
			 * when the page is loaded without the form being posted, load the values from the database
			 */
//var_dump($_POST);
//echo ' {'.count($_POST).'}';

			$_INPUT['nop_wid_widthSizeDisplay_value'] = (int)get_option( 'nop_wid_widthSizeDisplay_value' );
			$_INPUT['nop_wid_feedReadMore'] = (int)get_option( 'nop_wid_feedReadMore' );
			$_INPUT['nop_wid_feedShowOrg'] = (int)get_option( 'nop_wid_feedShowOrg' );
			$_INPUT['nop_wid_feedShowOrgDonate'] = (int)get_option( 'nop_wid_feedShowOrgDonate' );
			$_INPUT['nop_wid_width'] = (int)get_option( 'nop_wid_width' );
			$_INPUT['nop_wid_height'] = (int)get_option( 'nop_wid_height' );
			$_INPUT['nop_wid_feeds2show'] = (int)get_option( 'nop_wid_feeds2show' );
			$_INPUT['nop_wid_feedHeight'] = (int)get_option( 'nop_wid_feedHeight' );
			$_INPUT['nop_wid_feedStyle_paper'] = (int)get_option( 'nop_wid_feedStyle_paper' );
			$_INPUT['nop_wid_feedStyle_3D'] = (int)get_option( 'nop_wid_feedStyle_3D' );

//echo 'nop_wid_feeds2show{'.$_INPUT['nop_wid_feeds2show'].'}';


			foreach($langs as $key => $val) {

				$_INPUT['nop_wid_feedFilter_lang_'.$key] = get_option( 'nop_wid_feedFilter_lang_'.$key );
			}

			foreach($categories as $key => $val) {

				$_INPUT['nop_wid_feedFilter_category_'.$key] = get_option( 'nop_wid_feedFilter_category_'.$key );
			}

			foreach($regions as $key => $val) {

				$_INPUT['nop_wid_feedFilter_region_'.$key] = get_option( 'nop_wid_feedFilter_region_'.$key );
			}

			$_INPUT['nop_wid_feedStyle_textcolor'] = get_option( 'nop_wid_feedStyle_textcolor' );
			$_INPUT['nop_wid_feedStyle_linkscolor'] = get_option( 'nop_wid_feedStyle_linkscolor' );
			$_INPUT['nop_wid_feedStyle_bgcolor'] = get_option( 'nop_wid_feedStyle_bgcolor' );
			$_INPUT['nop_wid_feedStyle_bordcolor'] = get_option( 'nop_wid_feedStyle_bordcolor' );
		}


		/**
		 * actions
		 */
		switch($_POST['btn']) {

//			#----------------
//			case ACTIVATE_BTN:
//			#----------------
//				#
//				#	activate the plugin
//				#
//
//				break;
//				#
			#----------------
			case SAVE_BTN:
			case SAVE_AND_RELOAD_BTN:
			#----------------
				#
				#	save the posted value in the database
				#
				foreach($_INPUT as $key => $val) {

					update_option( $key, $val );
//echo $key.'<br>';
				}

				/**
				 * re-create the css override file
				 */
				$fp = fopen(NOPROFEED_CSS_OVERRIDE_FILE, 'w');
				if($fp) {

					fwrite($fp, '/* automatically created on '.date('Y-m-d H:i:s',time()) . ' by ' . $_SERVER['HTTP_HOST'] . " */\n\n");

					fwrite($fp, '#npf-widget-container{width:' . $_INPUT['nop_wid_widthSizeDisplay_value'] . 'px;}' . "\n");

					if($_INPUT['nop_wid_feedStyle_3D']==1) {

						fwrite($fp, '#npf-wid-topShadow,#npf-wid-botShadow{width:80%;margin-left:10%;height:10px;background:url('
																	.NOPROFEED_CDN
																	.'widget-shadow.png) no-repeat center top;display:block;}' . "\n");
//116%
						fwrite($fp, '#npf-wid-botShadow{background:url('
						                                            .NOPROFEED_CDN
						                                            .'widget-shadow.png) no-repeat center bottom;}' . "\n");
					}
					else {

						fwrite($fp, '#npf-wid-topShadow,#npf-wid-botShadow{display:none;}' . "\n");
					}

					fwrite($fp, '#npf-widget{padding:10px 20px;border:1px solid #' . $_INPUT['nop_wid_feedStyle_bordcolor'] . ';'
					            . 'color:#' . $_INPUT['nop_wid_feedStyle_textcolor'] . ';'
					); // ex: padding:4% 8%;width:100%;

					if($_INPUT['nop_wid_feedStyle_paper']==1) {

						fwrite($fp, 'background:#' . $_INPUT['nop_wid_feedStyle_bgcolor'] . ' url('.NOPROFEED_CDN.'bg-pattern.png) repeat;');
					}
					else {

						fwrite($fp, 'background:#' . $_INPUT['nop_wid_feedStyle_bgcolor'] . ';');
					}
					fwrite($fp, '}' . "\n");

					fwrite($fp, '#npf-widget a {color:#' . $_INPUT['nop_wid_feedStyle_linkscolor'] . ';}' . "\n");


//					fwrite($fp, '#npf-widget{color:#' . $_INPUT['nop_wid_feedStyle_bgcolor'] . ';}' . "\n");

//style="width:116%;margin-left:0;height:10px;background:url('.NOPROFEED_CDN.'/widget-shadow.png) no-repeat center top;display:block;"


					fclose($fp);
				}


//				$MYENOPROFIT_frontend = new noprofeed.org_FRONTEND();
//				$MYENOPROFIT_frontend->locale = NOPROFEED_LOCALE;

				break;
				#
			default:
		}


		?><form name="plugin_settings" method="post" action="">

			<div class="light"><?php
				#
				#	widget settings
				#
				echo '<div style="text-align:left;margin:6px 12px;"><b>&raquo; ' . __('Settings about how the widget will look like', NOPROFEED_LOCALE ) . '</b></div>';

				?><div class="left"><?php

//					$tmp = 'text';	#	debug
//					$tmp = 'hidden';

					echo '<div style="margin:0 0 0 6px;">';
						require_once('inc/sample_widget.php');
					echo '</div>';

				?>
				</div>

				<div class="right">
					<div class="medium" style="float:left;">
						<div class="light" style="/*float:left;*/margin-top:0;padding:12px;width:96%;"><?php

							$nop_wid_feedReadMore ='';
							if($_INPUT['nop_wid_feedReadMore']==1) {

								$nop_wid_feedReadMore = ' checked="checked"';
							}

							$nop_wid_feedShowOrg ='';
							if($_INPUT['nop_wid_feedShowOrg']==1) {

								$nop_wid_feedShowOrg = ' checked="checked"';
							}

							$nop_wid_feedShowOrgDonate ='';
							if($_INPUT['nop_wid_feedShowOrgDonate']==1) {

								$nop_wid_feedShowOrgDonate = ' checked="checked"';
							}

							?><p style="font-weight:bold;margin-top:0;"><?php

								_e('Actual widget size: ', NOPROFEED_LOCALE );
								_e('width', NOPROFEED_LOCALE ); ?> <span id="nop_wid_widthSizeDisplay" style="color:#346AA8;"><?php echo $_INPUT['nop_wid_width']; ?></span>, <?php
								_e('height', NOPROFEED_LOCALE ); ?> <span id="nop_wid_heightSizeDisplay" style="color:#346AA8"><?php echo $_INPUT['nop_wid_height']; ?></span>

							</p>
							<input type="hidden" id="nop_wid_widthSizeDisplay_value" name="nop_wid_widthSizeDisplay_value" value="<?php echo (int)$_INPUT['nop_wid_widthSizeDisplay_value']; ?>" />

							<p style="margin-top:0;">
								<?php _e('Move the slider knob to change the widget size; size limits are in the range of 200~401 pixels.', NOPROFEED_LOCALE ); ?>
							</p>

							<div id="nop_slider_wait" style="padding:8px;display:block;"><img src="<?php echo plugins_url('', __FILE__); ?>/img/loading.gif" align="absmiddle" style="margin-right:8px;" /><?php _e('Please wait...', NOPROFEED_LOCALE ); ?></div>
							<input class="slider" style="display:none;" type="text" id="nop_wid_width" name="nop_wid_width" value="<?php echo $_INPUT['nop_wid_width']; ?>" />

							<p style="margin-top:0;">
								<?php _e('The height of the widget is determined by the following settings: increasing their values you will increase the widget height.', NOPROFEED_LOCALE ); ?>
							</p>

							<p style="margin-top:0;">
								<?php _e('Number of feeds', NOPROFEED_LOCALE ); ?>:<br />
								<select id="nop_wid_feeds2show" name="nop_wid_feeds2show" onchange="npf_wid_toggle_feedItems(this.value);"><?php

									for($i=1;$i<6;$i++) {

										$checked = '';
										if($i == (int)$_INPUT['nop_wid_feeds2show']) {

											$checked = ' selected="selected"';
										}
										echo '<option value="'.$i.'" '.$checked.'>&nbsp;'.$i.'&nbsp;</option>';
									}

								?></select>
							</p>

							<p style="margin-top:0;"><?php

								$selected0 = '';
								$selected1 = '';
								$selected2 = '';
								switch($_INPUT['nop_wid_feedHeight']) {
									case 1: $selected1 = ' selected="selected"'; break;
									case 2: $selected2 = ' selected="selected"'; break;
									default:
										$selected0 = ' selected="selected"';
								}

								_e('How much information will be shown for each feed', NOPROFEED_LOCALE );

								?>:<br />
								<select id="nop_wid_feedHeight" name="nop_wid_feedHeight" onchange="npf_wid_toggle_feedContent(this.value);">
									<option value="0" <?php echo $selected0; ?>><?php _e('The title and the entire feed content', NOPROFEED_LOCALE ); ?></option>
									<option value="1" <?php echo $selected1; ?>><?php _e('The title and the first sentence', NOPROFEED_LOCALE ); ?></option>
									<option value="2" <?php echo $selected2; ?>><?php _e('Just the title', NOPROFEED_LOCALE ); ?></option>
								</select>
							</p>

							<p style="margin-top:0;">
								<input type="checkbox" name="nop_wid_feedReadMore" id="nop_wid_feedReadMore" value="1" <?php echo $nop_wid_feedReadMore; ?>
										onchange="npf_wid_toggle_readMore(this.checked);" />
								<label for="nop_wid_feedReadMore"><?php _e('Show the Read more link', NOPROFEED_LOCALE ); ?></label>
							</p>

							<p style="margin-top:0;">
								<input type="checkbox" name="nop_wid_feedShowOrg" id="nop_wid_feedShowOrg" value="1" <?php echo $nop_wid_feedShowOrg; ?>
										onchange="npf_wid_toggle_orgInfo(this.checked);" />
								<label for="nop_wid_feedShowOrg"><?php _e('Show the organization information', NOPROFEED_LOCALE ); ?></label>
							</p>

							<p style="margin-top:0;">
								<input type="checkbox" name="nop_wid_feedShowOrgDonate" id="nop_wid_feedShowOrgDonate" value="1" <?php echo $nop_wid_feedShowOrgDonate; ?>
								       onchange="npf_wid_toggle_orgDonate(this.checked);" />
								<label for="nop_wid_feedShowOrgDonate"><?php _e('Show the feed donation button', NOPROFEED_LOCALE ); ?></label>
							</p>

							<div style="clear:both;margin:12px 0 0 0;width:100%;text-align:right;"><?php
								echo ''
									.'<input class="button-secondary" type="button" onclick="npf_presetSettings(0);" value="'
											.__('Show the shortest possible widget', NOPROFEED_LOCALE ).'" />'
									.'<input class="button-secondary" style="margin-left:20px;" type="button" onclick="npf_presetSettings(1);" value="'
											.__('Show the most complete information', NOPROFEED_LOCALE ).'" />'
								;
							?></div>
						</div>

						<div class="light" style="/*float:left;*/padding:12px;width:96%;"><?php

							$nop_wid_feedStyle_paper ='';
							if($_INPUT['nop_wid_feedStyle_paper']==1) {

								$nop_wid_feedStyle_paper = ' checked="checked"';
							}

							$nop_wid_feedStyle_3D ='';
							if($_INPUT['nop_wid_feedStyle_3D']==1) {

								$nop_wid_feedStyle_3D = ' checked="checked"';
							}

							?><p style="font-weight:bold;margin-top:0;"><?php _e('Style', NOPROFEED_LOCALE ); ?></p>

							<p style="margin-top:0;">
								<?php _e('Text color', NOPROFEED_LOCALE ); ?>:
								<input class="nop-wid-color" type="text" id="nop_wid_feedStyle_textcolor" name="nop_wid_feedStyle_textcolor" value="<?php echo $_INPUT['nop_wid_feedStyle_textcolor']; ?>" size="6" />
								<?php _e('Links color', NOPROFEED_LOCALE ); ?>:
								<input class="nop-wid-color" type="text" id="nop_wid_feedStyle_linkscolor" name="nop_wid_feedStyle_linkscolor" value="<?php echo $_INPUT['nop_wid_feedStyle_linkscolor']; ?>" size="6" />
							</p>

							<p style="margin-top:0;">
								<?php _e('Background color', NOPROFEED_LOCALE ); ?>:
								<input class="nop-wid-color" type="text" id="nop_wid_feedStyle_bgcolor" name="nop_wid_feedStyle_bgcolor" value="<?php echo $_INPUT['nop_wid_feedStyle_bgcolor']; ?>" size="6" />
								<?php _e('Border color', NOPROFEED_LOCALE ); ?>:
								<input class="nop-wid-color" type="text" id="nop_wid_feedStyle_bordcolor" name="nop_wid_feedStyle_bordcolor" value="<?php echo $_INPUT['nop_wid_feedStyle_bordcolor']; ?>" size="6" />
							</p>

							<p style="margin-top:0;">
								<input type="checkbox" name="nop_wid_feedStyle_paper" id="nop_wid_feedStyle_paper" value="1" <?php echo $nop_wid_feedStyle_paper; ?>
								       onchange="npf_wid_toggle_paper(this.checked);" />
								<label for="nop_wid_feedStyle_paper"><?php _e('Give the background a paper feeling', NOPROFEED_LOCALE ); ?></label>
							</p>

							<p style="margin-top:0;">
								<input type="checkbox" name="nop_wid_feedStyle_3D" id="nop_wid_feedStyle_3D" value="1" <?php echo $nop_wid_feedStyle_3D; ?>
								       onchange="npf_wid_toggle_shadows(this.checked);" />
								<label for="nop_wid_feedStyle_3D"><?php _e('Show the shadows to give a 3D effect', NOPROFEED_LOCALE ); ?></label>
							</p>
						</div>
						<div style="clear:both;margin:12px 0 0 0;width:100%;text-align:right;"><?php
							echo ''
								.'<input class="button-secondary" type="button" onclick="npf_defaultSettings();" value="'
										.__('Reset the default values', NOPROFEED_LOCALE ).'" />'
							;
						?></div>
					</div>

				</div>
				<div style="clear:both;"></div>
			</div>
			<?php

			show_feed_registration_fields(true, true, $langs, $categories, $regions, $_INPUT, $error);

			?>
			<div class="button-separator">
				<input class="button-primary" style="margin:14px 12px;" type="submit" name="btn" value="<?php echo SAVE_BTN; ?>" />
				<input class="button-primary" style="margin:14px 12px;" type="submit" name="btn" value="<?php echo SAVE_AND_RELOAD_BTN; ?>" />
			</div>

			</form>
			<div style="clear:both;"></div><?php
/*		} */
		//include_once(MEH_PATH . '/inc/myEASYcom.php');
//		measycom_camaleo_links();

	}

	function get_feeds_update($domain_path) {

		/**
		 * get the feeds updates
		 */
		$domain = 'noprofeed.org';
//$domain = 'noprofeed.lan';//debug

		$data = '';

		$fp = @fsockopen($domain, 80, $errno, $errstr, 3);

		if(!$fp) {

			/**
			 * HTTP ERROR
			 */
			$data = 'Connection error get_feeds_update(' . $domain_path . ')';
		}
		else {

			/**
			 * get the info
			 */
			$header = "GET $domain_path HTTP/1.0\r\n"           // 1.0 !!
						."Host: $domain\r\n"
						."Connection: Close\r\n\r\n"
						//."Connection: keep-alive\r\n\r\n"
			;
			fwrite($fp, $header);

			$result = '';
			while (!feof($fp)) {

				$result .= fgets($fp, 1024);
			}

			$needle = '[start]';
			$p = strpos($result, $needle, 0);
			if($p!==false) {

				$beg = $p + strlen($needle);
				$end = strpos($result, '[end]', $p);
				$data = substr($result, $beg, ($end-$beg));
			}

			fclose($fp);
		}

		if(strlen($data)==0) {

			return $result;
		}
		return $data;
	}

}


class noprofeed_FRONTEND extends noprofeed_CLASS {

	/**
	 * only executed in the frontend: visiting the site
	 */
	function noprofeed_FRONTEND() {

		/**
		 * ...
		 */
		$this->url = plugins_url('', __FILE__);
		add_action('init', array($this, 'plugin_init'));


		/**
		 * on demand, show the credits on the footer
		 */
		if(get_option('myeasy_showcredits')==1 && !function_exists('myeasy_credits') && !defined('MYEASY_SHOWCREDITS')) {    /* 1.0.1 changed all references from 'myewally_showcredits' */

			define('MEBAK_FOOTER_CREDITS', '<div style="font-size:9px;text-align:center;">'
					.'<a href="http://myeasywp.com" target="_blank">Improve Your Life, Go The myEASY Way&trade;</a>'
					.'</div>');

			/**
			 * on demand, show the credits on the footer
			 */
			add_action('wp_footer', 'myeasy_credits');
			function myeasy_credits() {

				echo MEBAK_FOOTER_CREDITS;
				define('MYEASY_SHOWCREDITS', true);
			}
		}
	}
}

class noprofeed_BACKEND extends noprofeed_CLASS {

	/**
	 * only executed in the backend: admininstration
	 */
	function noprofeed_BACKEND() {

		$this->url = plugins_url('', __FILE__);

		add_action('admin_init', array($this, 'plugin_init'));
		add_action('admin_menu', array($this, 'plugin_setup'));

		add_action('wp_dashboard_setup', array($this, 'register_dashboard_widget_news'));
	}

	function register_dashboard_widget_news() {

		wp_add_dashboard_widget('noprofeedorg-news', 'noprofeed.org news', array($this, 'noprofeedorg_dashnews'));
	}

	function noprofeedorg_dashnews() {

		/**
		 * https://twitter.com/about/resources/widgets
		 *
		 * http://twitter.com/javascripts/widgets/widget.js
		 * http://twitter.com//stylesheets/widgets/widget.css
		 */
		echo ''
//			.'<script src="' . $this->url . '/js/src/npf-twitter.dev.js"></script>'
			.'<script src="' . $this->url . '/js/npf-twitter.js"></script>'
			.'<script>'
			.'new TWTR.Widget({'
				.'version:2,'
				.'type:\'profile\','
				.'rpp:4,'
				.'interval:6000,'
				.'width:\'auto\','
				.'height:300,'
				.'theme:{'
					.'shell:{'
						.'background:\'#dddddd\','
						.'color:\'#595859\''
					.'},'
					.'tweets:{'
						.'background:\'#ffffff\','
						.'color:\'#808080\','
						.'links:\'#70ad56\''
					.'}'
				.'},'
				.'features:{'
					.'scrollbar:true,'
					.'loop:false,'
					.'live:true,'
					.'hashtags:true,'
					.'timestamp:true,'
					.'avatars:false,'
					.'behavior:\'all\''
				.'}'
			.'}).render().setUser(\'noprofeed\').start();'
			.'</script>'
		;
	}
}

class noprofeed_widget extends WP_Widget {

	/**
	 * the noprofeed widget
	 */
	function noprofeed_widget() {

		/**
		 * Settings
		 */
		$widget_ops = array(
			'classname' => 'example',
			'description' => __('Help non-profit organizations to disseminate about their activities and to collect donations.', NOPROFEED_LOCALE)
		);

		/**
		 * Control settings
		 */
		$control_ops = array(
			'width' => 300,
			'height' => 350,
			'id_base' => 'noprofeed-widget'
		);

		/**
		 * Create the widget
		 */
		$this->WP_Widget('noprofeed-widget', 'noprofeed.org', $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		/**
		 * Display the widget on the screen
		 */

		global $before_widget, $after_widget, $before_title, $after_title;

		extract($args);

		/**
		 * Our variables from the widget settings
		 */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes) */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes) */
		if($title) {

			echo $before_title . $title . $after_title;
		}

		echo $this->widget_body();

		/* After widget (defined by themes) */
		echo $after_widget;
	}

	function donation_form($form_name, $item_name, $ref_desc, $ref_value) {

		/**
		 * return a parametric form to send paypal donations
		 */
		$url = 'www.paypal.com';
		$business = 'noprofeed@seedlearn.org';

		if(defined('isSANDBOX') && isSANDBOX==true) {

			$url = 'www.sandbox.paypal.com';
			$business = 'ugobiz_1263036493_biz@grandolini.net';
		}

		$form = '<div style="display:none">'
					.'<form action="https://' . $url . '/cgi-bin/webscr" id="' . $form_name . '" name="' . $form_name . '" method="post" target="_blank">'
						.'<input type="hidden" name="cmd" value="_xclick" />'
						.'<input type="hidden" name="business" value="' . $business . '" />'
						.'<input type="hidden" name="no_shipping" value="1" />'
						.'<input type="hidden" name="tax" value="0" />'
						.'<input type="hidden" name="no_note" value="0" />'
						.'<input type="hidden" name="item_name" value="' . $item_name . '" />'
						.'<input type="hidden" name="on0" value="' . $ref_desc . '" />'
						.'<input type="hidden" name="os0" value="' . $ref_value . '" />'
						.'<input type="hidden" name="currency_code" value="" />'
						.'<input type="hidden" name="amount" value="" />'
					.'</form>'
				.'</div>'
		;

		return $form;
	}


	function update( $new_instance, $old_instance ) {

		/**
		 * Update the widget settings
		 */
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs) */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	function form( $instance ) {

		/**
		 * Displays the widget settings controls on the widget panel
		 */
		$defaults = array(
			'title' => __('Help non-profit orgs!', NOPROFEED_LOCALE)
		);

		$instance = wp_parse_args((array)$instance, $defaults);

		?><p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
			       value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p><?php

	}

	function get_feed_cache_record() {

		/**
		 * get the required number of records from the cache
		 */
		global $wpdb;

		$sql = 'SELECT '

				.'id, '
				.'lastDisplay, '
				.'feedID, '
				.'feedURL, '
				.'siteTITLE, '
				.'favicon, '
				.'siteURL, '
				.'description, '
				.'language, '
				.'title, '
				.'permalink, '
				.'feedDATE, '
				.'content, '

				.'orgName, '
				.'orgURL, '
				.'orgTown, '
				.'orgCountry '

			.'FROM `'.NOP_TABLE_CACHE.'` '

			.'ORDER BY lastDisplay ASC '

			.'LIMIT '.(int)get_option('nop_wid_feeds2show').' '
		;
//echo $sql;

		$data = $wpdb->get_results($sql, ARRAY_A);

		/**
		 * set feeds rotation
		 */
		foreach($data as $key => $val) {

			$sql = 'UPDATE `'.NOP_TABLE_CACHE.'` '
					.'SET '
					.'lastDisplay = \''.mysql_real_escape_string(date('Y-m-d H:i:s', time())).'\' '

				.'WHERE id = '.(int)$val['id'].' '
			;
			$result = $wpdb->query($sql);
//echo $sql.'<br>';
		}

		return $data;
	}

	function widget_body() {

		/**
		 * show the widget body
		 */
		$feedHeight = get_option('nop_wid_feedHeight');
		$readMore = get_option('nop_wid_feedReadMore');
		$showOrg = get_option('nop_wid_feedShowOrg');
		$showOrgDonate = get_option('nop_wid_feedShowOrgDonate');


		$html = '<div id="npf-widget-container">'
				.'<div id="npf-wid-topShadow"></div>'
				.'<div id="npf-widget">'

					.'<div class="npf-wid-header">'
						.'<img src="'.NOPROFEED_CDN.'logo-widget.png" alt="" width="100%" />'
					.'</div>'

					.'<div class="npf-wid-block">'
		;

		$data = $this->get_feed_cache_record();
//var_dump($data);

		$afterTheFirst = false;

		foreach($data as $key => $value) {

//var_dump($value);echo '<hr>';

			if($afterTheFirst == true) {

				$html .= '<hr style="width:70%" />';
			}
			$afterTheFirst = true;

			$html .= '<div class="npf-wid-body">'

				/**
				 * feed title
				 */
//				.'<span class="npf-wid-feed-title">'
//				    .'<a href="'.$value['permalink'].'">'
//						.$value['title']
//					.'</a>'
//				.'</span><br />'
				.'<p class="npf-wid-feed-title">'
					. ($value['title'] ? $value['title'] : $value['siteTITLE'])
				.'</p>'

				/**
				 * feed rating
				 */
//todo ----------------------
//				.'<div class="npf-wid-rating">'
//					.'<span id="npf-wid-rateStatus-1" class="npf-wid-rateStatus">Rate this info</span>'
//					.'<span id="npf-wid-ratingSaved-1" class="npf-wid-ratingSaved">Rating Saved!</span>'
//					.'<div class="npf-wid-rateMe" title="Rate this organization">'
//						.'<a onclick="npf_WidRate(this,1)" id="widrate1_1" title="ehh..." onmouseover="npf_WidRating(this,1)" onmouseout="npf_WidRatingOff(this,1);npf_setActualRating(5,1)"></a>'
//						.'<a onclick="npf_WidRate(this,1)" id="widrate1_2" title="Not Bad" onmouseover="npf_WidRating(this,1)" onmouseout="npf_WidRatingOff(this,1);npf_setActualRating(5,1)"></a>'
//						.'<a onclick="npf_WidRate(this,1)" id="widrate1_3" title="Good" onmouseover="npf_WidRating(this,1)" onmouseout="npf_WidRatingOff(this,1);npf_setActualRating(5,1)"></a>'
//						.'<a onclick="npf_WidRate(this,1)" id="widrate1_4" title="Superb" onmouseover="npf_WidRating(this,1)" onmouseout="npf_WidRatingOff(this,1);npf_setActualRating(5,1)"></a>'
//						.'<a onclick="npf_WidRate(this,1)" id="widrate1_5" title="Awesome!" onmouseover="npf_WidRating(this,1)" onmouseout="npf_WidRatingOff(this,1);npf_setActualRating(5,1)"></a>'
//
//						/* feed date */
//						.'<span class="npf-wid-feed-date">'.date('d M Y', strtotime($value['feedDATE'])).'</span>'
//
////todo ----------------------
//						.'<img style="vertical-align:middle;cursor:pointer;margin-left:6px;" '
//							.'src="'.NOPROFEED_CDN.'/abuse.png" width="20" '
//							.'title="Report abuse!" '
//							.'onclick="alert(\'*** TODO Report abuse (feedID='.$value['feedID'].') ! ***\');" />'
////todo ----------------------
//					.'</div>'
//				.'</div>'
//todo ----------------------
			;

			/**
			 * content
			 */
			switch($feedHeight) {

				case 2:
					/**
					 * nothing
					 */
					break;

				case 1:
					/**
					 * just the first sentence
					 */
					$tmp = explode('.', strip_tags($value['content'], '<br><p>'));

					$html .= $tmp[0] . '.';
					break;

				case 0;
				default;
					/**
					 * full
					 */
/* 1.0.1: BEG */
//					$html .= strip_tags($value['content'], '<br><p>');

					$tmp = explode(' ', strip_tags($value['content'], '<br><p>'));

					$t = count($tmp);
					$tm = (int)NOPROFEED_WID_MAX_WORDS + 1;

					if($t < $tm) {

						$html .= $value['content'];
					}
					else {

						for($i=0; $i<$tm; $i++) {

							$html .= $tmp[$i] . ' ';
						}
						$html = substr($html, 0, -1) . '...<br />';
					}
/* 1.0.1: END */
			}

			if($readMore==1) {

				$html .= '&raquo;  <a href="'.$value['permalink'].'">' . __('Read more', NOPROFEED_LOCALE) . '</a><br />';
			}

			$html .= '</div>'
				.'<div class="npf-wid-feed-info">';

			if($showOrg==1) {

				$html .= '<div class="npf-wid-feed-org">'

					/**
					 * feed icon
					 */
					.'<img style="vertical-align:middle;cursor:help;" width="16" height="16" '
						.'src="'.$value['favicon'].'" '
						.'title="'.$value['description'].'" '
						.'onclick="alert(\''.$value['description'].'\');" /> '

					/**
					 * provider info
					 */
					.'<a href="'.$value['orgURL'].'" target="_blank"><strong>'
							.$value['orgName']
						 .'</strong></a>'

						.'<br />'
						.$value['orgTown'] . ', ' . $value['orgCountry']
						.'<br />'
					.'</div>'
				;
			}

			$html .= ''
					.'</div>'
					.'<div style="clear:both;"></div>'
			;

			if($showOrgDonate==1) {

				$formID = 'donate'.$value['feedID'];

				$html .= $this->donation_form($formID,'Donation to noprofeed.org','Ref.',$value['orgName'].' ('.$value['feedID'].')')
						.'<div class="npf-wid-donate-feed" onclick="document.'.$formID.'.submit();"></div>';
			}
		}

		/**
		 * footer
		 */
		$html .= '</div>'       /* npf-wid-block */

			.'<hr style="width:70%" />'

//			.'<div style="clear:both;margin:20px 0;">'
//				.'<input type="button" class="npf-wid-btnbig" value="Donate to noprofeed.org!" />'
//				.'<div style="clear:both;"></div>'
//			.'</div>'

			/**
			 * set the actual values for each rating
			 */
			.'<script>'
//todo ----------------------
//				.'//npf_setActualRating({how many stars the feed already has},{feed id});'
//				.'npf_setActualRating(5,1);'
//				.'npf_setActualRating(3,2);'
//todo ----------------------
			.'</script>'

			.'<div style="clear:both;width:100%;text-align:center;">'
				.'<p style="font-size:large;margin:6px 0;">'
					.'<a href="http://noprofeed.org" target="_blank">Add this to your blog!</a>'
				.'</p>'

//				.'<p style="font-size:x-small;margin:0;">'
//					.'developed by <a href="http://grandolini.com" target="_blank">Mr Camaleo</a> for <a href="http://www.seedlearn.org" target="_blank">Seed*</a>'
//				.'</p>'

				/* donation to noprofeed */
				.'<div align="center">'
				.$this->donation_form('donateus','Donation to noprofeed.org','Ref.','To support Seed efforts')
				.'<div class="npf-wid-donate-us" onclick="document.donateus.submit();"></div>'
				.'</div>'
			.'</div>'

			.'</div>'                               /* npf-widget */
			.'<div style="clear:both;"></div>'
			.'<div id="npf-wid-botShadow"></div>'

		.'</div>'                                   /* npf-widget-container */
		.'<div style="clear:both;"></div>';

		return $html;
	}
}

function load_noprofeed_widget() {

	register_widget( 'noprofeed_widget' );
}

?>