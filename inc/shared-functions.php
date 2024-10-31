<?php
/**
 * stuff shared between the noprofeed.org site and its plugin
 */
global $langs, $categories, $regions, $_INPUT;

if(!file_exists(ABSPATH . 'np-extras/np-config.php')) {

	require_once('shared_data.php');
}
else {

	/**
	 * data tables
	 */
	define('noLOAD', true);
	require_once(ABSPATH . 'np-extras/np-config.php');

	$fp = fopen(dirname(__FILE__) . '/shared_data.php', 'w');
	$CR = "\n";
	$CR = '';   /* uncomment to compress the resulting file */

	if($fp) {

		fwrite($fp, '<?php' . "\n");
		fwrite($fp, '/* automatically created on '.date('Y-m-d H:i:s',time()) . ' by ' . $_SERVER['HTTP_HOST'] . ' */' . $CR);

		fwrite($fp, '$langs = array();' . $CR);
		fwrite($fp, '$categories = array();' . $CR);
		fwrite($fp, '$regions = array();' . $CR);
	}

	/**
	 * languages
	 */
	$sql = 'SELECT * '

			.'FROM `'.NP_TABLE_LANGUAGES.'` '

			.'WHERE !ISNULL(`iso-639-1`) '
			.'AND isACTIVE = 1 '
			.'ORDER BY name ASC '
	;
	$data = $wpdb->get_results($sql, ARRAY_A);

	$langs = array();
	foreach($data as $key => $val) {

		$langs[$val['iso-639-1']] = $val['name'];

		if($fp) {

			fwrite($fp, '$langs[\'' . $val['iso-639-1'] .'\']=\'' . $val['name'] . '\';' . $CR);
		}
	}

	/**
	 * categories
	 */
	$sql = 'SELECT * '

			.'FROM `'.NP_TABLE_CATEGORIES.'` '

			.'ORDER BY name ASC '
	;
	$data = $wpdb->get_results($sql, ARRAY_A);

	$categories = array();
	foreach($data as $key => $val) {

		$categories[$val['id']] = $val['name'];

		if($fp) {

			fwrite($fp, '$categories[\'' . $val['id'] .'\']=\'' . $val['name'] . '\';' . $CR);
		}
	}

	/**
	 * geoareas
	 */
	$sql = 'SELECT * '

			.'FROM `'.NP_TABLE_GEOAREAS.'` '

			.'ORDER BY name ASC '
	;
	$data = $wpdb->get_results($sql, ARRAY_A);

	$regions = array();
	foreach($data as $key => $val) {

		$regions[$val['id']] = $val['name'];

		if($fp) {

			fwrite($fp, '$regions[\'' . $val['id'] .'\']=\'' . $val['name'] . '\';' . $CR);
		}
	}

	if($fp) {

		fwrite($fp, "\n" . '?>');
		fclose($fp);
	}
}



function show_feed_registration_fields($isPlugin=true, $isSampleWidget=true, $langs, $categories, $regions, $_INPUT, $error='') {

	/**
	 * feed registration data
	 */

	?><div class="light"><?php
		#
		#	feed filter settings
		#
		if($isPlugin) {

			echo '<div style="text-align:left;margin:6px 12px;"><b>&raquo; '
			     . __('To be shown in the widget, each feed is filtered pending on the following settings', NOPROFEED_LOCALE ) . '</b></div>';

		}
		else {
			
			echo '<div style="text-align:left;margin:6px 12px;"><b>&raquo; '
				. __('To be shown your feeds will be filtered pending on the following settings', NOPROFEED_LOCALE ) . '</b></div>';
		}

		?><div class="left"><?php

//			$tmp = 'text';	#	debug
//			$tmp = 'hidden';

			if($isSampleWidget) {

				echo '<div style="margin:0 0 0 6px;">';
					require_once('sample_widget.php');
				echo '</div>';
			}

		?>
		</div>

		<div class="right"><?php
			#
			#	language
			#
			?><div class="medium" style="float:left;">
				<div class="light" style="/*float:left;*/margin-top:0;padding:12px;width:96%;">
					<p style="margin-top:0;"><?php

						if($isPlugin) {

							_e('The feed must be written in one of the selected languages:', NOPROFEED_LOCALE );
							$type = 'checkbox';
						}
						else {

							_e('In which languages your feeds are written?', NOPROFEED_LOCALE );
							$type = 'radio';
						}

						if(isset($error['nop_wid_feedFilter_lang_'])) {

							echo $error['nop_wid_feedFilter_lang_'];
						}

					?></p><?php

					$fld_value = 1;

					foreach($langs as $key => $val) {

						$checked ='';

						if($isPlugin) {

							$name = $key;
							$fld_value = 1;
							if($_INPUT['nop_wid_feedFilter_lang_'.$name] == 1) {

								$checked = 'checked="checked"';
							}
						}
						else {

							$name = '';
							if($_INPUT['nop_wid_feedFilter_lang_'.$name] == $fld_value) {

								$checked = 'checked="checked"';
							}

						}

						echo '<input type="'.$type.'" '
								.'id="nop_wid_feedFilter_lang_'.$key.'" '
								.'name="nop_wid_feedFilter_lang_'.$name.'" '
								.'value="'.$fld_value.'" '.$checked.'/>'

							.'<label '
								.'for="nop_wid_feedFilter_lang_'.$key.'">'
								.' '.$val
							.'</label>'

							.'<br />'
						;

						if(!$isPlugin) {

							$fld_value++;
						}
					}

					echo '<p style="margin:12px 0 0 0;">';

					if($isPlugin) {

						_e('If you would like to publish one or more feeds in a language that is not included in the list,', NOPROFEED_LOCALE );
					}
					else {

						_e('If your language is not included in the list,', NOPROFEED_LOCALE );
					}

					echo ' <a href="/contact/">'
							.__('please let us know', NOPROFEED_LOCALE )
					.'</a>.</p>';

				?></div>
			</div><?php
			#
			#	tags
			#
			?><div class="medium" style="float:left;">
				<div class="light" style="/*float:left;*/margin-top:0;padding:12px;width:96%;">
					<p style="margin-top:0;"><?php

						if($isPlugin) {

							_e('And must be associated by its author to the following selected categories:', NOPROFEED_LOCALE );
						}
						else {

							_e('Which categories your feeds are related to?', NOPROFEED_LOCALE );
						}

						if(isset($error['nop_wid_feedFilter_category_'])) {

							echo $error['nop_wid_feedFilter_category_'];
						}

					?></p>

					<div style="margin:12px 20px;float:right;text-align:right;">
						<input class="button-secondary" type="button"
						       onclick="select_categories('all');"
						       value="<?php _e('All', NOPROFEED_LOCALE ); ?>" />
						<input class="button-secondary" type="button"
						       onclick="select_categories('none');"
						       value="<?php _e('None', NOPROFEED_LOCALE ); ?>" />
						<div style="clear:both;"></div>
					</div>
					<div id="_np_categories_"><?php

					foreach($categories as $key => $val) {

						$checked ='';
						if($_INPUT['nop_wid_feedFilter_category_'.$key] == 1) {

							$checked ='checked="checked"';
						}

						echo '<input type="checkbox" '
								.'id="nop_wid_feedFilter_category_'.$key.'" '
								.'name="nop_wid_feedFilter_category_'.$key.'" '
								.'value="1" '.$checked.'/>'

							.'<label '
								.'for="nop_wid_feedFilter_category_'.$key.'">'
								.' '.$val
							.'</label>'

							.'<br />'
						;
					}

				?></div>
				</div>
			</div><?php
			#
			#	countries
			#
			?><div class="medium" style="float:left;">
				<div class="light" style="/*float:left;*/margin-top:0;padding:12px;width:96%;">
					<p style="margin-top:0;"><?php

						if($isPlugin) {

							_e('The feed should talk about an activity that take place in one of the following selected areas:', NOPROFEED_LOCALE );
						}
						else {

							_e('Which geographics areas are covered by your activities?', NOPROFEED_LOCALE );
						}

						if(isset($error['nop_wid_feedFilter_region_'])) {

							echo $error['nop_wid_feedFilter_region_'];
						}

					?></p>

					<div style="margin:12px 20px;float:right;text-align:right;">
						<input class="button-secondary" type="button"
						       onclick="select_geolocations('all');"
						       value="<?php _e('All', NOPROFEED_LOCALE ); ?>" />
						<input class="button-secondary" type="button"
						       onclick="select_geolocations('none');"
						       value="<?php _e('None', NOPROFEED_LOCALE ); ?>" />
						<div style="clear:both;"></div>
					</div>
					<div id="_np_geoareas_"><?php

						foreach($regions as $key => $val) {

							$checked ='';
							if($_INPUT['nop_wid_feedFilter_region_'.$key] == 1) {

								$checked ='checked="checked"';
							}

							echo '<input type="checkbox" '
									.'id="nop_wid_feedFilter_region_'.$key.'" '
									.'name="nop_wid_feedFilter_region_'.$key.'" '
									.'value="1" '.$checked.'/>'

								.'<label '
									.'for="nop_wid_feedFilter_region_'.$key.'">'
									.' '.$val
								.'</label>'

								.'<br />'
							;
						}

					?></div>
					<div style="clear:both;"></div>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div style="clear:both;"></div>
<script type="text/javascript">
function select_categories(type) {

	var items = document.getElementById('_np_categories_').childNodes;
	var i = 0, t = items.length;
	for(i=0;i<t;i++) {

		if(items[i].type=='checkbox') {

			if(type=='all') {

				items[i].checked = true;
			}
			else {

				items[i].checked = false;
			}
		}
	}
}
function select_geolocations(type) {

	var items = document.getElementById('_np_geoareas_').childNodes;
	var i = 0, t = items.length;
	for(i=0;i<t;i++) {

		if(items[i].type=='checkbox') {

			if(type=='all') {

				items[i].checked = true;
			}
			else {

				items[i].checked = false;
			}
		}
	}
}
</script></div>
	<?php

}

if(!function_exists('noprofeed_emailer')) {

	function noprofeed_emailer(
						$to,
						$subject,
						$body,
						$reply		= '',
						$cc			= '',
						$bcc		= '',
						$from		= '',
						$text_type	= 'html',
						$x_prio		= '3',
						$attach_path= '',
						$attach_file= '',
						$_CHARSET   = 'utf-8'
	) {

		/**
		 * email sender wrapper
		 */
		//define('_CR_',"\n");					#	05/05/2010
		define('_CR_',"\r\n");					#	05/05/2010
		define('_TAB_',"\t");

		$user_body = $body;

		/**
		 * Initializations
		 */
		$myHOST = str_replace('www.','',$_SERVER['HTTP_HOST']);

		if($reply=='')				{ $reply = 'noreply@'.$myHOST; }

		if($from=='')				{ $from  = 'robot#robot@'.$myHOST; }
		list($user_id, $from_id)	= explode('#', $from);
		if($user_id=='')			{ $user_id ='robot'; }
		if($from_id=='')			{ $from_id ='robot@'.$myHOST; }

		$domain = $_SERVER['SERVER_NAME'];
		$mime_boundary = '------------{'.md5(uniqid(time())).'}';

		/**
		 * Set the common headers
		 */
		$headers = 'MIME-Version: 1.0'._CR_;
		$headers .= 'Reply-To:'.$reply._CR_;

		if(is_array($cc)) {

			/**
			 * copy to
			 */
			$t = count($cc);
			$headers .= 'Cc:';
			for($i=0;$i<$t;$i++) { $headers .= $cc[$i].', '; }
			$headers = substr($headers,0,-2)._CR_;
		}
		else { if($cc) { $headers .= 'Cc:'.$cc._CR_; } }

		if(is_array($bcc)) {

			/**
			 * blind copy to
			 */
			$t = count($bcc);
			$headers .= 'Bcc:';
			for($i=0;$i<$t;$i++) { $headers .= $bcc[$i].', '; }
			$headers = substr($headers,0,-2)._CR_;
		}
		else { if($bcc) { $headers .= 'Bcc:'.$bcc._CR_; } }

		$headers .= 'User-Agent: '.$_SERVER['HTTP_USER_AGENT']._CR_;
		$headers .= 'From: '.$user_id.' <'.$from_id.'>'._CR_;
		$headers .= 'Message-ID: <'.md5(uniqid(time())).'@'.$domain.'>'._CR_;

		switch($x_prio) {

			/**
			 * priority
			 */
			case '1':	$x_prio .= ' (Highest)';	break;
			case '2':	$x_prio .= ' (High)';		break;
			case '3':	$x_prio .= ' (Normal)';		break;
			case '4':	$x_prio .= ' (Low)';		break;
			case '5':	$x_prio .= ' (Lowest)';		break;

			default:
				$x_prio = '3 (Normal)';
		}

		if($x_prio)	{ $headers .= 'X-Priority: '.$x_prio._CR_; }

		/**
		 * Message Priority for Exchange Servers
		 *
		 * $headers .=	'X-MSmail-Priority: '.$x_prio_des._CR_;
		 *
		 * !!! WARNING !!!---# Hotmail and others do NOT like PHP mailer...
		 * $headers .=	'X-Mailer: PHP/'.phpversion()._CR_;---#
		 *
		 * $headers .= 'X-Mailer: Microsoft Office Outlook, Build 11.0.6353'._CR_;
		 * $headers .= 'X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2900.2527'._CR_;
		 *
		 */
		$headers .= 'X-Sender: '.$user_id.' <'.$from_id.'>'._CR_;

		$headers .= 'X-AntiAbuse: This is a solicited email for - '.$to.' - '._CR_;
		$headers .= 'X-AntiAbuse: Servername - {'.$domain.'}'._CR_;

		$headers .= 'X-AntiAbuse: User - '.$from_id._CR_;

		/**
		 * Set the right start of header
		 */
		if($attach_path && $attach_file) {

			if(!is_array($attach_path) || !is_array($attach_file)) {

				$_attach_path = array();
				$_attach_file = array();

				$_attach_path[] = $attach_path;
				$_attach_file[] = $attach_file;
			}
			else {

				$_attach_path = $attach_path;
				$_attach_file = $attach_file;
			}

			$a = 0;
			foreach($_attach_file as $key=>$attach_file) {

				$attach_path = $_attach_path[$key];

				$file_name_type = measycom_mimetype($attach_path, $attach_file);
				$file_name_name = $attach_file;

				/**
				 * Read the file to be attached
				 */
				$data = '';
				$file = @fopen($attach_path.$attach_file,'rb');
				if($file) {

					while(!feof($file)) { $data .= @fread($file, 8192); }
					@fclose($file);
				}

				/**
				 * Base64 encode the file data
				 */
				$data = chunk_split(base64_encode($data));

				if($a==0) {											/* send the body only once */

					/**
					 * Complete headers
					 */
					$headers .= 'Content-Type: multipart/mixed;'._CR_;
					$headers .= ' boundary="'.$mime_boundary.'"'."\n\n";

					/**
					 * Add a multipart boundary above the text message
					 */
					$mail_body_attach  = 'This is a multi-part message in MIME format.'._CR_;
					$mail_body_attach .= '--'.$mime_boundary."\n";
					$mail_body_attach .= 'Content-Type: text/'.$text_type.'; charset='.$_CHARSET.';'."\n";
					$mail_body_attach .= 'Content-Transfer-Encoding: 8bit'."\n\n";
					$mail_body_attach .= $body."\n";

					$body = $mail_body_attach;
				}

				/**
				 * Add the file attachment
				 */
				$mail_file_attach = '--'.$mime_boundary."\n";
				$mail_file_attach .= 'Content-Type: '.$file_name_type.";\n";
				$mail_file_attach .= ' name="'.$file_name_name.'"'."\n";
				$mail_file_attach .= 'Content-Disposition: attachment;'."\n";
				$mail_file_attach .= ' filename="'.$file_name_name.'"'."\n";
				$mail_file_attach .= 'Content-Transfer-Encoding: base64'."\n\n";
				$mail_file_attach .= $data."\n";

				$body .= $mail_file_attach;
				$a++;
			}
		}
		else {

			if($text_type=='plain') {

				$headers .= 'Content-Type: text/'.$text_type.'; charset='.$_CHARSET.';'."\n";
				$headers .= 'Content-Transfer-Encoding: 8bit'._CR_;
			}

			if($text_type=='html') {

				$headers .= 'Content-Type: multipart/alternative;'._CR_;
				$headers .= ' boundary="'.$mime_boundary.'"'."\n\n";

				$mail_body_multipart  = 'This is a multi-part message in MIME format.'._CR_;

				/**
				 * plain version
				 */
				$inp = array();
				$out = array();

				$inp[] = '<br>';        $out = "\n";
				$inp[] = '<br />';      $out = "\n";
				$inp[] = '<hr>';        $out = "\n------------------------------------------\n";
				$inp[] = '<hr />';      $out = "\n------------------------------------------\n";

				$plain = str_replace($inp, $out, $body);
				$plain = strip_tags($plain);

				$mail_body_multipart .= '--'.$mime_boundary."\n";
				$mail_body_multipart .= 'Content-Type: text/plain; charset='.$_CHARSET."\n";
				$mail_body_multipart .= 'Content-Transfer-Encoding: 8bit'."\n\n";
				$mail_body_multipart .= $plain."\n";

				/**
				 * html version
				 */
				$mail_body_multipart .= '--'.$mime_boundary."\n";
				$mail_body_multipart .= 'Content-Type: text/html; charset='.$_CHARSET.'; format=flowed'."\n";
				$mail_body_multipart .= 'Content-Transfer-Encoding: 8bit'."\n\n";
				$mail_body_multipart .= $body."\n\n";

				$body = $mail_body_multipart."\n".'--'.$mime_boundary."--\n";
			}
		}

		#
		#	$extra_header = '-fwebmaster@{'.$domain.'}'; # this is the User of the machine or hosting account
		#
//echo 'Subject:'.$subject
//	.'<br>Reply:'.$reply
//	.'<br>cc:'.$cc
//	.'<br>To:'.$to
//	.'<br>Body:<br>'.$body
//	.'<br>From_id:'.$from_id
//	.'<br>headers:'.$headers
//	.'<br>Mail Server:'.$_SESSION['misc']['MAILSRV'].':'.$_SESSION['misc']['MAILSRVPORT']
//	.'<br>E sender:'.$_SESSION['misc']['E_SENDER']
//	;
//die();

//$tmp = false;	#debug

		$tmp = @mail($to, $subject, $body, $headers); #, $extra_header);

		if($tmp==true) {

			return '*OK*';
		}
		else {

			$html = '<hr>There has been a mail error sending to:'.$to.'<hr>';

			$html .= 'Subject:'.$subject
					.'<br>Reply:'.$reply
					.'<br>cc:'.$cc
					.'<br>Body:<br>'.$body
					.'<br>From_id:'.$from_id
//					.'<br>Mail Server:'.$_SESSION['misc']['MAILSRV'].':'.$_SESSION['misc']['MAILSRVPORT']
					.'<br>Headers:'.$headers
			;

			echo $html;
			return $html;
		}
	}
}

?>