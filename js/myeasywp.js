/**
 * myeasywp.com
 * settings
 * 17 December 2012
 */function toggleOptions(e){var t=document.getElementById(e+"-toggler"),n=document.getElementById(e+"-contents");if(t&&n)if(n.style.display=="none"){n.style.display="block";t.className="optionsGroup-toggler-close"}else{n.style.display="none";t.className="optionsGroup-toggler-open"}}jQuery&&jQuery(document).ready(function(){jQuery("#signup").submit(function(){jQuery("#mc-response").html("Adding email address...");jQuery.ajax({url:location.protocol+"//"+location.hostname+"/wp-content/plugins/"+myeasyplugin+"/inc/mc/inc/store-address.php",data:"ajax=true&email="+escape(jQuery("#email").val()),success:function(e){jQuery("#mc-response").html(e)}});return!1})});