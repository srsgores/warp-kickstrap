/*------------------------------------------------------------------------------------------------------------------------
 Author: Sean Goresht
 www: http://seangoresht.com/
 github: https://github.com/srsgores

 twitter: http://twitter.com/S.Goresht

 warp-kickstrap wordpress Template
 Licensed under the GNU Public License

 =============================================================================
 Filename:  dropdownmenu.js
 =============================================================================
 This file is responsible for creating the main menu dropdown.  In the future, I will be entirely deleting this in favour of a CSS3, IE7-compatible menu.  Stay tuned, and just deal with this file.

 --------------------------------------------------------------------------------------------------------------------- */

(function (d)
{
	var e = function ()
	{
	};
	d.extend(e.prototype, {name: "dropdownMenu", options: {mode: "default", itemSelector: "li", firstLevelSelector: "li.level1", dropdownSelector: "ul", duration: 600, remainTime: 800, remainClass: "remain", matchHeight: !0, transition: "easeOutExpo", withopacity: !0, centerDropdown: !1, reverseAnimation: !1, fixWidth: !1, fancy: null, boundary: d(window), boundarySelector: null}, initialize: function (e, j)
	{
		this.options = d.extend({}, this.options, j);
		var a = this, g = null, r = !1;
		this.menu = e;
		this.dropdowns = [];
		this.options.withopacity =
			d.support.opacity ? this.options.withopacity : !1;
		if (this.options.fixWidth)
		{
			var s = 5;
			this.menu.children().each(function ()
			{
				s += d(this).width()
			});
			this.menu.css("width", s)
		}
		this.options.matchHeight && this.matchHeight();
		this.menu.find(this.options.firstLevelSelector).each(function (q)
		{
			var k = d(this), b = k.find(a.options.dropdownSelector).css({overflow: "hidden"});
			if (b.length)
			{
				b.css("overflow", "hidden").show();
				b.data("init-width", parseFloat(b.css("width")));
				b.data("columns", b.find(".column").length);
				b.data("single-width",
					1 < b.data("columns") ? b.data("init-width") / b.data("columns") : b.data("init-width"));
				var f = d("<div>").css({overflow: "hidden"}).append("<div></div>"), e = f.find("div:first");
				b.children().appendTo(e);
				f.appendTo(b);
				a.dropdowns.push({dropdown: b, div: f, innerdiv: e});
				b.show();
				a.options.centerDropdown && b.css("margin-left", -1 * (parseFloat(b.css("width")) / 2 - k.width() / 2));
				b.hide()
			}
			k.bind({mouseenter: function ()
			{
				r = !0;
				a.menu.trigger("menu:enter", [k, q]);
				if (g)
				{
					if (g.index == q)
					{
						return;
					}
					g.item.removeClass(a.options.remainClass);
					g.div.hide().parent().hide()
				}
				if (b.length)
				{
					b.parent().find("div").css({width: "", height: "", "min-width": "", "min-height": ""});
					b.removeClass("flip").removeClass("stack");
					k.addClass(a.options.remainClass);
					f.stop().show();
					b.show();
					var c = b.css("width", b.data("init-width")).data("init-width");
					dpitem = a.options.boundarySelector ? d(a.options.boundarySelector, f) : f;
					boundary = {top: 0, left: 0, width: a.options.boundary.width()};
					e.css({"min-width": c});
					try
					{
						d.extend(boundary, a.options.boundary.offset())
					}
					catch (i)
					{
					}
					if (dpitem.offset().left <
						boundary.left || dpitem.offset().left + c - boundary.left > boundary.width)
					{
						b.addClass("flip"), dpitem.offset().left < boundary.left && (b.removeClass("flip").addClass("stack"), c = b.css("width", b.data("single-width")).data("single-width"), e.css({"min-width": c}));
					}
					var l = parseFloat(b.height());
					switch (a.options.mode)
					{
						case "showhide":
							c = {width: c, height: l};
							f.css(c);
							break;
						case "diagonal":
							var h = {width: 0, height: 0}, c = {width: c, height: l};
							a.options.withopacity && (h.opacity = 0, c.opacity = 1);
							f.css(h).animate(c, a.options.duration,
								a.options.transition);
							break;
						case "height":
							h = {width: c, height: 0};
							c = {height: l};
							a.options.withopacity && (h.opacity = 0, c.opacity = 1);
							f.css(h).animate(c, a.options.duration, a.options.transition);
							break;
						case "width":
							h = {width: 0, height: l};
							c = {width: c};
							a.options.withopacity && (h.opacity = 0, c.opacity = 1);
							f.css(h).animate(c, a.options.duration, a.options.transition);
							break;
						case "slide":
							b.css({width: c, height: l});
							f.css({width: c, height: l, "margin-top": -1 * l}).animate({"margin-top": 0}, a.options.duration, a.options.transition);
							break;
						default:
							h = {width: c, height: l}, c = {}, a.options.withopacity && (h.opacity = 0, c.opacity = 1), f.css(h).animate(c, a.options.duration, a.options.transition)
					}
					g = {item: k, div: f, index: q}
				}
				else
				{
					g = active = null
				}
			}, mouseleave: function (c)
			{
				if (c.srcElement && d(c.srcElement).hasClass("module"))
				{
					return!1;
				}
				r = !1;
				b.length ? window.setTimeout(function ()
				{
					if (!(r || "none" == f.css("display")))
					{
						a.menu.trigger("menu:leave", [k, q]);
						var b = function ()
						{
							k.removeClass(a.options.remainClass);
							g = null;
							f.hide().parent().hide()
						};
						if (a.options.reverseAnimation)
						{
							switch (a.options.mode)
							{
								case "showhide":
									b();
									break;
								case "diagonal":
									var c = {width: 0, height: 0};
									a.options.withopacity && (c.opacity = 0);
									f.stop().animate(c, a.options.duration, a.options.transition, function ()
									{
										b()
									});
									break;
								case "height":
									c = {height: 0};
									a.options.withopacity && (c.opacity = 0);
									f.stop().animate(c, a.options.duration, a.options.transition, function ()
									{
										b()
									});
									break;
								case "width":
									c = {width: 0};
									a.options.withopacity && (c.opacity = 0);
									f.stop().animate(c, a.options.duration, a.options.transition, function ()
									{
										b()
									});
									break;
								case "slide":
									f.stop().animate({"margin-top": -1 *
										parseFloat(f.data("dpheight"))}, a.options.duration, a.options.transition, function ()
									{
										b()
									});
									break;
								default:
									c = {}, a.options.withopacity && (c.opacity = 0), f.stop().animate(c, a.options.duration, a.options.transition, function ()
									{
										b()
									})
							}
						}
						else
						{
							b()
						}
					}
				}, a.options.remainTime) : a.menu.trigger("menu:leave")
			}})
		});
		if (this.options.fancy)
		{
			var i = d.extend({mode: "move", transition: "easeOutExpo", duration: 500, onEnter: null, onLeave: null}, this.options.fancy), m = this.menu.append('<div class="fancy bg1"><div class="fancy-1"><div class="fancy-2"><div class="fancy-3"></div></div></div></div>').find(".fancy:first").hide(),
				o = this.menu.find(".active:first"), n = null, t = function (a, d)
				{
					if (!d || !(n && a.get(0) == n.get(0)))
					{
						m.stop().show().css("visibility", "visible"), "move" == i.mode ? !o.length && !d ? m.hide() : m.animate({left: a.position().left + "px", width: a.width() + "px"}, i.duration, i.transition) : d ? m.css({opacity: o ? 0 : 1, left: a.position().left + "px", width: a.width() + "px"}).animate({opacity: 1}, i.duration) : m.animate({opacity: 0}, i.duration), n = d ? a : null
					}
				};
			this.menu.bind({"menu:enter": function (a, d, b)
			{
				t(d, !0);
				if (i.onEnter)
				{
					i.onEnter(d, b, m)
				}
			}, "menu:leave": function (a, d, b)
			{
				t(o, !1);
				if (i.onLeave)
				{
					i.onLeave(d, b, m)
				}
			}, "menu:fixfancy": function ()
			{
				n && m.stop().show().css({left: n.position().left + "px", width: n.width() + "px"})
			}});
			o.length && "move" == i.mode && t(o, !0)
		}
	}, matchHeight: function ()
	{
		this.menu.find("li.level1.parent").each(function ()
		{
			var e = 0;
			d(this).find("ul.level2").each(function ()
			{
				e = Math.max(d(this).height(), e)
			}).css("min-height", e)
		})
	}});
	d.fn[e.prototype.name] = function ()
	{
		var p = arguments, j = p[0] ? p[0] : null;
		return this.each(function ()
		{
			var a = d(this);
			if (e.prototype[j] && a.data(e.prototype.name) &&
				"initialize" != j)
			{
				a.data(e.prototype.name)[j].apply(a.data(e.prototype.name), Array.prototype.slice.call(p, 1));
			}
			else if (!j || d.isPlainObject(j))
			{
				var g = new e;
				e.prototype.initialize && g.initialize.apply(g, d.merge([a], p));
				a.data(e.prototype.name, g)
			}
			else
			{
				d.error("Method " + j + " does not exist on jQuery." + e.name)
			}
		})
	}
})(jQuery);
