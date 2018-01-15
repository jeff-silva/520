var $=jQuery;


var script = document.createElement("script");
script.src = "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js";
document.head.appendChild(script);


jQuery.component = function(tagname, settings) {
	var proto = Object.create(HTMLElement.prototype);
	return document.registerElement(tagname, {prototype:proto});
};


function $_(attr, files, call) {
	var $els = $("["+attr+"]");
	call = typeof call=="function"? call: function() {};
	if ($els.length) {
		head.load(files, function() {
			$els.each(function() {
				var opts = $(this).attr(attr)||"{}";
				try { eval('opts='+opts); } catch(e) { opts={}; }
				call.call(this, opts);
			});
		});
	}
}





var cdzInit = function() {};
$.getScript("https://cdnjs.cloudflare.com/ajax/libs/headjs/1.0.3/head.load.min.js", function() {
	
	var cdzInit = function() {

		// [data-popup]
		$(document).on("click", "[data-popup]", function() {
			var popup = $(this).attr("data-popup")||false;
			$(popup).fadeToggle(200);
		});

		// .popup || .popup-close
		$(document).on("click", ".popup", function(ev) {
			var $this = $(ev.target);
			if ($this.hasClass("popup")) $this.fadeOut(200);
			else if ($this.hasClass("popup-close")) $this.closest(".popup").fadeOut(200);
		});


		// Remove autocomplete from forms
		$("form").attr("autocomplete", "off");


		/* <div data-tabs="{}"></div> */
		$("[data-tabs]").each(function() {
			var $parent = $(this);
			if ( $parent.hasClass("has-tabs") ) return false;
			$parent.addClass("has-tabs");
			var opts = $(this).attr("data-tabs")||"{}";
			try { eval('opts='+opts); } catch(e) { opts={}; }
			opts = $.extend({index:0}, opts);
			var $tab = '<ul class="nav nav-tabs">';
			$parent.find(">*").each(function(i) {
				var tab_title = $(this).attr("title")||("Tab"+(i+1));
				$tab += '<li><a href="javascriot:;">'+ tab_title +'</a></li>';
				$(this).addClass("has-tabs-content").css({padding:15}).hide();
			});
			$tab += '</ul>';
			$tab = $($tab);
			$parent.prepend($tab);
			$tab.find("a").on("click", function(ev) {
				ev.preventDefault();
				var index = $(this).parent().index();
				$parent.find(">.has-tabs-content").hide();
				$parent.find(">.has-tabs-content").eq(index).fadeIn(200);
				$tab.find(">li").removeClass("active");
				$tab.find(">li").eq(index).addClass("active");
			});
			$tab.find(">li").eq(opts.index).find(">a").click();
		});

		// Data mask
		$.jMaskGlobals = {};
		$.jMaskGlobals.dataMaskAttr = "*[data-masked]";
		$.jMaskGlobals.dataMask = false;
		$.jMaskGlobals.watchDataMask = false;
		$_("data-mask", ["https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.13/jquery.mask.min.js"], function(opts) {
			opts.mask = opts.mask||"";
			if (opts.mask=="money") { opts.mask="000.000.000.000.000,00"; opts.reverse=true; }
			// else if (opts.mask=="phone") {}
			$(this).mask(opts.mask, opts);
		});
		
		// Data table
		$_("data-table", [
			"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js",
			"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap.min.js",
			"https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap.min.css",
		], function(opts) {
			opts = $.extend({
				language: {
					url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Portuguese-Brasil.json",
				},
			}, opts);
			$(this).DataTable(opts);
		});

	};
	cdzInit.call();
});
