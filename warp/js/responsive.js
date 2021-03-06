/*------------------------------------------------------------------------------------------------------------------------
 Author: Sean Goresht
 www: http://seangoresht.com/
 github: https://github.com/srsgores

 twitter: http://twitter.com/S.Goresht

 warp-kickstrap wordpress Template
 Licensed under the GNU Public License

 =============================================================================
 Filename:  responsive.js
 =============================================================================
 This file is responsible for allowing the onMediaQuery events to happen with Javascript urually defined in the template..

 --------------------------------------------------------------------------------------------------------------------- */

(function (b, f, g)
{
	function d(b)
	{
		i.innerHTML = '&shy;<style media="' + b + '"> #mq-test-1 { width: 42px; }</style>';
		e.insertBefore(j, c);
		a = 42 == i.offsetWidth;
		e.removeChild(j);
		return a
	}

	function h(b)
	{
		var a = d(b.media);
		if (b._listeners && b.matches != a)
		{
			b.matches = a;
			for (var a = 0, c = b._listeners.length;
			     a < c;
			     a++)
			{
				b._listeners[a](b)
			}
		}
	}

	if (!f.matchMedia || b.userAgent.match(/(iPhone|iPod|iPad)/i))
	{
		var a, e = g.documentElement, c = e.firstElementChild || e.firstChild, j = g.createElement("body"), i = g.createElement("div");
		i.id = "mq-test-1";
		i.style.cssText =
			"position:absolute;top:-100em";
		j.style.background = "none";
		j.appendChild(i);
		f.matchMedia = function (b)
		{
			var a, c = [];
			a = {matches: d(b), media: b, _listeners: c, addListener: function (b)
			{
				"function" === typeof b && c.push(b)
			}, removeListener: function (b)
			{
				for (var a = 0, e = c.length;
				     a < e;
				     a++)
				{
					c[a] === b && delete c[a]
				}
			}};
			f.addEventListener && f.addEventListener("resize", function ()
			{
				h(a)
			}, !1);
			g.addEventListener && g.addEventListener("orientationchange", function ()
			{
				h(a)
			}, !1);
			return a
		}
	}
})(navigator, window, document);
(function (b, f, g)
{
	if (!b.onMediaQuery)
	{
		var d = {}, h = f.matchMedia && f.matchMedia("only all").matches;
		b(g).ready(function ()
		{
			for (var a in
				d)
			{
				b(d[a]).trigger("init"), d[a].matches && b(d[a]).trigger("valid")
			}
		});
		b(f).bind("load", function ()
		{
			for (var a in
				d)
			{
				d[a].matches && b(d[a]).trigger("valid")
			}
		});
		b.onMediaQuery = function (a, e)
		{
			var c = a && d[a];
			c || (c = d[a] = f.matchMedia(a), c.supported = h, c.addListener(function ()
			{
				b(c).trigger(c.matches ? "valid" : "invalid")
			}));
			b(c).bind(e);
			return c
		}
	}
})(jQuery, window, document);
(function (b, f, g)
{
	b.fn.responsiveMenu = function (d)
	{
		function h(a, e)
		{
			var c = "";
			b(a).children().each(function ()
			{
				var a = b(this);
				a.children("a, span.separator").each(function ()
				{
					var d = b(this), f = d.is("a") ? d.attr("href") : "", g = d.is("span") ? " disabled" : "", k = 1 < e ? Array(e).join("-") + " " : "", n = d.find(".title").length ? d.find(".title").text() : d.text();
					c += '<option value="' + f + '" class="' + d.attr("class") + '"' + g + ">" + k + n + "</option>";
					a.find("ul.level" + (e + 1)).each(function ()
					{
						c += h(this, e + 1)
					})
				})
			});
			return c
		}

		d = b.extend({current: ".current"},
			d);
		return this.each(function ()
		{
			var a = b(this), e = b("<select/>"), c = "";
			a.find("ul.menu").each(function ()
			{
				c += h(this, 1)
			});
			e.append(c).change(function ()
			{
				g.location.href = e.val()
			});
			e.find(d.current).attr("selected", !0);
			/iPhone|iPad|iPod/.test(f.platform) && (/OS [1-5]_[0-9_]* like Mac OS X/i.test(f.userAgent) && -1 < f.userAgent.indexOf("AppleWebKit")) && e.find(":disabled").remove();
			a.after(e)
		})
	}
})(jQuery, navigator, window);
(function (b, f)
{
	function g()
	{
		a.setAttribute("content", c);
		j = !0
	}

	function d(c)
	{
		k = c.accelerationIncludingGravity;
		i = Math.abs(k.x);
		l = Math.abs(k.y);
		m = Math.abs(k.z);
		(!b.orientation || 180 === b.orientation) && (7 < i || (6 < m && 8 > l || 8 > m && 6 < l) && 5 < i) ? j && (a.setAttribute("content", e), j = !1) : j || g()
	}

	if (/iPhone|iPad|iPod/.test(f.platform) && /OS [1-5]_[0-9_]* like Mac OS X/i.test(f.userAgent) && -1 < f.userAgent.indexOf("AppleWebKit"))
	{
		var h = b.document;
		if (h.querySelector)
		{
			var a = h.querySelector("meta[name=viewport]"), h = a && a.getAttribute("content"),
				e = h + ",maximum-scale=1", c = h + ",maximum-scale=10", j = !0, i, l, m, k;
			a && (b.addEventListener("orientationchange", g, !1), b.addEventListener("devicemotion", d, !1))
		}
	}
})(this, navigator);
