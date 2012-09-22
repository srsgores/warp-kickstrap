/*------------------------------------------------------------------------------------------------------------------------
 Author: Sean Goresht
 www: http://seangoresht.com/
 github: https://github.com/srsgores

 twitter: http://twitter.com/S.Goresht

 warp-kickstrap Joomla Template
 Licensed under the GNU Public License

 =============================================================================
 Filename:  dynslide.js
 =============================================================================
 This file is responsible for generating slides from anchor tags in the document.

 --------------------------------------------------------------------------------------------------------------------- */

function getRandomInt(min, max)
{
	return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Internal Helper
$.expr[':'].internal = function (obj, index, meta, stack)
{
	// Prepare
	var
		$this = $(obj),
		url = $this.attr('href') || '',
		isInternalLink;

	// Check link
	isInternalLink = url.substring(0, rootUrl.length) === rootUrl || url.indexOf(':') === -1;

	// Ignore or Keep
	return isInternalLink;
};

function setSlideHeight(stepSelector)
{
	var calculatedHeight = $(window).height() - ($("header").height() + $("footer").height()); //the space in which all content must fit
	console.log("Setting slide height to: " + calculatedHeight);
	$(stepSelector).height(function (index, height)
	{ //function being ignored because it only takes one function, not a param and function
		if (height > calculatedHeight)
		{
			//set overflow
			console.log("Old height was: " + height);
			console.log("Index was: " + index);
			$(this).height(calculatedHeight).css("overflow-y", "scroll");
			console.log("content overflowed.  Adding scroll");
		}
		else
		{
			console.log("not overflowing content");
			console.log("Old height was: " + height);
			console.log("Index was: " + index);
			$(this).height(calculatedHeight);
			/*$(this).css("overflow", "hidden").height(calculatedHeight);*/
		}
	});
}
function setCss() {
	$("header,footer").css("position", "fixed");
}

function unsetCss() {
	$("header,footer").css("position", "relative");
}
function setRowWidth($)
{
	var docWidth = $(window).width() + "px";
	//set row widths to adapt to screen size
	var rows = $("#content .row");
	console.log("Setting row widths to " + docWidth);
	rows.css("width", docWidth);
}

function resizeSlides()
{
	$(window).resize(function ()
	{
		setRowWidth($);
		setSlideHeight(".step");
	});
}

function getRootUrl()
{
	// Create
	var rootUrl = document.location.protocol + '//' + (document.location.hostname || document.location.host);
	if (document.location.port || false)
	{
		rootUrl += ':' + document.location.port;
	}
	rootUrl += '/';

	// Return
	return rootUrl;
}
var rootUrl = getRootUrl();

function addSlides(slideClass)
{
	var content_area = $("#content");
	var wrappingStep = "<div class = " + slideClass + "></div>";
	content_area.wrapInner(wrappingStep);

	//bind all a tags to have be loaded as new slides
	var anchors = $("a:internal:not(.no-ajaxy)");
	anchors.attr("href", function ()
	{
		//don't create duplicates
		if (!content_area.find("section[data-src='" + this.href + "']").length)
		{
			console.log("Creating new step in content area with href of " + this.href);
			var newStep = content_area.append("<section class = " + slideClass + " data-x = \"" + getRandomInt(-4000, 8000) + "\" data-y = \"" + getRandomInt(-4000, 8000) + "\" data-z = \"" + getRandomInt(-4000, 8000) + "\" data-src = \"" + this.href + "\"" + "id = " + $(this).text() + "></section>");
			return "#" + $(this).text();
		}
		return $(this).href;
		//if the step section already exists, then change this url to point to the corresponding step
	});
}

function dynSlide($)
{
	addSlides("step");
	$("#content").jmpress({
		'ajax:afterStepLoaded':function (element, eventData)
		{
			var scripts = String($(element).data('script') || '').split(',');
			scripts.forEach(function (script)
			{
				$.getScript(script, function ()
				{

				});
			});
			$(window).trigger("resize");
		},
		afterInit:resizeSlides()
	});

	$(window).trigger("resize");
}

jQuery(document).ready(function ($)
{
	setCss();
	dynSlide($);
});