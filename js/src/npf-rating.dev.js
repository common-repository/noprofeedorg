/**
 * Author: Addam M. Driver
 * Date: 10/31/2006
 * http://www.reignwaterdesigns.com/ad/tidbits/rateme/
 *
 * adapted to be used at noprofeed.org
 * 2011
 */
var sMax;	// Is the maximum number of stars
var holder; // Is the holding pattern for clicked state
var preSet; // Is the PreSet value onces a selection has been made
var rated;

// Rollover for image Stars //
function npf_WidRating(num,id) {

	sMax = 0;	// Is the maximum number of stars
	for(n=0; n<num.parentNode.childNodes.length; n++) {

		if(num.parentNode.childNodes[n].nodeName == "A") {

			sMax++;
		}
	}

	if(!rated) {

		s = num.id.replace('widrate'+id+"_", ''); // Get the selected star
		a = 0;
		for(i=1; i<=sMax; i++) {

			if(i<=s) {

				document.getElementById('widrate'+id+"_"+i).className = "on";
				document.getElementById("npf-wid-rateStatus-"+id).innerHTML = num.title;
				holder = a+1;
				a++;
			}
			else {

				document.getElementById('widrate'+id+"_"+i).className = "";
			}
		}
	}
}

// For when you roll out of the the whole thing //
function npf_WidRatingOff(me,id) {

	if(!rated) {

		if(!preSet) {

			for(i=1; i<=sMax; i++) {

				document.getElementById('widrate'+id+"_"+i).className = "";
				document.getElementById("npf-wid-rateStatus-"+id).innerHTML = me.parentNode.title;
			}
		}
		else {

			npf_WidRating(preSet);
			document.getElementById("npf-wid-rateStatus-"+id).innerHTML = document.getElementById("npf-wid-ratingSaved-"+id).innerHTML;
		}
	}
}

// When you actually rate something //
function npf_WidRate(me,id) {

	if(!rated) {

		document.getElementById("npf-wid-rateStatus-"+id).innerHTML = document.getElementById("npf-wid-ratingSaved-"+id).innerHTML + " :: "+me.title;
		preSet = me;
		rated=1;
		npf_WidRatingSendRate(me,id);
		npf_WidRating(me,id);
	}
}

// Send the rating information somewhere using Ajax or something like that.
function npf_WidRatingSendRate(sel) {

	alert("Your rating was: "+sel.title+'\n\nAJAX code to be implemented!');
}

// noprofeed.org: Show the actual status of the rating //
function npf_setActualRating(rating,id) {

	for(var i=1;i<=rating;i++) {

		npf_WidRating(document.getElementById('widrate'+id+'_'+i),id);
	}
}
