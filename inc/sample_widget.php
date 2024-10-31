<p><?php

	echo __('Here below you can see a sample of how the widget will look like in your site.', NOPROFEED_LOCALE )
		.'</p><p>'
		. __('Feel free to experiment by changing the values in the box here on the right, when you are happy with the widget look, click on one of the two "Update Options" buttons on the bottom of this page.', NOPROFEED_LOCALE )
	;

?></p>

<div id="npf-widget-container">
	<div id="npf-wid-topShadow"></div>
	<div id="npf-widget">
		<div class="npf-wid-header">
			<a href="http://noprofeed.org"><img src="<?php echo NOPROFEED_CDN; ?>logo-widget.png" alt="noprofeed.org logo" border="0" width="100%" /></a>
		</div>

		<!-- feed 1 -->
		<div class="npf-wid-block">
			<div class="npf-wid-body">
				<span class="npf-wid-feed-title">
					<a href="#">The Title Of This News</a>
				</span><br />
<!--
				<div class="npf-wid-rating">
					<img src="<?php echo bloginfo('url'); ?>/wp-content/plugins/noprofeed/img/stars5-16x16.png" />
					<span class="npf-wid-feed-date" style="margin-left:3px;"><?php echo date('j M Y', time()); ?></span>
					<img style="vertical-align:middle;cursor:pointer;margin-left:0px;"
					    src="<?php echo NOPROFEED_CDN; ?>abuse.png" width="16"
						title="Report abuse!"
						onclick="alert('Do you want to report abuse for this news?');" />
				</div>
-->
				<p id="npf-wid-content-1" style="clear:both;margin:0;">
					<span id="npf-wid-first_sentence-1" style="display:block;">Hallo there!</span>
					<span id="npf-wid-main_content-1" style="display:block;">
						We help non-profit organizations to publish their news on an increasing number of volunteers blog/sites!
					</span>
				</p>
				<p id="npf-wid-read_more-1" style="margin:0;display:block;">
					&raquo; <a href="#">Read more</a>
				</p>
			</div>
			<div class="npf-wid-feed-info">
				<div id="npf-wid-feed-org-1" class="npf-wid-feed-org">
					<img style="vertical-align:middle;cursor:help;" width="16"
					    src="http://noprofeed.org/favicon.ico" />
					<a href="#"><strong>Non-profit Name</strong></a>
					<p style="margin:0;">City, Country</p>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div id="npf-wid-donate-feed-1" class="npf-wid-donate-feed"></div>

			<hr style="width:70%" />
		</div>

		<!-- feed 2 -->
		<div class="npf-wid-block" id="npf-wid-block-2" style="display:none;">
			<div class="npf-wid-body">
				<span class="npf-wid-feed-title">
					<a href="#">The Title Of This News</a>
				</span><br />
<!--
				<div class="npf-wid-rating">
					<img src="<?php echo bloginfo('url'); ?>/wp-content/plugins/noprofeed/img/stars5-16x16.png" />
					<span class="npf-wid-feed-date" style="margin-left:3px;"><?php echo date('j M Y', time()); ?></span>
					<img style="vertical-align:middle;cursor:pointer;margin-left:0px;"
					    src="<?php echo NOPROFEED_CDN; ?>abuse.png" width="16"
						title="Report abuse!"
						onclick="alert('Do you want to report abuse for this news?');" />
				</div>
-->
				<p id="npf-wid-content-2" style="clear:both;margin:0;">
					<span id="npf-wid-first_sentence-2" style="display:block;">Hallo there!</span>
					<span id="npf-wid-main_content-2" style="display:block;">
						We help non-profit organizations to publish their news on an increasing number of volunteers blog/sites!
					</span>
				</p>
				<p id="npf-wid-read_more-2" style="margin:0;display:block;">
					&raquo; <a href="#">Read more</a>
				</p>
			</div>
			<div class="npf-wid-feed-info">
				<div id="npf-wid-feed-org-2" class="npf-wid-feed-org">
					<img style="vertical-align:middle;cursor:help;" width="16"
					    src="http://noprofeed.org/favicon.ico" />
					<a href="#"><strong>Non-profit Name</strong></a>
					<p style="margin:0;">City, Country</p>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div id="npf-wid-donate-feed-2" class="npf-wid-donate-feed"></div>

			<hr style="width:70%" />
		</div>

		<!-- feed 3 -->
		<div class="npf-wid-block" id="npf-wid-block-3" style="display:none;">
			<div class="npf-wid-body">
				<span class="npf-wid-feed-title">
					<a href="#">The Title Of This News</a>
				</span><br />
<!--
				<div class="npf-wid-rating">
					<img src="<?php echo bloginfo('url'); ?>/wp-content/plugins/noprofeed/img/stars5-16x16.png" />
					<span class="npf-wid-feed-date" style="margin-left:3px;"><?php echo date('j M Y', time()); ?></span>
					<img style="vertical-align:middle;cursor:pointer;margin-left:0px;"
					    src="<?php echo NOPROFEED_CDN; ?>abuse.png" width="16"
						title="Report abuse!"
						onclick="alert('Do you want to report abuse for this news?');" />
				</div>
-->
				<p id="npf-wid-content-3" style="clear:both;margin:0;">
					<span id="npf-wid-first_sentence-3" style="display:block;">Hallo there!</span>
					<span id="npf-wid-main_content-3" style="display:block;">
						We help non-profit organizations to publish their news on an increasing number of volunteers blog/sites!
					</span>
				</p>
				<p id="npf-wid-read_more-3" style="margin:0;display:block;">
					&raquo; <a href="#">Read more</a>
				</p>
			</div>
			<div class="npf-wid-feed-info">
				<div id="npf-wid-feed-org-3" class="npf-wid-feed-org">
					<img style="vertical-align:middle;cursor:help;" width="16"
					    src="http://noprofeed.org/favicon.ico" />
					<a href="#"><strong>Non-profit Name</strong></a>
					<p style="margin:0;">City, Country</p>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div id="npf-wid-donate-feed-3" class="npf-wid-donate-feed"></div>

			<hr style="width:70%" />
		</div>

		<!-- feed 4 -->
		<div class="npf-wid-block" id="npf-wid-block-4" style="display:none;">
			<div class="npf-wid-body">
				<span class="npf-wid-feed-title">
					<a href="#">The Title Of This News</a>
				</span><br />
<!--
				<div class="npf-wid-rating">
					<img src="<?php echo bloginfo('url'); ?>/wp-content/plugins/noprofeed/img/stars5-16x16.png" />
					<span class="npf-wid-feed-date" style="margin-left:3px;"><?php echo date('j M Y', time()); ?></span>
					<img style="vertical-align:middle;cursor:pointer;margin-left:0px;"
					    src="<?php echo NOPROFEED_CDN; ?>abuse.png" width="16"
						title="Report abuse!"
						onclick="alert('Do you want to report abuse for this news?');" />
				</div>
-->
				<p id="npf-wid-content-4" style="clear:both;margin:0;">
					<span id="npf-wid-first_sentence-4" style="display:block;">Hallo there!</span>
					<span id="npf-wid-main_content-4" style="display:block;">
						We help non-profit organizations to publish their news on an increasing number of volunteers blog/sites!
					</span>
				</p>
				<p id="npf-wid-read_more-4" style="margin:0;display:block;">
					&raquo; <a href="#">Read more</a>
				</p>
			</div>
			<div class="npf-wid-feed-info">
				<div id="npf-wid-feed-org-4" class="npf-wid-feed-org">
					<img style="vertical-align:middle;cursor:help;" width="16"
					    src="http://noprofeed.org/favicon.ico" />
					<a href="#"><strong>Non-profit Name</strong></a>
					<p style="margin:0;">City, Country</p>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div id="npf-wid-donate-feed-4" class="npf-wid-donate-feed"></div>

			<hr style="width:70%" />
		</div>

		<!-- feed 5 -->
		<div class="npf-wid-block" id="npf-wid-block-5" style="display:none;">
			<div class="npf-wid-body">
				<span class="npf-wid-feed-title">
					<a href="#">The Title Of This News</a>
				</span><br />
<!--
				<div class="npf-wid-rating">
					<img src="<?php echo bloginfo('url'); ?>/wp-content/plugins/noprofeed/img/stars5-16x16.png" />
					<span class="npf-wid-feed-date" style="margin-left:3px;"><?php echo date('j M Y', time()); ?></span>
					<img style="vertical-align:middle;cursor:pointer;margin-left:0px;"
					    src="<?php echo NOPROFEED_CDN; ?>abuse.png" width="16"
						title="Report abuse!"
						onclick="alert('Do you want to report abuse for this news?');" />
				</div>
-->
				<p id="npf-wid-content-5" style="clear:both;margin:0;">
					<span id="npf-wid-first_sentence-5" style="display:block;">Hallo there!</span>
					<span id="npf-wid-main_content-5" style="display:block;">
						We help non-profit organizations to publish their news on an increasing number of volunteers blog/sites!
					</span>
				</p>
				<p id="npf-wid-read_more-5" style="margin:0;display:block;">
					&raquo; <a href="#">Read more</a>
				</p>
			</div>
			<div class="npf-wid-feed-info">
				<div id="npf-wid-feed-org-5" class="npf-wid-feed-org">
					<img style="vertical-align:middle;cursor:help;" width="16"
					    src="http://noprofeed.org/favicon.ico" />
					<a href="#"><strong>Non-profit Name</strong></a>
					<p style="margin:0;">City, Country</p>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div id="npf-wid-donate-feed-5" class="npf-wid-donate-feed"></div>

			<hr style="width:70%" />
		</div>

		<div style="clear:both;width:100%;text-align:center;">
			<p style="font-size:14px;margin:6px 0;">
				<a href="#">Add this to your blog!</a>
			</p>
			<div align="center">
				<div class="npf-wid-donate-us"></div>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
	<div id="npf-wid-botShadow"></div>
	<div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
<script type="text/javascript">
/**
 * the number of feeds shown in the widget
 */
var feedsShown = 6;

function npf_wid_toggle_feedItems(val) {

	val = parseInt(val);

	for(var i=2; i<6; i++) {

		if(val >= i) {

			document.getElementById('npf-wid-block-'+i).style.display = 'block';
		}
		else {

			document.getElementById('npf-wid-block-'+i).style.display = 'none';
		}
	}
}

function npf_wid_toggle_feedContent(val) {

	val = parseInt(val);

	for(var i=1; i<feedsShown; i++) {
		switch(val) {

			case 2:
				document.getElementById('npf-wid-first_sentence-'+i).style.display = 'none';
				document.getElementById('npf-wid-main_content-'+i).style.display = 'none';
				break;

			case 1:
				document.getElementById('npf-wid-first_sentence-'+i).style.display = 'block';
				document.getElementById('npf-wid-main_content-'+i).style.display = 'none';
				break;

			case 0:
				document.getElementById('npf-wid-first_sentence-'+i).style.display = 'block';
				document.getElementById('npf-wid-main_content-'+i).style.display = 'block';
				break;

		}
		document.getElementById('npf-wid-feed-org-'+i).style.display = val;
	}
}

function npf_wid_toggle_orgInfo(check) {

	if(check) { var val = 'block'; } else { var val = 'none'; }
	for(var i=1; i<feedsShown; i++) {

		document.getElementById('npf-wid-feed-org-'+i).style.display = val;
	}
}

function npf_wid_toggle_readMore(check) {

	if(check) { var val = 'block'; } else { var val = 'none'; }
	for(var i=1; i<feedsShown; i++) {

		document.getElementById('npf-wid-read_more-'+i).style.display = val;
	}
}

function npf_wid_toggle_orgDonate(check) {

	if(check) { var val = 'block'; } else { var val = 'none'; }
	for(var i=1; i<feedsShown; i++) {

		document.getElementById('npf-wid-donate-feed-'+i).style.display = val;
	}
}

function npf_wid_toggle_paper(check) {

	if(check) {

		var val = 'url(<?php echo NOPROFEED_CDN; ?>bg-pattern.png)', rpt = 'repeat';
	}
	else { var val = 'url(<?php echo NOPROFEED_CDN; ?>--none--.png)', rpt = 'no-repeat'; }

	var el = document.getElementById('npf-widget');
	el.style.backgroundImage = val;
	el.style.backgroundRepeat = rpt;
}

function npf_wid_toggle_shadows(check) {

	if(check) { var val = 'block'; } else { var val = 'none'; }

	document.getElementById('npf-wid-topShadow').style.display = val;
	document.getElementById('npf-wid-botShadow').style.display = val;
}

function npf_presetSettings(val) {

	var el;

	switch(parseInt(val)) {

		case 0:
			el = document.getElementById('nop_wid_feeds2show');
			for(var i=0; i<el.length; i++) {

				if(el[i].value == 1) {

					el[i].selected = true;
				}
				else {

					el[i].selected = false;
				}
			}
			npf_wid_toggle_feedItems(1);

			el = document.getElementById('nop_wid_feedHeight');
			for(var i=0; i<el.length; i++) {

				if(el[i].value == 2) {

					el[i].selected = true;
				}
				else {

					el[i].selected = false;
				}
			}
			npf_wid_toggle_feedContent(2);

			document.getElementById('nop_wid_feedShowOrg').checked = false;
			npf_wid_toggle_orgInfo(false);

			document.getElementById('nop_wid_feedReadMore').checked = false;
			npf_wid_toggle_readMore(false);

			document.getElementById('nop_wid_feedShowOrgDonate').checked = false;
			npf_wid_toggle_orgDonate(false);
			break;

		case 1:
			/* the number of feeds is not changed in this case */

			el = document.getElementById('nop_wid_feedHeight');
			for(var i=0; i<el.length; i++) {

				if(el[i].value == 0) {

					el[i].selected = true;
				}
				else {

					el[i].selected = false;
				}
			}
			npf_wid_toggle_feedContent(0);

			document.getElementById('nop_wid_feedReadMore').checked = true;
			npf_wid_toggle_orgInfo(true);

			document.getElementById('nop_wid_feedShowOrg').checked = true;
			npf_wid_toggle_readMore(true);

			document.getElementById('nop_wid_feedShowOrgDonate').checked = true;
			npf_wid_toggle_orgDonate(true);
			break;
	}
}

function npf_wid_setDynamicWidget(resize) {

	var container = document.getElementById('npf-widget-container');
	var el = document.getElementById('nop_wid_width');

	if(container && el) {

		var val = parseInt(el.value);
		if(isNaN(val)) { val = 0; }

		var knob = parseInt(val);
		var perc = knob * 100 / 374;
		var w = parseInt(173 + (perc * 173 /100));
		var paddedWidth = w + (w * 16 / 100); // 16 is the vertical padding

		var h = container.offsetHeight;

		if(resize == 1) {

			container.style.width = w + 'px';
		}

		document.getElementById('nop_wid_widthSizeDisplay').innerHTML = parseInt(paddedWidth) + 'px';
		document.getElementById('nop_wid_widthSizeDisplay_value').value = parseInt(paddedWidth);
		document.getElementById('nop_wid_heightSizeDisplay').innerHTML = parseInt(h) + 'px';
	}
}

function npf_wid_setColorsWidget() {

	var children = document.getElementById('npf-widget-container').getElementsByTagName('*');

	var linkCol = '#' + document.getElementById('nop_wid_feedStyle_linkscolor').value;
	var textCol = '#' + document.getElementById('nop_wid_feedStyle_textcolor').value;

	for(var i=0; i<children.length; i++) {

		if(children[i].nodeType == 1) {

			switch(children[i].nodeName.toLowerCase()) {

				case 'a':
					 // links
					children[i].style.color = linkCol;
					break;

				case 'p':
				case 'span':
					 // text
					children[i].style.color = textCol;
					break;
			}
		}
	}

	el = document.getElementById('npf-widget');
	el.style.borderColor = '#' + document.getElementById('nop_wid_feedStyle_bordcolor').value;
	el.style.backgroundColor = '#' + document.getElementById('nop_wid_feedStyle_bgcolor').value;

	npf_wid_toggle_paper(document.getElementById('nop_wid_feedStyle_paper').checked);
	npf_wid_toggle_shadows(document.getElementById('nop_wid_feedStyle_3D').checked);
}

function npf_defaultSettings() {

	window.location = '/wp-admin/options-general.php?page=noprofeed.org_settings';
}

//setInterval('npf_wid_setDynamicWidget(0)', 50);
setInterval('npf_wid_setColorsWidget()', 50);
setTimeout('npf_wid_setDynamicWidget(0)', 500);

/**
 * initialize the widget with the previously saved options
 */
npf_wid_toggle_feedItems(<?php echo $_INPUT['nop_wid_feeds2show']; ?>);
npf_wid_toggle_feedContent(<?php echo $_INPUT['nop_wid_feedHeight']; ?>);
npf_wid_toggle_readMore(<?php echo $_INPUT['nop_wid_feedReadMore']; ?>);
npf_wid_toggle_orgInfo(<?php echo $_INPUT['nop_wid_feedShowOrg']; ?>);
npf_wid_toggle_orgDonate(<?php echo $_INPUT['nop_wid_feedShowOrgDonate']; ?>);

</script>
