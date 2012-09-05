/*------------------------------------------------------------------------------------------------------------------------
    Author: Sean Goresht
    www: http://seangoresht.com/
    github: https://github.com/srsgores

    twitter: http://twitter.com/S.Goresht

     warp-kickstrap Joomla Template
     Licensed under the GNU Public License

	=============================================================================
	Filename:  template.js
	=============================================================================
	 This file is to contain any template-specific javascript code.
	 In this file, we do the following:
	 	--move elements around in the DOM when there's a media query
	 	--Add checkbox menu when the max-width is below a certain amount of pixels
--------------------------------------------------------------------------------------------------------------------- */
function getRootUrl() {
	// Create
	var rootUrl = document.location.protocol+'//'+(document.location.hostname||document.location.host);
	if ( document.location.port||false ) {
		rootUrl += ':'+document.location.port;
	}
	rootUrl += '/';

	// Return
	return rootUrl;
}
var rootUrl = getRootUrl();

function getRandomInt(min, max)
{
	return Math.floor(Math.random() * (max - min + 1)) + min;
}
// Internal Helper
$.expr[':'].internal = function(obj, index, meta, stack){
	// Prepare
	var
		$this = $(obj),
		url = $this.attr('href')||'',
		isInternalLink;

	// Check link
	isInternalLink = url.substring(0,rootUrl.length) === rootUrl || url.indexOf(':') === -1;

	// Ignore or Keep
	return isInternalLink;
};

function addSlides(slideClass) {
	var content_area = $("#content");
	var wrappingStep = "<div class = " + slideClass + "></div>";
	content_area.wrapInner(wrappingStep);

	//bind all a tags to have be loaded as new slides
	var anchors = $("a:internal:not(.no-ajaxy)");
	anchors.attr("data-src", function() {
		//remove duplicate hrefs
		if (content_area.find("div[href='" + this.href + "']").length === 0) {
			console.log("Creating new step in content area with href of " + this.href);
			var newStep = content_area.append("<section class = " + slideClass + " data-x = \"" + getRandomInt(-4000, 8000) + "\" data-y = \"" + getRandomInt(-4000, 8000) + "\" data-src = \"" + this.href + "\"" + "></section>");
		}
		return this.href;
	});
}
(function($){

	$(document).ready(function() {

		var config = $('body').data('config') || {};

		// Accordion menu
		$('.menu-sidebar').accordionMenu({ mode:'slide' });

		// Dropdown menu
		$('#menu').dropdownMenu({ mode: 'slide', dropdownSelector: 'div.dropdown'});

		// Smoothscroller
		$('a[href="#page"]').smoothScroller({ duration: 500 });

		// Social buttons
		$('article[data-permalink]').socialButtons(config);
		//addSlides("step");
		//$("#content").jmpress();
	});

	$.onMediaQuery('(min-width: 960px)', {
		init: function() {
			if (!this.supported) this.matches = true;
		},
		valid: function() {
			$.matchWidth('grid-block', '.grid-block', '.grid-h').match();
			$.matchHeight('main', '#maininner, #sidebar-a, #sidebar-b').match();
			$.matchHeight('top-a', '#top-a .grid-h', '.deepest').match();
			$.matchHeight('top-b', '#top-b .grid-h', '.deepest').match();
			$.matchHeight('bottom-a', '#bottom-a .grid-h', '.deepest').match();
			$.matchHeight('bottom-b', '#bottom-b .grid-h', '.deepest').match();
			$.matchHeight('innertop', '#innertop .grid-h', '.deepest').match();
			$.matchHeight('innerbottom', '#innerbottom .grid-h', '.deepest').match();
		},
		invalid: function() {
			$.matchWidth('grid-block').remove();
			$.matchHeight('main').remove();
			$.matchHeight('top-a').remove();
			$.matchHeight('top-b').remove();
			$.matchHeight('bottom-a').remove();
			$.matchHeight('bottom-b').remove();
			$.matchHeight('innertop').remove();
			$.matchHeight('innerbottom').remove();
		}
	});

	var pairs = [];

	$.onMediaQuery('(min-width: 480px) and (max-width: 959px)', {
		valid: function() {
			$.matchHeight('sidebars', '.sidebars-2 #sidebar-a, .sidebars-2 #sidebar-b').match();
			pairs = [];
			$.each(['.sidebars-1 #sidebar-a > .grid-box', '.sidebars-1 #sidebar-b > .grid-box', '#top-a .grid-h', '#top-b .grid-h', '#bottom-a .grid-h', '#bottom-b .grid-h', '#innertop .grid-h', '#innerbottom .grid-h'], function(i, selector) {
				for (var i = 0, elms = $(selector), len = parseInt(elms.length / 2); i < len; i++) {
					var id = 'pair-' + pairs.length;
					$.matchHeight(id, [elms.get(i * 2), elms.get(i * 2 + 1)], '.deepest').match();
					pairs.push(id);
				}
			});
		},
		invalid: function() {
			$.matchHeight('sidebars').remove();
			$.each(pairs, function() { $.matchHeight(this).remove(); });
		}
	});

	$.onMediaQuery('(max-width: 767px)', {
		valid: function() {
			var header = $('#header-responsive');
			if (!header.length) {
				header = $('<div id="header-responsive"/>').prependTo('#header');
				$('#logo').clone().removeAttr('id').addClass('logo').appendTo(header);
				$('.searchbox').first().clone().removeAttr('id').appendTo(header);
				$('#menu').responsiveMenu().next().addClass('menu-responsive').appendTo(header);
			}
		}
	});

})(jQuery);