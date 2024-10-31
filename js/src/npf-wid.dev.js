/**
 * Author: noprofeed.org
 * Date: 1 July 2011
 */
addLoadListener(initSliders);

function initSliders()
{
	var sliderReplacements = getElementsByAttribute("class", "slider");

	for (var i = 0; i < sliderReplacements.length; i++)
	{
		var container = document.createElement("div");
		var slider = document.createElement("div");
		var newInput = document.createElement("input");
		var sliderReplacementID = sliderReplacements[i].getAttribute("id");

		if (sliderReplacementID != null || sliderReplacementID != "")
		{
			container.setAttribute("id", sliderReplacementID + "SliderContainer");
		}

		container.className = "sliderContainer";
		slider.className = "sliderWidget";
		slider.style.left = sliderReplacements[i].getAttribute("value") + "px";
		slider.valueX = parseInt(sliderReplacements[i].getAttribute("value"), 10);

		try
		{
			newInput.setAttribute("id", sliderReplacements[i].getAttribute("id"));
			newInput.setAttribute("name", sliderReplacements[i].getAttribute("name"));
			newInput.setAttribute("type", "hidden");
			newInput.setAttribute("value", sliderReplacements[i].getAttribute("value"));
		}
		catch(error)
		{
			return false;
		}

		container.appendChild(slider);
		sliderReplacements[i].parentNode.insertBefore(container, sliderReplacements[i]);
		sliderReplacements[i].parentNode.replaceChild(newInput, sliderReplacements[i]);

		container.input = newInput;

		attachEventListener(slider, "mousedown", mousedownSlider, false);
	}

	var el = document.getElementById('nop_slider_wait');
	if(el) {

		el.style.display = 'none';
	}

	return true;
}

function mousedownSlider(event)
{
	if (typeof event == "undefined")
	{
		event = window.event;
	}

	var target = getEventTarget(event);

	while (!/(^| )sliderWidget( |$)/.test(target.className))
	{
		target = target.parentNode;
	}

	document.currentSlider = target;
	target.originX = event.clientX;

	attachEventListener(document, "mousemove", mousemoveSlider, false);
	attachEventListener(document, "mouseup", mouseupSlider, false);

	stopDefaultAction(event);

	return true;
}

function mousemoveSlider(event)
{
	if (typeof event == "undefined")
	{
		event = window.event;
	}

	var slider = document.currentSlider;
	var sliderLeft = slider.valueX;
	var increment = 1;

	if (isNaN(sliderLeft))
	{
		sliderLeft = 0;
	}

	sliderLeft += event.clientX - slider.originX;

	if (sliderLeft < 0)
	{
		sliderLeft = 0;
	}
	else if (sliderLeft > (slider.parentNode.offsetWidth - slider.offsetWidth))
	{
		sliderLeft = slider.parentNode.offsetWidth - slider.offsetWidth;
	}
	else
	{
		slider.originX = event.clientX;
	}

	slider.style.left = Math.round(sliderLeft / increment) * increment + "px";
	slider.parentNode.input.setAttribute("value", Math.round(sliderLeft / increment) * increment);
	slider.valueX = sliderLeft;

	stopDefaultAction(event);

	npf_wid_setDynamicWidget(1);

	return true;
}

function mouseupSlider()
{
	detachEventListener(document, "mousemove", mousemoveSlider, false);
	detachEventListener(document, "mouseup", mouseupSlider, false);

	return true;
}

function addLoadListener(fn)
{
	if (typeof window.addEventListener != 'undefined')
	{
		window.addEventListener('load', fn, false);
	}
	else if (typeof document.addEventListener != 'undefined')
	{
		document.addEventListener('load', fn, false);
	}
	else if (typeof window.attachEvent != 'undefined')
	{
		window.attachEvent('onload', fn);
	}
	else
	{
		var oldfn = window.onload;
		if (typeof window.onload != 'function')
		{
			window.onload = fn;
		}
		else
		{
			window.onload = function()
			{
				oldfn();
				fn();
			};
		}
	}
}

function attachEventListener(target, eventType, functionRef, capture)
{
	if (typeof target.addEventListener != "undefined")
	{
		target.addEventListener(eventType, functionRef, capture);
	}
	else if (typeof target.attachEvent != "undefined")
	{
		target.attachEvent("on" + eventType, functionRef);
	}
	else
	{
		eventType = "on" + eventType;

		if (typeof target[eventType] == "function")
		{
			var oldListener = target[eventType];

			target[eventType] = function()
			{
				oldListener();

				return functionRef();
			}
		}
		else
		{
			target[eventType] = functionRef;
		}
	}

	return true;
}

function detachEventListener(target, eventType, functionRef, capture)
{
	if (typeof target.removeEventListener != "undefined")
	{
		target.removeEventListener(eventType, functionRef, capture)
	}
	else if (typeof target.detachEvent != "undefined")
	{
		target.detachEvent("on" + eventType, functionRef);
	}
	else
	{
		target["on" + eventType] = null;
	}

	return true;
}

function getEventTarget(event)
{
	var targetElement = null;

	if (typeof event.target != "undefined")
	{
		targetElement = event.target;
	}
	else
	{
		targetElement = event.srcElement;
	}

	while (targetElement.nodeType == 3 && targetElement.parentNode != null)
	{
		targetElement = targetElement.parentNode;
	}

	return targetElement;
}

function stopDefaultAction(event)
{
	event.returnValue = false;

	if (typeof event.preventDefault != "undefined")
	{
		event.preventDefault();
	}

	return true;
}

function getElementsByAttribute(attribute, attributeValue)
{
	var elementArray = new Array();
	var matchedArray = new Array();

	if (document.all)
	{
		elementArray = document.all;
	}
	else
	{
		elementArray = document.getElementsByTagName("*");
	}

	for (var i = 0; i < elementArray.length; i++)
	{
		if (attribute == "class")
		{
			var pattern = new RegExp("(^| )" + attributeValue + "( |$)");

			if (elementArray[i].className.match(pattern))
			{
				matchedArray[matchedArray.length] = elementArray[i];
			}
		}
		else if (attribute == "for")
		{
			if (elementArray[i].getAttribute("htmlFor") || elementArray[i].getAttribute("for"))
			{
				if (elementArray[i].htmlFor == attributeValue)
				{
					matchedArray[matchedArray.length] = elementArray[i];
				}
			}
		}
		else if (elementArray[i].getAttribute(attribute) == attributeValue)
		{
			matchedArray[matchedArray.length] = elementArray[i];
		}
	}

	return matchedArray;
}

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

/**
 * jscolor, JavaScript Color Picker
 *
 * @version 1.3.1
 * @license GNU Lesser General Public License, http://www.gnu.org/copyleft/lesser.html
 * @author  Jan Odvarko, http://odvarko.cz
 * @created 2008-06-15
 * @updated 2010-01-23
 * @link    http://jscolorNPF.com
 */

var jscolorNPF = {


	dir : '', // location of jscolor directory (leave empty to autodetect)
	bindClass : 'nop-wid-color', // class name
	binding : true, // automatic binding via <input class="...">
	preloading : true, // use image preloading?


	install : function() {
		jscolorNPF.addEvent(window, 'load', jscolorNPF.init);
	},


	init : function() {
		if(jscolorNPF.binding) {
			jscolorNPF.bind();
		}
		if(jscolorNPF.preloading) {
			jscolorNPF.preload();
		}
	},


	getDir : function() {
		if(!jscolorNPF.dir) {
			var detected = jscolorNPF.detectDir();
			/* camaleo: beg */
			//jscolorNPF.dir = detected!==false ? detected : 'jscolor/';
			jscolorNPF.dir = detected!==false ? detected : 'http://myeasywp.com/service/meh-img/';
			/* camaleo: end */
		}

		return jscolorNPF.dir;
	},


	detectDir : function() {
		var base = location.href;

		var e = document.getElementsByTagName('base');
		for(var i=0; i<e.length; i+=1) {
			if(e[i].href) { base = e[i].href; }
		}

		var e = document.getElementsByTagName('script');
		for(var i=0; i<e.length; i+=1) {
			if(e[i].src && /(^|\/)jscolor\.js([?#].*)?$/i.test(e[i].src)) {
				var src = new jscolorNPF.URI(e[i].src);
				var srcAbs = src.toAbsolute(base);
				srcAbs.path = srcAbs.path.replace(/[^\/]+$/, ''); // remove filename
				srcAbs.query = null;
				srcAbs.fragment = null;
				return srcAbs.toString();
			}
		}
		return false;
	},


	bind : function() {
		var matchClass = new RegExp('(^|\\s)('+jscolorNPF.bindClass+')\\s*(\\{[^}]*\\})?', 'i');
		var e = document.getElementsByTagName('input');
		for(var i=0; i<e.length; i+=1) {
			var m;
			if(!e[i].color && e[i].className && (m = e[i].className.match(matchClass))) {
				var prop = {};
				if(m[3]) {
					try {
						eval('prop='+m[3]);
					} catch(eInvalidProp) {}
				}
				e[i].color = new jscolorNPF.color(e[i], prop);
			}
		}
	},


	preload : function() {
		for(var fn in jscolorNPF.imgRequire) {
			if(jscolorNPF.imgRequire.hasOwnProperty(fn)) {
				jscolorNPF.loadImage(fn);
			}
		}
	},


	images : {
		pad : [ 181, 101 ],
		sld : [ 16, 101 ],
		cross : [ 15, 15 ],
		arrow : [ 7, 11 ]
	},


	imgRequire : {},
	imgLoaded : {},


	requireImage : function(filename) {
		jscolorNPF.imgRequire[filename] = true;
	},


	loadImage : function(filename) {
		if(!jscolorNPF.imgLoaded[filename]) {
			jscolorNPF.imgLoaded[filename] = new Image();
			jscolorNPF.imgLoaded[filename].src = jscolorNPF.getDir()+filename;
		}
	},


	fetchElement : function(mixed) {
		return typeof mixed === 'string' ? document.getElementById(mixed) : mixed;
	},


	addEvent : function(el, evnt, func) {
		if(el.addEventListener) {
			el.addEventListener(evnt, func, false);
		} else if(el.attachEvent) {
			el.attachEvent('on'+evnt, func);
		}
	},


	fireEvent : function(el, evnt) {
		if(!el) {
			return;
		}
		if(document.createEventObject) {
			var ev = document.createEventObject();
			el.fireEvent('on'+evnt, ev);
		} else if(document.createEvent) {
			var ev = document.createEvent('HTMLEvents');
			ev.initEvent(evnt, true, true);
			el.dispatchEvent(ev);
		} else if(el['on'+evnt]) { // alternatively use the traditional event model (IE5)
			el['on'+evnt]();
		}
	},


	getElementPos : function(e) {
		var e1=e, e2=e;
		var x=0, y=0;
		if(e1.offsetParent) {
			do {
				x += e1.offsetLeft;
				y += e1.offsetTop;
			} while(e1 = e1.offsetParent);
		}
		while((e2 = e2.parentNode) && e2.nodeName.toUpperCase() !== 'BODY') {
			x -= e2.scrollLeft;
			y -= e2.scrollTop;
		}
		return [x, y];
	},


	getElementSize : function(e) {
		return [e.offsetWidth, e.offsetHeight];
	},


	getMousePos : function(e) {
		if(!e) { e = window.event; }
		if(typeof e.pageX === 'number') {
			return [e.pageX, e.pageY];
		} else if(typeof e.clientX === 'number') {
			return [
				e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft,
				e.clientY + document.body.scrollTop + document.documentElement.scrollTop
			];
		}
	},


	getViewPos : function() {
		if(typeof window.pageYOffset === 'number') {
			return [window.pageXOffset, window.pageYOffset];
		} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
			return [document.body.scrollLeft, document.body.scrollTop];
		} else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
			return [document.documentElement.scrollLeft, document.documentElement.scrollTop];
		} else {
			return [0, 0];
		}
	},


	getViewSize : function() {
		if(typeof window.innerWidth === 'number') {
			return [window.innerWidth, window.innerHeight];
		} else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
			return [document.body.clientWidth, document.body.clientHeight];
		} else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
			return [document.documentElement.clientWidth, document.documentElement.clientHeight];
		} else {
			return [0, 0];
		}
	},


	URI : function(uri) { // See RFC3986

		this.scheme = null;
		this.authority = null;
		this.path = '';
		this.query = null;
		this.fragment = null;

		this.parse = function(uri) {
			var m = uri.match(/^(([A-Za-z][0-9A-Za-z+.-]*)(:))?((\/\/)([^\/?#]*))?([^?#]*)((\?)([^#]*))?((#)(.*))?/);
			this.scheme = m[3] ? m[2] : null;
			this.authority = m[5] ? m[6] : null;
			this.path = m[7];
			this.query = m[9] ? m[10] : null;
			this.fragment = m[12] ? m[13] : null;
			return this;
		};

		this.toString = function() {
			var result = '';
			if(this.scheme !== null) { result = result + this.scheme + ':'; }
			if(this.authority !== null) { result = result + '//' + this.authority; }
			if(this.path !== null) { result = result + this.path; }
			if(this.query !== null) { result = result + '?' + this.query; }
			if(this.fragment !== null) { result = result + '#' + this.fragment; }
			return result;
		};

		this.toAbsolute = function(base) {
			var base = new jscolorNPF.URI(base);
			var r = this;
			var t = new jscolorNPF.URI;

			if(base.scheme === null) { return false; }

			if(r.scheme !== null && r.scheme.toLowerCase() === base.scheme.toLowerCase()) {
				r.scheme = null;
			}

			if(r.scheme !== null) {
				t.scheme = r.scheme;
				t.authority = r.authority;
				t.path = removeDotSegments(r.path);
				t.query = r.query;
			} else {
				if(r.authority !== null) {
					t.authority = r.authority;
					t.path = removeDotSegments(r.path);
					t.query = r.query;
				} else {
					if(r.path === '') { // TODO: == or === ?
						t.path = base.path;
						if(r.query !== null) {
							t.query = r.query;
						} else {
							t.query = base.query;
						}
					} else {
						if(r.path.substr(0,1) === '/') {
							t.path = removeDotSegments(r.path);
						} else {
							if(base.authority !== null && base.path === '') { // TODO: == or === ?
								t.path = '/'+r.path;
							} else {
								t.path = base.path.replace(/[^\/]+$/,'')+r.path;
							}
							t.path = removeDotSegments(t.path);
						}
						t.query = r.query;
					}
					t.authority = base.authority;
				}
				t.scheme = base.scheme;
			}
			t.fragment = r.fragment;

			return t;
		};

		function removeDotSegments(path) {
			var out = '';
			while(path) {
				if(path.substr(0,3)==='../' || path.substr(0,2)==='./') {
					path = path.replace(/^\.+/,'').substr(1);
				} else if(path.substr(0,3)==='/./' || path==='/.') {
					path = '/'+path.substr(3);
				} else if(path.substr(0,4)==='/../' || path==='/..') {
					path = '/'+path.substr(4);
					out = out.replace(/\/?[^\/]*$/, '');
				} else if(path==='.' || path==='..') {
					path = '';
				} else {
					var rm = path.match(/^\/?[^\/]*/)[0];
					path = path.substr(rm.length);
					out = out + rm;
				}
			}
			return out;
		}

		if(uri) {
			this.parse(uri);
		}

	},


	/*
	 * Usage example:
	 * var myColor = new jscolorNPF.color(myInputElement)
	 */

	color : function(target, prop) {


		this.required = true; // refuse empty values?
		this.adjust = true; // adjust value to uniform notation?
		this.hash = false; // prefix color with # symbol?
		this.caps = true; // uppercase?
		this.valueElement = target; // value holder
		this.styleElement = target; // where to reflect current color
		this.hsv = [0, 0, 1]; // read-only  0-6, 0-1, 0-1
		this.rgb = [1, 1, 1]; // read-only  0-1, 0-1, 0-1

		this.pickerOnfocus = true; // display picker on focus?
		this.pickerMode = 'HSV'; // HSV | HVS
		this.pickerPosition = 'bottom'; // left | right | top | bottom
		this.pickerFace = 10; // px
		this.pickerFaceColor = 'ThreeDFace'; // CSS color
		this.pickerBorder = 1; // px
		this.pickerBorderColor = 'ThreeDHighlight ThreeDShadow ThreeDShadow ThreeDHighlight'; // CSS color
		this.pickerInset = 1; // px
		this.pickerInsetColor = 'ThreeDShadow ThreeDHighlight ThreeDHighlight ThreeDShadow'; // CSS color
		this.pickerZIndex = 10000;


		for(var p in prop) {
			if(prop.hasOwnProperty(p)) {
				this[p] = prop[p];
			}
		}


		this.hidePicker = function() {
			if(isPickerOwner()) {
				removePicker();
			}
		};


		this.showPicker = function() {
			if(!isPickerOwner()) {
				var tp = jscolorNPF.getElementPos(target); // target pos
				var ts = jscolorNPF.getElementSize(target); // target size
				var vp = jscolorNPF.getViewPos(); // view pos
				var vs = jscolorNPF.getViewSize(); // view size
				var ps = [ // picker size
					2*this.pickerBorder + 4*this.pickerInset + 2*this.pickerFace + jscolorNPF.images.pad[0] + 2*jscolorNPF.images.arrow[0] + jscolorNPF.images.sld[0],
					2*this.pickerBorder + 2*this.pickerInset + 2*this.pickerFace + jscolorNPF.images.pad[1]
				];
				var a, b, c;
				switch(this.pickerPosition.toLowerCase()) {
					case 'left': a=1; b=0; c=-1; break;
					case 'right':a=1; b=0; c=1; break;
					case 'top':  a=0; b=1; c=-1; break;
					default:     a=0; b=1; c=1; break;
				}
				var l = (ts[b]+ps[b])/2;
				var pp = [ // picker pos
					-vp[a]+tp[a]+ps[a] > vs[a] ?
							(-vp[a]+tp[a]+ts[a]/2 > vs[a]/2 && tp[a]+ts[a]-ps[a] >= 0 ? tp[a]+ts[a]-ps[a] : tp[a]) :
							tp[a],
					-vp[b]+tp[b]+ts[b]+ps[b]-l+l*c > vs[b] ?
							(-vp[b]+tp[b]+ts[b]/2 > vs[b]/2 && tp[b]+ts[b]-l-l*c >= 0 ? tp[b]+ts[b]-l-l*c : tp[b]+ts[b]-l+l*c) :
							(tp[b]+ts[b]-l+l*c >= 0 ? tp[b]+ts[b]-l+l*c : tp[b]+ts[b]-l-l*c)
				];
				drawPicker(pp[a], pp[b]);
			}
		};


		this.importColor = function() {
			if(!valueElement) {
				this.exportColor();
			} else {
				if(!this.adjust) {
					if(!this.fromString(valueElement.value, leaveValue)) {
						styleElement.style.backgroundColor = styleElement.jscStyle.backgroundColor;
						styleElement.style.color = styleElement.jscStyle.color;
						this.exportColor(leaveValue | leaveStyle);
					}
				} else if(!this.required && /^\s*$/.test(valueElement.value)) {
					valueElement.value = '';
					styleElement.style.backgroundColor = styleElement.jscStyle.backgroundColor;
					styleElement.style.color = styleElement.jscStyle.color;
					this.exportColor(leaveValue | leaveStyle);

				} else if(this.fromString(valueElement.value)) {
					// OK
				} else {
					this.exportColor();
				}
			}
		};


		this.exportColor = function(flags) {
			if(!(flags & leaveValue) && valueElement) {
				var value = this.toString();
				if(this.caps) { value = value.toUpperCase(); }
				if(this.hash) { value = '#'+value; }
				valueElement.value = value;
			}
			if(!(flags & leaveStyle) && styleElement) {
				styleElement.style.backgroundColor =
						'#'+this.toString();
				styleElement.style.color =
						0.213 * this.rgb[0] +
								0.715 * this.rgb[1] +
								0.072 * this.rgb[2]
								< 0.5 ? '#FFF' : '#000';
			}
			if(!(flags & leavePad) && isPickerOwner()) {
				redrawPad();
			}
			if(!(flags & leaveSld) && isPickerOwner()) {
				redrawSld();
			}
		};


		this.fromHSV = function(h, s, v, flags) { // null = don't change
			h<0 && (h=0) || h>6 && (h=6);
			s<0 && (s=0) || s>1 && (s=1);
			v<0 && (v=0) || v>1 && (v=1);
			this.rgb = HSV_RGB(
					h===null ? this.hsv[0] : (this.hsv[0]=h),
					s===null ? this.hsv[1] : (this.hsv[1]=s),
					v===null ? this.hsv[2] : (this.hsv[2]=v)
					);
			this.exportColor(flags);
		};


		this.fromRGB = function(r, g, b, flags) { // null = don't change
			r<0 && (r=0) || r>1 && (r=1);
			g<0 && (g=0) || g>1 && (g=1);
			b<0 && (b=0) || b>1 && (b=1);
			var hsv = RGB_HSV(
					r===null ? this.rgb[0] : (this.rgb[0]=r),
					g===null ? this.rgb[1] : (this.rgb[1]=g),
					b===null ? this.rgb[2] : (this.rgb[2]=b)
					);
			if(hsv[0] !== null) {
				this.hsv[0] = hsv[0];
			}
			if(hsv[2] !== 0) {
				this.hsv[1] = hsv[1];
			}
			this.hsv[2] = hsv[2];
			this.exportColor(flags);
		};


		this.fromString = function(hex, flags) {
			var m = hex.match(/^\W*([0-9A-F]{3}([0-9A-F]{3})?)\W*$/i);
			if(!m) {
				return false;
			} else {
				if(m[1].length === 6) { // 6-char notation
					this.fromRGB(
							parseInt(m[1].substr(0,2),16) / 255,
							parseInt(m[1].substr(2,2),16) / 255,
							parseInt(m[1].substr(4,2),16) / 255,
							flags
							);
				} else { // 3-char notation
					this.fromRGB(
							parseInt(m[1].charAt(0)+m[1].charAt(0),16) / 255,
							parseInt(m[1].charAt(1)+m[1].charAt(1),16) / 255,
							parseInt(m[1].charAt(2)+m[1].charAt(2),16) / 255,
							flags
							);
				}
				return true;
			}
		};


		this.toString = function() {
			return (
					(0x100 | Math.round(255*this.rgb[0])).toString(16).substr(1) +
							(0x100 | Math.round(255*this.rgb[1])).toString(16).substr(1) +
							(0x100 | Math.round(255*this.rgb[2])).toString(16).substr(1)
					);
		};


		function RGB_HSV(r, g, b) {
			var n = Math.min(Math.min(r,g),b);
			var v = Math.max(Math.max(r,g),b);
			var m = v - n;
			if(m === 0) { return [ null, 0, v ]; }
			var h = r===n ? 3+(b-g)/m : (g===n ? 5+(r-b)/m : 1+(g-r)/m);
			return [ h===6?0:h, m/v, v ];
		}


		function HSV_RGB(h, s, v) {
			if(h === null) { return [ v, v, v ]; }
			var i = Math.floor(h);
			var f = i%2 ? h-i : 1-(h-i);
			var m = v * (1 - s);
			var n = v * (1 - s*f);
			switch(i) {
				case 6:
				case 0: return [v,n,m];
				case 1: return [n,v,m];
				case 2: return [m,v,n];
				case 3: return [m,n,v];
				case 4: return [n,m,v];
				case 5: return [v,m,n];
			}
		}


		function removePicker() {
			delete jscolorNPF.picker.owner;
			document.getElementsByTagName('body')[0].removeChild(jscolorNPF.picker.boxB);
		}


		function drawPicker(x, y) {
			if(!jscolorNPF.picker) {
				jscolorNPF.picker = {
					box : document.createElement('div'),
					boxB : document.createElement('div'),
					pad : document.createElement('div'),
					padB : document.createElement('div'),
					padM : document.createElement('div'),
					sld : document.createElement('div'),
					sldB : document.createElement('div'),
					sldM : document.createElement('div')
				};
				for(var i=0,segSize=4; i<jscolorNPF.images.sld[1]; i+=segSize) {
					var seg = document.createElement('div');
					seg.style.height = segSize+'px';
					seg.style.fontSize = '1px';
					seg.style.lineHeight = '0';
					jscolorNPF.picker.sld.appendChild(seg);
				}
				jscolorNPF.picker.sldB.appendChild(jscolorNPF.picker.sld);
				jscolorNPF.picker.box.appendChild(jscolorNPF.picker.sldB);
				jscolorNPF.picker.box.appendChild(jscolorNPF.picker.sldM);
				jscolorNPF.picker.padB.appendChild(jscolorNPF.picker.pad);
				jscolorNPF.picker.box.appendChild(jscolorNPF.picker.padB);
				jscolorNPF.picker.box.appendChild(jscolorNPF.picker.padM);
				jscolorNPF.picker.boxB.appendChild(jscolorNPF.picker.box);
			}

			var p = jscolorNPF.picker;

			// recompute controls positions
			posPad = [
				x+THIS.pickerBorder+THIS.pickerFace+THIS.pickerInset,
				y+THIS.pickerBorder+THIS.pickerFace+THIS.pickerInset ];
			posSld = [
				null,
				y+THIS.pickerBorder+THIS.pickerFace+THIS.pickerInset ];

			// controls interaction
			p.box.onmouseup =
					p.box.onmouseout = function() { target.focus(); };
			p.box.onmousedown = function() { abortBlur=true; };
			p.box.onmousemove = function(e) { holdPad && setPad(e); holdSld && setSld(e); };
			p.padM.onmouseup =
					p.padM.onmouseout = function() { if(holdPad) { holdPad=false; jscolorNPF.fireEvent(valueElement,'change'); } };
			p.padM.onmousedown = function(e) { holdPad=true; setPad(e); };
			p.sldM.onmouseup =
					p.sldM.onmouseout = function() { if(holdSld) { holdSld=false; jscolorNPF.fireEvent(valueElement,'change'); } };
			p.sldM.onmousedown = function(e) { holdSld=true; setSld(e); };

			// picker
			p.box.style.width = 4*THIS.pickerInset + 2*THIS.pickerFace + jscolorNPF.images.pad[0] + 2*jscolorNPF.images.arrow[0] + jscolorNPF.images.sld[0] + 'px';
			p.box.style.height = 2*THIS.pickerInset + 2*THIS.pickerFace + jscolorNPF.images.pad[1] + 'px';

			// picker border
			p.boxB.style.position = 'absolute';
			p.boxB.style.clear = 'both';
			p.boxB.style.left = x+'px';
			p.boxB.style.top = y+'px';
			p.boxB.style.zIndex = THIS.pickerZIndex;
			p.boxB.style.border = THIS.pickerBorder+'px solid';
			p.boxB.style.borderColor = THIS.pickerBorderColor;
			p.boxB.style.background = THIS.pickerFaceColor;

			// pad image
			p.pad.style.width = jscolorNPF.images.pad[0]+'px';
			p.pad.style.height = jscolorNPF.images.pad[1]+'px';

			// pad border
			p.padB.style.position = 'absolute';
			p.padB.style.left = THIS.pickerFace+'px';
			p.padB.style.top = THIS.pickerFace+'px';
			p.padB.style.border = THIS.pickerInset+'px solid';
			p.padB.style.borderColor = THIS.pickerInsetColor;

			// pad mouse area
			p.padM.style.position = 'absolute';
			p.padM.style.left = '0';
			p.padM.style.top = '0';
			p.padM.style.width = THIS.pickerFace + 2*THIS.pickerInset + jscolorNPF.images.pad[0] + jscolorNPF.images.arrow[0] + 'px';
			p.padM.style.height = p.box.style.height;
			p.padM.style.cursor = 'crosshair';

			// slider image
			p.sld.style.overflow = 'hidden';
			p.sld.style.width = jscolorNPF.images.sld[0]+'px';
			p.sld.style.height = jscolorNPF.images.sld[1]+'px';

			// slider border
			p.sldB.style.position = 'absolute';
			p.sldB.style.right = THIS.pickerFace+'px';
			p.sldB.style.top = THIS.pickerFace+'px';
			p.sldB.style.border = THIS.pickerInset+'px solid';
			p.sldB.style.borderColor = THIS.pickerInsetColor;

			// slider mouse area
			p.sldM.style.position = 'absolute';
			p.sldM.style.right = '0';
			p.sldM.style.top = '0';
			p.sldM.style.width = jscolorNPF.images.sld[0] + jscolorNPF.images.arrow[0] + THIS.pickerFace + 2*THIS.pickerInset + 'px';
			p.sldM.style.height = p.box.style.height;
			try {
				p.sldM.style.cursor = 'pointer';
			} catch(eOldIE) {
				p.sldM.style.cursor = 'hand';
			}

			// load images in optimal order
			switch(modeID) {
				case 0: var padImg = 'hs.png'; break;
				case 1: var padImg = 'hv.png'; break;
			}
			p.padM.style.background = "url('"+jscolorNPF.getDir()+"cross.gif') no-repeat";
			p.sldM.style.background = "url('"+jscolorNPF.getDir()+"arrow.gif') no-repeat";
			p.pad.style.background = "url('"+jscolorNPF.getDir()+padImg+"') 0 0 no-repeat";

			// place pointers
			redrawPad();
			redrawSld();

			jscolorNPF.picker.owner = THIS;
			document.getElementsByTagName('body')[0].appendChild(p.boxB);
		}


		function redrawPad() {
			// redraw the pad pointer
			switch(modeID) {
				case 0: var yComponent = 1; break;
				case 1: var yComponent = 2; break;
			}
			var x = Math.round((THIS.hsv[0]/6) * (jscolorNPF.images.pad[0]-1));
			var y = Math.round((1-THIS.hsv[yComponent]) * (jscolorNPF.images.pad[1]-1));
			jscolorNPF.picker.padM.style.backgroundPosition =
					(THIS.pickerFace+THIS.pickerInset+x - Math.floor(jscolorNPF.images.cross[0]/2)) + 'px ' +
							(THIS.pickerFace+THIS.pickerInset+y - Math.floor(jscolorNPF.images.cross[1]/2)) + 'px';

			// redraw the slider image
			var seg = jscolorNPF.picker.sld.childNodes;

			switch(modeID) {
				case 0:
					var rgb = HSV_RGB(THIS.hsv[0], THIS.hsv[1], 1);
					for(var i=0; i<seg.length; i+=1) {
						seg[i].style.backgroundColor = 'rgb('+
								(rgb[0]*(1-i/seg.length)*100)+'%,'+
								(rgb[1]*(1-i/seg.length)*100)+'%,'+
								(rgb[2]*(1-i/seg.length)*100)+'%)';
					}
					break;
				case 1:
					var rgb, s, c = [ THIS.hsv[2], 0, 0 ];
					var i = Math.floor(THIS.hsv[0]);
					var f = i%2 ? THIS.hsv[0]-i : 1-(THIS.hsv[0]-i);
					switch(i) {
						case 6:
						case 0: rgb=[0,1,2]; break;
						case 1: rgb=[1,0,2]; break;
						case 2: rgb=[2,0,1]; break;
						case 3: rgb=[2,1,0]; break;
						case 4: rgb=[1,2,0]; break;
						case 5: rgb=[0,2,1]; break;
					}
					for(var i=0; i<seg.length; i+=1) {
						s = 1 - 1/(seg.length-1)*i;
						c[1] = c[0] * (1 - s*f);
						c[2] = c[0] * (1 - s);
						seg[i].style.backgroundColor = 'rgb('+
								(c[rgb[0]]*100)+'%,'+
								(c[rgb[1]]*100)+'%,'+
								(c[rgb[2]]*100)+'%)';
					}
					break;
			}
		}


		function redrawSld() {
			// redraw the slider pointer
			switch(modeID) {
				case 0: var yComponent = 2; break;
				case 1: var yComponent = 1; break;
			}
			var y = Math.round((1-THIS.hsv[yComponent]) * (jscolorNPF.images.sld[1]-1));
			jscolorNPF.picker.sldM.style.backgroundPosition =
					'0 ' + (THIS.pickerFace+THIS.pickerInset+y - Math.floor(jscolorNPF.images.arrow[1]/2)) + 'px';
		}


		function isPickerOwner() {
			return jscolorNPF.picker && jscolorNPF.picker.owner === THIS;
		}


		function blurTarget() {
			if(valueElement === target) {
				THIS.importColor();
			}
			if(THIS.pickerOnfocus) {
				THIS.hidePicker();
			}
		}


		function blurValue() {
			if(valueElement !== target) {
				THIS.importColor();
			}
		}


		function setPad(e) {
			var posM = jscolorNPF.getMousePos(e);
			var x = posM[0]-posPad[0];
			var y = posM[1]-posPad[1];
			switch(modeID) {
				case 0: THIS.fromHSV(x*(6/(jscolorNPF.images.pad[0]-1)), 1 - y/(jscolorNPF.images.pad[1]-1), null, leaveSld); break;
				case 1: THIS.fromHSV(x*(6/(jscolorNPF.images.pad[0]-1)), null, 1 - y/(jscolorNPF.images.pad[1]-1), leaveSld); break;
			}
		}


		function setSld(e) {
			var posM = jscolorNPF.getMousePos(e);
			var y = posM[1]-posPad[1];
			switch(modeID) {
				case 0: THIS.fromHSV(null, null, 1 - y/(jscolorNPF.images.sld[1]-1), leavePad); break;
				case 1: THIS.fromHSV(null, 1 - y/(jscolorNPF.images.sld[1]-1), null, leavePad); break;
			}
		}


		var THIS = this;
		var modeID = this.pickerMode.toLowerCase()==='hvs' ? 1 : 0;
		var abortBlur = false;
		var
				valueElement = jscolorNPF.fetchElement(this.valueElement),
				styleElement = jscolorNPF.fetchElement(this.styleElement);
		var
				holdPad = false,
				holdSld = false;
		var
				posPad,
				posSld;
		var
				leaveValue = 1<<0,
				leaveStyle = 1<<1,
				leavePad = 1<<2,
				leaveSld = 1<<3;

		// target
		jscolorNPF.addEvent(target, 'focus', function() {
			if(THIS.pickerOnfocus) { THIS.showPicker(); }
		});
		jscolorNPF.addEvent(target, 'blur', function() {
			if(!abortBlur) {
				window.setTimeout(function(){ abortBlur || blurTarget(); abortBlur=false; }, 0);
			} else {
				abortBlur = false;
			}
		});

		// valueElement
		if(valueElement) {
			var updateField = function() {
				THIS.fromString(valueElement.value, leaveValue);
			};
			jscolorNPF.addEvent(valueElement, 'keyup', updateField);
			jscolorNPF.addEvent(valueElement, 'input', updateField);
			jscolorNPF.addEvent(valueElement, 'blur', blurValue);
			valueElement.setAttribute('autocomplete', 'off');
		}

		// styleElement
		if(styleElement) {
			styleElement.jscStyle = {
				backgroundColor : styleElement.style.backgroundColor,
				color : styleElement.style.color
			};
		}

		// require images
		switch(modeID) {
			case 0: jscolorNPF.requireImage('hs.png'); break;
			case 1: jscolorNPF.requireImage('hv.png'); break;
		}
		jscolorNPF.requireImage('cross.gif');
		jscolorNPF.requireImage('arrow.gif');

		this.importColor();
	}

};

/**
 * INITS
 */
setTimeout(function(){jscolorNPF.install();},500);
