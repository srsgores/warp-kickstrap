/*------------------------------------------------------------------------------------------------------------------------
 Author: Sean Goresht
 www: http://seangoresht.com/
 github: https://github.com/srsgores

 twitter: http://twitter.com/S.Goresht

 warp-kickstrap wordpress Template
 Licensed under the GNU Public License

 =============================================================================
 Filename:  follower.js
 =============================================================================
 This file is responsible for letting the user "follow" an article post if the social links parameter was enabled.

 --------------------------------------------------------------------------------------------------------------------- */

(function (d)
{
	var b = function ()
	{
	};
	d.extend(b.prototype, {name: "follower", options: {activeClass: "active", hoveredClass: "isfollowing", slider: {"class": "fancyfollower", html: "<div></div>"}, effect: {transition: "easeOutBack", duration: 200}}, initialize: function (a, e)
	{
		this.options = d.extend({}, this.options, e);
		var c = this;
		a.css("position", "relative");
		this.current = null;
		d(a.children()).each(function ()
		{
			d(this).bind({mouseenter: function ()
			{
				c.slider.stop();
				c.slideTo(d(this), "enter")
			}, mouseleave: function ()
			{
				c.slideTo(c.current,
					"leave")
			}, click: function ()
			{
				c.setCurrent(d(this), !0)
			}}).css({position: "relative"})
		});
		var b = a.children()[0].tagName.toLowerCase();
		a.append(d("<" + b + ">").addClass(this.options.slider["class"]).html(this.options.slider.html));
		this.slider = a.find(">" + b + ":last");
		this.setCurrent(a.find("." + this.options.activeClass + ":first"));
		this.current && (this.startElement = this.current)
	}, setCurrent: function (a, b)
	{
		if (a.length && !this.current)
		{
			var c = a.position();
			this.slider.css({left: c.left, width: a.width(), height: a.height(),
				top: c.top, opacity: 1});
			b ? this.slider.fadeIn() : this.slider.show()
		}
		this.current && this.current.removeClass(this.options.hoveredClass);
		a.length && (this.current = a.addClass(this.options.hoveredClass));
		return this
	}, slideTo: function (a, b)
	{
		this.current || this.setCurrent(a);
		this.slider.stop().animate({left: a.position().left + "px", width: a.outerWidth() + "px", top: a.position().top + "px", height: a.outerHeight() + "px"}, this.options.effect.duration, this.options.effect.transition);
		this.isHovered = "leave" == b ? !1 : !0;
		if ("leave" ==
			b && !this.startElement)
		{
			var c = this;
			window.setTimeout(function ()
			{
				c.isHovered || (c.slider.fadeOut(), c.current = !1)
			}, 200)
		}
		else
		{
			this.slider.css("opacity", 1).fadeIn();
		}
		return this
	}});
	d.fn[b.prototype.name] = function ()
	{
		var a = arguments, e = a[0] ? a[0] : null;
		return this.each(function ()
		{
			var c = d(this);
			if (b.prototype[e] && c.data(b.prototype.name) && "initialize" != e)
			{
				c.data(b.prototype.name)[e].apply(c.data(b.prototype.name), Array.prototype.slice.call(a, 1));
			}
			else if (!e || d.isPlainObject(e))
			{
				var f = new b;
				b.prototype.initialize &&
				f.initialize.apply(f, d.merge([c], a));
				c.data(b.prototype.name, f)
			}
			else
			{
				d.error("Method " + e + " does not exist on jQuery." + b.name)
			}
		})
	}
})(jQuery);
