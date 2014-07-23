(function () {
    ! function (t) {
        return t.fn.bootstrapSwitch = function (e) {
            var a;
            return a = {
                init: function () {
                    return this.each(function () {
                        var e, a, s, n, i, r, o, c;
                        return a = t(this), i = t("<span>", {
                            "class": "switch-left",
                            html: function () {
                                var t, e;
                                return t = "ON", e = a.data("on-label"), null != e && (t = e), t
                            }
                        }), r = t("<span>", {
                            "class": "switch-right",
                            html: function () {
                                var t, e;
                                return t = "OFF", e = a.data("off-label"), null != e && (t = e), t
                            }
                        }), n = t("<label>", {
                            "for": a.attr("id"),
                            html: function () {
                                var t, e, s;
                                return t = "&nbsp;", e = a.data("label-icon"), s = a.data("text-label"), null != e && (t = '<i class="icon ' + e + '"></i>'), null != s && (t = s), t
                            }
                        }), e = t("<div>"), o = t("<div>", {
                            "class": "has-switch",
                            tabindex: 0
                        }), s = a.closest("form"), c = function () {
                            return n.hasClass("label-change-switch") ? void 0 : n.trigger("mousedown").trigger("mouseup").trigger("click")
                        }, a.data("bootstrap-switch", !0), a.attr("class") && t.each(["switch-mini", "switch-small", "switch-large"], function (t, e) {
                            return a.attr("class").indexOf(e) >= 0 ? (i.addClass(e), n.addClass(e), r.addClass(e)) : void 0
                        }), null != a.data("on") && i.addClass("switch-" + a.data("on")), null != a.data("off") && r.addClass("switch-" + a.data("off")), e.data("animated", !1), a.data("animated") !== !1 && e.addClass("switch-animate").data("animated", !0), e = a.wrap(e).parent(), o = e.wrap(o).parent(), a.before(i).before(n).before(r), e.addClass(a.is(":checked") ? "switch-on" : "switch-off"), (a.is(":disabled") || a.is("[readonly]")) && o.addClass("disabled"), a.on("keydown", function (t) {
                            return 32 === t.keyCode ? (t.stopImmediatePropagation(), t.preventDefault(), c()) : void 0
                        }).on("change", function (t, s) {
                            var n, i;
                            return n = a.is(":checked"), i = e.hasClass("switch-off"), t.preventDefault(), e.css("left", ""), i !== n || (n ? e.removeClass("switch-off").addClass("switch-on") : e.removeClass("switch-on").addClass("switch-off"), e.data("animated") !== !1 && e.addClass("switch-animate"), "boolean" == typeof s && s) ? void 0 : a.trigger("switch-change", {
                                el: a,
                                value: n
                            })
                        }), o.on("keydown", function (t) {
                            if (t.which && !a.is(":disabled") && !a.is("[readonly]")) switch (t.which) {
                            case 32:
                                return t.preventDefault(), c();
                            case 37:
                                if (t.preventDefault(), a.is(":checked")) return c();
                                break;
                            case 39:
                                if (t.preventDefault(), !a.is(":checked")) return c()
                            }
                        }), i.on("click", function () {
                            return c()
                        }), r.on("click", function () {
                            return c()
                        }), n.on("mousedown touchstart", function (t) {
                            var s;
                            return s = !1, t.preventDefault(), t.stopImmediatePropagation(), e.removeClass("switch-animate"), a.is(":disabled") || a.is("[readonly]") || a.hasClass("radio-no-uncheck") ? n.unbind("click") : n.on("mousemove touchmove", function (t) {
                                var a, n, i, r;
                                return i = (t.pageX || t.originalEvent.targetTouches[0].pageX) - o.offset().left, n = i / o.width() * 100, a = 25, r = 75, s = !0, a > n ? n = a : n > r && (n = r), e.css("left", n - r + "%")
                            }).on("click touchend", function (t) {
                                return t.stopImmediatePropagation(), t.preventDefault(), n.unbind("mouseleave"), s ? a.prop("checked", parseInt(n.parent().css("left"), 10) > -25) : a.prop("checked", !a.is(":checked")), s = !1, a.trigger("change")
                            }).on("mouseleave", function (t) {
                                return t.preventDefault(), t.stopImmediatePropagation(), n.unbind("mouseleave mousemove").trigger("mouseup"), a.prop("checked", parseInt(n.parent().css("left"), 10) > -25).trigger("change")
                            }).on("mouseup", function (t) {
                                return t.stopImmediatePropagation(), t.preventDefault(), n.trigger("mouseleave")
                            })
                        }), s.data("bootstrap-switch") ? void 0 : s.bind("reset", function () {
                            return window.setTimeout(function () {
                                return s.find(".has-switch").each(function () {
                                    var e;
                                    return e = t(this).find("input"), e.prop("checked", e.is(":checked")).trigger("change")
                                })
                            }, 1)
                        }).data("bootstrap-switch", !0)
                    })
                },
                setDisabled: function (e) {
                    var a, s;
                    return a = t(this), s = a.parents(".has-switch"), e ? (s.addClass("disabled"), a.prop("disabled", !0)) : (s.removeClass("disabled"), a.prop("disabled", !1)), a
                },
                toggleDisabled: function () {
                    var e;
                    return e = t(this), e.prop("disabled", !e.is(":disabled")).parents(".has-switch").toggleClass("disabled"), e
                },
                isDisabled: function () {
                    return t(this).is(":disabled")
                },
                setReadOnly: function (e) {
                    var a, s;
                    return a = t(this), s = a.parents(".has-switch"), e ? (s.addClass("disabled"), a.prop("readonly", !0)) : (s.removeClass("disabled"), a.prop("readonly", !1)), a
                },
                toggleReadOnly: function () {
                    var e;
                    return e = t(this), e.prop("readonly", !e.is("[readonly]")).parents(".has-switch").toggleClass("disabled"), e
                },
                isReadOnly: function () {
                    return t(this).is("[readonly]")
                },
                toggleState: function (e) {
                    var a;
                    return a = t(this), a.prop("checked", !a.is(":checked")).trigger("change", e), a
                },
                toggleRadioState: function (e) {
                    var a;
                    return a = t(this), a.not(":checked").prop("checked", !a.is(":checked")).trigger("change", e), a
                },
                toggleRadioStateAllowUncheck: function (e, a) {
                    var s;
                    return s = t(this), e ? s.not(":checked").trigger("change", a) : s.not(":checked").prop("checked", !s.is(":checked")).trigger("change", a), s
                },
                setState: function (e, a) {
                    var s;
                    return s = t(this), s.prop("checked", e).trigger("change", a), s
                },
                setOnLabel: function (e) {
                    var a;
                    return a = t(this), a.siblings(".switch-left").html(e), a
                },
                setOffLabel: function (e) {
                    var a;
                    return a = t(this), a.siblings(".switch-right").html(e), a
                },
                setOnClass: function (e) {
                    var a, s, n;
                    return a = t(this), s = a.siblings(".switch-left"), n = a.attr("data-on"), null != e ? (null != n && s.removeClass("switch-" + n), s.addClass("switch-" + e), a) : void 0
                },
                setOffClass: function (e) {
                    var a, s, n;
                    return a = t(this), s = a.siblings(".switch-right"), n = a.attr("data-off"), null != e ? (null != n && s.removeClass("switch-" + n), s.addClass("switch-" + e), a) : void 0
                },
                setAnimated: function (e) {
                    var a, s;
                    return s = t(this), a = s.parent(), null == e && (e = !1), a.data("animated", e).attr("data-animated", e)[a.data("animated") !== !1 ? "addClass" : "removeClass"]("switch-animate"), s
                },
                setSizeClass: function (e) {
                    var a, s, n, i;
                    return a = t(this), n = a.siblings(".switch-left"), s = a.siblings("label"), i = a.siblings(".switch-right"), t.each(["switch-mini", "switch-small", "switch-large"], function (t, a) {
                        return a !== e ? (n.removeClass(a), s.removeClass(a), i.removeClass(a)) : (n.addClass(a), s.addClass(a), i.addClass(a))
                    }), a
                },
                setTextLabel: function (e) {
                    var a;
                    return a = t(this), a.siblings("label").html(e || "&nbsp"), a
                },
                setTextIcon: function (e) {
                    var a;
                    return a = t(this), a.siblings("label").html(e ? '<i class="icon ' + e + '"></i>' : "&nbsp;"), a
                },
                state: function () {
                    return t(this).is(":checked")
                },
                destroy: function () {
                    var e, a, s;
                    return a = t(this), e = a.parent(), s = e.closest("form"), e.children().not(a).remove(), a.unwrap().unwrap().unbind("change"), s.length && s.unbind("reset").removeData("bootstrapSwitch"), a
                }
            }, a[e] ? a[e].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof e && e ? t.error("Method " + e + " does not exist!") : a.init.apply(this, arguments)
        }, this
    }(jQuery)
}).call(this);