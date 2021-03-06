var Mouse = {
    x: 0,
    y: 0,
    refresh: function(e) {
        if (e && !this.down && !jQuery(e.target).hasClass("flowpaper_zoomSlider")) {
            return;
        }
        var posx = 0,
            posy = 0;
        if (!e) {
            e = window.event;
        }
        if (e.pageX || e.pageY) {
            posx = e.pageX;
            posy = e.pageY;
        } else {
            if (e.clientX || e.clientY) {
                posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
            }
        }
        this.x = posx;
        this.y = posy;
    }
};
var mouseMoveHandler = document.onmousemove || function() {};
document.onmousemove = function(e) {
    if (!e) {
        e = window.event;
    }
    if (e && e.which == 1) {
        Mouse.down = true;
    }
    Mouse.refresh(e);
};
var MPosition = {
    get: function(obj) {
        var curleft = curtop = 0;
        if (obj.offsetParent) {
            do {
                curleft += obj.offsetLeft;
                curtop += obj.offsetTop;
            } while (obj = obj.offsetParent);
        }
        return [curleft, curtop];
    }
};
var Slider = function(wrapper, options) {
    if (typeof wrapper == "string") {
        wrapper = document.getElementById(wrapper);
    }
    if (!wrapper) {
        return;
    }
    var handle = wrapper.getElementsByTagName("div")[0];
    if (!handle || handle.className.search(/(^|\s)flowpaper_handle(\s|$)/) == -1) {
        return;
    }
    this.init(wrapper, handle, options || {});
    this.setup();
};
Slider.prototype = {
    init: function(wrapper, handle, options) {
        this.wrapper = wrapper;
        this.handle = handle;
        this.options = options;
        this.value = {
            current: options.value || 0,
            target: options.value || 0,
            prev: -1
        };
        this.disabled = options.disabled || false;
        this.steps = options.steps || 0;
        this.snapping = options.snapping || false;
        this.speed = options.speed || 5;
        this.callback = options.callback || null;
        this.animation_callback = options.animation_callback || null;
        this.bounds = {
            pleft: options.pleft || 0,
            left: 0,
            pright: -(options.pright || 0),
            right: 0,
            width: 0,
            diff: 0
        };
        this.offset = {
            wrapper: 0,
            mouse: 0,
            target: 0,
            current: 0,
            prev: -9999
        };
        this.dragging = false;
        this.tapping = false;
    },
    setup: function() {
        var self = this;
        this.wrapper.onselectstart = function() {
            return false;
        };
        this.handle.onmousedown = function(e) {
            self.preventDefaults(e, true);
            this.focus();
            self.handleMouseDownHandler(e);
        };
        this.wrapper.onmousedown = function(e) {
            self.preventDefaults(e);
            self.wrapperMouseDownHandler(e);
        };
        var mouseUpHandler = document.onmouseup || function() {};
        if (document.addEventListener) {
            document.addEventListener("mouseup", function(e) {
                if (self.dragging) {
                    mouseUpHandler(e);
                    self.preventDefaults(e);
                    self.documentMouseUpHandler(e);
                }
            });
        } else {
            document.onmouseup = function(e) {
                if (self.dragging) {
                    mouseUpHandler(e);
                    self.preventDefaults(e);
                    self.documentMouseUpHandler(e);
                }
            };
        }
        var resizeHandler = document.onresize || function() {};
        window.onresize = function(e) {
            resizeHandler(e);
            self.setWrapperOffset();
            self.setBounds();
        };
        this.setWrapperOffset();
        if (!this.bounds.pleft && !this.bounds.pright) {
            this.bounds.pleft = MPosition.get(this.handle)[0] - this.offset.wrapper;
            this.bounds.pright = -this.bounds.pleft;
        }
        this.setBounds();
        this.setSteps();
        this.interval = setInterval(function() {
            self.animate();
        }, 100);
        self.animate(false, true);
    },
    setWrapperOffset: function() {
        this.offset.wrapper = MPosition.get(this.wrapper)[0];
    },
    setBounds: function() {
        this.bounds.left = this.bounds.pleft;
        this.bounds.right = this.bounds.pright + this.wrapper.offsetWidth;
        this.bounds.width = this.bounds.right - this.bounds.left;
        this.bounds.diff = this.bounds.width - this.handle.offsetWidth;
    },
    setSteps: function() {
        if (this.steps > 1) {
            this.stepsRatio = [];
            for (var i = 0; i <= this.steps - 1; i++) {
                this.stepsRatio[i] = i / (this.steps - 1);
            }
        }
    },
    disable: function() {
        this.disabled = true;
        this.handle.className += " disabled";
    },
    enable: function() {
        this.disabled = false;
        this.handle.className = this.handle.className.replace(/\s?disabled/g, "");
    },
    handleMouseDownHandler: function(e) {
        if (Mouse) {
            Mouse.down = true;
            Mouse.refresh(e);
        }
        var self = this;
        this.startDrag(e);
        this.cancelEvent(e);
    },
    wrapperMouseDownHandler: function(e) {
        this.startTap();
    },
    documentMouseUpHandler: function(e) {
        this.stopDrag();
        this.stopTap();
        if (Mouse) {
            Mouse.down = false;
        }
    },
    startTap: function(target) {
        if (this.disabled) {
            return;
        }
        if (target === undefined) {
            target = Mouse.x - this.offset.wrapper - this.handle.offsetWidth / 2;
        }
        this.setOffsetTarget(target);
        this.tapping = true;
    },
    stopTap: function() {
        if (this.disabled || !this.tapping) {
            return;
        }
        this.setOffsetTarget(this.offset.current);
        this.tapping = false;
        this.result();
    },
    startDrag: function(e) {
        if (!e) {
            e = window.event;
        }
        if (this.disabled) {
            return;
        }
        this.offset.mouse = Mouse.x - MPosition.get(this.handle)[0];
        this.dragging = true;
        if (e.preventDefault) {
            e.preventDefault();
        }
    },
    stopDrag: function() {
        if (this.disabled || !this.dragging) {
            return;
        }
        this.dragging = false;
        this.result();
    },
    feedback: function() {
        var value = this.value.current;
        if (this.steps > 1 && this.snapping) {
            value = this.getClosestStep(value);
        }
        if (value != this.value.prev) {
            if (typeof this.animation_callback == "function") {
                this.animation_callback(value);
            }
            this.value.prev = value;
        }
    },
    result: function() {
        var value = this.value.target;
        if (this.steps > 1) {
            value = this.getClosestStep(value);
        }
        if (typeof this.callback == "function") {
            this.callback(value);
        }
    },
    animate: function(onMove, first) {
        if (onMove && !this.dragging) {
            return;
        }
        if (this.dragging) {
            this.setOffsetTarget(Mouse.x - this.offset.mouse - this.offset.wrapper);
        }
        this.value.target = Math.max(this.value.target, 0);
        this.value.target = Math.min(this.value.target, 1);
        this.offset.target = this.getOffsetByRatio(this.value.target);
        if (!this.dragging && !this.tapping || this.snapping) {
            if (this.steps > 1) {
                this.setValueTarget(this.getClosestStep(this.value.target));
            }
        }
        if (this.dragging || first) {
            this.value.current = this.value.target;
        }
        this.slide();
        this.show();
        this.feedback();
    },
    slide: function() {
        if (this.value.target > this.value.current) {
            this.value.current += Math.min(this.value.target - this.value.current, this.speed / 100);
        } else {
            if (this.value.target < this.value.current) {
                this.value.current -= Math.min(this.value.current - this.value.target, this.speed / 100);
            }
        }
        if (!this.snapping) {
            this.offset.current = this.getOffsetByRatio(this.value.current);
        } else {
            this.offset.current = this.getOffsetByRatio(this.getClosestStep(this.value.current));
        }
    },
    show: function() {
        if (this.offset.current != this.offset.prev) {
            this.handle.style.left = String(this.offset.current) + "px";
            this.offset.prev = this.offset.current;
        }
    },
    setValue: function(value, snap) {
        this.setValueTarget(value);
        if (snap) {
            this.value.current = this.value.target;
        }
    },
    setValueTarget: function(value) {
        this.value.target = value;
        this.offset.target = this.getOffsetByRatio(value);
    },
    setOffsetTarget: function(value) {
        this.offset.target = value;
        this.value.target = this.getRatioByOffset(value);
    },
    getRatioByOffset: function(offset) {
        return (offset - this.bounds.left) / this.bounds.diff;
    },
    getOffsetByRatio: function(ratio) {
        return Math.round(ratio * this.bounds.diff) + this.bounds.left;
    },
    getClosestStep: function(value) {
        var k = 0;
        var min = 1;
        for (var i = 0; i <= this.steps - 1; i++) {
            if (Math.abs(this.stepsRatio[i] - value) < min) {
                min = Math.abs(this.stepsRatio[i] - value);
                k = i;
            }
        }
        return this.stepsRatio[k];
    },
    preventDefaults: function(e, selection) {
        if (!e) {
            e = window.event;
        }
        if (e.preventDefault) {
            e.preventDefault();
        }
        if (selection && document.selection) {
            document.selection.empty();
        }
    },
    cancelEvent: function(e) {
        if (!e) {
            e = window.event;
        }
        if (e.stopPropagation) {
            e.stopPropagation();
        } else {
            e.cancelBubble = true;
        }
    }
};
var E, FLOWPAPER = window.FLOWPAPER ? window.FLOWPAPER : window.FLOWPAPER = {};
FLOWPAPER.Cj = function() {
    var f = [];
    return {
        wq: function(c) {
            f.push(c);
        },
        notify: function(c, d) {
            for (var e = 0, g = f.length; e < g; e++) {
                var h = f[e];
                if (h[c]) {
                    h[c](d);
                }
            }
        }
    };
}();

function K(f) {
    FLOWPAPER.Cj.notify("warn", f);
}

function O(f, c, d, e) {
    try {
        throw Error();
    } catch (g) {}
    FLOWPAPER.Cj.notify("error", f);
    d && c && (e ? jQuery("#" + d).trigger(c, e) : jQuery("#" + d).trigger(c));
    throw Error(f);
}
FLOWPAPER.Ak = {
    init: function() {
        "undefined" != typeof eb && eb || (eb = {});
        var f = navigator.userAgent.toLowerCase(),
            c = location.hash.substr(1),
            d = !1,
            e = "";
        0 <= c.indexOf("mobilepreview=") && (d = !0, e = c.substr(c.indexOf("mobilepreview=")).split("&")[0].split("=")[1]);
        var g;
        try {
            g = "ontouchstart" in document.documentElement;
        } catch (v) {
            g = !1;
        }!g && (f.match(/iphone/i) || f.match(/ipod/i) || f.match(/ipad/i)) && (d = !0);
        c = eb;
        g = /win/.test(f);
        var h = /mac/.test(f),
            m;
        if (!(m = d)) {
            try {
                m = "ontouchstart" in document.documentElement;
            } catch (v) {
                m = !1;
            }
        }
        c.platform = {
            win: g,
            mac: h,
            touchdevice: m || f.match(/touch/i) || navigator.Cb || navigator.msPointerEnabled,
            ios: d && ("ipad" == e || "iphone" == e) || f.match(/iphone/i) || f.match(/ipod/i) || f.match(/ipad/i),
            android: d && "android" == e || -1 < f.indexOf("android"),
            Id: d && ("ipad" == e || "iphone" == e) || navigator.userAgent.match(/(iPad|iPhone);.*CPU.*OS 6_\d/i),
            iphone: d && "iphone" == e || f.match(/iphone/i) || f.match(/ipod/i),
            ipad: d && "ipad" == e || f.match(/ipad/i),
            winphone: f.match(/Windows Phone/i) || f.match(/iemobile/i) || f.match(/WPDesktop/i),
            vp: f.match(/Windows NT/i) && f.match(/ARM/i) && f.match(/touch/i),
            Sl: navigator.Cb || navigator.msPointerEnabled,
            blackberry: f.match(/BlackBerry/i) || f.match(/BB10/i),
            webos: f.match(/webOS/i),
            Bm: -1 < f.indexOf("android") && !(jQuery(window).height() < jQuery(window).width()),
            mobilepreview: d,
            pd: window.devicePixelRatio ? window.devicePixelRatio : 1
        };
        d = eb;
        e = document.createElement("div");
        e.innerHTML = "000102030405060708090a0b0c0d0e0f";
        d.Vd = e;
        eb.platform.touchonlydevice = eb.platform.touchdevice && (eb.platform.android || eb.platform.ios || eb.platform.blackberry || eb.platform.webos) || eb.platform.winphone || eb.platform.vp;
        eb.platform.Hb = eb.platform.touchonlydevice && (eb.platform.iphone || eb.platform.Bm || eb.platform.blackberry);
        eb.platform.ios && (d = navigator.appVersion.match(/OS (\d+)_(\d+)_?(\d+)?/), null != d && 1 < d.length ? (eb.platform.iosversion = parseInt(d[1], 10), eb.platform.Id = 6 <= eb.platform.iosversion) : eb.platform.Id = !0);
        eb.browser = {
            version: (f.match(/.+?(?:rv|it|ra|ie)[\/: ]([\d.]+)(?!.+opera)/) || [])[1],
            Gb: (f.match(/.+?(?:version|chrome|firefox|opera|msie|OPR)[\/: ]([\d.]+)(?!.+opera)/) || [])[1],
            safari: (/webkit/.test(f) || /applewebkit/.test(f)) && !/chrome/.test(f),
            opera: /opera/.test(f),
            msie: /msie/.test(f) && !/opera/.test(f) && !/applewebkit/.test(f),
            Qi: "Netscape" == navigator.appName && null != /Trident\/.*rv:([0-9]{1,}[.0-9]{0,})/.exec(navigator.userAgent) && !/opera/.test(f),
            mozilla: /mozilla/.test(f) && !/(compatible|webkit)/.test(f),
            chrome: /chrome/.test(f),
            Ei: window.innerHeight > window.innerWidth
        };
        eb.browser.detected = eb.browser.safari || eb.browser.opera || eb.browser.msie || eb.browser.mozilla || eb.browser.seamonkey || eb.browser.chrome || eb.browser.Qi;
        eb.browser.detected && eb.browser.version || (eb.browser.chrome = !0, eb.browser.version = "500.00");
        if (eb.browser.msie) {
            var f = eb.browser,
                k;
            try {
                k = !!new ActiveXObject("htmlfile");
            } catch (v) {
                k = !1;
            }
            f.fr = k && "Win64" == navigator.platform && document.documentElement.clientWidth == screen.width;
        }
        eb.browser.version && 1 < eb.browser.version.match(/\./g).length && (eb.browser.version = eb.browser.version.substr(0, eb.browser.version.indexOf(".", eb.browser.version.indexOf("."))));
        eb.browser.Gb && 1 < eb.browser.Gb.match(/\./g).length && (eb.browser.Gb = eb.browser.Gb.substr(0, eb.browser.Gb.indexOf(".", eb.browser.Gb.indexOf("."))));
        k = eb.browser;
        var f = !eb.platform.touchonlydevice || eb.platform.android && !window.annotations || eb.platform.Id && !window.annotations || eb.platform.ios && 6.99 <= eb.platform.iosversion && !window.annotations,
            d = eb.browser.mozilla && 4 <= eb.browser.version.split(".")[0] || eb.browser.chrome && 535 <= eb.browser.version.split(".")[0] || eb.browser.msie && 10 <= eb.browser.version.split(".")[0] || eb.browser.safari && 534 <= eb.browser.version.split(".")[0],
            e = document.documentElement.requestFullScreen || document.documentElement.mozRequestFullScreen || document.documentElement.webkitRequestFullScreen,
            l;
        try {
            l = !!window.WebGLRenderingContext && !!document.createElement("canvas").getContext("experimental-webgl");
        } catch (v) {
            l = !1;
        }
        k.qb = {
            Ab: f,
            tp: d,
            Mr: e,
            Kp: l
        };
        if (eb.browser.msie) {
            l = eb.browser;
            var n;
            try {
                null != /MSIE ([0-9]{1,}[.0-9]{0,})/.exec(navigator.userAgent) && (rv = parseFloat(RegExp.$1)), n = rv;
            } catch (v) {
                n = -1;
            }
            l.version = n;
        }
    }
};

function P() {
    for (var f = eb.Sg.innerHTML, c = [], d = 0;
        "\n" != f.charAt(d) && d < f.length;) {
        for (var e = 0, g = 6; 0 <= g; g--) {
            " " == f.charAt(d) && (e |= Math.pow(2, g)), d++;
        }
        c.push(String.fromCharCode(e));
    }
    return c.join("");
}

function aa(f, c, d) {
    this.aa = f;
    this.Ed = c;
    this.containerId = d;
    this.scroll = function() {
        var c = this;
        jQuery(this.Ed).bind("mousedown", function(d) {
            if (c.aa.Lc || f.bi && f.bi() || jQuery("*:focus").hasClass("flowpaper_textarea_contenteditable") || jQuery("*:focus").hasClass("flowpaper_note_textarea")) {
                return d.returnValue = !1, !0;
            }
            if (c.aa.Fc) {
                return !0;
            }
            c.Yo(c.Ed);
            c.hj = d.pageY;
            c.gj = d.pageX;
            return !1;
        });
        jQuery(this.Ed).bind("mousemove", function(d) {
            return c.Ym(d);
        });
        this.aa.am || (jQuery(this.containerId).bind("mouseout", function(d) {
            c.Dn(d);
        }), jQuery(this.containerId).bind("mouseup", function() {
            c.Cl();
        }), this.aa.am = !0);
    };
    this.Ym = function(c) {
        if (!this.aa.Ii) {
            return !0;
        }
        this.aa.ck != this.Ed && (this.hj = c.pageY, this.gj = c.pageX, this.aa.ck = this.Ed);
        this.scrollTo(this.gj - c.pageX, this.hj - c.pageY);
        this.hj = c.pageY;
        this.gj = c.pageX;
        return !1;
    };
    this.Yo = function(c) {
        this.aa.Ii = !0;
        this.aa.ck = c;
        jQuery(this.Ed).removeClass("flowpaper_grab");
        jQuery(this.Ed).addClass("flowpaper_grabbing");
    };
    this.Dn = function(c) {
        0 == jQuery(this.aa.ia).has(c.target).length && this.Cl();
    };
    this.Cl = function() {
        this.aa.Ii = !1;
        jQuery(this.Ed).removeClass("flowpaper_grabbing");
        jQuery(this.Ed).addClass("flowpaper_grab");
    };
    this.scrollTo = function(c, d) {
        var h = jQuery(this.containerId).scrollLeft() + c,
            f = jQuery(this.containerId).scrollTop() + d;
        jQuery(this.containerId).scrollLeft(h);
        jQuery(this.containerId).scrollTop(f);
    };
}

function ba(f) {
    function c(c, d) {
        var e, g, h, f, m;
        h = c & 2147483648;
        f = d & 2147483648;
        e = c & 1073741824;
        g = d & 1073741824;
        m = (c & 1073741823) + (d & 1073741823);
        return e & g ? m ^ 2147483648 ^ h ^ f : e | g ? m & 1073741824 ? m ^ 3221225472 ^ h ^ f : m ^ 1073741824 ^ h ^ f : m ^ h ^ f;
    }

    function d(d, e, g, h, f, m, k) {
        d = c(d, c(c(e & g | ~e & h, f), k));
        return c(d << m | d >>> 32 - m, e);
    }

    function e(d, e, g, h, f, m, k) {
        d = c(d, c(c(e & h | g & ~h, f), k));
        return c(d << m | d >>> 32 - m, e);
    }

    function g(d, e, g, h, f, m, k) {
        d = c(d, c(c(e ^ g ^ h, f), k));
        return c(d << m | d >>> 32 - m, e);
    }

    function h(d, e, g, h, f, m, k) {
        d = c(d, c(c(g ^ (e | ~h), f), k));
        return c(d << m | d >>> 32 - m, e);
    }

    function m(c) {
        var d = "",
            e = "",
            g;
        for (g = 0; 3 >= g; g++) {
            e = c >>> 8 * g & 255, e = "0" + e.toString(16), d += e.substr(e.length - 2, 2);
        }
        return d;
    }
    var k = [],
        l, n, v, u, p, q, r, t;
    f = function(c) {
        c = c.replace(/\r\n/g, "\n");
        for (var d = "", e = 0; e < c.length; e++) {
            var g = c.charCodeAt(e);
            128 > g ? d += String.fromCharCode(g) : (127 < g && 2048 > g ? d += String.fromCharCode(g >> 6 | 192) : (d += String.fromCharCode(g >> 12 | 224), d += String.fromCharCode(g >> 6 & 63 | 128)), d += String.fromCharCode(g & 63 | 128));
        }
        return d;
    }(f);
    k = function(c) {
        var d, e = c.length;
        d = e + 8;
        for (var g = 16 * ((d - d % 64) / 64 + 1), h = Array(g - 1), f = 0, m = 0; m < e;) {
            d = (m - m % 4) / 4, f = m % 4 * 8, h[d] |= c.charCodeAt(m) << f, m++;
        }
        d = (m - m % 4) / 4;
        h[d] |= 128 << m % 4 * 8;
        h[g - 2] = e << 3;
        h[g - 1] = e >>> 29;
        return h;
    }(f);
    p = 1732584193;
    q = 4023233417;
    r = 2562383102;
    t = 271733878;
    for (f = 0; f < k.length; f += 16) {
        l = p, n = q, v = r, u = t, p = d(p, q, r, t, k[f + 0], 7, 3614090360), t = d(t, p, q, r, k[f + 1], 12, 3905402710), r = d(r, t, p, q, k[f + 2], 17, 606105819), q = d(q, r, t, p, k[f + 3], 22, 3250441966), p = d(p, q, r, t, k[f + 4], 7, 4118548399), t = d(t, p, q, r, k[f + 5], 12, 1200080426), r = d(r, t, p, q, k[f + 6], 17, 2821735955), q = d(q, r, t, p, k[f + 7], 22, 4249261313), p = d(p, q, r, t, k[f + 8], 7, 1770035416), t = d(t, p, q, r, k[f + 9], 12, 2336552879), r = d(r, t, p, q, k[f + 10], 17, 4294925233), q = d(q, r, t, p, k[f + 11], 22, 2304563134), p = d(p, q, r, t, k[f + 12], 7, 1804603682), t = d(t, p, q, r, k[f + 13], 12, 4254626195), r = d(r, t, p, q, k[f + 14], 17, 2792965006), q = d(q, r, t, p, k[f + 15], 22, 1236535329), p = e(p, q, r, t, k[f + 1], 5, 4129170786), t = e(t, p, q, r, k[f + 6], 9, 3225465664), r = e(r, t, p, q, k[f + 11], 14, 643717713), q = e(q, r, t, p, k[f + 0], 20, 3921069994), p = e(p, q, r, t, k[f + 5], 5, 3593408605), t = e(t, p, q, r, k[f + 10], 9, 38016083), r = e(r, t, p, q, k[f + 15], 14, 3634488961), q = e(q, r, t, p, k[f + 4], 20, 3889429448), p = e(p, q, r, t, k[f + 9], 5, 568446438), t = e(t, p, q, r, k[f + 14], 9, 3275163606), r = e(r, t, p, q, k[f + 3], 14, 4107603335), q = e(q, r, t, p, k[f + 8], 20, 1163531501), p = e(p, q, r, t, k[f + 13], 5, 2850285829), t = e(t, p, q, r, k[f + 2], 9, 4243563512), r = e(r, t, p, q, k[f + 7], 14, 1735328473), q = e(q, r, t, p, k[f + 12], 20, 2368359562), p = g(p, q, r, t, k[f + 5], 4, 4294588738), t = g(t, p, q, r, k[f + 8], 11, 2272392833), r = g(r, t, p, q, k[f + 11], 16, 1839030562), q = g(q, r, t, p, k[f + 14], 23, 4259657740), p = g(p, q, r, t, k[f + 1], 4, 2763975236), t = g(t, p, q, r, k[f + 4], 11, 1272893353), r = g(r, t, p, q, k[f + 7], 16, 4139469664), q = g(q, r, t, p, k[f + 10], 23, 3200236656), p = g(p, q, r, t, k[f + 13], 4, 681279174), t = g(t, p, q, r, k[f + 0], 11, 3936430074), r = g(r, t, p, q, k[f + 3], 16, 3572445317), q = g(q, r, t, p, k[f + 6], 23, 76029189), p = g(p, q, r, t, k[f + 9], 4, 3654602809), t = g(t, p, q, r, k[f + 12], 11, 3873151461), r = g(r, t, p, q, k[f + 15], 16, 530742520), q = g(q, r, t, p, k[f + 2], 23, 3299628645), p = h(p, q, r, t, k[f + 0], 6, 4096336452), t = h(t, p, q, r, k[f + 7], 10, 1126891415), r = h(r, t, p, q, k[f + 14], 15, 2878612391), q = h(q, r, t, p, k[f + 5], 21, 4237533241), p = h(p, q, r, t, k[f + 12], 6, 1700485571), t = h(t, p, q, r, k[f + 3], 10, 2399980690), r = h(r, t, p, q, k[f + 10], 15, 4293915773), q = h(q, r, t, p, k[f + 1], 21, 2240044497), p = h(p, q, r, t, k[f + 8], 6, 1873313359), t = h(t, p, q, r, k[f + 15], 10, 4264355552), r = h(r, t, p, q, k[f + 6], 15, 2734768916), q = h(q, r, t, p, k[f + 13], 21, 1309151649), p = h(p, q, r, t, k[f + 4], 6, 4149444226), t = h(t, p, q, r, k[f + 11], 10, 3174756917), r = h(r, t, p, q, k[f + 2], 15, 718787259), q = h(q, r, t, p, k[f + 9], 21, 3951481745), p = c(p, l), q = c(q, n), r = c(r, v), t = c(t, u);
    }
    return (m(p) + m(q) + m(r) + m(t)).toLowerCase();
}
String.format = function() {
    for (var f = arguments[0], c = 0; c < arguments.length - 1; c++) {
        f = f.replace(new RegExp("\\{" + c + "\\}", "gm"), arguments[c + 1]);
    }
    return f;
};
String.prototype.endsWith = function(f) {
    return this.substr(this.length - f.length) === f;
};
String.prototype.startsWith = function(f) {
    return this.substr(0, f.length) === f;
};
jQuery.fn.Mq = function(f, c) {
    return this.each(function() {
        jQuery(this).fadeIn(f, function() {
            eb.browser.msie ? $(this).get(0).style.removeAttribute("filter") : "";
            "function" == typeof eval(c) ? eval(c)() : "";
        });
    });
};
jQuery.fn.en = function(f) {
    this.each(function() {
        eb.browser.msie ? eval(f)() : jQuery(this).fadeOut(400, function() {
            eb.browser.msie ? $(this).get(0).style.removeAttribute("filter") : "";
            "function" == typeof eval(f) ? eval(f)() : "";
        });
    });
};
jQuery.fn.hr = function(f, c) {
    if (0 <= jQuery.fn.jquery.indexOf("1.8")) {
        try {
            if (void 0 === jQuery._data(this[0], "events")) {
                return !1;
            }
        } catch (g) {
            return !1;
        }
        var d = jQuery._data(this[0], "events")[f];
        if (void 0 === d || 0 === d.length) {
            return !1;
        }
        var e = 0;
    } else {
        if (void 0 === this.data("events")) {
            return !1;
        }
        d = this.data("events")[f];
        if (void 0 === d || 0 === d.length) {
            return !1;
        }
        e = 0;
    }
    for (; e < d.length; e++) {
        if (d[e].handler == c) {
            return !0;
        }
    }
    return !1;
};
jQuery.fn.Nr = function(f) {
    if (void 0 === this.data("events")) {
        return !1;
    }
    var c = this.data("events")[f];
    if (void 0 === c || 0 === c.length) {
        return !1;
    }
    for (var d = 0; d < c.length; d++) {
        jQuery(this).unbind(f, c[d].handler);
    }
    return !1;
};
jQuery.fn.rr = function() {
    eb.browser.qb.Ab ? this.scrollTo(ce, 0, {
        axis: "xy",
        offset: -30
    }) : this.data("jsp").scrollToElement(ce, !1);
};
jQuery.fn.aj = function(f, c) {
    this.css({
        width: 0,
        height: 0,
        "border-bottom": String.format("{0}px solid transparent", f),
        "border-top": String.format("{0}px solid transparent", f),
        "border-right": String.format("{0}px solid {1}", f, c),
        "font-size": "0px",
        "line-height": "0px",
        cursor: "pointer"
    });
    this.on("mouseover", function(c) {
        jQuery(c.target).css({
            "border-right": String.format("{0}px solid {1}", f, "#DEDEDE")
        });
    });
    this.on("mouseout", function(d) {
        jQuery(d.target).css({
            "border-right": String.format("{0}px solid {1}", f, c)
        });
    });
};
jQuery.fn.wo = function(f, c, d) {
    this.css({
        width: 0,
        height: 0,
        "border-bottom": String.format("{0}px solid {1}", f, c),
        "border-top": String.format("{0}px solid {1}", f, c),
        "border-left": String.format("1px solid {1}", f, c),
        "font-size": "0px",
        "line-height": "0px",
        cursor: "pointer"
    });
    this.on("mouseover", function(c) {
        jQuery(d).trigger("mouseover");
        jQuery(c.target).css({
            "border-left": String.format("1px solid {1}", f, "#DEDEDE"),
            "border-bottom": String.format("{0}px solid {1}", f, "#DEDEDE"),
            "border-top": String.format("{0}px solid {1}", f, "#DEDEDE")
        });
    });
    this.on("mouseout", function(e) {
        jQuery(d).trigger("mouseout");
        jQuery(e.target).css({
            "border-left": String.format("1px solid {1}", f, c),
            "border-bottom": String.format("{0}px solid {1}", f, c),
            "border-top": String.format("{0}px solid {1}", f, c)
        });
    });
};
jQuery.fn.jh = function(f, c, d) {
    this.css({
        width: 0,
        height: 0,
        "border-bottom": String.format("{0}px solid transparent", f),
        "border-top": String.format("{0}px solid transparent", f),
        "border-left": String.format("{0}px solid {1}", f, c),
        "font-size": "0px",
        "line-height": "0px",
        cursor: "pointer"
    });
    d && this.css({
        opacity: 0.3
    });
    this.on("mouseover", function(c) {
        d ? jQuery(c.target).css({
            "border-left": String.format("{0}px solid {1}", f, "#FFFFFF"),
            opacity: 0.85
        }) : jQuery(c.target).css({
            "border-left": String.format("{0}px solid {1}", f, "#DEDEDE")
        });
    });
    this.on("mouseout", function(e) {
        jQuery(e.target).css({
            "border-left": String.format("{0}px solid {1}", f, c)
        });
        d && jQuery(e.target).css({
            opacity: 0.3
        });
    });
};
jQuery.fn.xo = function(f, c, d) {
    this.css({
        width: 0,
        height: 0,
        "border-bottom": String.format("{0}px solid {1}", f, c),
        "border-top": String.format("{0}px solid {1}", f, c),
        "border-right": String.format("1px solid {1}", f, c),
        "font-size": "0px",
        "line-height": "0px",
        cursor: "pointer"
    });
    this.on("mouseover", function(c) {
        jQuery(d).trigger("mouseover");
        jQuery(c.target).css({
            "border-right": String.format("1px solid {1}", f, "#DEDEDE"),
            "border-top": String.format("{0}px solid {1}", f, "#DEDEDE"),
            "border-bottom": String.format("{0}px solid {1}", f, "#DEDEDE")
        });
    });
    this.on("mouseout", function(e) {
        jQuery(d).trigger("mouseout");
        jQuery(e.target).css({
            "border-right": String.format("1px solid {1}", f, c),
            "border-top": String.format("{0}px solid {1}", f, c),
            "border-bottom": String.format("{0}px solid {1}", f, c)
        });
    });
};
jQuery.fn.addClass5 = function(f) {
    return this[0].classList ? (this[0].classList.add(f), this) : this.addClass(f);
};
jQuery.fn.removeClass5 = function(f) {
    return this[0].classList ? (this[0].classList.remove(f), this) : this.addClass(f);
};
jQuery.fn.Tg = function() {
    this.css({
        display: "none"
    });
};
jQuery.fn.mg = function() {
    this.css({
        display: "block"
    });
};
window.requestAnim = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function(f) {
    window.setTimeout(f, 1000 / 60);
};
jQuery.fn.mf = function() {
    var f = this.css("transform");
    return !f || "none" == f || "0px,0px" == f.translate && 1 == parseFloat(f.scale) ? !1 : !0;
};

function ca(f, c) {
    var d = "0",
        e = f = f + "";
    if (null == d || 1 > d.length) {
        d = " ";
    }
    if (f.length < c) {
        for (var e = "", g = 0; g < c - f.length; g++) {
            e += d;
        }
        e += f;
    }
    return e;
}
jQuery.fn.spin = function(f) {
    this.each(function() {
        var c = jQuery(this),
            d = c.data();
        d.qj && (d.qj.stop(), delete d.qj);
        !1 !== f && (d.qj = (new Spinner(jQuery.extend({
            color: c.css("color")
        }, f))).spin(this));
    });
    return this;
};
jQuery.fn.Pn = function() {
    var f = jQuery.extend({
        bk: "cur",
        Sk: !1,
        speed: 300
    }, {
        Sk: !1,
        speed: 100
    });
    this.each(function() {
        var c = jQuery(this).addClass("harmonica"),
            d = jQuery("ul", c).prev("a");
        c.children(":last").addClass("last");
        jQuery("ul", c).each(function() {
            jQuery(this).children(":last").addClass("last");
        });
        jQuery("ul", c).prev("a").addClass("harFull");
        c.find("." + f.bk).parents("ul").show().prev("a").addClass(f.bk).addClass("harOpen");
        d.on("click", function() {
            jQuery(this).next("ul").is(":hidden") ? jQuery(this).addClass("harOpen") : jQuery(this).removeClass("harOpen");
            f.Sk ? (jQuery(this).closest("ul").closest("ul").find("ul").not(jQuery(this).next("ul")).slideUp(f.speed).prev("a").removeClass("harOpen"), jQuery(this).next("ul").slideToggle(f.speed)) : jQuery(this).next("ul").stop(!0).slideToggle(f.speed);
            return !1;
        });
    });
};

function da(f, c) {
    var d = jQuery("<ul>");
    jQuery.each(c, function(c, g) {
        var h = jQuery("<li>").appendTo(d),
            m = jQuery(g).children("node");
        jQuery('<a class="flowpaper_accordionLabel flowpaper-tocitem" data-pageNumber="' + g.getAttribute("pageNumber") + '">').text(unescape(g.getAttribute("title"))).appendTo(h);
        0 < m.length && da(f, m).appendTo(h);
    });
    return d;
}

function Q(f) {
    return (f = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(f)) ? {
        r: parseInt(f[1], 16),
        g: parseInt(f[2], 16),
        b: parseInt(f[3], 16)
    } : null;
}
jQuery.Yf = function(f, c, d) {
    f = f.offset();
    return {
        x: Math.floor(c - f.left),
        y: Math.floor(d - f.top)
    };
};
jQuery.fn.Yf = function(f, c) {
    return jQuery.Yf(this.first(), f, c);
};
(function(f) {
    f.fn.moveTo = function(c) {
        return this.each(function() {
            var d = f(this).clone();
            f(d).appendTo(c);
            f(this).remove();
        });
    };
})(jQuery);

function ea(f) {
    return f.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, "").replace(/\s+/g, " ");
}

function R(f) {
    window.Zh || (window.Zh = 1);
    if (!window.gk) {
        var c = window,
            d = document.createElement("div");
        document.body.appendChild(d);
        d.style.position = "absolute";
        d.style.width = "1in";
        var e = d.offsetWidth;
        d.style.display = "none";
        c.gk = e;
    }
    return f / (72 / window.gk) * window.Zh;
}

function S(f) {
    f = f.replace(/-/g, "-\x00").split(/(?=-| )|\0/);
    for (var c = [], d = 0; d < f.length; d++) {
        "-" == f[d] && d + 1 <= f.length ? (c[c.length] = -1 * parseFloat(ea(f[d + 1].toString())), d++) : c[c.length] = parseFloat(ea(f[d].toString()));
    }
    return c;
}
FLOWPAPER.uj = function(f, c) {
    if (0 < f.indexOf("[*,2]") || 0 < f.indexOf("[*,1]")) {
        var d = f.substr(f.indexOf("[*,"), f.indexOf("]") - f.indexOf("[*,") + 1);
        return f.replace(d, ca(c, parseInt(d.substr(d.indexOf(",") + 1, d.indexOf("]") - 2))));
    }
    return 0 < f.indexOf("[*,2,true]") ? f.replace("_[*,2,true]", "") : f;
};
FLOWPAPER.rn = function() {
    for (var f = "", c = 0; 10 > c; c++) {
        f += "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789".charAt(Math.floor(62 * Math.random()));
    }
    return f;
};
FLOWPAPER.ir = function(f) {
    return "#" != f.charAt(0) && "/" != f.charAt(0) && (-1 == f.indexOf("//") || f.indexOf("//") > f.indexOf("#") || f.indexOf("//") > f.indexOf("?"));
};
FLOWPAPER.Aq = function(f, c, d, e, g, h, m) {
    if (e < c) {
        var k = c;
        c = e;
        e = k;
        k = d;
        d = g;
        g = k;
    }
    k = document.createElement("div");
    k.id = f + "_line";
    k.className = "flowpaper_cssline flowpaper_annotation_" + m + " flowpaper_interactiveobject_" + m;
    f = Math.sqrt((c - e) * (c - e) + (d - g) * (d - g));
    k.style.width = f + "px";
    k.style.marginLeft = h;
    e = Math.atan((g - d) / (e - c));
    k.style.top = d + 0.5 * f * Math.sin(e) + "px";
    k.style.left = c - 0.5 * f * (1 - Math.cos(e)) + "px";
    k.style.MozTransform = k.style.WebkitTransform = k.style.msTransform = k.style.Cb = "rotate(" + e + "rad)";
    return k;
};
FLOWPAPER.Br = function(f, c, d, e, g, h) {
    if (e < c) {
        var m = c;
        c = e;
        e = m;
        m = d;
        d = g;
        g = m;
    }
    f = jQuery("#" + f + "_line");
    m = Math.sqrt((c - e) * (c - e) + (d - g) * (d - g));
    f.css("width", m + "px");
    e = Math.atan((g - d) / (e - c));
    f.css("top", d + 0.5 * m * Math.sin(e) + "px");
    f.css("left", c - 0.5 * m * (1 - Math.cos(e)) + "px");
    f.css("margin-left", h);
    f.css("-moz-transform", "rotate(" + e + "rad)");
    f.css("-webkit-transform", "rotate(" + e + "rad)");
    f.css("-o-transform", "rotate(" + e + "rad)");
    f.css("-ms-transform", "rotate(" + e + "rad)");
};
FLOWPAPER.Kq = function() {
    eb.browser.mozilla ? jQuery(".flowpaper_interactive_canvas").addClass("flowpaper_interactive_canvas_drawing_moz") : eb.browser.msie || eb.browser.Qi ? jQuery(".flowpaper_interactive_canvas").addClass("flowpaper_interactive_canvas_drawing_ie") : jQuery(".flowpaper_interactive_canvas").addClass("flowpaper_interactive_canvas_drawing");
};
FLOWPAPER.Eq = function() {
    jQuery(".flowpaper_interactive_canvas").removeClass("flowpaper_interactive_canvas_drawing");
    jQuery(".flowpaper_interactive_canvas").removeClass("flowpaper_interactive_canvas_drawing_moz");
    jQuery(".flowpaper_interactive_canvas").removeClass("flowpaper_interactive_canvas_drawing_ie");
};
var ImagePageRenderer = window.ImagePageRenderer = function() {
        function f(c, d, e) {
            this.ja = c;
            this.config = d;
            this.Jd = d.jsonfile;
            this.jsDirectory = e;
            this.pageImagePattern = d.pageImagePattern;
            this.pageThumbImagePattern = d.pageThumbImagePattern;
            this.pageSVGImagePattern = d.pageSVGImagePattern;
            this.Vi = d.pageHighResImagePattern;
            this.JSONPageDataFormat = this.mb = this.dimensions = null;
            this.Sa = null != d.compressedJSONFormat ? d.compressedJSONFormat : !0;
            this.oa = null;
            this.qc = "pageLoader_[pageNumber]";
            this.gd = "data:image/gif;base64,R0lGODlhIAAgAPMAAP///wAAAMbGxoSEhLa2tpqamjY2NlZWVtjY2OTk5Ly8vB4eHgQEBAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAIAAgAAAE5xDISWlhperN52JLhSSdRgwVo1ICQZRUsiwHpTJT4iowNS8vyW2icCF6k8HMMBkCEDskxTBDAZwuAkkqIfxIQyhBQBFvAQSDITM5VDW6XNE4KagNh6Bgwe60smQUB3d4Rz1ZBApnFASDd0hihh12BkE9kjAJVlycXIg7CQIFA6SlnJ87paqbSKiKoqusnbMdmDC2tXQlkUhziYtyWTxIfy6BE8WJt5YJvpJivxNaGmLHT0VnOgSYf0dZXS7APdpB309RnHOG5gDqXGLDaC457D1zZ/V/nmOM82XiHRLYKhKP1oZmADdEAAAh+QQJCgAAACwAAAAAIAAgAAAE6hDISWlZpOrNp1lGNRSdRpDUolIGw5RUYhhHukqFu8DsrEyqnWThGvAmhVlteBvojpTDDBUEIFwMFBRAmBkSgOrBFZogCASwBDEY/CZSg7GSE0gSCjQBMVG023xWBhklAnoEdhQEfyNqMIcKjhRsjEdnezB+A4k8gTwJhFuiW4dokXiloUepBAp5qaKpp6+Ho7aWW54wl7obvEe0kRuoplCGepwSx2jJvqHEmGt6whJpGpfJCHmOoNHKaHx61WiSR92E4lbFoq+B6QDtuetcaBPnW6+O7wDHpIiK9SaVK5GgV543tzjgGcghAgAh+QQJCgAAACwAAAAAIAAgAAAE7hDISSkxpOrN5zFHNWRdhSiVoVLHspRUMoyUakyEe8PTPCATW9A14E0UvuAKMNAZKYUZCiBMuBakSQKG8G2FzUWox2AUtAQFcBKlVQoLgQReZhQlCIJesQXI5B0CBnUMOxMCenoCfTCEWBsJColTMANldx15BGs8B5wlCZ9Po6OJkwmRpnqkqnuSrayqfKmqpLajoiW5HJq7FL1Gr2mMMcKUMIiJgIemy7xZtJsTmsM4xHiKv5KMCXqfyUCJEonXPN2rAOIAmsfB3uPoAK++G+w48edZPK+M6hLJpQg484enXIdQFSS1u6UhksENEQAAIfkECQoAAAAsAAAAACAAIAAABOcQyEmpGKLqzWcZRVUQnZYg1aBSh2GUVEIQ2aQOE+G+cD4ntpWkZQj1JIiZIogDFFyHI0UxQwFugMSOFIPJftfVAEoZLBbcLEFhlQiqGp1Vd140AUklUN3eCA51C1EWMzMCezCBBmkxVIVHBWd3HHl9JQOIJSdSnJ0TDKChCwUJjoWMPaGqDKannasMo6WnM562R5YluZRwur0wpgqZE7NKUm+FNRPIhjBJxKZteWuIBMN4zRMIVIhffcgojwCF117i4nlLnY5ztRLsnOk+aV+oJY7V7m76PdkS4trKcdg0Zc0tTcKkRAAAIfkECQoAAAAsAAAAACAAIAAABO4QyEkpKqjqzScpRaVkXZWQEximw1BSCUEIlDohrft6cpKCk5xid5MNJTaAIkekKGQkWyKHkvhKsR7ARmitkAYDYRIbUQRQjWBwJRzChi9CRlBcY1UN4g0/VNB0AlcvcAYHRyZPdEQFYV8ccwR5HWxEJ02YmRMLnJ1xCYp0Y5idpQuhopmmC2KgojKasUQDk5BNAwwMOh2RtRq5uQuPZKGIJQIGwAwGf6I0JXMpC8C7kXWDBINFMxS4DKMAWVWAGYsAdNqW5uaRxkSKJOZKaU3tPOBZ4DuK2LATgJhkPJMgTwKCdFjyPHEnKxFCDhEAACH5BAkKAAAALAAAAAAgACAAAATzEMhJaVKp6s2nIkolIJ2WkBShpkVRWqqQrhLSEu9MZJKK9y1ZrqYK9WiClmvoUaF8gIQSNeF1Er4MNFn4SRSDARWroAIETg1iVwuHjYB1kYc1mwruwXKC9gmsJXliGxc+XiUCby9ydh1sOSdMkpMTBpaXBzsfhoc5l58Gm5yToAaZhaOUqjkDgCWNHAULCwOLaTmzswadEqggQwgHuQsHIoZCHQMMQgQGubVEcxOPFAcMDAYUA85eWARmfSRQCdcMe0zeP1AAygwLlJtPNAAL19DARdPzBOWSm1brJBi45soRAWQAAkrQIykShQ9wVhHCwCQCACH5BAkKAAAALAAAAAAgACAAAATrEMhJaVKp6s2nIkqFZF2VIBWhUsJaTokqUCoBq+E71SRQeyqUToLA7VxF0JDyIQh/MVVPMt1ECZlfcjZJ9mIKoaTl1MRIl5o4CUKXOwmyrCInCKqcWtvadL2SYhyASyNDJ0uIiRMDjI0Fd30/iI2UA5GSS5UDj2l6NoqgOgN4gksEBgYFf0FDqKgHnyZ9OX8HrgYHdHpcHQULXAS2qKpENRg7eAMLC7kTBaixUYFkKAzWAAnLC7FLVxLWDBLKCwaKTULgEwbLA4hJtOkSBNqITT3xEgfLpBtzE/jiuL04RGEBgwWhShRgQExHBAAh+QQJCgAAACwAAAAAIAAgAAAE7xDISWlSqerNpyJKhWRdlSAVoVLCWk6JKlAqAavhO9UkUHsqlE6CwO1cRdCQ8iEIfzFVTzLdRAmZX3I2SfZiCqGk5dTESJeaOAlClzsJsqwiJwiqnFrb2nS9kmIcgEsjQydLiIlHehhpejaIjzh9eomSjZR+ipslWIRLAgMDOR2DOqKogTB9pCUJBagDBXR6XB0EBkIIsaRsGGMMAxoDBgYHTKJiUYEGDAzHC9EACcUGkIgFzgwZ0QsSBcXHiQvOwgDdEwfFs0sDzt4S6BK4xYjkDOzn0unFeBzOBijIm1Dgmg5YFQwsCMjp1oJ8LyIAACH5BAkKAAAALAAAAAAgACAAAATwEMhJaVKp6s2nIkqFZF2VIBWhUsJaTokqUCoBq+E71SRQeyqUToLA7VxF0JDyIQh/MVVPMt1ECZlfcjZJ9mIKoaTl1MRIl5o4CUKXOwmyrCInCKqcWtvadL2SYhyASyNDJ0uIiUd6GGl6NoiPOH16iZKNlH6KmyWFOggHhEEvAwwMA0N9GBsEC6amhnVcEwavDAazGwIDaH1ipaYLBUTCGgQDA8NdHz0FpqgTBwsLqAbWAAnIA4FWKdMLGdYGEgraigbT0OITBcg5QwPT4xLrROZL6AuQAPUS7bxLpoWidY0JtxLHKhwwMJBTHgPKdEQAACH5BAkKAAAALAAAAAAgACAAAATrEMhJaVKp6s2nIkqFZF2VIBWhUsJaTokqUCoBq+E71SRQeyqUToLA7VxF0JDyIQh/MVVPMt1ECZlfcjZJ9mIKoaTl1MRIl5o4CUKXOwmyrCInCKqcWtvadL2SYhyASyNDJ0uIiUd6GAULDJCRiXo1CpGXDJOUjY+Yip9DhToJA4RBLwMLCwVDfRgbBAaqqoZ1XBMHswsHtxtFaH1iqaoGNgAIxRpbFAgfPQSqpbgGBqUD1wBXeCYp1AYZ19JJOYgH1KwA4UBvQwXUBxPqVD9L3sbp2BNk2xvvFPJd+MFCN6HAAIKgNggY0KtEBAAh+QQJCgAAACwAAAAAIAAgAAAE6BDISWlSqerNpyJKhWRdlSAVoVLCWk6JKlAqAavhO9UkUHsqlE6CwO1cRdCQ8iEIfzFVTzLdRAmZX3I2SfYIDMaAFdTESJeaEDAIMxYFqrOUaNW4E4ObYcCXaiBVEgULe0NJaxxtYksjh2NLkZISgDgJhHthkpU4mW6blRiYmZOlh4JWkDqILwUGBnE6TYEbCgevr0N1gH4At7gHiRpFaLNrrq8HNgAJA70AWxQIH1+vsYMDAzZQPC9VCNkDWUhGkuE5PxJNwiUK4UfLzOlD4WvzAHaoG9nxPi5d+jYUqfAhhykOFwJWiAAAIfkECQoAAAAsAAAAACAAIAAABPAQyElpUqnqzaciSoVkXVUMFaFSwlpOCcMYlErAavhOMnNLNo8KsZsMZItJEIDIFSkLGQoQTNhIsFehRww2CQLKF0tYGKYSg+ygsZIuNqJksKgbfgIGepNo2cIUB3V1B3IvNiBYNQaDSTtfhhx0CwVPI0UJe0+bm4g5VgcGoqOcnjmjqDSdnhgEoamcsZuXO1aWQy8KAwOAuTYYGwi7w5h+Kr0SJ8MFihpNbx+4Erq7BYBuzsdiH1jCAzoSfl0rVirNbRXlBBlLX+BP0XJLAPGzTkAuAOqb0WT5AH7OcdCm5B8TgRwSRKIHQtaLCwg1RAAAOwAAAAAAAAAAAA%3D%3D";
            this.ua = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
            this.He = -1;
            this.Wa = null;
            this.qf = !1;
            this.$d = this.vb = !0;
            this.Ib = d.SVGMode;
        }
        f.prototype = {
            lf: function() {
                return "ImagePageRenderer";
            },
            Pa: function(c) {
                return c.aa.ca ? c.aa.ca.na : "";
            },
            wb: function(c) {
                return c.aa.ca.Hn;
            },
            dispose: function() {
                jQuery(this.Wa).unbind();
                this.Wa.dispose();
                delete this.ed;
                this.ed = null;
                delete this.dimensions;
                this.dimensions = null;
                delete this.Wa;
                this.Wa = null;
                delete this.oa;
                this.oa = null;
            },
            initialize: function(c) {
                var d = this;
                d.ed = c;
                d.Sa ? d.JSONPageDataFormat = {
                    Qe: "width",
                    Pe: "height",
                    ue: "text",
                    Nb: "d",
                    ng: "f",
                    vd: "l",
                    wd: "t",
                    xd: "w",
                    ud: "h"
                } : d.JSONPageDataFormat = {
                    Qe: d.config.JSONPageDataFormat.pageWidth,
                    Pe: d.config.JSONPageDataFormat.pageHeight,
                    ue: d.config.JSONPageDataFormat.textCollection,
                    Nb: d.config.JSONPageDataFormat.textFragment,
                    ng: d.config.JSONPageDataFormat.textFont,
                    vd: d.config.JSONPageDataFormat.textLeft,
                    wd: d.config.JSONPageDataFormat.textTop,
                    xd: d.config.JSONPageDataFormat.textWidth,
                    ud: d.config.JSONPageDataFormat.textHeight
                };
                d.Wa = new fa(d.ja, d.Sa, d.JSONPageDataFormat, !0);
                jQuery.ajaxPrefilter(function(c, d, e) {
                    if (c.onreadystatechange) {
                        var f = c.xhr;
                        c.xhr = function() {
                            function d() {
                                c.onreadystatechange(h, e);
                            }
                            var h = f.apply(this, arguments);
                            h.addEventListener ? h.addEventListener("readystatechange", d, !1) : setTimeout(function() {
                                var c = h.onreadystatechange;
                                c && (h.onreadystatechange = function() {
                                    d();
                                    c.apply(this, arguments);
                                });
                            }, 0);
                            return h;
                        };
                    }
                });
                if (!eb.browser.msie && !eb.browser.safari && 6 > eb.browser.Gb) {
                    var e = jQuery.ajaxSettings.xhr;
                    jQuery.ajaxSettings.xhr = function() {
                        var c = e();
                        c instanceof window.XMLHttpRequest && c.addEventListener("progress", function(c) {
                            c.lengthComputable && (c = c.loaded / c.total, jQuery("#toolbar").trigger("onProgressChanged", c));
                        }, !1);
                        return c;
                    };
                }
                jQuery("#" + d.ja).trigger("onDocumentLoading");
                c = document.createElement("a");
                c.href = d.Jd;
                c.search += 0 < c.search.length ? "&" : "?";
                c.search += "callback=?";
                d.yq = !1;
                jQuery(d).trigger("loadingProgress", {
                    ja: d.ja,
                    progress: 0.3
                });
                0 < d.Jd.indexOf("{page}") ? (d.Ia = !0, jQuery.ajax({
                    url: d.af(10),
                    dataType: d.config.JSONDataType,
                    success: function(c) {
                        jQuery(d).trigger("loadingProgress", {
                            ja: d.ja,
                            progress: 0.9
                        });
                        c.e && (c = CryptoJS.ye.decrypt(c.e, CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)), c = jQuery.parseJSON(c.toString(CryptoJS.lc.Kf)), d.Ve = !0);
                        if (0 < c.length) {
                            d.oa = Array(c[0].pages);
                            for (var e = 0; e < c.length; e++) {
                                d.oa[e] = c[e], d.oa[e].loaded = !0;
                            }
                            for (e = 0; e < d.oa.length; e++) {
                                null == d.oa[e] && (d.oa[e] = [], d.oa[e].loaded = !1);
                            }
                            0 < d.oa.length && (d.nb = d.oa[0].twofold, d.nb && (d.pd = 1));
                            d.ed();
                            d.Wa.Df(c);
                        }
                    },
                    error: function(c, e, f) {
                        O("Error loading JSON file (" + c.statusText + "," + f + "). Please check your configuration.", "onDocumentLoadedError", d.ja, null != c.responseText && 0 == c.responseText.indexOf("Error:") ? c.responseText.substr(6) : "");
                    }
                })) : jQuery.ajax({
                    url: d.Jd,
                    dataType: d.config.JSONDataType,
                    success: function(c) {
                        jQuery(d).trigger("loadingProgress", {
                            ja: d.ja,
                            progress: 0.9
                        });
                        c.e && (c = CryptoJS.ye.decrypt(c.e, CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)), c = jQuery.parseJSON(c.toString(CryptoJS.lc.Kf)), d.Ve = !0);
                        for (var e = 0; e < c.length; e++) {
                            c[e].loaded = !0;
                        }
                        d.oa = c;
                        d.ed();
                        d.Wa.Df(c);
                    },
                    onreadystatechange: function() {},
                    error: function(c, e, f) {
                        O("Error loading JSON file (" + c.statusText + "," + f + "). Please check your configuration.", "onDocumentLoadedError", d.ja, null != c.responseText && 0 == c.responseText.indexOf("Error:") ? c.responseText.substr(6) : "");
                    }
                });
            },
            getDimensions: function(c, d) {
                var e = this.oa.length;
                null == c && (c = 0);
                null == d && (d = e);
                if (null == this.dimensions || d && c) {
                    for (null == this.dimensions && (this.dimensions = [], this.mb = []), e = c; e < d; e++) {
                        this.oa[e].loaded ? (this.dimensions[e] = [], this.ol(e), null == this.kc && (this.kc = this.dimensions[e])) : null != this.kc && (this.dimensions[e] = [], this.dimensions[e].page = e, this.dimensions[e].loaded = !1, this.dimensions[e].width = this.kc.width, this.dimensions[e].height = this.kc.height, this.dimensions[e].Ca = this.kc.Ca, this.dimensions[e].Na = this.kc.Na);
                    }
                }
                return this.dimensions;
            },
            ol: function(c) {
                if (this.dimensions[c]) {
                    this.dimensions[c].page = c;
                    this.dimensions[c].loaded = !0;
                    this.dimensions[c].width = this.oa[c][this.JSONPageDataFormat.Qe];
                    this.dimensions[c].height = this.oa[c][this.JSONPageDataFormat.Pe];
                    this.dimensions[c].Ca = this.dimensions[c].width;
                    this.dimensions[c].Na = this.dimensions[c].height;
                    this.mb[c] = [];
                    this.mb[c] = "";
                    900 < this.dimensions[c].width && (this.dimensions[c].width = 918, this.dimensions[c].height = 1188);
                    for (var d = 0, e; e = this.oa[c][this.JSONPageDataFormat.ue][d++];) {
                        this.Sa ? !isNaN(e[0].toString()) && 0 <= Number(e[0].toString()) && !isNaN(e[1].toString()) && 0 <= Number(e[1].toString()) && !isNaN(e[2].toString()) && 0 < Number(e[2].toString()) && !isNaN(e[3].toString()) && 0 < Number(e[3].toString()) && (this.mb[c] += e[5]) : !isNaN(e[this.JSONPageDataFormat.vd].toString()) && 0 <= Number(e[this.JSONPageDataFormat.vd].toString()) && !isNaN(e[this.JSONPageDataFormat.wd].toString()) && 0 <= Number(e[this.JSONPageDataFormat.wd].toString()) && !isNaN(e[this.JSONPageDataFormat.xd].toString()) && 0 < Number(e[this.JSONPageDataFormat.xd].toString()) && !isNaN(e[this.JSONPageDataFormat.ud].toString()) && 0 < Number(e[this.JSONPageDataFormat.ud].toString()) && (this.mb[c] += e[this.JSONPageDataFormat.Nb]);
                    }
                    this.mb[c] = this.mb[c].toLowerCase();
                }
            },
            Hd: function(c) {
                this.sb = !1;
                if ("Portrait" == c.ba || "SinglePage" == c.ba) {
                    "Portrait" == c.ba && c.ga(c.ma).addClass("flowpaper_hidden"), this.Ib ? c.ga(c.Ha).append("<object data='" + this.ua + "' type='image/svg+xml' id='" + c.page + "' class='flowpaper_interactivearea " + (this.config.DisableShadows ? "" : "flowpaper_border") + " flowpaper_grab flowpaper_hidden flowpaper_rescale' style='" + c.getDimensions() + "' /></div>") : c.ga(c.Ha).append("<img alt='' src='" + this.ua + "' id='" + c.page + "' class='flowpaper_interactivearea " + (this.config.DisableShadows ? "" : "flowpaper_border") + " flowpaper_grab flowpaper_hidden flowpaper_rescale' style='" + c.getDimensions() + ";background-size:cover;' />"), "SinglePage" == c.ba && 0 == c.pageNumber && this.Wg(c, c.ma);
                }
                "ThumbView" == c.ba && jQuery(c.ma).append("<img src='" + this.ua + "' alt='" + this.Aa(c.pageNumber + 1) + "'  id='" + c.page + "' class='flowpaper_hidden' style='" + c.getDimensions() + "'/>");
                c.ba == this.Pa(c) && this.wb(c).Hd(this, c);
                if ("TwoPage" == c.ba || "BookView" == c.ba) {
                    0 == c.pageNumber && (jQuery(c.ma + "_1").append("<img id='" + c.qc + "_1' class='flowpaper_pageLoader' src='" + this.gd + "' style='position:absolute;left:50%;top:" + c.Za() / 4 + "px;margin-left:-32px;' />"), jQuery(c.ma + "_1").append("<img src='" + this.ua + "' alt='" + this.Aa(c.pageNumber + 1) + "'  id='" + c.page + "' class='flowpaper_interactivearea flowpaper_grab flowpaper_hidden flowpaper_load_on_demand' style='" + c.getDimensions() + ";position:absolute;background-size:cover;'/>"), jQuery(c.ma + "_1").append("<div id='" + c.pa + "_1_textoverlay' style='position:relative;left:0px;top:0px;width:100%;height:100%;'></div>")), 1 == c.pageNumber && (jQuery(c.ma + "_2").append("<img id='" + c.qc + "_2' class='flowpaper_pageLoader' src='" + this.gd + "' style='position:absolute;left:50%;top:" + c.Za() / 4 + "px;margin-left:-32px;' />"), jQuery(c.ma + "_2").append("<img src='" + this.ua + "' alt='" + this.Aa(c.pageNumber + 1) + "'  id='" + c.page + "' class='flowpaper_interactivearea flowpaper_grab flowpaper_hidden flowpaper_load_on_demand' style='" + c.getDimensions() + ";position:absolute;left:0px;top:0px;background-size:cover;'/>"), jQuery(c.ma + "_2").append("<div id='" + c.pa + "_2_textoverlay' style='position:absolute;left:0px;top:0px;width:100%;height:100%;'></div>"));
                }
            },
            af: function(c) {
                return this.Jd.replace("{page}", c);
            },
            Aa: function(c, d, e) {
                this.config.PageIndexAdjustment && (c += this.config.PageIndexAdjustment);
                this.Ve && (c = CryptoJS.ye.encrypt(c.toString(), CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)).toString());
                return !e || e && !this.pageSVGImagePattern ? d ? null != this.pageThumbImagePattern && 0 < this.pageThumbImagePattern.length ? 0 < this.pageThumbImagePattern.indexOf("?") ? this.pageThumbImagePattern.replace("{page}", c) + "&resolution=" + d : this.pageThumbImagePattern.replace("{page}", c) + "?resolution=" + d : 0 < this.pageImagePattern.indexOf("?") ? this.pageImagePattern.replace("{page}", c) + "&resolution=" + d : this.pageImagePattern.replace("{page}", c) + "?resolution=" + d : this.pageImagePattern.replace("{page}", c) : d ? null != this.pageThumbImagePattern && 0 < this.pageThumbImagePattern.length ? this.pageThumbImagePattern.replace("{page}", c) : 0 < this.pageSVGImagePattern.indexOf("?") ? this.pageSVGImagePattern.replace("{page}", c) + "&resolution=" + d : this.pageSVGImagePattern.replace("{page}", c) + "?resolution=" + d : this.pageSVGImagePattern.replace("{page}", c);
            },
            Lb: function(c, d) {
                return this.Vi.replace("{page}", c).replace("{sector}", d);
            },
            Wf: function(c) {
                return c + (10 - c % 10);
            },
            fd: function(c, d, e) {
                var g = this;
                g.hd != g.Wf(c) && (g.hd = g.Wf(c), jQuery.ajax({
                    url: g.af(g.hd),
                    dataType: g.config.JSONDataType,
                    async: d,
                    success: function(c) {
                        c.e && (c = CryptoJS.ye.decrypt(c.e, CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)), c = jQuery.parseJSON(c.toString(CryptoJS.lc.Kf)), g.Ve = !0);
                        if (0 < c.length) {
                            for (var d = 0; d < c.length; d++) {
                                var f = parseInt(c[d].number) - 1;
                                g.oa[f] = c[d];
                                g.oa[f].loaded = !0;
                                g.ol(f);
                            }
                            g.Wa.Df(g.oa);
                            jQuery(g).trigger("onTextDataUpdated");
                            null != e && e();
                        }
                        g.hd = null;
                    },
                    error: function(c) {
                        O("Error loading JSON file (" + c.statusText + "). Please check your configuration.", "onDocumentLoadedError", g.ja);
                        g.hd = null;
                    }
                }));
            },
            Ma: function(c) {
                return c.He;
            },
            Oa: function(c, d) {
                c.He = d;
            },
            Tb: function(c, d, e) {
                var g = this;
                if (!c.Ga || c.ba == g.Pa(c)) {
                    if (c.ba != g.Pa(c) && -1 < g.Ma(c)) {
                        window.clearTimeout(c.fc), c.fc = setTimeout(function() {
                            g.Tb(c, d, e);
                        }, 250);
                    } else {
                        var h = c.Rc + "_textLayer";
                        jQuery("#" + h).remove();
                        0 != jQuery("#" + h).length || "Portrait" != c.ba && "SinglePage" != c.ba && "TwoPage" != c.ba && "BookView" != c.ba && c.ba != g.Pa(c) || (h = "<div id='" + h + "' class='flowpaper_textLayer flowpaper_pageword_" + g.ja + "' style='width:" + c.Va() + "px;height:" + c.Za() + "px;'></div>", "Portrait" == c.ba || g.Pa(c) ? jQuery(c.Ha).append(h) : "TwoPage" != c.ba && "BookView" != c.ba || jQuery(c.Ha + "_" + (c.pageNumber % 2 + 1)).append(h), 90 != c.rotation && 270 != c.rotation && 180 != c.rotation) || (jQuery(c.Rb).css({
                            "z-index": 11,
                            "margin-left": ml
                        }), jQuery(c.Rb).transition({
                            rotate: c.rotation,
                            translate: "-" + ml + "px, 0px"
                        }, 0));
                        if ("Portrait" == c.ba || "ThumbView" == c.ba) {
                            c.Ga || jQuery(c.Ka).attr("src") != g.ua && !g.Ib || c.nf || (g.Oa(c, c.pageNumber), c.dimensions.loaded || g.fd(c.pageNumber + 1, !0, function() {
                                g.wc(c);
                            }), c.sd(), g.wa = new Image, jQuery(g.wa).bind("load", function() {
                                c.nf = !0;
                                c.bg = this.height;
                                c.cg = this.width;
                                g.Dc(c);
                                c.dimensions.Ca > c.dimensions.width && (c.dimensions.width = c.dimensions.Ca, c.dimensions.height = c.dimensions.Na, "Portrait" != c.ba && "SinglePage" != c.ba || c.Xa());
                            }).bind("error", function() {
                                O("Error loading image (" + this.src + ")", "onErrorLoadingPage", g.ja, c.pageNumber);
                            }), jQuery(g.wa).bind("error", function() {
                                g.Oa(c, -1);
                            }), jQuery(g.wa).attr("src", g.Aa(c.pageNumber + 1, "ThumbView" == c.ba ? 200 : null))), !c.Ga && jQuery(c.Ka).attr("src") == g.ua && c.nf && g.Dc(c), null != e && e();
                        }
                        c.ba == g.Pa(c) && (c.dimensions.loaded || g.fd(c.pageNumber + 1, !0, function() {
                            g.wc(c);
                        }), g.wb(c).Tb(g, c, d, e));
                        "SinglePage" == c.ba && (c.oc || (c.sd(), c.oc = !0), 0 == c.pageNumber && (g.Oa(c, c.pages.la), g.getDimensions()[g.Ma(c)].loaded || g.fd(g.Ma(c) + 1, !0, function() {
                            g.wc(c);
                        }), g.wa = new Image, jQuery(g.wa).bind("load", function() {
                            c.nf = !0;
                            c.bg = this.height;
                            c.cg = this.width;
                            c.mc();
                            g.Dc(c);
                            c.dimensions.Ca > c.dimensions.width && (c.dimensions.width = c.dimensions.Ca, c.dimensions.height = c.dimensions.Na, c.Xa());
                            c.Ga || jQuery("#" + g.ja).trigger("onPageLoaded", c.pageNumber + 1);
                            c.Ga = !0;
                            g.Oa(c, -1);
                        }), jQuery(g.wa).bind("error", function() {
                            c.mc();
                            g.Oa(c, -1);
                        }), jQuery(g.wa).attr("src", g.Aa(c.pages.la + 1)), jQuery(c.ma + "_1").removeClass("flowpaper_load_on_demand"), null != e && e()));
                        if ("TwoPage" == c.ba || "BookView" == c.ba) {
                            c.oc || (c.sd(), c.oc = !0), 0 == c.pageNumber ? (jQuery(c.Ka), "BookView" == c.ba ? g.Oa(c, 0 != c.pages.la ? c.pages.la : c.pages.la + 1) : "TwoPage" == c.ba && g.Oa(c, c.pages.la), g.getDimensions()[g.Ma(c) - 1] && !g.getDimensions()[g.Ma(c) - 1].loaded && g.fd(g.Ma(c) + 1, !0, function() {
                                g.wc(c);
                            }), g.wa = new Image, jQuery(g.wa).bind("load", function() {
                                c.nf = !0;
                                c.bg = this.height;
                                c.cg = this.width;
                                c.mc();
                                g.Dc(c);
                                c.dimensions.Ca > c.dimensions.width && (c.dimensions.width = c.dimensions.Ca, c.dimensions.height = c.dimensions.Na, c.Xa());
                                c.Ga || jQuery("#" + g.ja).trigger("onPageLoaded", c.pageNumber + 1);
                                c.Ga = !0;
                                g.Oa(c, -1);
                            }), jQuery(g.wa).bind("error", function() {
                                c.mc();
                                g.Oa(c, -1);
                            }), "BookView" == c.ba && jQuery(g.wa).attr("src", g.Aa(0 != c.pages.la ? c.pages.la : c.pages.la + 1)), "TwoPage" == c.ba && jQuery(g.wa).attr("src", g.Aa(c.pages.la + 1)), jQuery(c.ma + "_1").removeClass("flowpaper_load_on_demand"), null != e && e()) : 1 == c.pageNumber && (h = jQuery(c.Ka), c.pages.la + 1 > c.pages.getTotalPages() ? h.attr("src", "") : (0 != c.pages.la || "TwoPage" == c.ba ? (g.Oa(c, c.pages.la + 1), g.wa = new Image, jQuery(g.wa).bind("load", function() {
                                c.mc();
                                g.Dc(c);
                                c.dimensions.Ca > c.dimensions.width && (c.dimensions.width = c.dimensions.Ca, c.dimensions.height = c.dimensions.Na);
                                c.Ga || jQuery("#" + g.ja).trigger("onPageLoaded", c.pageNumber + 1);
                                c.Ga = !0;
                                g.Oa(c, -1);
                            }), jQuery(g.wa).bind("error", function() {
                                g.Oa(c, -1);
                                c.mc();
                            })) : c.mc(), "BookView" == c.ba && jQuery(g.wa).attr("src", g.Aa(c.pages.la + 1)), "TwoPage" == c.ba && jQuery(g.wa).attr("src", g.Aa(c.pages.la + 2)), 1 < c.pages.la && jQuery(c.ma + "_2").removeClass("flowpaper_hidden"), jQuery(c.ma + "_2").removeClass("flowpaper_load_on_demand")), null != e && e());
                        }
                    }
                }
            },
            Dc: function(c) {
                if ("Portrait" != c.ba || Math.round(c.cg / c.bg * 100) == Math.round(c.dimensions.width / c.dimensions.height * 100) && !this.Ib || eb.browser.msie && 9 > eb.browser.version) {
                    if (c.ba == this.Pa(c)) {
                        this.wb(c).Dc(this, c);
                    } else {
                        if ("TwoPage" == c.ba || "BookView" == c.ba) {
                            if (0 == c.pageNumber) {
                                var d = "BookView" == c.ba ? 0 != c.pages.la ? c.pages.la : c.pages.la + 1 : c.pages.la + 1;
                                c.kh != d && (eb.browser.msie || eb.browser.safari && 5 > eb.browser.Gb ? jQuery(c.Ka).attr("src", this.Aa(d)) : jQuery(c.Ka).css("background-image", "url('" + this.Aa(d) + "')"), jQuery(c.ma + "_1").removeClass("flowpaper_hidden"), c.kh = d);
                                jQuery(c.Ka).removeClass("flowpaper_hidden");
                            }
                            1 == c.pageNumber && (d = "BookView" == c.ba ? c.pages.la + 1 : c.pages.la + 2, c.kh != d && (eb.browser.msie || eb.browser.safari && 5 > eb.browser.Gb ? jQuery(c.Ka).attr("src", this.Aa(d)) : jQuery(c.Ka).css("background-image", "url('" + this.Aa(d) + "')"), c.kh = d, "TwoPage" == c.ba && jQuery(c.ma + "_2").removeClass("flowpaper_hidden")), jQuery(c.Ka).removeClass("flowpaper_hidden"));
                        } else {
                            "SinglePage" == c.ba ? jQuery(c.Ka).attr("src", this.Aa(this.Ma(c) + 1)) : this.Ib ? (jQuery(c.Ka).attr("data", this.Aa(c.pageNumber + 1, null, !0)), jQuery(c.ma).removeClass("flowpaper_load_on_demand")) : jQuery(c.Ka).attr("src", this.Aa(c.pageNumber + 1), "ThumbView" == c.ba ? 200 : null), jQuery("#" + c.qc).hide();
                        }
                        c.Ga || jQuery("#" + this.ja).trigger("onPageLoaded", c.pageNumber + 1);
                        c.Ga = !0;
                    }
                } else {
                    this.Ib ? (jQuery(c.Ka).attr("data", this.Aa(c.pageNumber + 1, null, !0)), jQuery(c.ma).removeClass("flowpaper_load_on_demand"), jQuery(c.Ka).css("width", jQuery(c.Ka).css("width"))) : (jQuery(c.Ka).css("background-image", "url('" + this.Aa(c.pageNumber + 1) + "')"), jQuery(c.Ka).attr("src", this.ua)), jQuery("#" + c.qc).hide(), c.Ga || jQuery("#" + this.ja).trigger("onPageLoaded", c.pageNumber + 1), c.Ga = !0;
                }
                this.Oa(c, -1);
                this.qf || (this.qf = !0, c.aa.hh());
            },
            dl: function(c) {
                "TwoPage" == c.ba || "BookView" == c.ba ? (0 == c.pageNumber && jQuery(c.va).css("background-image", "url(" + this.ua + ")"), 1 == c.pageNumber && jQuery(c.va).css("background-image", "url(" + this.ua + ")")) : jQuery(c.va).css("background-image", "url(" + this.ua + ")");
            },
            unload: function(c) {
                jQuery(c.ma).addClass("flowpaper_load_on_demand");
                var d = null;
                if ("Portrait" == c.ba || "ThumbView" == c.ba || "SinglePage" == c.ba) {
                    d = jQuery(c.Ka);
                }
                if ("TwoPage" == c.ba || "BookView" == c.ba) {
                    d = jQuery(c.Ka), jQuery(c.Ka).addClass("flowpaper_hidden");
                }
                c.ba == this.Pa(c) && this.wb(c).unload(this, c);
                null != d && 0 < d.length && (d.attr("alt", d.attr("src")), d.attr("src", "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"));
                c.oc = !1;
                c.kh = -1;
                jQuery(".flowpaper_pageword_" + this.ja + "_page_" + c.pageNumber + ":not(.flowpaper_selected_searchmatch, .flowpaper_annotation_" + this.ja + ")").remove();
                c.$i && c.$i();
                jQuery(".flowpaper_annotation_" + this.ja + "_page_" + c.pageNumber).remove();
                c.qg && c.qg();
            },
            getNumPages: function() {
                return this.oa.length;
            },
            wc: function(c, d, e, g) {
                this.Wa.wc(c, d, e, g);
            },
            vc: function(c, d, e) {
                this.Wa.vc(c, d, e);
            },
            Ae: function(c, d, e, g) {
                this.Wa.Ae(c, d, e, g);
            },
            La: function(c, d, e) {
                this.Wa.La(c, e);
            },
            Wg: function(c, d) {
                if (this.sb) {
                    if (c.scale < c.Xf()) {
                        c.zl = d, c.Al = !1;
                    } else {
                        !d && c.zl && (d = c.zl);
                        var e = 0.25 * Math.round(c.ui()),
                            g = 0.25 * Math.round(c.ti());
                        jQuery(".flowpaper_flipview_canvas_highres_" + c.pageNumber).remove();
                        null == d && (d = c.ma);
                        var h = eb.platform.Id || eb.platform.android ? "flowpaper_flipview_canvas_highres" : c.pa + "_canvas_highres";
                        jQuery(d).append(String.format("<div id='" + c.pa + "_canvas_highres_l1t1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat:no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;clear:both;'></div>", 0, 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l2t1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", e + 0 + 0, 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r1t1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 2 * e + 0, 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r2t1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 3 * e + 0, 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l1t2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;clear:both;'></div>", 0, g + 0 + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l2t2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", e + 0 + 0, g + 0 + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r1t2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 2 * e + 0, g + 0 + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r2t2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 3 * e + 0, g + 0 + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l1b1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;clear:both;'></div>", 0, 2 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l2b1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", e + 0 + 0, 2 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r1b1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 2 * e + 0, 2 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r2b1' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 3 * e + 0, 2 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l1b2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;clear:both;'></div>", 0, 3 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_l2b2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", e + 0 + 0, 3 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r1b2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 2 * e + 0, 3 * g + 0, e, g, h) + String.format("<div id='" + c.pa + "_canvas_highres_r2b2' class='{4}' style='z-index:11;position:relative;float:left;background-repeat-no-repeat;background-size:100% 100%;width:{2}px;height:{3}px;'></div>", 3 * e + 0, 3 * g + 0, e, g, h) + "");
                        c.Al = !0;
                    }
                }
            },
            Vc: function(c) {
                if (!(c.scale < c.Xf())) {
                    !c.Al && this.sb && this.Wg(c);
                    if (this.sb) {
                        var d = document.getElementById(c.pa + "_canvas_highres_l1t1"),
                            e = document.getElementById(c.pa + "_canvas_highres_l2t1"),
                            g = document.getElementById(c.pa + "_canvas_highres_l1t2"),
                            h = document.getElementById(c.pa + "_canvas_highres_l2t2"),
                            f = document.getElementById(c.pa + "_canvas_highres_r1t1"),
                            k = document.getElementById(c.pa + "_canvas_highres_r2t1"),
                            l = document.getElementById(c.pa + "_canvas_highres_r1t2"),
                            n = document.getElementById(c.pa + "_canvas_highres_r2t2"),
                            v = document.getElementById(c.pa + "_canvas_highres_l1b1"),
                            u = document.getElementById(c.pa + "_canvas_highres_l2b1"),
                            p = document.getElementById(c.pa + "_canvas_highres_l1b2"),
                            q = document.getElementById(c.pa + "_canvas_highres_l2b2"),
                            r = document.getElementById(c.pa + "_canvas_highres_r1b1"),
                            t = document.getElementById(c.pa + "_canvas_highres_r2b1"),
                            y = document.getElementById(c.pa + "_canvas_highres_r1b2"),
                            C = document.getElementById(c.pa + "_canvas_highres_r2b2");
                        if (1 == c.pageNumber && 1 == c.pages.la || c.pageNumber == c.pages.la - 1 || c.pageNumber == c.pages.la - 2) {
                            var w = c.ba == this.Pa(c) ? c.pages.da : null,
                                A = c.ba == this.Pa(c) ? c.pageNumber + 1 : c.pages.la + 1;
                            jQuery(d).visible(!0, w) && "none" === jQuery(d).css("background-image") && jQuery(d).css("background-image", "url('" + this.Lb(A, "l1t1") + "')");
                            jQuery(e).visible(!0, w) && "none" === jQuery(e).css("background-image") && jQuery(e).css("background-image", "url('" + this.Lb(A, "l2t1") + "')");
                            jQuery(g).visible(!0, w) && "none" === jQuery(g).css("background-image") && jQuery(g).css("background-image", "url('" + this.Lb(A, "l1t2") + "')");
                            jQuery(h).visible(!0, w) && "none" === jQuery(h).css("background-image") && jQuery(h).css("background-image", "url('" + this.Lb(A, "l2t2") + "')");
                            jQuery(f).visible(!0, w) && "none" === jQuery(f).css("background-image") && jQuery(f).css("background-image", "url('" + this.Lb(A, "r1t1") + "')");
                            jQuery(k).visible(!0, w) && "none" === jQuery(k).css("background-image") && jQuery(k).css("background-image", "url('" + this.Lb(A, "r2t1") + "')");
                            jQuery(l).visible(!0, w) && "none" === jQuery(l).css("background-image") && jQuery(l).css("background-image", "url('" + this.Lb(A, "r1t2") + "')");
                            jQuery(n).visible(!0, w) && "none" === jQuery(n).css("background-image") && jQuery(n).css("background-image", "url('" + this.Lb(A, "r2t2") + "')");
                            jQuery(v).visible(!0, w) && "none" === jQuery(v).css("background-image") && jQuery(v).css("background-image", "url('" + this.Lb(A, "l1b1") + "')");
                            jQuery(u).visible(!0, w) && "none" === jQuery(u).css("background-image") && jQuery(u).css("background-image", "url('" + this.Lb(A, "l2b1") + "')");
                            jQuery(p).visible(!0, w) && "none" === jQuery(p).css("background-image") && jQuery(p).css("background-image", "url('" + this.Lb(A, "l1b2") + "')");
                            jQuery(q).visible(!0, w) && "none" === jQuery(q).css("background-image") && jQuery(q).css("background-image", "url('" + this.Lb(A, "l2b2") + "')");
                            jQuery(r).visible(!0, w) && "none" === jQuery(r).css("background-image") && jQuery(r).css("background-image", "url('" + this.Lb(A, "r1b1") + "')");
                            jQuery(t).visible(!0, w) && "none" === jQuery(t).css("background-image") && jQuery(t).css("background-image", "url('" + this.Lb(A, "r2b1") + "')");
                            jQuery(y).visible(!0, w) && "none" === jQuery(y).css("background-image") && jQuery(y).css("background-image", "url('" + this.Lb(A, "r1b2") + "')");
                            jQuery(C).visible(!0, w) && "none" === jQuery(C).css("background-image") && jQuery(C).css("background-image", "url('" + this.Lb(A, "r2b2") + "')");
                        }
                    }
                    c.$k = !0;
                }
            },
            yc: function(c) {
                if (this.sb) {
                    var d = eb.platform.Id || eb.platform.android ? "flowpaper_flipview_canvas_highres" : c.pa + "_canvas_highres";
                    c.$k && 0 < jQuery("." + d).length && (jQuery("." + d).css("background-image", ""), c.$k = !1);
                }
            }
        };
        return f;
    }(),
    CanvasPageRenderer = window.CanvasPageRenderer = function() {
        function f(c, d, e, g) {
            this.ja = c;
            this.file = d;
            this.jsDirectory = e;
            this.initialized = !1;
            this.JSONPageDataFormat = this.Qa = this.dimensions = null;
            this.pageThumbImagePattern = g.pageThumbImagePattern;
            this.pageImagePattern = g.pageImagePattern;
            this.config = g;
            this.Ig = this.ja + "_dummyPageCanvas_[pageNumber]";
            this.ci = "#" + this.Ig;
            this.Jg = this.ja + "dummyPageCanvas2_[pageNumber]";
            this.di = "#" + this.Jg;
            this.rb = [];
            this.context = this.va = null;
            this.Ua = [];
            this.ph = [];
            this.gd = "data:image/gif;base64,R0lGODlhIAAgAPMAAP///wAAAMbGxoSEhLa2tpqamjY2NlZWVtjY2OTk5Ly8vB4eHgQEBAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAIAAgAAAE5xDISWlhperN52JLhSSdRgwVo1ICQZRUsiwHpTJT4iowNS8vyW2icCF6k8HMMBkCEDskxTBDAZwuAkkqIfxIQyhBQBFvAQSDITM5VDW6XNE4KagNh6Bgwe60smQUB3d4Rz1ZBApnFASDd0hihh12BkE9kjAJVlycXIg7CQIFA6SlnJ87paqbSKiKoqusnbMdmDC2tXQlkUhziYtyWTxIfy6BE8WJt5YJvpJivxNaGmLHT0VnOgSYf0dZXS7APdpB309RnHOG5gDqXGLDaC457D1zZ/V/nmOM82XiHRLYKhKP1oZmADdEAAAh+QQJCgAAACwAAAAAIAAgAAAE6hDISWlZpOrNp1lGNRSdRpDUolIGw5RUYhhHukqFu8DsrEyqnWThGvAmhVlteBvojpTDDBUEIFwMFBRAmBkSgOrBFZogCASwBDEY/CZSg7GSE0gSCjQBMVG023xWBhklAnoEdhQEfyNqMIcKjhRsjEdnezB+A4k8gTwJhFuiW4dokXiloUepBAp5qaKpp6+Ho7aWW54wl7obvEe0kRuoplCGepwSx2jJvqHEmGt6whJpGpfJCHmOoNHKaHx61WiSR92E4lbFoq+B6QDtuetcaBPnW6+O7wDHpIiK9SaVK5GgV543tzjgGcghAgAh+QQJCgAAACwAAAAAIAAgAAAE7hDISSkxpOrN5zFHNWRdhSiVoVLHspRUMoyUakyEe8PTPCATW9A14E0UvuAKMNAZKYUZCiBMuBakSQKG8G2FzUWox2AUtAQFcBKlVQoLgQReZhQlCIJesQXI5B0CBnUMOxMCenoCfTCEWBsJColTMANldx15BGs8B5wlCZ9Po6OJkwmRpnqkqnuSrayqfKmqpLajoiW5HJq7FL1Gr2mMMcKUMIiJgIemy7xZtJsTmsM4xHiKv5KMCXqfyUCJEonXPN2rAOIAmsfB3uPoAK++G+w48edZPK+M6hLJpQg484enXIdQFSS1u6UhksENEQAAIfkECQoAAAAsAAAAACAAIAAABOcQyEmpGKLqzWcZRVUQnZYg1aBSh2GUVEIQ2aQOE+G+cD4ntpWkZQj1JIiZIogDFFyHI0UxQwFugMSOFIPJftfVAEoZLBbcLEFhlQiqGp1Vd140AUklUN3eCA51C1EWMzMCezCBBmkxVIVHBWd3HHl9JQOIJSdSnJ0TDKChCwUJjoWMPaGqDKannasMo6WnM562R5YluZRwur0wpgqZE7NKUm+FNRPIhjBJxKZteWuIBMN4zRMIVIhffcgojwCF117i4nlLnY5ztRLsnOk+aV+oJY7V7m76PdkS4trKcdg0Zc0tTcKkRAAAIfkECQoAAAAsAAAAACAAIAAABO4QyEkpKqjqzScpRaVkXZWQEximw1BSCUEIlDohrft6cpKCk5xid5MNJTaAIkekKGQkWyKHkvhKsR7ARmitkAYDYRIbUQRQjWBwJRzChi9CRlBcY1UN4g0/VNB0AlcvcAYHRyZPdEQFYV8ccwR5HWxEJ02YmRMLnJ1xCYp0Y5idpQuhopmmC2KgojKasUQDk5BNAwwMOh2RtRq5uQuPZKGIJQIGwAwGf6I0JXMpC8C7kXWDBINFMxS4DKMAWVWAGYsAdNqW5uaRxkSKJOZKaU3tPOBZ4DuK2LATgJhkPJMgTwKCdFjyPHEnKxFCDhEAACH5BAkKAAAALAAAAAAgACAAAATzEMhJaVKp6s2nIkolIJ2WkBShpkVRWqqQrhLSEu9MZJKK9y1ZrqYK9WiClmvoUaF8gIQSNeF1Er4MNFn4SRSDARWroAIETg1iVwuHjYB1kYc1mwruwXKC9gmsJXliGxc+XiUCby9ydh1sOSdMkpMTBpaXBzsfhoc5l58Gm5yToAaZhaOUqjkDgCWNHAULCwOLaTmzswadEqggQwgHuQsHIoZCHQMMQgQGubVEcxOPFAcMDAYUA85eWARmfSRQCdcMe0zeP1AAygwLlJtPNAAL19DARdPzBOWSm1brJBi45soRAWQAAkrQIykShQ9wVhHCwCQCACH5BAkKAAAALAAAAAAgACAAAATrEMhJaVKp6s2nIkqFZF2VIBWhUsJaTokqUCoBq+E71SRQeyqUToLA7VxF0JDyIQh/MVVPMt1ECZlfcjZJ9mIKoaTl1MRIl5o4CUKXOwmyrCInCKqcWtvadL2SYhyASyNDJ0uIiRMDjI0Fd30/iI2UA5GSS5UDj2l6NoqgOgN4gksEBgYFf0FDqKgHnyZ9OX8HrgYHdHpcHQULXAS2qKpENRg7eAMLC7kTBaixUYFkKAzWAAnLC7FLVxLWDBLKCwaKTULgEwbLA4hJtOkSBNqITT3xEgfLpBtzE/jiuL04RGEBgwWhShRgQExHBAAh+QQJCgAAACwAAAAAIAAgAAAE7xDISWlSqerNpyJKhWRdlSAVoVLCWk6JKlAqAavhO9UkUHsqlE6CwO1cRdCQ8iEIfzFVTzLdRAmZX3I2SfZiCqGk5dTESJeaOAlClzsJsqwiJwiqnFrb2nS9kmIcgEsjQydLiIlHehhpejaIjzh9eomSjZR+ipslWIRLAgMDOR2DOqKogTB9pCUJBagDBXR6XB0EBkIIsaRsGGMMAxoDBgYHTKJiUYEGDAzHC9EACcUGkIgFzgwZ0QsSBcXHiQvOwgDdEwfFs0sDzt4S6BK4xYjkDOzn0unFeBzOBijIm1Dgmg5YFQwsCMjp1oJ8LyIAACH5BAkKAAAALAAAAAAgACAAAATwEMhJaVKp6s2nIkqFZF2VIBWhUsJaTokqUCoBq+E71SRQeyqUToLA7VxF0JDyIQh/MVVPMt1ECZlfcjZJ9mIKoaTl1MRIl5o4CUKXOwmyrCInCKqcWtvadL2SYhyASyNDJ0uIiUd6GGl6NoiPOH16iZKNlH6KmyWFOggHhEEvAwwMA0N9GBsEC6amhnVcEwavDAazGwIDaH1ipaYLBUTCGgQDA8NdHz0FpqgTBwsLqAbWAAnIA4FWKdMLGdYGEgraigbT0OITBcg5QwPT4xLrROZL6AuQAPUS7bxLpoWidY0JtxLHKhwwMJBTHgPKdEQAACH5BAkKAAAALAAAAAAgACAAAATrEMhJaVKp6s2nIkqFZF2VIBWhUsJaTokqUCoBq+E71SRQeyqUToLA7VxF0JDyIQh/MVVPMt1ECZlfcjZJ9mIKoaTl1MRIl5o4CUKXOwmyrCInCKqcWtvadL2SYhyASyNDJ0uIiUd6GAULDJCRiXo1CpGXDJOUjY+Yip9DhToJA4RBLwMLCwVDfRgbBAaqqoZ1XBMHswsHtxtFaH1iqaoGNgAIxRpbFAgfPQSqpbgGBqUD1wBXeCYp1AYZ19JJOYgH1KwA4UBvQwXUBxPqVD9L3sbp2BNk2xvvFPJd+MFCN6HAAIKgNggY0KtEBAAh+QQJCgAAACwAAAAAIAAgAAAE6BDISWlSqerNpyJKhWRdlSAVoVLCWk6JKlAqAavhO9UkUHsqlE6CwO1cRdCQ8iEIfzFVTzLdRAmZX3I2SfYIDMaAFdTESJeaEDAIMxYFqrOUaNW4E4ObYcCXaiBVEgULe0NJaxxtYksjh2NLkZISgDgJhHthkpU4mW6blRiYmZOlh4JWkDqILwUGBnE6TYEbCgevr0N1gH4At7gHiRpFaLNrrq8HNgAJA70AWxQIH1+vsYMDAzZQPC9VCNkDWUhGkuE5PxJNwiUK4UfLzOlD4WvzAHaoG9nxPi5d+jYUqfAhhykOFwJWiAAAIfkECQoAAAAsAAAAACAAIAAABPAQyElpUqnqzaciSoVkXVUMFaFSwlpOCcMYlErAavhOMnNLNo8KsZsMZItJEIDIFSkLGQoQTNhIsFehRww2CQLKF0tYGKYSg+ygsZIuNqJksKgbfgIGepNo2cIUB3V1B3IvNiBYNQaDSTtfhhx0CwVPI0UJe0+bm4g5VgcGoqOcnjmjqDSdnhgEoamcsZuXO1aWQy8KAwOAuTYYGwi7w5h+Kr0SJ8MFihpNbx+4Erq7BYBuzsdiH1jCAzoSfl0rVirNbRXlBBlLX+BP0XJLAPGzTkAuAOqb0WT5AH7OcdCm5B8TgRwSRKIHQtaLCwg1RAAAOwAAAAAAAAAAAA%3D%3D";
            this.vb = this.qf = !1;
            this.ua = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
            this.dh = 1;
            this.mb = [];
            this.eh = {};
            this.JSONPageDataFormat = null;
            this.$d = !0;
            this.Sa = null != g.compressedJSONFormat ? g.compressedJSONFormat : !0;
            this.Th = [];
        }
        f.prototype = {
            lf: function() {
                return "CanvasPageRenderer";
            },
            Pa: function(c) {
                return c.aa ? c.aa.ca ? c.aa.ca.na : "" : !1;
            },
            wb: function(c) {
                return c.aa.ca.Lm;
            },
            dispose: function() {
                jQuery(this.Wa).unbind();
                this.Wa.dispose();
                delete this.ed;
                this.ed = null;
                delete this.dimensions;
                this.dimensions = null;
                delete this.Wa;
                this.Wa = null;
                delete this.Ua;
                this.Ua = null;
                delete this.ph;
                this.ph = null;
            },
            initialize: function(c, d) {
                var e = this;
                e.ed = c;
                e.pd = eb.platform.pd;
                2 == e.pd && eb.platform.touchonlydevice && (e.pd = 1);
                e.lo = ("undefined" != e.jsDirectory && null != e.jsDirectory ? e.jsDirectory : "js/") + "pdf.min.js";
                e.Sa ? e.JSONPageDataFormat = {
                    Qe: "width",
                    Pe: "height",
                    ue: "text",
                    Nb: "d",
                    ng: "f",
                    vd: "l",
                    wd: "t",
                    xd: "w",
                    ud: "h"
                } : e.JSONPageDataFormat = {
                    Qe: e.config.JSONPageDataFormat.pageWidth,
                    Pe: e.config.JSONPageDataFormat.pageHeight,
                    ue: e.config.JSONPageDataFormat.textCollection,
                    Nb: e.config.JSONPageDataFormat.textFragment,
                    ng: e.config.JSONPageDataFormat.textFont,
                    vd: e.config.JSONPageDataFormat.textLeft,
                    wd: e.config.JSONPageDataFormat.textTop,
                    xd: e.config.JSONPageDataFormat.textWidth,
                    ud: e.config.JSONPageDataFormat.textHeight
                };
                e.Ia = e.file.indexOf && 0 <= e.file.indexOf("[*,") && e.config && null != e.config.jsonfile && !d.pk;
                e.Ia && (e.Wo = e.file.substr(e.file.indexOf("[*,"), e.file.indexOf("]") - e.file.indexOf("[*,")), e.hk = e.hk = !1);
                PDFJS.workerSrc = ("undefined" != e.jsDirectory && null != e.jsDirectory ? e.jsDirectory : "js/") + "pdf.worker.min.js";
                jQuery.getScript(e.lo, function() {
                    if (e.hk) {
                        var g = new XMLHttpRequest;
                        g.open("HEAD", e.Yh(1), !1);
                        g.overrideMimeType("application/pdf");
                        g.onreadystatechange = function() {
                            if (200 == g.status) {
                                var c = g.getAllResponseHeaders(),
                                    d = {};
                                if (c) {
                                    for (var c = c.split("\r\n"), h = 0; h < c.length; h++) {
                                        var f = c[h],
                                            m = f.indexOf(": ");
                                        0 < m && (d[f.substring(0, m)] = f.substring(m + 2));
                                    }
                                }
                                e.Hj = "bytes" === d["Accept-Ranges"];
                                e.Qm = "identity" === d["Content-Encoding"] || null === d["Content-Encoding"] || !d["Content-Encoding"];
                                e.Hj && e.Qm && !eb.platform.ios && !eb.browser.safari && (e.file = e.file.substr(0, e.file.indexOf(e.Wo) - 1) + ".pdf", e.Ia = !1);
                            }
                            g.abort();
                        };
                        try {
                            g.send(null);
                        } catch (f) {}
                    }
                    e.Wa = new fa(e.ja, e.Ia, e.JSONPageDataFormat, !0);
                    window["wordPageList_" + e.ja] = e.Wa.Ua;
                    jQuery("#" + e.ja).trigger("onDocumentLoading");
                    FLOWPAPER.RANGE_CHUNK_SIZE && (PDFJS.RANGE_CHUNK_SIZE = FLOWPAPER.RANGE_CHUNK_SIZE);
                    PDFJS.disableWorker = e.Ia || eb.browser.Qi || eb.browser.msie;
                    PDFJS.disableRange = eb.platform.ios || e.Ia || eb.browser.safari;
                    PDFJS.disableAutoFetch = eb.platform.ios || e.Ia;
                    PDFJS.pushTextGeometries = !e.Ia;
                    PDFJS.verbosity = PDFJS.VERBOSITY_LEVELS.errors;
                    PDFJS.enableStats = !1;
                    PDFJS.Fq = !0;
                    PDFJS.Gq = !0;
                    if (e.Ia) {
                        e.Ia && e.config && null != e.config.jsonfile && (e.Ia = !0, e.Jd = e.config.jsonfile, e.ur = new Promise(function() {}), jQuery.ajax({
                            url: e.af(10),
                            dataType: e.config.JSONDataType,
                            success: function(c) {
                                c.e && (c = CryptoJS.ye.decrypt(c.e, CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)), c = jQuery.parseJSON(c.toString(CryptoJS.lc.Kf)), e.Ve = !0);
                                jQuery(e).trigger("loadingProgress", {
                                    ja: e.ja,
                                    progress: 0.1
                                });
                                if (0 < c.length) {
                                    e.oa = Array(c[0].pages);
                                    for (var d = 0; d < c.length; d++) {
                                        e.oa[d] = c[d], e.oa[d].loaded = !0, e.nh(d);
                                    }
                                    0 < e.oa.length && (e.nb = e.oa[0].twofold, e.nb && (e.pd = 1));
                                    for (d = 0; d < e.oa.length; d++) {
                                        null == e.oa[d] && (e.oa[d] = [], e.oa[d].loaded = !1);
                                    }
                                    e.Wa.Df(e.oa);
                                }
                                e.Ge = 1;
                                e.Qa = Array(c[0].pages);
                                e.rb = Array(c[0].pages);
                                e.Ki(e.Ge, function() {
                                    jQuery(e).trigger("loadingProgress", {
                                        ja: e.ja,
                                        progress: 1
                                    });
                                    e.ed();
                                }, null, function(c) {
                                    c = 0.1 + c;
                                    1 < c && (c = 1);
                                    jQuery(e).trigger("loadingProgress", {
                                        ja: e.ja,
                                        progress: c
                                    });
                                });
                            },
                            error: function(g, h, f) {
                                h = null != g.responseText && 0 == g.responseText.indexOf("Error:") ? g.responseText.substr(6) : "";
                                this.url.indexOf("view.php") || this.url.indexOf("view.ashx") ? (console.log("Warning: Could not load JSON file. Switching to single file mode."), d.pk = !0, e.Ia = !1, e.initialize(c, d), e.pageThumbImagePattern = null) : O("Error loading JSON file (" + g.statusText + "," + f + "). Please check your configuration.", "onDocumentLoadedError", e.ja, h);
                            }
                        }));
                    } else {
                        e.Jd = e.config.jsonfile;
                        var h = new jQuery.Deferred;
                        if (e.Jd && 0 < e.Jd.length) {
                            var m = jQuery.ajax({
                                url: e.af(10),
                                dataType: e.config.JSONDataType,
                                success: function(c) {
                                    c.e && (c = CryptoJS.ye.decrypt(c.e, CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)), c = jQuery.parseJSON(c.toString(CryptoJS.lc.Kf)), e.Ve = !0);
                                    if (0 < c.length) {
                                        e.oa = Array(c[0].pages);
                                        for (var d = 0; d < c.length; d++) {
                                            e.oa[d] = c[d], e.oa[d].loaded = !0, e.nh(d);
                                        }
                                        for (d = 0; d < e.oa.length; d++) {
                                            null == e.oa[d] && (e.oa[d] = [], e.oa[d].loaded = !1);
                                        }
                                        e.Wa.Df(e.oa);
                                        0 < e.oa.length && (e.nb = e.oa[0].twofold, e.nb && (e.pd = 1));
                                    }
                                }
                            });
                            m.fail(function() {
                                h.resolve();
                            });
                            m.then(function() {
                                h.resolve();
                            });
                        } else {
                            h.resolve();
                        }
                        h.then(function() {
                            var c = {},
                                g = e.file;
                            d && d.pk && g.match(/(page=\d)/ig) && (g = g.replace(/(page=\d)/ig, ""));
                            !e.file.indexOf || e.file instanceof Uint8Array || e.file.indexOf && 0 == e.file.indexOf("blob:") ? c = g : c.url = g;
                            e.al() && (c.password = e.config.signature + "e0737b87e9be157a2f73ae6ba1352a65");
                            var h = 0;
                            c.rangeChunkSize = FLOWPAPER.RANGE_CHUNK_SIZE;
                            c = PDFJS.getDocument(c);
                            c.onPassword = function(c, d) {
                                jQuery("#" + e.ja).trigger("onPasswordNeeded", c, d);
                            };
                            c.onProgress = function(c) {
                                h = c.loaded / c.total;
                                1 < h && (h = 1);
                                jQuery(e).trigger("loadingProgress", {
                                    ja: e.ja,
                                    progress: h
                                });
                            };
                            c.then(function(c) {
                                0.5 > h && jQuery(e).trigger("loadingProgress", {
                                    ja: e.ja,
                                    progress: 0.5
                                });
                                e.pdf = e.Qa = c;
                                e.Qa.getPageLabels().then(function(c) {
                                    jQuery(e).trigger("labelsLoaded", {
                                        Kk: c
                                    });
                                });
                                e.initialized = !0;
                                e.dimensions = null;
                                e.rb = Array(e.nb ? e.oa.length : e.Qa.numPages);
                                e.dimensions = [];
                                e.Qa.getDestinations().then(function(c) {
                                    e.destinations = c;
                                });
                                var g = d && d.StartAtPage ? parseInt(d.StartAtPage) : 1;
                                e.Qa.getPage(g).then(function(c) {
                                    c = c.getViewport(1);
                                    var d = e.Qa.numPages;
                                    !e.Ia && e.nb && (d = e.oa.length);
                                    for (i = 1; i <= d; i++) {
                                        e.dimensions[i - 1] = [], e.dimensions[i - 1].page = i - 1, e.dimensions[i - 1].width = c.width, e.dimensions[i - 1].height = c.height, e.dimensions[i - 1].Ca = c.width, e.dimensions[i - 1].Na = c.height;
                                    }
                                    e.$h = !0;
                                    jQuery(e).trigger("loadingProgress", {
                                        ja: e.ja,
                                        progress: 1
                                    });
                                    1 == g && 1 < d && window.zine ? e.Qa.getPage(2).then(function(c) {
                                        c = c.getViewport(1);
                                        e.nb = 2 * Math.round(e.dimensions[0].width) >= Math.round(c.width) - 1 && 2 * Math.round(e.dimensions[0].width) <= Math.round(c.width) + 1;
                                        if (e.nb) {
                                            e.oa = Array(d);
                                            for (var g = 0; g < e.oa.length; g++) {
                                                e.oa[g] = {}, e.oa[g].text = [], e.oa[g].pages = d, e.oa[g].nb = !0, e.oa[g].width = 0 == g ? e.dimensions[0].width : c.width, e.oa[g].height = 0 == g ? e.dimensions[0].height : c.height, e.nh(g);
                                            }
                                        }
                                        e.ed();
                                    }) : e.ed();
                                });
                                e.Bl(e.Qa);
                            }, function(c) {
                                O("Cannot load PDF file (" + c + ")", "onDocumentLoadedError", e.ja, "Cannot load PDF file (" + c + ")");
                                jQuery(e).trigger("loadingProgress", {
                                    ja: e.ja,
                                    progress: "Error"
                                });
                            }, function() {}, function(c) {
                                jQuery(e).trigger("loadingProgress", {
                                    ja: e.ja,
                                    progress: c.loaded / c.total
                                });
                            });
                        });
                    }
                }).fail(function() {});
                e.JSONPageDataFormat = {
                    Qe: "width",
                    Pe: "height",
                    ue: "text",
                    Nb: "d",
                    ng: "f",
                    vd: "l",
                    wd: "t",
                    xd: "w",
                    ud: "h"
                };
            },
            Ki: function(c, d, e) {
                var g = this,
                    h = {};
                h.url = g.Yh(c);
                g.al() && (h.password = g.config.signature + "e0737b87e9be157a2f73ae6ba1352a65");
                h.rangeChunkSize = FLOWPAPER.RANGE_CHUNK_SIZE;
                g.Zr = PDFJS.getDocument(h).then(function(h) {
                    g.Qa[c - 1] = h;
                    g.initialized = !0;
                    g.dimensions || (g.dimensions = []);
                    g.Qa[c - 1].getDestinations().then(function(c) {
                        g.destinations = c;
                    });
                    g.Qa[c - 1].getPage(1).then(function(h) {
                        g.rb[c - 1] = h;
                        var f = h.getViewport(1);
                        for (i = 1; i <= g.Qa[c - 1].numPages; i++) {
                            var m = g.dimensions && g.dimensions[i - 1] ? g.dimensions[i - 1] : [];
                            g.dimensions[i - 1] = [];
                            g.dimensions[i - 1].loaded = !0;
                            g.dimensions[i - 1].page = i - 1;
                            g.dimensions[i - 1].width = f.width;
                            1 < c && g.nb && (c < g.Qa[c - 1].numPages || 0 != g.Qa[c - 1].numPages % 2) ? (g.dimensions[i - 1].width = g.dimensions[i - 1].width / 2, g.dimensions[i - 1].Ca = f.width / 2) : g.dimensions[i - 1].Ca = f.width;
                            m.width && g.dimensions[i - 1].width != m.width && e && (e.dimensions.Ca = f.width, e.dimensions.Na = f.height, e.Xa());
                            g.dimensions[i - 1].Na = f.height;
                            g.dimensions[i - 1].height = f.height;
                            g.dimensions[i - 1].Ca = f.width;
                            g.dimensions[i - 1].Na = f.height;
                            1 < c && g.nb && (c < g.Qa[c - 1].numPages || 0 != g.Qa[c - 1].numPages % 2) && (g.dimensions[i - 1].Ca = g.dimensions[i - 1].Ca / 2);
                            null != g.Ra[i - 1] && g.Ra.length > i && (g.dimensions[i - 1].Xc = g.Ra[i].Xc, g.dimensions[i - 1].Wc = g.Ra[i].Wc, g.dimensions[i - 1].tb = g.Ra[i].tb, g.dimensions[i - 1].yd = g.Ra[i].yd);
                            g.eh[c - 1 + " " + h.ref.gen + " R"] = c - 1;
                        }
                        g.$h = !0;
                        g.Ge = -1;
                        d && d();
                    });
                    g.Ge = -1;
                }, function(c) {
                    O("Cannot load PDF file (" + c + ")", "onDocumentLoadedError", g.ja);
                    jQuery(g).trigger("loadingProgress", {
                        ja: g.ja,
                        progress: "Error"
                    });
                    g.Ge = -1;
                });
            },
            af: function(c) {
                return this.Jd.replace("{page}", c);
            },
            ii: function(c) {
                var d = 1;
                if (1 < c) {
                    for (var e = 0; e < c; e++) {
                        (0 != e % 2 || 0 == e % 2 && 0 == c % 2 && e == c - 1) && d++;
                    }
                    return d;
                }
                return 1;
            },
            al: function() {
                return null != this.config.signature && 0 < this.config.signature.length;
            },
            Yh: function(c) {
                this.config.PageIndexAdjustment && (c += this.config.PageIndexAdjustment);
                this.nb && 1 < c && (c = this.ii(c));
                if (0 <= this.file.indexOf("{page}")) {
                    return this.file.replace("{page}", c);
                }
                if (0 <= this.file.indexOf("[*,")) {
                    var d = this.file.substr(this.file.indexOf("[*,"), this.file.indexOf("]") - this.file.indexOf("[*,") + 1);
                    return this.file.replace(d, ca(c, parseInt(d.substr(d.indexOf(",") + 1, d.indexOf("]") - 2))));
                }
            },
            Wf: function(c) {
                return c + (10 - c % 10);
            },
            fd: function(c, d, e, g, h) {
                var f = this;
                f.hd == f.Wf(c) ? (window.clearTimeout(h.Vn), h.Vn = setTimeout(function() {
                    h.dimensions.loaded || f.fd(c, d, e, g, h);
                }, 100)) : (f.hd = f.Wf(c), jQuery.ajax({
                    url: f.af(f.hd),
                    dataType: f.config.JSONDataType,
                    async: d,
                    success: function(c) {
                        c.e && (c = CryptoJS.ye.decrypt(c.e, CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)), c = jQuery.parseJSON(c.toString(CryptoJS.lc.Kf)), f.Ve = !0);
                        if (0 < c.length) {
                            for (var d = 0; d < c.length; d++) {
                                var g = parseInt(c[d].number) - 1;
                                f.oa[g] = c[d];
                                f.oa[g].loaded = !0;
                                f.Yn(g);
                                f.nh(g, h);
                            }
                            f.Wa.Df(f.oa);
                            jQuery(f).trigger("onTextDataUpdated");
                            null != e && e();
                        }
                        f.hd = null;
                    },
                    error: function(c) {
                        O("Error loading JSON file (" + c.statusText + "). Please check your configuration.", "onDocumentLoadedError", f.ja);
                        f.hd = null;
                    }
                }));
            },
            nh: function(c) {
                this.Ra || (this.Ra = []);
                this.Ra[c] || (this.Ra[c] = []);
                this.Ra[c].Xc = this.oa[c][this.JSONPageDataFormat.Qe];
                this.Ra[c].Wc = this.oa[c][this.JSONPageDataFormat.Pe];
                this.Ra[c].tb = this.Ra[c].Xc;
                this.Ra[c].yd = this.Ra[c].Wc;
                c = this.Ra[c];
                for (var d = 0; d < this.getNumPages(); d++) {
                    null == this.Ra[d] && (this.Ra[d] = [], this.Ra[d].Xc = c.Xc, this.Ra[d].Wc = c.Wc, this.Ra[d].tb = c.tb, this.Ra[d].yd = c.yd);
                }
            },
            getDimensions: function() {
                var c = this;
                if (null == c.dimensions || c.$h || null != c.dimensions && 0 == c.dimensions.length) {
                    null == c.dimensions && (c.dimensions = []);
                    var d = c.Qa.numPages;
                    !c.Ia && c.nb && (d = c.oa.length);
                    if (c.Ia) {
                        for (var e = 0; e < c.getNumPages(); e++) {
                            null != c.dimensions[e] || null != c.dimensions[e] && !c.dimensions[e].loaded ? (null == c.kc && (c.kc = c.dimensions[e]), c.dimensions[e].tb || null == c.Ra[e] || (c.dimensions[e].tb = c.Ra[e].tb, c.dimensions[e].yd = c.Ra[e].yd)) : null != c.kc && (c.dimensions[e] = [], c.dimensions[e].page = e, c.dimensions[e].loaded = !1, c.dimensions[e].width = c.kc.width, c.dimensions[e].height = c.kc.height, c.dimensions[e].Ca = c.kc.Ca, c.dimensions[e].Na = c.kc.Na, null != c.Ra[e - 1] && (c.dimensions[e - 1].Xc = c.Ra[e].Xc, c.dimensions[e - 1].Wc = c.Ra[e].Wc, c.dimensions[e - 1].tb = c.Ra[e].tb, c.dimensions[e - 1].yd = c.Ra[e].yd), e == c.getNumPages() - 1 && (c.dimensions[e].Xc = c.Ra[e].Xc, c.dimensions[e].Wc = c.Ra[e].Wc, c.dimensions[e].tb = c.Ra[e].tb, c.dimensions[e].yd = c.Ra[e].yd), c.eh[e + " 0 R"] = e);
                        }
                    } else {
                        for (e = 1; e <= d; e++) {
                            var g = e;
                            c.nb && (g = c.ii(e));
                            c.Qa.getPage(g).then(function(d) {
                                var e = d.getViewport(1);
                                c.dimensions[d.pageIndex] = [];
                                c.dimensions[d.pageIndex].page = d.pageIndex;
                                c.dimensions[d.pageIndex].width = e.width;
                                c.dimensions[d.pageIndex].height = e.height;
                                c.dimensions[d.pageIndex].Ca = e.width;
                                c.dimensions[d.pageIndex].Na = e.height;
                                e = d.ref;
                                c.eh[e.num + " " + e.gen + " R"] = d.pageIndex;
                            });
                        }
                    }
                    c.$h = !1;
                }
                return c.dimensions;
            },
            Yn: function(c) {
                if (this.dimensions[c]) {
                    this.dimensions[c].page = c;
                    this.dimensions[c].loaded = !0;
                    this.mb[c] = [];
                    this.mb[c] = "";
                    for (var d = 0, e; e = this.oa[c][this.JSONPageDataFormat.ue][d++];) {
                        this.Sa ? !isNaN(e[0].toString()) && 0 <= Number(e[0].toString()) && !isNaN(e[1].toString()) && 0 <= Number(e[1].toString()) && !isNaN(e[2].toString()) && 0 <= Number(e[2].toString()) && !isNaN(e[3].toString()) && 0 <= Number(e[3].toString()) && (this.mb[c] += e[5]) : !isNaN(e[this.JSONPageDataFormat.vd].toString()) && 0 <= Number(e[this.JSONPageDataFormat.vd].toString()) && !isNaN(e[this.JSONPageDataFormat.wd].toString()) && 0 <= Number(e[this.JSONPageDataFormat.wd].toString()) && !isNaN(e[this.JSONPageDataFormat.xd].toString()) && 0 < Number(e[this.JSONPageDataFormat.xd].toString()) && !isNaN(e[this.JSONPageDataFormat.ud].toString()) && 0 < Number(e[this.JSONPageDataFormat.ud].toString()) && (this.mb[c] += e[this.JSONPageDataFormat.Nb]);
                    }
                    this.mb[c] = this.mb[c].toLowerCase();
                }
            },
            getNumPages: function() {
                return this.Ia ? this.oa.length : this.nb ? this.oa.length : this.Qa ? this.Qa.numPages : this.oa.length;
            },
            getPage: function(c) {
                this.Qa.getPage(c).then(function(c) {
                    return c;
                });
                return null;
            },
            Dc: function(c) {
                var d = this;
                "TwoPage" == c.ba || "BookView" == c.ba ? (0 == c.pageNumber && jQuery(c.va).css("background-image", "url('" + d.Aa(c.pages.la + 1) + "')"), 1 == c.pageNumber && jQuery(c.va).css("background-image", "url('" + d.Aa(c.pages.la + 2) + "')")) : "ThumbView" == c.ba ? jQuery(c.va).css("background-image", "url('" + d.Aa(c.pageNumber + 1, 200) + "')") : "SinglePage" == c.ba ? jQuery(c.va).css("background-image", "url('" + d.Aa(d.Ma(c) + 1) + "')") : jQuery(c.va).css("background-image", "url('" + d.Aa(c.pageNumber + 1) + "')");
                c.wa = new Image;
                jQuery(c.wa).bind("load", function() {
                    var e = Math.round(c.wa.width / c.wa.height * 100),
                        g = Math.round(c.dimensions.width / c.dimensions.height * 100);
                    if ("SinglePage" == c.ba) {
                        var e = d.Ra[c.pages.la],
                            h = Math.round(e.Xc / e.Wc * 100),
                            g = Math.round(c.dimensions.Ca / c.dimensions.Na * 100);
                        h != g && (c.dimensions.Ca = e.Xc, c.dimensions.Na = e.Wc, c.Xa(), c.sj = -1, d.La(c, !0, null));
                    } else {
                        e != g && (c.dimensions.Ca = c.wa.width, c.dimensions.Na = c.wa.height, c.Xa(), c.sj = -1, d.La(c, !0, null));
                    }
                });
                jQuery(c.wa).attr("src", d.Aa(c.pageNumber + 1));
            },
            dl: function(c) {
                "TwoPage" == c.ba || "BookView" == c.ba ? (0 == c.pageNumber && jQuery(c.va).css("background-image", "url(" + this.ua + ")"), 1 == c.pageNumber && jQuery(c.va).css("background-image", "url(" + this.ua + ")")) : jQuery(c.va).css("background-image", "url(" + this.ua + ")");
            },
            Hd: function(c) {
                this.ub = c.ub = this.Ia && this.config.MixedMode;
                "Portrait" != c.ba && "SinglePage" != c.ba || jQuery(c.ma).append("<canvas id='" + this.Ja(1, c) + "' style='position:relative;left:0px;top:0px;width:100%;height:100%;display:none;background-repeat:no-repeat;background-size:" + ((eb.browser.mozilla || eb.browser.safari) && eb.platform.mac ? "100% 100%" : "cover") + ";background-color:#ffffff;' class='" + (this.config.DisableShadows ? "" : "flowpaper_border") + " flowpaper_interactivearea flowpaper_grab flowpaper_hidden flowpaper_rescale'></canvas><canvas id='" + this.Ja(2, c) + "' style='position:relative;left:0px;top:0px;width:100%;height:100%;display:block;background-repeat:no-repeat;background-size:" + ((eb.browser.mozilla || eb.browser.safari) && eb.platform.mac ? "100% 100%" : "cover") + ";background-color:#ffffff;' class='" + (this.config.DisableShadows ? "" : "flowpaper_border") + " flowpaper_interactivearea flowpaper_grab flowpaper_hidden flowpaper_rescale'></canvas>");
                c.ba == this.Pa(c) && this.wb(c).Hd(this, c);
                "ThumbView" == c.ba && jQuery(c.ma).append("<canvas id='" + this.Ja(1, c) + "' style='" + c.getDimensions() + ";background-repeat:no-repeat;background-size:" + ((eb.browser.mozilla || eb.browser.safari) && eb.platform.mac ? "100% 100%" : "cover") + ";background-color:#ffffff;' class='flowpaper_interactivearea flowpaper_grab flowpaper_hidden' ></canvas>");
                if ("TwoPage" == c.ba || "BookView" == c.ba) {
                    0 == c.pageNumber && (jQuery(c.ma + "_1").append("<img id='" + c.qc + "_1' src='" + this.gd + "' style='position:absolute;left:" + (c.Va() - 30) + "px;top:" + c.Za() / 2 + "px;' />"), jQuery(c.ma + "_1").append("<canvas id='" + this.Ja(1, c) + "' style='position:absolute;width:100%;height:100%;background-repeat:no-repeat;background-size:" + ((eb.browser.mozilla || eb.browser.safari) && eb.platform.mac ? "100% 100%" : "cover") + ";background-color:#ffffff;' class='flowpaper_interactivearea flowpaper_grab flowpaper_hidden'/></canvas>"), jQuery(c.ma + "_1").append("<div id='" + c.pa + "_1_textoverlay' style='position:relative;left:0px;top:0px;width:100%;height:100%;z-index:10'></div>")), 1 == c.pageNumber && (jQuery(c.ma + "_2").append("<img id='" + c.qc + "_2' src='" + this.gd + "' style='position:absolute;left:" + (c.Va() / 2 - 10) + "px;top:" + c.Za() / 2 + "px;' />"), jQuery(c.ma + "_2").append("<canvas id='" + this.Ja(2, c) + "' style='position:absolute;width:100%;height:100%;background-repeat:no-repeat;background-size:" + ((eb.browser.mozilla || eb.browser.safari) && eb.platform.mac ? "100% 100%" : "cover") + ";background-color:#ffffff;' class='flowpaper_interactivearea flowpaper_grab flowpaper_hidden'/></canvas>"), jQuery(c.ma + "_2").append("<div id='" + c.pa + "_2_textoverlay' style='position:absolute;left:0px;top:0px;width:100%;height:100%;z-index:10'></div>"));
                }
            },
            Ja: function(c, d) {
                var e = d.pageNumber;
                if (("TwoPage" == d.ba || "BookView" == d.ba) && 0 == d.pageNumber % 2) {
                    return this.ja + "_dummyCanvas1";
                }
                if (("TwoPage" == d.ba || "BookView" == d.ba) && 0 != d.pageNumber % 2) {
                    return this.ja + "_dummyCanvas2";
                }
                if (1 == c) {
                    return this.Ig.replace("[pageNumber]", e);
                }
                if (2 == c) {
                    return this.Jg.replace("[pageNumber]", e);
                }
            },
            wn: function(c, d) {
                if (("TwoPage" == d.ba || "BookView" == d.ba) && 0 == d.pageNumber % 2) {
                    return "#" + this.ja + "_dummyCanvas1";
                }
                if (("TwoPage" == d.ba || "BookView" == d.ba) && 0 != d.pageNumber % 2) {
                    return "#" + this.ja + "_dummyCanvas2";
                }
                if (1 == c) {
                    return this.ci.replace("[pageNumber]", d.pageNumber);
                }
                if (2 == c) {
                    return this.di.replace("[pageNumber]", d.pageNumber);
                }
            },
            Tb: function(c, d, e) {
                var g = this;
                g.hi = !0;
                if (c.ba != g.Pa(c) || g.wb(c).So(g, c, d, e)) {
                    if ("Portrait" != c.ba && "TwoPage" != c.ba && "BookView" != c.ba || null != c.context || c.oc || (c.sd(), c.oc = !0), 1 == g.yo && 1 < c.scale && c.ub && g.Oa(c, -1), -1 < g.Ma(c) || g.Ia && null != g.Af) {
                        window.clearTimeout(c.fc), c.fc = setTimeout(function() {
                            window.requestAnim(function() {
                                g.Tb(c, d, e);
                            });
                        }, 50);
                    } else {
                        g.Mk = c;
                        g.yo = c.scale;
                        if ("TwoPage" == c.ba || "BookView" == c.ba) {
                            if (0 == c.pageNumber) {
                                "BookView" == c.ba ? g.Oa(c, 0 == c.pages.la ? c.pages.la : c.pages.la - 1) : "TwoPage" == c.ba && g.Oa(c, c.pages.la), g.dk = c, c.mc();
                            } else {
                                if (1 == c.pageNumber) {
                                    "BookView" == c.ba ? g.Oa(c, c.pages.la) : "TwoPage" == c.ba && g.Oa(c, c.pages.la + 1), g.dk = c, jQuery(c.ma + "_2").removeClass("flowpaper_hidden"), jQuery(c.ma + "_2").removeClass("flowpaper_load_on_demand"), c.mc();
                                } else {
                                    return;
                                }
                            }
                        } else {
                            "SinglePage" == c.ba ? g.Oa(c, c.pages.la) : (g.Oa(c, c.pageNumber), g.dk = c);
                        }
                        g.jj(c);
                        if ((c.ub || g.Ia) && !c.dimensions.loaded) {
                            var h = c.pageNumber + 1;
                            "SinglePage" == c.ba && (h = g.Ma(c) + 1);
                            g.fd(h, !0, function() {
                                c.dimensions.loaded = !1;
                                g.wc(c);
                            }, !0, c);
                        }
                        var h = !1,
                            f = c.Rc + "_textLayer";
                        jQuery("#" + f).remove();
                        if (0 == jQuery("#" + f).length && ("Portrait" == c.ba || "SinglePage" == c.ba || "TwoPage" == c.ba || "BookView" == c.ba || c.ba == g.Pa(c) && g.wb(c).Gp(g, c))) {
                            var h = !0,
                                k = c.Kc(),
                                f = "<div id='" + f + "' class='flowpaper_textLayer flowpaper_pageword_" + g.ja + "' style='width:" + c.Va() + "px;height:" + c.Za() + "px;backface-visibility:hidden;'></div>";
                            "Portrait" == c.ba || g.Pa(c) ? jQuery(c.Ha).append(f) : "TwoPage" != c.ba && "BookView" != c.ba || jQuery(c.Ha + "_" + (c.pageNumber % 2 + 1)).append(f);
                            if (90 == c.rotation || 270 == c.rotation || 180 == c.rotation) {
                                jQuery(c.Rb).css({
                                    "z-index": 11,
                                    "margin-left": k
                                }), jQuery(c.Rb).transition({
                                    rotate: c.rotation,
                                    translate: "-" + k + "px, 0px"
                                }, 0);
                            }
                        }
                        if (c.ub && c.scale <= g.ah(c) && !c.ai) {
                            -1 < g.Ma(c) && window.clearTimeout(c.fc), jQuery(c.ma).removeClass("flowpaper_load_on_demand"), g.Ia && c.aa.initialized && !c.Km ? g.Th.push(function() {
                                var d = new XMLHttpRequest;
                                d.open("GET", g.Yh(c.pageNumber + 1), !0);
                                d.overrideMimeType("text/plain; charset=x-user-defined");
                                d.addEventListener("load", function() {
                                    g.De();
                                });
                                d.addEventListener("error", function() {
                                    g.De();
                                });
                                d.send(null);
                                c.Km = !0;
                            }) : g.Hj && null == g.rb[g.Ma(c)] && (k = g.Ma(c) + 1, g.Qa && g.Qa.getPage && g.Qa.getPage(k).then(function(d) {
                                g.rb[g.Ma(c)] = d;
                            })), c.ba == g.Pa(c) ? g.wb(c).Tb(g, c, d, e) : (g.Dc(c), g.Ke(c, e)), c.Ga = !0;
                        } else {
                            if (c.ub && c.scale > g.ah(c) && !c.ai) {
                                c.ba != g.Pa(c) && g.Dc(c);
                            } else {
                                if (!c.ub && c.ie && c.ba == g.Pa(c) && 1 == c.scale) {
                                    if (!c.Yc && 100 != c.va.width) {
                                        c.Yc = c.va.toDataURL(), k = jQuery("#" + g.Ja(1, c)), k.css("background-image").length < c.Yc.length + 5 && k.css("background-image", "url(" + c.Yc + ")"), k[0].width = 100;
                                    } else {
                                        if (c.Yc && !g.Ia && "none" != jQuery("#" + g.Ja(1, c)).css("background-image")) {
                                            g.Oa(c, -1);
                                            c.Ga = !0;
                                            return;
                                        }
                                    }
                                    g.Wk(c);
                                }
                            }
                            null != g.rb[g.Ma(c)] || g.Ia || (k = g.Ma(c) + 1, g.nb && (k = g.ii(k)), g.Qa && g.Qa.getPage && g.Qa.getPage(k).then(function(h) {
                                g.rb[g.Ma(c)] = h;
                                window.clearTimeout(c.fc);
                                g.Oa(c, -1);
                                g.Tb(c, d, e);
                            }));
                            if (c.va) {
                                if (100 == c.va.width || 1 != c.scale || c.ba != g.Pa(c) || c.jl) {
                                    if (k = !0, null == g.rb[g.Ma(c)] && g.Ia && (c.ba == g.Pa(c) && (k = g.wb(c).Ro(g, c)), null == g.Qa[g.Ma(c)] && -1 == g.Ge && k && null == g.Af && (g.Ge = g.Ma(c) + 1, g.Ki(g.Ge, function() {
                                            window.clearTimeout(c.fc);
                                            g.Oa(c, -1);
                                            g.Tb(c, d, e);
                                        }, c))), null != g.rb[g.Ma(c)] || !k) {
                                        if (c.ba == g.Pa(c) ? g.wb(c).Tb(g, c, d, e) : (c.va.width = c.Va(), c.va.height = c.Za()), g.nb && 0 < c.Db.indexOf("cropCanvas") && (c.va.width = 2 * c.va.width), null != g.rb[g.Ma(c)] || !k) {
                                            if (g.hi) {
                                                k = c.va.height / g.getDimensions()[c.pageNumber].height;
                                                c.ba != g.Pa(c) && (k *= g.pd);
                                                g.Bp = k;
                                                1.5 > k && (k = 1.5);
                                                g.vr = k;
                                                var l = g.rb[g.Ma(c)].getViewport(k);
                                                g.nb || (c.va.width = l.width, c.va.height = l.height);
                                                var n = c.vo = {
                                                    canvasContext: c.context,
                                                    viewport: l,
                                                    pageNumber: c.pageNumber,
                                                    qh: h && !g.Ia ? new ga : null
                                                };
                                                g.rb[g.Ma(c)].objs.geometryTextList = [];
                                                window.requestAnim(function() {
                                                    c.va.style.display = "none";
                                                    c.va.redraw = c.va.offsetHeight;
                                                    c.va.style.display = "";
                                                    g.Af = g.rb[g.Ma(c)].render(n);
                                                    g.Af.onContinue = function(c) {
                                                        c();
                                                    };
                                                    g.Af.promise.then(function() {
                                                        g.Af = null;
                                                        if (null != g.rb[g.Ma(c)]) {
                                                            if (g.Ia || c.ub && c.scale <= g.ah(c) || !c.va) {
                                                                g.Ia || g.vl(g.rb[g.Ma(c)], c, l, g.Ia), g.Ke(c, e);
                                                            } else {
                                                                var d = c.va.height / g.getDimensions()[c.pageNumber].height,
                                                                    h = g.rb[g.Ma(c)].objs.geometryTextList;
                                                                if (h) {
                                                                    for (var f = 0; f < h.length; f++) {
                                                                        h[f].Io != d && (h[f].h = h[f].metrics.height / d, h[f].l = h[f].metrics.left / d, h[f].t = h[f].metrics.top / d, h[f].w = h[f].textMetrics.geometryWidth / d, h[f].d = h[f].unicode, h[f].f = h[f].fontFamily, h[f].Io = d);
                                                                    }
                                                                    "SinglePage" == c.ba || "TwoPage" == c.ba || "BookView" == c.ba ? g.Wa.pl(h, g.Ma(c), g.getNumPages()) : g.Wa.pl(h, c.pageNumber, g.getNumPages());
                                                                }
                                                                g.vl(g.rb[g.Ma(c)], c, l, g.Ia);
                                                                g.Ke(c, e);
                                                                g.La(c, !0, e);
                                                            }
                                                        } else {
                                                            g.Ke(c, e), K(c.pageNumber + "  is missing its pdf page (" + g.Ma(c) + ")");
                                                        }
                                                    }, function(c) {
                                                        O(c.toString(), "onDocumentLoadedError", g.ja);
                                                        g.Af = null;
                                                    });
                                                }, 50);
                                            } else {
                                                g.Oa(c, -1);
                                            }
                                            jQuery(c.ma).removeClass("flowpaper_load_on_demand");
                                        }
                                    }
                                } else {
                                    jQuery("#" + g.Ja(1, c)).mg(), jQuery("#" + g.Ja(2, c)).Tg(), 1 == c.scale && eb.browser.safari ? (jQuery("#" + g.Ja(1, c)).css("-webkit-backface-visibility", "hidden"), jQuery("#" + g.Ja(2, c)).css("-webkit-backface-visibility", "hidden"), jQuery("#" + c.pa + "_textoverlay").css("-webkit-backface-visibility", "hidden")) : eb.browser.safari && (jQuery("#" + g.Ja(1, c)).css("-webkit-backface-visibility", "visible"), jQuery("#" + g.Ja(2, c)).css("-webkit-backface-visibility", "visible"), jQuery("#" + c.pa + "_textoverlay").css("-webkit-backface-visibility", "visible")), g.Oa(c, -1), c.Ga || jQuery("#" + g.ja).trigger("onPageLoaded", c.pageNumber + 1), c.Ga = !0, g.La(c, !0, e);
                                }
                            } else {
                                window.clearTimeout(c.fc);
                            }
                        }
                    }
                }
            },
            Wk: function(c) {
                var d = null,
                    e = null;
                0 != c.pageNumber % 2 ? (d = c, e = c.aa.pages.pages[c.pageNumber - 1]) : (e = c, d = c.aa.pages.pages[c.pageNumber + 1]);
                if (c.ba == this.Pa(c) && !c.ub && c.ie && d && e && (!d.Qc || !e.Qc)) {
                    var g = e.Yc,
                        d = d.Yc;
                    g && d && !c.Qc && e.ie(g, d);
                }
            },
            ah: function() {
                return 1.1;
            },
            Ma: function(c) {
                return this.Ia || PDFJS.disableWorker || null == c ? this.He : c.He;
            },
            Oa: function(c, d) {
                (!this.Ia || c && c.ub && 1 == c.scale) && c && (c.He = d);
                this.He = d;
            },
            jj: function(c) {
                "Portrait" == c.ba || "SinglePage" == c.ba ? jQuery(this.wn(1, c)).is(":visible") ? (c.Db = this.Ja(2, c), c.tf = this.Ja(1, c)) : (c.Db = this.Ja(1, c), c.tf = this.Ja(2, c)) : c.ba == this.Pa(c) ? this.wb(c).jj(this, c) : (c.Db = this.Ja(1, c), c.tf = null);
                this.nb && 0 < c.pageNumber && 0 == c.pageNumber % 2 ? (c.va = document.createElement("canvas"), c.va.width = c.va.height = 100, c.va.id = c.Db + "_cropCanvas", c.Db = c.Db + "_cropCanvas") : c.va = document.getElementById(c.Db);
                null != c.Jn && (c.Jn = document.getElementById(c.tf));
                c.va && c.va.getContext && (c.context = c.va.getContext("2d"), c.context.Hf = c.context.mozImageSmoothingEnabled = c.context.imageSmoothingEnabled = !1);
            },
            Rm: function(c, d, e, g) {
                c = g.convertToViewportRectangle(d.rect);
                c = PDFJS.Util.normalizeRect(c);
                d = e.Kc();
                g = document.createElement("a");
                var h = e.ba == this.Pa(e) ? 1 : this.pd;
                g.style.position = "absolute";
                g.style.left = Math.floor(c[0]) / h + d + "px";
                g.style.top = Math.floor(c[1]) / h + "px";
                g.style.width = Math.ceil(c[2] - c[0]) / h + "px";
                g.style.height = Math.ceil(c[3] - c[1]) / h + "px";
                g.style["z-index"] = 20;
                g.style.cursor = "pointer";
                g.className = "pdfPageLink_" + e.pageNumber + " flowpaper_interactiveobject_" + this.ja;
                return g;
            },
            vl: function(c, d, e, g) {
                var h = this;
                if (1 == d.scale || d.ba != h.Pa(d)) {
                    jQuery(".pdfPageLink_" + d.pageNumber).remove(), c.getAnnotations().then(function(e) {
                        for (var f = 0; f < e.length; f++) {
                            var l = e[f];
                            switch (l.subtype) {
                                case "Link":
                                    var n = h.Rm("a", l, d, c.getViewport(h.Bp), c.view);
                                    n.style.position = "absolute";
                                    n.href = l.url || "";
                                    eb.platform.touchonlydevice || (jQuery(n).on("mouseover", function() {
                                        jQuery(this).stop(!0, !0);
                                        jQuery(this).css("background", d.aa.linkColor);
                                        jQuery(this).css({
                                            opacity: d.aa.ge
                                        });
                                    }), jQuery(n).on("mouseout", function() {
                                        jQuery(this).css("background", "");
                                        jQuery(this).css({
                                            opacity: 0
                                        });
                                    }));
                                    l.url || g ? null != n.href && "" != n.href && l.url && (jQuery(n).on("click", function() {
                                        jQuery(d.ia).trigger("onExternalLinkClicked", this.href);
                                    }), jQuery(d.Ha).append(n)) : (l = "string" === typeof l.dest ? h.destinations[l.dest][0] : null != l && null != l.dest ? l.dest[0] : null, l = l instanceof Object ? h.eh[l.num + " " + l.gen + " R"] : l + 1, jQuery(n).data("gotoPage", l + 1), jQuery(n).on("click", function() {
                                        d.aa.gotoPage(parseInt(jQuery(this).data("gotoPage")));
                                        return !1;
                                    }), jQuery(d.Ha).append(n));
                            }
                        }
                    });
                }
            },
            Ke: function(c, d) {
                this.La(c, !0, d);
                jQuery("#" + c.Db).mg();
                this.Ck(c);
                "Portrait" != c.ba && "SinglePage" != c.ba || jQuery(c.cc).remove();
                c.ba == this.Pa(c) && this.wb(c).Ke(this, c, d);
                if (c.Db && 0 < c.Db.indexOf("cropCanvas")) {
                    var e = c.va;
                    c.Db = c.Db.substr(0, c.Db.length - 11);
                    c.va = jQuery("#" + c.Db).get(0);
                    c.va.width = e.width / 2;
                    c.va.height = e.height;
                    c.va.getContext("2d").drawImage(e, e.width / 2, 0, c.va.width, c.va.height, 0, 0, e.width / 2, e.height);
                    jQuery(c.va).mg();
                }!c.ub && c.ie && !c.Qc && c.va && (c.Yc = c.va.toDataURL(), this.Wk(c));
                if (c.Yc && 1 == c.scale) {
                    var g = jQuery("#" + this.Ja(1, c));
                    requestAnim(function() {
                        g.css("background-image").length < c.Yc.length + 5 && g.css("background-image", "url(" + c.Yc + ")");
                        g[0].width = 100;
                    });
                }
                if ("TwoPage" == c.ba || "BookView" == c.ba) {
                    0 == c.pageNumber && (jQuery(c.Ka).removeClass("flowpaper_hidden"), jQuery(c.ma + "_1").removeClass("flowpaper_hidden")), 1 == c.pageNumber && jQuery(c.Ka).removeClass("flowpaper_hidden");
                }
                c.Ga || jQuery("#" + this.ja).trigger("onPageLoaded", c.pageNumber + 1);
                c.Ga = !0;
                c.jl = !1;
                c.jr = !1;
                this.qf || (this.qf = !0, c.aa.hh());
                null != d && d();
                this.De();
            },
            De: function() {
                0 < this.Th.length && -1 == this.Ma() && this.Mk.Ga && !this.Mk.zc && this.Th.shift()();
            },
            Ck: function(c) {
                "TwoPage" == c.ba || "BookView" == c.ba || c.ba == this.Pa(c) && !eb.browser.safari || jQuery("#" + c.tf).Tg();
                this.Oa(c, -1);
            },
            Aa: function(c, d) {
                this.Ve && (c = CryptoJS.ye.encrypt(c.toString(), CryptoJS.lc.xe.parse(eb.Sg ? P() : eb.Vd.innerHTML)).toString());
                this.config.PageIndexAdjustment && (c += this.config.PageIndexAdjustment);
                if (!d) {
                    return this.pageSVGImagePattern ? this.pageSVGImagePattern.replace("{page}", c) : this.pageImagePattern.replace("{page}", c);
                }
                if (null != this.pageThumbImagePattern && 0 < this.pageThumbImagePattern.length) {
                    return this.pageThumbImagePattern.replace("{page}", c) + (0 < this.pageThumbImagePattern.indexOf("?") ? "&" : "?") + "resolution=" + d;
                }
            },
            unload: function(c) {
                jQuery(".flowpaper_pageword_" + this.ja + "_page_" + c.pageNumber + ":not(.flowpaper_selected_searchmatch, .flowpaper_annotation_" + this.ja + ")").remove();
                c.ba != this.Pa(c) && this.dl(c);
                c.ub && (jQuery(c.va).css("background-image", "url(" + this.ua + ")"), c.wa = null);
                null != c.context && null != c.va && 100 != c.va.width && (this.context = this.va = c.vo = null, c.$i && c.$i(), jQuery(".flowpaper_annotation_" + this.ja + "_page_" + c.pageNumber).remove());
                this.Ia && (this.rb[c.pageNumber] && this.rb[c.pageNumber].cleanup(), this.Qa[c.pageNumber] = null, this.rb[c.pageNumber] = null);
                c.qg && c.qg();
            },
            Bl: function(c) {
                var d = this;
                d.Qa && d.Qa.getPage(d.dh).then(function(e) {
                    e.getTextContent().then(function(e) {
                        var h = "";
                        if (e) {
                            for (var f = 0; f < e.items.length; f++) {
                                h += e.items[f].str;
                            }
                        }
                        d.mb[d.dh - 1] = h.toLowerCase();
                        d.dh + 1 < d.getNumPages() + 1 && (d.dh++, d.Bl(c));
                    });
                });
            },
            wc: function(c, d, e, g) {
                this.Wa.wc(c, d, e, g);
            },
            vc: function(c, d, e) {
                this.Wa.vc(c, d, e);
            },
            Ae: function(c, d, e, g) {
                this.Wa.Ae(c, d, e, g);
            },
            La: function(c, d, e) {
                var g = null != this.oa && this.oa[c.pageNumber] && this.oa[c.pageNumber].text && 0 < this.oa[c.pageNumber].text.length && this.Ia;
                if (c.Ga || d || g) {
                    c.sj != c.scale && (jQuery(".flowpaper_pageword_" + this.ja + "_page_" + c.pageNumber).remove(), c.sj = c.scale), d = null != this.Ff ? this.Ff : e, this.Ff = null, this.Wa && this.Wa.La(c, d);
                } else {
                    if (null != e) {
                        if (null != this.Ff) {
                            var h = this.Ff;
                            this.Ff = function() {
                                h();
                                e();
                            };
                        } else {
                            this.Ff = e;
                        }
                    }
                }
            }
        };
        return f;
    }();

function ga() {
    this.beginLayout = function() {
        this.textDivs = [];
        this.ph = [];
    };
    this.endLayout = function() {};
}
var fa = window.TextOverlay = function() {
    function f(c, d, e, g) {
        this.ja = c;
        this.JSONPageDataFormat = e;
        this.oa = [];
        this.bb = null;
        this.Ua = [];
        this.Sa = this.Fp = d;
        this.vb = g;
        this.state = {};
        this.ua = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    }
    f.prototype = {
        dispose: function() {
            delete this.ja;
            this.ja = null;
            delete this.oa;
            this.oa = null;
            delete this.JSONPageDataFormat;
            this.JSONPageDataFormat = null;
            delete this.bb;
            this.bb = null;
            delete this.Ua;
            this.Ua = null;
            delete this.state;
            this.state = null;
            delete this.ua;
            this.ua = null;
            delete this.vb;
            this.vb = null;
        },
        Ho: function() {
            this.state[this.Sa] || (this.state[this.Sa] = [], this.state[this.Sa].oa = this.oa, this.state[this.Sa].bb = this.bb, this.state[this.Sa].Ua = this.Ua, window["wordPageList_" + this.ja] = null);
            this.oa = [];
            this.bb = null;
            this.Ua = [];
            this.Sa = this.Fp;
        },
        Pa: function(c) {
            return c.aa.ca ? c.aa.ca.na : "";
        },
        wb: function(c) {
            return c.aa.ca.yp;
        },
        Dm: function(c) {
            return c.aa.document.AutoDetectLinks;
        },
        Df: function(c) {
            this.oa = c;
            null == this.bb && (this.bb = Array(c.length));
            window["wordPageList_" + this.ja] = this.Ua;
        },
        pl: function(c, d, e) {
            null == this.bb && (this.bb = Array(e));
            this.oa[d] = [];
            this.oa[d].text = c;
            window["wordPageList_" + this.ja] = this.Ua;
        },
        wc: function(c, d, e, g) {
            var h = c.pageNumber,
                f = !1,
                k = !1;
            if (!this.bb) {
                if (c.ub && (this.Sa = !0), this.state[this.Sa]) {
                    if (this.oa = this.state[this.Sa].oa, this.bb = this.state[this.Sa].bb, this.Ua = this.state[this.Sa].Ua, window["wordPageList_" + this.ja] = this.Ua, !this.bb) {
                        return;
                    }
                } else {
                    return;
                }
            }
            if (window.annotations || !eb.touchdevice || g) {
                if (window.annotations || c.aa.Fc || g || c.aa.Hk || (f = !0), k = null != this.od && null != this.od[c.pageNumber], "ThumbView" != c.ba) {
                    if ("BookView" == c.ba && (0 == c.pageNumber && (h = 0 != c.pages.la ? c.pages.la - 1 : c.pages.la), 1 == c.pageNumber && (h = c.pages.la), 0 == c.pages.getTotalPages() % 2 && h == c.pages.getTotalPages() && (h = h - 1), 0 == c.pages.la % 2 && c.pages.la > c.pages.getTotalPages())) {
                        return;
                    }
                    "SinglePage" == c.ba && (h = c.pages.la);
                    if ("TwoPage" == c.ba && (0 == c.pageNumber && (h = c.pages.la), 1 == c.pageNumber && (h = c.pages.la + 1), 1 == c.pageNumber && h >= c.pages.getTotalPages() && 0 != c.pages.getTotalPages() % 2)) {
                        return;
                    }
                    d = c.ib || !d;
                    c.ba == this.Pa(c) && (isvisble = this.wb(c).Mc(this, c));
                    var l = jQuery(".flowpaper_pageword_" + this.ja + "_page_" + h + ":not(.flowpaper_annotation_" + this.ja + ")").length;
                    g = null != c.dimensions.tb ? c.dimensions.tb : c.dimensions.Ca;
                    g = this.vb ? c.Va() / g : 1;
                    if (d && 0 == l) {
                        var n = l = "",
                            v = 0;
                        if (null == this.bb[h] || !this.vb) {
                            if (null == this.oa[h]) {
                                return;
                            }
                            this.bb[h] = this.oa[h][this.JSONPageDataFormat.ue];
                        }
                        if (null != this.bb[h]) {
                            c.ub && (this.Sa = !0);
                            var u = new WordPage(this.ja, h),
                                p = c.Kc(),
                                q = [],
                                r = c.de(),
                                t = c.hf(),
                                y = !1,
                                C = -1,
                                w = -1,
                                A = 0,
                                z = -1,
                                H = -1,
                                I = !1;
                            this.Ua[h] = u;
                            c.ba == this.Pa(c) && (g = this.wb(c).sn(this, c, g));
                            c.Ir = g;
                            for (var J = 0; y = this.bb[h][J++];) {
                                var G = J - 1,
                                    x = this.Sa ? y[5] : y[this.JSONPageDataFormat.Nb],
                                    F = J,
                                    B = J < this.bb[h].length ? this.bb[h][J] : null,
                                    I = B ? this.Sa ? B[5] : B[this.JSONPageDataFormat.Nb] : "";
                                " " == I && (F = J + 1, I = (B = F < this.bb[h].length ? this.bb[h][F] : null) ? this.Sa ? B[5] : B[this.JSONPageDataFormat.Nb] : "");
                                var M = null,
                                    N = null;
                                if (null == x) {
                                    K("word not found in node");
                                    e && e();
                                    return;
                                }
                                0 == x.length && (x = " ");
                                var D = null;
                                if (-1 == x.indexOf("actionGoToR") && -1 == x.indexOf("actionGoTo") && -1 == x.indexOf("actionURI") && this.Dm(c)) {
                                    if (D = x.match(/\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?\u00ab\u00bb\u201c\u201d\u2018\u2019]))/ig)) {
                                        x = "actionURI(" + D[0] + "):" + D[0], this.bb[h][G][this.Sa ? 5 : this.JSONPageDataFormat.Nb] = x;
                                    }!D && -1 < x.indexOf("@") && (D = (x.trim() + I.trim()).match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi)) && (!x.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi) && B && (I = "actionURI(mailto:" + D[0] + "):" + D[0], this.bb[h][F][this.Sa ? 5 : this.JSONPageDataFormat.Nb] = I), x = "actionURI(mailto:" + D[0] + "):" + D[0], this.bb[h][G][this.Sa ? 5 : this.JSONPageDataFormat.Nb] = x);
                                }
                                if (0 <= x.indexOf("actionGoToR")) {
                                    M = x.substring(x.indexOf("actionGoToR") + 12, x.indexOf(",", x.indexOf("actionGoToR") + 13)), x = x.substring(x.indexOf(",") + 1);
                                } else {
                                    if (0 <= x.indexOf("actionGoTo")) {
                                        M = x.substring(x.indexOf("actionGoTo") + 11, x.indexOf(",", x.indexOf("actionGoTo") + 12)), x = x.substring(x.indexOf(",") + 1);
                                    } else {
                                        if (0 <= x.indexOf("actionURI") || D) {
                                            if (0 <= x.indexOf("actionURI(") && 0 < x.indexOf("):") ? (N = x.substring(x.indexOf("actionURI(") + 10, x.lastIndexOf("):")), x = x.substring(x.indexOf("):") + 2)) : (N = x.substring(x.indexOf("actionURI") + 10), x = x.substring(x.indexOf("actionURI") + 10)), -1 == N.indexOf("http") && -1 == N.indexOf("mailto") && 0 != N.indexOf("/")) {
                                                N = "http://" + N;
                                            } else {
                                                if (!D) {
                                                    for (G = J, F = this.Sa ? y[5] : y[this.JSONPageDataFormat.Nb], B = 1; 2 >= B; B++) {
                                                        for (G = J; G < this.bb[h].length && 0 <= this.bb[h][G].toString().indexOf("actionURI") && -1 == this.bb[h][G].toString().indexOf("actionURI(");) {
                                                            I = this.bb[h][G], D = this.Sa ? I[5] : I[this.JSONPageDataFormat.Nb], 1 == B ? 0 <= D.indexOf("actionURI") && 11 < D.length && -1 == D.indexOf("http://") && -1 == D.indexOf("https://") && -1 == D.indexOf("mailto") && (F += D.substring(D.indexOf("actionURI") + 10)) : this.Sa ? I[5] = F : I[this.JSONPageDataFormat.Nb], G++;
                                                        }
                                                        2 == B && -1 == F.indexOf("actionURI(") && (x = F, N = x.substring(x.indexOf("actionURI") + 10), x = x.substring(x.indexOf("actionURI") + 10));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if (M || N || !f || k) {
                                    G = (this.Sa ? y[0] : y[this.JSONPageDataFormat.wd]) * g + 0;
                                    F = (this.Sa ? y[1] : y[this.JSONPageDataFormat.vd]) * g + 0;
                                    B = (this.Sa ? y[2] : y[this.JSONPageDataFormat.xd]) * g;
                                    D = (this.Sa ? y[3] : y[this.JSONPageDataFormat.ud]) * g;
                                    u.Qo(v, x);
                                    y = -1 != C && C != G;
                                    I = J == this.bb[h].length;
                                    F + B > r && (B = r - F);
                                    G + D > t && (D = t - G);
                                    q[v] = {};
                                    q[v].left = F;
                                    q[v].right = F + B;
                                    q[v].top = G;
                                    q[v].bottom = G + D;
                                    q[v].el = "#" + this.ja + "page_" + h + "_word_" + v;
                                    q[v].i = v;
                                    q[v].Uk = M;
                                    q[v].Rl = N;
                                    l += "<span id='" + this.ja + "page_" + h + "_word_" + v + "' class='flowpaper_pageword flowpaper_pageword_" + this.ja + "_page_" + h + " flowpaper_pageword_" + this.ja + "' style='left:" + F + "px;top:" + G + "px;width:" + B + "px;height:" + D + "px;margin-left:" + p + "px;" + (q[v].Uk || q[v].Rl ? "cursor:hand;" : "") + ";" + (eb.browser.msie ? "background-image:url(" + this.ua + ");color:transparent;" : "") + "'>" + (c.aa.Hk ? x : "") + "</span>";
                                    if (null != M || null != N) {
                                        var L = document.createElement("a");
                                        L.style.position = "absolute";
                                        L.style.left = Math.floor(F) + p + "px";
                                        L.style.top = Math.floor(G) + "px";
                                        L.style.width = Math.ceil(B) + "px";
                                        L.style.height = Math.ceil(D) + "px";
                                        L.style["margin-left"] = p;
                                        L.style.cursor = "pointer";
                                        L.setAttribute("data-href", null != N ? N : "");
                                        jQuery(L).css("z-index", "99");
                                        L.className = "pdfPageLink_" + c.pageNumber + " flowpaper_interactiveobject_" + this.ja + " flowpaper_pageword_" + this.ja + "_page_" + h + " gotoPage_" + M + " flowpaper_pageword_" + this.ja;
                                        eb.platform.touchonlydevice && (L.style.background = c.aa.linkColor, L.style.opacity = c.aa.ge);
                                        null != M && (jQuery(L).data("gotoPage", M), jQuery(L).on("click touchstart", function() {
                                            c.aa.gotoPage(parseInt(jQuery(this).data("gotoPage")));
                                            return !1;
                                        }));
                                        if (null != N) {
                                            jQuery(L).on("click touchstart", function(d) {
                                                jQuery(c.ia).trigger("onExternalLinkClicked", this.getAttribute("data-href"));
                                                d.stopImmediatePropagation();
                                                d.preventDefault();
                                                return !1;
                                            });
                                        }
                                        eb.platform.touchonlydevice || (jQuery(L).on("mouseover", function() {
                                            jQuery(this).stop(!0, !0);
                                            jQuery(this).css("background", c.aa.linkColor);
                                            jQuery(this).css({
                                                opacity: c.aa.ge
                                            });
                                        }), jQuery(L).on("mouseout", function() {
                                            jQuery(this).css("background", "");
                                            jQuery(this).css({
                                                opacity: 0
                                            });
                                        }));
                                        "TwoPage" == c.ba || "BookView" == c.ba ? (0 == c.pageNumber && jQuery(c.ma + "_1_textoverlay").append(L), 1 == c.pageNumber && jQuery(c.ma + "_2_textoverlay").append(L)) : jQuery(c.Ha).append(L);
                                    }
                                    eb.platform.touchdevice && "Portrait" == c.ba && (y || I ? (I && (A += B, n = n + "<div style='float:left;width:" + B + "px'>" + (" " == x ? "&nbsp;" : x) + "</div>"), n = "<div id='" + this.ja + "page_" + h + "_word_" + v + "_wordspan' class='flowpaper_pageword flowpaper_pageword_" + this.ja + "_page_" + h + " flowpaper_pageword_" + this.ja + "' style='color:transparent;left:" + z + "px;top:" + C + "px;width:" + A + "px;height:" + w + "px;margin-left:" + H + "px;font-size:" + w + "px" + (q[v].Uk || q[v].Rl ? "cursor:hand;" : "") + "'>" + n + "</div>", jQuery(c.Ui).append(n), C = G, w = D, A = B, z = F, H = p, n = "<div style='background-colorfloat:left;width:" + B + "px'>" + (" " == x ? "&nbsp;" : x) + "</div>") : (-1 == z && (z = F), -1 == H && (H = p), -1 == C && (C = G), -1 == w && (w = D), n = n + "<div style='float:left;width:" + B + "px'>" + (" " == x ? "&nbsp;" : x) + "</div>", A += B, w = D));
                                }
                                v++;
                            }
                            u.Mo(q);
                            "Portrait" == c.ba && jQuery(c.Rb).append(l);
                            "SinglePage" == c.ba && jQuery(c.Rb).append(l);
                            c.ba == this.Pa(c) && this.wb(c).Cm(this, c, l);
                            if ("TwoPage" == c.ba || "BookView" == c.ba) {
                                0 == c.pageNumber && jQuery(c.ma + "_1_textoverlay").append(l), 1 == c.pageNumber && jQuery(c.ma + "_2_textoverlay").append(l);
                            }
                            d && jQuery(c).trigger("onAddedTextOverlay", c.pageNumber);
                            if (k) {
                                for (h = 0; h < this.od[c.pageNumber].length; h++) {
                                    this.um(c, this.od[c.pageNumber][h].Zo, this.od[c.pageNumber][h].zp);
                                }
                            }
                        }
                    }
                    null != e && e();
                }
            } else {
                e && e();
            }
        },
        vc: function(c, d, e) {
            var g = this;
            window.annotations || jQuery(c).unbind("onAddedTextOverlay");
            var h = "TwoPage" == c.ba || "BookView" == c.ba ? c.pages.la + c.pageNumber : c.pageNumber;
            "BookView" == c.ba && 0 < c.pages.la && 1 == c.pageNumber && (h = h - 2);
            "SinglePage" == c.ba && (h = c.pages.la);
            if ((c.ib || !e) && c.aa.gb - 1 == h) {
                if (jQuery(".flowpaper_selected").removeClass("flowpaper_selected"), jQuery(".flowpaper_selected_searchmatch").removeClass("flowpaper_selected_searchmatch"), jQuery(".flowpaper_selected_default").removeClass("flowpaper_selected_default"), jQuery(".flowpaper_tmpselection").remove(), !g.Ua[h] || null != g.Ua[h] && 0 == g.Ua[h].tg.length) {
                    jQuery(c).bind("onAddedTextOverlay", function() {
                        g.vc(c, d, e);
                    }), g.wc(c, e, null, !0);
                } else {
                    for (var f = g.Ua[h].tg, k = "", l = 0, n = 0, v = -1, u = -1, p = d.split(" "), q = 0; q < f.length; q++) {
                        var r = (f[q] + "").toLowerCase();
                        if (jQuery.trim(r) == d || jQuery.trim(k + r) == d) {
                            r = jQuery.trim(r);
                        }
                        if (0 == d.indexOf(k + r) && (k + r).length <= d.length && " " != k + r) {
                            if (k += r, -1 == v && (v = l, u = l + 1), d.length == r.length && (v = l), k.length == d.length) {
                                if (n++, c.aa.me == n) {
                                    if ("Portrait" == c.ba || "SinglePage" == c.ba) {
                                        eb.browser.qb.Ab ? jQuery("#pagesContainer_" + g.ja).scrollTo(jQuery(g.Ua[h].positions[v].el), 0, {
                                            axis: "xy",
                                            offset: -30
                                        }) : jQuery("#pagesContainer_" + g.ja).data("jsp").scrollToElement(jQuery(g.Ua[h].positions[v].el), !1);
                                    }
                                    for (var t = v; t < l + 1; t++) {
                                        c.ba == g.Pa(c) ? (r = jQuery(g.Ua[h].positions[t].el).clone(), g.wb(c).Sj(g, c, r, d)) : (jQuery(g.Ua[h].positions[t].el).addClass("flowpaper_selected"), jQuery(g.Ua[h].positions[t].el).addClass("flowpaper_selected_default"), jQuery(g.Ua[h].positions[t].el).addClass("flowpaper_selected_searchmatch"));
                                    }
                                } else {
                                    k = "", v = -1;
                                }
                            }
                        } else {
                            if (0 <= (k + r).indexOf(p[0])) {
                                -1 == v && (v = l, u = l + 1);
                                k += r;
                                if (1 < p.length) {
                                    for (r = 0; r < p.length - 1; r++) {
                                        0 < p[r].length && f.length > l + 1 + r && 0 <= (k + f[l + 1 + r]).toLowerCase().indexOf(p[r]) ? (k += f[l + 1 + r].toLowerCase(), u = l + 1 + r + 1) : (k = "", u = v = -1);
                                    }
                                } - 1 == k.indexOf(d) ? (k = "", u = v = -1) : n++;
                                if (c.aa.me == n && 0 < k.length) {
                                    for (var t = jQuery(g.Ua[h].positions[v].el), y = parseFloat(t.css("left").substring(0, t.css("left").length - 2)) - (c.ba == g.Pa(c) ? c.Kc() : 0), r = t.clone(), C = 0, w = 0, A = 0; v < u; v++) {
                                        C += parseFloat(jQuery(g.Ua[h].positions[v].el).css("width").substring(0, t.css("width").length - 2));
                                    }
                                    w = 1 - (k.length - d.length) / k.length;
                                    A = k.indexOf(d) / k.length;
                                    r.addClass("flowpaper_tmpselection");
                                    r.attr("id", r.attr("id") + "tmp");
                                    r.addClass("flowpaper_selected");
                                    r.addClass("flowpaper_selected_searchmatch");
                                    r.addClass("flowpaper_selected_default");
                                    r.css("width", C * w + "px");
                                    r.css("left", y + C * A + "px");
                                    if ("Portrait" == c.ba || "SinglePage" == c.ba) {
                                        jQuery(c.Ha).append(r), eb.browser.qb.Ab ? jQuery("#pagesContainer_" + g.ja).scrollTo(r, 0, {
                                            axis: "xy",
                                            offset: -30
                                        }) : jQuery("#pagesContainer_" + g.ja).data("jsp").scrollToElement(r, !1);
                                    }
                                    c.ba == g.Pa(c) && g.wb(c).Sj(g, c, r, d);
                                    "SinglePage" == c.ba && jQuery("#dummyPage_0_" + g.ja + "_textoverlay").append(r);
                                    "BookView" == c.ba && (0 == h ? jQuery("#dummyPage_0_" + g.ja + "_1_textoverlay").append(r) : jQuery("#dummyPage_" + (h - 1) % 2 + "_" + g.ja + "_" + ((h - 1) % 2 + 1) + "_textoverlay").append(r));
                                    "TwoPage" == c.ba && jQuery("#dummyPage_" + h % 2 + "_" + g.ja + "_" + (h % 2 + 1) + "_textoverlay").append(r);
                                } else {
                                    k = "";
                                }
                                u = v = -1;
                            } else {
                                0 < k.length && (k = "", v = -1);
                            }
                        }
                        l++;
                    }
                }
            }
        },
        Ae: function(c, d, e) {
            null == this.od && (this.od = Array(this.bb.length));
            null == this.od[c.pageNumber] && (this.od[c.pageNumber] = []);
            var g = {};
            g.Zo = d;
            g.zp = e;
            this.od[c.pageNumber][this.od[c.pageNumber].length] = g;
        },
        um: function(c, d, e) {
            jQuery(c).unbind("onAddedTextOverlay");
            var g = "TwoPage" == c.ba || "BookView" == c.ba ? c.pages.la + c.pageNumber : c.pageNumber;
            "BookView" == c.ba && 0 < c.pages.la && 1 == c.pageNumber && (g = g - 2);
            "SinglePage" == c.ba && (g = c.pages.la);
            for (var h = this.Ua[g].tg, f = -1, k = -1, l = 0, n = 0; n < h.length; n++) {
                var v = h[n] + "";
                l >= d && -1 == f && (f = n);
                if (l + v.length >= d + e && -1 == k && (k = n, -1 != f)) {
                    break;
                }
                l += v.length;
            }
            for (d = f; d < k + 1; d++) {
                c.ba == this.Pa(c) ? jQuery(this.Ua[g].positions[d].el).clone() : (jQuery(this.Ua[g].positions[d].el).addClass("flowpaper_selected"), jQuery(this.Ua[g].positions[d].el).addClass("flowpaper_selected_yellow"), jQuery(this.Ua[g].positions[d].el).addClass("flowpaper_selected_searchmatch"));
            }
        },
        La: function(c, d) {
            this.wc(c, null == d, d);
        }
    };
    return f;
}();
window.WordPage = function(f, c) {
    this.ja = f;
    this.pageNumber = c;
    this.tg = [];
    this.positions = null;
    this.Qo = function(c, e) {
        this.tg[c] = e;
    };
    this.Mo = function(c) {
        this.positions = c;
    };
    this.match = function(c, e) {
        var g, h = null;
        g = "#page_" + this.pageNumber + "_" + this.ja;
        0 == jQuery(g).length && (g = "#dummyPage_" + this.pageNumber + "_" + this.ja);
        g = jQuery(g).offset();
        "SinglePage" == window.$FlowPaper(this.ja).ba && (g = "#dummyPage_0_" + this.ja, g = jQuery(g).offset());
        if ("TwoPage" == window.$FlowPaper(this.ja).ba || "BookView" == window.$FlowPaper(this.ja).ba) {
            g = 0 == this.pageNumber || "TwoPage" == window.$FlowPaper(this.ja).ba ? jQuery("#dummyPage_" + this.pageNumber % 2 + "_" + this.ja + "_" + (this.pageNumber % 2 + 1) + "_textoverlay").offset() : jQuery("#dummyPage_" + (this.pageNumber - 1) % 2 + "_" + this.ja + "_" + ((this.pageNumber - 1) % 2 + 1) + "_textoverlay").offset();
        }
        c.top = c.top - g.top;
        c.left = c.left - g.left;
        for (g = 0; g < this.positions.length; g++) {
            this.In(c, this.positions[g], e) && (null == h || null != h && h.top < this.positions[g].top || null != h && h.top <= this.positions[g].top && null != h && h.left < this.positions[g].left) && (h = this.positions[g], h.pageNumber = this.pageNumber);
        }
        return h;
    };
    this.Qk = function(c) {
        for (var e = 0; e < this.positions.length; e++) {
            if (this.positions[e].el == "#" + c) {
                return this.positions[e];
            }
        }
        return null;
    };
    this.In = function(c, e, g) {
        return e ? g ? c.left + 3 >= e.left && c.left - 3 <= e.right && c.top + 3 >= e.top && c.top - 3 <= e.bottom : c.left + 3 >= e.left && c.top + 3 >= e.top : !1;
    };
    this.bf = function(c, e) {
        var g = window.a,
            h = window.b,
            f = new ha,
            k, l, n = 0,
            v = -1;
        if (null == g) {
            return f;
        }
        if (g && h) {
            var u = [],
                p;
            g.top > h.top ? (k = h, l = g) : (k = g, l = h);
            for (k = k.i; k <= l.i; k++) {
                if (this.positions[k]) {
                    var q = jQuery(this.positions[k].el);
                    0 != q.length && (p = parseInt(q.attr("id").substring(q.attr("id").indexOf("word_") + 5)), v = parseInt(q.attr("id").substring(q.attr("id").indexOf("page_") + 5, q.attr("id").indexOf("word_") - 1)) + 1, 0 <= p && u.push(this.tg[p]), n++, c && (q.addClass("flowpaper_selected"), q.addClass(e), "flowpaper_selected_strikeout" != e || q.data("adjusted") || (p = q.height(), q.css("margin-top", p / 2 - p / 3 / 1.5), q.height(p / 2.3), q.data("adjusted", !0))));
                }
            }
            eb.platform.touchdevice || jQuery(".flowpaper_selector").val(u.join("")).select();
        } else {
            eb.platform.touchdevice || jQuery("#selector").val("");
        }
        f.er = n;
        f.Wr = g.left;
        f.Xr = g.right;
        f.Yr = g.top;
        f.Vr = g.bottom;
        f.Rr = g.left;
        f.Sr = g.right;
        f.Ur = g.top;
        f.Qr = g.bottom;
        f.hn = null != u && 0 < u.length ? u[0] : null;
        f.mr = null != u && 0 < u.length ? u[u.length - 1] : f.hn;
        f.jn = null != g ? g.i : -1;
        f.nr = null != h ? h.i : f.jn;
        f.text = null != u ? u.join("") : "";
        f.page = v;
        f.Pr = this;
        return f;
    };
};

function ha() {}

function T(f) {
    var c = hoverPage;
    if (f = window["wordPageList_" + f]) {
        return f.length >= c ? f[c] : null;
    }
}
var V = function() {
        function f(c, d, e, g) {
            this.aa = d;
            this.ia = c;
            this.pages = {};
            this.selectors = {};
            this.container = "pagesContainer_" + e;
            this.da = "#" + this.container;
            this.la = null == g ? 0 : g - 1;
            this.te = g;
            this.Md = this.Cf = null;
            this.Pc = this.Oc = -1;
            this.ke = this.nd = 0;
            this.initialized = !1;
            this.ja = this.aa.ja;
            this.document = this.aa.document;
        }
        f.prototype = {
            ga: function(c) {
                if (0 < c.indexOf("undefined")) {
                    return jQuery(null);
                }
                this.selectors || (this.selectors = {});
                this.selectors[c] || (this.selectors[c] = jQuery(c));
                return this.selectors[c];
            },
            Ti: function() {
                null != this.gi && (window.clearTimeout(this.gi), this.gi = null);
                this.aa.ca && this.aa.ba == this.aa.ca.na && this.aa.ca.pb.Ti(this);
            },
            hc: function() {
                return this.aa.ca && this.aa.ba == this.aa.ca.na && this.aa.ca.pb.hc(this) || "SinglePage" == this.aa.ba;
            },
            zo: function() {
                return !(this.aa.ca && this.aa.ca.pb.hc(this));
            },
            Xa: function(c, d, e) {
                var g = this.aa.scale;
                this.aa.scale = c;
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    var h = 100 * c + "%";
                    eb.platform.touchdevice || this.ga(this.da).css({
                        width: h,
                        "margin-left": this.kf()
                    });
                }
                this.pages[0] && (this.pages[0].scale = c);
                for (h = 0; h < this.document.numPages; h++) {
                    this.kb(h) && (this.pages[h].scale = c, this.pages[h].Xa());
                }
                this.aa.ca && this.aa.ba == this.aa.ca.na && this.aa.ca.pb.Xa(this, g, c, d, e);
            },
            dispose: function() {
                for (var c = 0; c < this.document.numPages; c++) {
                    this.pages[c].dispose(), delete this.pages[c];
                }
                this.selectors = this.pages = this.ia = this.aa = null;
            },
            resize: function(c, d, e) {
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    d += eb.browser.qb.Ab ? 0 : 14, c = c - (eb.browser.msie ? 0 : 2);
                }
                "ThumbView" == this.aa.ba && (d = d - 10);
                this.ga(this.da).css({
                    width: c,
                    height: d
                });
                "TwoPage" == this.aa.ba && (this.aa.wj = this.ia.height() - (eb.platform.touchdevice ? 0 : 27), this.aa.pg = c / 2 - 2, this.ga(this.da).height(this.aa.wj), this.ga("#" + this.container + "_2").css("left", this.ga("#" + this.container).width() / 2), eb.platform.touchdevice || (this.ga(this.da + "_1").width(this.aa.pg), this.ga(this.da + "_2").width(this.aa.pg)));
                if (this.aa.ca && this.aa.ba == this.aa.ca.na) {
                    this.aa.ca.pb.resize(this, c, d, e);
                } else {
                    for (this.qd(), c = 0; c < this.document.numPages; c++) {
                        this.kb(c) && this.pages[c].Xa();
                    }
                }
                this.xj = null;
                null != this.jScrollPane && (this.jScrollPane.data("jsp").reinitialise(this.Nc), this.jScrollPane.data("jsp").scrollTo(this.Oc, this.Pc, !1));
            },
            fe: function(c) {
                var d = this;
                if (!d.sa) {
                    var e = !1;
                    "function" === typeof d.yi && d.ar();
                    jQuery(".flowpaper_pageword").each(function() {
                        jQuery(this).hasClass("flowpaper_selected_default") && (e = !0);
                    });
                    null != d.touchwipe && (d.touchwipe.config.preventDefaultEvents = !1);
                    d.hc() || (jQuery(".flowpaper_pageword_" + d.ja).remove(), setTimeout(function() {
                        "TwoPage" != d.aa.ba && "BookView" != d.aa.ba || d.ec();
                        d.La();
                        e && d.getPage(d.aa.gb - 1).vc(d.aa.Nd, !1);
                    }, 500));
                    d.aa.ca && d.aa.ba == d.aa.ca.na ? d.aa.ca.pb.fe(d, c) : d.Xa(1);
                    null != d.jScrollPane ? (d.jScrollPane.data("jsp").reinitialise(d.Nc), d.jScrollPane.data("jsp").scrollTo(d.Oc, d.Pc, !1)) : "TwoPage" != d.aa.ba && "BookView" != d.aa.ba || d.ga(d.da).parent().scrollTo({
                        left: d.Oc + "px",
                        top: d.Pc + "px"
                    }, 0, {
                        axis: "xy"
                    });
                }
            },
            dd: function(c) {
                var d = this;
                if (!d.sa) {
                    var e = !1;
                    null != d.touchwipe && (d.touchwipe.config.preventDefaultEvents = !0);
                    "function" === typeof d.yi && d.cr();
                    jQuery(".flowpaper_pageword").each(function() {
                        jQuery(this).hasClass("flowpaper_selected_default") && (e = !0);
                    });
                    d.hc() || jQuery(".flowpaper_pageword_" + d.ja).remove();
                    d.aa.ca && d.aa.ba == d.aa.ca.na ? d.aa.ca.pb.dd(d, c) : d.Xa(window.FitHeightScale);
                    setTimeout(function() {
                        d.La();
                        e && d.getPage(d.aa.gb - 1).vc(d.aa.Nd, !1);
                    }, 500);
                    d.La();
                    null != d.jScrollPane ? (d.jScrollPane.data("jsp").scrollTo(0, 0, !1), d.jScrollPane.data("jsp").reinitialise(d.Nc)) : d.ga(d.da).parent().scrollTo({
                        left: 0,
                        top: 0
                    }, 0, {
                        axis: "xy"
                    });
                }
            },
            Si: function() {
                var c = this;
                c.Ce();
                if (c.aa.ca && c.aa.ba == c.aa.ca.na) {
                    c.aa.ca.pb.Si(c);
                } else {
                    if ("SinglePage" == c.aa.ba || "TwoPage" == c.aa.ba || "BookView" == c.aa.ba) {
                        c.touchwipe = c.ga(c.da).touchwipe({
                            wipeLeft: function() {
                                if (!c.aa.Lc && !window.Cb && null == c.sa && ("TwoPage" != c.aa.ba && "BookView" != c.aa.ba || 1 == c.aa.scale || c.next(), "SinglePage" == c.aa.ba)) {
                                    var d = jQuery(c.da).width() - 5,
                                        e = 1 < c.aa.getTotalPages() ? c.aa.ta - 1 : 0;
                                    0 > e && (e = 0);
                                    var g = c.getPage(e).dimensions.Ca / c.getPage(e).dimensions.Na,
                                        d = Math.round(100 * (d / (c.getPage(e).ab * g) - 0.03));
                                    100 * c.aa.scale < 1.2 * d && c.next();
                                }
                            },
                            wipeRight: function() {
                                if (!c.aa.Lc && !window.Cb && null == c.sa && ("TwoPage" != c.aa.ba && "BookView" != c.aa.ba || 1 == c.aa.scale || c.previous(), "SinglePage" == c.aa.ba)) {
                                    var d = jQuery(c.da).width() - 15,
                                        e = 1 < c.aa.getTotalPages() ? c.aa.ta - 1 : 0;
                                    0 > e && (e = 0);
                                    var g = c.getPage(e).dimensions.Ca / c.getPage(e).dimensions.Na,
                                        d = Math.round(100 * (d / (c.getPage(e).ab * g) - 0.03));
                                    100 * c.aa.scale < 1.2 * d && c.previous();
                                }
                            },
                            preventDefaultEvents: "TwoPage" == c.aa.ba || "BookView" == c.aa.ba || "SinglePage" == c.aa.ba,
                            min_move_x: eb.platform.Hb ? 150 : 200,
                            min_move_y: 500
                        });
                    }
                }
                if (eb.platform.mobilepreview) {
                    c.ga(c.da).on("mousedown", function(d) {
                        c.Oc = d.pageX;
                        c.Pc = d.pageY;
                    });
                }
                c.ga(c.da).on("touchstart", function(d) {
                    c.Oc = d.originalEvent.touches[0].pageX;
                    c.Pc = d.originalEvent.touches[0].pageY;
                });
                c.ga(c.da).on(eb.platform.mobilepreview ? "mouseup" : "touchend", function() {
                    null != c.aa.pages.jScrollPane && c.aa.pages.jScrollPane.data("jsp").enable && c.aa.pages.jScrollPane.data("jsp").enable();
                    if (null != c.xb) {
                        for (var d = 0; d < c.document.numPages; d++) {
                            c.kb(d) && c.ga(c.pages[d].Ka).transition({
                                y: 0,
                                scale: 1
                            }, 0, "ease", function() {
                                c.sa > c.aa.scale && c.sa - c.aa.scale < c.aa.document.ZoomInterval && (c.sa += c.aa.document.ZoomInterval);
                                0 < c.Hc - c.Wd && c.sa < c.aa.scale && (c.sa = c.aa.scale + c.aa.document.ZoomInterval);
                                c.aa.lb(c.sa, {
                                    Gg: !0
                                });
                                c.sa = null;
                            });
                        }
                        c.pages[0] && c.pages[0].Ce();
                        c.ga(c.da).addClass("flowpaper_pages_border");
                        c.Wi = c.xb < c.sa;
                        c.xb = null;
                        c.jg = null;
                        c.sa = null;
                        c.zb = null;
                        c.nc = null;
                    }
                });
                c.aa.ca && c.aa.ba == c.aa.ca.na ? c.aa.ca.pb.Vj(c) : eb.platform.touchdevice && c.ga(c.da).doubletap(function(d) {
                    if ("TwoPage" == c.aa.ba || "BookView" == c.aa.ba) {
                        "TwoPage" != c.aa.ba && "BookView" != c.aa.ba || 1 == c.aa.scale ? "TwoPage" != c.aa.ba && "BookView" != c.aa.ba || 1 != c.aa.scale || c.dd() : c.fe(), d.preventDefault();
                    }
                }, null, 300);
                c.ga(c.da).on("scroll gesturechange", function() {
                    "SinglePage" == c.aa.ba ? c.aa.renderer.sb && !c.sa && c.aa.renderer.Vc(c.pages[0]) : c.aa.ca && c.aa.ba == c.aa.ca.na || (eb.platform.ios && c.cj(-1 * c.ga(c.da).scrollTop()), eb.platform.ios ? (setTimeout(function() {
                        c.sg();
                        c.le();
                    }, 1000), setTimeout(function() {
                        c.sg();
                        c.le();
                    }, 2000), setTimeout(function() {
                        c.sg();
                        c.le();
                    }, 3000)) : c.sg(), c.le(), c.La(), null != c.Cf && (window.clearTimeout(c.Cf), c.Cf = null), c.Cf = setTimeout(function() {
                        c.Bk();
                        window.clearTimeout(c.Cf);
                        c.Cf = null;
                    }, 100), c.wr = !0);
                });
                this.Bk();
            },
            Vj: function() {},
            cj: function(c) {
                for (var d = 0; d < this.document.numPages; d++) {
                    this.kb(d) && this.pages[d].cj(c);
                }
            },
            Kl: function() {
                var c = this.ga(this.da).css("transform") + "";
                null != c && (c = c.replace("translate", ""), c = c.replace("(", ""), c = c.replace(")", ""), c = c.replace("px", ""), c = c.split(","), this.nd = parseFloat(c[0]), this.ke = parseFloat(c[1]), isNaN(this.nd) && (this.ke = this.nd = 0));
            },
            $j: function(c, d) {
                this.ga(this.da).transition({
                    x: this.nd + (c - this.zb) / this.aa.scale,
                    y: this.ke + (d - this.nc) / this.aa.scale
                }, 0);
            },
            Fg: function(c, d) {
                this.aa.ca && this.aa.ca.pb.Fg(this, c, d);
            },
            un: function(c, d) {
                var e = this.ia.width();
                return c / d - this.zd / e / d * e;
            },
            vn: function(c) {
                var d = this.ia.height();
                return c / this.aa.scale - this.Ad / d / this.aa.scale * d;
            },
            Ce: function() {
                this.aa.ca && this.aa.ca.pb.Ce(this);
            },
            vi: function() {
                if (this.aa.ca) {
                    return this.aa.ca.pb.vi(this);
                }
            },
            getTotalPages: function() {
                return this.document.numPages;
            },
            Xh: function(c) {
                var d = this;
                c.empty();
                jQuery(d.aa.renderer).on("onTextDataUpdated", function() {
                    d.La(d);
                });
                null != d.aa.Md || d.aa.document.DisableOverflow || d.aa.Zb || (d.aa.Md = d.ia.height(), eb.platform.touchonlydevice ? d.aa.Yd || d.ia.height(d.aa.Md - 10) : d.ia.height(d.aa.Md - 27));
                var e = d.aa.ca && d.aa.ca.backgroundColor ? "background-color:" + d.aa.ca.backgroundColor + ";" : "";
                d.aa.ca && d.aa.ca.backgroundImage && (e = "background-color:transparent;");
                if ("Portrait" == d.aa.ba || "SinglePage" == d.aa.ba) {
                    eb.platform.touchonlydevice && "SinglePage" == d.aa.ba && (eb.browser.qb.Ab = !1);
                    var g = jQuery(d.aa.ea).height(),
                        h = eb.platform.touchonlydevice ? 31 : 26,
                        g = d.ia.height() + (eb.browser.qb.Ab ? window.annotations ? 0 : h - g : -5),
                        h = d.ia.width() - 2,
                        f = 1 < d.te ? "visibility:hidden;" : "",
                        k = eb.browser.msie && 9 > eb.browser.version ? "position:relative;" : "";
                    d.aa.document.DisableOverflow ? c.append("<div id='" + d.container + "' class='flowpaper_pages' style='overflow:hidden;padding:0;margin:0;'></div>") : c.append("<div id='" + d.container + "' class='flowpaper_pages " + (window.annotations ? "" : "flowpaper_pages_border") + "' style='" + (eb.platform.Sl ? "touch-action: none;" : "") + "-moz-user-select:none;-webkit-user-select:none;" + k + ";" + f + "height:" + g + "px;width:" + h + "px;overflow-y: auto;overflow-x: auto;;-webkit-overflow-scrolling: touch;-webkit-backface-visibility: hidden;-webkit-perspective: 1000;" + e + ";'></div>");
                    eb.browser.qb.Ab ? eb.platform.touchonlydevice ? (jQuery(c).css("overflow-y", "auto"), jQuery(c).css("overflow-x", "auto"), jQuery(c).css("-webkit-overflow-scrolling", "touch")) : (jQuery(c).css("overflow-y", "visible"), jQuery(c).css("overflow-x", "visible"), jQuery(c).css("-webkit-overflow-scrolling", "visible")) : jQuery(c).css("-webkit-overflow-scrolling", "hidden");
                    eb.platform.touchdevice && (eb.platform.ipad || eb.platform.iphone || eb.platform.android || eb.platform.Sl) && (jQuery(d.da).on("touchmove", function(c) {
                        if (!eb.platform.ios && 2 == c.originalEvent.touches.length && (d.aa.pages.jScrollPane && d.aa.pages.jScrollPane.data("jsp").disable(), 1 != d.bi)) {
                            c.preventDefault && c.preventDefault();
                            c.returnValue = !1;
                            c = Math.sqrt((c.originalEvent.touches[0].pageX - c.originalEvent.touches[1].pageX) * (c.originalEvent.touches[0].pageX - c.originalEvent.touches[1].pageX) + (c.originalEvent.touches[0].pageY - c.originalEvent.touches[1].pageY) * (c.originalEvent.touches[0].pageY - c.originalEvent.touches[1].pageY));
                            c *= 2;
                            null == d.sa && (d.ga(d.da).removeClass("flowpaper_pages_border"), d.xb = 1, d.jg = c);
                            null == d.sa && (d.xb = 1, d.Wd = 1 + (jQuery(d.pages[0].Ka).width() - d.ia.width()) / d.ia.width());
                            var e = c = (d.xb + (c - d.jg) / jQuery(d.da).width() - d.xb) / d.xb;
                            d.hc() || (1 < e && (e = 1), -0.3 > e && (e = -0.3), 0 < c && (c *= 0.7));
                            d.Hc = d.Wd + d.Wd * c;
                            d.Hc < d.aa.document.MinZoomSize && (d.Hc = d.aa.document.MinZoomSize);
                            d.Hc > d.aa.document.MaxZoomSize && (d.Hc = d.aa.document.MaxZoomSize);
                            d.rc = 1 + (d.Hc - d.Wd);
                            d.sa = d.pages[0].qk(jQuery(d.pages[0].Ka).width() * d.rc);
                            d.sa < d.aa.document.MinZoomSize && (d.sa = d.aa.document.MinZoomSize);
                            d.sa > d.aa.document.MaxZoomSize && (d.sa = d.aa.document.MaxZoomSize);
                            jQuery(d.pages[0].Ka).width() > jQuery(d.pages[0].Ka).height() ? d.sa < d.aa.Ng() && (d.rc = d.fg, d.sa = d.aa.Ng()) : d.sa < d.aa.Me() && (d.rc = d.fg, d.sa = d.aa.Me());
                            d.fg = d.rc;
                            if (d.hc() && 0 < d.rc) {
                                for (jQuery(".flowpaper_annotation_" + d.ja).hide(), c = 0; c < d.document.numPages; c++) {
                                    d.kb(c) && jQuery(d.pages[c].Ka).transition({
                                        transformOrigin: "50% 50%",
                                        scale: d.rc
                                    }, 0, "ease", function() {});
                                }
                            }
                        }
                    }), jQuery(d.da).on("touchstart", function() {}), jQuery(d.da).on("gesturechange", function(c) {
                        if (1 != d.wp && 1 != d.bi) {
                            d.aa.renderer.sb && jQuery(".flowpaper_flipview_canvas_highres").hide();
                            null == d.sa && (d.xb = 1, d.Wd = 1 + (jQuery(d.pages[0].Ka).width() - d.ia.width()) / d.ia.width());
                            var e, g = e = (c.originalEvent.scale - d.xb) / d.xb;
                            d.hc() || (1 < g && (g = 1), -0.3 > g && (g = -0.3), 0 < e && (e *= 0.7));
                            d.Hc = d.Wd + d.Wd * e;
                            d.Hc < d.aa.document.MinZoomSize && (d.Hc = d.aa.document.MinZoomSize);
                            d.Hc > d.aa.document.MaxZoomSize && (d.Hc = d.aa.document.MaxZoomSize);
                            d.rc = 1 + (d.Hc - d.Wd);
                            d.sa = d.pages[0].qk(jQuery(d.pages[0].Ka).width() * d.rc);
                            jQuery(d.pages[0].Ka).width() > jQuery(d.pages[0].Ka).height() ? d.sa < d.aa.Ng() && (d.rc = d.fg, d.sa = d.aa.Ng()) : d.sa < d.aa.Me() && (d.rc = d.fg, d.sa = d.aa.Me());
                            d.sa < d.aa.document.MinZoomSize && (d.sa = d.aa.document.MinZoomSize);
                            d.sa > d.aa.document.MaxZoomSize && (d.sa = d.aa.document.MaxZoomSize);
                            c.preventDefault && c.preventDefault();
                            d.fg = d.rc;
                            if (d.hc() && 0 < d.rc) {
                                for (jQuery(".flowpaper_annotation_" + d.ja).hide(), c = 0; c < d.document.numPages; c++) {
                                    d.kb(c) && jQuery(d.pages[c].Ka).transition({
                                        transformOrigin: "50% 50%",
                                        scale: d.rc
                                    }, 0, "ease", function() {});
                                }
                            }!d.hc() && (0.7 <= g || -0.3 >= g) && (d.wp = !0, d.sa > d.aa.scale && d.sa - d.aa.scale < d.aa.document.ZoomInterval && (d.sa += d.aa.document.ZoomInterval), d.aa.lb(d.sa), d.sa = null);
                        }
                    }), jQuery(d.da).on("gestureend", function() {}));
                }
                if ("TwoPage" == d.aa.ba || "BookView" == d.aa.ba) {
                    g = d.ia.height() - (eb.browser.msie ? 37 : 0), h = d.ia.width() - (eb.browser.msie ? 0 : 20), e = 0, 1 == d.aa.ta && "BookView" == d.aa.ba && (e = h / 3, h -= e), eb.platform.touchdevice ? eb.browser.qb.Ab ? (c.append("<div id='" + d.container + "' style='-moz-user-select:none;-webkit-user-select:none;margin-left:" + e + "px;position:relative;width:100%;' class='flowpaper_twopage_container'><div id='" + d.container + "_1' class='flowpaper_pages' style='position:absolute;top:0px;height:99%;margin-top:20px;'></div><div id='" + d.container + "_2' class='flowpaper_pages' style='position:absolute;top:0px;height:99%;margin-top:20px;'></div></div>"), jQuery(c).css("overflow-y", "scroll"), jQuery(c).css("overflow-x", "scroll"), jQuery(c).css("-webkit-overflow-scrolling", "touch")) : (c.append("<div id='" + d.container + "_jpane' style='-moz-user-select:none;-webkit-user-select:none;height:" + g + "px;width:100%;" + (window.eb.browser.msie || eb.platform.android ? "overflow-y: scroll;overflow-x: scroll;" : "overflow-y: auto;overflow-x: auto;") + ";-webkit-overflow-scrolling: touch;'><div id='" + d.container + "' style='margin-left:" + e + "px;position:relative;height:100%;width:100%' class='flowpaper_twopage_container'><div id='" + d.container + "_1' class='flowpaper_pages' style='position:absolute;top:0px;height:99%;margin-top:20px;'></div><div id='" + d.container + "_2' class='flowpaper_pages' style='position:absolute;top:0px;height:99%;margin-top:20px;'></div></div></div>"), jQuery(c).css("overflow-y", "visible"), jQuery(c).css("overflow-x", "visible"), jQuery(c).css("-webkit-overflow-scrolling", "visible")) : (c.append("<div id='" + d.container + "' style='-moz-user-select:none;-webkit-user-select:none;margin-left:" + e + "px;position:relative;' class='flowpaper_twopage_container'><div id='" + d.container + "_1' class='flowpaper_pages' style='position:absolute;top:0px;height:99%;margin-top:" + (eb.browser.msie ? 10 : 20) + "px;'></div><div id='" + d.container + "_2' class='flowpaper_pages " + ("BookView" == d.aa.ba && 2 > d.te ? "flowpaper_hidden" : "") + "' style='position:absolute;top:0px;height:99%;margin-top:" + (eb.browser.msie ? 10 : 20) + "px;'></div></div>"), jQuery(c).css("overflow-y", "auto"), jQuery(c).css("overflow-x", "auto"), jQuery(c).css("-webkit-overflow-scrolling", "touch")), null == d.aa.wj && (d.aa.wj = d.ia.height() - (eb.platform.touchdevice ? 0 : 27), d.aa.pg = d.ga(d.da).width() / 2 - 2), d.ga(d.da).css({
                        height: "90%"
                    }), d.ga("#" + this.container + "_2").css("left", d.ga("#" + d.container).width() / 2), eb.platform.touchdevice || (d.ga(d.da + "_1").width(d.aa.pg), d.ga(d.da + "_2").width(d.aa.pg));
                }
                "ThumbView" == d.aa.ba && (jQuery(c).css("overflow-y", "visible"), jQuery(c).css("overflow-x", "visible"), jQuery(c).css("-webkit-overflow-scrolling", "visible"), k = eb.browser.msie && 9 > eb.browser.version ? "position:relative;" : "", c.append("<div id='" + this.container + "' class='flowpaper_pages' style='" + k + ";" + (eb.platform.touchdevice ? "padding-left:10px;" : "") + (eb.browser.msie ? "overflow-y: scroll;overflow-x: hidden;" : "overflow-y: auto;overflow-x: hidden;-webkit-overflow-scrolling: touch;") + "'></div>"), jQuery(".flowpaper_pages").height(d.ia.height() - 0));
                d.aa.ca && d.aa.ca.pb.Xh(d, c);
                d.ia.trigger("onPagesContainerCreated");
                jQuery(d).bind("onScaleChanged", d.Ti);
            },
            create: function(c) {
                var d = this;
                d.Xh(c);
                eb.browser.qb.Ab || "ThumbView" == d.aa.ba || (d.Nc = {}, "TwoPage" != d.aa.ba && "BookView" != d.aa.ba) || (d.jScrollPane = d.ga(d.da + "_jpane").jScrollPane(d.Nc));
                for (c = 0; c < this.document.numPages; c++) {
                    d.kb(c) && this.addPage(c);
                }
                d.Si();
                if (!eb.browser.qb.Ab) {
                    if ("Portrait" == d.aa.ba || "SinglePage" == d.aa.ba) {
                        d.jScrollPane = d.ga(this.da).jScrollPane(d.Nc);
                    }!window.zine || d.aa.ca && d.aa.ca.na == d.aa.ba || jQuery(d.ga(this.da)).bind("jsp-initialised", function() {
                        jQuery(this).find(".jspHorizontalBar, .jspVerticalBar").hide();
                    }).jScrollPane().hover(function() {
                        jQuery(this).find(".jspHorizontalBar, .jspVerticalBar").stop().fadeTo("fast", 0.9);
                    }, function() {
                        jQuery(this).find(".jspHorizontalBar, .jspVerticalBar").stop().fadeTo("fast", 0);
                    });
                }
                eb.browser.qb.Ab || "ThumbView" != d.aa.ba || (d.jScrollPane = d.ga(d.da).jScrollPane(d.Nc));
                1 < d.te && "Portrait" == d.aa.ba && setTimeout(function() {
                    d.scrollTo(d.te, !0);
                    d.te = -1;
                    jQuery(d.da).css("visibility", "visible");
                }, 500);
                d.te && "SinglePage" == d.aa.ba && jQuery(d.da).css("visibility", "visible");
            },
            getPage: function(c) {
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    if (0 != c % 2) {
                        return this.pages[1];
                    }
                    if (0 == c % 2) {
                        return this.pages[0];
                    }
                } else {
                    return "SinglePage" == this.aa.ba ? this.pages[0] : this.pages[c];
                }
            },
            kb: function(c) {
                if (this.document.DisplayRange) {
                    var d = this.document.DisplayRange.split("-");
                    if (c + 1 >= parseInt(d[0]) && c <= parseInt(d[1]) - 1) {
                        return !0;
                    }
                } else {
                    return ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) && (0 == c || 1 == c) || "TwoPage" != this.aa.ba && "BookView" != this.aa.ba;
                }
            },
            addPage: function(c) {
                this.pages[c] = new U(this.ja, c, this, this.ia, this.aa, this.Pg(c));
                this.pages[c].create(this.ga(this.da));
                jQuery(this.aa.ia).trigger("onPageCreated", c);
            },
            Pg: function(c) {
                for (var d = 0; d < this.document.dimensions.length; d++) {
                    if (this.document.dimensions[d].page == c) {
                        return this.document.dimensions[d];
                    }
                }
                return {
                    width: -1,
                    height: -1
                };
            },
            scrollTo: function(c, d) {
                if (this.la + 1 != c || d) {
                    !eb.browser.qb.Ab && this.jScrollPane ? this.jScrollPane.data("jsp").scrollToElement(this.pages[c - 1].ga(this.pages[c - 1].Ha), !0, !1) : jQuery(this.da).scrollTo && jQuery(this.da).scrollTo(this.pages[c - 1].ga(this.pages[c - 1].Ha), 0);
                }
                this.La();
            },
            Bo: function() {
                for (var c = 0; c < this.getTotalPages(); c++) {
                    this.kb(c) && this.pages[c] && this.pages[c].fc && window.clearTimeout(this.pages[c].fc);
                }
            },
            Bk: function() {
                this.qd();
            },
            qd: function() {
                var c = this;
                null != c.Od && (window.clearTimeout(c.Od), c.Od = null);
                c.Od = setTimeout(function() {
                    c.ec();
                }, 200);
            },
            rj: function() {
                if (null != this.jScrollPane) {
                    try {
                        this.jScrollPane.data("jsp").reinitialise(this.Nc);
                    } catch (c) {}
                }
            },
            ec: function(c) {
                var d = this;
                if (d.aa) {
                    if (d.aa.ca && d.aa.ba == d.aa.ca.na) {
                        d.aa.ca.pb.ec(d, c);
                    } else {
                        null != d.Od && (window.clearTimeout(d.Od), d.Od = null);
                        c = d.ga(this.da).scrollTop();
                        for (var e = 0; e < this.document.numPages; e++) {
                            if (this.pages[e] && d.kb(e)) {
                                var g = !d.pages[e].ib;
                                this.pages[e].Mc(c, d.ga(this.da).height(), !0) ? (g && d.ia.trigger("onVisibilityChanged", e + 1), this.pages[e].ib = !0, this.pages[e].load(function() {
                                    if ("TwoPage" == d.aa.ba || "BookView" == d.aa.ba) {
                                        d.ga(d.da).is(":animated") || 1 == d.aa.scale || (d.ga(d.da).css("margin-left", d.kf()), d.ga("#" + this.container + "_2").css("left", d.ga("#" + d.container).width() / 2)), d.initialized || null == d.jScrollPane || (d.jScrollPane.data("jsp").reinitialise(d.Nc), d.initialized = !0);
                                    }
                                }), this.pages[e].Un(), this.pages[e].La()) : "TwoPage" != d.aa.ba && "BookView" != d.aa.ba && this.pages[e].unload();
                            }
                        }
                    }
                }
            },
            le: function() {
                this.aa.ba != this.aa.na() ? this.aa.bd(this.la + 1) : this.aa.bd(this.la);
            },
            La: function(c) {
                c = c ? c : this;
                for (var d = 0; d < c.document.numPages; d++) {
                    c.kb(d) && c.pages[d] && c.pages[d].ib && c.pages[d].La();
                }
            },
            sg: function() {
                for (var c = this.la, d = this.ga(this.da).scrollTop(), e = 0; e < this.document.numPages; e++) {
                    if (this.kb(e) && "SinglePage" != this.aa.ba) {
                        var g = !this.pages[e].ib;
                        if (this.pages[e].Mc(d, this.ga(this.da).height(), !1)) {
                            c = e;
                            g && this.ia.trigger("onVisibilityChanged", e + 1);
                            break;
                        }
                    }
                }
                this.la != c && this.ia.trigger("onCurrentPageChanged", c + 1);
                this.la = c;
            },
            setCurrentCursor: function(c) {
                for (var d = 0; d < this.document.numPages; d++) {
                    this.kb(d) && ("TextSelectorCursor" == c ? jQuery(this.pages[d].ma).addClass("flowpaper_nograb") : jQuery(this.pages[d].ma).removeClass("flowpaper_nograb"));
                }
            },
            gotoPage: function(c) {
                this.aa.gotoPage(c);
            },
            Zf: function(c, d) {
                c = parseInt(c);
                var e = this;
                e.aa.renderer.yc && e.aa.renderer.yc(e.pages[0]);
                jQuery(".flowpaper_pageword").remove();
                jQuery(".flowpaper_interactiveobject_" + e.ja).remove();
                e.pages[0].unload();
                e.pages[0].visible = !0;
                var g = e.ga(e.da).scrollTop();
                e.aa.bd(c);
                e.ia.trigger("onCurrentPageChanged", c);
                e.pages[0].Mc(g, e.ga(this.da).height(), !0) && (e.ia.trigger("onVisibilityChanged", c + 1), e.pages[0].load(function() {
                    null != d && d();
                    e.qd();
                    null != e.jScrollPane && e.jScrollPane.data("jsp").reinitialise(e.Nc);
                }));
            },
            ag: function(c, d) {
                c = parseInt(c);
                var e = this;
                0 == c % 2 && 0 < c && "BookView" == e.aa.ba && c != e.getTotalPages() && (c += 1);
                c == e.getTotalPages() && "TwoPage" == e.aa.ba && 0 == e.getTotalPages() % 2 && (c = e.getTotalPages() - 1);
                0 == c % 2 && "TwoPage" == e.aa.ba && --c;
                c > e.getTotalPages() && (c = e.getTotalPages());
                jQuery(".flowpaper_pageword").remove();
                jQuery(".flowpaper_interactiveobject_" + e.ja).remove();
                if (c <= e.getTotalPages() && 0 < c) {
                    e.aa.bd(c);
                    e.la != c && e.ia.trigger("onCurrentPageChanged", c);
                    e.pages[0].unload();
                    e.pages[0].load(function() {
                        if ("TwoPage" == e.aa.ba || "BookView" == e.aa.ba) {
                            e.ga(e.da).animate({
                                "margin-left": e.kf()
                            }, {
                                duration: 250
                            }), e.ga("#" + this.container + "_2").css("left", e.ga("#" + e.container).width() / 2), e.Xa(e.aa.scale);
                        }
                    });
                    1 < e.aa.ta ? (e.ga(e.pages[1].ma + "_2").removeClass("flowpaper_hidden"), e.ga(e.da + "_2").removeClass("flowpaper_hidden")) : "BookView" == e.aa.ba && 1 == e.aa.ta && (e.ga(e.pages[1].ma + "_2").addClass("flowpaper_hidden"), e.ga(e.da + "_2").addClass("flowpaper_hidden"));
                    0 != e.getTotalPages() % 2 && "TwoPage" == e.aa.ba && c >= e.getTotalPages() && e.ga(e.pages[1].ma + "_2").addClass("flowpaper_hidden");
                    0 == e.getTotalPages() % 2 && "BookView" == e.aa.ba && c >= e.getTotalPages() && e.ga(e.pages[1].ma + "_2").addClass("flowpaper_hidden");
                    var g = e.ga(this.da).scrollTop();
                    e.pages[1].unload();
                    e.pages[1].visible = !0;
                    !e.ga(e.pages[1].ma + "_2").hasClass("flowpaper_hidden") && e.pages[1].Mc(g, e.ga(this.da).height(), !0) && (e.ia.trigger("onVisibilityChanged", c + 1), e.pages[1].load(function() {
                        null != d && d();
                        e.ga(e.da).animate({
                            "margin-left": e.kf()
                        }, {
                            duration: 250
                        });
                        e.ga("#" + this.container + "_2").css("left", e.ga("#" + e.container).width() / 2);
                        e.qd();
                        null != e.jScrollPane && e.jScrollPane.data("jsp").reinitialise(e.Nc);
                    }));
                }
            },
            rotate: function(c) {
                this.pages[c].rotate();
            },
            kf: function(c) {
                this.ia.width();
                var d = 0;
                1 != this.aa.ta || c || "BookView" != this.aa.ba ? (c = jQuery(this.da + "_2").width(), 0 == c && (c = this.ga(this.da + "_1").width()), d = (this.ia.width() - (this.ga(this.da + "_1").width() + c)) / 2) : d = (this.ia.width() / 2 - this.ga(this.da + "_1").width() / 2) * (this.aa.scale + 0.7);
                10 > d && (d = 0);
                return d;
            },
            previous: function() {
                var c = this;
                if ("Portrait" == c.aa.ba) {
                    var d = c.ga(c.da).scrollTop() - c.pages[0].height - 14;
                    0 > d && (d = 1);
                    eb.browser.qb.Ab ? c.ga(c.da).scrollTo(d, {
                        axis: "y",
                        duration: 500
                    }) : c.jScrollPane.data("jsp").scrollToElement(this.pages[c.aa.ta - 2].ga(this.pages[c.aa.ta - 2].Ha), !0, !0);
                }
                "SinglePage" == c.aa.ba && 0 < c.aa.ta - 1 && (eb.platform.touchdevice && 1 != this.aa.scale ? (c.aa.Lc = !0, c.ga(c.da).removeClass("flowpaper_pages_border"), c.ga(c.da).transition({
                    x: 1000
                }, 350, function() {
                    c.pages[0].unload();
                    c.ga(c.da).transition({
                        x: -800
                    }, 0);
                    c.jScrollPane ? c.jScrollPane.data("jsp").scrollTo(0, 0, !1) : c.ga(c.da).scrollTo(0, {
                        axis: "y",
                        duration: 0
                    });
                    c.Zf(c.aa.ta - 1, function() {});
                    c.ga(c.da).transition({
                        x: 0
                    }, 350, function() {
                        c.aa.Lc = !1;
                        window.annotations || c.ga(c.da).addClass("flowpaper_pages_border");
                    });
                })) : c.Zf(c.aa.ta - 1));
                c.aa.ca && c.aa.ba == c.aa.ca.na && c.aa.ca.pb.previous(c);
                "TwoPage" != c.aa.ba && "BookView" != c.aa.ba || 1 > c.aa.ta - 2 || (eb.platform.touchdevice && 1 != this.aa.scale ? (c.la = c.aa.ta - 2, c.aa.Lc = !0, c.ga(c.da).animate({
                    "margin-left": 1000
                }, {
                    duration: 350,
                    complete: function() {
                        jQuery(".flowpaper_interactiveobject_" + c.ja).remove();
                        1 == c.aa.ta - 2 && "BookView" == c.aa.ba && c.pages[1].ga(c.pages[1].ma + "_2").addClass("flowpaper_hidden");
                        setTimeout(function() {
                            c.ga(c.da).css("margin-left", -800);
                            c.pages[0].unload();
                            c.pages[1].unload();
                            c.ga(c.da).animate({
                                "margin-left": c.kf()
                            }, {
                                duration: 350,
                                complete: function() {
                                    setTimeout(function() {
                                        c.aa.Lc = !1;
                                        c.ag(c.aa.ta - 2);
                                    }, 500);
                                }
                            });
                        }, 500);
                    }
                })) : c.ag(c.aa.ta - 2));
            },
            next: function() {
                var c = this;
                if ("Portrait" == c.aa.ba) {
                    0 == c.aa.ta && (c.aa.ta = 1);
                    var d = c.aa.ta - 1;
                    100 < this.pages[c.aa.ta - 1].ga(this.pages[c.aa.ta - 1].Ha).offset().top - c.ia.offset().top ? d = c.aa.ta - 1 : d = c.aa.ta;
                    eb.browser.qb.Ab ? this.pages[d] && c.ga(c.da).scrollTo(this.pages[d].ga(this.pages[d].Ha), {
                        axis: "y",
                        duration: 500
                    }) : c.jScrollPane.data("jsp").scrollToElement(this.pages[c.aa.ta].ga(this.pages[c.aa.ta].Ha), !0, !0);
                }
                "SinglePage" == c.aa.ba && c.aa.ta < c.getTotalPages() && (eb.platform.touchdevice && 1 != c.aa.scale ? (c.aa.Lc = !0, c.ga(c.da).removeClass("flowpaper_pages_border"), c.ga(c.da).transition({
                    x: -1000
                }, 350, "ease", function() {
                    c.pages[0].unload();
                    c.ga(c.da).transition({
                        x: 1200
                    }, 0);
                    c.jScrollPane ? c.jScrollPane.data("jsp").scrollTo(0, 0, !1) : c.ga(c.da).scrollTo(0, {
                        axis: "y",
                        duration: 0
                    });
                    c.Zf(c.aa.ta + 1, function() {});
                    c.ga(c.da).transition({
                        x: 0
                    }, 350, "ease", function() {
                        window.annotations || c.ga(c.da).addClass("flowpaper_pages_border");
                        c.aa.Lc = !1;
                    });
                })) : c.Zf(c.aa.ta + 1));
                c.aa.ca && c.aa.ba == c.aa.ca.na && c.aa.ca.pb.next(c);
                if ("TwoPage" == c.aa.ba || "BookView" == c.aa.ba) {
                    if ("TwoPage" == c.aa.ba && c.aa.ta + 2 > c.getTotalPages()) {
                        return !1;
                    }
                    eb.platform.touchdevice && 1 != this.aa.scale ? (c.la = c.aa.ta + 2, c.aa.Lc = !0, c.ga(c.da).animate({
                        "margin-left": -1000
                    }, {
                        duration: 350,
                        complete: function() {
                            jQuery(".flowpaper_interactiveobject_" + c.ja).remove();
                            c.aa.ta + 2 <= c.getTotalPages() && 0 < c.aa.ta + 2 && c.pages[1].ga(c.pages[1].ma + "_2").removeClass("flowpaper_hidden");
                            setTimeout(function() {
                                c.ga(c.da).css("margin-left", 800);
                                c.pages[0].unload();
                                c.pages[1].unload();
                                c.pages[0].ib = !0;
                                c.pages[1].ib = !0;
                                c.ia.trigger("onVisibilityChanged", c.la);
                                c.ga(c.da).animate({
                                    "margin-left": c.kf(!0)
                                }, {
                                    duration: 350,
                                    complete: function() {
                                        setTimeout(function() {
                                            c.aa.Lc = !1;
                                            c.ag(c.aa.ta + 2);
                                        }, 500);
                                    }
                                });
                            }, 500);
                        }
                    })) : c.ag(c.aa.ta + 2);
                }
            },
            Ne: function(c) {
                this.aa.ca && this.aa.ba == this.aa.ca.na && this.aa.ca.pb.Ne(this, c);
            }
        };
        return f;
    }(),
    U = function() {
        function f(c, d, e, g, f, m) {
            this.ia = g;
            this.aa = f;
            this.pages = e;
            this.ab = 1000;
            this.Ga = this.ib = !1;
            this.ja = c;
            this.pageNumber = d;
            this.dimensions = m;
            this.selectors = {};
            this.gd = "data:image/gif;base64,R0lGODlhHgAKAMIAALSytPTy9MzKzLS2tPz+/AAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQJBgAEACwAAAAAHgAKAAADTki63P4riDFEaJJaPOsNFCAOlwIOIkBG4SilqbBMMCArNJzDw4LWPcWPN0wFCcWRr6YSMG8EZw0q1YF4JcLVmN26tJ0NI+PhaLKQtJqQAAAh+QQJBgADACwAAAAAHgAKAIKUlpTs7uy0srT8/vzMysycmpz08vS0trQDWTi63P7LnFKOaYacQy7LWzcEBWACRRBtQmutRytYx3kKiya3RB7vhJINtfjtDsWda3hKKpEKo2zDxCkISkHvmiWQhiqF5BgejKeqgMAkKIs1HE8ELoLY74sEACH5BAkGAAUALAAAAAAeAAoAg3R2dMzKzKSipOzq7LSytPz+/Hx+fPTy9LS2tAAAAAAAAAAAAAAAAAAAAAAAAAAAAARfsMhJq71zCGPEqEeAIMEBiqQ5cADAfdIxEjRixnN9CG0PCBMRbRgIIoa0gMHlM0yOSALiGZUuW0sONTqVQJEIHrYFlASqRTN6dXXBCjLwDf6VqjaddwxVOo36GIGCExEAIfkECQYABQAsAAAAAB4ACgCDXFpctLK05ObkjI6MzMrM/P78ZGJktLa09PL0AAAAAAAAAAAAAAAAAAAAAAAAAAAABFmwyEmrvVMMY4aoCHEcBAKKpCkYQAsYn4SMQX2YMm0jg+sOE1FtSAgehjUCy9eaHJGBgxMaZbqmUKnkiTz0mEAJgVoUk1fMWGHWxa25UdXXcxqV6imMfk+JAAAh+QQJBgAJACwAAAAAHgAKAIM8Ojy0srTk4uR8enxEQkTMysz08vS0trRERkT8/vwAAAAAAAAAAAAAAAAAAAAAAAAEXDDJSau9UwyEhqhGcRyFAYqkKSBACyCfZIxBfZgybRuD6w4TUW1YCB6GtQLB10JMjsjA4RmVsphOCRQ51VYPPSZQUqgWyeaVDzaZcXEJ9/CW0HA8p1Epn8L4/xQRACH5BAkGAAkALAAAAAAeAAoAgxweHLSytNza3GRmZPTy9CwqLMzKzLS2tNze3Pz+/CwuLAAAAAAAAAAAAAAAAAAAAARgMMlJq70TjVIGqoRxHAYBiqSJFEALKJ9EjEF9mDJtE4PrDhNRbWgIHoY1A8sHKEyOyMDhGZUufU4JFDnVVg89JlBiqBbJZsG1KZjMuLjEe3hLaDiDNiU0Kp36cRiCgwkRACH5BAkGAAwALAAAAAAeAAoAgwQCBLSytNza3ExOTAwODMzKzPTy9AwKDLS2tFRSVBQSFNTW1Pz+/AAAAAAAAAAAAARikMlJq71TJKKSqEaBIIUBiqQpEEALEJ9kjEGNmDJtG4PrDhNRbVgIIoa1wsHXOkyOyADiGZUumU4JFDnVVhE9JlBSqBbJ5gXLRVhMZlwcAz68MQSDw2EQe6NKJyOAGISFExEAIfkECQYACAAsAAAAAB4ACgCDHB4clJaU3NrctLK07O7sZGZkLCoszMrM/P78nJqc3N7ctLa09PL0LC4sAAAAAAAABGwQyUmrvVMVY4qqzJIkCwMey3KYigG8QPNJTBLcQUJM4TL8pQIMVpgscLjBBPVrHlxDgGFiQ+aMzeYCOpxKqlZsdrAQRouSgTWglBzGg4OAKxXwwLcdzafdaTgFdhQEamwEJjwoKogYF4yNCBEAIfkECQYACwAsAAAAAB4ACgCDPDo8pKKk5OLkdHZ0zMrM9PL0REJEtLK0fH587OrsfHp8/P78REZEtLa0AAAAAAAABHRwyUmrvVMoxpSoSYAgQVIVRNMQxSIwQAwwn5QgijIoiCkVqoOwUVDIZIpJQLfbBSYpoZRgOMYYE0SzmZQ0pNIGzIqV4La5yRd8aAysgIFywB08JQT2gfA60iY3TAM9E0BgRC4IHAg1gEsKJScpKy0YlpcTEQAh+QQJBgAFACwAAAAAHgAKAINcWly0srTk5uSMjozMysz8/vxkYmS0trT08vQAAAAAAAAAAAAAAAAAAAAAAAAAAAAEW7DISau9Uwxjhqga51UIcRwEUggG4ALGJ7EvLBfIGewHMtSuweQHFEpMuyShBQRMmMDJIZk8NF3Pq5TKI9aMBe8LTOAGCLTaTdC85ai9FXFE0QRvktIphen7KREAIfkECQYACwAsAAAAAB4ACgCDPDo8pKKk5OLkdHZ0zMrM9PL0REJEtLK0fH587OrsfHp8/P78REZEtLa0AAAAAAAABHVwyUmrvTMFhEKqgsIwilAVRNMQxZIgijIoyCcJDKADjCkVqoOwUQgMjjJFYKLY7RSTlHBKgM2OA8TE4NQxJo3ptIG4JqGSXPcrCYsPDaN5sJQ0u4Po+0B4yY41EzhOPRNAYkQuATEeIAMjCD6GKSstGJeYExEAIfkECQYACAAsAAAAAB4ACgCDHB4clJaU3NrctLK07O7sZGZkLCoszMrM/P78nJqc3N7ctLa09PL0LC4sAAAAAAAABGsQyUmrvZOtlBarSmEYhVIxx7IcH5EEcJAQk9IAONCYkrYMQM8iFhtMCrlcYZICOg8vomxiSOIMk58zKI1RrQCsRLtVdY0SpHUpOWyBB5eUJhFUcwZBhjxY0AgDMAN0NSIkPBkpKx8YjY4TEQAh+QQJBgAMACwAAAAAHgAKAIMEAgS0srTc2txMTkwMDgzMysz08vQMCgy0trRUUlQUEhTU1tT8/vwAAAAAAAAAAAAEYpDJSau90xSEiqlCQiiJUGmcxxhc4CKfJBBADRCmxCJuABe9XmGSsNkGk00woFwiJgdj7TDhOa3BpyQqpUqwvc6SORlIAUgJcOkBwyYzI2GRcX9QnRh8cDgMchkbeRiEhRQRACH5BAkGAAgALAAAAAAeAAoAgxweHJSWlNza3LSytOzu7GRmZCwqLMzKzPz+/JyanNze3LS2tPTy9CwuLAAAAAAAAARsEMlJq72TnbUOq0phGIVSMUuSLB+6DDA7KQ1gA40pMUngBwnCAUYcHCaF260wWfx+g1cxOjEobYZJ7wmUFhfVKyAr2XKH06MkeWVKBtzAAPUlTATWm0GQMfvsGhweICIkOhMEcHIEHxiOjo0RACH5BAkGAAsALAAAAAAeAAoAgzw6PKSipOTi5HR2dMzKzPTy9ERCRLSytHx+fOzq7Hx6fPz+/ERGRLS2tAAAAAAAAARxcMlJq72zkNZIqYLCMIpQJQGCBMlScEfcfJLAADjAmFKCKIqBApEgxI4HwkSRyykmgaBQGGggZRNDE8eYIKZThfXamNy2XckPDDRelRLmdgAdhAeBF3I2sTV3Ez5SA0QuGx00fQMjCDyBUQosGJOUFBEAIfkECQYABQAsAAAAAB4ACgCDXFpctLK05ObkjI6MzMrM/P78ZGJktLa09PL0AAAAAAAAAAAAAAAAAAAAAAAAAAAABFiwyEmrvRORcwiqwmAYgwCKpIlwQXt8kmAANGCY8VzfROsHhMmgVhsIibTB4eea6JBOJG3JPESlV2SPGZQMkUavdLD6vSYCKa6QRqo2HRj6Wzol15i8vhABACH5BAkGAAsALAAAAAAeAAoAgzw6PKSipOTi5HR2dMzKzPTy9ERCRLSytHx+fOzq7Hx6fPz+/ERGRLS2tAAAAAAAAARycMlJq72zkNZIqUmAIEFSCQrDKMJScEfcfFKCKMqgIKYkMIAggCEgxI4HwiSQ0+kCE4VQOGggZROE06mYGKZBhvXayOaauAkQzDBelZLAgDuASqTgwQs5m9iaAzwTP1NELhsdNH5MCiUnAyoILRiUlRMRACH5BAkGAAgALAAAAAAeAAoAgxweHJSWlNza3LSytOzu7GRmZCwqLMzKzPz+/JyanNze3LS2tPTy9CwuLAAAAAAAAARvEMlJq72TnbUOq8ySJMtHKYVhFAoSLkNcZklgBwkxKQ3gAw3FIUYcHCaL220wKfx+BVhxsJjUlLiJ4ekzSItVyRWr5QIMw+lRMsAGmBIntxAC6ySMse2OEGx/BgIuGx0mEwRtbwSGCCgqLBiRjJERACH5BAkGAAwALAAAAAAeAAoAgwQCBLSytNza3ExOTAwODMzKzPTy9AwKDLS2tFRSVBQSFNTW1Pz+/AAAAAAAAAAAAARmkMlJq73TFISKqRrnVUJCKInAGFzgIp/EIm4ATwIB7AAhFLVaYbIJBoaSBI83oBkRE2cQKjksdwdpjcrQvibW6wFoRDLIQfPgChiwprGV9ibJLQmL1aYTl+1HFAIDBwcDKhiIiRMRACH5BAkGAAkALAAAAAAeAAoAgxweHLSytNza3GRmZPTy9CwqLMzKzLS2tNze3Pz+/CwuLAAAAAAAAAAAAAAAAAAAAARiMMlJq72TmHMMqRrnVchQFAOSEFzgHp/EHm4AT4gC7ICCGLWaYbIJBoaSAY83oBkPE2cQKiksdwVpjZrQvibWawFoRCbIQbPyOmBNYyvtTSIIYwWrTQcu048oJScpGISFFBEAIfkECQYACQAsAAAAAB4ACgCDPDo8tLK05OLkfHp8REJEzMrM9PL0tLa0REZE/P78AAAAAAAAAAAAAAAAAAAAAAAABGEwyUmrvdOUc4qpGudVwoAgg5AYXOAen8QebgBPAgLsACIUtVphsgkGhpIBjzegGQ8TZxAqISx3CGmNmtC+JrorAmhEJshBs/I6YE1jK+1Nklv6VpsOXJYfUUonKRiDhBQRACH5BAkGAAUALAAAAAAeAAoAg1xaXLSytOTm5IyOjMzKzPz+/GRiZLS2tPTy9AAAAAAAAAAAAAAAAAAAAAAAAAAAAAResMhJq70TkXMIqhrnVcJgGINQIFzgHp/EHm4AT4IB7IAhELUaYbIJBoaSAY83oBkPE2cQKtEtd9IatZB9TaxXoBFZEAfJyuuANY2tsjeJ4ApQhTpu2QZPSqcwgIEUEQAh+QQJBgAFACwAAAAAHgAKAIN0dnTMysykoqTs6uy0srT8/vx8fnz08vS0trQAAAAAAAAAAAAAAAAAAAAAAAAAAAAEY7DISau98wSEwqka51WDYBjCUBwc4SKfxCIuAU/DCQDnENS1wGQDJAglgp0SIKAVERMnECox8HZWg7RGLWxfE+sV+yseC2XgOYndCVjT2Gp7k+TEPFWoI5dt+CQmKCoYhYYTEQAh+QQJBgADACwAAAAAHgAKAIKUlpTs7uy0srT8/vzMysycmpz08vS0trQDWTi63P7LkHOIaZJafEo5l0EJJBiN5aUYBeACRUCQtEAsU20vx/sKBx2QJzwsWj5YUGdULGvNATI5090U1dp1IEgCBCJo4CSOTF3jTEUVmawbge43wIbYH6oEADs%3D";
            this.Om = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAdCAYAAABWk2cPAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAVVSURBVHjaxFdbSFxHGJ7djfdb1HgNpsV7iwQrYhWN5EmReHlqUEGqUcGHohBCMSqhqEgU8aWiqH0QBDGkAe2bF1ARMduKldqqsURFrVqtBo1uvOzu9P+n/znMWVfNWwc+zp455/zf/LdvZnXs8qGTrrbAwe2ASddrDdvOIfSEGwADQW9DagVYCGa6t9os4kpS5bdCgGSOCpqamj5PSUm5d+fOnS98fHyiHB0dg3U6HT8/P//r6Ojoj729PePy8vJIRkbGnLQQdh25johcADcBQYDQ4uLitNevX3eB4Q2r1coVbG1t8ZWVFS7PnZ6ewtTK856eniiypbskmuoDB4ArwBfwCSCmvr7+GzBiJIO8s7OTP3jwgLu6umqQnJzMW1pauMlkEuTg9eDo6Gg62bRLrHiIhLfQO0B8VVXVk83NzUU0Mjg4yKOioi6Q2eLu3bt8enpaEJ+cnBiHh4fTJY81QwmpLxEmpKWlPVpYWJjFj7u7u7mHh8e1hC4uLgLu7u68oaFBEIPng11dXdH2iJ0ohxjSeEDmy5cvf1I8vIpQIbKHtrY2Qfz27dvnxKGXSd2oaGIAaVB9Nbu7u3tQODw8PFxDkpiYyO/fv3+BICQkhJeWlnJfX191zsvLi6+vr4vigsKKt/XWm8KaDMiFghjAFba2tmoI4+Li1Cqtra1VjUdHR/ONjQ0x39HRoc47OzvzsrIyMT8zM1NJrSdI9XSDReSJC4iNjY3ABy9evNAk/vj4mEFxiN81NTXs6dOnLDQ0lI2MjLDg4GAx//79e8Y5F8AxMDDAgJRBxL609TQEiwfwFeBbWPXewcGB3fzl5OSobYHA95Tfr1694m5ubsJDGbOzs1jJS2Dbg0RHeOpAiUZvXSEvntvb2xovlZUPDQ2x3NxcdnZ2Ju6hyMS1v7+fFRUV/SdnBoMGkFfm4OBwmwjV8Cpy50RgIG0XCJUBYiHCKI/5+XlmsVjsSh3Ogw2drNt6W2Hf2dk5DgwMtGsAciO8hWiIe8wXDhASVllZafcbzDdEZlNWJr3tS4uLi+9A0MXLspcYSiQMCAhQQ/rw4UO1uKqrq1lJSYnGFoY3MjKSQfu9kef10naEW5NlfHx8Bx9kZWVpDODHMmFhYSED8WD5+fkqMWiw5pvU1FTm6enJlpaWfrXd7rBH7wG+BnwXExPzI1TwEe4icrMjsO8qKio4GBKVqgC2PF5XV8cjIiI08xMTExx3J2ivdFK9G3ZbBvB9Y2Pj79gGzc3NGlJsAdnoVYBQi1YyGo1dxKG2jIHE3pGu2DYukFcrSJ4P5Mx9dXWVzc3NqfnV6/XXnUZYQkIC6+vrY7BL/fzs2bNW2DywkE4ohdxAhPIpwenw8BALCj++CSt2MZvNbHJy8qNIsbh6e3vZ/v7+m/b29h9AGo0oaIBT6TShFXzAI1Q6DHNSUtIwkG1hmGC1PC8vj/v5+dkNZ2ZmJocThggpFM7s48ePn5DNIOJQZVBHgoCh9QL4AQLpRSzVW0FBQbfLy8s/Kygo+BTayA12DaxGBiIuVgyFx6CARJXCiWF/bGxsEmqhH3L5GzzeBRwAPqDmUJeopwblqOJFpwd/wi3ahdzh5BCUnZ0dAluff1hYmLe/vz+uHokO19bW/p6amvoTWukXqNhZmMa2+4cITURoUVpGUQmDzW7jI8GbKs+VomJQFI7yhEZRF98B9iUc0rMzmZBJfWOh1ZjooYWq7ZhW6y6RKt+YJdIjIjmgBRxJIbXYOx9x8tYsqYaFVmgiQwqhoySdVnpHITYR0QeaO7/s7PvRh23K+w0bUjMZP5Ngvu6w/b/8rfhXgAEAmJkyLSnsNQEAAAAASUVORK5CYII=";
            this.pa = "dummyPage_" + this.pageNumber + "_" + this.ja;
            this.page = "page_" + this.pageNumber + "_" + this.ja;
            this.Rc = "pageContainer_" + this.pageNumber + "_" + this.ja;
            this.io = this.Rc + "_textLayer";
            this.Ig = "dummyPageCanvas_" + this.pageNumber + "_" + this.ja;
            this.Jg = "dummyPageCanvas2_" + this.pageNumber + "_" + this.ja;
            this.Uh = this.page + "_canvasOverlay";
            this.qc = "pageLoader_" + this.pageNumber + "_" + this.ja;
            this.Tk = this.Rc + "_textoverlay";
            this.ba = this.aa.ba;
            this.na = this.aa.ca ? this.aa.ca.na : "";
            this.renderer = this.aa.renderer;
            c = this.aa.scale;
            this.scale = c;
            this.ma = "#" + this.pa;
            this.Ka = "#" + this.page;
            this.Ha = "#" + this.Rc;
            this.Rb = "#" + this.io;
            this.ci = "#" + this.Ig;
            this.di = "#" + this.Jg;
            this.cc = "#" + this.qc;
            this.Ui = "#" + this.Tk;
            this.Ba = {
                bottom: 3,
                top: 2,
                right: 0,
                left: 1,
                jb: 4,
                back: 5
            };
            this.$a = [];
            this.duration = 1.3;
            this.ho = 16777215;
            this.offset = this.force = 0;
        }
        f.prototype = {
            ga: function(c) {
                if (0 < c.indexOf("undefined")) {
                    return jQuery(null);
                }
                this.selectors || (this.selectors = {});
                this.selectors[c] || (this.selectors[c] = jQuery(c));
                return this.selectors[c];
            },
            show: function() {
                "TwoPage" != this.aa.ba && "BookView" != this.aa.ba && this.ga(this.Ka).removeClass("flowpaper_hidden");
            },
            Ce: function() {
                this.pages.jScrollPane && (!eb.browser.qb.Ab && this.pages.jScrollPane ? "SinglePage" == this.aa.ba ? 0 > this.ga(this.pages.da).width() - this.ga(this.Ha).width() ? (this.pages.jScrollPane.data("jsp").scrollToPercentX(0.5, !1), this.pages.jScrollPane.data("jsp").scrollToPercentY(0.5, !1)) : (this.pages.jScrollPane.data("jsp").scrollToPercentX(0, !1), this.pages.jScrollPane.data("jsp").scrollToPercentY(0, !1)) : this.pages.jScrollPane.data("jsp").scrollToPercentX(0, !1) : this.ga(this.Ha).parent().scrollTo && this.ga(this.Ha).parent().scrollTo({
                    left: "50%"
                }, 0, {
                    axis: "x"
                }));
            },
            create: function(c) {
                var d = this;
                if ("Portrait" == d.aa.ba && (c.append("<div class='flowpaper_page " + (d.aa.document.DisableOverflow ? "flowpaper_ppage" : "") + " " + (d.aa.document.DisableOverflow && d.pageNumber < d.aa.renderer.getNumPages() - 1 ? "ppage_break" : "ppage_none") + "' id='" + d.Rc + "' style='position:relative;" + (d.aa.document.DisableOverflow ? "max-height:100%;margin:0;padding:0;overflow:hidden;" : "") + "'><div id='" + d.pa + "' class='' style='z-index:11;" + d.getDimensions() + ";'></div></div>"), 0 < jQuery(d.aa.yj).length)) {
                    var e = this.ab * this.scale;
                    jQuery(d.aa.yj).append("<div id='" + d.Tk + "' class='flowpaper_page' style='position:relative;height:" + e + "px;width:100%;overflow:hidden;'></div>");
                }
                "SinglePage" == d.aa.ba && 0 == d.pageNumber && c.append("<div class='flowpaper_page' id='" + d.Rc + "' class='flowpaper_rescale' style='position:relative;'><div id='" + d.pa + "' class='' style='position:absolute;z-index:11;" + d.getDimensions() + "'></div></div>");
                if ("TwoPage" == d.aa.ba || "BookView" == d.aa.ba) {
                    0 == d.pageNumber && jQuery(c.children().get(0)).append("<div class='flowpaper_page' id='" + d.Rc + "_1' style='z-index:2;float:right;position:relative;'><div id='" + d.pa + "_1' class='flowpaper_hidden flowpaper_border' style='" + d.getDimensions() + ";float:right;'></div></div>"), 1 == d.pageNumber && jQuery(c.children().get(1)).append("<div class='flowpaper_page' id='" + d.Rc + "_2' style='position:relative;z-index:1;float:left;'><div id='" + d.pa + "_2' class='flowpaper_hidden flowpaper_border' style='" + d.getDimensions() + ";float:left'></div></div>");
                }
                "ThumbView" == d.aa.ba && (c.append("<div class='flowpaper_page' id='" + d.Rc + "' style='position:relative;" + (eb.browser.msie ? "clear:none;float:left;" : "display:inline-block;") + "'><div id=\"" + d.pa + '" class="flowpaper_page flowpaper_thumb flowpaper_border flowpaper_load_on_demand" style="margin-left:10px;' + d.getDimensions() + '"></div></div>'), jQuery(d.Ha).on("mousedown touchstart", function() {
                    d.aa.gotoPage(d.pageNumber + 1);
                }));
                d.aa.ba == d.na ? d.aa.ca.tc.create(d, c) : (d.aa.renderer.Hd(d), d.show(), d.height = d.ga(d.Ha).height(), d.wl());
            },
            Ln: function() {
                var c = this;
                if (c.aa.Fi && !eb.platform.mobilepreview) {
                    jQuery(c.Ha).on("mouseover, mousemove", function(d) {
                        if (!c.aa.gh || c.aa.gh.button != d.target) {
                            for (var e = jQuery(".popover"), g = d.target.getBoundingClientRect().right + 200 < window.innerWidth ? "right" : "left", f = 0; f < e.length; f++) {
                                e[f].remove();
                            }
                            c.aa.gh = c.aa.ca && c.aa.ca.Ta ? new Popover({
                                position: g,
                                button: d.target,
                                className: "left" == g ? "popover-pushright" : "popover-pushleft"
                            }) : new Popover({
                                position: g,
                                button: d.target
                            });
                            c.aa.gh.setContent(String.format('<div class="flowpaper-popover-content" style="height:40px"><span class="flowpaper-publisher-popover-label">Page {0}</span><div id="flowpaper-publisher-edit-section" class="flowpaper-publisher-edit-button" style="bottom:10px;width:107px;" onmousedown="window.parent.postMessage(\'EditPage:{0}\',\'*\');event.preventDefault();event.stopImmediatePropagation();return false;" onclick="event.preventDefault();event.stopImmediatePropagation();return false;" onmouseup="event.preventDefault();event.stopImmediatePropagation();return false;">Edit Page</div></div>', c.pageNumber + 1, ""));
                            c.aa.gh.render("open");
                        }
                    });
                }
            },
            tn: function() {
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    return this.Uh;
                }
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    if (0 == this.pageNumber) {
                        return this.Uh + "_1";
                    }
                    if (1 == this.pageNumber) {
                        return this.Uh + "_2";
                    }
                }
            },
            cj: function(c) {
                this.ga(this.Ui).css({
                    top: c
                });
            },
            mc: function() {
                "Portrait" != this.aa.ba && "SinglePage" != this.aa.ba && this.aa.ba != this.na || this.ga("#" + this.qc).hide();
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    0 == this.pageNumber && this.ga(this.cc + "_1").hide(), 1 == this.pageNumber && this.ga(this.cc + "_2").hide();
                }
            },
            sd: function() {
                var c = this;
                if ("Portrait" == c.aa.ba || "SinglePage" == c.aa.ba || c.aa.ba == c.na) {
                    c.ab = 1000;
                    if (0 < c.ga(c.cc).length) {
                        return;
                    }
                    if (null === c.we && c.aa.ba == c.na) {
                        c.we = jQuery("<div class='flowpaper_pageLoader' style='position:absolute;left:50%;top:50%;'></div>"), c.ga(c.Ha).append(c.we), c.we.spin({
                            color: "#777"
                        }), c.Vg = setTimeout(function() {
                            c.we.remove();
                        }, 1000);
                    } else {
                        var d = 0 < jQuery(c.Ha).length ? jQuery(c.Ha) : c.Sc;
                        d && d.find && 0 != d.length ? 0 == d.find("#" + c.qc).length && d.append("<img id='" + c.qc + "' src='" + c.gd + "' class='flowpaper_pageLoader'  style='position:absolute;left:50%;top:50%;height:8px;margin-left:" + (c.Kc() - 10) + "px;' />") : K("can't show loader, missing container for page " + c.pageNumber);
                    }
                }
                if ("TwoPage" == c.aa.ba || "BookView" == c.aa.ba) {
                    if (0 == c.pageNumber) {
                        if (0 < c.ga(c.cc + "_1").length) {
                            c.ga(c.cc + "_1").show();
                            return;
                        }
                        c.ga(c.ma + "_1").append("<img id='" + c.qc + "_1' src='" + c.gd + "' style='position:absolute;left:" + (c.Va() - 30) + "px;top:" + c.Za() / 2 + "px;' />");
                        c.ga(c.cc + "_1").show();
                    }
                    1 == c.pageNumber && (0 < c.ga(c.cc + "_2").length || c.ga(c.ma + "_2").append("<img id='" + c.qc + "_2' src='" + c.gd + "' style='position:absolute;left:" + (c.Va() / 2 - 10) + "px;top:" + c.Za() / 2 + "px;' />"), c.ga(c.cc + "_2").show());
                }
            },
            Xa: function() {
                var c, d;
                d = this.Va();
                c = this.Za();
                var e = this.Kc();
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    this.ga(this.Ha).css({
                        height: c,
                        width: d,
                        "margin-left": e,
                        "margin-top": 0
                    }), this.ga(this.ma).css({
                        height: c,
                        width: d,
                        "margin-left": e
                    }), this.ga(this.Ka).css({
                        height: c,
                        width: d,
                        "margin-left": e
                    }), this.ga(this.ci).css({
                        height: c,
                        width: d
                    }), this.ga(this.di).css({
                        height: c,
                        width: d
                    }), this.ga(this.Ui).css({
                        height: c,
                        width: d
                    }), this.ga(this.cc).css({
                        "margin-left": e
                    }), jQuery(this.Rb).css({
                        height: c,
                        width: d,
                        "margin-left": e
                    }), this.aa.renderer.sb && (jQuery(".flowpaper_flipview_canvas_highres").css({
                        width: 0.25 * d,
                        height: 0.25 * c
                    }).show(), this.scale < this.Xf() ? this.aa.renderer.yc(this) : this.aa.renderer.Vc(this)), this.uf(this.scale, e);
                }
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    this.ga(this.ma + "_1").css({
                        height: c,
                        width: d
                    }), this.ga(this.ma + "_2").css({
                        height: c,
                        width: d
                    }), this.ga(this.ma + "_1_textoverlay").css({
                        height: c,
                        width: d
                    }), this.ga(this.ma + "_2_textoverlay").css({
                        height: c,
                        width: d
                    }), this.ga(this.Ka).css({
                        height: c,
                        width: d
                    }), eb.browser.qb.Ab || (0 == this.pages.la ? this.pages.ga(this.pages.da).css({
                        height: c,
                        width: d
                    }) : this.pages.ga(this.pages.da).css({
                        height: c,
                        width: 2 * d
                    }), "TwoPage" == this.aa.ba && this.pages.ga(this.pages.da).css({
                        width: "100%"
                    })), eb.platform.touchdevice && 1 <= this.scale && this.pages.ga(this.pages.da).css({
                        width: 2 * d
                    }), eb.platform.touchdevice && ("TwoPage" == this.aa.ba && this.pages.ga(this.pages.da + "_2").css("left", this.pages.ga(this.pages.da + "_1").width() + e + 2), "BookView" == this.aa.ba && this.pages.ga(this.pages.da + "_2").css("left", this.pages.ga(this.pages.da + "_1").width() + e + 2));
                }
                if (this.aa.ba == this.na) {
                    var g = this.Kg() * this.ab,
                        f = this.Va() / g;
                    null != this.dimensions.tb && this.vb && this.aa.renderer.Ia && (f = this.pages.$c / 2 / g);
                    this.aa.ba == this.na ? 1 == this.scale && this.uf(f, e) : this.uf(f, e);
                }
                this.height = c;
                this.width = d;
            },
            Xf: function() {
                return 1;
            },
            hc: function() {
                return "SinglePage" == this.aa.ba;
            },
            resize: function() {},
            Kg: function() {
                return this.dimensions.Ca / this.dimensions.Na;
            },
            de: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.de(this) : this.dimensions.Ca / this.dimensions.Na * this.scale * this.ab;
            },
            hf: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.hf(this) : this.ab * this.scale;
            },
            getDimensions: function() {
                var c = this.jf(),
                    d = this.aa.de();
                if (this.aa.document.DisableOverflow) {
                    var e = this.ab * this.scale;
                    return "height:" + e + "px;width:" + e * c + "px";
                }
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    return e = this.ab * this.scale, "height:" + e + "px;width:" + e * c + "px;margin-left:" + (d - e * c) / 2 + "px;";
                }
                if (this.aa.ba == this.na) {
                    return this.aa.ca.tc.getDimensions(this, c);
                }
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    return e = this.ia.width() / 2 * this.scale, (0 == this.pageNumber ? "margin-left:0px;" : "") + "height:" + e + "px;width:" + e * c + "px";
                }
                if ("ThumbView" == this.aa.ba) {
                    return e = this.ab * ((this.ia.height() - 100) / this.ab) / 2.7, "height:" + e + "px;width:" + e * c + "px";
                }
            },
            jf: function() {
                return this.dimensions.Ca / this.dimensions.Na;
            },
            Va: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.Va(this) : this.ab * this.jf() * this.scale;
            },
            ui: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.ui(this) : this.ab * this.jf() * this.scale;
            },
            qk: function(c) {
                return c / (this.ab * this.jf());
            },
            wi: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.wi(this) : this.ab * this.jf();
            },
            Za: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.Za(this) : this.ab * this.scale;
            },
            ti: function() {
                return this.aa.ba == this.na ? this.aa.ca.tc.ti(this) : this.ab * this.scale;
            },
            Kc: function() {
                var c = this.aa.de(),
                    d = 0;
                if (this.aa.document.DisableOverflow) {
                    return 0;
                }
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    return d = (c - this.Va()) / 2 / 2 - 4, 0 < d ? d : 0;
                }
                if ("TwoPage" == this.aa.ba || "BookView" == this.aa.ba) {
                    return 0;
                }
                if (this.aa.ba == this.na) {
                    return this.aa.ca.tc.Kc(this);
                }
            },
            Mc: function(c, d, e) {
                var g = !1;
                if ("Portrait" == this.aa.ba || "ThumbView" == this.aa.ba) {
                    if (this.offset = this.ga(this.Ha).offset()) {
                        this.pages.xj || (this.pages.xj = this.aa.ka.offset().top);
                        var g = this.offset.top - this.pages.xj + c,
                            f = this.offset.top + this.height;
                        d = c + d;
                        g = e || eb.platform.touchdevice && !eb.browser.qb.Ab ? this.ib = c - this.height <= g && d >= g || g - this.height <= c && f >= d : c <= g && d >= g || g <= c && f >= d;
                    } else {
                        g = !1;
                    }
                }
                "SinglePage" == this.aa.ba && (g = this.ib = 0 == this.pageNumber);
                this.aa.ba == this.na && (g = this.ib = this.aa.ca.tc.Mc(this));
                if ("BookView" == this.aa.ba) {
                    if (0 == this.pages.getTotalPages() % 2 && this.pages.la >= this.pages.getTotalPages() && 1 == this.pageNumber) {
                        return !1;
                    }
                    g = this.ib = 0 == this.pageNumber || 0 != this.pages.la && 1 == this.pageNumber;
                }
                if ("TwoPage" == this.aa.ba) {
                    if (0 != this.pages.getTotalPages() % 2 && this.pages.la >= this.pages.getTotalPages() && 1 == this.pageNumber) {
                        return !1;
                    }
                    g = this.ib = 0 == this.pageNumber || 1 == this.pageNumber;
                }
                return g;
            },
            Un: function() {
                this.Ga || this.load();
            },
            load: function(c) {
                this.La(c);
                if (!this.Ga) {
                    "TwoPage" == this.aa.ba && (c = this.aa.renderer.getDimensions(this.pageNumber - 1, this.pageNumber - 1)[this.pages.la + this.pageNumber], c.width != this.dimensions.width || c.height != this.dimensions.height) && (this.dimensions = c, this.Xa());
                    "BookView" == this.aa.ba && (c = this.aa.renderer.getDimensions(this.pageNumber - 1, this.pageNumber - 1)[this.pages.la - (0 < this.pages.la ? 1 : 0) + this.pageNumber], c.width != this.dimensions.width || c.height != this.dimensions.height) && (this.dimensions = c, this.Xa());
                    if ("SinglePage" == this.aa.ba) {
                        c = this.aa.renderer.getDimensions(this.pageNumber - 1, this.pageNumber - 1)[this.pages.la];
                        if (c.width != this.dimensions.width || c.height != this.dimensions.height) {
                            this.dimensions = c, this.Xa(), jQuery(".flowpaper_pageword_" + this.ja).remove(), this.La();
                        }
                        this.dimensions.loaded = !1;
                    }
                    "Portrait" == this.aa.ba && (c = this.aa.renderer.getDimensions(this.pageNumber - 1, this.pageNumber - 1)[this.pageNumber], c.width != this.dimensions.width || c.height != this.dimensions.height) && (this.dimensions = c, this.Xa(), jQuery(".flowpaper_pageword_" + this.ja).remove(), this.La());
                    this.aa.renderer.Tb(this, !1);
                    "function" === typeof this.yi && this.loadOverlay();
                }
            },
            unload: function() {
                if (this.Ga || "TwoPage" == this.aa.ba || "BookView" == this.aa.ba || this.aa.ba == this.na) {
                    delete this.selectors, this.selectors = {}, jQuery(this.wa).unbind(), delete this.wa, this.wa = null, this.Ga = !1, this.aa.renderer.unload(this), jQuery(this.cc).remove(), this.we && (delete this.we, this.we = null), this.aa.ba == this.na && this.aa.ca.tc.unload(this), "TwoPage" != this.aa.ba && "BookView" != this.aa.ba && this.ga("#" + this.tn()).remove(), "function" === typeof this.yi && this.Or();
                }
            },
            La: function(c) {
                "ThumbView" == this.aa.ba || !this.ib && null == c || this.pages.animating || this.aa.renderer.La(this, !1, c);
            },
            vc: function(c, d) {
                this.aa.renderer.vc(this, c, d);
            },
            Ae: function(c, d, e) {
                this.aa.renderer.Ae(this, c, d, e);
            },
            wl: function() {
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    eb.browser.msie && 9 > eb.browser.version || eb.platform.ios || (new aa(this.aa, "CanvasPageRenderer" == this.renderer.lf() ? this.ma : this.Ka, this.ga(this.Ha).parent())).scroll();
                }
            },
            uf: function(c, d) {
                var e = this;
                if (e.aa.za[e.pageNumber]) {
                    for (var g = 0; g < e.aa.za[e.pageNumber].length; g++) {
                        if ("link" == e.aa.za[e.pageNumber][g].type) {
                            var f = e.aa.za[e.pageNumber][g].Rn * c,
                                m = e.aa.za[e.pageNumber][g].Sn * c,
                                k = e.aa.za[e.pageNumber][g].width * c,
                                l = e.aa.za[e.pageNumber][g].height * c;
                            if (0 == jQuery("#flowpaper_mark_link_" + e.pageNumber + "_" + g).length) {
                                var n = jQuery(String.format("<div id='flowpaper_mark_link_{4}_{5}' class='flowpaper_mark_link flowpaper_mark' style='left:{0}px;top:{1}px;width:{2}px;height:{3}px;box-shadow: 0px 0px 0px 0px;'></div>", f, m, k, l, e.pageNumber, g)),
                                    l = e.Ha;
                                0 == jQuery(l).length && (l = e.Sc);
                                n = jQuery(l).append(n).find("#flowpaper_mark_link_" + e.pageNumber + "_" + g);
                                n.data("link", e.aa.za[e.pageNumber][g].href);
                                n.bind("mousedown touchstart", function(c) {
                                    if (0 == jQuery(this).data("link").indexOf("actionGoTo:")) {
                                        e.aa.gotoPage(jQuery(this).data("link").substr(11));
                                    } else {
                                        if (0 == jQuery(this).data("link").indexOf("javascript")) {
                                            var d = unescape(jQuery(this).data("link"));
                                            eval(d.substring(11));
                                        } else {
                                            jQuery(e.ia).trigger("onExternalLinkClicked", jQuery(this).data("link"));
                                        }
                                    }
                                    c.preventDefault();
                                    c.stopImmediatePropagation();
                                    return !1;
                                });
                                eb.platform.touchonlydevice || (jQuery(n).on("mouseover", function() {
                                    jQuery(this).stop(!0, !0);
                                    jQuery(this).css("background", e.aa.linkColor);
                                    jQuery(this).css({
                                        opacity: e.aa.ge
                                    });
                                }), jQuery(n).on("mouseout", function() {
                                    jQuery(this).css("background", "");
                                    jQuery(this).css({
                                        opacity: 0
                                    });
                                }));
                            } else {
                                n = jQuery("#flowpaper_mark_link_" + e.pageNumber + "_" + g), n.css({
                                    left: f + "px",
                                    top: m + "px",
                                    width: k + "px",
                                    height: l + "px",
                                    "margin-left": d + "px"
                                });
                            }
                        }
                        if ("video" == e.aa.za[e.pageNumber][g].type) {
                            if (m = e.aa.za[e.pageNumber][g].Pl * c, k = e.aa.za[e.pageNumber][g].Ql * c, n = e.aa.za[e.pageNumber][g].width * c, f = e.aa.za[e.pageNumber][g].height * c, l = e.aa.za[e.pageNumber][g].src, 0 == jQuery("#flowpaper_mark_video_" + e.pageNumber + "_" + g).length) {
                                var v = jQuery(String.format("<div id='flowpaper_mark_video_{4}_{5}' class='flowpaper_mark_video flowpaper_mark' style='left:{0}px;top:{1}px;width:{2}px;height:{3}px;margin-left:{7}px'><img src='{6}' style='width:{2}px;height:{3}px;' class='flowpaper_mark'/></div>", m, k, n, f, e.pageNumber, g, l, d)),
                                    l = e.Ha;
                                0 == jQuery(l).length && (l = e.Sc);
                                n = jQuery(l).append(v).find("#flowpaper_mark_video_" + e.pageNumber + "_" + g);
                                n.data("video", e.aa.za[e.pageNumber][g].url);
                                n.data("maximizevideo", e.aa.za[e.pageNumber][g].Zn);
                                n.bind("mousedown touchstart", function(c) {
                                    var d = jQuery(this).data("video"),
                                        g = "true" == jQuery(this).data("maximizevideo");
                                    if (d && 0 <= d.toLowerCase().indexOf("youtube")) {
                                        for (var f = d.substr(d.indexOf("?") + 1).split("&"), h = "", m = 0; m < f.length; m++) {
                                            0 == f[m].indexOf("v=") && (h = f[m].substr(2));
                                        }
                                        g ? (e.aa.ld = jQuery(String.format('<div class="flowpaper_mark_video_maximized flowpaper_mark" style="position:absolute;z-index:99999;left:2.5%;top:2.5%;width:95%;height:95%"></div>')), e.aa.ka.append(e.aa.ld), jQuery(e.aa.ld).html(String.format("<iframe width='{0}' height='{1}' src='{3}://www.youtube.com/embed/{2}?rel=0&autoplay=1&enablejsapi=1' frameborder='0' allowfullscreen ></iframe>", 0.95 * e.aa.ka.width(), 0.95 * e.aa.ka.height(), h, -1 < location.href.indexOf("https:") ? "https" : "http")), f = jQuery(String.format('<img class="flowpaper_mark_video_maximized_closebutton" src="{0}" style="position:absolute;left:97%;top:1%;z-index:999999;cursor:pointer;">', e.Om)), e.aa.ka.append(f), jQuery(f).bind("mousedown touchstart", function() {
                                            jQuery(".flowpaper_mark_video_maximized").remove();
                                            jQuery(".flowpaper_mark_video_maximized_closebutton").remove();
                                        })) : jQuery(this).html(String.format("<iframe width='{0}' height='{1}' src='{3}://www.youtube.com/embed/{2}?rel=0&autoplay=1&enablejsapi=1' frameborder='0' allowfullscreen ></iframe>", jQuery(this).width(), jQuery(this).height(), h, -1 < location.href.indexOf("https:") ? "https" : "http"));
                                    }
                                    d && 0 <= d.toLowerCase().indexOf("vimeo") && (h = d.substr(d.lastIndexOf("/") + 1), g ? (jQuery(this).html(""), e.aa.ld = jQuery(String.format('<div class="flowpaper_mark_video_maximized flowpaper_mark" style="position:absolute;z-index:99999;left:2.5%;top:2.5%;width:95%;height:95%"></div>')), e.aa.ka.append(e.aa.ld), jQuery(e.aa.ld).html(String.format("<iframe src='//player.vimeo.com/video/{2}?autoplay=1' width='{0}' height='{1}' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>", 0.95 * e.aa.ka.width(), 0.95 * e.aa.ka.height(), h))) : jQuery(this).html(String.format("<iframe src='//player.vimeo.com/video/{2}?autoplay=1' width='{0}' height='{1}' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>", jQuery(this).width(), jQuery(this).height(), h)));
                                    if (d && -1 < d.indexOf("{")) {
                                        try {
                                            var k = JSON.parse(d),
                                                l = "vimeoframe_" + FLOWPAPER.rn();
                                            g ? (jQuery(this).html(""), e.aa.ld = jQuery(String.format('<div class="flowpaper_mark_video_maximized flowpaper_mark" style="position:absolute;z-index:99999;left:2.5%;top:2.5%;width:95%;height:95%"></div>')), e.aa.ka.append(e.aa.ld), jQuery(e.aa.ld).html(jQuery(String.format('<video id="{2}" style="width:{3}px;height:{4}px;" class="videoframe flowpaper_mark video-js vjs-default-skin" controls autoplay preload="auto" width="{3}" height="{4}" data-setup=\'{"example_option":true}\'><source src="{0}" type="video/mp4" /><source src="{1}" type="video/webm" /></video>', k.mp4, k.webm, l, 0.95 * e.aa.ka.width(), 0.95 * e.aa.ka.height())))) : jQuery(this).html(jQuery(String.format('<video id="{2}" style="width:{3}px;height:{4}px;" class="videoframe flowpaper_mark video-js vjs-default-skin" controls autoplay preload="auto" width="{3}" height="{4}" data-setup=\'{"example_option":true}\'><source src="{0}" type="video/mp4" /><source src="{1}" type="video/webm" /></video>', k.mp4, k.webm, l, jQuery(this).width(), jQuery(this).height())));
                                        } catch (n) {}
                                    }
                                    c.preventDefault();
                                    c.stopImmediatePropagation();
                                    return !1;
                                });
                            } else {
                                v = jQuery("#flowpaper_mark_video_" + e.pageNumber + "_" + g), v.css({
                                    left: m + "px",
                                    top: k + "px",
                                    width: n + "px",
                                    height: f + "px",
                                    "margin-left": d + "px"
                                }).find(".flowpaper_mark").css({
                                    width: n + "px",
                                    height: f + "px"
                                }), m = v.find("iframe"), 0 < m.length && (m.attr("width", n), m.attr("height", f));
                            }
                        }
                        if ("image" == e.aa.za[e.pageNumber][g].type) {
                            var l = e.aa.za[e.pageNumber][g].Ai * c,
                                v = e.aa.za[e.pageNumber][g].Bi * c,
                                u = e.aa.za[e.pageNumber][g].width * c,
                                p = e.aa.za[e.pageNumber][g].height * c,
                                n = e.aa.za[e.pageNumber][g].src,
                                f = e.aa.za[e.pageNumber][g].href,
                                m = e.aa.za[e.pageNumber][g].Gn;
                            0 == jQuery("#flowpaper_mark_image_" + e.pageNumber + "_" + g).length ? (k = jQuery(String.format("<div id='flowpaper_mark_image_{4}_{5}' class='flowpaper_mark_image flowpaper_mark' style='left:{0}px;top:{1}px;width:{2}px;height:{3}px;'><img src='{6}' style='width:{2}px;height:{3}px;' class='flowpaper_mark'/></div>", l, v, u, p, e.pageNumber, g, n)), l = e.Ha, 0 == jQuery(l).length && (l = e.Sc), l = jQuery(l).append(k).find("#flowpaper_mark_image_" + e.pageNumber + "_" + g), l.data("image", e.aa.za[e.pageNumber][g].url), null != f && 0 < f.length ? (l.data("link", f), l.bind("mousedown touchstart", function(c) {
                                0 == jQuery(this).data("link").indexOf("actionGoTo:") ? e.aa.gotoPage(jQuery(this).data("link").substr(11)) : jQuery(e.ia).trigger("onExternalLinkClicked", jQuery(this).data("link"));
                                c.preventDefault();
                                c.stopImmediatePropagation();
                                return !1;
                            })) : e.aa.Fi || k.css({
                                "pointer-events": "none"
                            }), null != m && 0 < m.length && (l.data("hoversrc", m), l.data("imagesrc", n), l.bind("mouseover", function() {
                                jQuery(this).find(".flowpaper_mark").attr("src", jQuery(this).data("hoversrc"));
                            }), l.bind("mouseout", function() {
                                jQuery(this).find(".flowpaper_mark").attr("src", jQuery(this).data("imagesrc"));
                            }))) : (k = jQuery("#flowpaper_mark_image_" + e.pageNumber + "_" + g), k.css({
                                left: l + "px",
                                top: v + "px",
                                width: u + "px",
                                height: p + "px",
                                "margin-left": d + "px"
                            }).find(".flowpaper_mark").css({
                                width: u + "px",
                                height: p + "px"
                            }));
                        }
                    }
                }
            },
            dispose: function() {
                jQuery(this.Ha).find("*").unbind();
                jQuery(this).unbind();
                jQuery(this.wa).unbind();
                delete this.wa;
                this.wa = null;
                jQuery(this.Ha).find("*").remove();
                this.selectors = this.pages = this.aa = this.ia = null;
            },
            rotate: function() {
                this.rotation && 360 != this.rotation || (this.rotation = 0);
                this.rotation = this.rotation + 90;
                360 == this.rotation && (this.rotation = 0);
                var c = this.Kc();
                if ("Portrait" == this.aa.ba || "SinglePage" == this.aa.ba) {
                    this.Xa(), 90 == this.rotation ? (this.ga(this.ma).transition({
                        rotate: this.rotation
                    }, 0), jQuery(this.Rb).css({
                        "z-index": 11,
                        "margin-left": c
                    }), jQuery(this.Rb).transition({
                        rotate: this.rotation,
                        translate: "-" + c + "px, 0px"
                    }, 0)) : 270 == this.rotation ? (jQuery(this.Rb).css({
                        "z-index": 11,
                        "margin-left": c
                    }), this.ga(this.ma).transition({
                        rotate: this.rotation
                    }, 0), jQuery(this.Rb).transition({
                        rotate: this.rotation,
                        translate: "-" + c + "px, 0px"
                    }, 0)) : 180 == this.rotation ? (jQuery(this.Rb).css({
                        "z-index": 11,
                        "margin-left": c
                    }), this.ga(this.ma).transition({
                        rotate: this.rotation
                    }, 0), jQuery(this.Rb).transition({
                        rotate: this.rotation,
                        translate: "-" + c + "px, 0px"
                    }, 0)) : (jQuery(this.Rb).css({
                        "z-index": "",
                        "margin-left": 0
                    }), this.ga(this.ma).css("transform", ""), jQuery(this.Rb).css("transform", ""));
                }
            }
        };
        return f;
    }();

function ia(f, c) {
    this.aa = this.ra = f;
    this.ia = this.aa.ia;
    this.resources = this.aa.resources;
    this.ja = this.aa.ja;
    this.document = c;
    this.Xe = null;
    this.Ya = "toolbar_" + this.aa.ja;
    this.ea = "#" + this.Ya;
    this.Zj = this.Ya + "_bttnPrintdialogPrint";
    this.Nh = this.Ya + "_bttnPrintdialogCancel";
    this.Wj = this.Ya + "_bttnPrintDialog_RangeAll";
    this.Xj = this.Ya + "_bttnPrintDialog_RangeCurrent";
    this.Yj = this.Ya + "_bttnPrintDialog_RangeSpecific";
    this.Kh = this.Ya + "_bttnPrintDialogRangeText";
    this.Lk = this.Ya + "_labelPrintProgress";
    this.ei = null;
    this.create = function() {
        var c = this;
        c.yl = "";
        if (eb.platform.touchonlydevice || c.ei) {
            c.ei || (e = c.resources.xa.Xp, jQuery(c.ea).html((eb.platform.touchonlydevice ? "" : String.format("<img src='{0}' class='flowpaper_tbbutton_large flowpaper_print flowpaper_bttnPrint' style='margin-left:5px;'/>", c.resources.xa.jq)) + (c.aa.config.document.ViewModeToolsVisible ? (eb.platform.Hb ? "" : String.format("<img src='{0}' class='flowpaper_tbbutton_large flowpaper_viewmode flowpaper_singlepage {1} flowpaper_bttnSinglePage' style='margin-left:15px;'>", c.resources.xa.kq, "Portrait" == c.aa.Fb ? "flowpaper_tbbutton_pressed" : "")) + (eb.platform.Hb ? "" : String.format("<img src='{0}' style='margin-left:-1px;' class='flowpaper_tbbutton_large flowpaper_viewmode  flowpaper_twopage {1} flowpaper_bttnTwoPage'>", c.resources.xa.rq, "TwoPage" == c.aa.Fb ? "flowpaper_tbbutton_pressed" : "")) + (eb.platform.Hb ? "" : String.format("<img src='{0}' style='margin-left:-1px;' class='flowpaper_tbbutton_large flowpaper_viewmode flowpaper_thumbview flowpaper_bttnThumbView'>", c.resources.xa.qq)) + (eb.platform.Hb ? "" : String.format("<img src='{0}' style='margin-left:-1px;' class='flowpaper_tbbutton_large flowpaper_fitmode flowpaper_fitwidth flowpaper_bttnFitWidth'>", c.resources.xa.Zp)) + (eb.platform.Hb ? "" : String.format("<img src='{0}' style='margin-left:-1px;' class='flowpaper_tbbutton_large flowpaper_fitmode fitheight flowpaper_bttnFitHeight'>", c.resources.xa.hq)) + "" : "") + (c.aa.config.document.ZoomToolsVisible ? String.format("<img class='flowpaper_tbbutton_large flowpaper_bttnZoomIn' src='{0}' style='margin-left:5px;' />", c.resources.xa.tq) + String.format("<img class='flowpaper_tbbutton_large flowpaper_bttnZoomOut' src='{0}' style='margin-left:-1px;' />", c.resources.xa.uq) + (eb.platform.Hb ? "" : String.format("<img class='flowpaper_tbbutton_large flowpaper_bttnFullScreen' src='{0}' style='margin-left:-1px;' />", c.resources.xa.aq)) + "" : "") + (c.aa.config.document.NavToolsVisible ? String.format("<img src='{0}' class='flowpaper_tbbutton_large flowpaper_previous flowpaper_bttnPrevPage' style='margin-left:15px;'/>", c.resources.xa.Np) + String.format("<input type='text' class='flowpaper_tbtextinput_large flowpaper_currPageNum flowpaper_txtPageNumber' value='1' style='width:70px;text-align:right;' />") + String.format("<div class='flowpaper_tblabel_large flowpaper_numberOfPages flowpaper_lblTotalPages'> / </div>") + String.format("<img src='{0}'  class='flowpaper_tbbutton_large flowpaper_next flowpaper_bttnPrevNext'/>", c.resources.xa.Op) + "" : "") + (c.aa.config.document.SearchToolsVisible ? String.format("<input type='text' class='flowpaper_tbtextinput_large flowpaper_txtSearch' style='margin-left:15px;width:130px;' />") + String.format("<img src='{0}' class='flowpaper_find flowpaper_tbbutton_large flowpaper_bttnFind' style=''/>", c.resources.xa.Yp) + "" : "")), jQuery(c.ea).addClass("flowpaper_toolbarios"));
        } else {
            var e = c.resources.xa.Wl,
                g = String.format("<div class='flowpaper_floatright flowpaper_bttnPercent' sbttnPrintIdtyle='text-align:center;padding-top:5px;background-repeat:no-repeat;width:20px;height:20px;font-size:9px;font-family:Arial;background-image:url({0})'><div id='lblPercent'></div></div>", c.resources.xa.nm);
            eb.browser.msie && addCSSRule(".flowpaper_tbtextinput", "height", "18px");
            jQuery(c.ea).html(String.format("<img src='{0}' class='flowpaper_tbbutton print flowpaper_bttnPrint'/>", c.resources.xa.jm) + String.format("<img src='{0}' class='flowpaper_tbseparator' />", e) + (c.aa.config.document.ViewModeToolsVisible ? String.format("<img src='{1}' class='flowpaper_bttnSinglePage flowpaper_tbbutton flowpaper_viewmode flowpaper_singlepage {0}' />", "Portrait" == c.aa.Fb ? "flowpaper_tbbutton_pressed" : "", c.resources.xa.mm) + String.format("<img src='{1}' class='flowpaper_bttnTwoPage flowpaper_tbbutton flowpaper_viewmode flowpaper_twopage {0}' />", "TwoPage" == c.aa.Fb ? "flowpaper_tbbutton_pressed" : "", c.resources.xa.qm) + String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_thumbview flowpaper_viewmode flowpaper_bttnThumbView' />", c.resources.xa.pm) + String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_fitmode flowpaper_fitwidth flowpaper_bttnFitWidth' />", c.resources.xa.im) + String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_fitmode flowpaper_fitheight flowpaper_bttnFitHeight'/>", c.resources.xa.hm) + String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_bttnRotate'/>", c.resources.xa.lm) + String.format("<img src='{0}' class='flowpaper_tbseparator' />", e) : "") + (c.aa.config.document.ZoomToolsVisible ? String.format("<div class='flowpaper_slider flowpaper_zoomSlider' style='{0}'><div class='flowpaper_handle' style='{0}'></div></div>", eb.browser.msie && 9 > eb.browser.version ? c.yl : "") + String.format("<input type='text' class='flowpaper_tbtextinput flowpaper_txtZoomFactor' style='width:40px;' />") + String.format("<img class='flowpaper_tbbutton flowpaper_bttnFullScreen' src='{0}' />", c.resources.xa.$l) + String.format("<img src='{0}' class='flowpaper_tbseparator' style='margin-left:5px' />", e) : "") + (c.aa.config.document.NavToolsVisible ? String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_previous flowpaper_bttnPrevPage'/>", c.resources.xa.Tl) + String.format("<input type='text' class='flowpaper_tbtextinput flowpaper_currPageNum flowpaper_txtPageNumber' value='1' style='width:50px;text-align:right;' />") + String.format("<div class='flowpaper_tblabel flowpaper_numberOfPages flowpaper_lblTotalPages'> / </div>") + String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_next flowpaper_bttnPrevNext'/>", c.resources.xa.Ul) + String.format("<img src='{0}' class='flowpaper_tbseparator' />", e) : "") + (c.aa.config.document.CursorToolsVisible ? String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_bttnTextSelect'/>", c.resources.xa.om) + String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_tbbutton_pressed flowpaper_bttnHand'/>", c.resources.xa.bm) + String.format("<img src='{0}' class='flowpaper_tbseparator' />", e) : "") + (c.aa.config.document.SearchToolsVisible ? String.format("<input type='text' class='flowpaper_tbtextinput flowpaper_txtSearch' style='width:70px;margin-left:4px' />") + String.format("<img src='{0}' class='flowpaper_find flowpaper_tbbutton flowpaper_bttnFind' />", c.resources.xa.Zl) + String.format("<img src='{0}' class='flowpaper_tbseparator' />", e) : "") + g);
            jQuery(c.ea).addClass("flowpaper_toolbarstd");
        }
        jQuery(c.ia).bind("onDocumentLoaded", function() {
            jQuery(c.ea).find(".flowpaper_bttnPercent").hide();
        });
    };
    this.Ok = function(c) {
        c = this.fb = c.split("\n");
        jQuery(this.ea).find(".flowpaper_bttnPrint").attr("title", this.Fa(c, "Print"));
        jQuery(this.ea).find(".flowpaper_bttnSinglePage").attr("title", this.Fa(c, "SinglePage"));
        jQuery(this.ea).find(".flowpaper_bttnTwoPage, .flowpaper_bttnBookView").attr("title", this.Fa(c, "TwoPage"));
        jQuery(this.ea).find(".flowpaper_bttnThumbView").attr("title", this.Fa(c, "ThumbView"));
        jQuery(this.ea).find(".flowpaper_bttnFitWidth").attr("title", this.Fa(c, "FitWidth"));
        jQuery(this.ea).find(".flowpaper_bttnFitHeight").attr("title", this.Fa(c, "FitHeight"));
        jQuery(this.ea).find(".flowpaper_bttnFitHeight").attr("title", this.Fa(c, "FitPage"));
        jQuery(this.ea).find(".flowpaper_zoomSlider").attr("title", this.Fa(c, "Scale"));
        jQuery(this.ea).find(".flowpaper_txtZoomFactor").attr("title", this.Fa(c, "Scale"));
        jQuery(this.ea).find(".flowpaper_bttnFullScreen, .flowpaper_bttnFullscreen").attr("title", this.Fa(c, "Fullscreen"));
        jQuery(this.ea).find(".flowpaper_bttnPrevPage").attr("title", this.Fa(c, "PreviousPage"));
        jQuery(this.ea).find(".flowpaper_txtPageNumber").attr("title", this.Fa(c, "CurrentPage"));
        jQuery(this.ea).find(".flowpaper_bttnPrevNext").attr("title", this.Fa(c, "NextPage"));
        jQuery(this.ea).find(".flowpaper_txtSearch, .flowpaper_bttnTextSearch").attr("title", this.Fa(c, "Search"));
        jQuery(this.ea).find(".flowpaper_bttnFind").attr("title", this.Fa(c, "Search"));
        var e = this.aa.Ye && 0 < this.aa.Ye.length ? this.aa.Ye : this.aa.ka;
        e.find(".flowpaper_bttnHighlight").find(".flowpaper_tbtextbutton").html(this.Fa(c, "Highlight", "Highlight"));
        e.find(".flowpaper_bttnComment").find(".flowpaper_tbtextbutton").html(this.Fa(c, "Comment", "Comment"));
        e.find(".flowpaper_bttnStrikeout").find(".flowpaper_tbtextbutton").html(this.Fa(c, "Strikeout", "Strikeout"));
        e.find(".flowpaper_bttnDraw").find(".flowpaper_tbtextbutton").html(this.Fa(c, "Draw", "Draw"));
        e.find(".flowpaper_bttnDelete").find(".flowpaper_tbtextbutton").html(this.Fa(c, "Delete", "Delete"));
        e.find(".flowpaper_bttnShowHide").find(".flowpaper_tbtextbutton").html(this.Fa(c, "ShowAnnotations", "Show Annotations"));
    };
    this.Fa = function(c, e, g) {
        for (var f = 0; f < c.length; f++) {
            var m = c[f].split("=");
            if (m[0] == e) {
                return m[1];
            }
        }
        return g ? g : null;
    };
    this.bindEvents = function() {
        var c = this;
        jQuery(c.ea).find(".flowpaper_tbbutton_large, .flowpaper_tbbutton").each(function() {
            jQuery(this).data("minscreenwidth") && parseInt(jQuery(this).data("minscreenwidth")) > window.innerWidth && jQuery(this).hide();
        });
        if (0 == c.aa.ka.find(".flowpaper_printdialog").length) {
            var e = c.Fa(c.fb, "Enterpagenumbers", "Enter page numbers and/or page ranges separated by commas. For example 1,3,5-12");
            c.aa.Fi ? c.aa.ka.prepend("<div id='modal-print' class='modal-content flowpaper_printdialog' style='overflow:hidden;;'><div style='background-color:#fff;color:#000;padding:10px 10px 10px 10px;height:205px;padding-bottom:20px;'>It's not possible to print from within Desktop Publisher. <br/><br/>You can try this feature by clicking on 'Publish' and then 'View in Browser'.<br/><br/><a class='flowpaper_printdialog_button' id='" + c.Nh + "'>OK</a></div></div>") : c.aa.ka.prepend("<div id='modal-print' class='modal-content flowpaper_printdialog' style='overflow:hidden;'><font style='color:#000000;font-size:11px'><b>" + c.Fa(c.fb, "Selectprintrange", "Select print range") + "</b></font><div style='width:98%;padding-top:5px;padding-left:5px;background-color:#ffffff;'><table border='0' style='margin-bottom:10px;'><tr><td><input type='radio' name='PrintRange' checked='checked' id='" + c.Wj + "'/></td><td>" + c.Fa(c.fb, "All", "All") + "</td></tr><tr><td><input type='radio' name='PrintRange' id='" + c.Xj + "'/></td><td>" + c.Fa(c.fb, "CurrentPage", "Current Page") + "</td></tr><tr><td><input type='radio' name='PrintRange' id='" + c.Yj + "'/></td><td>" + c.Fa(c.fb, "Pages", "Pages") + "</td><td><input type='text' style='width:120px' id='" + c.Kh + "' /><td></tr><tr><td colspan='3'>" + e + "</td></tr></table><a id='" + c.Zj + "' class='flowpaper_printdialog_button'>" + c.Fa(c.fb, "Print", "Print") + "</a>&nbsp;&nbsp;<a class='flowpaper_printdialog_button' id='" + c.Nh + "'>" + c.Fa(c.fb, "Cancel", "Cancel") + "</a><span id='" + c.Lk + "' style='padding-left:5px;'></span><div style='height:5px;display:block;margin-top:5px;'>&nbsp;</div></div></div>");
        }
        jQuery("input:radio[name=PrintRange]:nth(0)").attr("checked", !0);
        c.aa.config.Toolbar ? (jQuery(c.ea).find(".flowpaper_txtZoomFactor").bind("click", function() {
            if (!jQuery(this).hasClass("flowpaper_tbbutton_disabled")) {
                return !1;
            }
        }), jQuery(c.ea).find(".flowpaper_currPageNum").bind("click", function() {
            jQuery(c.ea).find(".flowpaper_currPageNum").focus();
        }), jQuery(c.ea).find(".flowpaper_txtSearch").bind("click", function() {
            jQuery(c.ea).find(".flowpaper_txtSearch").focus();
            return !1;
        }), jQuery(c.ea).find(".flowpaper_bttnFind").bind("click", function() {
            c.searchText(jQuery(c.ea).find(".flowpaper_txtSearch").val());
            jQuery(c.ea).find(".flowpaper_bttnFind").focus();
            return !1;
        })) : (jQuery(c.ea).find(".flowpaper_bttnFitWidth").bind("click", function() {
            jQuery(this).hasClass("flowpaper_tbbutton_disabled") || (c.aa.fitwidth(), jQuery("#toolbar").trigger("onFitModeChanged", "Fit Width"));
        }), jQuery(c.ea).find(".flowpaper_bttnFitHeight").bind("click", function() {
            jQuery(this).hasClass("flowpaper_tbbutton_disabled") || (c.aa.fitheight(), jQuery("#toolbar").trigger("onFitModeChanged", "Fit Height"));
        }), jQuery(c.ea).find(".flowpaper_bttnTwoPage").bind("click", function() {
            jQuery(this).hasClass("flowpaper_tbbutton_disabled") || ("BookView" == c.aa.Fb ? c.aa.switchMode("BookView") : c.aa.switchMode("TwoPage"));
        }), jQuery(c.ea).find(".flowpaper_bttnSinglePage").bind("click", function() {
            c.aa.config.document.TouchInitViewMode && "SinglePage" != !c.aa.config.document.TouchInitViewMode || !eb.platform.touchonlydevice ? c.aa.switchMode("Portrait", c.aa.getCurrPage() - 1) : c.aa.switchMode("SinglePage", c.aa.getCurrPage());
        }), jQuery(c.ea).find(".flowpaper_bttnThumbView").bind("click", function() {
            c.aa.switchMode("Tile");
        }), jQuery(c.ea).find(".flowpaper_bttnPrint").bind("click", function() {
            eb.platform.touchonlydevice ? c.aa.printPaper("current") : (jQuery("#modal-print").css("background-color", "#dedede"), c.aa.Zi = jQuery("#modal-print").smodal({
                minHeight: 255,
                appendTo: c.aa.ka
            }), jQuery("#modal-print").parent().css("background-color", "#dedede"));
        }), jQuery(c.ea).find(".flowpaper_bttnDownload").bind("click", function() {
            window.zine ? (window.open(FLOWPAPER.uj(c.document.PDFFile, c.aa.getCurrPage()), "windowname3", null), 0 < c.document.PDFFile.indexOf("[*,") && -1 == c.document.PDFFile.indexOf("[*,2,true]") && 1 < c.aa.getTotalPages() && 1 < c.aa.getCurrPage() && window.open(FLOWPAPER.uj(c.document.PDFFile, c.aa.getCurrPage() - 1), "windowname4", null)) : window.open(FLOWPAPER.uj(c.document.PDFFile, c.aa.getCurrPage()), "windowname4", null);
            return !1;
        }), jQuery(c.ea).find(".flowpaper_bttnOutline").bind("click", function() {
            c.aa.dn();
        }), jQuery(c.ea).find(".flowpaper_bttnPrevPage").bind("click", function() {
            c.aa.previous();
            return !1;
        }), jQuery(c.ea).find(".flowpaper_bttnPrevNext").bind("click", function() {
            c.aa.next();
            return !1;
        }), jQuery(c.ea).find(".flowpaper_bttnZoomIn").bind("click", function() {
            "TwoPage" == c.aa.ba || "BookView" == c.aa.ba ? c.aa.pages.fe() : "Portrait" != c.aa.ba && "SinglePage" != c.aa.ba || c.aa.ZoomIn();
        }), jQuery(c.ea).find(".flowpaper_bttnZoomOut").bind("click", function() {
            "TwoPage" == c.aa.ba || "BookView" == c.aa.ba ? c.aa.pages.dd() : "Portrait" != c.aa.ba && "SinglePage" != c.aa.ba || c.aa.ZoomOut();
        }), jQuery(c.ea).find(".flowpaper_txtZoomFactor").bind("click", function() {
            if (!jQuery(this).hasClass("flowpaper_tbbutton_disabled")) {
                return jQuery(c.ea).find(".flowpaper_txtZoomFactor").focus(), !1;
            }
        }), jQuery(c.ea).find(".flowpaper_currPageNum").bind("click", function() {
            jQuery(c.ea).find(".flowpaper_currPageNum").focus();
        }), jQuery(c.ea).find(".flowpaper_txtSearch").bind("click", function() {
            jQuery(c.ea).find(".flowpaper_txtSearch").focus();
            return !1;
        }), jQuery(c.ea).find(".flowpaper_bttnFullScreen, .flowpaper_bttnFullscreen").bind("click", function() {
            c.aa.openFullScreen();
        }), jQuery(c.ea).find(".flowpaper_bttnFind").bind("click", function() {
            c.searchText(jQuery(c.ea).find(".flowpaper_txtSearch").val());
            jQuery(c.ea).find(".flowpaper_bttnFind").focus();
            return !1;
        }), jQuery(c.ea).find(".flowpaper_bttnTextSelect").bind("click", function() {
            c.aa.se = "flowpaper_selected_default";
            jQuery(c.ea).find(".flowpaper_bttnTextSelect").addClass("flowpaper_tbbutton_pressed");
            jQuery(c.ea).find(".flowpaper_bttnHand").removeClass("flowpaper_tbbutton_pressed");
            c.aa.setCurrentCursor("TextSelectorCursor");
        }), jQuery(c.ea).find(".flowpaper_bttnHand").bind("click", function() {
            jQuery(c.ea).find(".flowpaper_bttnHand").addClass("flowpaper_tbbutton_pressed");
            jQuery(c.ea).find(".flowpaper_bttnTextSelect").removeClass("flowpaper_tbbutton_pressed");
            c.aa.setCurrentCursor("ArrowCursor");
        }), jQuery(c.ea).find(".flowpaper_bttnRotate").bind("click", function() {
            c.aa.rotate();
        }));
        jQuery("#" + c.Kh).bind("keydown", function() {
            jQuery(this).focus();
        });
        jQuery(c.ea).find(".flowpaper_currPageNum, .flowpaper_txtPageNumber").bind("keydown", function(e) {
            if (!jQuery(this).hasClass("flowpaper_tbbutton_disabled")) {
                if ("13" != e.keyCode) {
                    return;
                }
                c.gotoPage(this);
            }
            return !1;
        });
        jQuery(c.ea).find(".flowpaper_txtSearch").bind("keydown", function(e) {
            if ("13" == e.keyCode) {
                return c.searchText(jQuery(c.ea).find(".flowpaper_txtSearch").val()), !1;
            }
        });
        jQuery(c.ea).bind("onZoomFactorChanged", function(e, f) {
            var m = Math.round(f.df / c.aa.document.MaxZoomSize * 100 * c.aa.document.MaxZoomSize) + "%";
            jQuery(c.ea).find(".flowpaper_txtZoomFactor").val(m);
            c.df != f.df && (c.df = f.df, jQuery(c.aa).trigger("onScaleChanged", f.df));
        });
        jQuery(c.ia).bind("onDocumentLoaded", function(e, f) {
            2 > f ? jQuery(c.ea).find(".flowpaper_bttnTwoPage").addClass("flowpaper_tbbutton_disabled") : jQuery(c.ea).find(".flowpaper_bttnTwoPage").removeClass("flowpaper_tbbutton_disabled");
        });
        jQuery(c.ea).bind("onCursorChanged", function(e, f) {
            "TextSelectorCursor" == f && (jQuery(c.ea).find(".flowpaper_bttnTextSelect").addClass("flowpaper_tbbutton_pressed"), jQuery(c.ea).find(".flowpaper_bttnHand").removeClass("flowpaper_tbbutton_pressed"));
            "ArrowCursor" == f && (jQuery(c.ea).find(".flowpaper_bttnHand").addClass("flowpaper_tbbutton_pressed"), jQuery(c.ea).find(".flowpaper_bttnTextSelect").removeClass("flowpaper_tbbutton_pressed"));
        });
        jQuery(c.ea).bind("onFitModeChanged", function(e, f) {
            jQuery(".flowpaper_fitmode").each(function() {
                jQuery(this).removeClass("flowpaper_tbbutton_pressed");
            });
            "FitHeight" == f && jQuery(c.ea).find(".flowpaper_bttnFitHeight").addClass("flowpaper_tbbutton_pressed");
            "FitWidth" == f && jQuery(c.ea).find(".flowpaper_bttnFitWidth").addClass("flowpaper_tbbutton_pressed");
        });
        jQuery(c.ea).bind("onProgressChanged", function(e, f) {
            jQuery("#lblPercent").html(100 * f);
            1 == f && jQuery(c.ea).find(".flowpaper_bttnPercent").hide();
        });
        jQuery(c.ea).bind("onViewModeChanged", function(e, f) {
            jQuery(c.ia).trigger("onViewModeChanged", f);
            jQuery(".flowpaper_viewmode").each(function() {
                jQuery(this).removeClass("flowpaper_tbbutton_pressed");
            });
            if ("Portrait" == c.aa.ba || "SinglePage" == c.aa.ba) {
                jQuery(c.ea).find(".flowpaper_bttnSinglePage").addClass("flowpaper_tbbutton_pressed"), jQuery(c.ea).find(".flowpaper_bttnFitWidth").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnFitHeight").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnPrevPage").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnPrevNext").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnTextSelect").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_zoomSlider").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_txtZoomFactor").removeClass("flowpaper_tbbutton_disabled"), c.aa.toolbar.Ec && c.aa.toolbar.Ec.enable();
            }
            if ("TwoPage" == c.aa.ba || "BookView" == c.aa.ba || "FlipView" == c.aa.ba) {
                jQuery(c.ea).find(".flowpaper_bttnBookView").addClass("flowpaper_tbbutton_pressed"), jQuery(c.ea).find(".flowpaper_bttnTwoPage").addClass("flowpaper_tbbutton_pressed"), jQuery(c.ea).find(".flowpaper_bttnFitWidth").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnFitHeight").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnPrevPage").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnPrevNext").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnTextSelect").removeClass("flowpaper_tbbutton_disabled"), eb.platform.touchdevice && (jQuery(c.ea).find(".flowpaper_zoomSlider").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_txtZoomFactor").addClass("flowpaper_tbbutton_disabled"), c.aa.toolbar.Ec && c.aa.toolbar.Ec.disable()), eb.platform.touchdevice || eb.browser.msie || (jQuery(c.ea).find(".flowpaper_zoomSlider").removeClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_txtZoomFactor").removeClass("flowpaper_tbbutton_disabled"), c.aa.toolbar.Ec && c.aa.toolbar.Ec.enable());
            }
            "ThumbView" == c.aa.ba && (jQuery(c.ea).find(".flowpaper_bttnThumbView").addClass("flowpaper_tbbutton_pressed"), jQuery(c.ea).find(".flowpaper_bttnFitWidth").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnFitHeight").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnPrevPage").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnPrevNext").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_bttnTextSelect").addClass("flowpaper_tbbutton_disabled"), eb.platform.touchdevice || (jQuery(c.ea).find(".flowpaper_zoomSlider").addClass("flowpaper_tbbutton_disabled"), jQuery(c.ea).find(".flowpaper_txtZoomFactor").addClass("flowpaper_tbbutton_disabled"), c.aa.toolbar.Ec && c.aa.toolbar.Ec.disable()));
        });
        jQuery(c.ea).bind("onFullscreenChanged", function(e, f) {
            f ? jQuery(c.ea).find(".flowpaper_bttnFullscreen").addClass("flowpaper_tbbutton_disabled") : jQuery(c.ea).find(".flowpaper_bttnFullscreen").removeClass("flowpaper_tbbutton_disabled");
        });
        jQuery(c.ea).bind("onScaleChanged", function(e, f) {
            jQuery(c.ia).trigger("onScaleChanged", f);
            c.Ec && c.Ec.setValue(f, !0);
        });
        jQuery("#" + c.Nh).bind("click", function(e) {
            jQuery.smodal.close();
            e.stopImmediatePropagation();
            c.aa.Zi = null;
            return !1;
        });
        jQuery("#" + c.Zj).bind("click", function() {
            var e = "";
            jQuery("#" + c.Wj).is(":checked") && (c.aa.printPaper("all"), e = "1-" + c.aa.renderer.getNumPages());
            jQuery("#" + c.Xj).is(":checked") && (c.aa.printPaper("current"), e = jQuery(c.ea).find(".flowpaper_txtPageNumber").val());
            jQuery("#" + c.Yj).is(":checked") && (e = jQuery("#" + c.Kh).val(), c.aa.printPaper(e));
            jQuery(this).html("Please wait");
            window.onPrintRenderingProgress = function(e) {
                jQuery("#" + c.Lk).html("Processing page:" + e);
            };
            window.onPrintRenderingCompleted = function() {
                jQuery.smodal.close();
                c.aa.Zi = null;
                c.ia.trigger("onDocumentPrinted", e);
            };
            return !1;
        });
        c.Dp();
    };
    this.xm = function(c, e) {
        var g = this;
        if (0 != jQuery(g.ea).find(".flowpaper_zoomSlider").length && null == g.Ec) {
            g = this;
            this.wf = c;
            this.vf = e;
            if (window.zine) {
                var f = {
                    Jf: 0,
                    ic: g.aa.ia.width() / 2,
                    Ic: g.aa.ia.height() / 2
                };
                g.Ec = new Slider(jQuery(g.ea).find(".flowpaper_zoomSlider").get(0), {
                    callback: function(c) {
                        c * g.aa.document.MaxZoomSize >= g.aa.document.MinZoomSize && c <= g.aa.document.MaxZoomSize ? g.aa.lb(g.aa.document.MaxZoomSize * c, f) : c * g.aa.document.MaxZoomSize < g.aa.document.MinZoomSize ? g.aa.lb(g.aa.document.MinZoomSize, f) : c > g.aa.document.MaxZoomSize && g.aa.lb(g.aa.document.MaxZoomSize, f);
                    },
                    animation_callback: function(c) {
                        c * g.aa.document.MaxZoomSize >= g.aa.document.MinZoomSize && c <= g.aa.document.MaxZoomSize ? g.aa.lb(g.aa.document.MaxZoomSize * c, f) : c * g.aa.document.MaxZoomSize < g.aa.document.MinZoomSize ? g.aa.lb(g.aa.document.MinZoomSize, f) : c > g.aa.document.MaxZoomSize && g.aa.lb(g.aa.document.MaxZoomSize, f);
                    },
                    snapping: !1
                });
            } else {
                jQuery(g.ea).find(".flowpaper_zoomSlider > *").bind("mousedown", function() {
                    jQuery(g.ea).find(".flowpaper_bttnFitWidth").removeClass("flowpaper_tbbutton_pressed");
                    jQuery(g.ea).find(".flowpaper_bttnFitHeight").removeClass("flowpaper_tbbutton_pressed");
                }), g.Ec = new Slider(jQuery(g.ea).find(".flowpaper_zoomSlider").get(0), {
                    callback: function(c) {
                        jQuery(g.ea).find(".flowpaper_bttnFitWidth, .flowpaper_bttnFitHeight").hasClass("flowpaper_tbbutton_pressed") && "up" === g.aa.bh || (c * g.aa.document.MaxZoomSize >= g.wf && c <= g.vf ? g.aa.lb(g.aa.document.MaxZoomSize * c) : c * g.aa.document.MaxZoomSize < g.wf ? g.aa.lb(g.wf) : c > g.vf && g.aa.lb(g.vf));
                    },
                    animation_callback: function(c) {
                        jQuery(g.ea).find(".flowpaper_bttnFitWidth, .flowpaper_bttnFitHeight").hasClass("flowpaper_tbbutton_pressed") && "up" === g.aa.bh || (c * g.aa.document.MaxZoomSize >= g.wf && c <= g.vf ? g.aa.lb(g.aa.document.MaxZoomSize * c) : c * g.aa.document.MaxZoomSize < g.wf ? g.aa.lb(g.wf) : c > g.vf && g.aa.lb(g.vf));
                    },
                    snapping: !1
                });
            }
            jQuery(g.ea).find(".flowpaper_txtZoomFactor").bind("keypress", function(c) {
                if (!jQuery(this).hasClass("flowpaper_tbbutton_disabled") && 13 == c.keyCode) {
                    try {
                        var d = {
                                Jf: 0,
                                ic: g.aa.ia.width() / 2,
                                Ic: g.aa.ia.height() / 2
                            },
                            e = jQuery(g.ea).find(".flowpaper_txtZoomFactor").val().replace("%", "") / 100;
                        g.aa.Zoom(e, d);
                    } catch (f) {}
                    return !1;
                }
            });
        }
    };
    this.Ep = function(c) {
        jQuery(c).val() > this.document.numPages && jQuery(c).val(this.document.numPages);
        (1 > jQuery(c).val() || isNaN(jQuery(c).val())) && jQuery(c).val(1);
    };
    this.Cp = function(c) {
        "TwoPage" == this.aa.ba ? "1" == c ? jQuery(this.ea).find(".flowpaper_txtPageNumber").val("1-2") : parseInt(c) <= this.document.numPages && 0 == this.document.numPages % 2 || parseInt(c) < this.document.numPages && 0 != this.document.numPages % 2 ? jQuery(this.ea).find(".flowpaper_txtPageNumber").val(c + "-" + (c + 1)) : jQuery(this.ea).find(".flowpaper_txtPageNumber").val(this.document.numPages) : "BookView" == this.aa.ba || "FlipView" == this.aa.ba ? "1" != c || eb.platform.iphone ? !(parseInt(c) + 1 <= this.document.numPages) || this.aa.ca && this.aa.ca.Ta ? jQuery(this.ea).find(".flowpaper_txtPageNumber").val(this.oh(c, c)) : (0 != parseInt(c) % 2 && 1 < parseInt(c) && (c = c - 1), jQuery(this.ea).find(".flowpaper_txtPageNumber").val(this.oh(c, 1 < parseInt(c) ? c + "-" + (c + 1) : c))) : jQuery(this.ea).find(".flowpaper_txtPageNumber").val(this.oh(1, "1")) : "0" != c && jQuery(this.ea).find(".flowpaper_txtPageNumber").val(this.oh(c, c));
    };
    this.Eo = function(c) {
        if (this.aa.labels) {
            for (var e = this.aa.labels.children(), g = 0; g < e.length; g++) {
                if (e[g].getAttribute("title") == c) {
                    return parseInt(e[g].getAttribute("pageNumber"));
                }
            }
        }
        return null;
    };
    this.oh = function(c, e) {
        0 == c && (c = 1);
        if (this.aa.labels) {
            var g = this.aa.labels.children();
            if (g.length > parseInt(c) - 1) {
                var f = g[parseInt(c - 1)].getAttribute("title");
                isNaN(f) ? e = unescape(g[parseInt(c) - 1].getAttribute("title")) : !("FlipView" == this.aa.ba && 1 < parseInt(f) && parseInt(f) + 1 <= this.document.numPages) || this.aa.ca && this.aa.ca.Ta ? e = f : (0 != parseInt(f) % 2 && (f = parseInt(f) - 1), e = f + "-" + (parseInt(f) + 1));
            }
        }
        return e;
    };
    this.Dp = function() {
        jQuery(this.ea).find(".flowpaper_lblTotalPages").html(" / " + this.document.numPages);
    };
    this.gotoPage = function(c) {
        var e = this.Eo(jQuery(c).val());
        e ? this.aa.gotoPage(e) : 0 <= jQuery(c).val().indexOf("-") && "TwoPage" == this.aa.ba ? (c = jQuery(c).val().split("-"), isNaN(c[0]) || isNaN(c[1]) || (0 == parseInt(c[0]) % 2 ? this.aa.gotoPage(parseInt(c[0]) - 1) : this.aa.gotoPage(parseInt(c[0])))) : isNaN(jQuery(c).val()) || (this.Ep(c), this.aa.gotoPage(jQuery(c).val()));
    };
    this.searchText = function(c) {
        this.aa.searchText(c);
    };
}
window.addCSSRule = function(f, c, d) {
    for (var e = null, g = 0; g < document.styleSheets.length; g++) {
        try {
            var h = document.styleSheets[g],
                m = h.cssRules || h.rules,
                k = f.toLowerCase();
            if (null != m) {
                null == e && (e = document.styleSheets[g]);
                for (var l = 0, n = m.length; l < n; l++) {
                    if (m[l].selectorText && m[l].selectorText.toLowerCase() == k) {
                        if (null != d) {
                            m[l].style[c] = d;
                            return;
                        }
                        h.deleteRule ? h.deleteRule(l) : h.removeRule ? h.removeRule(l) : m[l].style.cssText = "";
                    }
                }
            }
        } catch (v) {}
    }
    h = e || {};
    h.insertRule ? (m = h.cssRules || h.rules, h.insertRule(f + "{ " + c + ":" + d + "; }", m.length)) : h.addRule && h.addRule(f, c + ":" + d + ";", 0);
};
window.FlowPaperViewer_Zine = function(f, c, d) {
    this.aa = c;
    this.ia = d;
    this.toolbar = f;
    this.na = "FlipView";
    this.Gm = this.toolbar.Ya + "_barPrint";
    this.Im = this.toolbar.Ya + "_barViewMode";
    this.Fm = this.toolbar.Ya + "_barNavTools";
    this.Em = this.toolbar.Ya + "_barCursorTools";
    this.Hm = this.toolbar.Ya + "_barSearchTools";
    this.ua = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
    this.Mh = this.Lh = "/include/pdf/assets_zine/bttnPrint.png";
    this.wg = this.Fh = "/include/pdf/assets_zine/bttnBookView.png";
    this.Oh = this.Bg = "/include/pdf/assets_zine/bttnSinglePage.png";
    this.Rh = this.Eg = this.Qh = this.Dg = this.Ph = this.Cg = "/include/pdf/assets_zine/";
    this.yg = "/include/pdf/assets_zine/bttnFullscreen.png";
    this.Gh = this.xg = this.Ih = this.zg = this.Jh = this.Ag = this.Hh = "/include/pdf/assets_zine/";
    this.Of = "/include/pdf/assets_zine/bar.png";
    this.ap = "/include/pdf/assets_zine/bttnDownload.png";
    this.gp = "/include/pdf/assets_zine/bttnPrint.png";
    this.np = "/include/pdf/assets_zine/bttnSocialShare.png";
    this.$o = "/include/pdf/assets_zine/bttnSinglePage.png";
    this.qp = "/include/pdf/assets_zine/bttnBookView.png";
    this.ep = "/include/pdf/assets_zine/bttnHand.png";
    this.fp = "/include/pdf/assets_zine/bttnPrevPage.png";
    this.hp = "/include/pdf/assets_zine/bttnPrevNext.png";
    this.rp = "/include/pdf/assets_zine/bttnFind.png";
    this.cp = "/include/pdf/assets_zine/bttnFullscreen.png";
    this.pp = "/include/pdf/assets_zine/bttnTextSelect.png";
    this.mp = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NDJBOEJGMUEyN0IyMTFFMTlFOTNFMjNDNDUxOUFGMTciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NDJBOEJGMUIyN0IyMTFFMTlFOTNFMjNDNDUxOUFGMTciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo0MkE4QkYxODI3QjIxMUUxOUU5M0UyM0M0NTE5QUYxNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo0MkE4QkYxOTI3QjIxMUUxOUU5M0UyM0M0NTE5QUYxNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PrESQzQAAAF3SURBVHjaYvz//z8DPQATA53A8LOIkRLNNpaWAkCqHogVgBjEbjxy/PgBbGpZKLRkPxAbQIUuAPEHXOqZsRhwX05WVhCIHzx68gSnRqB8O5AKQBKSAGIPoPhFoL4HBIMOaNF5JFcuAOKF6MEBVOMA9Q0ukAjUs4BQYkhECoIEkIFAg/dDDYeBfAIh2w9Ur0BMqkMPMgeohfOhBgQQsAiWSPAGHcig+3gMeQBNZYTAA2jogCy1Z8SRokAung9VRCkAWRiIK+guQBVQCj5AzalnITKOyAWg1HoQlHoZCWRIUBD2kxmEG4BJPJBgWQdUBPM2ufG0EaVkALkcmJN/YFMJyuHAnM4IzcAcpAQZ0KGF6PkoAGhZAzSosAUfP4m+AoVEINYiCGQRNLeDIu8iVE6fiIyJzRJHoG8u4CzrgJYlUBDxsBQWCI1b/PURtFSoh5ZxxIIL0HpoA8kVH1J55g9NCAJowXMBmj82YAsmrBaNtoIGvUUAAQYApBd2hzrzVVQAAAAASUVORK5CYII%3D";
    this.jp = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NDJBOEJGMUUyN0IyMTFFMTlFOTNFMjNDNDUxOUFGMTciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NDJBOEJGMUYyN0IyMTFFMTlFOTNFMjNDNDUxOUFGMTciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo0MkE4QkYxQzI3QjIxMUUxOUU5M0UyM0M0NTE5QUYxNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo0MkE4QkYxRDI3QjIxMUUxOUU5M0UyM0M0NTE5QUYxNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pj8crNUAAAFxSURBVHjavFbNbYMwGDU0A7BA2oxAj5EqlU7QZgKSY4+ZgDJBmgmAY09JN8ihUo7NBqVVBmCD9H3qc4UsnCBi8qQnGwN+fL/GU8TdePyCIQZHyg1KsPjYbmVf5VEkwzBV/SCH2MyjJYnqF6lPd/WN2HcYk2O4hMYfJEaHSwj5l7JocOTeBgzAd84j8J6jM6E5U16EQq69go8uXZeDO4po6DpLXQoVYNWwHlrWOwuFaBk79qomMRseyNbpLQK34BOYca1i3BaGS/+Bj9N989A2GaSKv8AlNw8Ys1WvBStfimfEZZ82K2yo732yYPHwlDGbnZMMTRbJZmvOA+06iM1tlnWJUcXMyYwMi7BBxHt5l0PSdF1qdAMztSUTv120oNJSP6rmyvhU4NtYlNB9TYHfsKmOulpU1l7WwZYamtQ69Q3nXU/KcsDelhgFu3B8HBU6JVcMdB9YI/UnVzL72e/frodDj9YEDn8glxB5lotfAQYAtCJqk8z+2M8AAAAASUVORK5CYII%3D";
    this.kp = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Q0FBOEM3Q0EyOTQ4MTFFMUFDMjBDMDlDMDQxRTYzMzkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Q0FBOEM3Q0IyOTQ4MTFFMUFDMjBDMDlDMDQxRTYzMzkiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpBMENEMDM3NTI5NDgxMUUxQUMyMEMwOUMwNDFFNjMzOSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpBMENEMDM3NjI5NDgxMUUxQUMyMEMwOUMwNDFFNjMzOSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Ptz3FgYAAAErSURBVHjaYmQAAhtLSwEgVQ/ECUAMYlMDfADiBUDceOT48Q+MUEv2A7EBA23ABSB2ZJaTlW0HMgIYaAckgJiDCRpctAYJLFjiBBS2E4GYn4pxJsCCRdAQGHkPoIlkIzT+KAZM6L6BWQICQPYBaoUdukUCQF/A4wzILqCWRaDk/R9HkmSgZpJnwiFuQKIlFwgpwEgMwHhhRObDfIxDvBAoPgFJDBTs/dhSKhMFoZGIbAnUMaAixxGaRahjEchQoA8MgNgBTfwCtIyjjkVAC0BBdB6Uz4Bs9Ly2kZpBh5z0HQglDiZaFGygaoEuFpGSj0YtGoEWgUrv91Rs+eBsETFhKy5oABaALGokppinsLnVyPzoyZMfwCbXSlCTCIg1oDS1GpAzoKX8B4AAAwAuBFgKFwVWUgAAAABJRU5ErkJggg%3D%3D";
    this.lp = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gEfAAUcuIwRjAAAAVpJREFUSMftlrtKA0EUhr9ZFkEMCCpYCb6AIGJzdF7AUhRsREF9AQmCl1IhgpjGwkohb+Ab2Ew4ldZik8pOVOy8kNhMYAhBd5PZVB5Y2BnO8O3M/5+zYwCsyA6wD0wALeKEAZ6BY6daM1ZkA6hRbGwmQJniYy8FRnMsePVHOwSUcqwbSfJo4lTHnOo4sJx3S0mOXA3eh4sEHVmRnkVKM+adONXbDutGBT0CW0613mX+FGgGc4f9gK6AehdTPAAH7bEVMX+BkgxOy+LGVr9Ht2ZFZoDrUCMrMusLvRlLozn/OCA0wxSwXpS9+4p/UDu+iwJ12vetKFAp7HNOVYE7P/wC7oFqjF634FSrQR3hVOfDBCuyHWNHK1ZkMYCEgEy6GSvSAKYzAs+BS+AJ+PD/pUlgCbj45cMbac6WX+71jpEALwMoo/cEqAwAVDFe0FXgzN9uYsYnsOtUb34AitxcDYrQdlwAAAAASUVORK5CYII%3D";
    this.ip = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NDJBOEJGMTYyN0IyMTFFMTlFOTNFMjNDNDUxOUFGMTciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NDJBOEJGMTcyN0IyMTFFMTlFOTNFMjNDNDUxOUFGMTciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDNTQyQTc3NTI3QjExMUUxOUU5M0UyM0M0NTE5QUYxNyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDNTQyQTc3NjI3QjExMUUxOUU5M0UyM0M0NTE5QUYxNyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PkQAqvIAAADoSURBVHjaYmEAAhtLSwEgVQ/ECUAMYlMDfADiBUDceOT48Q+MUEv2A7EBA23ABSB2ZJaTlW0HMgIYaAckgJiDCRpctAYJTFSME3xAgIlCAw4AcSAoDoBYEBjpjCCMTSELJZYADXUkVjElPppIimIWCpMtHACzyXt88U22j4DB9gA9wmkVdCQBcixqxJaykFJcIb18JEAvi+SxCYIK1f9kJgZGtFT3f8gmhlGLRi2i3KIPdLDnAwu0SVRAqk4SM/oCkI8a0esWGjS3GpkfPXnyA9jkWglqEgGxBpSmVgNyBhAnghqQAAEGADc+O4K5UN0FAAAAAElFTkSuQmCC";
    this.ra = f.ra;
    window[this.ra.Gi].changeConfigSetting = this.Mm;
    this.aa.Ub = -1;
    this.ig = !0;
    this.pb = new ja;
    this.tc = new ka;
    this.Hn = new la;
    this.Lm = new ma;
    this.yp = new na;
    this.Mm = function() {};
    this.Sm = function(c) {
        var d = this;
        d.Ya = c;
        d.ra.Zb = "FlipView" == d.aa.ba && !(eb.browser.safari && 7 <= eb.browser.Gb && !eb.platform.touchdevice);
        d.ra.document.DisableOverflow || (d.Vb = d.aa.Yd ? jQuery("#" + d.Ya).wrap("<div id='" + d.Ya + "_wrap' style='" + (d.ra.Zb ? "position:absolute;z-index:50;" : "") + "opacity:0;text-align:center;width:100%;position:absolute;z-index:100;top:-70px'></div>").parent() : jQuery("#" + d.Ya).wrap("<div id='" + d.Ya + "_wrap' style='" + (d.ra.Zb ? "position:absolute;z-index:50;" : "") + "opacity:0;text-align:center;width:100%;'></div>").parent(), jQuery("#" + d.Ya).css("visibility", "hidden"), d.ra.PreviewMode = d.ra.config.document.PreviewMode, null != d.ra.config.document.UIConfig ? jQuery.ajax({
            type: "GET",
            url: null != d.ra.config.document.UIConfig ? d.ra.config.document.UIConfig : "UI_Zine.xml",
            dataType: "xml",
            error: function() {
                d.Ij();
            },
            success: function(c) {
                d.Jc = c;
                c = eb.platform.touchonlydevice ? "mobile" : "desktop";
                !eb.platform.Hb && eb.platform.touchonlydevice && 0 < jQuery(d.Jc).find("tablet").length && (c = "tablet");
                toolbar_el = jQuery(d.Jc).find(c).find("toolbar");
                var e = jQuery(d.Jc).find(c).find("general");
                d.readOnly = "true" == jQuery(e).attr("ReadOnly");
                d.backgroundColor = jQuery(e).attr("backgroundColor");
                d.linkColor = null != jQuery(e).attr("linkColor") ? jQuery(e).attr("linkColor") : "#72e6ff";
                d.ra.linkColor = d.linkColor;
                d.ge = null != jQuery(e).attr("linkAlpha") ? jQuery(e).attr("linkAlpha") : 0.4;
                d.ra.ge = d.ge;
                d.backgroundImage = jQuery(e).attr("backgroundImage");
                d.sp = null == jQuery(e).attr("stretchBackgroundImage") || null != jQuery(e).attr("stretchBackgroundImage") && "true" == jQuery(e).attr("stretchBackgroundImage");
                d.aa.Uf = null == jQuery(e).attr("enablePageShadows") || null != jQuery(e).attr("enablePageShadows") && "true" == jQuery(e).attr("enablePageShadows");
                d.Ta = ("true" == jQuery(e).attr("forceSinglePage") || (eb.platform.Hb || eb.platform.ios || eb.platform.android) && eb.browser.Ei || d.aa.gf || d.Wp) && !d.ra.PreviewMode;
                d.dc = jQuery(e).attr("panelColor");
                d.$e = null != jQuery(e).attr("arrowColor") ? jQuery(e).attr("arrowColor") : "#AAAAAA";
                d.Uj = jQuery(e).attr("backgroundAlpha");
                d.hg = jQuery(e).attr("navPanelBackgroundAlpha");
                d.Gk = jQuery(e).attr("imageAssets");
                d.Sf = !eb.platform.touchonlydevice && (null == jQuery(e).attr("enableFisheyeThumbnails") || jQuery(e).attr("enableFisheyeThumbnails") && "false" != jQuery(e).attr("enableFisheyeThumbnails")) && (!d.Ta || d.aa.gf);
                d.ig = "false" != jQuery(e).attr("navPanelsVisible");
                d.gn = "false" != jQuery(e).attr("firstLastButtonsVisible");
                d.We = null != jQuery(e).attr("zoomDragMode") && "false" != jQuery(e).attr("zoomDragMode");
                d.dr = null != jQuery(e).attr("hideNavPanels") && "false" != jQuery(e).attr("hideNavPanels");
                d.Vm = null != jQuery(e).attr("disableMouseWheel") && "false" != jQuery(e).attr("disableMouseWheel");
                d.Rf = null != jQuery(e).attr("disableZoom") && "false" != jQuery(e).attr("disableZoom");
                d.Gc = null != jQuery(e).attr("flipSpeed") ? jQuery(e).attr("flipSpeed").toLowerCase() : "medium";
                d.yb = d.yb && !d.Ta;
                d.Jm = null != jQuery(e).attr("bindBindNavigationKeys") && "false" != jQuery(e).attr("bindBindNavigationKeys");
                jQuery(d.toolbar.ea).css("visibility", "hidden");
                if (d.backgroundImage) {
                    d.sp ? (jQuery(d.ra.ia).css("background-color", ""), jQuery(d.ra.ia).css("background", ""), jQuery(d.ra.ka).css({
                        background: "url('" + d.backgroundImage + "')",
                        "background-size": "cover"
                    }), jQuery(d.ra.ia).css("background-size", "cover")) : (jQuery(d.ra.ia).css("background", ""), jQuery(d.ra.ka).css({
                        background: "url('" + d.backgroundImage + "')",
                        "background-color": d.backgroundColor
                    }), jQuery(d.ra.ia).css("background-size", ""), jQuery(d.ra.ia).css("background-position", "center"), jQuery(d.ra.ka).css("background-position", "center"), jQuery(d.ra.ia).css("background-repeat", "no-repeat"), jQuery(d.ra.ka).css("background-repeat", "no-repeat"));
                } else {
                    if (d.backgroundColor && -1 == d.backgroundColor.indexOf("[")) {
                        var f = Q(d.backgroundColor),
                            f = "rgb(" + f.r + "," + f.g + "," + f.b + ")";
                        jQuery(d.ra.ia).css("background", f);
                        jQuery(d.ra.ka).css("background", f);
                        d.ra.Zb || jQuery(d.Vb).css("background", f);
                    } else {
                        if (d.backgroundColor && 0 <= d.backgroundColor.indexOf("[")) {
                            var l = d.backgroundColor.split(",");
                            l[0] = l[0].toString().replace("[", "");
                            l[0] = l[0].toString().replace("]", "");
                            l[0] = l[0].toString().replace(" ", "");
                            l[1] = l[1].toString().replace("[", "");
                            l[1] = l[1].toString().replace("]", "");
                            l[1] = l[1].toString().replace(" ", "");
                            f = l[0].toString().substring(0, l[0].toString().length);
                            l = l[1].toString().substring(0, l[1].toString().length);
                            jQuery(d.ra.ia).css("background", "");
                            jQuery(d.ra.ka).css({
                                background: "linear-gradient(" + f + ", " + l + ")"
                            });
                            jQuery(d.ra.ka).css({
                                background: "-webkit-linear-gradient(" + f + ", " + l + ")"
                            });
                            eb.browser.msie && 10 > eb.browser.version && (jQuery(d.ra.ia).css("filter", "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorStr='" + f + "', endColorStr='" + l + "');"), jQuery(d.ra.ka).css("filter", "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorStr='" + f + "', endColorStr='" + l + "');"));
                        } else {
                            jQuery(d.ra.ka).css("background-color", "#222222");
                        }
                    }
                }
                d.Kj();
                jQuery(d.toolbar.ea).children().css("display", "none");
                d.Lh = d.ua;
                d.Mh = d.ua;
                d.Fh = d.ua;
                d.wg = d.ua;
                d.Bg = d.ua;
                d.Oh = d.ua;
                d.Cg = d.ua;
                d.Ph = d.ua;
                d.Dg = d.ua;
                d.Qh = d.ua;
                d.Eg = d.ua;
                d.Rh = d.ua;
                d.yg = d.ua;
                d.Hh = d.ua;
                d.Ag = d.ua;
                d.Jh = d.ua;
                d.zg = d.ua;
                d.Ih = d.ua;
                d.xg = d.ua;
                d.Gh = d.ua;
                var n = "",
                    v = null,
                    f = 0;
                jQuery(toolbar_el).attr("visible") && "false" == jQuery(toolbar_el).attr("visible") ? d.Fl = !1 : d.Fl = !0;
                !jQuery(toolbar_el).attr("width") || null != jQuery(toolbar_el).attr("width") && 0 <= jQuery(toolbar_el).attr("width").indexOf("%") ? jQuery(d.toolbar.ea).css("width", null) : jQuery(toolbar_el).attr("width") && jQuery(d.toolbar.ea).css("width", parseInt(jQuery(toolbar_el).attr("width")) + 60 + "px");
                jQuery(toolbar_el).attr("backgroundColor") && jQuery(d.toolbar.ea).css("background-color", jQuery(toolbar_el).attr("backgroundColor"));
                jQuery(toolbar_el).attr("borderColor") && jQuery(d.toolbar.ea).css("border-color", jQuery(toolbar_el).attr("borderColor"));
                jQuery(toolbar_el).attr("borderStyle") && jQuery(d.toolbar.ea).css("border-style", jQuery(toolbar_el).attr("borderStyle"));
                jQuery(toolbar_el).attr("borderThickness") && jQuery(d.toolbar.ea).css("border-width", jQuery(toolbar_el).attr("borderThickness"));
                jQuery(toolbar_el).attr("paddingTop") && (jQuery(d.toolbar.ea).css("padding-top", jQuery(toolbar_el).attr("paddingTop") + "px"), f += parseFloat(jQuery(toolbar_el).attr("paddingTop")));
                jQuery(toolbar_el).attr("paddingLeft") && jQuery(d.toolbar.ea).css("padding-left", jQuery(toolbar_el).attr("paddingLeft") + "px");
                jQuery(toolbar_el).attr("paddingRight") && jQuery(d.toolbar.ea).css("padding-right", jQuery(toolbar_el).attr("paddingRight") + "px");
                jQuery(toolbar_el).attr("paddingBottom") && (jQuery(d.toolbar.ea).css("padding-bottom", jQuery(toolbar_el).attr("paddingBottom") + "px"), f += parseFloat(jQuery(toolbar_el).attr("paddingTop")));
                jQuery(toolbar_el).attr("cornerRadius") && jQuery(d.toolbar.ea).css({
                    "border-radius": jQuery(toolbar_el).attr("cornerRadius") + "px",
                    "-moz-border-radius": jQuery(toolbar_el).attr("cornerRadius") + "px"
                });
                jQuery(toolbar_el).attr("height") && jQuery(d.toolbar.ea).css("height", parseFloat(jQuery(toolbar_el).attr("height")) - f + "px");
                jQuery(toolbar_el).attr("location") && "float" == jQuery(toolbar_el).attr("location") && (d.uh = !0);
                jQuery(toolbar_el).attr("location") && "bottom" == jQuery(toolbar_el).attr("location") && (d.rh = !0, jQuery(d.toolbar.ea).parent().detach().insertAfter(d.ia), jQuery(d.toolbar.ea).css("margin-top", "15px"), jQuery(d.toolbar.ea + "_wrap").css("bottom", "0px"), jQuery(jQuery(d.aa.ia).css("height", jQuery(d.aa.ia).height() - 40 + "px")));
                var u = 1 < eb.platform.pd && !eb.platform.touchonlydevice ? "@2x" : "";
                jQuery(jQuery(d.Jc).find(c)).find("toolbar").find("element").each(function() {
                    "bttnPrint" != jQuery(this).attr("id") && "bttnDownload" != jQuery(this).attr("id") && "bttnTextSelect" != jQuery(this).attr("id") && "bttnHand" != jQuery(this).attr("id") && "barCursorTools" != jQuery(this).attr("id") || !d.readOnly || jQuery(this).attr("visible", !1);
                    "bttnDownload" != jQuery(this).attr("id") || d.aa.document.PDFFile || jQuery(this).attr("visible", !1);
                    "bttnDownload" == jQuery(this).attr("id") && d.ra.renderer.config.signature && 0 < d.ra.renderer.config.signature.length && jQuery(this).attr("visible", !1);
                    if (!jQuery(this).attr("visible") || "true" == jQuery(this).attr("visible")) {
                        switch (jQuery(this).attr("type")) {
                            case "button":
                                n = ".flowpaper_" + jQuery(this).attr("id");
                                jQuery(this).attr("paddingLeft") && jQuery(n).css("padding-left", jQuery(this).attr("paddingLeft") - 6 + "px");
                                if (0 == jQuery(n).length && (jQuery(d.toolbar.ea).append(String.format("<img id='{0}' class='{1} flowpaper_tbbutton'/>", jQuery(this).attr("id"), "flowpaper_" + jQuery(this).attr("id"))), jQuery(this).attr("onclick"))) {
                                    var c = jQuery(this).attr("onclick");
                                    jQuery(n).bind("mousedown", function() {
                                        eval(c);
                                    });
                                }
                                var e = jQuery(this).attr("id");
                                jQuery(this).attr("src") && (e = jQuery(this).attr("src"));
                                jQuery(n).load(function() {
                                    jQuery(this).css("display", "block");
                                });
                                jQuery(n).attr("src", d.Gk + e + u + ".png");
                                jQuery(this).attr("icon_width") && jQuery(n).css("width", jQuery(this).attr("icon_width") + "px");
                                jQuery(this).attr("icon_height") && jQuery(n).css("height", jQuery(this).attr("icon_height") + "px");
                                jQuery(this).attr("paddingRight") && jQuery(n).css("padding-right", jQuery(this).attr("paddingRight") - 6 + "px");
                                jQuery(this).attr("paddingTop") && jQuery(n).css("padding-top", jQuery(this).attr("paddingTop") + "px");
                                d.uh ? jQuery(n).css("margin-top", "0px") : jQuery(n).css("margin-top", "2px");
                                null != v && jQuery(n).insertAfter(v);
                                v = jQuery(n);
                                break;
                            case "separator":
                                n = "#" + d.toolbar.Ya + "_" + jQuery(this).attr("id");
                                jQuery(n).css("display", "block");
                                jQuery(n).attr("src", d.Gk + "/bar" + u + ".png");
                                jQuery(this).attr("width") && jQuery(n).css("width", jQuery(this).attr("width") + "px");
                                jQuery(this).attr("height") && jQuery(n).css("height", jQuery(this).attr("height") + "px");
                                jQuery(this).attr("paddingLeft") && jQuery(n).css("padding-left", +jQuery(this).attr("paddingLeft"));
                                jQuery(this).attr("paddingRight") && jQuery(n).css("padding-right", +jQuery(this).attr("paddingRight"));
                                jQuery(this).attr("paddingTop") && jQuery(n).css("padding-top", +jQuery(this).attr("paddingTop"));
                                jQuery(n).css("margin-top", "0px");
                                null != v && jQuery(n).insertAfter(v);
                                v = jQuery(n);
                                break;
                            case "slider":
                                n = ".flowpaper_" + jQuery(this).attr("id");
                                jQuery(n).css("display", "block");
                                jQuery(this).attr("width") && jQuery(n).css("width : " + jQuery(this).attr("width"));
                                jQuery(this).attr("height") && jQuery(n).css("height : " + jQuery(this).attr("height"));
                                jQuery(this).attr("paddingLeft") && jQuery(n).css("padding-left : " + jQuery(this).attr("paddingLeft"));
                                jQuery(this).attr("paddingRight") && jQuery(n).css("padding-right : " + jQuery(this).attr("paddingRight"));
                                jQuery(this).attr("paddingTop") && jQuery(n).css("padding-top : " + jQuery(this).attr("paddingTop"));
                                d.uh ? jQuery(n).css("margin-top", "-5px") : jQuery(n).css("margin-top", "-3px");
                                null != v && jQuery(n).insertAfter(v);
                                v = jQuery(n);
                                break;
                            case "textinput":
                                n = ".flowpaper_" + jQuery(this).attr("id");
                                jQuery(n).css("display", "block");
                                jQuery(this).attr("width") && jQuery(n).css("width : " + jQuery(this).attr("width"));
                                jQuery(this).attr("height") && jQuery(n).css("height : " + jQuery(this).attr("height"));
                                jQuery(this).attr("paddingLeft") && jQuery(n).css("padding-left : " + jQuery(this).attr("paddingLeft"));
                                jQuery(this).attr("paddingRight") && jQuery(n).css("padding-right : " + jQuery(this).attr("paddingRight"));
                                jQuery(this).attr("paddingTop") && jQuery(n).css("padding-top : " + jQuery(this).attr("paddingTop"));
                                jQuery(this).attr("readonly") && "true" == jQuery(this).attr("readonly") && jQuery(n).attr("disabled", "disabled");
                                null != v && jQuery(n).insertAfter(v);
                                eb.platform.touchonlydevice ? jQuery(n).css("margin-top", jQuery(this).attr("marginTop") ? jQuery(this).attr("marginTop") + "px" : "7px") : d.uh ? jQuery(n).css("margin-top", "-2px") : jQuery(n).css("margin-top", "0px");
                                v = jQuery(n);
                                break;
                            case "label":
                                n = ".flowpaper_" + jQuery(this).attr("id"), jQuery(n).css("display", "block"), jQuery(this).attr("width") && jQuery(n).css("width : " + jQuery(this).attr("width")), jQuery(this).attr("height") && jQuery(n).css("height : " + jQuery(this).attr("height")), jQuery(this).attr("paddingLeft") && jQuery(n).css("padding-left : " + jQuery(this).attr("paddingLeft")), jQuery(this).attr("paddingRight") && jQuery(n).css("padding-right : " + jQuery(this).attr("paddingRight")), jQuery(this).attr("paddingTop") && jQuery(n).css("padding-top : " + jQuery(this).attr("paddingTop")), null != v && jQuery(n).insertAfter(v), eb.platform.touchonlydevice ? jQuery(n).css("margin-top", jQuery(this).attr("marginTop") ? jQuery(this).attr("marginTop") + "px" : "9px") : d.uh ? jQuery(n).css("margin-top", "1px") : jQuery(n).css("margin-top", "3px"), v = jQuery(n);
                        }
                    }
                });
                d.ra.outline = jQuery(jQuery(d.Jc).find("outline"));
                d.ra.labels = jQuery(jQuery(d.Jc).find("labels"));
                jQuery(d.toolbar.ea).css({
                    "margin-left": "auto",
                    "margin-right": "auto"
                });
                jQuery(toolbar_el).attr("location") && jQuery(toolbar_el).attr("location");
                jQuery(e).attr("glow") && "true" == jQuery(e).attr("glow") && (d.fq = !0, jQuery(d.toolbar.ea).css({
                    "box-shadow": "0 0 35px rgba(22, 22, 22, 1)",
                    "-webkit-box-shadow": "0 0 35px rgba(22, 22, 22, 1)",
                    "-moz-box-shadow": "0 0 35px rgba(22, 22, 22, 1)"
                }));
                d.dc ? jQuery(d.toolbar.ea).css("background-color", d.dc) : eb.platform.touchonlydevice ? !jQuery(toolbar_el).attr("gradients") || jQuery(toolbar_el).attr("gradients") && "true" == jQuery(toolbar_el).attr("gradients") ? jQuery(d.toolbar.ea).addClass("flowpaper_toolbarios_gradients") : jQuery(d.toolbar.ea).css("background-color", "#555555") : jQuery(d.toolbar.ea).css("background-color", "#555555");
                d.Fl ? jQuery(d.toolbar.ea).css("visibility", "visible") : jQuery(d.toolbar.ea).hide();
                jQuery(jQuery(d.Jc).find("content")).find("page").each(function() {
                    var c = jQuery(this);
                    jQuery(this).find("link").each(function() {
                        d.aa.addLink(jQuery(c).attr("number"), jQuery(this).attr("href"), jQuery(this).attr("x"), jQuery(this).attr("y"), jQuery(this).attr("width"), jQuery(this).attr("height"));
                    });
                    jQuery(this).find("video").each(function() {
                        d.aa.addVideo(jQuery(c).attr("number"), jQuery(this).attr("src"), jQuery(this).attr("url"), jQuery(this).attr("x"), jQuery(this).attr("y"), jQuery(this).attr("width"), jQuery(this).attr("height"), jQuery(this).attr("maximizevideo"));
                    });
                    jQuery(this).find("image").each(function() {
                        d.aa.addImage(jQuery(c).attr("number"), jQuery(this).attr("src"), jQuery(this).attr("x"), jQuery(this).attr("y"), jQuery(this).attr("width"), jQuery(this).attr("height"), jQuery(this).attr("href"), jQuery(this).attr("hoversrc"));
                    });
                });
                d.Jm && jQuery(window).bind("keydown", function(c) {
                    !c || Mouse.down || jQuery(c.target).hasClass("flowpaper_zoomSlider") || (d.ra.pages.Xd() || d.ra.pages.animating) && !d.Lg || ("37" == c.keyCode ? d.ra.previous() : "39" == c.keyCode && d.ra.next());
                });
            }
        }) : d.Ij(), d.ra.PreviewMode && (d.Dk(), d.Ug()));
    };
    this.Ug = function() {
        this.aa.ka.find(".flowpaper_fisheye").hide();
    };
    this.mj = function() {
        this.ak();
    };
    this.Dk = function() {
        jQuery(this.ra.ia).css("padding-top", "20px");
        jQuery("#" + this.Ya).hide();
    };
    this.Uo = function() {
        jQuery(this.ra.ia).css("padding-top", "0px");
        jQuery("#" + this.Ya).show();
    };
    this.Ij = function() {
        this.Ta = eb.platform.Hb && !this.ra.PreviewMode;
        this.We = !0;
        this.Sf = !eb.platform.touchonlydevice;
        this.hg = 1;
        this.aa.Uf = !0;
        jQuery(this.toolbar.ea).css({
            "border-radius": "3px",
            "-moz-border-radius": "3px"
        });
        jQuery(this.toolbar.ea).css({
            "margin-left": "auto",
            "margin-right": "auto"
        });
        this.ra.config.document.PanelColor && (this.dc = this.ra.config.document.PanelColor);
        this.ra.config.document.BackgroundColor ? this.backgroundColor = this.ra.config.document.BackgroundColor : this.backgroundColor = "#222222";
        this.backgroundImage || jQuery(this.ra.ka).css("background-color", this.backgroundColor);
        this.dc ? jQuery(this.toolbar.ea).css("background-color", this.dc) : eb.platform.touchonlydevice ? jQuery(this.toolbar.ea).addClass("flowpaper_toolbarios_gradients") : jQuery(this.toolbar.ea).css("background-color", "#555555");
        this.Kj();
    };
    this.Kj = function() {
        if (eb.platform.touchonlydevice) {
            var c = eb.platform.Hb ? -5 : -1,
                d = eb.platform.Hb ? 7 : 15,
                f = eb.platform.Hb ? 40 : 60;
            jQuery(this.toolbar.ea).html((this.toolbar.ra.config.document.ViewModeToolsVisible ? String.format("<img src='{0}' style='margin-left:{1}px' class='flowpaper_tbbutton_large flowpaper_twopage flowpaper_tbbutton_pressed flowpaper_bttnBookView flowpaper_viewmode'>", this.Fh, d) + String.format("<img src='{0}' class='flowpaper_bttnSinglePage flowpaper_tbbutton_large flowpaper_singlepage flowpaper_viewmode' style='margin-left:{1}px;'>", this.Bg, c) + String.format("<img src='{0}' style='margin-left:{1}px;' class='flowpaper_tbbutton_large flowpaper_thumbview flowpaper_bttnThumbView flowpaper_viewmode' >", this.Cg, c) + "" : "") + (this.toolbar.ra.config.document.ZoomToolsVisible ? String.format("<img class='flowpaper_tbbutton_large flowpaper_bttnZoomIn' src='{0}' style='margin-left:{1}px;' />", this.Dg, d) + String.format("<img class='flowpaper_tbbutton_large flowpaper_bttnZoomOut' src='{0}' style='margin-left:{1}px;' />", this.Eg, c) + String.format("<img class='flowpaper_tbbutton_large flowpaper_bttnFullscreen' src='{0}' style='margin-left:{1}px;' />", this.yg, c) + "" : "") + (this.toolbar.ra.config.document.NavToolsVisible ? String.format("<img src='{0}' class='flowpaper_tbbutton_large flowpaper_previous flowpaper_bttnPrevPage' style='margin-left:{0}px;'/>", this.Ag, d) + String.format("<input type='text' class='flowpaper_tbtextinput_large flowpaper_currPageNum flowpaper_txtPageNumber' value='1' style='width:{0}px;' />", f) + String.format("<div class='flowpaper_lblTotalPages flowpaper_tblabel_large flowpaper_numberOfPages'> / </div>") + String.format("<img src='{0}' class='flowpaper_bttnPrevNext flowpaper_tbbutton_large flowpaper_next'/>", this.zg) + "" : "") + (this.toolbar.ra.config.document.SearchToolsVisible ? String.format("<input type='text' class='flowpaper_txtSearch flowpaper_tbtextinput_large' style='margin-left:{1}px;width:130px;' />", d) + String.format("<img src='{0}' class='flowpaper_bttnFind flowpaper_find flowpaper_tbbutton_large' style=''/>", this.xg) + "" : ""));
            jQuery(this.toolbar.ea).removeClass("flowpaper_toolbarstd");
            jQuery(this.toolbar.ea).addClass("flowpaper_toolbarios");
            jQuery(this.toolbar.ea).parent().parent().css({
                "background-color": this.backgroundColor
            });
        } else {
            jQuery(this.toolbar.ea).css("margin-top", "15px"), c = this.ra.renderer.config.signature && 0 < this.ra.renderer.config.signature.length, jQuery(this.toolbar.ea).html(String.format("<img style='margin-left:10px;' src='{0}' class='flowpaper_bttnPrint flowpaper_tbbutton print'/>", this.gp) + (this.aa.document.PDFFile && 0 < this.aa.document.PDFFile.length && !c ? String.format("<img src='{0}' class='flowpaper_bttnDownload flowpaper_tbbutton download'/>", this.ap) : "") + String.format("<img src='{0}' id='{1}' class='flowpaper_tbseparator' />", this.Of, this.Gm) + (this.ra.config.document.ViewModeToolsVisible ? String.format("<img style='margin-left:10px;' src='{1}' class='flowpaper_tbbutton {0} flowpaper_bttnBookView flowpaper_twopage flowpaper_tbbuttonviewmode flowpaper_viewmode' />", "FlipView" == this.ra.Fb ? "flowpaper_tbbutton_pressed" : "", this.qp) + String.format("<img src='{1}' class='flowpaper_tbbutton {0} flowpaper_bttnSinglePage flowpaper_singlepage flowpaper_tbbuttonviewmode flowpaper_viewmode' />", "Portrait" == this.ra.Fb ? "flowpaper_tbbutton_pressed" : "", this.$o) + String.format("<img src='{0}' id='{1}' class='flowpaper_tbseparator' />", this.Of, this.Im) : "") + (this.ra.config.document.ZoomToolsVisible ? String.format("<div class='flowpaper_zoomSlider flowpaper_slider' style='background-image:url({1})'><div class='flowpaper_handle' style='{0}'></div></div>", eb.browser.msie && 9 > eb.browser.version ? this.aa.toolbar.yl : "", "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTIiPjxsaW5lIHgxPSIwIiB5MT0iNiIgeDI9Ijk1IiB5Mj0iNiIgc3R5bGU9InN0cm9rZTojQUFBQUFBO3N0cm9rZS13aWR0aDoxIiAvPjwvc3ZnPg==") + String.format("<input type='text' class='flowpaper_tbtextinput flowpaper_txtZoomFactor' style='width:40px;' />") + String.format("<img style='margin-left:10px;' class='flowpaper_tbbutton flowpaper_bttnFullscreen' src='{0}' />", this.cp) : "") + (this.ra.config.document.NavToolsVisible ? String.format("<img src='{0}' class='flowpaper_tbbutton flowpaper_previous flowpaper_bttnPrevPage'/>", this.fp) + String.format("<input type='text' class='flowpaper_txtPageNumber flowpaper_tbtextinput flowpaper_currPageNum' value='1' style='width:50px;text-align:right;' />") + String.format("<div class='flowpaper_lblTotalPages flowpaper_tblabel flowpaper_numberOfPages'> / </div>") + String.format("<img src='{0}' class='flowpaper_bttnPrevNext flowpaper_tbbutton flowpaper_next'/>", this.hp) + String.format("<img src='{0}' id='{1}' class='flowpaper_tbseparator' />", this.Of, this.Fm) : "") + (this.ra.config.document.CursorToolsVisible ? String.format("<img style='margin-top:5px;margin-left:6px;' src='{0}' class='flowpaper_tbbutton flowpaper_bttnTextSelect'/>", this.pp) + String.format("<img style='margin-top:4px;' src='{0}' class='flowpaper_tbbutton flowpaper_tbbutton_pressed flowpaper_bttnHand'/>", this.ep) + String.format("<img src='{0}' id='{1}' class='flowpaper_tbseparator' />", this.Of, this.Em) : "") + (this.ra.config.document.SearchToolsVisible ? String.format("<input id='{0}' type='text' class='flowpaper_tbtextinput flowpaper_txtSearch' style='width:40px;margin-left:4px' />") + String.format("<img src='{0}' class='flowpaper_find flowpaper_tbbutton flowpaper_bttnFind' />", this.rp) : "") + String.format("<img src='{0}' id='{1}' class='flowpaper_tbseparator' />", this.Of, this.Hm));
        }
    };
    this.bindEvents = function() {
        var c = this;
        eb.platform.touchonlydevice ? (jQuery(c.toolbar.ea).find(".flowpaper_bttnPrint").on("mousedown touchstart", function() {
            c.Mh != c.ua && jQuery(this).attr("src", c.Mh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnPrint").on("mouseup touchend", function() {
            c.Lh != c.ua && jQuery(this).attr("src", c.Lh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnBookView").on("mousedown touchstart", function() {
            c.wg != c.ua && jQuery(this).attr("src", c.wg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnBookView").on("mouseup touchend", function() {
            c.wg != c.ua && jQuery(this).attr("src", c.Fh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnSinglePage").on("mousedown touchstart", function() {
            c.Oh != c.ua && jQuery(this).attr("src", c.Oh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnSinglePage").on("mouseup touchend", function() {
            c.Bg != c.ua && jQuery(this).attr("src", c.Bg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnThumbView").on("mousedown touchstart", function() {
            c.Ph != c.ua && jQuery(this).attr("src", c.Ph);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnThumbView").on("mouseup touchend", function() {
            c.Cg != c.ua && jQuery(this).attr("src", c.Cg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnZoomIn").on("mousedown touchstart", function() {
            c.Qh != c.ua && jQuery(this).attr("src", c.Qh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnZoomIn").on("mouseup touchend", function() {
            c.Dg != c.ua && jQuery(this).attr("src", c.Dg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnZoomOut").on("mousedown touchstart", function() {
            c.Rh != c.ua && jQuery(this).attr("src", c.Rh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnZoomOut").on("mouseup touchend", function() {
            c.Eg != c.ua && jQuery(this).attr("src", c.Eg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnFullscreen").on("mousedown touchstart", function() {
            c.Hh != c.ua && jQuery(this).attr("src", c.Hh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnFullscreen").on("mouseup touchend", function() {
            c.yg != c.ua && jQuery(this).attr("src", c.yg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnPrevPage").on("mousedown touchstart", function() {
            c.Jh != c.ua && jQuery(this).attr("src", c.Jh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnPrevPage").on("mouseup touchend", function() {
            c.Ag != c.ua && jQuery(this).attr("src", c.Ag);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnNextPage").on("mousedown touchstart", function() {
            c.Ih != c.ua && jQuery(this).attr("src", c.Ih);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnNextPage").on("mouseup touchend", function() {
            c.zg != c.ua && jQuery(this).attr("src", c.zg);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnFind").on("mousedown touchstart", function() {
            c.Gh != c.ua && jQuery(this).attr("src", c.Gh);
        }), jQuery(c.toolbar.ea).find(".flowpaper_bttnFind").on("mouseup touchend", function() {
            c.xg != c.ua && jQuery(this).attr("src", c.xg);
        })) : (jQuery(c.toolbar.ea).find(".flowpaper_txtSearch").on("focus", function() {
            40 >= jQuery(this).width() && (jQuery(c.toolbar.ea).animate({
                width: jQuery(c.toolbar.ea).width() + 60
            }, 100), jQuery(this).animate({
                width: jQuery(this).width() + 60
            }, 100));
        }), jQuery(c.toolbar.ea).find(".flowpaper_txtSearch").on("blur", function() {
            40 < jQuery(this).width() && (jQuery(c.toolbar.ea).animate({
                width: jQuery(c.toolbar.ea).width() - 60
            }, 100), jQuery(this).animate({
                width: 40
            }, 100));
        }));
        jQuery(c.toolbar.ea).find(".flowpaper_bttnZoomIn").bind("click", function() {
            c.ra.pages.fe(!0);
        });
        jQuery(c.toolbar.ea).find(".flowpaper_bttnZoomOut").bind("click", function() {
            c.ra.pages.dd();
        });
        0 == c.aa.ka.find(".flowpaper_socialsharedialog").length && c.aa.ka.prepend(String.format("<div id='modal-socialshare' class='modal-content flowpaper_socialsharedialog' style='overflow:hidden;'><font style='color:#000000;font-size:11px'><img src='{0}' align='absmiddle' />&nbsp;<b>{15}</b></font><div style='width:530px;height:307px;margin-top:5px;padding-top:5px;padding-left:5px;background-color:#ffffff;box-shadow: 0px 2px 10px #aaa'><div style='position:absolute;left:20px;top:42px;color:#000000;font-weight:bold;'>{6}</div><div style='position:absolute;left:177px;top:42px;color:#000000;font-weight:bold;'><hr size='1' style='width:350px'/></div><div style='position:absolute;left:20px;top:62px;color:#000000;font-weight:bold;'><select class='flowpaper_ddlSharingOptions'><option>{7}</option><option>{16}</option></select></div><div style='position:absolute;left:175px;top:62px;color:#000000;font-weight:bold;'><input type='text' readonly style='width:355px;' class='flowpaper_socialsharing_txtUrl' /></div><div style='position:absolute;left:20px;top:102px;color:#000000;font-weight:bold;'>{8}</div><div style='position:absolute;left:177px;top:107px;color:#000000;font-weight:bold;'><hr size='1' style='width:350px'/></div><div style='position:absolute;left:20px;top:118px;color:#000000;font-size:10px;'>{9}</div><div style='position:absolute;left:20px;top:148px;color:#000000;font-weight:bold;'><input type='text' style='width:139px;' value='&lt;{10}&gt;' class='flowpaper_txtPublicationTitle' /></div><div style='position:absolute;left:165px;top:146px;color:#000000;'><img src='{1}' class='flowpaper_socialshare_twitter' style='cursor:pointer;' /></div><div style='position:absolute;left:200px;top:146px;color:#000000;'><img src='{2}' class='flowpaper_socialshare_facebook' style='cursor:pointer;' /></div><div style='position:absolute;left:235px;top:146px;color:#000000;'><img src='{3}' class='flowpaper_socialshare_googleplus' style='cursor:pointer;' /></div><div style='position:absolute;left:270px;top:146px;color:#000000;'><img src='{4}' class='flowpaper_socialshare_tumblr' style='cursor:pointer;' /></div><div style='position:absolute;left:305px;top:146px;color:#000000;'><img src='{5}' class='flowpaper_socialshare_linkedin' style='cursor:pointer;' /></div><div style='position:absolute;left:20px;top:192px;color:#000000;font-weight:bold;'>{11}</div><div style='position:absolute;left:20px;top:208px;color:#000000;font-size:10px;'>{12}</div><div style='position:absolute;left:20px;top:228px;color:#000000;font-size:10px;'><input type='radio' name='InsertCode' class='flowpaper_radio_miniature' checked />&nbsp;{13}&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='InsertCode' class='flowpaper_radio_fullembed' />&nbsp;{14}</div><div style='position:absolute;left:20px;top:251px;color:#000000;font-size:10px;'><textarea class='flowpaper_txtEmbedCode' readonly style='width:507px;height:52px'></textarea></div></div></div>", c.np, c.mp, c.ip, c.jp, c.lp, c.kp, c.aa.toolbar.Fa(c.aa.toolbar.fb, "CopyUrlToPublication", "Copy URL to publication"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "DefaultStartPage", "Default start page"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "ShareOnSocialNetwork", "Share on Social Network"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "ShareOnSocialNetworkDesc", "You can easily share this publication to social networks. Just click on the appropriate button below."), c.aa.toolbar.Fa(c.aa.toolbar.fb, "SharingTitle", "Sharing Title"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "EmbedOnSite", "Embed on Site"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "EmbedOnSiteDesc", "Use the code below to embed this publication to your website."), c.aa.toolbar.Fa(c.aa.toolbar.fb, "EmbedOnSiteMiniature", "Linkable Miniature"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "EmbedOnSiteFull", "Full Publication"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "Share", "Share"), c.aa.toolbar.Fa(c.aa.toolbar.fb, "StartOnCurrentPage", "Start on current page")));
        c.aa.ka.find(".flowpaper_radio_miniature, .flowpaper_radio_fullembed, .flowpaper_ddlSharingOptions").on("change", function() {
            c.wh();
        });
        c.aa.ka.find(".flowpaper_txtPublicationTitle").on("focus", function(c) {
            -1 != jQuery(c.target).val().indexOf("Sharing Title") && jQuery(c.target).val("");
        });
        c.aa.ka.find(".flowpaper_txtPublicationTitle").on("blur", function(c) {
            0 == jQuery(c.target).val().length && jQuery(c.target).val("<Sharing Title>");
        });
        c.aa.ka.find(".flowpaper_txtPublicationTitle").on("keydown", function() {
            c.wh();
        });
        c.wh();
        jQuery(c.toolbar.ea).find(".flowpaper_bttnSocialShare").bind("click", function() {
            c.wh();
            jQuery("#modal-socialshare").css("background-color", "#dedede");
            jQuery("#modal-socialshare").smodal({
                minHeight: 350,
                minWidth: 550,
                appendTo: c.aa.ka
            });
            jQuery("#modal-socialshare").parent().css("background-color", "#dedede");
        });
        jQuery(c.toolbar.ea).find(".flowpaper_bttnBookView").bind("click", function() {
            eb.browser.msie && 8 >= eb.browser.version ? c.ra.switchMode("BookView", c.ra.getCurrPage()) : c.ra.switchMode("FlipView", c.ra.getCurrPage() + 1);
            jQuery(this).addClass("flowpaper_tbbutton_pressed");
        });
        c.aa.ka.find(".flowpaper_socialsharing_txtUrl, .flowpaper_txtEmbedCode").bind("focus", function() {
            jQuery(this).select();
        });
        c.aa.ka.find(".flowpaper_socialsharing_txtUrl, .flowpaper_txtEmbedCode").bind("mouseup", function() {
            return !1;
        });
        c.aa.ka.find(".flowpaper_socialshare_twitter").bind("mousedown", function() {
            window.open("https://twitter.com/intent/tweet?url=" + escape(c.Le(!1)) + "&text=" + escape(c.Qg()), "_flowpaper_exturl");
            c.aa.ia.trigger("onSocialMediaShareClicked", "Twitter");
        });
        c.aa.ka.find(".flowpaper_socialshare_facebook").bind("mousedown", function() {
            window.open("http://www.facebook.com/sharer.php?u=" + escape(c.Le(!1), "_flowpaper_exturl"));
            c.aa.ia.trigger("onSocialMediaShareClicked", "Facebook");
        });
        c.aa.ka.find(".flowpaper_socialshare_googleplus").bind("mousedown", function() {
            window.open("https://plus.google.com/share?url=" + escape(c.Le(!1)), "_flowpaper_exturl");
            c.aa.ia.trigger("onSocialMediaShareClicked", "GooglePlus");
        });
        c.aa.ka.find(".flowpaper_socialshare_tumblr").bind("mousedown", function() {
            window.open("http://www.tumblr.com/share/link?name=" + escape(c.Qg()) + "&url=" + escape(c.Le(!1)), "_flowpaper_exturl");
            c.aa.ia.trigger("onSocialMediaShareClicked", "Tumblr");
        });
        c.aa.ka.find(".flowpaper_socialshare_linkedin").bind("mousedown", function() {
            window.open("http://www.linkedin.com/shareArticle?mini=true&url=" + escape(c.Le(!1)) + "&title=" + escape(c.Qg()), "_flowpaper_exturl");
            c.aa.ia.trigger("onSocialMediaShareClicked", "LinkedIn");
        });
    };
    this.wh = function() {
        this.aa.ka.find(".flowpaper_txtEmbedCode").val('<iframe frameborder="0"  width="400" height="300"  title="' + this.Qg() + '" src="' + this.Le() + '" type="text/html" scrolling="no" marginwidth="0" marginheight="0"></iframe>');
        this.aa.ka.find(".flowpaper_socialsharing_txtUrl").val(this.Le(!1));
    };
    this.Qg = function() {
        return -1 == this.aa.ka.find(".flowpaper_txtPublicationTitle").val().indexOf("Sharing Title") ? this.aa.ka.find(".flowpaper_txtPublicationTitle").val() : "";
    };
    this.Le = function(c) {
        0 == arguments.length && (c = !0);
        var d = this.aa.ka.find(".flowpaper_ddlSharingOptions").prop("selectedIndex"),
            f = this.aa.ka.find(".flowpaper_radio_miniature").is(":checked");
        return window.location.href.toString().substring(0) + (0 < d ? "#page=" + this.aa.getCurrPage() : "") + (0 < d && f && c ? "&" : f && c ? "#" : "") + (f && c ? "PreviewMode=Miniature" : "");
    };
    this.initialize = function() {
        var c = this.aa;
        c.ca.yb = c.ca.Wh();
        c.ca.Lg = !1;
        eb.platform.ios && 8 > eb.platform.iosversion && (c.ca.yb = !1);
        if (!c.config.document.InitViewMode || c.config.document.InitViewMode && "Zine" == c.config.document.InitViewMode || "TwoPage" == c.config.document.InitViewMode || "Flip-SinglePage" == c.config.document.InitViewMode) {
            "Flip-SinglePage" != c.config.document.InitViewMode || (eb.platform.Hb || eb.platform.ios || eb.platform.android) && eb.browser.Ei || (c.gf = !0), c.Fb = "FlipView", c.config.document.MinZoomSize = 1, c.ba = c.Fb, "TwoPage" == c.ba && (c.ba = "FlipView"), c.scale = 1;
        }
        c.config.document.Rk = c.config.document.MinZoomSize;
        null === c.ka && (c.ka = jQuery("<div style='" + c.ia.attr("style") + ";margin-bottom;20px;overflow-x: hidden;overflow-y: hidden;' class='flowpaper_viewer_container'/>"), c.ka = c.ia.wrap(c.ka).parent(), c.ia.css({
            left: "0px",
            top: "0px",
            position: "relative",
            width: "100%",
            height: "100%"
        }).addClass("flowpaper_viewer"), eb.browser.safari && c.ia.css("-webkit-transform", "translateZ(0)"));
        jQuery(c.ia).bind("onCurrentPageChanged", function() {
            c.fisheye && c.Nm();
        });
    };
    this.xp = function(d) {
        eb.platform.touchonlydevice ? c.switchMode("SinglePage", d) : c.switchMode("Portrait", d);
    };
    FlowPaperViewer_HTML.prototype.Zk = function(c) {
        var d = this;
        if (d.Ub != c) {
            var f = (c - 20 + 1) / 2,
                m = f + 9 + 1,
                k = 1,
                l = null != d.ca.dc ? d.ca.dc : "#555555";
            d.fisheye.find(".flowpaper_fisheye_item").parent().parent().remove();
            0 > d.getTotalPages() - c && (m = m + (d.getTotalPages() - c) / 2 + (c - d.getTotalPages()) % 2);
            19 < c ? d.fisheye.find(".flowpaper_fisheye_panelLeft").animate({
                opacity: 1
            }, 150) : d.fisheye.find(".flowpaper_fisheye_panelLeft").animate({
                opacity: 0
            }, 150);
            c < d.getTotalPages() ? d.fisheye.find(".flowpaper_fisheye_panelRight").animate({
                opacity: 1
            }, 150) : d.fisheye.find(".flowpaper_fisheye_panelRight").animate({
                opacity: 0
            }, 150);
            for (i = f; i < m; i++) {
                d.ym(k), k++;
            }
            d.fisheye.find(".flowpaper_fisheye_item, .flowpaper_fisheye_panelLeft, .flowpaper_fisheye_panelRight").bind("mouseover", function() {
                if (!d.pages.animating && 0 != d.fisheye.css("opacity")) {
                    var c = (1 - Math.min(1, Math.max(0, 1 / d.mk))) * d.kk + d.Yb;
                    d.fisheye.css({
                        "z-index": 12,
                        "pointer-events": "auto"
                    });
                    jQuery(this).parent().parent().parent().find("span").css({
                        display: "none"
                    });
                    jQuery(this).parent().find("span").css({
                        display: "inline-block"
                    });
                    jQuery(this).parent().parent().parent().find("p").remove();
                    var e = jQuery(this).context.dataset && 1 == jQuery(this).context.dataset.pageindex ? d.Mg / 3 : 0;
                    jQuery(this).parent().find("span").after(String.format("<p style='width: 0;height: 0;border-left: 7px solid transparent;border-right: 7px solid transparent;border-top: 7px solid {0};margin-top:-35px;margin-left:{1}px;'></p>", l, c / 2 - 14 + e));
                }
            });
            d.fisheye.find(".flowpaper_fisheye_item").bind("mouseout", function(c) {
                d.pages.animating || 0 == d.fisheye.css("opacity") || (d.ki = c.pageX, d.li = c.pageY, d.ce = c.target, jQuery(d.ce).get(0), d.xl(), d.fisheye.css({
                    "z-index": 9,
                    "pointer-events": "none"
                }), jQuery(this).parent().find("span").css({
                    display: "none"
                }), jQuery(this).parent().find("p").remove());
            });
            d.fisheye.find("li").each(function() {
                jQuery(this).bind("mousemove", function(c) {
                    d.pages.animating || 0 < c.buttons || !d.fisheye.is(":visible") || (d.ce = c.target, d.ki = c.pageX, d.li = c.pageY, jQuery(d.ce).get(0), d.mi = !0, d.Xk());
                });
            });
            jQuery(d.pages.da + ", " + d.pages.da + "_parent, #" + d.ja).bind("mouseover", function() {
                if (d.fisheye && (d.fisheye.css({
                        "z-index": 9,
                        "pointer-events": "none"
                    }), (eb.browser.msie || eb.browser.safari && 5 > eb.browser.Gb) && d.ce)) {
                    d.ce = null;
                    var c = d.fisheye.find("a").find("canvas").data("origwidth"),
                        e = d.fisheye.find("a").find("canvas").data("origheight");
                    d.fisheye.find("li").each(function() {
                        jQuery(this).find("a").css({
                            height: e,
                            width: c,
                            top: d.Yb / 3
                        });
                        jQuery(this).find("a").find("canvas").css({
                            height: e,
                            width: c,
                            top: d.Yb / 3
                        });
                    });
                }
            });
        }
        d.Ub = c;
    };
    FlowPaperViewer_HTML.prototype.Nm = function() {
        (this.ta > this.Ub || this.ta <= this.Ub - 20) && -1 != this.Ub && this.Xg(this.ta > this.Ub ? 20 : -20);
    };
    FlowPaperViewer_HTML.prototype.Xg = function(c) {
        var d = this;
        0 != c && d.Zk(d.Ub + c);
        window.setTimeout(function() {
            d.Se = (d.Ub - 20 + 1) / 2 + 1;
            d.tj = d.Se + 9;
            0 > d.getTotalPages() - d.Ub && (d.tj = d.tj + (d.getTotalPages() - d.Ub) / 2 + (d.Ub - d.getTotalPages()) % 2);
            d.renderer.Je(d, d.Se, 2 * d.be);
        }, 300);
    };
    FlowPaperViewer_HTML.prototype.ym = function(c) {
        var d = 0 == i ? 1 : 2 * i + 1,
            f = this;
        if (f.fisheye) {
            var m = null != f.ca.dc ? f.ca.dc : "#555555",
                k = "";
            1 == d ? k = "&nbsp;&nbsp;" + c + "&nbsp;&nbsp;" : d == f.getTotalPages() && 0 == f.getTotalPages() % 2 ? k = (d - 1).toString() : k = d - 1 + "-" + d;
            c = jQuery(String.format("<li><a style='height:{2}px;width:{7}px;top:{9}px;' class='flowpaper_thumbitem'><span style='margin-left:{8}px;background-color:{0}'>{4}</span><canvas data-pageIndex='{5}' data-ThumbIndex='{6}' class='flowpaper_fisheye_item' style='pointer-events: auto;' /></a></li>", m, f.ef, 0.8 * f.be, f.Mg, k, d, c, f.Yb, 1 == d ? f.Mg : 0, f.Yb / 3));
            c.insertBefore(f.fisheye.find(".flowpaper_fisheye_panelRight").parent());
            c.find(".flowpaper_fisheye_item").css({
                opacity: 0
            });
            jQuery(c).bind("mousedown", function() {
                1 != !f.scale && (f.fisheye && f.fisheye.css({
                    "z-index": 9,
                    "pointer-events": "none"
                }), d > f.getTotalPages() && (d = f.getTotalPages()), f.gotoPage(d));
            });
        }
    };
    this.ak = function() {
        var c = this.aa;
        0 < c.ka.find(".flowpaper_fisheye").length && c.ka.find(".flowpaper_fisheye").remove();
        c.Ub = -1;
        var d = 0;
        0 < c.getDimensions(0).length && (d = c.getDimensions(0)[0].Ca / c.getDimensions(0)[0].Na - 0.3);
        c.Pq = 25;
        c.be = 0.25 * c.ia.height();
        c.Mg = 0.41 * c.be;
        c.ef = jQuery(c.ia).offset().top + jQuery(c.pages.da).height() - c.ka.offset().top + c.gc;
        c.mk = 1.25 * c.be;
        c.Yb = c.be / (3.5 - d);
        c.ln = 2.5 * c.Yb;
        c.mn = -(c.Yb / 3);
        d = null != c.ca.dc ? c.ca.dc : "#555555";
        c.ca.hg && (d = Q(d), d = "rgba(" + d.r + "," + d.g + "," + d.b + "," + c.ca.hg + ")");
        c.ka.append(jQuery(String.format("<div class='flowpaper_fisheye' style='position:absolute;pointer-events: none;top:{1}px;z-index:12;left:{4}px;'><ul><li><div class='flowpaper_fisheye_panelLeft' style='pointer-events: auto;position:relative;-moz-border-radius-topleft: 10px;border-top-left-radius: 10px;-moz-border-radius-bottomleft: 10px;border-bottom-left-radius: 10px;background-color:{0};left:0px;width:22px;'><div style='position:absolute;height:100px;width:100px;left:0px;top:-40px;'></div><div class='flowpaper_fisheye_leftArrow' style='position:absolute;top:20%;left:3px'></div></div></li><li><div class='flowpaper_fisheye_panelRight' style='pointer-events: auto;position:relative;-moz-border-radius-topright: 10px;border-top-right-radius: 10px;-moz-border-radius-bottomright: 10px;border-bottom-right-radius: 10px;background-color:{0};left:0px;width:22px;'><div style='position:absolute;height:100px;width:100px;left:0px;top:-40px;'></div><div class='flowpaper_fisheye_rightArrow' style='position:absolute;top:20%;left:3px;'></div></div></li></ul></div>", d, c.ef, 0.8 * c.be, c.Mg, c.mn)));
        c.fisheye = c.ka.find(".flowpaper_fisheye");
        c.fisheye.css({
            top: c.ef - (c.fisheye.find(".flowpaper_fisheye_panelLeft").offset().top - jQuery(c.fisheye).offset().top) + c.fisheye.find(".flowpaper_fisheye_panelLeft").height() / 2
        });
        c.kk = c.ln - c.Yb;
        c.ki = -1;
        c.li = -1;
        c.ji = !1;
        c.mi = !1;
        c.Vf = c.Yb - 0.4 * c.Yb;
        c.Oq = c.Vf / c.Yb;
        c.fisheye.find(".flowpaper_fisheye_panelLeft").bind("mousedown", function() {
            c.Xg(-20);
        });
        c.fisheye.find(".flowpaper_fisheye_panelRight").bind("mousedown", function() {
            c.Xg(20);
        });
        36 < c.Vf && (c.Vf = 36);
        c.fisheye.find(".flowpaper_fisheye_panelLeft").css({
            opacity: 0,
            height: c.Vf + "px",
            top: "-10px"
        });
        c.fisheye.find(".flowpaper_fisheye_panelRight").css({
            height: c.Vf + "px",
            top: "-10px"
        });
        c.fisheye.css({
            top: c.ef - (c.fisheye.find(".flowpaper_fisheye_panelLeft").offset().top - jQuery(c.fisheye).offset().top) + c.fisheye.find(".flowpaper_fisheye_panelLeft").height() / 3
        });
        c.jk = 30 < c.fisheye.find(".flowpaper_fisheye_panelLeft").height() ? 11 : 0.35 * c.fisheye.find(".flowpaper_fisheye_panelLeft").height();
        c.fisheye.find(".flowpaper_fisheye_leftArrow").aj(c.jk, c.ca.$e ? c.ca.$e : "#AAAAAA");
        c.fisheye.find(".flowpaper_fisheye_rightArrow").jh(c.jk, c.ca.$e ? c.ca.$e : "#AAAAAA");
        jQuery(c).unbind("onThumbPanelThumbAdded");
        jQuery(c).bind("onThumbPanelThumbAdded", function(d, g) {
            var f = c.fisheye.find(String.format('*[data-thumbIndex="{0}"]', g.Te));
            f.data("pageIndex");
            var l = (g.Te - 1) % 10;
            f && f.animate({
                opacity: 1
            }, 300);
            c.Se < c.tj && (c.Ub - 20 + 1) / 2 + l + 2 > c.Se && (c.Ap ? (c.Se++, c.Ap = !1) : c.Se = (c.Ub - 20 + 1) / 2 + l + 2, c.renderer.Je(c, c.Se, 2 * c.be));
            0 == l && f.height() - 10 < c.fisheye.find(".flowpaper_fisheye_panelRight").height() && (c.fisheye.find(".flowpaper_fisheye_panelLeft").css("top", c.fisheye.find(".flowpaper_fisheye_panelLeft").height() - f.height() + 5 + "px"), c.fisheye.find(".flowpaper_fisheye_panelLeft").height(c.fisheye.find(".flowpaper_fisheye_panelLeft").height() - 3), c.fisheye.find(".flowpaper_fisheye_panelRight").css("top", c.fisheye.find(".flowpaper_fisheye_panelRight").height() - f.height() + 5 + "px"), c.fisheye.find(".flowpaper_fisheye_panelRight").height(c.fisheye.find(".flowpaper_fisheye_panelRight").height() - 3));
        });
        c.Zk(19);
        c.PreviewMode || c.Xg(0);
        1 != c.scale && c.fisheye.animate({
            opacity: 0
        }, 0);
    };
    this.hh = function() {
        if ("FlipView" == c.ba && window.zine) {
            c.gc = c.Zb && !c.ca.rh ? c.ca.Vb.height() : 0;
            c.Yd && c.Zb && (c.gc = 5);
            c.document.StartAtPage && !c.vg && (c.vg = 0 != c.document.StartAtPage % 2 ? c.document.StartAtPage - 1 : c.document.StartAtPage);
            c.Gf = !1;
            var d = 1400;
            "very fast" == c.ca.Gc && (d = 300);
            "fast" == c.ca.Gc && (d = 700);
            "slow" == c.ca.Gc && (d = 2300);
            "very slow" == c.ca.Gc && (d = 6300);
            c.Hl = 600;
            c.Ea = jQuery(c.pages.da).turn({
                gradients: !eb.platform.android,
                acceleration: !0,
                elevation: 50,
                duration: d,
                page: c.vg ? c.vg : 1,
                display: c.ca.Ta ? "single" : "double",
                pages: c.getTotalPages(),
                cornerDragging: c.document.EnableCornerDragging,
                disableCornerNavigation: c.ca.yb,
                when: {
                    turning: function(d, e) {
                        c.pages.animating = !0;
                        c.pages.Bf = null;
                        c.pages.la = 0 == e % 2 ? e + 1 : e;
                        if (1 != e || c.ca.Ta) {
                            c.ca.Ta ? c.ca.Ta && c.gc && jQuery(c.pages.da + "_parent").transition({
                                x: 0,
                                y: c.gc
                            }, 0) : jQuery(c.pages.da + "_parent").transition({
                                x: 0,
                                y: c.gc
                            }, c.Hl, "ease", function() {});
                        } else {
                            var g = c.Gf ? c.Hl : 0;
                            jQuery(c.pages.da + "_parent").transition({
                                x: -(c.pages.cd() / 4),
                                y: c.gc
                            }, g, "ease", function() {});
                        }
                        c.ta = 1 < e ? c.pages.la : e;
                        c.renderer.$d && c.Gf && c.pages.Ne(e - 1);
                        c.renderer.$d && c.Gf && c.pages.Ne(e);
                        "FlipView" == c.ba && (!c.pages.pages[e - 1] || c.pages.pages[e - 1].oc || c.pages.pages[e - 1].Ga || (c.pages.pages[e - 1].oc = !0, c.pages.pages[e - 1].sd()), e < c.getTotalPages() && c.pages.pages[e] && !c.pages.pages[e].oc && !c.pages.pages[e].Ga && (c.pages.pages[e].oc = !0, c.pages.pages[e].sd()));
                    },
                    turned: function(d, e) {
                        c.ca.yb && c.Ea ? c.pages.Xd() || (c.Ea.css({
                            opacity: 1
                        }), c.yf ? (c.Gf = !0, c.pages.animating = !1, c.bd(e), c.pages.ec(), c.ia.trigger("onCurrentPageChanged", e), null != c.Qd && (c.Qd(), c.Qd = null)) : jQuery("#" + c.pages.Ob).animate({
                            opacity: 0.5
                        }, {
                            duration: 50,
                            always: function() {
                                jQuery("#" + c.pages.Ob).animate({
                                    opacity: 0
                                }, {
                                    duration: 50,
                                    always: function() {
                                        jQuery("#" + c.pages.Ob).css("z-index", -1);
                                        c.Gf = !0;
                                        c.pages.animating = !1;
                                        c.bd(e);
                                        c.pages.ec();
                                        c.ia.trigger("onCurrentPageChanged", e);
                                        null != c.Qd && (c.Qd(), c.Qd = null);
                                    }
                                });
                            }
                        })) : (c.Gf = !0, c.pages.animating = !1, c.bd(e), c.pages.ec(), c.ia.trigger("onCurrentPageChanged", e), null != c.Qd && (c.Qd(), c.Qd = null));
                    },
                    pageAdded: function(d, e) {
                        var g = c.pages.getPage(e - 1);
                        g.Ln();
                        c.ca.tc.Kn(g);
                    },
                    foldedPageClicked: function(d, e) {
                        c.Zi || (c.pages.Xd() || c.pages.animating) && !c.ca.Lg || c.cb || c.Jb || requestAnim(function() {
                            window.clearTimeout(c.yf);
                            c.yf = null;
                            e >= c.pages.la && e < c.getTotalPages() ? c.pages.vj("next") : c.pages.vj("previous");
                        });
                    },
                    destroyed: function() {
                        c.Xm && c.ia.parent().remove();
                    }
                }
            });
            jQuery(c.Ea).bind("cornerActivated", function() {
                c.fisheye && c.fisheye.css({
                    "z-index": 9,
                    "pointer-events": "none"
                });
            });
            jQuery(c.ea).trigger("onScaleChanged", 1 / c.document.MaxZoomSize);
        }
        if (c.backgroundColor && -1 == c.backgroundColor.indexOf("[") && !this.backgroundImage) {
            d = Q(this.backgroundColor), d = "rgba(" + d.r + "," + d.g + "," + d.b + "," + (null != this.Uj ? parseFloat(this.Uj) : 1) + ")", jQuery(this.ra.ia).css("background", d), this.ra.Zb || jQuery(this.Vb).css("background", d);
        } else {
            if (c.backgroundColor && 0 <= c.backgroundColor.indexOf("[") && !this.backgroundImage) {
                var g = c.backgroundColor.split(",");
                g[0] = g[0].toString().replace("[", "");
                g[0] = g[0].toString().replace("]", "");
                g[0] = g[0].toString().replace(" ", "");
                g[1] = g[1].toString().replace("[", "");
                g[1] = g[1].toString().replace("]", "");
                g[1] = g[1].toString().replace(" ", "");
                d = g[0].toString().substring(0, g[0].toString().length);
                g = g[1].toString().substring(0, g[1].toString().length);
                jQuery(c.ra.ia).css("backgroundImage", "linear-gradient(top, " + d + ", " + g + ")");
            }
        }
        "FlipView" == c.ba && !eb.platform.touchonlydevice && c.ca.mj && c.ca.Sf ? (c.ca.ak(), c.PreviewMode && c.ca.Ug()) : (c.fisheye && (c.fisheye.remove(), c.fisheye = null), c.Ub = -1);
        FlowPaperViewer_HTML.prototype.distance = function(c, d, e, g) {
            c = e - c;
            d = g - d;
            return Math.sqrt(c * c + d * d);
        };
        FlowPaperViewer_HTML.prototype.turn = function(c) {
            var d = this,
                e = arguments[0],
                g = 2 == arguments.length ? arguments[1] : null;
            !d.ca.yb || "next" != e && "previous" != e || d.cb || d.Jb ? (jQuery("#" + d.pages.Ob).css("z-index", -1), d.Ea && (1 == arguments.length && d.Ea.turn(arguments[0]), 2 == arguments.length && d.Ea.turn(arguments[0], arguments[1]))) : !d.pages.Xd() && !d.pages.animating || d.ca.Lg ? requestAnim(function() {
                window.clearTimeout(d.yf);
                d.yf = null;
                d.pages.vj(e, g);
            }) : (window.clearTimeout(d.yf), d.yf = window.setTimeout(function() {
                d.turn(e, g);
            }, 500));
        };
        FlowPaperViewer_HTML.prototype.Xk = function() {
            var c = this;
            c.ji || (c.ji = !0, c.ik && window.clearTimeout(c.ik), c.ik = window.setTimeout(function() {
                c.kn(c);
            }, 40));
        };
        FlowPaperViewer_HTML.prototype.kn = function(c) {
            c.xl();
            c.ji = !1;
            c.mi && (c.mi = !1, c.Xk());
        };
        FlowPaperViewer_HTML.prototype.xl = function() {
            var c = this;
            c.fisheye.find("li").each(function() {
                var d = c.ce;
                if (!(eb.browser.msie || eb.browser.safari && 5 > eb.browser.Gb) || c.ce) {
                    if ("IMG" != jQuery(d).get(0).tagName && "DIV" != jQuery(d).get(0).tagName && "CANVAS" != jQuery(d).get(0).tagName) {
                        c.fisheye.find("li").each(function() {
                            var d = this;
                            requestAnim(function() {
                                jQuery(d).find("a").css({
                                    width: c.Yb,
                                    top: c.Yb / 3
                                });
                            }, 10);
                        });
                    } else {
                        var d = jQuery(this).offset().left + jQuery(this).outerWidth() / 2,
                            e = jQuery(this).offset().top + jQuery(this).outerHeight() / 2,
                            d = c.distance(d, e, c.ki, c.li),
                            g = (1 - Math.min(1, Math.max(0, d / c.mk))) * c.kk + c.Yb,
                            d = jQuery(this).find("a").find("canvas").data("origwidth"),
                            e = jQuery(this).find("a").find("canvas").data("origheight"),
                            f = g / d;
                        if (d && e) {
                            var v = this;
                            eb.browser.msie || eb.browser.safari && 5 > eb.browser.Gb ? (jQuery(this).find("a").animate({
                                height: e * f,
                                width: g,
                                top: g / 3
                            }, 0), jQuery(this).find("a").find("canvas").css({
                                height: e * f,
                                width: g,
                                top: g / 3
                            }), c.kr = c.ce) : requestAnim(function() {
                                jQuery(v).find("a").css({
                                    width: g,
                                    top: g / 3
                                });
                            }, 10);
                        }
                    }
                }
            });
        };
        jQuery(c.toolbar.ea).css("visibility", "visible");
        c.fisheye && c.fisheye.css({
            "z-index": 9,
            "pointer-events": "none"
        });
        c.ca.Vb.animate({
            opacity: 1
        }, 300);
    };
    this.dispose = function() {
        c.Ea.turn("destroy");
        delete c.Ea;
    };
    this.kg = function() {
        c.Ea = null;
    };
    this.switchMode = function(d, g) {
        c.Ea && c.Ea.turn("destroy");
        c.Ea = null;
        "Portrait" == d || "SinglePage" == d ? (c.Md = c.ia.height(), c.Md = c.Md - jQuery(c.ea).outerHeight() + 20, c.ia.height(c.Md)) : (c.vg = 0 != g % 2 ? g - 1 : g, c.Md = null, c.ia.css({
            left: "0px",
            top: "0px",
            position: "relative",
            width: "100%",
            height: "100%"
        }), c.Pj());
        "FlipView" == c.ba && "FlipView" != d && (c.config.document.MinZoomSize = 1, jQuery(c.pages.da).turn("destroy"), c.fisheye && c.fisheye.remove());
        c.pages.Rd && c.pages.Dd && c.pages.Dd();
        "FlipView" != d && c.config.document.Rk && (c.config.document.MinZoomSize = c.config.document.Rk);
        "FlipView" == d && (c.scale = 1, c.ba = "FlipView", c.ca.yb = c.ca.Wh());
    };
    this.Wh = function() {
        return c.config.document.EnableWebGL && !eb.platform.Hb && !eb.platform.android && !eb.browser.Ei && !c.ca.Ta && eb.browser.qb.Kp && "Flip-SinglePage" != c.config.document.InitViewMode && window.THREE;
    };
    this.gotoPage = function(d, g) {
        "FlipView" == c.ba && c.pages.Bn(d, g);
    };
    this.bd = function(d) {
        if ("FlipView" == c.ba) {
            1 < c.pages.la && 1 == c.scale ? jQuery(c.pages.da + "_panelLeft").animate({
                opacity: 1
            }, 100) : 1 == c.pages.la && jQuery(c.pages.da + "_panelLeft").animate({
                opacity: 0
            }, 100);
            if (c.pages.la <= c.getTotalPages() && 1.1 >= c.scale) {
                1 < c.getTotalPages() && jQuery(c.pages.da + "_panelRight").animate({
                    opacity: 1
                }, 100), c.fisheye && "1" != c.fisheye.css("opacity") && window.setTimeout(function() {
                    1.1 >= c.scale && (c.fisheye.show(), c.fisheye.animate({
                        opacity: 1
                    }, 100));
                }, 700);
            } else {
                if (1.1 < c.scale || c.pages.la + 2 >= c.getTotalPages()) {
                    jQuery(c.pages.da + "_panelRight").animate({
                        opacity: 0
                    }, 100), 1 == c.scale && 0 == c.getTotalPages() % 2 && c.pages.la - 1 <= c.getTotalPages() ? c.fisheye && (c.fisheye.show(), c.fisheye.animate({
                        opacity: 1
                    }, 100)) : c.fisheye && c.fisheye.animate({
                        opacity: 0
                    }, 0, function() {
                        c.fisheye.hide();
                    });
                }
            }
            eb.platform.touchonlydevice || (window.clearTimeout(c.Qn), c.Qn = setTimeout(function() {
                0 != parseInt(d) % 2 && (d = d - 1);
                var g = [d - 1];
                1 < d && parseInt(d) + 1 <= c.document.numPages && !c.Ta && g.push(d);
                for (var f = 0; f < g.length; f++) {
                    jQuery(".flowpaper_mark_link, .pdfPageLink_" + g[f]).stop(), jQuery(".flowpaper_mark_link, .pdfPageLink_" + g[f]).css({
                        background: c.linkColor,
                        opacity: c.ge
                    }), jQuery(".flowpaper_mark_link, .pdfPageLink_" + g[f]).animate({
                        opacity: 0
                    }, {
                        duration: 1700,
                        complete: function() {}
                    });
                }
            }, 100));
        }
    };
    this.pj = function() {
        this.aa.fisheye && (this.lk = this.aa.fisheye.css("margin-left"), this.aa.fisheye.animate({
            "margin-left": parseFloat(this.aa.fisheye.css("margin-left")) + 0.5 * this.aa.cb.width() + "px"
        }, 200));
    };
    this.To = function() {
        this.aa.fisheye && (this.lk = this.aa.fisheye.css("margin-left"), this.aa.fisheye.animate({
            "margin-left": parseFloat(this.aa.fisheye.css("margin-left")) + 0.5 * this.aa.Jb.width() + "px"
        }, 200));
    };
    this.rf = function() {
        this.aa.fisheye && this.aa.fisheye.animate({
            "margin-left": parseFloat(this.lk) + "px"
        }, 200);
    };
    this.resize = function(d, g, f, m) {
        c.gc = c.Zb ? c.ca.Vb.height() : 0;
        if ("FlipView" == c.ba && c.pages) {
            c.ia.css({
                width: d,
                height: g - 35
            });
            d = c.ia.width();
            g = c.ia.height();
            d - 5 < jQuery(document.body).width() && d + 5 > jQuery(document.body).width() && g + 37 - 5 < jQuery(document.body).height() && g + 37 + 5 > jQuery(document.body).height() ? (c.ka.css({
                width: "100%",
                height: "100%"
            }), c.ca.rh && jQuery(jQuery(c.ia).css("height", jQuery(c.ia).height() - 40 + "px"))) : null != f && 1 != f || c.ka.css({
                width: d,
                height: g + 37
            });
            c.pages.resize(d, g, m);
            c.fisheye && c.ia && (c.ef = jQuery(c.ia).offset().top + jQuery(c.pages.da).height() - jQuery(c.ka).offset().top + c.gc, c.fisheye.css({
                top: c.ef - (c.fisheye.find(".flowpaper_fisheye_panelLeft").offset().top - jQuery(c.fisheye).offset().top) + c.fisheye.find(".flowpaper_fisheye_panelLeft").height() / 2
            }), c.be = 0.25 * c.ia.height());
            for (d = 0; d < c.document.numPages; d++) {
                c.pages.kb(d) && (c.pages.pages[d].jl = !0, c.pages.pages[d].Ga = !1);
            }
            window.clearTimeout(c.Lp);
            c.Lp = setTimeout(function() {
                c.ec();
                c.pages.La();
            }, 350);
        }
    };
    this.setCurrentCursor = function() {};
};
window.FlowPaper_Resources = function(f) {
    this.aa = f;
    this.xa = {};
    this.xa.Mp = "";
    this.xa.nm = "";
    this.xa.jm = "";
    this.xa.Wl = "";
    this.xa.mm = "";
    this.xa.qm = "";
    this.xa.pm = "";
    this.xa.im = "";
    this.xa.hm = "";
    this.xa.$l = "";
    this.xa.Tl = "";
    this.xa.Ul = "";
    this.xa.om = "";
    this.xa.bm = "";
    this.xa.Zl = "";
    this.xa.lm = "";
    this.xa.$p = "";
    this.xa.bq = "";
    this.xa.cq = "";
    this.xa.iq = "";
    this.xa.gq = "";
    this.xa.mq = "";
    this.xa.nq = "";
    this.xa.oq = "";
    this.xa.lq = "";
    this.xa.pq = "";
    this.no = function() {
        var c = this.aa,
            d = !0,
            d = d = "",
            d = ["Z1n3d0ma1n"],
            d = d[0],
            d = c.resources.Nl(d);
        d || (d = ["d0ma1n"], d = d[0] + "#FlexPaper-1-4-5-Annotations-1.0.10", d = c.resources.Nl(d));
        d || alert("License key not accepted. Please check your configuration settings.");
        jQuery(".flowpaper_tbloader").hide();
        d && jQuery(this).trigger("onPostinitialized");
    };
    this.Nl = function(c) {
        var d = this.aa,
            e = null != d.config.key && 0 < d.config.key.length && 0 <= d.config.key.indexOf("@"),
            g = parseInt(Math.pow(6, 2)) + W(!0) + "AdaptiveUId0ma1n";
        c = ba(parseInt(Math.pow(9, 3)) + (e ? d.config.key.split("$")[0] : W(!0)) + c);
        var f = ba(g),
            g = "$" + c.substring(11, 30).toLowerCase();
        c = "$" + f.substring(11, 30).toLowerCase();
        f = W(!1);
        return validated = (0 == f.indexOf("http://localhost/") || 0 == f.indexOf("http://localhost:") || 0 == f.indexOf("http://localhost:") || 0 == f.indexOf("http://192.168.") || 0 == f.indexOf("http://127.0.0.1") || 0 == f.indexOf("https://localhost/") || 0 == f.indexOf("https://localhost:") || 0 == f.indexOf("https://localhost:") || 0 == f.indexOf("https://192.168.") || 0 == f.indexOf("https://127.0.0.1") || 0 == f.indexOf("http://10.1.1.") || 0 == f.indexOf("http://git.devaldi.com") || 0 == f.indexOf("file://") ? !0 : 0 == f.indexOf("http://") ? !1 : 0 == f.indexOf("/") ? !0 : !1) || d.config.key == g || d.config.key == c || e && g == "$" + d.config.key.split("$")[1];
    };
    this.initialize = function() {};
};

function W(f) {
    var c = window.location.href.toString();
    0 == c.length && (c = document.URL.toString());
    if (f) {
        var d;
        d = c.indexOf("///");
        0 <= d ? d = d + 3 : (d = c.indexOf("//"), d = 0 <= d ? d + 2 : 0);
        c = c.substr(d);
        d = c.indexOf(":");
        var e = c.indexOf("/");
        0 < d && 0 < e && d < e || (0 < e ? d = e : (e = c.indexOf("?"), d = 0 < e ? e : c.length));
        c = c.substr(0, d);
    }
    if (f && (f = c.split(".")) && (d = f.length, !(2 >= d))) {
        if (!(e = -1 != "co,com,net,org,web,gov,edu,".indexOf(f[d - 2] + ","))) {
            b: {
                for (var e = ".ac.uk .ab.ca .bc.ca .mb.ca .nb.ca .nf.ca .nl.ca .ns.ca .nt.ca .nu.ca .on.ca .pe.ca .qc.ca .sk.ca .yk.ca".split(" "), g = 0; g < e.length;) {
                    var h = e[g];
                    if (-1 !== c.indexOf(h, c.length - h.length)) {
                        e = !0;
                        break b;
                    }
                    g++;
                }
                e = !1;
            }
        }
        c = e ? f[d - 3] + "." + f[d - 2] + "." + f[d - 1] : f[d - 2] + "." + f[d - 1];
    }
    return c;
}
var ma = function() {
        function f() {}
        f.prototype = {
            Hd: function(c, d) {
                if (d.ib && (d.Hi || d.create(d.pages.da), !d.initialized)) {
                    c.ub = d.ub = c.config.MixedMode;
                    var e = d.ma;
                    0 == jQuery(e).length && (e = jQuery(d.Sc).find(d.ma));
                    if ("FlipView" == d.ba) {
                        var g = 0 != d.pageNumber % 2 ? "flowpaper_zine_page_left" : "flowpaper_zine_page_right";
                        0 == d.pageNumber && (g = "flowpaper_zine_page_left_noshadow");
                        d.aa.Uf || (g = 0 != d.pageNumber % 2 ? "flowpaper_zine_page_left_noshadow" : "flowpaper_zine_page_right_noshadow");
                        jQuery(e).append("<div id='" + d.pa + "_canvascontainer' style='height:100%;width:100%;position:relative;'><canvas id='" + c.Ja(1, d) + "' style='background-repeat:no-repeat;background-size:100% 100%;position:relative;left:0px;top:0px;height:100%;width:100%;background-color:#ffffff;display:none;' class='flowpaper_interactivearea flowpaper_grab flowpaper_hidden flowpaper_flipview_canvas flowpaper_flipview_page' width='100%' height='100%' ></canvas><canvas id='" + c.Ja(2, d) + "' style='position:relative;left:0px;top:0px;width:100%;height:100%;display:block;background-color:#ffffff;display:none;' class='flowpaper_border flowpaper_interactivearea flowpaper_grab flowpaper_rescale flowpaper_flipview_canvas_highres flowpaper_flipview_page' width='100%' height='100%'></canvas><div id='" + d.pa + "_textoverlay' style='position:absolute;z-index:11;left:0px;top:0px;width:100%;height:100%;' class='" + g + "'></div></div>");
                        if (eb.browser.chrome || eb.browser.safari) {
                            jQuery("#" + c.Ja(1, d)).css("-webkit-backface-visibility", "hidden"), jQuery("#" + c.Ja(2, d)).css("-webkit-backface-visibility", "hidden"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "hidden");
                        }
                        eb.browser.mozilla && (jQuery("#" + c.Ja(1, d)).css("backface-visibility", "hidden"), jQuery("#" + c.Ja(2, d)).css("backface-visibility", "hidden"), jQuery("#" + d.pa + "_textoverlay").css("backface-visibility", "hidden"));
                    }
                    d.initialized = !0;
                }
            },
            So: function(c, d) {
                if ("FlipView" == d.ba && 0 == jQuery("#" + c.Ja(1, d)).length || "FlipView" == d.ba && d.Ga) {
                    return !1;
                }
                "FlipView" != d.ba || null != d.context || d.oc || d.Ga || (d.sd(), d.oc = !0);
                return !0;
            },
            Ro: function(c, d) {
                return 1 == d.scale || 1 < d.scale && d.pageNumber == d.pages.la - 1 || d.pageNumber == d.pages.la - 2;
            },
            Tb: function(c, d, e, g) {
                1 == d.scale && eb.browser.safari ? (jQuery("#" + c.Ja(1, d)).css("-webkit-backface-visibility", "hidden"), jQuery("#" + c.Ja(2, d)).css("-webkit-backface-visibility", "hidden"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "hidden")) : eb.browser.safari && (jQuery("#" + c.Ja(1, d)).css("-webkit-backface-visibility", "visible"), jQuery("#" + c.Ja(2, d)).css("-webkit-backface-visibility", "visible"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "visible"));
                if ("FlipView" != d.ba || 0 != jQuery("#" + c.Ja(1, d)).length) {
                    if ("FlipView" != d.ba || !d.Ga) {
                        if ("FlipView" == d.ba && 1 < d.scale) {
                            if (d.pageNumber == d.pages.la - 1 || d.pageNumber == d.pages.la - 2) {
                                jQuery(c).trigger("UIBlockingRenderingOperation", {
                                    ja: c.ja,
                                    mo: !0
                                });
                                var f = 3 > d.scale ? 2236 : 3236;
                                magnifier = f * Math.sqrt(1 / (d.Va() * d.Za()));
                                d.va.width = d.Va() * magnifier;
                                d.va.height = d.Za() * magnifier;
                            } else {
                                c.hi = !1, d.va.width = 2 * d.Va(), d.va.height = 2 * d.Za(), d.Ga = !0, jQuery("#" + d.Db).mg(), c.Ck(d), eb.platform.touchdevice && (null != c.zi && window.clearTimeout(c.zi), c.zi = setTimeout(function() {}, 1500)), null != g && g();
                            }
                        } else {
                            d.ub && c.pageImagePattern && !d.ai ? (d.ie && d.ie(c.Aa(d.pageNumber + 1), c.Aa(d.pageNumber + 2)), d.dimensions.loaded || c.fd(d.pageNumber + 1, !0, function() {
                                c.wc(d);
                            }, !0, d), d.tm = !0, null == d.wa ? (d.zc = !0, d.wa = new Image, jQuery(d.wa).bind("load", function() {
                                jQuery(d.cc).remove();
                                jQuery(d.va).css("background-image", "url('" + c.Aa(d.pageNumber + 1) + "')");
                                d.zc = !1;
                                c.De();
                            }), jQuery(d.wa).bind("error", function() {
                                jQuery.ajax({
                                    url: this.src,
                                    type: "HEAD",
                                    error: function(f) {
                                        if (404 == f.status || 500 <= f.status) {
                                            d.ai = !0, d.zc = !1, d.tm = !0, d.Ga = !1, 1 == d.pageNumber && d.aa.pages.Dd && d.aa.pages.Dd(), c.Tb(d, e, g);
                                        }
                                    },
                                    success: function() {}
                                });
                            }), jQuery(d.wa).bind("error", function() {
                                jQuery(d.cc).remove();
                                jQuery(d.va).css("background-image", "url('" + c.Aa(d.pageNumber + 1) + "')");
                                d.zc = !1;
                                c.De();
                            }), jQuery(d.wa).attr("src", c.Aa(d.pageNumber + 1)), c.zi = setTimeout(function() {
                                d.zc && ("none" == jQuery(d.va).css("background-image") && jQuery(d.va).css("background-image", "url('" + c.Aa(d.pageNumber + 1) + "')"), d.zc = !1, c.De());
                            }, 6000)) : d.zc || "none" == jQuery(d.va).css("background-image") && jQuery(d.va).css("background-image", "url('" + c.Aa(d.pageNumber + 1) + "')"), c.Ke(d, g)) : !c.Ia && c.nb ? (magnifier = 1236 * Math.sqrt(1 / (d.Va() * d.Za())), d.va.width = d.Va() * magnifier, d.va.height = d.Za() * magnifier) : (d.va.width = 1 * d.Va(), d.va.height = 1 * d.Za());
                        }
                    }
                }
            },
            Gp: function(c, d) {
                return "FlipView" == d.ba;
            },
            jj: function(c, d) {
                "FlipView" == d.ba && (1 < d.scale ? (d.Db = c.Ja(2, d), d.tf = c.Ja(1, d)) : (d.Db = c.Ja(1, d), d.tf = c.Ja(2, d)));
            },
            Ke: function(c, d) {
                "FlipView" == d.ba && (1 < d.scale ? requestAnim(function() {
                    var e = jQuery("#" + c.Ja(2, d)).get(0);
                    e.redraw = d.va.offsetHeight;
                    e.style.display = "";
                    jQuery("#" + c.Ja(1, d)).Tg();
                }) : (jQuery("#" + c.Ja(1, d)).mg(), jQuery("#" + c.Ja(2, d)).Tg()), d.ub && c.pageImagePattern && 1 == d.scale || jQuery(d.cc).remove());
                jQuery(c).trigger("UIBlockingRenderingOperationCompleted", {
                    ja: c.ja
                });
                c.De();
            }
        };
        CanvasPageRenderer.prototype.ah = function(c) {
            return "FlipView" == c.ba ? 1 : 1.4;
        };
        CanvasPageRenderer.prototype.Je = function(c, d, e) {
            var g = this;
            if (null != g.pageThumbImagePattern && 0 < g.pageThumbImagePattern.length) {
                for (var f = 0, m = null, k = c.getDimensions(d)[d - 1].width / c.getDimensions(d)[d - 1].height, l = 1; l < d; l++) {
                    f += 2;
                }
                var n = 1 == d ? f + 1 : f,
                    v = new Image;
                jQuery(v).bind("load", function() {
                    var m = d % 10;
                    0 == m && (m = 10);
                    var l = c.ka.find(".flowpaper_fisheye").find(String.format('*[data-thumbIndex="{0}"]', m)).get(0);
                    l.width = e * k - 2;
                    l.height = e / k / 2 - 2;
                    var p = jQuery(l).parent().width() / l.width;
                    l.getContext("2d").fillStyle = "#999999";
                    var u = (l.height - l.height * k) / 2,
                        C = l.height * k;
                    0 > u && (l.height += l.width - C, u += (l.width - C) / 2);
                    eb.browser.msie && jQuery(l).css({
                        width: l.width * p + "px",
                        height: l.height * p + "px"
                    });
                    jQuery(l).data("origwidth", l.width * p);
                    jQuery(l).data("origheight", l.height * p);
                    l.getContext("2d").fillRect(1 == d ? l.width / 2 : 0, u, n == c.getTotalPages() ? l.width / 2 + 2 : l.width + 2, C + 2);
                    l.getContext("2d").drawImage(v, 1 == d ? l.width / 2 + 1 : 1, u + 1, l.width / 2, C);
                    if (1 < d && f + 1 <= c.getTotalPages() && n + 1 <= c.getTotalPages()) {
                        var w = new Image;
                        jQuery(w).bind("load", function() {
                            l.getContext("2d").drawImage(w, l.width / 2 + 1, u + 1, l.width / 2, C);
                            l.getContext("2d").strokeStyle = "#999999";
                            l.getContext("2d").moveTo(l.width - 1, u);
                            l.getContext("2d").lineTo(l.width - 1, C + 1);
                            l.getContext("2d").stroke();
                            jQuery(c).trigger("onThumbPanelThumbAdded", {
                                Te: m,
                                thumbData: l
                            });
                        });
                        jQuery(w).attr("src", g.Aa(n + 1, 200));
                    } else {
                        jQuery(c).trigger("onThumbPanelThumbAdded", {
                            Te: m,
                            thumbData: l
                        });
                    }
                });
                n <= c.getTotalPages() && jQuery(v).attr("src", g.Aa(n, 200));
            } else {
                if (-1 < g.Ma(null) || 1 != c.scale) {
                    window.clearTimeout(g.Ue), g.Ue = setTimeout(function() {
                        g.Je(c, d, e);
                    }, 50);
                } else {
                    f = 0;
                    m = null;
                    k = c.getDimensions(d)[d - 1].width / c.getDimensions(d)[d - 1].height;
                    for (l = 1; l < d; l++) {
                        f += 2;
                    }
                    var n = 1 == d ? f + 1 : f,
                        v = new Image,
                        u = d % 10;
                    0 == u && (u = 10);
                    m = c.ka.find(".flowpaper_fisheye").find(String.format('*[data-thumbIndex="{0}"]', u)).get(0);
                    m.width = e * k;
                    m.height = e / k / 2;
                    l = jQuery(m).parent().width() / m.width;
                    eb.browser.msie && jQuery(m).css({
                        width: m.width * l + "px",
                        height: m.height * l + "px"
                    });
                    jQuery(m).data("origwidth", m.width * l);
                    jQuery(m).data("origheight", m.height * l);
                    var p = m.height / g.getDimensions()[n - 1].height;
                    g.Oa(null, "thumb_" + n);
                    g.Qa.getPage(n).then(function(l) {
                        var v = l.getViewport(p),
                            t = m.getContext("2d"),
                            y = document.createElement("canvas");
                        y.height = m.height;
                        y.width = y.height * k;
                        var C = {
                            canvasContext: y.getContext("2d"),
                            viewport: v,
                            qh: null,
                            pageNumber: n,
                            continueCallback: function(f) {
                                1 != c.scale ? (window.clearTimeout(g.Ue), g.Ue = setTimeout(function() {
                                    g.Je(c, d, e);
                                }, 50)) : f();
                            }
                        };
                        l.render(C).promise.then(function() {
                            var l = (m.height - m.height * k) / 2,
                                q = m.height * k;
                            0 > l && (m.height += m.width - q, l += (m.width - q) / 2);
                            g.Oa(null, -1, "thumb_" + n);
                            1 < d && f + 1 <= c.getTotalPages() && n + 1 <= c.getTotalPages() ? -1 < g.Ma(null) || 1 != c.scale ? (window.clearTimeout(g.Ue), g.Ue = setTimeout(function() {
                                g.Je(c, d, e);
                            }, 50)) : (g.Oa(null, "thumb_" + (n + 1)), g.Qa.getPage(n + 1).then(function(f) {
                                v = f.getViewport(p);
                                var h = document.createElement("canvas");
                                h.width = y.width;
                                h.height = y.height;
                                C = {
                                    canvasContext: h.getContext("2d"),
                                    viewport: v,
                                    qh: null,
                                    pageNumber: n + 1,
                                    continueCallback: function(f) {
                                        1 != c.scale ? (window.clearTimeout(g.Ue), g.Ue = setTimeout(function() {
                                            g.Je(c, d, e);
                                        }, 50)) : f();
                                    }
                                };
                                f.render(C).promise.then(function() {
                                    g.Oa(null, -1);
                                    t.fillStyle = "#ffffff";
                                    t.fillRect(1 == d ? m.width / 2 : 0, l, m.width / 2, q);
                                    1 != d && t.fillRect(m.width / 2, l, m.width / 2, q);
                                    t.drawImage(y, 1 == d ? m.width / 2 : 0, l, m.width / 2, q);
                                    1 != d && t.drawImage(h, m.width / 2, l, m.width / 2, q);
                                    jQuery(c).trigger("onThumbPanelThumbAdded", {
                                        Te: u,
                                        thumbData: m
                                    });
                                }, function() {
                                    g.Oa(null, -1, "thumb_" + (n + 1));
                                });
                            })) : (t.fillStyle = "#ffffff", t.fillRect(1 == d ? m.width / 2 : 0, l, m.width / 2, q), 1 != d && t.fillRect(m.width / 2, l, m.width / 2, q), t.drawImage(y, 1 == d ? m.width / 2 : 0, l, m.width / 2, q), jQuery(c).trigger("onThumbPanelThumbAdded", {
                                Te: u,
                                thumbData: m
                            }));
                        }, function() {
                            g.Oa(null, -1);
                        });
                    });
                }
            }
        };
        return f;
    }(),
    la = function() {
        function f() {}
        f.prototype = {
            Hd: function(c, d) {
                if (d.ib && (d.Hi || d.create(d.pages.da), !d.initialized)) {
                    c.sb = null != c.Vi && 0 < c.Vi.length && eb.platform.touchonlydevice && !eb.platform.mobilepreview;
                    if ("FlipView" == d.ba) {
                        var e = 0 != d.pageNumber % 2 ? "flowpaper_zine_page_left" : "flowpaper_zine_page_right";
                        0 == d.pageNumber && (e = "flowpaper_zine_page_left_noshadow");
                        d.aa.Uf || (e = 0 != d.pageNumber % 2 ? "flowpaper_zine_page_left_noshadow" : "flowpaper_zine_page_right_noshadow");
                        var g = d.ma;
                        0 == jQuery(g).length && (g = jQuery(d.Sc).find(d.ma));
                        c.Wg(d, g);
                        c.Ib ? jQuery(g).append("<canvas id='" + d.pa + "_canvas' class='flowpaper_flipview_page' height='100%' width='100%' style='z-index:10;position:absolute;left:0px;top:0px;width:100%;height:100%;'></canvas><canvas id='" + d.pa + "_canvas_highres' class='flowpaper_flipview_page' height='100%' width='100%' style='display:none;z-index:10;position:absolute;left:0px;top:0px;width:100%;height:100%;background-color:#ffffff;'></canvas><div id='" + d.pa + "_textoverlay' style='z-index:11;position:absolute;left:0px;top:0px;width:100%;height:100%;' class='" + e + "'></div>") : jQuery(g).append("<canvas id='" + d.pa + "_canvas' class='flowpaper_flipview_page' height='100%' width='100%' style='z-index:10;position:absolute;left:0px;top:0px;width:100%;height:100%;'></canvas><canvas id='" + d.pa + "_canvas_highres' class='flowpaper_flipview_page' height='100%' width='100%' style='image-rendering:-webkit-optimize-contrast;display:none;z-index:10;position:absolute;left:0px;top:0px;width:100%;height:100%;'></canvas><div id='" + d.pa + "_textoverlay' style='z-index:11;position:absolute;left:0px;top:0px;width:100%;height:100%;' class='" + e + "'></div>");
                        if (eb.browser.chrome || eb.browser.safari) {
                            eb.browser.safari && (jQuery("#" + d.pa + "_canvas").css("-webkit-backface-visibility", "hidden"), jQuery("#" + d.pa + "_canvas_highres").css("-webkit-backface-visibility", "hidden")), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "hidden");
                        }
                    }
                    d.initialized = !0;
                }
            },
            Tb: function(c, d, e, g) {
                d.initialized || c.Hd(d);
                if (!d.Ga && "FlipView" == d.ba) {
                    if (-1 < c.Ma(d) && d.pageNumber != d.pages.la && d.pageNumber != d.pages.la - 2 && d.pageNumber != d.pages.la - 1) {
                        if (window.clearTimeout(d.fc), d.pageNumber == d.pages.la || d.pageNumber == d.pages.la - 2 || d.pageNumber == d.pages.la - 1) {
                            d.fc = setTimeout(function() {
                                c.Tb(d, e, g);
                            }, 250);
                        }
                    } else {
                        1 == d.scale && d.ie && d.ie(c.Aa(d.pageNumber + 1), c.Aa(d.pageNumber + 2));
                        if (!d.Ga) {
                            c.Cq = d.scale;
                            c.Oa(d, d.pageNumber);
                            1 == d.scale && d.sd();
                            d.zc = !0;
                            if (!d.wa || d.On != d.scale || c.Ll(d)) {
                                d.On = d.scale, d.wa = new Image, jQuery(d.wa).bind("load", function() {
                                    d.zc = !1;
                                    d.nf = !0;
                                    d.bg = this.height;
                                    d.cg = this.width;
                                    d.mc();
                                    c.Dc(d);
                                    d.dimensions.Ca > d.dimensions.width && (d.dimensions.width = d.dimensions.Ca, d.dimensions.height = d.dimensions.Na);
                                }), jQuery(d.wa).bind("abort", function() {
                                    d.zc = !1;
                                    c.Oa(d, -1);
                                }), jQuery(d.wa).bind("error", function() {
                                    d.zc = !1;
                                    c.Oa(d, -1);
                                });
                            }
                            1 >= d.scale ? jQuery(d.wa).attr("src", c.Aa(d.pageNumber + 1, null, c.Ib)) : c.sb && 1 < d.scale ? d.pageNumber == d.pages.la - 1 || d.pageNumber == d.pages.la - 2 ? jQuery(d.wa).attr("src", c.Aa(d.pageNumber + 1, null, c.Ib)) : jQuery(d.wa).attr("src", c.ua) : d.pageNumber == d.pages.la - 1 || d.pageNumber == d.pages.la - 2 ? (!c.Ib || -1 != jQuery(d.wa).attr("src").indexOf(".svg") && d.Wn == d.scale || c.Ma(d) != d.pageNumber || d.pageNumber != d.pages.la - 1 && d.pageNumber != d.pages.la - 2 ? d.El == d.scale && (jQuery(d.ma + "_canvas_highres").show(), jQuery(d.ma + "_canvas").hide()) : (jQuery(c).trigger("UIBlockingRenderingOperation", c.ja), d.Wn = d.scale, jQuery(d.wa).attr("src", c.Aa(d.pageNumber + 1, null, c.Ib))), c.Ib || jQuery(d.wa).attr("src", c.Aa(d.pageNumber + 1, null, c.Ib))) : jQuery(d.wa).attr("src", c.ua);
                        }
                        jQuery(d.ma).removeClass("flowpaper_load_on_demand");
                        !d.Ga && jQuery(d.Ka).attr("src") == c.ua && d.nf && c.Dc(d);
                        null != g && g();
                    }
                }
            },
            Dc: function(c, d) {
                if ("FlipView" == d.ba) {
                    jQuery(d.ma).removeClass("flowpaper_hidden");
                    jQuery(".flowpaper_pageLoader").hide();
                    1 == d.scale && eb.browser.safari ? (jQuery("#" + d.pa + "_canvas").css("-webkit-backface-visibility", "hidden"), jQuery("#" + d.pa + "_canvas_highres").css("-webkit-backface-visibility", "hidden"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "hidden")) : eb.browser.safari && (jQuery("#" + d.pa + "_canvas").css("-webkit-backface-visibility", "visible"), jQuery("#" + d.pa + "_canvas_highres").css("-webkit-backface-visibility", "visible"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "visible"));
                    if (c.Ll(d)) {
                        1 == d.scale ? (jQuery(d.Ha).css("background-image", "url('" + c.Aa(d.pageNumber + 1, null, c.Ib) + "')"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "visible"), jQuery("#" + d.pa + "_textoverlay").css("backface-visibility", "visible"), c.yc(d)) : (d.pageNumber == d.pages.la - 1 || d.pageNumber == d.pages.la - 2 ? jQuery(d.Ha).css("background-image", "url('" + c.Aa(d.pageNumber + 1) + "')") : jQuery(d.Ha).css("background-image", "url(" + c.ua + ")"), jQuery("#" + d.pa + "_textoverlay").css("-webkit-backface-visibility", "visible"), jQuery("#" + d.pa + "_textoverlay").css("backface-visibility", "visible"), jQuery(d.ma + "_canvas").hide(), c.sb && d.scale > d.Xf() && (d.fc = setTimeout(function() {
                            c.Vc(d);
                            jQuery(".flowpaper_flipview_canvas_highres").show();
                            jQuery(".flowpaper_flipview_canvas").hide();
                        }, 500)));
                    } else {
                        var e = document.getElementById(d.pa + "_canvas");
                        jQuery(d.Ha).css("background-image", "url(" + c.ua + ")");
                        if (1 == d.scale && e && (100 == e.width || jQuery(e).hasClass("flowpaper_redraw"))) {
                            var g = e;
                            if (g) {
                                g.width = d.Va();
                                g.height = d.Za();
                                var f = g.getContext("2d");
                                f.Hf = f.mozImageSmoothingEnabled = f.imageSmoothingEnabled = !0;
                                f.drawImage(d.wa, 0, 0, d.Va(), d.Za());
                                jQuery(e).removeClass("flowpaper_redraw");
                                1 == d.scale && (jQuery(d.ma + "_canvas").show(), jQuery(d.ma + "_canvas_highres").hide());
                                1 < d.pageNumber && jQuery(d.ma + "_pixel").css({
                                    width: 2 * d.Va(),
                                    height: 2 * d.Za()
                                });
                                c.yc(d);
                            }
                        } else {
                            1 == d.scale && e && 100 != e.width && (jQuery(d.ma + "_canvas").show(), jQuery(d.ma + "_canvas_highres").hide(), c.yc(d));
                        }
                        if (1 < d.scale) {
                            if (g = document.getElementById(d.pa + "_canvas_highres")) {
                                !(c.Ib && d.El != d.scale || 100 == g.width || jQuery(g).hasClass("flowpaper_redraw")) || d.pageNumber != d.pages.la - 1 && d.pageNumber != d.pages.la - 2 ? (jQuery(d.ma + "_pixel").css({
                                    width: 2 * d.Va(),
                                    height: 2 * d.Za()
                                }), jQuery(d.ma + "_canvas_highres").show(), jQuery(d.ma + "_canvas").hide(), c.sb && jQuery(d.ma + "_canvas_highres").css("z-index", "-1")) : (d.El = d.scale, jQuery(c).trigger("UIBlockingRenderingOperation", c.ja), e = 1000 < d.ia.width() || 1000 < d.ia.height() ? 1 : 2, f = (d.ia.width() - 30) * d.scale, eb.platform.ios && (1500 < f * d.jf() || 535 < d.hf()) && (e = 2236 * Math.sqrt(1 / (d.Va() * d.Za()))), eb.browser.safari && !eb.platform.touchdevice && 3 > e && (e = 3), f = g.getContext("2d"), f.Hf || f.mozImageSmoothingEnabled || f.imageSmoothingEnabled ? (f.Hf = f.mozImageSmoothingEnabled = f.imageSmoothingEnabled = !1, c.Ib ? (g.width = d.Va() * e, g.height = d.Za() * e, f.drawImage(d.wa, 0, 0, d.Va() * e, d.Za() * e)) : (g.width = d.wa.width, g.height = d.wa.height, f.drawImage(d.wa, 0, 0))) : (g.width = d.Va() * e, g.height = d.Za() * e, f.drawImage(d.wa, 0, 0, d.Va() * e, d.Za() * e)), c.Ib ? c.Tn(d, g.width / d.wa.width, function() {
                                    jQuery(g).removeClass("flowpaper_redraw");
                                    jQuery(d.ma + "_canvas_highres").show();
                                    jQuery(d.ma + "_canvas").hide();
                                    jQuery(d.ma + "_canvas_highres").addClass("flowpaper_flipview_canvas_highres");
                                    jQuery(d.ma + "_canvas").addClass("flowpaper_flipview_canvas");
                                    c.Oa(d, -1);
                                }) : (jQuery(g).removeClass("flowpaper_redraw"), jQuery(d.ma + "_canvas_highres").show(), jQuery(d.ma + "_canvas").hide(), jQuery(d.ma + "_canvas_highres").addClass("flowpaper_flipview_canvas_highres"), jQuery(d.ma + "_canvas").addClass("flowpaper_flipview_canvas"), c.sb && jQuery(d.ma + "_canvas_highres").css("z-index", "-1")));
                            }
                            d.fc = setTimeout(function() {
                                c.Vc(d);
                            }, 500);
                        }
                    }
                    d.Ga = 0 < jQuery(d.Ha).length;
                }
            },
            unload: function(c, d) {
                d.wa = null;
                jQuery(d.Ha).css("background-image", "url(" + c.ua + ")");
                var e = document.getElementById(d.pa + "_canvas");
                e && (e.width = 100, e.height = 100);
                if (e = document.getElementById(d.pa + "_canvas_highres")) {
                    e.width = 100, e.height = 100;
                }
            }
        };
        ImagePageRenderer.prototype.Ll = function(c) {
            return eb.platform.touchdevice && (eb.platform.Id || 5000000 < c.cg * c.bg || eb.platform.android) && (eb.platform.Id || eb.platform.android) || eb.browser.chrome || eb.browser.mozilla;
        };
        ImagePageRenderer.prototype.resize = function(c, d) {
            this.Wg(d);
        };
        ImagePageRenderer.prototype.Tn = function(c, d, e) {
            var g = this;
            window.Zh = d;
            jQuery.ajax({
                type: "GET",
                url: g.Aa(c.pageNumber + 1, null, g.Ib),
                cache: !0,
                dataType: "xml",
                success: function(f) {
                    var m = new Image;
                    jQuery(m).bind("load", function() {
                        var g = document.getElementById(c.pa + "_canvas"),
                            l = document.getElementById(c.pa + "_canvas_highres").getContext("2d");
                        l.Hf = l.mozImageSmoothingEnabled = l.imageSmoothingEnabled = !1;
                        var n = g.getContext("2d");
                        n.Hf = n.mozImageSmoothingEnabled = n.imageSmoothingEnabled = !1;
                        g.width = c.wa.width * d;
                        g.height = c.wa.height * d;
                        n.drawImage(m, 0, 0, c.wa.width * d, c.wa.height * d);
                        if (c.Dl) {
                            v = c.Dl;
                        } else {
                            var v = [];
                            jQuery(f).find("image").each(function() {
                                var c = {};
                                c.id = jQuery(this).attr("id");
                                c.width = R(jQuery(this).attr("width"));
                                c.height = R(jQuery(this).attr("height"));
                                c.data = jQuery(this).attr("xlink:href");
                                c.dataType = 0 < c.data.length ? c.data.substr(0, 15) : "";
                                v[v.length] = c;
                                jQuery(f).find("use[xlink\\:href='#" + c.id + "']").each(function() {
                                    if (jQuery(this).attr("transform") && (c.transform = jQuery(this).attr("transform"), c.transform = c.transform.substr(7, c.transform.length - 8), c.vh = c.transform.split(" "), c.x = R(c.vh[c.vh.length - 2]), c.y = R(c.vh[c.vh.length - 1]), "g" == jQuery(this).parent()[0].nodeName && null != jQuery(this).parent().attr("clip-path"))) {
                                        var d = jQuery(this).parent().attr("clip-path"),
                                            d = d.substr(5, d.length - 6);
                                        jQuery(f).find("*[id='" + d + "']").each(function() {
                                            c.Qf = [];
                                            jQuery(this).find("path").each(function() {
                                                var d = {};
                                                d.d = jQuery(this).attr("d");
                                                c.Qf[c.Qf.length] = d;
                                            });
                                        });
                                    }
                                });
                            });
                            c.Dl = v;
                        }
                        for (n = 0; n < v.length; n++) {
                            if (v[n].Qf) {
                                for (var u = 0; u < v[n].Qf.length; u++) {
                                    for (var p = v[n].Qf[u].d.replace(/M/g, "M\x00").replace(/m/g, "m\x00").replace(/v/g, "v\x00").replace(/l/g, "l\x00").replace(/h/g, "h\x00").replace(/c/g, "c\x00").replace(/s/g, "s\x00").replace(/z/g, "z\x00").split(/(?=M|m|v|h|s|c|l|z)|\0/), q = 0, r = 0, t = 0, y = 0, C = !1, w, A = !0, z = 0; z < p.length; z += 2) {
                                        if ("M" == p[z] && p.length > z + 1 && (w = S(p[z + 1]), t = q = R(w[0]), y = r = R(w[1]), A && (C = !0)), "m" == p[z] && p.length > z + 1 && (w = S(p[z + 1]), t = q += R(w[0]), y = r += R(w[1]), A && (C = !0)), "l" == p[z] && p.length > z + 1 && (w = S(p[z + 1]), q += R(w[0]), r += R(w[1])), "h" == p[z] && p.length > z + 1 && (w = S(p[z + 1]), q += R(w[0])), "v" == p[z] && p.length > z + 1 && (w = S(p[z + 1]), r += R(w[0])), "s" == p[z] && p.length > z + 1 && (w = S(p[z + 1])), "c" == p[z] && p.length > z + 1 && (w = S(p[z + 1])), "z" == p[z] && p.length > z + 1 && (q = t, r = y, w = null), C && (l.save(), l.beginPath(), A = C = !1), "M" == p[z] || "m" == p[z]) {
                                            l.moveTo(q, r);
                                        } else {
                                            if ("c" == p[z] && null != w) {
                                                for (var H = 0; H < w.length; H += 6) {
                                                    var I = q + R(w[H + 0]),
                                                        J = r + R(w[H + 1]),
                                                        G = q + R(w[H + 2]),
                                                        x = r + R(w[H + 3]),
                                                        F = q + R(w[H + 4]),
                                                        B = r + R(w[H + 5]);
                                                    l.bezierCurveTo(I, J, G, x, F, B);
                                                    q = F;
                                                    r = B;
                                                }
                                            } else {
                                                "s" == p[z] && null != w ? (G = q + R(w[0]), x = r + R(w[1]), F = q + R(w[2]), B = r + R(w[3]), l.bezierCurveTo(q, r, G, x, F, B), q = F, r = B) : "z" == p[z] ? (l.lineTo(q, r), l.closePath(), l.clip(), l.drawImage(g, 0, 0), l.restore(), A = !0, z--) : l.lineTo(q, r);
                                            }
                                        }
                                    }
                                }
                            } else {
                                K("no clip path for image!");
                            }
                        }
                        e && e();
                    });
                    m.src = g.Aa(c.pageNumber + 1);
                }
            });
        };
        ImagePageRenderer.prototype.Je = function(c, d, e) {
            var g = this,
                f = 0,
                m = c.getDimensions(d)[d - 1].Ca / c.getDimensions(d)[d - 1].Na;
            g.nb && 1 < d && (m = c.getDimensions(1)[0].Ca / c.getDimensions(1)[0].Na);
            for (var k = 1; k < d; k++) {
                f += 2;
            }
            var l = 1 == d ? f + 1 : f,
                n = new Image;
            jQuery(n).bind("load", function() {
                var k = d % 10;
                0 == k && (k = 10);
                var u = jQuery(".flowpaper_fisheye").find(String.format('*[data-thumbIndex="{0}"]', k)).get(0);
                u.width = e * m - 2;
                u.height = e / m / 2 - 2;
                var p = jQuery(u).parent().width() / u.width;
                u.getContext("2d").fillStyle = "#999999";
                var q = (u.height - u.height * m) / 2,
                    r = u.height * m;
                0 > q && (u.height += u.width - r, q += (u.width - r) / 2);
                jQuery(u).data("origwidth", u.width * p);
                jQuery(u).data("origheight", u.height * p);
                (eb.browser.msie || eb.browser.safari && 5 > eb.browser.Gb) && jQuery(u).css({
                    width: u.width * p + "px",
                    height: u.height * p + "px"
                });
                u.getContext("2d").fillRect(1 == d ? u.width / 2 : 0, q, l == c.getTotalPages() ? u.width / 2 + 2 : u.width + 2, r + 2);
                u.getContext("2d").drawImage(n, 1 == d ? u.width / 2 + 1 : 1, q + 1, u.width / 2, r);
                if (1 < d && f + 1 <= c.getTotalPages() && l + 1 <= c.getTotalPages()) {
                    var t = new Image;
                    jQuery(t).bind("load", function() {
                        u.getContext("2d").drawImage(t, u.width / 2 + 1, q + 1, u.width / 2, r);
                        u.getContext("2d").strokeStyle = "#999999";
                        u.getContext("2d").moveTo(u.width - 1, q);
                        u.getContext("2d").lineTo(u.width - 1, r + 1);
                        u.getContext("2d").stroke();
                        jQuery(c).trigger("onThumbPanelThumbAdded", {
                            Te: k,
                            thumbData: u
                        });
                    });
                    jQuery(t).attr("src", g.Aa(l + 1, 200));
                } else {
                    jQuery(c).trigger("onThumbPanelThumbAdded", {
                        Te: k,
                        thumbData: u
                    });
                }
            });
            l <= c.getTotalPages() && jQuery(n).attr("src", g.Aa(l, 200));
        };
        return f;
    }(),
    ja = function() {
        function f() {}
        V.prototype.Og = function() {
            var c = this.aa.ca.ig,
                d = this.Pg(0),
                d = d.Ca / d.Na,
                e = Math.round(this.ia.height() - 10);
            this.aa.ka.find(".flowpaper_fisheye");
            var g = eb.platform.touchdevice ? 90 == window.orientation || -90 == window.orientation || jQuery(window).height() > jQuery(window).width() : !1;
            this.aa.ca.Sf && !this.aa.PreviewMode ? e -= eb.platform.touchonlydevice ? this.aa.Zb ? this.aa.ca.Vb.height() : 0 : this.ia.height() * (this.aa.Zb ? 0.2 : 0.15) : this.aa.PreviewMode ? this.aa.PreviewMode && (e = this.aa.ka.height() - 15, e -= eb.platform.touchonlydevice ? this.aa.Zb ? this.aa.ca.Vb.height() + 30 : 0 : this.ia.height() * (g ? 0.5 : 0.09)) : e = this.aa.Yd ? e - (eb.platform.touchonlydevice ? this.aa.Zb ? 5 : 0 : this.ia.height() * (g ? 0.5 : 0.07)) : e - (eb.platform.touchonlydevice ? this.aa.Zb ? this.aa.ca.Vb.height() : 0 : this.ia.height() * (g ? 0.5 : 0.07));
            g = this.ia.width();
            2 * e * d > g - (c ? 53 : 0) && !this.aa.ca.Ta && (e = g / 2 / d - +(c ? 35 : 75));
            if (e * d > g - (c ? 53 : 0) && this.aa.ca.Ta) {
                for (var f = 10; e * d > g - (c ? 53 : 0) && 1000 > f;) {
                    e = g / d - f + (c ? 0 : 50), f += 10;
                }
            }
            if (!eb.browser.Nq) {
                for (c = 2.5 * Math.floor(e * (this.aa.ca.Ta ? 1 : 2) * d), g = 0; 0 != c % 4 && 20 > g;) {
                    e += 0.5, c = 2.5 * Math.floor(e * (this.aa.ca.Ta ? 1 : 2) * d), g++;
                }
            }
            return e;
        };
        V.prototype.Bn = function(c, d) {
            var e = this;
            c = parseInt(c);
            e.aa.Qd = d;
            e.aa.renderer.$d && e.Ne(c);
            1 != this.aa.scale ? e.Xa(1, !0, function() {
                e.aa.turn("page", c);
            }) : e.aa.turn("page", c);
        };
        V.prototype.si = function() {
            return (this.ia.width() - this.cd()) / 2;
        };
        V.prototype.cd = function() {
            var c = this.Pg(0),
                c = c.Ca / c.Na;
            return Math.floor(this.Og() * (this.aa.ca.Ta ? 1 : 2) * c);
        };
        V.prototype.de = function() {
            if ("FlipView" == this.aa.ba) {
                return 0 < this.width ? this.width : this.width = this.ga(this.da).width();
            }
        };
        V.prototype.hf = function() {
            if ("FlipView" == this.aa.ba) {
                return 0 < this.height ? this.height : this.height = this.ga(this.da).height();
            }
        };
        f.prototype = {
            Ne: function(c, d) {
                for (var e = d - 10; e < d + 10; e++) {
                    0 < e && e + 1 < c.aa.getTotalPages() + 1 && !c.getPage(e).initialized && (c.getPage(e).ib = !0, c.aa.renderer.Hd(c.getPage(e)), c.getPage(e).ib = !1);
                }
            },
            ec: function(c) {
                null != c.Od && (window.clearTimeout(c.Od), c.Od = null);
                var d = 1 < c.la ? c.la - 1 : c.la;
                if (!c.aa.renderer.vb || c.aa.renderer.ub && 1 == c.aa.scale) {
                    1 <= c.la ? (c.pages[d - 1].load(function() {
                        1 < c.la && c.pages[d] && c.pages[d].load(function() {
                            c.pages[d].La();
                            for (var e = c.ga(c.da).scrollTop(), g = 0; g < c.document.numPages; g++) {
                                c.kb(g) && (c.pages[g].Mc(e, c.ga(c.da).height(), !0) ? (c.pages[g].ib = !0, c.pages[g].load(function() {}), c.pages[g].La()) : c.pages[g].unload());
                            }
                        });
                    }), c.pages[d - 1].La()) : c.pages[d] && c.pages[d].load(function() {
                        c.pages[d].La();
                        for (var e = c.ga(c.da).scrollTop(), g = 0; g < c.document.numPages; g++) {
                            c.kb(g) && (c.pages[g].Mc(e, c.ga(c.da).height(), !0) ? (c.pages[g].ib = !0, c.pages[g].load(function() {}), c.pages[g].La()) : c.pages[g].unload());
                        }
                    });
                } else {
                    1 < c.la ? (c.pages[d - 1] && c.pages[d - 1].load(function() {}), c.pages[d - 0] && c.pages[d - 0].load(function() {})) : c.pages[d] && c.pages[d].load(function() {});
                    for (var e = c.ga(c.da).scrollTop(), g = 0; g < c.document.numPages; g++) {
                        c.kb(g) && (c.pages[g].Mc(e, c.ga(c.da).height(), !0) ? (c.pages[g].ib = !0, c.pages[g].load(function() {}), c.pages[g].La()) : c.pages[g].unload());
                    }
                }
            },
            Ti: function(c) {
                c.gi = setTimeout(function() {
                    c.aa.pages && "FlipView" == c.aa.ba && (1.1 < c.aa.scale ? (c.ga(c.da + "_panelLeft").finish(), c.ga(c.da + "_panelRight").finish(), c.ga(c.da + "_panelLeft").fadeTo("fast", 0), c.ga(c.da + "_panelRight").fadeTo("fast", 0), c.aa.Ea.data().opts.cornerDragging = !1) : (c.ga(c.da + "_panelLeft").finish(), c.ga(c.da + "_panelRight").finish(), 1 < c.la ? c.ga(c.da + "_panelLeft").fadeTo("fast", 1) : c.ga(c.da + "_panelLeft").fadeTo("fast", 0), c.aa.ta < c.aa.getTotalPages() && c.ga(c.da + "_panelRight").fadeTo("fast", 1), c.aa.Ea && c.aa.Ea.data().opts && (c.aa.Ea.data().opts.cornerDragging = !0)), c.fh = !1);
                }, 1000);
            },
            hc: function(c) {
                return "FlipView" == c.aa.ba && !(eb.browser.safari && 7 <= eb.browser.Gb && !eb.platform.touchdevice);
            },
            Xa: function(c, d, e, g, f) {
                jQuery(c).trigger("onScaleChanged");
                1 < e && 0 < jQuery("#" + c.Ob).length && jQuery("#" + c.Ob).css("z-index", -1);
                if ("FlipView" == c.aa.ba && (e >= 1 + c.aa.document.ZoomInterval ? jQuery(".flowpaper_page, " + c.da).removeClass("flowpaper_page_zoomIn").addClass("flowpaper_page_zoomOut") : jQuery(".flowpaper_page, " + c.da).removeClass("flowpaper_page_zoomOut").addClass("flowpaper_page_zoomIn"), jQuery(c.da).data().totalPages)) {
                    var m = c.Pg(0),
                        k = m.Ca / m.Na,
                        m = c.Og() * e,
                        k = 2 * m * k;
                    if (!g || !c.hc() || 1 < d && !c.ga(c.da + "_parent").mf()) {
                        if (c.ga(c.da + "_parent").mf() && e >= 1 + c.aa.document.ZoomInterval && ((d = c.vi()) ? (c.ga(c.da + "_parent").transition({
                                transformOrigin: "0px 0px"
                            }, 0), c.ga(c.da + "_parent").transition({
                                x: 0,
                                y: 0,
                                scale: 1
                            }, 0), g.ic = d.left, g.Ic = d.top, g.Cd = !0) : (l = 1 != c.aa.ta || c.aa.ca.Ta ? 0 : -(c.cd() / 4), c.ga(c.da + "_parent").transition({
                                x: l,
                                y: c.aa.gc,
                                scale: 1
                            }, 0))), c.ga(c.da).mf() && c.ga(c.da).transition({
                                x: 0,
                                y: 0,
                                scale: 1
                            }, 0), !c.animating) {
                            c.ih || (c.ih = c.aa.Ea.width(), c.oo = c.aa.Ea.height());
                            1 == e && c.ih ? (turnwidth = c.ih, turnheight = c.oo) : (turnwidth = k - (c.ga(c.da + "_panelLeft").width() + c.ga(c.da + "_panelRight").width() + 40), turnheight = m);
                            c.ga(c.da).css({
                                width: k,
                                height: m
                            });
                            c.aa.Ea.turn("size", turnwidth, turnheight, !1);
                            e >= 1 + c.aa.document.ZoomInterval ? (g.Cd || eb.platform.touchonlydevice) && requestAnim(function() {
                                c.ia.scrollTo({
                                    left: jQuery(c.ia).scrollLeft() + g.ic / e + "px",
                                    top: jQuery(c.ia).scrollTop() + g.Ic / e + "px"
                                });
                            }, 500) : c.Ce();
                            for (m = 0; m < c.document.numPages; m++) {
                                c.kb(m) && (c.pages[m].Ga = !1);
                            }
                            1 < e ? c.aa.Ea.turn("setCornerDragging", !1) : (c.ga(c.da + "_panelLeft").show(), c.ga(c.da + "_panelRight").show(), c.aa.Ea.turn("setCornerDragging", !0));
                            c.qd();
                            setTimeout(function() {
                                null != f && f();
                            }, 200);
                        }
                    } else {
                        if (!c.animating || !c.Rj) {
                            c.animating = !0;
                            c.Rj = g.Cd;
                            jQuery(".flowpaper_flipview_canvas").show();
                            jQuery(".flowpaper_flipview_canvas_highres").hide();
                            jQuery("#" + c.Ob).css("z-index", -1);
                            jQuery(c).trigger("onScaleChanged");
                            m = 400;
                            d = "snap";
                            c.aa.document.ZoomTime && (m = 1000 * parseFloat(c.aa.document.ZoomTime));
                            c.aa.document.ZoomTransition && ("easeOut" == c.aa.document.ZoomTransition && (d = "snap"), "easeIn" == c.aa.document.ZoomTransition && (d = "ease-in", m /= 2));
                            g && g.ic && g.Ic ? (g.Cd && (g.ic = g.ic + c.si()), g.Cd || eb.platform.touchonlydevice ? (c.zd = g.ic, c.Ad = g.Ic) : (k = c.ga(c.da + "_parent").css("transformOrigin").split(" "), 2 == k.length ? (k[0] = k[0].replace("px", ""), k[1] = k[1].replace("px", ""), c.zd = parseFloat(k[0]), c.Ad = parseFloat(k[1])) : (c.zd = g.ic, c.Ad = g.Ic), c.ml = !0), g.Jf && (m = g.Jf)) : (c.zd = 0, c.Ad = 0);
                            c.aa.renderer.vb && c.aa.renderer.sb && 1 == e && (k = 1 < c.la ? c.la - 1 : c.la, 1 < c.la && c.aa.renderer.yc(c.pages[k - 1]), c.aa.renderer.yc(c.pages[k]));
                            "undefined" != g.Jf && (m = g.Jf);
                            e >= 1 + c.aa.document.ZoomInterval ? ("preserve-3d" == c.ga(c.da + "_parent").css("transform-style") && (m = 0), (g.Cd || eb.platform.touchonlydevice) && c.ga(c.da + "_parent").css({
                                transformOrigin: c.zd + "px " + c.Ad + "px"
                            }), c.aa.Ea.turn("setCornerDragging", !1)) : (c.ga(c.da).transition({
                                x: 0,
                                y: 0
                            }, 0), c.aa.Ea.turn("setCornerDragging", !0));
                            var l = 1 != c.aa.ta || c.aa.ca.Ta ? 0 : -(c.cd() / 4);
                            c.ga(c.da + "_parent").transition({
                                x: l,
                                y: c.aa.gc,
                                scale: e
                            }, m, d, function() {
                                null != c.ne && (window.clearTimeout(c.ne), c.ne = null);
                                c.ne = setTimeout(function() {
                                    for (var d = 0; d < c.document.numPages; d++) {
                                        c.pages[d].Ga = !1;
                                    }
                                    c.nd = 0;
                                    c.ke = 0;
                                    c.qd();
                                    c.animating = !1;
                                    c.Rj = !1;
                                }, 50);
                                1 == e && c.ga(c.da + "_parent").css("-webkit-transform-origin:", "");
                                null != f && f();
                            });
                        }
                    }
                }
            },
            resize: function(c, d, e, g) {
                c.width = -1;
                c.height = -1;
                jQuery(".flowpaper_pageword_" + c.ja + ", .flowpaper_interactiveobject_" + c.ja).remove();
                if ("FlipView" == c.aa.ba) {
                    1 != c.aa.ta || c.aa.ca.Ta ? c.aa.ca.Ta || jQuery(c.da + "_parent").transition({
                        x: 0,
                        y: c.aa.gc
                    }, 0, "snap", function() {}) : jQuery(c.da + "_parent").transition({
                        x: -(c.cd() / 4),
                        y: c.aa.gc
                    }, 0, "snap", function() {});
                    var f = c.Og(),
                        m = c.cd();
                    c.ga(c.da + "_parent").css({
                        width: d,
                        height: f
                    });
                    c.$c = m;
                    c.Mf = f;
                    d = c.si();
                    c.aa.Ea && c.aa.Ea.turn("size", m, f, !1);
                    c.ga(c.da + "_panelLeft").css({
                        "margin-left": d - 22,
                        width: 22,
                        height: f - 30
                    });
                    c.ga(c.da + "_arrowleft").css({
                        top: (f - 30) / 2 + "px"
                    });
                    c.ga(c.da + "_arrowright").css({
                        top: (f - 30) / 2 + "px"
                    });
                    c.ga(c.da + "_panelRight").css({
                        width: 22,
                        height: f - 30
                    });
                    c.aa.PreviewMode ? (jQuery(c.da + "_arrowleftbottom").hide(), jQuery(c.da + "_arrowleftbottommarker").hide(), jQuery(c.da + "_arrowrightbottom").hide(), jQuery(c.da + "_arrowrightbottommarker").hide()) : (jQuery(c.da + "_arrowleftbottom").show(), jQuery(c.da + "_arrowleftbottommarker").show(), jQuery(c.da + "_arrowrightbottom").show(), jQuery(c.da + "_arrowrightbottommarker").show());
                    c.ih = null;
                    c.zr = null;
                }
                jQuery(".flowpaper_flipview_page").addClass("flowpaper_redraw");
                for (d = 0; d < c.document.numPages; d++) {
                    c.kb(d) && c.pages[d].Xa();
                }
                "FlipView" == c.aa.ba ? (window.clearTimeout(c.Co), c.Co = setTimeout(function() {
                    c.il && c.il();
                    for (var d = 0; d < c.document.numPages; d++) {
                        c.kb(d) && (c.pages[d].Ga = !1, null != c.aa.renderer.resize && c.aa.renderer.resize(c.aa.renderer, c.pages[d]));
                    }
                    c.qd();
                    jQuery(c.aa).trigger("onResizeCompleted");
                    c.aa.ca.yb && jQuery("#" + c.pages.container + "_webglcanvas").css({
                        width: m,
                        height: f
                    });
                    g && g();
                }, 300)) : g && g();
            },
            fe: function(c, d) {
                c.aa.PreviewMode ? c.aa.openFullScreen() : c.Xd() || ("FlipView" == c.aa.ba ? d ? c.Xa(2, {
                    ic: jQuery(c.da + "_parent").width() / 2,
                    Ic: jQuery(c.da + "_parent").height() / 2
                }) : c.Xa(2, {
                    ic: c.Oc,
                    Ic: c.Pc
                }) : c.Xa(1), c.le());
            },
            dd: function(c, d) {
                "FlipView" == c.aa.ba ? c.Xa(1, !0, d) : c.Xa(window.FitHeightScale);
                c.le();
            },
            Si: function(c) {
                "FlipView" == c.aa.ba && (this.touchwipe = c.ga(c.da).touchwipe({
                    wipeLeft: function() {
                        c.xh = !0;
                        setTimeout(function() {
                            c.xh = !1;
                        }, 800);
                        c.Ef = null;
                        null == c.sa && (c.aa.Ea.turn("cornerActivated") || c.animating || 1 == c.aa.scale && c.next());
                    },
                    wipeRight: function() {
                        c.xh = !0;
                        setTimeout(function() {
                            c.xh = !1;
                        }, 800);
                        c.Ef = null;
                        c.aa.Ea.turn("cornerActivated") || c.animating || null == c.sa && 1 == c.aa.scale && c.previous();
                    },
                    preventDefaultEvents: !0,
                    min_move_x: 100,
                    min_move_y: 100
                }));
            },
            Vj: function(c) {
                eb.platform.touchdevice && !c.aa.ca.Rf && c.ga(c.da).doubletap(function(d) {
                    c.Ef = null;
                    if ("TwoPage" == c.aa.ba || "BookView" == c.aa.ba || "FlipView" == c.aa.ba) {
                        "TwoPage" != c.aa.ba && "BookView" != c.aa.ba || 1 == c.aa.scale ? 1 != c.aa.scale || "FlipView" != c.aa.ba || c.fh ? "FlipView" == c.aa.ba && 1 <= c.aa.scale && !c.Wi ? c.dd() : "TwoPage" == c.aa.ba && 1 == c.aa.scale && c.dd() : c.fe() : c.fe(), d.preventDefault(), c.Wi = !1, c.fh = !1;
                    }
                }, null, 300);
            },
            Xh: function(c, d) {
                if ("FlipView" == c.aa.ba) {
                    var e = c.Og(),
                        g = c.cd(),
                        f = c.si(),
                        m = c.aa.ca.ig && (430 < g || c.aa.PreviewMode || c.aa.ca.Ta),
                        k = m ? 0 : f,
                        l = 22,
                        f = f - l;
                    20 > l && (l = 20);
                    var n = c.aa.ca.dc ? c.aa.ca.dc : "#555555",
                        v = c.aa.ca.$e ? c.aa.ca.$e : "#AAAAAA";
                    c.$c = g;
                    c.Mf = e;
                    d.append("<div id='" + c.container + "_parent' style='width:100%;height:" + e + "px;z-index:10" + (!eb.browser.mozilla || !eb.platform.mac || eb.platform.mac && (18 > parseFloat(eb.browser.version) || 33 < parseFloat(eb.browser.version)) ? "" : ";transform-style:preserve-3d;") + "'>" + (m ? "<div id='" + c.container + "_panelLeft' class='flowpaper_arrow' style='cursor:pointer;opacity: 0;margin-top:15px;-moz-border-radius-topleft: 10px;border-top-left-radius: 10px;-moz-border-radius-bottomleft: 10px;border-bottom-left-radius: 10px;position:relative;float:left;background-color:" + n + ";left:0px;top:0px;height:" + (e - 30) + "px;width:" + l + "px;margin-left:" + f + "px;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select: none;'><div style='position:relative;left:" + (l - (l - 0.4 * l)) / 2 + "px;top:" + (e / 2 - l) + "px' id='" + c.container + "_arrowleft' class='flowpaper_arrow'></div><div style='position:absolute;left:" + (l - (l - 0.55 * l)) / 2 + "px;bottom:0px;margin-bottom:10px;' id='" + c.container + "_arrowleftbottom' class='flowpaper_arrow flowpaper_arrow_start'></div><div style='position:absolute;left:" + (l - 0.8 * l) + "px;bottom:0px;width:2px;margin-bottom:10px;' id='" + c.container + "_arrowleftbottommarker' class='flowpaper_arrow flowpaper_arrow_start'></div></div>" : "") + "<div id='" + c.container + "' style='float:left;position:relative;height:" + e + "px;width:" + g + "px;margin-left:" + k + "px;z-index:10;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select: none;' class='flowpaper_twopage_container flowpaper_hidden'></div>" + (m ? "<div id='" + c.container + "_panelRight' class='flowpaper_arrow' style='cursor:pointer;opacity: 0;margin-top:15px;-moz-border-radius-topright: 10px;border-top-right-radius: 10px;-moz-border-radius-bottomright: 10px;border-bottom-right-radius: 10px;position:relative;float:left;background-color:" + n + ";left:0px;top:0px;height:" + (e - 30) + "px;width:" + l + "px;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select: none;'><div style='position:relative;left:" + (l - (l - 0.4 * l)) / 2 + "px;top:" + (e / 2 - l) + "px' id='" + c.container + "_arrowright' class='flowpaper_arrow'></div><div style='position:absolute;left:" + (l - (l - 0.55 * l)) / 2 + "px;bottom:0px;margin-bottom:10px;' id='" + c.container + "_arrowrightbottom' class='flowpaper_arrow flowpaper_arrow_end'></div><div style='position:absolute;left:" + ((l - (l - 0.55 * l)) / 2 + l - 0.55 * l) + "px;bottom:0px;width:2px;margin-bottom:10px;' id='" + c.container + "_arrowrightbottommarker' class='flowpaper_arrow flowpaper_arrow_end'></div></div>" : "") + "</div>");
                    g = Q(n);
                    jQuery(c.da + "_panelLeft").css("background-color", "rgba(" + g.r + "," + g.g + "," + g.b + "," + c.aa.ca.hg + ")");
                    jQuery(c.da + "_panelRight").css("background-color", "rgba(" + g.r + "," + g.g + "," + g.b + "," + c.aa.ca.hg + ")");
                    jQuery(c.da + "_arrowleft").aj(l - 0.4 * l, v);
                    jQuery(c.da + "_arrowright").jh(l - 0.4 * l, v);
                    c.aa.ca.gn && (jQuery(c.da + "_arrowleftbottom").aj(l - 0.55 * l, v), jQuery(c.da + "_arrowleftbottommarker").wo(l - 0.55 * l, v, jQuery(c.da + "_arrowleftbottom")), jQuery(c.da + "_arrowrightbottom").jh(l - 0.55 * l, v), jQuery(c.da + "_arrowrightbottommarker").xo(l - 0.55 * l, v, jQuery(c.da + "_arrowrightbottom")));
                    !c.aa.ca.Ta || c.aa.gf || c.aa.Zb || d.css("top", (d.height() - e) / 2.1 + "px");
                    c.aa.ca.ig || (jQuery(c.da + "_panelLeft").attr("id", c.da + "_panelLeft_disabled").css("visibility", "none"), jQuery(c.da + "_panelRight").attr("id", c.da + "_panelRight_disabled").css("visibility", "none"));
                    c.aa.PreviewMode && (jQuery(c.da + "_arrowleftbottom").hide(), jQuery(c.da + "_arrowleftbottommarker").hide(), jQuery(c.da + "_arrowrightbottom").hide(), jQuery(c.da + "_arrowrightbottommarker").hide());
                    jQuery(c.da).on(c.aa.ca.We ? "mouseup" : "mousedown", function(d) {
                        if (jQuery(d.target).hasClass("flowpaper_mark")) {
                            return !1;
                        }
                        var e = !0;
                        c.aa.ca.We && (c.Kl(), null == c.zb || d.pageX && d.pageY && d.pageX <= c.zb + 2 && d.pageX >= c.zb - 2 && d.pageY <= c.nc + 2 && d.pageY >= c.nc - 2 || (e = !1), c.zb = null, c.nc = null, c.sf && eb.browser.safari && (jQuery(".flowpaper_flipview_canvas_highres").show(), jQuery(".flowpaper_flipview_canvas").hide(), c.sf = !1));
                        if ((!c.aa.ca.We || e) && !c.aa.ca.Rf) {
                            var e = !1,
                                g = 0 < jQuery(d.target).parents(".flowpaper_page").children().find(".flowpaper_zine_page_left, .flowpaper_zine_page_left_noshadow").length;
                            c.Bf = g ? c.aa.ta - 2 : c.aa.ta - 1;
                            jQuery(d.target).hasClass("flowpaper_interactiveobject_" + c.ja) && (e = !0);
                            if (c.aa.Ea.turn("cornerActivated") || c.animating || jQuery(d.target).hasClass("turn-page-wrapper") || jQuery(d.target).hasClass("flowpaper_shadow") && jQuery(d.target).mf()) {
                                return;
                            }
                            if (c.aa.PreviewMode) {
                                c.aa.openFullScreen();
                                return;
                            }
                            eb.platform.mobilepreview || c.Xd() || (g = jQuery(c.da).Yf(d.pageX, d.pageY), e || c.aa.Fc || 1 != c.aa.scale ? !e && !c.aa.Fc && 1 < c.aa.scale && c.aa.Zoom(1, {
                                Cd: !0,
                                ic: g.x,
                                Ic: g.y
                            }) : c.aa.Zoom(2.5, {
                                Cd: !0,
                                ic: g.x,
                                Ic: g.y
                            }));
                            var f = {};
                            jQuery(jQuery(d.target).attr("class").split(" ")).each(function() {
                                "" !== this && (f[this] = this);
                            });
                            for (class_name in f) {
                                0 == class_name.indexOf("gotoPage") && c.gotoPage(parseInt(class_name.substr(class_name.indexOf("_") + 1)));
                            }
                        }
                        if (c.aa.renderer.vb && c.aa.renderer.sb && 1 < c.aa.scale) {
                            var h = 1 < c.la ? c.la - 1 : c.la;
                            setTimeout(function() {
                                1 < c.aa.scale ? (1 < c.la && c.aa.renderer.Vc(c.pages[h - 1]), c.aa.renderer.Vc(c.pages[h])) : (1 < c.la && c.aa.renderer.yc(c.pages[h - 1]), c.aa.renderer.yc(c.pages[h]));
                            }, 500);
                        }
                    });
                    jQuery(c.da + "_parent").on("mousemove", function(d) {
                        if (1 < c.aa.scale && !c.aa.Fc) {
                            if (c.aa.ca.We && "down" == c.aa.bh) {
                                c.zb || (c.zb = d.pageX, c.nc = d.pageY), !c.sf && eb.browser.safari && (jQuery(".flowpaper_flipview_canvas").show(), jQuery(".flowpaper_flipview_canvas_highres").hide(), c.sf = !0), eb.platform.touchdevice || c.ga(c.da + "_parent").mf() ? (c.ml && (c.Kl(), c.ml = !1), c.$j(d.pageX, d.pageY)) : (c.ia.scrollTo({
                                    left: jQuery(c.ia).scrollLeft() + (c.zb - d.pageX) + "px",
                                    top: jQuery(c.ia).scrollTop() + (c.nc - d.pageY) + "px"
                                }, 0, {
                                    axis: "xy"
                                }), c.zb = d.pageX + 3, c.nc = d.pageY + 3);
                            } else {
                                if (!c.aa.ca.We) {
                                    var e = c.ia.Yf(d.pageX, d.pageY);
                                    eb.platform.touchdevice || c.ga(c.da + "_parent").mf() || c.ia.scrollTo({
                                        left: d.pageX + "px",
                                        top: d.pageY + "px"
                                    }, 0, {
                                        axis: "xy"
                                    });
                                    d = e.x / jQuery(c.da + "_parent").width();
                                    e = e.y / jQuery(c.da + "_parent").height();
                                    c.Fg((jQuery(c.ia).width() + 150) * d - 20, (jQuery(c.ia).height() + 150) * e - 250);
                                }
                            }
                            c.aa.renderer.vb && c.aa.renderer.sb && !c.aa.ca.We && (e = 1 < c.la ? c.la - 1 : c.la, 1 < c.aa.scale ? (1 < c.la && c.aa.renderer.Vc(c.pages[e - 1]), c.aa.renderer.Vc(c.pages[e])) : (1 < c.la && c.aa.renderer.yc(c.pages[e - 1]), c.aa.renderer.yc(c.pages[e])));
                        }
                    });
                    jQuery(c.da + "_parent").on("touchmove", function(d) {
                        if (!eb.platform.ios && 2 == d.originalEvent.touches.length) {
                            d.preventDefault && d.preventDefault();
                            d.returnValue = !1;
                            var e = Math.sqrt((d.originalEvent.touches[0].pageX - d.originalEvent.touches[1].pageX) * (d.originalEvent.touches[0].pageX - d.originalEvent.touches[1].pageX) + (d.originalEvent.touches[0].pageY - d.originalEvent.touches[1].pageY) * (d.originalEvent.touches[0].pageY - d.originalEvent.touches[1].pageY)),
                                e = 2 * e;
                            if (null == c.sa) {
                                c.xb = c.aa.scale, c.jg = e;
                            } else {
                                c.aa.Ea.turn("setCornerDragging", !1);
                                1 > c.sa && (c.sa = 1);
                                2 < c.sa && !eb.platform.Id && (c.sa = 2);
                                c.aa.renderer.sb && 4 < c.sa && eb.platform.ipad && (c.sa = 4);
                                !c.aa.renderer.sb && 2 < c.sa && eb.platform.ipad && (c.sa = 2);
                                var g = 1 != c.aa.ta || c.aa.ca.Ta ? 0 : -(c.cd() / 4);
                                1 < c.sa && (c.ga(c.da + "_panelLeft").hide(), c.ga(c.da + "_panelRight").hide());
                                c.ga(c.da + "_parent").transition({
                                    x: g,
                                    y: c.aa.gc,
                                    scale: c.sa
                                }, 0, "ease", function() {});
                            }
                            c.sa = c.xb + (e - c.jg) / jQuery(c.da + "_parent").width();
                        }
                        if (1 < c.aa.scale || null != c.sa && 1 < c.sa) {
                            e = d.originalEvent.touches[0] || d.originalEvent.changedTouches[0], eb.platform.ios || 2 != d.originalEvent.touches.length ? c.zb || (c.zb = e.pageX, c.nc = e.pageY) : c.zb || (g = d.originalEvent.touches[1] || d.originalEvent.changedTouches[1], g.pageX > e.pageX ? (c.zb = e.pageX + (g.pageX - e.pageX) / 2, c.nc = e.pageY + (g.pageY - e.pageY) / 2) : (c.zb = g.pageX + (e.pageX - g.pageX) / 2, c.nc = g.pageY + (e.pageY - g.pageY) / 2)), c.sf || (jQuery(".flowpaper_flipview_canvas").show(), jQuery(".flowpaper_flipview_canvas_highres").hide(), c.sf = !0), c.$j(e.pageX, e.pageY), d.preventDefault();
                        }
                    });
                    jQuery(c.da + "_parent, " + c.da).on(!eb.platform.touchonlydevice || eb.platform.mobilepreview ? "mousedown" : "touchstart", function() {
                        c.Ef = (new Date).getTime();
                    });
                    jQuery(c.da + "_parent").on(!eb.platform.touchonlydevice || eb.platform.mobilepreview ? "mouseup" : "touchend", function(d) {
                        !c.aa.Yd || null != c.sa || c.xh || c.aa.Ea.turn("cornerActivated") || c.animating ? c.aa.Yd && 0 == c.aa.ca.Vb.position().top && c.aa.ca.Vb.animate({
                            opacity: 0,
                            top: "-" + c.aa.ca.Vb.height() + "px"
                        }, 300) : setTimeout(function() {
                            !jQuery(d.target).hasClass("flowpaper_arrow") && 1 == c.aa.scale && c.Ef && c.Ef > (new Date).getTime() - 1000 ? 0 == c.aa.ca.Vb.position().top ? c.aa.ca.Vb.animate({
                                opacity: 0,
                                top: "-" + c.aa.ca.Vb.height() + "px"
                            }, 300) : c.aa.ca.Vb.animate({
                                opacity: 1,
                                top: "0px"
                            }, 300) : c.Ef = null;
                        }, 600);
                        null != c.xb && (c.Wi = c.xb < c.sa, c.xb = null, c.jg = null, c.sa = null, c.zb = null, c.nc = null);
                        if (1 < c.aa.scale) {
                            var e = c.ga(c.da).css("transform") + "";
                            null != e && (e = e.replace("translate", ""), e = e.replace("(", ""), e = e.replace(")", ""), e = e.replace("px", ""), e = e.split(","), c.nd = parseFloat(e[0]), c.ke = parseFloat(e[1]), isNaN(c.nd) && (c.nd = 0, c.ke = 0));
                            c.zb && 1.9 < c.aa.scale && (jQuery(".flowpaper_flipview_canvas_highres").show(), jQuery(".flowpaper_flipview_canvas").hide());
                            c.aa.renderer.vb && c.aa.renderer.sb && 1.9 < c.aa.scale && (e = 1 < c.la ? c.la - 1 : c.la, 1 < c.la && c.aa.renderer.Vc(c.pages[e - 1]), c.aa.renderer.Vc(c.pages[e]));
                        } else {
                            c.nd = 0, c.ke = 0;
                        }
                        c.sf = !1;
                        c.zb = null;
                        c.nc = null;
                    });
                    jQuery(c.da + "_parent").on("gesturechange", function(d) {
                        d.preventDefault();
                        c.aa.ca.Rf || (null == c.sa && (c.xb = d.originalEvent.scale), c.aa.Ea.turn("setCornerDragging", !1), c.sa = c.aa.scale + (c.xb > c.aa.scale ? (d.originalEvent.scale - c.xb) / 2 : 4 * (d.originalEvent.scale - c.xb)), 1 > c.sa && (c.sa = 1), 2 < c.sa && !eb.platform.Id && (c.sa = 2), c.aa.renderer.sb && 4 < c.sa && eb.platform.ipad && (c.sa = 4), !c.aa.renderer.sb && 2 < c.sa && eb.platform.ipad && (c.sa = 2), d = 1 != c.aa.ta || c.aa.ca.Ta ? 0 : -(c.cd() / 4), c.ga(c.da + "_parent").transition({
                            x: d,
                            y: c.aa.gc,
                            scale: c.sa
                        }, 0, "ease", function() {}));
                    });
                    jQuery(c.da + "_parent").on("gestureend", function(d) {
                        d.preventDefault();
                        if (!c.aa.ca.Rf) {
                            c.fh = c.sa < c.aa.scale || c.fh;
                            c.aa.scale = c.sa;
                            for (d = 0; d < c.document.numPages; d++) {
                                c.kb(d) && (c.pages[d].scale = c.aa.scale, c.pages[d].Xa());
                            }
                            setTimeout(function() {
                                1 == c.aa.scale && (c.ga(c.da).transition({
                                    x: 0,
                                    y: 0
                                }, 0), c.aa.Ea.turn("setCornerDragging", !0));
                                for (var d = 0; d < c.document.numPages; d++) {
                                    c.kb(d) && (c.pages[d].Ga = !1);
                                }
                                c.qd();
                                jQuery(c).trigger("onScaleChanged");
                                c.sa = null;
                            }, 500);
                        }
                    });
                    jQuery(c.da + "_parent").on("mousewheel", function(d) {
                        if (!(c.Xd() || c.aa.PreviewMode || (c.aa.Ea.turn("cornerActivated") && c.aa.Ea.turn("stop"), c.aa.ca.Rf || c.aa.ca.Vm))) {
                            c.Bd || (c.Bd = 0);
                            0 < d.deltaY ? c.aa.scale + c.Bd + 2 * c.aa.document.ZoomInterval < c.aa.document.MaxZoomSize && (c.Bd = c.Bd + 2 * c.aa.document.ZoomInterval) : c.Bd = 1.2 < c.aa.scale + c.Bd - 3 * c.aa.document.ZoomInterval ? c.Bd - 3 * c.aa.document.ZoomInterval : -(c.aa.scale - 1);
                            null != c.ne && (window.clearTimeout(c.ne), c.ne = null);
                            1.1 <= c.aa.scale + c.Bd ? (c.aa.fisheye && c.aa.fisheye.animate({
                                opacity: 0
                            }, 0, function() {
                                c.aa.fisheye.hide();
                            }), c.ga(c.da + "_panelLeft").finish(), c.ga(c.da + "_panelRight").finish(), c.ga(c.da + "_panelLeft").fadeTo("fast", 0), c.ga(c.da + "_panelRight").fadeTo("fast", 0), c.aa.Ea.turn("setCornerDragging", !1)) : (c.ga(c.da + "_panelLeft").finish(), c.ga(c.da + "_panelRight").finish(), 1 < c.la ? c.ga(c.da + "_panelLeft").fadeTo("fast", 1) : c.ga(c.da + "_panelLeft").fadeTo("fast", 0), c.aa.ta < c.aa.getTotalPages() && c.ga(c.da + "_panelRight").fadeTo("fast", 1), c.ga(c.da).transition({
                                x: 0,
                                y: 0
                            }, 0), c.aa.fisheye && (c.aa.fisheye.show(), c.aa.fisheye.animate({
                                opacity: 1
                            }, 100)), c.zb = null, c.nc = null, c.nd = 0, c.ke = 0);
                            c.je = c.aa.scale + c.Bd;
                            1 > c.je && (c.je = 1);
                            if (!(eb.browser.mozilla && 30 > eb.browser.version) && 0 < jQuery(c.da).find(d.target).length) {
                                if (1 == c.je) {
                                    c.ga(c.da + "_parent").transition({
                                        transformOrigin: "0px 0px"
                                    }, 0);
                                } else {
                                    if (1 == c.aa.scale && c.ga(c.da + "_parent").transition({
                                            transformOrigin: "0px 0px"
                                        }, 0), c.aa.Ea.turn("setCornerDragging", !1), 0 < jQuery(c.da).has(d.target).length) {
                                        d = jQuery(c.da + "_parent").Yf(d.pageX, d.pageY);
                                        var e = c.ga(c.da + "_parent").css("transformOrigin").split(" ");
                                        2 <= e.length ? (e[0] = e[0].replace("px", ""), e[1] = e[1].replace("px", ""), c.zd = parseFloat(e[0]), c.Ad = parseFloat(e[1]), 0 == c.zd && (c.zd = d.x), 0 == c.Ad && (c.Ad = d.y)) : (c.zd = d.x, c.Ad = d.y);
                                        c.ga(c.da + "_parent").transition({
                                            transformOrigin: c.zd + "px " + c.Ad + "px"
                                        }, 0);
                                    }
                                }
                            }
                            c.ga(c.da + "_parent").transition({
                                scale: c.je
                            }, 0, "ease", function() {
                                c.aa.Ea.turn("setCornerDragging", !1);
                                jQuery(".flowpaper_flipview_canvas").show();
                                jQuery(".flowpaper_flipview_canvas_highres").hide();
                                window.clearTimeout(c.ne);
                                c.ne = setTimeout(function() {
                                    c.aa.scale = c.je;
                                    for (var d = c.Bd = 0; d < c.document.numPages; d++) {
                                        c.kb(d) && (c.pages[d].scale = c.aa.scale, c.pages[d].Xa());
                                    }
                                    1 == c.aa.scale && (c.ga(c.da).transition({
                                        x: 0,
                                        y: 0
                                    }, 0), c.aa.Ea.turn("setCornerDragging", !0));
                                    for (d = 0; d < c.document.numPages; d++) {
                                        c.kb(d) && (c.pages[d].Ga = !1);
                                    }
                                    c.qd();
                                    c.je = null;
                                    jQuery(c).trigger("onScaleChanged");
                                    jQuery(c.aa.ea).trigger("onScaleChanged", c.aa.scale / c.aa.document.MaxZoomSize);
                                }, 150);
                            });
                        }
                    });
                    jQuery(c.da + "_arrowleft, " + c.da + "_panelLeft").on(!eb.platform.touchonlydevice || eb.platform.mobilepreview ? "mousedown" : "touchstart", function(d) {
                        if (c.aa.ca.ig) {
                            return jQuery(d.target).hasClass("flowpaper_arrow_start") ? c.gotoPage(1) : c.previous(), !1;
                        }
                    });
                    jQuery(c.da + "_arrowright, " + c.da + "_panelRight").on(!eb.platform.touchonlydevice || eb.platform.mobilepreview ? "mousedown" : "touchstart", function(d) {
                        jQuery(d.target).hasClass("flowpaper_arrow_end") ? c.gotoPage(c.aa.getTotalPages()) : c.next();
                        return !1;
                    });
                    jQuery(d).css("overflow-y", "hidden");
                    jQuery(d).css("overflow-x", "hidden");
                    jQuery(d).css("-webkit-overflow-scrolling", "hidden");
                }
            },
            Dh: function(c, d) {
                c.Vk = d.append("<div id='" + c.container + "_play' onclick='$FlowPaper(\"" + c.ja + "\").openFullScreen()' class='abc' style='position:absolute;left:" + (d.width() / 2 - 20) + "px;top:" + (c.Mf / 2 - 25) + "px;width:" + c.$c + "px;height:" + c.Mf + "px;z-index:100;'></div>");
                jQuery("#" + c.container + "_play").jh(50, "#AAAAAA", !0);
            },
            uo: function(c, d) {
                d.find("#" + c.container + "_play").remove();
                c.Vk = null;
            },
            previous: function(c) {
                if ("FlipView" == c.aa.ba) {
                    var d = c.la - 1;
                    c.aa.renderer.$d && c.Ne(d);
                    1 != c.aa.scale ? c.Xa(1, !0, function() {
                        jQuery(c.aa.ea).trigger("onScaleChanged", 1 / c.aa.document.MaxZoomSize);
                        c.aa.turn("previous");
                    }) : c.aa.turn("previous");
                }
            },
            next: function(c) {
                if ("FlipView" == c.aa.ba) {
                    var d = c.la;
                    if (d < c.aa.getTotalPages() || d == c.aa.getTotalPages() && c.aa.ca.Ta) {
                        d++, c.aa.renderer.$d && c.Ne(d), 1 != c.aa.scale ? c.Xa(1, !0, function() {
                            jQuery(c.aa.ea).trigger("onScaleChanged", 1 / c.aa.document.MaxZoomSize);
                            c.aa.turn("next");
                        }) : c.aa.turn("next");
                    }
                }
            },
            Fg: function(c, d, e) {
                var g = c.ia.width(),
                    f = c.ia.height(),
                    m = null == c.je ? c.aa.scale : c.je;
                "FlipView" == c.aa.ba && 1 < m && !eb.browser.safari ? c.ga(c.da).transition({
                    x: -c.un(d, c.aa.scale),
                    y: -c.vn(e)
                }, 0) : "FlipView" == c.aa.ba && 1 < m && eb.browser.safari && jQuery(".flowpaper_viewer").scrollTo({
                    top: 0.9 * e / f * 100 + "%",
                    left: d / g * 100 + "%"
                }, 0, {
                    axis: "xy"
                });
            },
            vi: function(c) {
                c = c.ga(c.da + "_parent").css("transformOrigin") + "";
                return null != c ? (c = c.replace("translate", ""), c = c.replace("(", ""), c = c.replace(")", ""), c = c.split(" "), 1 < c.length ? {
                    left: parseFloat(c[0].replace("px", "")),
                    top: parseFloat(c[1].replace("px", ""))
                } : null) : null;
            },
            Ce: function(c) {
                !eb.platform.touchdevice && "FlipView" == c.aa.ba && 1 < c.aa.scale ? jQuery(".flowpaper_viewer").scrollTo({
                    left: "50%"
                }, 0, {
                    axis: "x"
                }) : eb.platform.touchdevice || "FlipView" != c.aa.ba || 1 != c.aa.scale || c.hc() || jQuery(".flowpaper_viewer").scrollTo({
                    left: "0%",
                    top: "0%"
                }, 0, {
                    axis: "xy"
                });
            }
        };
        return f;
    }(),
    X = window.Qq = X || {},
    Y = X;
Y.yh = {
    PI: Math.PI,
    gr: 1 / Math.PI,
    Cn: 0.5 * Math.PI,
    bn: 2 * Math.PI,
    Kr: Math.PI / 180,
    Jr: 180 / Math.PI
};
Y.Ud = {
    NONE: 0,
    LEFT: -1,
    RIGHT: 1,
    X: 1,
    Y: 2,
    Z: 4,
    Xi: 0,
    sq: 1,
    vq: 2
};
Y.Vl = "undefined" !== typeof Float32Array ? Float32Array : Array;
Y.Tp = "undefined" !== typeof Float64Array ? Float64Array : Array;
Y.Up = "undefined" !== typeof Int8Array ? Int8Array : Array;
Y.Pp = "undefined" !== typeof Int16Array ? Int16Array : Array;
Y.Rp = "undefined" !== typeof Int32Array ? Int32Array : Array;
Y.Vp = "undefined" !== typeof Uint8Array ? Uint8Array : Array;
Y.Qp = "undefined" !== typeof Uint16Array ? Uint16Array : Array;
Y.Sp = "undefined" !== typeof Uint32Array ? Uint32Array : Array;
Y.Bh = Y.Vl;
!0;
! function(f, c) {
    var d = f.Ej = ring.create({
        constructor: function(d, g) {
            this.x = d === c ? 0 : d;
            this.y = g === c ? 0 : g;
        },
        x: 0,
        y: 0,
        dispose: function() {
            this.y = this.x = null;
            return this;
        },
        serialize: function() {
            return {
                name: this.name,
                x: this.x,
                y: this.y
            };
        },
        Bb: function(c) {
            c && this.name === c.name && (this.x = c.x, this.y = c.y);
            return this;
        },
        clone: function() {
            return new d(this.x, this.y);
        }
    });
}(X);
! function(f, c) {
    var d = Math.sin,
        e = Math.cos,
        g = f.Ej,
        h = f.em = ring.create({
            constructor: function(d, e, g, f) {
                this.m11 = d === c ? 1 : d;
                this.m12 = e === c ? 0 : e;
                this.m21 = g === c ? 0 : g;
                this.m22 = f === c ? 1 : f;
            },
            m11: 1,
            m12: 0,
            m21: 0,
            m22: 1,
            dispose: function() {
                this.m22 = this.m21 = this.m12 = this.m11 = null;
                return this;
            },
            serialize: function() {
                return {
                    name: this.name,
                    m11: this.m11,
                    m12: this.m12,
                    m21: this.m21,
                    m22: this.m22
                };
            },
            Bb: function(c) {
                c && this.name === c.name && (this.m11 = c.m11, this.m12 = c.m12, this.m21 = c.m21, this.m22 = c.m22);
                return this;
            },
            reset: function() {
                this.m11 = 1;
                this.m21 = this.m12 = 0;
                this.m22 = 1;
                return this;
            },
            rotate: function(c) {
                var g = e(c);
                c = d(c);
                this.m11 = g;
                this.m12 = -c;
                this.m21 = c;
                this.m22 = g;
                return this;
            },
            scale: function(d, e) {
                this.m21 = this.m12 = 0;
                this.m22 = this.m11 = 1;
                d !== c && (this.m22 = this.m11 = d);
                e !== c && (this.m22 = e);
                return this;
            },
            multiply: function(c) {
                var d = this.m11,
                    e = this.m12,
                    g = this.m21,
                    f = this.m22,
                    h = c.m11,
                    p = c.m12,
                    q = c.m21;
                c = c.m22;
                this.m11 = d * h + e * q;
                this.m12 = d * p + e * c;
                this.m21 = g * h + f * q;
                this.m22 = g * p + f * c;
                return this;
            },
            Lr: function(c) {
                var d = c.x;
                c = c.y;
                return new g(this.m11 * d + this.m12 * c, this.m21 * d + this.m22 * c);
            },
            Gl: function(c) {
                var d = c.x,
                    e = c.y;
                c.x = this.m11 * d + this.m12 * e;
                c.y = this.m21 * d + this.m22 * e;
                return c;
            },
            clone: function() {
                return new h(this.m11, this.m12, this.m21, this.m22);
            }
        });
}(X);
! function(f, c) {
    var d = Math.sqrt,
        e = f.Bh,
        g = f.Vector3 = ring.create({
            constructor: function(d, g, f) {
                d && d.length ? this.qa = new e([d[0], d[1], d[2]]) : (d = d === c ? 0 : d, g = g === c ? 0 : g, f = f === c ? 0 : f, this.qa = new e([d, g, f]));
            },
            qa: null,
            dispose: function() {
                this.qa = null;
                return this;
            },
            serialize: function() {
                return {
                    name: this.name,
                    qa: this.qa
                };
            },
            Bb: function(c) {
                c && this.name === c.name && (this.qa = c.qa);
                return this;
            },
            Gd: function() {
                return new e(this.qa);
            },
            xk: function() {
                return this.qa;
            },
            setXYZ: function(c) {
                this.qa = new e(c);
                return this;
            },
            ul: function(c) {
                this.qa = c;
                return this;
            },
            clone: function() {
                return new g(this.qa);
            },
            Lq: function(c) {
                var d = this.qa;
                c = c.qa;
                return d[0] == c[0] && d[1] == c[1] && d[2] == c[2];
            },
            $r: function() {
                this.qa[0] = 0;
                this.qa[1] = 0;
                this.qa[2] = 0;
                return this;
            },
            negate: function() {
                var c = this.qa;
                return new g([-c[0], -c[1], -c[2]]);
            },
            sr: function() {
                var c = this.qa;
                c[0] = -c[0];
                c[1] = -c[1];
                c[2] = -c[2];
                return this;
            },
            add: function(c) {
                var d = this.qa;
                c = c.qa;
                return new g([d[0] + c[0], d[1] + c[1], d[2] + c[2]]);
            },
            wm: function(c) {
                var d = this.qa;
                c = c.qa;
                d[0] += c[0];
                d[1] += c[1];
                d[2] += c[2];
                return this;
            },
            Fr: function(c) {
                var d = this.qa;
                c = c.qa;
                return new g([d[0] - c[0], d[1] - c[1], d[2] - c[2]]);
            },
            Gr: function(c) {
                var d = this.qa;
                c = c.qa;
                d[0] -= c[0];
                d[1] -= c[1];
                d[2] -= c[2];
                return this;
            },
            multiplyScalar: function(c) {
                var d = this.qa;
                return new g([d[0] * c, d[1] * c, d[2] * c]);
            },
            pr: function(c) {
                var d = this.qa;
                d[0] *= c;
                d[1] *= c;
                d[2] *= c;
                return this;
            },
            multiply: function(c) {
                var d = this.qa;
                c = c.qa;
                return new g([d[0] * c[0], d[1] * c[1], d[2] * c[2]]);
            },
            qr: function(c) {
                var d = this.qa;
                c = c.qa;
                d[0] *= c[0];
                d[1] *= c[1];
                d[2] *= c[2];
                return this;
            },
            divide: function(c) {
                c = 1 / c;
                var d = this.qa;
                return new g([d[0] * c, d[1] * c, d[2] * c]);
            },
            Iq: function(c) {
                c = 1 / c;
                var d = this.qa;
                d[0] *= c;
                d[1] *= c;
                d[2] *= c;
                return this;
            },
            normalize: function() {
                var c = this.qa,
                    e = c[0],
                    f = c[1],
                    c = c[2],
                    l = e * e + f * f + c * c;
                0 < l && (l = 1 / d(l), e *= l, f *= l, c *= l);
                return new g([e, f, c]);
            },
            ao: function() {
                var c = this.qa,
                    e = c[0],
                    g = c[1],
                    f = c[2],
                    n = e * e + g * g + f * f;
                0 < n && (n = 1 / d(n), e *= n, g *= n, f *= n);
                c[0] = e;
                c[1] = g;
                c[2] = f;
                return this;
            },
            Tq: function() {
                var c = this.qa,
                    e = c[0],
                    g = c[1],
                    c = c[2];
                return d(e * e + g * g + c * c);
            },
            Dr: function(c) {
                this.ao();
                var d = this.qa;
                d[0] *= c;
                d[1] *= c;
                d[2] *= c;
                return this;
            },
            Jq: function(c) {
                var d = this.qa;
                c = c.qa;
                return d[0] * c[0] + d[1] * c[1] + d[2] * c[2];
            },
            Bq: function(c) {
                var d = this.qa,
                    e = c.qa;
                c = d[0];
                var g = d[1],
                    f = d[2],
                    v = e[0],
                    u = e[1],
                    e = e[2];
                d[0] = g * e - f * u;
                d[1] = f * v - c * e;
                d[2] = c * u - g * v;
                return this;
            },
            Hq: function(c) {
                var e = this.qa,
                    g = c.qa;
                c = e[0] - g[0];
                var f = e[1] - g[1],
                    e = e[2] - g[2];
                return d(c * c + f * f + e * e);
            },
            toString: function() {
                return "[" + this.qa[0] + " , " + this.qa[1] + " , " + this.qa[2] + "]";
            }
        });
    f.Vector3.ZERO = function() {
        return new g([0, 0, 0]);
    };
    f.Vector3.dot = function(c, d) {
        var e = c.qa,
            g = d.qa;
        return e[0] * g[0] + e[1] * g[1] + e[2] * g[2];
    };
    f.Vector3.equals = function(c, d) {
        var e = c.qa,
            g = d.qa;
        return e[0] == g[0] && e[1] == g[1] && e[2] == g[2];
    };
    f.Vector3.cross = function(c, d) {
        var e = c.qa,
            f = d.qa,
            n = e[0],
            v = e[1],
            e = e[2],
            u = f[0],
            p = f[1],
            f = f[2];
        return new g([v * f - e * p, e * u - n * f, n * p - v * u]);
    };
    f.Vector3.distance = function(c, e) {
        var g = c.qa,
            f = e.qa,
            n = g[0] - f[0],
            v = g[1] - f[1],
            g = g[2] - f[2];
        return d(n * n + v * v + g * g);
    };
    f.Vector3.Hr = function(c, d) {
        var e = c.qa,
            f = d.qa;
        return new g([e[0] + f[0], e[1] + f[1], e[2] + f[2]]);
    };
}(X);
! function(f, c) {
    var d = f.Ud,
        e = d.X,
        g = d.Y,
        h = d.Z,
        m = f.Vector3,
        k = f.Bh;
    f.Lf = ring.create({
        constructor: function(d) {
            this.qa = new k([0, 0, 0]);
            this.Mb = new k([0, 0, 0]);
            this.ratio = new k([0, 0, 0]);
            c !== d && null !== d && !1 !== d && this.sl(d);
        },
        ob: null,
        qa: null,
        Mb: null,
        ratio: null,
        dispose: function() {
            this.ratio = this.Mb = this.qa = this.ob = null;
            return this;
        },
        serialize: function() {
            return {
                ob: this.name,
                qa: this.Gd(),
                Mb: this.Mb,
                ratio: this.ratio
            };
        },
        Bb: function(c) {
            c && (this.setXYZ(c.qa), this.Mb = c.Mb, this.ratio = c.ratio);
            return this;
        },
        sl: function(c) {
            this.ob = c;
            return this;
        },
        Zq: function() {
            return new m(this.ratio);
        },
        Yq: function(c) {
            switch (c) {
                case e:
                    return this.ratio[0];
                case g:
                    return this.ratio[1];
                case h:
                    return this.ratio[2];
            }
            return -1;
        },
        Xq: function(c) {
            switch (c) {
                case e:
                    return this.Mb[0];
                case g:
                    return this.Mb[1];
                case h:
                    return this.Mb[2];
            }
            return 0;
        },
        Po: function(d, e, g) {
            d = d === c ? 0 : d;
            e = e === c ? 0 : e;
            g = g === c ? 0 : g;
            this.ratio = new k([d, e, g]);
            return this;
        },
        Ko: function(d, e, g) {
            d = d === c ? 0 : d;
            e = e === c ? 0 : e;
            g = g === c ? 0 : g;
            this.Mb = new k([d, e, g]);
            return this;
        },
        Gd: function() {
            return new k(this.qa);
        },
        xk: function() {
            return this.qa;
        },
        wk: function() {
            return this.qa[0];
        },
        yk: function() {
            return this.qa[1];
        },
        zk: function() {
            return this.qa[2];
        },
        setXYZ: function(c) {
            this.qa = new k(c);
            return this;
        },
        ul: function(c) {
            this.qa = c;
            return this;
        },
        setX: function(c) {
            this.qa[0] = c;
            return this;
        },
        setY: function(c) {
            this.qa[1] = c;
            return this;
        },
        setZ: function(c) {
            this.qa[2] = c;
            return this;
        },
        Rg: function(c) {
            switch (c) {
                case e:
                    return this.wk();
                case g:
                    return this.yk();
                case h:
                    return this.zk();
            }
            return 0;
        },
        setValue: function(c, d) {
            switch (c) {
                case e:
                    this.setX(d);
                    break;
                case g:
                    this.setY(d);
                    break;
                case h:
                    this.setZ(d);
            }
            return this;
        },
        reset: function() {
            this.setXYZ(this.Mb);
            return this;
        },
        collapse: function() {
            this.Mb = this.Gd();
            return this;
        },
        sk: function() {
            return new m(this.Gd());
        },
        ql: function(c) {
            this.setXYZ(c.qa);
        }
    });
}(X);
! function(f, c) {
    var d = f.Ud,
        e = d.X,
        g = d.Y,
        h = d.Z,
        m = Math.min,
        k = Math.max,
        l, n;
    l = function(c) {
        return c ? c.serialize() : c;
    };
    n = f.isWorker ? function(c) {
        return c && c.ob ? (new f.Lf).Bb(c) : c;
    } : function(c, d) {
        return c && c.ob ? this.vertices[d].Bb(c) : c;
    };
    f.ug = ring.create({
        constructor: function(d) {
            this.depth = this.height = this.width = this.pc = this.bc = this.ac = this.minZ = this.minY = this.minX = this.maxZ = this.maxY = this.maxX = null;
            this.vertices = [];
            this.faces = [];
            this.mesh = null;
            c !== d && this.kj(d);
        },
        maxX: null,
        maxY: null,
        maxZ: null,
        minX: null,
        minY: null,
        minZ: null,
        ac: null,
        bc: null,
        pc: null,
        width: null,
        height: null,
        depth: null,
        vertices: null,
        faces: null,
        mesh: null,
        dispose: function() {
            this.depth = this.height = this.width = this.pc = this.bc = this.ac = this.minZ = this.minY = this.minX = this.maxZ = this.maxY = this.maxX = null;
            this.ek();
            this.fk();
            this.mesh = null;
            return this;
        },
        fk: function() {
            var c, d;
            if (this.vertices) {
                for (d = this.vertices.length, c = 0; c < d; c++) {
                    this.vertices[c].dispose();
                }
            }
            this.vertices = null;
            return this;
        },
        ek: function() {
            var c, d;
            if (this.faces) {
                for (d = this.faces.length, c = 0; c < d; c++) {
                    this.faces[c].dispose();
                }
            }
            this.faces = null;
            return this;
        },
        serialize: function() {
            return {
                mesh: this.name,
                maxX: this.maxX,
                maxY: this.maxY,
                maxZ: this.maxZ,
                minX: this.minX,
                minY: this.minY,
                minZ: this.minZ,
                ac: this.ac,
                bc: this.bc,
                pc: this.pc,
                width: this.width,
                height: this.height,
                depth: this.depth,
                vertices: this.vertices ? this.vertices.map(l) : null,
                faces: null
            };
        },
        Bb: function(c) {
            c && (f.isWorker && (this.ek(), this.fk()), this.maxX = c.maxX, this.maxY = c.maxY, this.maxZ = c.maxZ, this.minX = c.minX, this.minY = c.minY, this.minZ = c.minZ, this.ac = c.ac, this.bc = c.bc, this.pc = c.pc, this.width = c.width, this.height = c.height, this.depth = c.depth, this.vertices = (c.vertices || []).map(n, this), this.faces = null);
            return this;
        },
        kj: function(c) {
            this.mesh = c;
            this.vertices = [];
            return this;
        },
        tk: function() {
            return this.vertices;
        },
        Rq: function() {
            return this.faces;
        },
        Qj: function() {
            var c = this.vertices,
                d = c.length,
                f = d,
                l, n, t, y, C, w, A, z, H, I, J;
            for (d && (l = c[0], n = l.Gd(), t = n[0], y = n[1], n = n[2], C = w = t, A = z = y, H = I = n); 0 <= --f;) {
                l = c[f], n = l.Gd(), t = n[0], y = n[1], n = n[2], l.Ko(t, y, n), C = m(C, t), A = m(A, y), H = m(H, n), w = k(w, t), z = k(z, y), I = k(I, n);
            }
            t = w - C;
            y = z - A;
            J = I - H;
            this.width = t;
            this.height = y;
            this.depth = J;
            this.minX = C;
            this.maxX = w;
            this.minY = A;
            this.maxY = z;
            this.minZ = H;
            this.maxZ = I;
            f = k(t, y, J);
            l = m(t, y, J);
            f == t && l == y ? (this.pc = g, this.bc = h, this.ac = e) : f == t && l == J ? (this.pc = h, this.bc = g, this.ac = e) : f == y && l == t ? (this.pc = e, this.bc = h, this.ac = g) : f == y && l == J ? (this.pc = h, this.bc = e, this.ac = g) : f == J && l == t ? (this.pc = e, this.bc = g, this.ac = h) : f == J && l == y && (this.pc = g, this.bc = e, this.ac = h);
            for (f = d; 0 <= --f;) {
                l = c[f], n = l.Gd(), l.Po((n[0] - C) / t, (n[1] - A) / y, (n[2] - H) / J);
            }
            return this;
        },
        Ao: function() {
            for (var c = this.vertices, d = c.length; 0 <= --d;) {
                c[d].reset();
            }
            this.update();
            return this;
        },
        Pm: function() {
            for (var c = this.vertices, d = c.length; 0 <= --d;) {
                c[d].collapse();
            }
            this.update();
            this.Qj();
            return this;
        },
        zn: function(c) {
            switch (c) {
                case e:
                    return this.minX;
                case g:
                    return this.minY;
                case h:
                    return this.minZ;
            }
            return -1;
        },
        Uq: function(c) {
            switch (c) {
                case e:
                    return this.maxX;
                case g:
                    return this.maxY;
                case h:
                    return this.maxZ;
            }
            return -1;
        },
        rk: function(c) {
            switch (c) {
                case e:
                    return this.width;
                case g:
                    return this.height;
                case h:
                    return this.depth;
            }
            return -1;
        },
        update: function() {
            return this;
        },
        xr: function() {
            return this;
        },
        Jl: function() {
            return this;
        }
    });
}(X);
! function(f) {
    var c = 0,
        d = f.Ud.NONE;
    f.Dj = ring.create({
        constructor: function(e) {
            this.id = ++c;
            this.ya = e || null;
            this.Xb = this.Be = d;
            this.enabled = !0;
        },
        id: null,
        ya: null,
        Be: null,
        Xb: null,
        enabled: !0,
        dispose: function(c) {
            !0 === c && this.ya && this.ya.dispose();
            this.Xb = this.Be = this.name = this.ya = null;
            return this;
        },
        serialize: function() {
            return {
                md: this.name,
                params: {
                    Be: this.Be,
                    Xb: this.Xb,
                    enabled: !!this.enabled
                }
            };
        },
        Bb: function(c) {
            c && this.name === c.md && (c = c.params, this.Be = c.Be, this.Xb = c.Xb, this.enabled = c.enabled);
            return this;
        },
        enable: function(c) {
            return arguments.length ? (this.enabled = !!c, this) : this.enabled;
        },
        zq: function(c) {
            this.Be = c || d;
            return this;
        },
        Cr: function(c) {
            this.Xb = c || d;
            return this;
        },
        mh: function(c) {
            this.ya = c;
            return this;
        },
        tk: function() {
            return this.ya ? this.ya.tk() : null;
        },
        Ze: function() {
            return this;
        },
        apply: function(c) {
            var d = this;
            d._worker ? d.bind("apply", function(f) {
                d.unbind("apply");
                f && f.gg && (d.ya.Bb(f.gg), d.ya.update());
                c && c.call(d);
            }).send("apply", {
                params: d.serialize(),
                gg: d.ya.serialize()
            }) : (d.Ze(), c && c.call(d));
            return d;
        },
        toString: function() {
            return "[Modifier " + this.name + "]";
        }
    });
}(X);
! function(f) {
    f.zh = ring.create({
        constructor: function() {
            this.Ni = f.ug;
            this.Ol = f.Lf;
        },
        Ni: null,
        Ol: null
    });
    var c = ring.create({
        yn: function(c) {
            if (arguments.length) {
                var e = c.Ni;
                return e ? new e : null;
            }
            return null;
        },
        An: function(c) {
            return c && c.md && f[c.md] ? new f[c.md] : null;
        },
        Sq: function(c) {
            return c && c.Nk && f[c.Nk] ? new f[c.Nk] : new f.zh;
        },
        Vq: function(c) {
            return c && c.mesh && f[c.mesh] ? (new f.ug).Bb(c) : new f.ug;
        },
        $q: function(c) {
            return c && c.ob && f[c.ob] ? (new f.Lf).Bb(c) : new f.Lf;
        }
    });
    f.Bj = new c;
}(X);
! function(f) {
    function c(c) {
        return c ? c.serialize() : c;
    }
    var d = f.Bj.yn,
        e = f.gm = ring.create({
            constructor: function(c, e) {
                this.ya = null;
                this.stack = [];
                this.Ji = f.isWorker ? new f.zh : c;
                this.ya = d(this.Ji);
                e && (this.ya.kj(e), this.ya.Qj());
            },
            Ji: null,
            ya: null,
            stack: null,
            dispose: function(c) {
                this.Ji = null;
                if (c && this.stack) {
                    for (; this.stack.length;) {
                        this.stack.pop().dispose();
                    }
                }
                this.stack = null;
                this.ya && this.ya.dispose();
                this.ya = null;
                return this;
            },
            serialize: function() {
                return {
                    md: this.name,
                    params: {
                        $n: this.stack.map(c)
                    }
                };
            },
            Bb: function(c) {
                if (c && this.name === c.md) {
                    c = c.params.$n;
                    var d = this.stack,
                        e;
                    if (c.length !== d.length) {
                        for (e = d.length = 0; e < c.length; e++) {
                            d.push(f.Bj.An(c[e]));
                        }
                    }
                    for (e = 0; e < d.length; e++) {
                        d[e] = d[e].Bb(c[e]).mh(this.ya);
                    }
                    this.stack = d;
                }
                return this;
            },
            mh: function(c) {
                this.ya = c;
                return this;
            },
            add: function(c) {
                c && (c.mh(this.ya), this.stack.push(c));
                return this;
            },
            Ze: function() {
                if (this.ya && this.stack && this.stack.length) {
                    var c = this.stack,
                        d = c.length,
                        e = this.ya,
                        f = 0;
                    for (e.Ao(); f < d;) {
                        c[f].enabled && c[f].Ze(), f++;
                    }
                    e.update();
                }
                return this;
            },
            apply: function(c) {
                var d = this;
                d._worker ? d.bind("apply", function(e) {
                    d.unbind("apply");
                    e && e.gg && (d.ya.Bb(e.gg), d.ya.update());
                    c && c.call(d);
                }).send("apply", {
                    params: d.serialize(),
                    gg: d.ya.serialize()
                }) : (d.Ze(), c && c.call(d));
                return d;
            },
            collapse: function() {
                this.ya && this.stack && this.stack.length && (this.apply(), this.ya.Pm(), this.stack.length = 0);
                return this;
            },
            clear: function() {
                this.stack && (this.stack.length = 0);
                return this;
            },
            Wq: function() {
                return this.ya;
            }
        });
    e.prototype.Mj = e.prototype.add;
}(X);
! function(f) {
    var c = f.Vector3;
    f.km = ring.create([f.Dj], {
        constructor: function(d, e, g) {
            this.$super();
            this.Sb = new c([d || 0, e || 0, g || 0]);
        },
        Sb: null,
        dispose: function() {
            this.Sb.dispose();
            this.Sb = null;
            this.$super();
            return this;
        },
        serialize: function() {
            return {
                md: this.name,
                params: {
                    Sb: this.Sb.serialize(),
                    enabled: !!this.enabled
                }
            };
        },
        Bb: function(c) {
            c && this.name === c.md && (c = c.params, this.Sb.Bb(c.Sb), this.enabled = !!c.enabled);
            return this;
        },
        Er: function() {
            var d = this.ya;
            this.Sb = new c(-(d.minX + 0.5 * d.width), -(d.minY + 0.5 * d.height), -(d.minZ + 0.5 * d.depth));
            return this;
        },
        Ze: function() {
            for (var c = this.ya.vertices, e = c.length, g = this.Sb, f; 0 <= --e;) {
                f = c[e], f.ql(f.sk().wm(g));
            }
            this.ya.Jl(g.negate());
            return this;
        }
    });
}(X);
! function(f, c) {
    var d = f.Ud.NONE,
        e = f.Ud.LEFT,
        g = f.Ud.RIGHT,
        h = f.em,
        m = Math.atan,
        k = Math.sin,
        l = Math.cos,
        n = f.yh.PI,
        v = f.yh.Cn,
        u = f.yh.bn,
        p = f.Ej;
    f.Xl = ring.create([f.Dj], {
        constructor: function(e, g, f) {
            this.$super();
            this.Xb = d;
            this.origin = this.height = this.width = this.Kd = this.min = this.max = 0;
            this.kd = this.jd = null;
            this.Ie = 0;
            this.Pd = !1;
            this.force = e !== c ? e : 0;
            this.offset = g !== c ? g : 0;
            f !== c ? this.lg(f) : this.lg(0);
        },
        force: 0,
        offset: 0,
        angle: 0,
        Ie: 0,
        max: 0,
        min: 0,
        Kd: 0,
        width: 0,
        height: 0,
        origin: 0,
        jd: null,
        kd: null,
        Pd: !1,
        dispose: function() {
            this.origin = this.height = this.width = this.Kd = this.min = this.max = this.Ie = this.angle = this.offset = this.force = null;
            this.jd && this.jd.dispose();
            this.kd && this.kd.dispose();
            this.Pd = this.kd = this.jd = null;
            this.$super();
            return this;
        },
        serialize: function() {
            return {
                md: this.name,
                params: {
                    force: this.force,
                    offset: this.offset,
                    angle: this.angle,
                    Ie: this.Ie,
                    max: this.max,
                    min: this.min,
                    Kd: this.Kd,
                    width: this.width,
                    height: this.height,
                    origin: this.origin,
                    jd: this.jd.serialize(),
                    kd: this.kd.serialize(),
                    Pd: this.Pd,
                    Xb: this.Xb,
                    enabled: !!this.enabled
                }
            };
        },
        Bb: function(c) {
            c && this.name === c.md && (c = c.params, this.force = c.force, this.offset = c.offset, this.angle = c.angle, this.Ie = c.Ie, this.max = c.max, this.min = c.min, this.Kd = c.Kd, this.width = c.width, this.height = c.height, this.origin = c.origin, this.jd.Bb(c.jd), this.kd.Bb(c.kd), this.Pd = c.Pd, this.Xb = c.Xb, this.enabled = !!c.enabled);
            return this;
        },
        lg: function(c) {
            this.angle = c;
            this.jd = (new h).rotate(c);
            this.kd = (new h).rotate(-c);
            return this;
        },
        mh: function(c) {
            this.$super(c);
            this.max = this.Pd ? this.ya.bc : this.ya.ac;
            this.min = this.ya.pc;
            this.Kd = this.Pd ? this.ya.ac : this.ya.bc;
            this.width = this.ya.rk(this.max);
            this.height = this.ya.rk(this.Kd);
            this.origin = this.ya.zn(this.max);
            this.Ie = m(this.width / this.height);
            return this;
        },
        Ze: function() {
            if (!this.force) {
                return this;
            }
            for (var c = this.ya.vertices, d = c.length, f = this.Xb, h = this.width, m = this.offset, w = this.origin, A = this.max, z = this.min, H = this.Kd, I = this.jd, J = this.kd, G = w + h * m, x = h / n / this.force, F = h / (x * u) * u, B, M, N, D, L = 1 / h; 0 <= --d;) {
                h = c[d], B = h.Rg(A), M = h.Rg(H), N = h.Rg(z), M = I.Gl(new p(B, M)), B = M.x, M = M.y, D = (B - w) * L, e === f && D <= m || g === f && D >= m || (D = v - F * m + F * D, B = k(D) * (x + N), D = l(D) * (x + N), N = B - x, B = G - D), M = J.Gl(new p(B, M)), B = M.x, M = M.y, h.setValue(A, B), h.setValue(H, M), h.setValue(z, N);
            }
            return this;
        }
    });
}(X);
! function(f) {
    var c = f.Ud,
        d = c.X,
        e = c.Y,
        g = c.Z,
        h = f.Vector3,
        m = f.Bh,
        c = f.Fj = ring.create([f.Lf], {
            constructor: function(c, d) {
                this.mesh = c;
                this.$super(d);
            },
            mesh: null,
            dispose: function() {
                this.mesh = null;
                this.$super();
                return this;
            },
            sl: function(c) {
                this.ob = c;
                this.Mb = new m([c.x, c.y, c.z]);
                this.qa = new m(this.Mb);
                return this;
            },
            Gd: function() {
                var c = this.ob;
                return new m([c.x, c.y, c.z]);
            },
            wk: function() {
                return this.ob.x;
            },
            yk: function() {
                return this.ob.y;
            },
            zk: function() {
                return this.ob.z;
            },
            setXYZ: function(c) {
                var d = this.ob;
                d.x = c[0];
                d.y = c[1];
                d.z = c[2];
                return this;
            },
            setX: function(c) {
                this.ob.x = c;
                return this;
            },
            setY: function(c) {
                this.ob.y = c;
                return this;
            },
            setZ: function(c) {
                this.ob.z = c;
                return this;
            },
            reset: function() {
                var c = this.ob,
                    d = this.Mb;
                c.x = d[0];
                c.y = d[1];
                c.z = d[2];
                return this;
            },
            collapse: function() {
                var c = this.ob;
                this.Mb = new m([c.x, c.y, c.z]);
                return this;
            },
            Rg: function(c) {
                var f = this.ob;
                switch (c) {
                    case d:
                        return f.x;
                    case e:
                        return f.y;
                    case g:
                        return f.z;
                }
                return 0;
            },
            setValue: function(c, f) {
                var h = this.ob;
                switch (c) {
                    case d:
                        h.x = f;
                        break;
                    case e:
                        h.y = f;
                        break;
                    case g:
                        h.z = f;
                }
                return this;
            },
            ql: function(c) {
                var d = this.ob;
                c = c.qa;
                d.x = c[0];
                d.y = c[1];
                d.z = c[2];
                return this;
            },
            sk: function() {
                var c = this.ob;
                return new h([c.x, c.y, c.z]);
            }
        });
    c.prototype.xk = c.prototype.Gd;
    c.prototype.ul = c.prototype.setXYZ;
}(X);
! function(f) {
    var c = f.Fj;
    f.fm = ring.create([f.ug], {
        constructor: function(c) {
            this.$super(c);
        },
        kj: function(d) {
            this.$super(d);
            var e = 0;
            d = this.mesh;
            for (var g = this.vertices, f = d.geometry.vertices, m = f.length, k, e = 0; e < m;) {
                k = new c(d, f[e]), g.push(k), e++;
            }
            this.faces = null;
            return this;
        },
        update: function() {
            var c = this.mesh.geometry;
            c.verticesNeedUpdate = !0;
            c.normalsNeedUpdate = !0;
            c.xq = !0;
            c.dynamic = !0;
            return this;
        },
        Jl: function(c) {
            var e = this.mesh.position;
            c = c.qa;
            e.x += c[0];
            e.y += c[1];
            e.z += c[2];
            return this;
        }
    });
}(X);
! function(f) {
    var c = ring.create([f.zh], {
        constructor: function() {
            this.Ni = f.fm;
            this.Ol = f.Fj;
        }
    });
    f.dm = new c;
}(X);
E = V.prototype;
E.Ik = function() {
    var f = this;
    if (f.aa.ba && (!f.aa.ba || 0 != f.aa.ba.length) && f.aa.ca.yb && !f.Di) {
        f.Di = !0;
        f.Ob = f.container + "_webglcanvas";
        var c = jQuery(f.da).offset(),
            d = f.aa.ka.width(),
            e = f.aa.ka.height(),
            g = c.left,
            c = c.top;
        f.jc = new THREE.Scene;
        f.Rd = jQuery(String.format("<canvas id='{0}' style='opacity:0;pointer-events:none;position:absolute;left:0px;top:0px;z-index:-1;width:100%;height:100%;'></canvas>", f.Ob, g, c));
        f.Rd.get(0).addEventListener("webglcontextlost", function(c) {
            f.Dd();
            c.preventDefault && c.preventDefault();
            f.Rd.remove();
            return !1;
        }, !1);
        f.ve = new THREE.WebGLRenderer({
            alpha: !0,
            antialias: !0,
            canvas: f.Rd.get(0)
        });
        f.ve.shadowMapType = THREE.PCFSoftShadowMap;
        f.Kb = new THREE.PerspectiveCamera(180 / Math.PI * Math.atan(e / 1398) * 2, d / e, 1, 1000);
        f.Kb.position.z = 700;
        f.jc.add(f.Kb);
        f.ve.setSize(d, e);
        0 == f.ve.context.getError() ? (jQuery(f.aa.ka).append(f.ve.domElement), f.WebGLObject = new THREE.Object3D, f.WebGLObject.scale.set(1, 1, 0.35), f.uc = new THREE.Object3D, f.WebGLObject.add(f.uc), f.jc.add(f.WebGLObject), f.Eb = new THREE.DirectionalLight(16777215), f.Eb.position.set(500, 0, 800), f.Eb.intensity = 0.1, f.jc.add(f.Eb), f.ad = new THREE.AmbientLight(16777215), f.ad.color.setRGB(1, 1, 1), f.jc.add(f.ad), f.Kb.lookAt(f.jc.position), f.Mi()) : f.Dd();
        f.Di = !1;
    }
};
E.Dd = function() {
    this.aa.ca.yb = !1;
    for (var f = 0; f < this.document.numPages; f++) {
        this.pages[f] && this.pages[f].mesh && this.pages[f].Wm();
    }
    this.jc && (this.WebGLObject && this.jc.remove(this.WebGLObject), this.Kb && this.jc.remove(this.Kb), this.ad && this.jc.remove(this.ad), this.Eb && this.jc.remove(this.Eb), this.Rd.remove());
    this.Ob = null;
};
E.il = function() {
    if (this.aa.ca.yb) {
        if (this.ee = [], this.Rd) {
            for (var f = 0; f < this.document.numPages; f++) {
                this.pages[f].mesh && this.pages[f].qg(!0);
            }
            var f = this.aa.ka.width(),
                c = this.aa.ka.height(),
                d = 180 / Math.PI * Math.atan(c / 1398) * 2;
            this.ve.setSize(f, c);
            this.Kb.fov = d;
            this.Kb.aspect = f / c;
            this.Kb.position.z = 700;
            this.Kb.position.x = 0;
            this.Kb.position.y = 0;
            this.Kb.updateProjectionMatrix();
            jQuery("#" + this.Ob).css("opacity", "0");
        } else {
            this.Ik();
        }
    }
};
E.Vo = function() {
    var f = jQuery(this.da).offset();
    jQuery(this.da).width();
    var c = jQuery(this.da).height();
    this.Kb.position.y = -1 * ((this.Rd.height() - c) / 2 - f.top) - this.aa.ka.offset().top;
    this.Kb.position.x = 0;
    this.En = !0;
};
E.Xd = function() {
    if (!this.aa.ca.yb) {
        return !1;
    }
    for (var f = this.Nf, c = 0; c < this.document.numPages; c++) {
        if (this.pages[c].Pb || this.pages[c].Qb) {
            f = !0;
        }
    }
    return f;
};
E.xn = function(f) {
    return f == this.Da ? 2 : f == this.Da - 2 ? 1 : f == this.Da + 2 ? 1 : 0;
};
E.zm = function() {
    for (var f = 0; f < this.document.numPages; f++) {
        this.pages[f].mesh && (f + 1 < this.la ? this.pages[f].Pb || this.pages[f].Qb || this.pages[f].mesh.rotation.y == -Math.PI || this.pages[f].Mn() : this.pages[f].Pb || this.pages[f].Qb || 0 == this.pages[f].mesh.rotation.y || this.pages[f].Nn(), this.pages[f].mesh.position.x = 0, this.pages[f].mesh.position.y = 0, this.pages[f].Pb || this.pages[f].Qb || (this.pages[f].mesh.position.z = this.xn(f)));
    }
};
E.vj = function(f, c) {
    var d = this;
    d.Ek = !1;
    var e = d.aa.getTotalPages();
    d.Nf = !0;
    d.zj = f;
    d.Jp = c;
    if (1 == d.aa.scale) {
        if ("next" == f && (d.Da ? d.Da = d.Da + 2 : d.Da = d.la - 1, 0 == e % 2 && d.Da == e - 2 && (d.Ek = !0), 0 != d.Da % 2 && (d.Da = d.Da - 1), d.Da >= e - 1 && 0 != e % 2)) {
            d.Nf = !1;
            return;
        }
        "previous" == f && (d.Da = d.Da ? d.Da - 2 : d.la - 3, 0 != d.Da % 2 && (d.Da += 1), d.Da >= e && (d.Da = e - 3));
        "page" == f && (d.Da = c - 3, f = d.Da >= d.la - 1 ? "next" : "previous");
        d.pages[d.Da] && !d.pages[d.Da].mesh && d.pages[d.Da].Fe();
        d.pages[d.Da - 2] && !d.pages[d.Da - 2].mesh && d.pages[d.Da - 2].Fe();
        d.pages[d.Da + 2] && !d.pages[d.Da + 2].mesh && d.pages[d.Da + 2].Fe();
        d.Vo();
        "0" == jQuery("#" + d.Ob).css("opacity") && jQuery("#" + d.Ob).animate({
            opacity: 0.5
        }, 50, function() {});
        jQuery("#" + d.Ob).animate({
            opacity: 1
        }, {
            duration: 60,
            always: function() {
                d.zm();
                d.Nf = !1;
                if ("next" == f && !d.pages[d.Da].Pb && !d.pages[d.Da].Qb) {
                    if (0 == d.Da || d.Ek) {
                        d.aa.Ea.css({
                            opacity: 0
                        }), d.uc.position.x = d.pages[d.Da].Ac / 2 * -1, jQuery(d.da + "_parent").transition({
                            x: 0
                        }, 0, "ease", function() {});
                    }
                    0 < d.Da && (d.uc.position.x = 0);
                    jQuery("#" + d.Ob).css("z-index", 99);
                    d.Sd || (d.Sd = !0, d.bj());
                    d.Eb.intensity = 0.1;
                    d.Eb.position.set(500, 0, 800);
                    d.ad.color.setRGB(1, 1, 1);
                    var c = d.vk();
                    (new TWEEN.Tween({
                        intensity: d.Eb.intensity
                    })).to({
                        intensity: 0.6
                    }, c / 2).easing(TWEEN.Easing.Sinusoidal.EaseInOut).onUpdate(function() {
                        d.Eb.intensity = this.intensity;
                        d.ad.color.setRGB(1 - this.intensity, 1 - this.intensity, 1 - this.intensity);
                    }).onComplete(function() {
                        (new TWEEN.Tween({
                            intensity: d.Eb.intensity
                        })).to({
                            intensity: 0
                        }, c / 2).easing(TWEEN.Easing.Sinusoidal.EaseInOut).onUpdate(function() {
                            d.Eb.intensity = this.intensity;
                            d.ad.color.setRGB(1 - this.intensity, 1 - this.intensity, 1 - this.intensity);
                        }).start();
                    }).start();
                    d.pages[d.Da].nn(d.uk());
                }
                "previous" == f && (d.Nf = !1, !d.pages[d.Da] || d.pages[d.Da].Qb || d.pages[d.Da].Pb || (0 == d.Da && (d.aa.Ea.css({
                    opacity: 0
                }), jQuery(d.da + "_parent").transition({
                    x: -(d.cd() / 4)
                }, 0, "ease", function() {}), d.uc.position.x = 0), 0 < d.Da && (d.uc.position.x = 0), jQuery("#" + d.Ob).css("z-index", 99), d.Sd || (d.Sd = !0, d.bj()), d.Eb.intensity = 0.1, d.Eb.position.set(-500, 0, 800), d.ad.color.setRGB(1, 1, 1), c = d.vk(), (new TWEEN.Tween({
                    intensity: d.Eb.intensity
                })).to({
                    intensity: 0.6
                }, c / 2).easing(TWEEN.Easing.Sinusoidal.EaseInOut).onUpdate(function() {
                    d.Eb.intensity = this.intensity;
                    d.ad.color.setRGB(1 - this.intensity, 1 - this.intensity, 1 - this.intensity);
                }).onComplete(function() {
                    (new TWEEN.Tween({
                        intensity: d.Eb.intensity
                    })).to({
                        intensity: 0
                    }, c / 2).easing(TWEEN.Easing.Sinusoidal.EaseInOut).onUpdate(function() {
                        d.Eb.intensity = this.intensity;
                        d.ad.color.setRGB(1 - this.intensity, 1 - this.intensity, 1 - this.intensity);
                    }).start();
                }).start(), d.pages[d.Da].pn(d.uk())));
            }
        });
    }
};
E.vk = function() {
    var f = 639.5;
    "very fast" == this.aa.ca.Gc && (f = 100);
    "fast" == this.aa.ca.Gc && (f = 300);
    "slow" == this.aa.ca.Gc && (f = 1700);
    "very slow" == this.aa.ca.Gc && (f = 2700);
    return f;
};
E.uk = function() {
    var f = 1.5;
    "very fast" == this.aa.ca.Gc && (f = 0.4);
    "fast" == this.aa.ca.Gc && (f = 0.7);
    "slow" == this.aa.ca.Gc && (f = 2.3);
    "very slow" == this.aa.ca.Gc && (f = 3.7);
    return f;
};
E.Fn = function() {
    this.aa.ca.Lg ? ("next" == this.zj && this.aa.Ea.turn("page", this.Da + 2, "instant"), "previous" == this.zj && this.aa.Ea.turn("page", this.Da, "instant")) : this.aa.Ea.turn(this.zj, this.Jp, "instant");
    this.Da = null;
};
E.bj = function() {
    var f, c = this;
    c.$b || (c.$b = []);
    3 > c.$b.length && (f = !0);
    if ((c.aa.ca.yb || c.Sd) && (c.Sd || f) && (c.Fd || (c.Fd = 0, c.eg = (new Date).getTime(), c.elapsedTime = 0), f = (new Date).getTime(), requestAnim(function() {
            c.bj();
        }), TWEEN.update(), c.ve.render(c.jc, c.Kb), c.Fd++, c.elapsedTime += f - c.eg, c.eg = f, 1000 <= c.elapsedTime && 4 > c.$b.length && (f = c.Fd, c.Fd = 0, c.elapsedTime -= 1000, c.$b.push(f), 3 == c.$b.length && !c.ni))) {
        c.ni = !0;
        for (var d = f = 0; 3 > d; d++) {
            f += c.$b[d];
        }
        25 > f / 3 && c.Dd();
    }
};
E.zf = function(f) {
    var c = this;
    if (f && !c.Uc) {
        c.Uc = f;
    } else {
        if (f && c.Uc && 10 > c.Uc + f) {
            c.Uc = c.Uc + f;
            return;
        }
    }
    c.ve && c.jc && c.Kb && c.En ? c.animating ? setTimeout(function() {
        c.zf();
    }, 500) : (0 < c.Uc ? (c.Uc = c.Uc - 1, requestAnim(function() {
        c.zf();
    })) : c.Uc = null, !c.Sd && 0 < c.Uc && c.ve.render(c.jc, c.Kb)) : c.Uc = null;
};
E.Mi = function() {
    var f = this;
    if (!f.aa.initialized) {
        setTimeout(function() {
            f.Mi();
        }, 1000);
    } else {
        if (!eb.platform.ios && (f.$b || (f.$b = []), f.Rd && f.aa.ca.yb && !f.Sd && 4 > f.$b.length)) {
            f.Fd || (f.Fd = 0, f.eg = (new Date).getTime(), f.elapsedTime = 0);
            var c = (new Date).getTime();
            requestAnim(function() {
                f.Mi();
            });
            f.Fd++;
            f.elapsedTime += c - f.eg;
            f.eg = c;
            c = f.Rd.get(0);
            if (c = c.getContext("webgl") || c.getContext("experimental-webgl")) {
                if (c.clearColor(0, 0, 0, 0), c.enable(c.DEPTH_TEST), c.depthFunc(c.LEQUAL), c.clear(c.COLOR_BUFFER_BIT | c.DEPTH_BUFFER_BIT), 1000 <= f.elapsedTime && 4 > f.$b.length && (c = f.Fd, f.Fd = 0, f.elapsedTime -= 1000, f.$b.push(c), 4 == f.$b.length && !f.ni)) {
                    f.ni = !0;
                    for (var d = c = 0; 3 > d; d++) {
                        c += f.$b[d];
                    }
                    25 > c / 3 && f.Dd();
                }
            } else {
                f.Dd();
            }
        }
    }
};
E.jo = function() {
    for (var f = this, c = !1, d = 0; d < f.document.numPages; d++) {
        if (f.pages[d].Pb || f.pages[d].Qb) {
            c = !0;
        }
    }
    c || (f.Nf = !1, 3 > f.$b ? setTimeout(function() {
        f.Xd() || (f.Sd = !1);
    }, 3000) : f.Sd = !1, f.Fn());
};
var na = function() {
        function f() {}
        f.prototype = {
            Mc: function(c, d) {
                return d.pages.la == d.pageNumber || d.la == d.pageNumber + 1;
            },
            sn: function(c, d, e) {
                var g = null != d.dimensions.tb ? d.dimensions.tb : d.dimensions.Ca;
                return !d.pages.hc() && c.vb && (!eb.browser.safari || eb.platform.touchdevice || eb.browser.safari && 7.1 > eb.browser.Gb) ? e : null != d.dimensions.tb && c.vb && d.aa.renderer.Ia ? d.pages.$c / (d.aa.gf ? 1 : 2) / g : d.ub && !d.aa.renderer.Ia ? d.pages.$c / 2 / d.aa.renderer.Ra[d.pageNumber].tb : c.vb && !d.ub && !d.aa.renderer.Ia && 1 < d.scale ? d.wi() / g : e;
            },
            Cm: function(c, d, e) {
                jQuery(d.ma + "_textoverlay").append(e);
            },
            Sj: function(c, d, e, g) {
                var f = c.qo == g && !d.aa.renderer.vb;
                !e || e && e.attr("id") == c.po || (c.qo = g, c.po = e.attr("id"), c.ro != e.css("top") || c.so != d.pageNumber ? (null != c.rd && c.rd.remove(), c.ro = e.css("top"), c.rd = e.wrap(jQuery(String.format("<div class='flowpaper_pageword flowpaper_pageword_" + c.ja + "' style='{0};border-width: 3px;border-style:dotted;border-color: #ee0000;'></div>", e.attr("style")))).parent(), c.rd.css({
                    "margin-left": "-3px",
                    "margin-top": "-4px",
                    "z-index": "11"
                }), jQuery(d.Ha).append(c.rd)) : f ? (c.rd.css("width", c.rd.width() + e.width()), jQuery(c.rd.children()[0]).width(c.rd.width())) : (c.rd.css("left", e.css("left")), c.rd.append(e)), e.css({
                    left: "0px",
                    top: "0px"
                }), e.addClass("flowpaper_selected"), e.addClass("flowpaper_selected_default"), e.addClass("flowpaper_selected_searchmatch"), c.so = d.pageNumber);
            }
        };
        return f;
    }(),
    ka = function() {
        function f() {}
        f.prototype = {
            create: function(c, d) {
                if ("FlipView" == c.aa.ba && (c.Um = 10 < c.pages.te ? c.pages.te : 10, !(c.Hi || c.aa.renderer.$d && !c.ib && c.pageNumber > c.Um + 6))) {
                    c.Sc = jQuery("<div class='flowpaper_page flowpaper_page_zoomIn' id='" + c.Rc + "' style='" + c.getDimensions() + ";z-index:2;background-size:cover;background-color:#ffffff;margin-bottom:0px;'><div id='" + c.pa + "' style='height:100%;width:100%;'></div></div>");
                    c.pages.aa.Ea && c.aa.renderer.$d ? c.pages.aa.Ea.turn("addPage", c.Sc, c.pageNumber + 1) : jQuery(d).append(c.Sc);
                    var e = c.Kg() * c.ab,
                        g = c.Va() / e;
                    null != c.dimensions.tb && c.vb && c.aa.renderer.Ia && (g = c.pages.$c / 2 / e);
                    c.Li = g;
                    c.uf(g);
                    c.Hi = !0;
                    c.ib = !0;
                    c.aa.renderer.Hd(c);
                    c.show();
                    c.height = c.ga(c.Ha).height();
                    c.wl();
                    c.Fe && c.Fe();
                }
            },
            Kn: function(c) {
                var d = c.Kg() * c.ab,
                    e = c.Va() / d;
                null != c.dimensions.tb && c.vb && c.aa.renderer.Ia && (e = c.pages.$c / 2 / d);
                c.Li = e;
                c.uf(e);
            },
            de: function(c) {
                return c.pages.de() / (c.aa.ca.Ta ? 1 : 2);
            },
            hf: function(c) {
                return c.pages.hf();
            },
            getDimensions: function(c) {
                if ("FlipView" == c.aa.ba) {
                    return c.ia.width(), "position:absolute;left:0px;top:0px;width:" + c.Va(c) + ";height:" + c.Za(c);
                }
            },
            Va: function(c) {
                if ("FlipView" == c.aa.ba) {
                    return c.pages.$c / (c.aa.ca.Ta ? 1 : 2) * c.scale;
                }
            },
            ui: function(c) {
                if ("FlipView" == c.aa.ba) {
                    return c.pages.$c / (c.aa.ca.Ta ? 1 : 2) * 1;
                }
            },
            wi: function(c) {
                if ("FlipView" == c.aa.ba) {
                    return c.pages.$c / (c.aa.ca.Ta ? 1 : 2);
                }
            },
            Za: function(c) {
                if ("FlipView" == c.aa.ba) {
                    return c.pages.Mf * c.scale;
                }
            },
            ti: function(c) {
                if ("FlipView" == c.aa.ba) {
                    return 1 * c.pages.Mf;
                }
            },
            Kc: function() {
                return 0;
            },
            Mc: function(c) {
                var d = c.aa.ca.yb;
                if ("FlipView" == c.aa.ba) {
                    return c.pages.la >= c.pageNumber - (d ? 3 : 2) && c.pages.la <= c.pageNumber + (d ? 5 : 4);
                }
            },
            unload: function(c) {
                var d = c.ma;
                0 == jQuery(d).length && (d = jQuery(c.Sc).find(c.ma));
                (c.pageNumber < c.pages.la - 15 || c.pageNumber > c.pages.la + 15) && c.Sc && !c.Sc.parent().hasClass("turn-page-wrapper") && !c.zc && 0 != c.pageNumber && (jQuery(d).find("*").unbind(), jQuery(d).find("*").remove(), c.initialized = !1, c.oc = !1);
            }
        };
        U.prototype.Xf = function() {
            return eb.platform.touchdevice ? "FlipView" == this.aa.ba ? !this.aa.ca.Ta && window.devicePixelRatio && 1 < window.devicePixelRatio ? 1.9 : 2.6 : 1 : "FlipView" == this.aa.ba ? 2 : 1;
        };
        return f;
    }();
E = U.prototype;
E.Fe = function() {
    var f = this;
    if (0 == f.pageNumber % 2 && 1 == f.scale && f.aa.ca.yb) {
        if (f.mesh && f.pages.uc.remove(f.mesh), f.pages.Ob || f.pages.Ik(), f.pages.Di) {
            setTimeout(function() {
                f.Fe();
            }, 200);
        } else {
            f.Ac = f.Va(f);
            f.Ld = f.Za(f);
            f.angle = 0.25 * Math.PI * this.Ac / this.Ld;
            for (var c = 0; 6 > c; c++) {
                c != f.Ba.jb || f.$a[f.Ba.jb] ? c != f.Ba.back || f.$a[f.Ba.back] ? f.$a[c] || c == f.Ba.back || c == f.Ba.jb || (f.$a[c] = new THREE.MeshPhongMaterial({
                    color: f.ho
                }), f.$a[c].name = "edge") : (f.$a[f.Ba.back] = new THREE.MeshPhongMaterial({
                    map: null,
                    overdraw: !0
                }), f.$a[f.Ba.back].name = "back", f.Jj(f.pageNumber, f.Ac, f.Ld, f.Ba.back, function(c) {
                    f.Qc || (f.$a[f.Ba.back].map = THREE.ImageUtils.loadTexture(c));
                })) : (f.$a[f.Ba.jb] = new THREE.MeshPhongMaterial({
                    map: null,
                    overdraw: !0
                }), f.$a[f.Ba.jb].name = "front", f.Jj(f.pageNumber, f.Ac, f.Ld, f.Ba.jb, function(c) {
                    f.Qc || (f.$a[f.Ba.jb].map = THREE.ImageUtils.loadTexture(c));
                }));
            }
            f.mesh = new THREE.Mesh(new THREE.BoxGeometry(f.Ac, f.Ld, 0.1, 10, 10, 1), new THREE.MeshFaceMaterial(f.$a));
            f.mesh.overdraw = !0;
            f.ya = new X.gm(X.dm, f.mesh);
            f.Sb = new X.km(f.Ac / 2, 0, 0);
            f.ya.Mj(f.Sb);
            f.ya.collapse();
            f.Wb = new X.Xl(0, 0, 0);
            f.Wb.Xb = X.Ud.LEFT;
            f.Ld > f.Ac && (f.Wb.Pd = !0);
            f.ya.Mj(f.Wb);
            f.pages.uc.add(f.mesh);
            f.mesh.position.x = 0;
            f.mesh.position.z = -1;
            f.Yg && (f.mesh.rotation.y = -Math.PI);
            f.Zg && (f.mesh.rotation.y = 0);
        }
    }
};
E.Jj = function(f, c, d, e, g) {
    var h = "image/jpeg",
        m = 0.95,
        k = this,
        l = new Image,
        n;
    k.pages.ee || (k.pages.ee = []);
    h = "image/jpeg";
    m = m || 0.92;
    e == k.Ba.jb && k.pages.ee[k.Ba.jb] ? g(k.pages.ee[k.Ba.jb]) : e == k.Ba.back && k.pages.ee[k.Ba.back] ? g(k.pages.ee[k.Ba.back]) : (l.onload = function() {
        var v = document.createElement("canvas");
        v.width = c;
        v.height = d;
        n = v.getContext("2d");
        n.Hf = n.mozImageSmoothingEnabled = n.imageSmoothingEnabled = !0;
        n.fillStyle = "white";
        n.fillRect(0, 0, v.width, v.height);
        n.drawImage(l, v.width / 2 + (k.Kc() - 10), v.height / 2, 24, 8);
        if (k.aa.Uf) {
            if (e == k.Ba.back) {
                n.beginPath();
                n.strokeStyle = "transparent";
                n.rect(0.65 * c, 0, 0.35 * c, d);
                var u = n.createLinearGradient(0, 0, c, 0);
                u.addColorStop(0.93, "rgba(255, 255, 255, 0)");
                u.addColorStop(0.96, "rgba(170, 170, 170, 0.05)");
                u.addColorStop(1, "rgba(125, 124, 125, 0.3)");
                n.fillStyle = u;
                n.fill();
                n.stroke();
                n.closePath();
                u = v.toDataURL(h, m);
                k.pages.ee[k.Ba.back] = u;
                g(u);
            }
            e == k.Ba.jb && 0 != f && (n.beginPath(), n.strokeStyle = "transparent", n.rect(0, 0, 0.35 * c, d), u = n.createLinearGradient(0, 0, 0.07 * c, 0), u.addColorStop(0.07, "rgba(125, 124, 125, 0.3)"), u.addColorStop(0.93, "rgba(255, 255, 255, 0)"), n.fillStyle = u, n.fill(), n.stroke(), n.closePath(), u = v.toDataURL(h, m), k.pages.ee[k.Ba.jb] = u, g(u));
        }
    }, l.src = k.gd);
};
E.qg = function(f) {
    if (this.mesh && this.Qc || f) {
        this.Il(), this.ya.dispose(), this.Sb.dispose(), this.ya = this.mesh = this.Sb = null, this.$a = [], this.Yc = this.resources = null, this.Fe(), this.Qc = !1;
    }
};
E.Wm = function() {
    this.mesh && this.Qc && (this.Il(), this.ya.dispose(), this.Sb.dispose(), this.ya = this.mesh = this.Sb = null, this.$a = [], this.resources = null, this.Qc = !1);
};
E.Il = function() {
    var f = this.mesh;
    if (f) {
        for (var c = 0; c < f.material.materials.length; c++) {
            f.material.materials[c].map && f.material.materials[c].map.dispose(), f.material.materials[c].dispose();
        }
        f.geometry.dispose();
        this.pages.uc.remove(f);
    }
};
E.ie = function(f, c) {
    var d = this;
    if (d.aa.ca.yb && !d.Qc && 0 == d.pageNumber % 2 && 1 == d.aa.scale && 1 == d.scale) {
        d.Qc = !0;
        d.$g = !0;
        d.Ac = d.Va(d);
        d.Ld = d.Za(d);
        d.angle = 0.25 * Math.PI * this.Ac / this.Ld;
        for (var e = 0; 6 > e; e++) {
            e == d.Ba.jb ? d.loadResources(d.pageNumber, function() {
                d.cl(d.pageNumber, d.Ba.jb, f, d.Ac, d.Ld, function(c) {
                    d.$a[d.Ba.jb] && (d.$a[d.Ba.jb].map = null);
                    d.pages.zf(2);
                    d.$a[d.Ba.jb] = new THREE.MeshPhongMaterial({
                        map: THREE.ImageUtils.loadTexture(c),
                        overdraw: !0
                    });
                    d.mesh && d.mesh.material.materials && d.mesh.material.materials && (d.mesh.material.materials[d.Ba.jb] = d.$a[d.Ba.jb]);
                    d.$g && d.$a[d.Ba.jb] && d.$a[d.Ba.jb].map && d.$a[d.Ba.back] && d.$a[d.Ba.back].map && (d.$g = !1, d.pages.zf(2));
                });
            }) : e == d.Ba.back && d.loadResources(d.pageNumber + 1, function() {
                d.cl(d.pageNumber + 1, d.Ba.back, c, d.Ac, d.Ld, function(c) {
                    d.$a[d.Ba.back] && (d.$a[d.Ba.back].map = null);
                    d.pages.zf(2);
                    d.$a[d.Ba.back] = new THREE.MeshPhongMaterial({
                        map: THREE.ImageUtils.loadTexture(c),
                        overdraw: !0
                    });
                    d.mesh && d.mesh.material.materials && d.mesh.material.materials && (d.mesh.material.materials[d.Ba.back] = d.$a[d.Ba.back]);
                    d.$g && d.$a[d.Ba.jb] && d.$a[d.Ba.jb].map && d.$a[d.Ba.back] && d.$a[d.Ba.back].map && (d.$g = !1, d.pages.zf(2));
                });
            });
        }
    }
};
E.loadResources = function(f, c) {
    var d = this,
        e = d.pages.getPage(f);
    if (e) {
        if (null == e.resources && (e.resources = [], d.aa.za[f])) {
            for (var g = 0; g < d.aa.za[f].length; g++) {
                if ("image" == d.aa.za[f][g].type || "video" == d.aa.za[f][g].type) {
                    var h = d.aa.za[f][g].src,
                        m = new Image;
                    m.loaded = !1;
                    m.setAttribute("data-x", d.aa.za[f][g].Ai ? d.aa.za[f][g].Ai : d.aa.za[f][g].Pl);
                    m.setAttribute("data-y", d.aa.za[f][g].Bi ? d.aa.za[f][g].Bi : d.aa.za[f][g].Ql);
                    m.setAttribute("data-width", d.aa.za[f][g].width);
                    m.setAttribute("data-height", d.aa.za[f][g].height);
                    jQuery(m).bind("load", function() {
                        this.loaded = !0;
                        d.kl(f) && c();
                    });
                    m.src = h;
                    e.resources.push(m);
                }
            }
        }
        d.kl(f) && c();
    }
};
E.kl = function(f) {
    var c = !0;
    f = this.pages.getPage(f);
    if (!f.resources) {
        return !1;
    }
    for (var d = 0; d > f.resources.length; d++) {
        f.resources[d].loaded || (c = !1);
    }
    return c;
};
E.Mn = function() {
    this.mesh.rotation.y = -Math.PI;
    this.page.Pb = !1;
    this.page.Yg = !0;
    this.page.Qb = !1;
    this.page.Zg = !1;
};
E.Nn = function() {
    this.mesh.rotation.y = 0;
    this.page.Pb = !1;
    this.page.Zg = !0;
    this.page.Qb = !1;
    this.page.Yg = !1;
};
E.cl = function(f, c, d, e, g, h) {
    var m = "image/jpeg",
        k = 0.95,
        l = this,
        n = new Image,
        v, u, p, q, m = 0 == d.indexOf("data:image/png") ? "image/png" : "image/jpeg",
        k = k || 0.92;
    n.src = d;
    jQuery(n).bind("load", function() {
        p = this.naturalWidth;
        q = this.naturalHeight;
        v = document.createElement("canvas");
        p /= 2;
        q /= 2;
        if (p < e || q < g) {
            p = e, q = g;
        }
        p < d.width && (p = d.width);
        q < d.height && (q = d.height);
        v.width = p;
        v.height = q;
        u = v.getContext("2d");
        u.clearRect(0, 0, v.width, v.height);
        u.fillStyle = "rgba(255, 255, 255, 1)";
        u.fillRect(0, 0, p, q);
        u.drawImage(n, 0, 0, p, q);
        var r = p / (l.Kg() * l.ab),
            t = l.pages.getPage(f).resources;
        if (t) {
            for (var y = 0; y < t.length; y++) {
                u.drawImage(t[y], parseFloat(t[y].getAttribute("data-x")) * r, parseFloat(t[y].getAttribute("data-y")) * r, parseFloat(t[y].getAttribute("data-width")) * r, parseFloat(t[y].getAttribute("data-height")) * r);
            }
        }
        l.aa.Uf && (c == l.Ba.back && (u.beginPath(), u.strokeStyle = "transparent", u.rect(0.65 * p, 0, 0.35 * p, q), r = u.createLinearGradient(0, 0, p, 0), r.addColorStop(0.93, "rgba(255, 255, 255, 0)"), r.addColorStop(0.96, "rgba(170, 170, 170, 0.05)"), r.addColorStop(1, "rgba(125, 124, 125, 0.3)"), u.fillStyle = r, u.fill(), u.stroke(), u.closePath()), c == l.Ba.jb && 0 != f && (u.beginPath(), u.strokeStyle = "transparent", u.rect(0, 0, 0.35 * p, q), r = u.createLinearGradient(0, 0, 0.07 * p, 0), r.addColorStop(0.07, "rgba(125, 124, 125, 0.3)"), r.addColorStop(0.93, "rgba(255, 255, 255, 0)"), u.fillStyle = r, u.fill(), u.stroke(), u.closePath()));
        h(v.toDataURL(m, k));
    });
};
E.nn = function(f) {
    var c = this;
    f && (c.duration = f);
    f = 415 * c.duration;
    var d = 315 * c.duration,
        e = 210 * c.duration;
    c.Pb || c.Qb || (c.Pb = !0, c.Wb.lg(-0.15), c.Wb.force = 0, c.Wb.offset = 0, c.ya.apply(), c.to = {
        angle: c.mesh.rotation.y,
        t: -1,
        If: 0,
        page: c,
        force: c.force,
        offset: c.offset
    }, (new TWEEN.Tween(c.to)).to({
        angle: -Math.PI,
        If: 1,
        t: 1
    }, f).easing(TWEEN.Easing.Sinusoidal.EaseInOut).onUpdate(c.Yk).start(), (new TWEEN.Tween(c.to)).to({
        force: 0.6
    }, d).easing(TWEEN.Easing.Quadratic.EaseInOut).onUpdate(c.xf).onComplete(function() {
        (new TWEEN.Tween(c.to)).to({
            force: 0,
            offset: 1
        }, e).easing(TWEEN.Easing.Sinusoidal.EaseOut).onUpdate(c.xf).onComplete(c.nk).start();
    }).start(), (new TWEEN.Tween(c.to)).to({
        offset: 0.1
    }, d).easing(TWEEN.Easing.Quadratic.EaseOut).onUpdate(c.xf).start(), c.mesh.position.z = 2);
};
E.pn = function(f) {
    var c = this;
    f && (c.duration = f);
    f = 415 * c.duration;
    var d = 315 * c.duration,
        e = 210 * c.duration;
    c.Qb || c.Pb || (c.Qb = !0, c.Wb.lg(-0.15), c.Wb.force = 0, c.Wb.offset = 0, c.ya.apply(), c.to = {
        angle: c.mesh.rotation.y,
        t: -1,
        If: 0,
        page: c,
        force: c.force,
        offset: c.offset
    }, (new TWEEN.Tween(c.to)).to({
        angle: 0,
        If: 1,
        t: 1
    }, f).easing(TWEEN.Easing.Sinusoidal.EaseInOut).onUpdate(c.Yk).start(), (new TWEEN.Tween(c.to)).to({
        force: -0.6
    }, d).easing(TWEEN.Easing.Quadratic.EaseInOut).onUpdate(c.xf).onComplete(function() {
        (new TWEEN.Tween(c.to)).to({
            force: 0,
            offset: 1
        }, e).easing(TWEEN.Easing.Sinusoidal.EaseOut).onUpdate(c.xf).onComplete(c.nk).start();
    }).start(), (new TWEEN.Tween(c.to)).to({
        offset: 0.1
    }, d).easing(TWEEN.Easing.Quadratic.EaseOut).onUpdate(c.xf).start(), c.mesh.position.z = 2);
};
E.Yk = function() {
    this.page.mesh.rotation.y = this.angle;
    this.page.Pb && 0 == this.page.pageNumber && (this.page.pages.uc.position.x = (1 - this.If) * this.page.pages.uc.position.x);
    this.page.Qb && 0 == this.page.pageNumber && (this.page.pages.uc.position.x = (1 - this.If) * this.page.pages.uc.position.x - this.If * this.page.Ac * 0.5);
};
E.xf = function() {
    this.page.Wb.force = this.force;
    this.page.Wb.offset = this.offset;
    this.page.ya.apply();
};
E.nk = function() {
    this.page.Pb ? (this.page.Pb = !1, this.page.Yg = !0, this.page.Qb = !1, this.page.Zg = !1, this.page.mesh.position.z = 2) : this.page.Qb && (this.page.Pb = !1, this.page.Zg = !0, this.page.Qb = !1, this.page.Yg = !1, this.page.mesh.position.z = 2);
    this.page.Wb.force = 0;
    this.page.Wb.lg(0);
    this.page.Wb.offset = 0;
    this.page.ya.apply();
    this.page.pages.jo();
};
var oa = "undefined" == typeof window;
oa && (window = []);
var FlowPaperViewer_HTML = window.FlowPaperViewer_HTML = function() {
    function f(c) {
        window.zine = !0;
        this.config = c;
        this.Gi = this.config.instanceid;
        this.document = this.config.document;
        this.ja = this.config.rootid;
        this.ia = {};
        this.Zc = this.ka = null;
        this.selectors = {};
        this.ba = "Portrait";
        this.Fb = null != c.document.InitViewMode && "undefined" != c.document.InitViewMode && "" != c.document.InitViewMode ? c.document.InitViewMode : window.zine ? "FlipView" : "Portrait";
        this.initialized = !1;
        this.se = "flowpaper_selected_default";
        this.hb = {};
        this.za = [];
        this.rm = "data:image/gif;base64,R0lGODlhIwAjAIQAAJyenNTS1Ly+vOzq7KyurNze3Pz6/KSmpMzKzNza3PTy9LS2tOTm5KSipNTW1MTCxOzu7LSytOTi5Pz+/KyqrMzOzAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQJDQAWACwAAAAAIwAjAAAF/uAkjiQ5LBQALE+ilHAMG5IKNLcdJXI/Ko7KI2cjAigSHwxYCVQqOGMu+jAoRYNmc2AwPBGBR6SYo0CUkmZgILMaEFFb4yVLBxzW61sOiORLWQEJf1cTA3EACEtNeIWAiGwkDgEBhI4iCkULfxBOkZclcCoNPCKTAaAxBikqESJeFZ+pJAFyLwNOlrMTmTaoCRWluyWsiRMFwcMwAjoTk0nKtKMLEwEIDNHSNs4B0NkTFUUTwMLZQzeuCXffImMqD4ZNurMGRTywssO1NnSn2QZxXGHZEi0BkXKn5jnad6SEgiflUgVg5W1ElgoVL6WRV6dJxit2PpbYmCCfjAGTMTAqNPHkDhdVKJ3EusTEiaAEEgZISJDSiQM6oHA9Gdqy5ZpoBgYU4HknQYEBQNntCgEAIfkECQ0AFQAsAAAAACMAIwCEnJ6c1NLU7OrsxMLErK6s3N7c/Pr8pKak3Nrc9PL0zMrMtLa05ObkpKKk1NbU7O7stLK05OLk/P78rKqszM7MAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABf6gJI5kaZ5oKhpCgTiBgxQCEyCqmjhU0P8+BWA4KeRKO6AswoggEAtAY9hYGI4SAVCQOEWG4Aahq4r0AoIcojENP1Lm2PVoULSlk3lJe9NjBXcAAyYJPQ5+WBIJdw0RJTABiIlZYAATJA8+aZMmQmA4IpCcJwZ3CysUFJujJQFhXQI+kqwGlTgIFKCsJhBggwW5uycDYBASMI7CrVQAEgEKDMrLYMcBydIiFMUSuLrYxFLGCDHYI71Dg3yzowlSQwoSBqmryq5gZKLSBhNgpyJ89Fhpa+MN0roj7cDkIVEoGKsHU9pEQKSFwrVEgNwBMOalx8UcntosRGEmV8ATITSpkElRMYaAWSyYWTp5IomPGwgiCHACg8KdAQYOmoiVqmgqHz0ULFgwcRcLFzBk0FhZTlgIACH5BAkNABcALAAAAAAjACMAhJyenNTS1Ly+vOzq7KyurNze3MzKzPz6/KSmpNza3MTGxPTy9LS2tOTm5KSipNTW1MTCxOzu7LSytOTi5MzOzPz+/KyqrAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAX+YCWOZGmeaCoeQ5E8wZMUw6He1fJQAe/3vccCZ9L9ZJPGJJHwURJDYmXwG0RLhwbMQBkQJ7yAFzcATm7gmE162CkgDxQ1kFhLRQEHAMAo8h52dxUNAHoOCSUwAYGCC3t7DnYRPWOCJAGQABQjipYnFo8SKxRdniZ5j0NlFIymjo+ITYimJhKPBhUFT7QmAqEVMGe8l499AQYNwyQUjxbAAcLKFZh7fbLSIr6Fogkx2BW2e7hzrZ6ve4gHpJW8D3p7UZ3DB+8AEmtz7J6Y7wEkiuWIDHgEwBmJBaRmWYpgCJ0JKhSiSRlQD4CAcmkkqjhA7Z2FgBXAPNFXQgcCgoU4rsghFaOGiAUBAgiw9e6dBJUpjABJYAClz4sgH/YgRdNnwTqmWBSAYFSCP2kHIFiQwMAAlKAVQgAAIfkECQ0AFgAsAAAAACMAIwAABf7gJI5kaZ5oKhpDkTiBkxSDod6T4lQB7/c9hwJn0v1kEoYkkfBVEkPiZPAbREsGBgxRGRAlvIAXNwBKbuCYTWrYVc4oaiCxlooSvXFJwXPU7XcVFVcjMAF/gBMGPQklEHmJJlRdJIaRJzAOIwaCepcjcmtlFYifnA8FgY2fWAcADV4FT6wlFQ0AAAITMHC0IgG4ABQTAQgMviMVwQ27Ab2+wLjMTavID8ELE3iayBMRwQ9TPKWRBsEAjZyUvrbBUZa0Bre4EaA8npEIr7jVzYefA84NI8FnViQIt+Y9EzFpIQ4FCXE9IJemgAxyJQZQEIhxggQEB24d+FckwDdprzrwmXCAkt4DIA9OLhMGAYe8c/POoZwXoWMJCRtx7suJi4JDHAkoENUJIAIdnyoUJIh5K8ICBAEIoQgBACH5BAkNABYALAAAAAAjACMAAAX+4CSOZGmeaCoaQ5E4gZMUg6Hek+JUAe/3PYcCZ9L9ZBKGJJHwVRJD4mTwG0RLBgYMURkQJbyAFzcASm7gmE1q2FXOKGogsZaKEr1xScFz1O13FRVXIzABf4ATBj0JJRB5iSZUXSSGkScwDiMGgnqXI3JrZRWIn5yUE02NnyZNBSIFT6ytcyIwcLMjYJoTAQgMuSRytgG4wWmBq8Gptcy8yzuvUzyllwwLCGOnnp8JDQAAeggHAAizBt8ADeYiC+nslwHg38oL6uDcUhDzABQkEuDmQUik4Fs6ZSIEBGzQYKCUAenARTBhgELAfvkoIlgIIEI1iBwjBCC0KUC6kxk4RSiweFHiAyAPIrQERyHlpggR7828l+5BtRMSWHI02JKChJ8oDCTAuTNgBDqsFPiKYK/jAyg4QgAAIfkECQ0AFgAsAAAAACMAIwAABf7gJI5kaZ5oKhpDkTiBkxSDod6T4lQB7/c9hwJn0v1kEoYkkfBVEkPiZPAbREsGBgxRGRAlvIAXNwBKbuCYTWrYVc4oaiCxlooSvXFJwXPU7XcVFVcjMAF/gBMGPQklEHmJJlRdJIaRJzAOIwaCepcjcmtlFYifnJQTTY2fJk0Fig8ECKytcxMPAAANhLRgmhS5ABW0JHITC7oAAcQjaccNuQ/Md7YIwRHTEzuvCcEAvJeLlAreq7ShIhHBFKWJO5oiAcENs6yjnsC5DZ6A4vAj3eZBuNQkADgB3vbZUTDADYMTBihAS3YIhzxdCOCcUDBxnpCNCfJBE9BuhAJ1CTEBRBAARABKb8pwGEAIs+M8mBFKtspXE6Y+c3YQvPSZKwICnTgUJBAagUKEBQig4AgBACH5BAkNABYALAAAAAAjACMAAAX+4CSOZGmeaCoaQ5E4gZMUg6Hek+JUAe/3PYcCZ9L9ZBKGJJHwVRJD4mTwG0RLBgYMURkQJbyAFzcASm7gmE1q2FXOp3YvsZaKEr0xSQIAUAJ1dncVFVciFH0ADoJYcyQJAA19CYwlVF0jEYkNgZUTMIs5fZIInpY8NpCJnZ4GhF4PkQARpiZNBRMLiQ+1JXiUsgClvSNgi4kAAcQjVMoLksLLImm5u9ITvxMCibTSO7gV0ACGpgZ5oonKxM1run0UrIw7odji6qZlmCuIiXqM5hXoTUPWgJyUJgEMRoDWoIE/IgUIMYjDLxGCeCck9IBzYoC4UYBUDIDxBqMIBRUxxUV4AAQQC5L6bhiIRRDZKEJBDKqQUHFUsAYPAj60k4DCx00FTNpRkODBQj8RhqIIAQAh+QQJDQAWACwAAAAAIwAjAAAF/uAkjmRpnmgqGkOROIGTFIOhqtKyVAHv90AH5FYyCAANJE8mYUgSiYovoSBOIBQkADmomlg9HuOmSG63D+IAKEkZsloAwjoxOKTtE+KMzNMnCT0DJhBbSQ2DfyNRFV4rC2YAiYorPQkkCXwBlCUDUpOQWxQ2nCQwDiIKhnKlnTw2DpGOrXWfEw9nFLQlUQUTC1oCu5gBl6GswyISFaiaySKem3Fzz8ubwGjPgMW3ZhHad76ZZ6S7BoITqmebw9GkEWcN5a13qCIJkdStaxWTE3Bb/Ck6x6yEBD4NZv2JEkDhhCPxHN4oIGXMlyyRAszD0cOPiQGRDF1SMQBGBQkbM0soAKjF4wgWJvtZMQAv0gIoEgY8MdnDgcQUCQAiCCMlTIAAAukYSIBgwAAop2Z00UYrBAAh+QQJDQAXACwAAAAAIwAjAIScnpzU0tS8vrzs6uysrqzc3tzMysz8+vykpqTc2tzExsT08vS0trTk5uSkoqTU1tTEwsTs7uy0srTk4uTMzsz8/vysqqwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAF/mAljqS4JAbDWNBRvjA8SUANOLVQDG7smxAbTkgIUAKPyO91EAyHtpohQTlSEouliXaLSiCGQLZyGBiPjeUCEQVYsD2Y+TjxHWhQwyFuf1TrMAJRDgNaJQlGhYddN4qGJFQUYyMWUY6PIwdGCSQBjAaYclWOBDYWfKEjD0gmUJypLwNHLglRk7CZoxUKQxKouBVUBRUMNgLAL4icDEOgyCQTFA8VlTUBzySy18VS2CPR20MQ3iLKFUE1EuQVfsO1NrfAmhSFC4zX2No9XG7eftMiKAjBB2yOowMOoMTDNA/giABQAMGiIuYFNwevUhWokgZGAAgQAkh8NMHISCbROq5c8jFgFYUJv2JVCRCAB4wyLulhWmCkZ4IEEwZMSODSyIOFWiKcqcL0DM2VqcoUKLDqQYIdSNc9CgEAIfkECQ0AFgAsAAAAACMAIwAABf7gJI6kqDjPsgDA8iRKKc+jUSwNC+Q520QJmnAioeh2x56OIhmSDCuk8oisGpwTCGXKojwQAcQjQm0EnIpej4KIyQyIBq/SpBmMR8R1aEgEHAF0NAI+OwNYTwkVAQwyElUNh4gligFuI3gskpNPgQ4kCXl7nCQDi5tkPKOkJA4VnxMKeawzA4FXoT2rtCIGpxMPOhG8M64FEys5D8QyfkFVCMwlEq8TR2fSI6ZnmdHZItRnOCzY384TDKrfIsbgDwG7xAaBknAVm9Lbo4Dl0q6wIrbh42XrXglX8JjNq1ZCQaAgxCpdKlVBEK0CFRvRCFeHk4RAHTdWTDCQxgBAdDLiyTC1yMEAlQZOBjI46cSiRQkSSBggIQFKTxMnFaxI9OaiACVJxSzg80+CAgOCrmMVAgAh+QQJDQAWACwAAAAAIwAjAAAF/uAkjqSoJM8CAMvyOEopz2QRrWsD6PmSGLSghJLb4YxFiiRYMgiKxygPtwAyIcTpKvJABBCPG07XiECCCu0OYbCSFAjisXGWGeQ8NnNiQEwbFG4jKkYNA4JMA1oPJQl/A3syaWNLIndFkJEyA0cRIw5FCJo0CFQjATgUo0GlDaIiEkYJq0EDAQFWAwgRlbQzfRWZCRWzvkEOAcUFycZBw8UOFb3NJRIBDiIBwdQzDBUBIsgF3DLW4BPP5I3EIgnX6iTiIgPfiNQG2pkGFdvw9BVukJ1TJ5AEvQCZuB1MGO6WvVX4KmAroYBfsWbDAsTYxG/aqgLfGAj55jGSNWl7OCRYZFgLmbSHJf5dO/RrgMt+mhRE05YsgYQBEhK41AbDmC1+SPlp+4aQnIEBBYReS1BgwEZ43EIAACH5BAkNABcALAAAAAAjACMAhJyenNTS1Ly+vOzq7KyurNze3MzKzPz6/KSmpNza3MTGxPTy9LS2tOTm5KSipNTW1MTCxOzu7LSytOTi5MzOzPz+/KyqrAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAX+YCWOpLgkEMNYqpEsZSyPRyABOODgOy5Ns2Dl0dPljDwcBCakMXrF4hEpODSHUpwFYggYIBbpTsIMQo6WQJl0yjrWpQmkZ7geDFGJNTagUAITcEIDUgIxC38Je1ckhEcJJQ8BFIuMjWgkEZMDljMBOQ4BI5KinTIHRRIiB36cpjIBRTADk5WvIwuPFQkUkLcyNzh1Bb2/Mgw5qpJAxiWfOgwVXg3NzjkWQ4DVbDl1vL7bIgYSEFYJAQ/hIwkuIn0BtsasAa6sFK7bfZSjAaXbpI3+4DNG616kfvE61aCQrgSiYsZ4qZGhj9krYhSozZjwx6KlCZM8yuDYa2CQAZIzKExIWEIfugEJD6CcZNDSggd/EiWYMGBCgpSTHgi6UtCP0Zx/6FWTWeAnugQFBgxV1ykEADs%3D";
        this.Gj = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAANCAYAAABy6+R8AAAAAXNSR0IArs4c6QAAAAZiS0dEAFEAUQBRjSJ44QAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9wCCgEMO6ApCe8AAAFISURBVCjPfZJBi49hFMV/521MUYxEsSGWDDWkFKbkA/gAajaytPIFLKx8BVkodjP5AINGU0xZKAslC3Ys2NjP+VnM++rfPzmb23065z6de27aDsMwVD0C3AfOAYeB38BP9fEwDO/aMgwDAAFQDwKbwC9gZxScUM8Al5M8SPJ0Eu5JYV0FeAZcBFaAxSSPkjwHnrQ9Pf1E22XVsX5s+1m9o54cB9J2q+361KM+VN+ot9uqrjIH9VJbpz7qOvAeuAIcSnJzThA1SXaTBGAAvgCrwEvg0yxRXUhikrOjZ1RQz7uHFfUu/4C60fb16G9hetxq+1a9Pkdears2Dt1Rj87mdAx4BfwAttWvSQ4AV9W1aYlJtoFbmQJTjwP3gAvAIlDgG7CsXvu7uWQzs+cxmj0F7Fd3k3wfuRvqDWAfM+HxP6hL6oe2tn3xB7408HFbpc41AAAAAElFTkSuQmCC";
        this.Ch = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAKT2lDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQSoghodkVUcERRUUEG8igiAOOjoCMFVEsDIoK2AfkIaKOg6OIisr74Xuja9a89+bN/rXXPues852zzwfACAyWSDNRNYAMqUIeEeCDx8TG4eQuQIEKJHAAEAizZCFz/SMBAPh+PDwrIsAHvgABeNMLCADATZvAMByH/w/qQplcAYCEAcB0kThLCIAUAEB6jkKmAEBGAYCdmCZTAKAEAGDLY2LjAFAtAGAnf+bTAICd+Jl7AQBblCEVAaCRACATZYhEAGg7AKzPVopFAFgwABRmS8Q5ANgtADBJV2ZIALC3AMDOEAuyAAgMADBRiIUpAAR7AGDIIyN4AISZABRG8lc88SuuEOcqAAB4mbI8uSQ5RYFbCC1xB1dXLh4ozkkXKxQ2YQJhmkAuwnmZGTKBNA/g88wAAKCRFRHgg/P9eM4Ors7ONo62Dl8t6r8G/yJiYuP+5c+rcEAAAOF0ftH+LC+zGoA7BoBt/qIl7gRoXgugdfeLZrIPQLUAoOnaV/Nw+H48PEWhkLnZ2eXk5NhKxEJbYcpXff5nwl/AV/1s+X48/Pf14L7iJIEyXYFHBPjgwsz0TKUcz5IJhGLc5o9H/LcL//wd0yLESWK5WCoU41EScY5EmozzMqUiiUKSKcUl0v9k4t8s+wM+3zUAsGo+AXuRLahdYwP2SycQWHTA4vcAAPK7b8HUKAgDgGiD4c93/+8//UegJQCAZkmScQAAXkQkLlTKsz/HCAAARKCBKrBBG/TBGCzABhzBBdzBC/xgNoRCJMTCQhBCCmSAHHJgKayCQiiGzbAdKmAv1EAdNMBRaIaTcA4uwlW4Dj1wD/phCJ7BKLyBCQRByAgTYSHaiAFiilgjjggXmYX4IcFIBBKLJCDJiBRRIkuRNUgxUopUIFVIHfI9cgI5h1xGupE7yAAygvyGvEcxlIGyUT3UDLVDuag3GoRGogvQZHQxmo8WoJvQcrQaPYw2oefQq2gP2o8+Q8cwwOgYBzPEbDAuxsNCsTgsCZNjy7EirAyrxhqwVqwDu4n1Y8+xdwQSgUXACTYEd0IgYR5BSFhMWE7YSKggHCQ0EdoJNwkDhFHCJyKTqEu0JroR+cQYYjIxh1hILCPWEo8TLxB7iEPENyQSiUMyJ7mQAkmxpFTSEtJG0m5SI+ksqZs0SBojk8naZGuyBzmULCAryIXkneTD5DPkG+Qh8lsKnWJAcaT4U+IoUspqShnlEOU05QZlmDJBVaOaUt2ooVQRNY9aQq2htlKvUYeoEzR1mjnNgxZJS6WtopXTGmgXaPdpr+h0uhHdlR5Ol9BX0svpR+iX6AP0dwwNhhWDx4hnKBmbGAcYZxl3GK+YTKYZ04sZx1QwNzHrmOeZD5lvVVgqtip8FZHKCpVKlSaVGyovVKmqpqreqgtV81XLVI+pXlN9rkZVM1PjqQnUlqtVqp1Q61MbU2epO6iHqmeob1Q/pH5Z/YkGWcNMw09DpFGgsV/jvMYgC2MZs3gsIWsNq4Z1gTXEJrHN2Xx2KruY/R27iz2qqaE5QzNKM1ezUvOUZj8H45hx+Jx0TgnnKKeX836K3hTvKeIpG6Y0TLkxZVxrqpaXllirSKtRq0frvTau7aedpr1Fu1n7gQ5Bx0onXCdHZ4/OBZ3nU9lT3acKpxZNPTr1ri6qa6UbobtEd79up+6Ynr5egJ5Mb6feeb3n+hx9L/1U/W36p/VHDFgGswwkBtsMzhg8xTVxbzwdL8fb8VFDXcNAQ6VhlWGX4YSRudE8o9VGjUYPjGnGXOMk423GbcajJgYmISZLTepN7ppSTbmmKaY7TDtMx83MzaLN1pk1mz0x1zLnm+eb15vft2BaeFostqi2uGVJsuRaplnutrxuhVo5WaVYVVpds0atna0l1rutu6cRp7lOk06rntZnw7Dxtsm2qbcZsOXYBtuutm22fWFnYhdnt8Wuw+6TvZN9un2N/T0HDYfZDqsdWh1+c7RyFDpWOt6azpzuP33F9JbpL2dYzxDP2DPjthPLKcRpnVOb00dnF2e5c4PziIuJS4LLLpc+Lpsbxt3IveRKdPVxXeF60vWdm7Obwu2o26/uNu5p7ofcn8w0nymeWTNz0MPIQ+BR5dE/C5+VMGvfrH5PQ0+BZ7XnIy9jL5FXrdewt6V3qvdh7xc+9j5yn+M+4zw33jLeWV/MN8C3yLfLT8Nvnl+F30N/I/9k/3r/0QCngCUBZwOJgUGBWwL7+Hp8Ib+OPzrbZfay2e1BjKC5QRVBj4KtguXBrSFoyOyQrSH355jOkc5pDoVQfujW0Adh5mGLw34MJ4WHhVeGP45wiFga0TGXNXfR3ENz30T6RJZE3ptnMU85ry1KNSo+qi5qPNo3ujS6P8YuZlnM1VidWElsSxw5LiquNm5svt/87fOH4p3iC+N7F5gvyF1weaHOwvSFpxapLhIsOpZATIhOOJTwQRAqqBaMJfITdyWOCnnCHcJnIi/RNtGI2ENcKh5O8kgqTXqS7JG8NXkkxTOlLOW5hCepkLxMDUzdmzqeFpp2IG0yPTq9MYOSkZBxQqohTZO2Z+pn5mZ2y6xlhbL+xW6Lty8elQfJa7OQrAVZLQq2QqboVFoo1yoHsmdlV2a/zYnKOZarnivN7cyzytuQN5zvn//tEsIS4ZK2pYZLVy0dWOa9rGo5sjxxedsK4xUFK4ZWBqw8uIq2Km3VT6vtV5eufr0mek1rgV7ByoLBtQFr6wtVCuWFfevc1+1dT1gvWd+1YfqGnRs+FYmKrhTbF5cVf9go3HjlG4dvyr+Z3JS0qavEuWTPZtJm6ebeLZ5bDpaql+aXDm4N2dq0Dd9WtO319kXbL5fNKNu7g7ZDuaO/PLi8ZafJzs07P1SkVPRU+lQ27tLdtWHX+G7R7ht7vPY07NXbW7z3/T7JvttVAVVN1WbVZftJ+7P3P66Jqun4lvttXa1ObXHtxwPSA/0HIw6217nU1R3SPVRSj9Yr60cOxx++/p3vdy0NNg1VjZzG4iNwRHnk6fcJ3/ceDTradox7rOEH0x92HWcdL2pCmvKaRptTmvtbYlu6T8w+0dbq3nr8R9sfD5w0PFl5SvNUyWna6YLTk2fyz4ydlZ19fi753GDborZ752PO32oPb++6EHTh0kX/i+c7vDvOXPK4dPKy2+UTV7hXmq86X23qdOo8/pPTT8e7nLuarrlca7nuer21e2b36RueN87d9L158Rb/1tWeOT3dvfN6b/fF9/XfFt1+cif9zsu72Xcn7q28T7xf9EDtQdlD3YfVP1v+3Njv3H9qwHeg89HcR/cGhYPP/pH1jw9DBY+Zj8uGDYbrnjg+OTniP3L96fynQ89kzyaeF/6i/suuFxYvfvjV69fO0ZjRoZfyl5O/bXyl/erA6xmv28bCxh6+yXgzMV70VvvtwXfcdx3vo98PT+R8IH8o/2j5sfVT0Kf7kxmTk/8EA5jz/GMzLdsAAAAGYktHRAD/AP8A/6C9p5MAAAAJcEhZcwAACxMAAAsTAQCanBgAAAAHdElNRQfcCBUXESpvlMWrAAAAYklEQVQ4y9VTQQrAIAxLiv//cnaYDNeVWqYXA4LYNpoEKQkrMCxiLwFJABAAkcS4xvPXjPNAjvCe/Br1sLTseSo4bNGNGXyPzRpmtf0xZrqjWppCZkVJAjt+pVDZRxIO/EwXL00iPZwDxWYAAAAASUVORK5CYII%3D";
        this.sm = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAMAAADzN3VRAAAARVBMVEX///////////////////////////////////////////////////////////////////////////////////////////+QFj7cAAAAFnRSTlMAHDE8PkJmcXR4eY+Vs8fL09Xc5vT5J4/h6AAAAFtJREFUeNqt0kkOgDAMQ9EPZSgztMX3PyoHiMKi6ttHkZ1QI+UDpmwkXl0QZbwUnTDLKEg3LLIIQw/dYATa2vYI425sSA+ssvw8/szPnrb83vyu/Tz+Tf0/qPABFzEW/E1C02AAAAAASUVORK5CYII=";
        this.Hp = this.ja + "_textoverlay";
        this.yj = "#" + this.Hp;
        this.ta = 1;
        this.renderer = this.config.renderer;
        this.Ya = "toolbar_" + this.ja;
        this.ea = "#" + this.Ya;
        this.Fc = !1;
        this.scale = this.config.document.Scale;
        this.resources = new FlowPaper_Resources(this);
        this.Yd = !1;
        this.Pf = 0;
        this.linkColor = "#72e6ff";
        this.ge = 0.4;
    }
    f.prototype = {
        ga: function(c) {
            if (0 < c.indexOf("undefined")) {
                return jQuery(null);
            }
            this.selectors || (this.selectors = {});
            this.selectors[c] || (this.selectors[c] = jQuery(c));
            return this.selectors[c];
        },
        na: function() {
            return this.ca ? this.ca.na : "";
        },
        loadFromUrl: function(c) {
            var d = this;
            d.kg();
            var e;
            window.annotations && d.plugin && d.plugin.clearMarks();
            if (d.pages) {
                for (var g = 0; g < d.document.numPages; g++) {
                    d.pages.pages[g] && delete d.pages.pages[g];
                }
            }
            eb.browser.qb.tp && c.PDFFile ? e = new CanvasPageRenderer(this.ja, c.PDFFile, d.config.jsDirectory, {
                jsonfile: c.jsonfile,
                pageImagePattern: c.pageImagePattern,
                JSONDataType: d.renderer.config.JSONDataType,
                signature: d.renderer.config.signature
            }) : c.JSONFile && c.IMGFiles && (e = new ImagePageRenderer(this.ja, {
                jsonfile: c.JSONFile,
                pageImagePattern: c.IMGFiles,
                JSONDataType: d.renderer.config.JSONDataType,
                signature: d.renderer.config.signature
            }, d.config.jsDirectory));
            if (d.renderer = e) {
                d.dg = "", d.oj(), d.renderer = e, e.initialize(function() {
                    d.document.numPages = e.getNumPages();
                    d.document.dimensions = e.getDimensions();
                    d.document.StartAtPage = c.StartAtPage;
                    d.loadDoc(e, e.getNumPages());
                });
            }
        },
        loadDoc: function(c, d) {
            this.initialized = !1;
            this.document.numPages = d;
            this.renderer = c;
            this.show();
        },
        getDimensions: function(c) {
            return this.renderer.getDimensions(c);
        },
        $m: function(c) {
            if (jQuery(c.target).hasClass("flowpaper_note_container") && eb.platform.touchdevice) {
                return window.Cb = !1, !0;
            }
            var d = eb.platform.touchdevice && "undefined" !== typeof c.originalEvent.touches ? c.originalEvent.touches[0].pageX : c.pageX,
                e = eb.platform.touchdevice && "undefined" !== typeof c.originalEvent.touches ? c.originalEvent.touches[0].pageY : c.pageY;
            if (this.Fc || eb.platform.touchdevice) {
                c.target && c.target.id && 0 <= c.target.id.indexOf("page") && 0 <= c.target.id.indexOf("word") && (hoverPage = parseInt(c.target.id.substring(c.target.id.indexOf("_") + 1)), hoverPageObject = T(this.ja));
                if (!hoverPageObject && !window.Cb || !window.Cb) {
                    return !0;
                }
                eb.platform.touchdevice && (c.preventDefault && c.preventDefault(), c.stopPropagation && c.stopPropagation(), this.pages.jScrollPane && this.pages.jScrollPane.data("jsp").disable());
                this.ba == this.na() && 1 < this.scale ? window.b = hoverPageObject.Qk(c.target.id) : window.b = hoverPageObject.match({
                    left: d,
                    top: e
                }, !1);
                null != window.b && null != window.a && window.a.pageNumber != window.b.pageNumber && (window.a = hoverPageObject.match({
                    left: d - 1,
                    top: e - 1
                }, !1));
                this.Ee(!0);
                this.Zd = hoverPageObject.bf(!0, this.se);
            } else {
                if (c.target && c.target.id && 0 <= c.target.id.indexOf("page") && (hoverPage = parseInt(c.target.id.substring(c.target.id.indexOf("_") + 1)), hoverPageObject = T(this.ja)), hoverPageObject && hoverPageObject.match({
                        left: d,
                        top: e
                    }, !0), !hoverPageObject && !window.Cb) {
                    return !0;
                }
            }
        },
        Ee: function(c) {
            eb.platform.touchdevice || (this.Zd = null);
            this.Fc && (jQuery(".flowpaper_pageword_" + this.ja).removeClass("flowpaper_selected"), jQuery(".flowpaper_pageword_" + this.ja).removeClass("flowpaper_selected_default"));
            c && jQuery(".flowpaper_pageword_" + this.ja).each(function() {
                jQuery(this).hasClass("flowpaper_selected_yellow") && !jQuery(this).data("isMark") && jQuery(this).removeClass("flowpaper_selected_yellow");
                jQuery(this).hasClass("flowpaper_selected_orange") && !jQuery(this).data("isMark") && jQuery(this).removeClass("flowpaper_selected_orange");
                jQuery(this).hasClass("flowpaper_selected_green") && !jQuery(this).data("isMark") && jQuery(this).removeClass("flowpaper_selected_green");
                jQuery(this).hasClass("flowpaper_selected_blue") && !jQuery(this).data("isMark") && jQuery(this).removeClass("flowpaper_selected_blue");
                jQuery(this).hasClass("flowpaper_selected_strikeout") && !jQuery(this).data("isMark") && jQuery(this).removeClass("flowpaper_selected_strikeout");
            });
        },
        an: function(c) {
            this.bh = "up";
            this.Lc = this.Ii = !1;
            this.Pk = null;
            if (!this.pages || !this.pages.animating) {
                if (jQuery(c.target).hasClass("flowpaper_searchabstract_result") || jQuery(c.target).parent().hasClass("flowpaper_searchabstract_result") || jQuery(c.target).hasClass("flowpaper_note_container")) {
                    return !0;
                }
                if (this.Fc || eb.platform.touchdevice) {
                    if (hoverPageObject) {
                        if (eb.platform.touchdevice) {
                            var d = null;
                            "undefined" != typeof c.originalEvent.touches && (d = c.originalEvent.touches[0] || c.originalEvent.changedTouches[0]);
                            null != d && this.Oc == d.pageX && this.Pc == d.pageY && (this.Ee(), this.Zd = hoverPageObject.bf(window.Cb, this.se));
                            null != d && (this.Oc = d.pageX, this.Pc = d.pageY);
                            this.pages.jScrollPane && this.pages.jScrollPane.data("jsp").enable();
                        } else {
                            window.b = hoverPageObject.match({
                                left: c.pageX,
                                top: c.pageY
                            }, !1);
                        }
                        null != this.Zd && this.ia.trigger("onSelectionCreated", this.Zd.text);
                        window.Cb = !1;
                        window.a = null;
                        window.b = null;
                    }
                } else {
                    hoverPageObject && (window.b = hoverPageObject.match({
                        left: c.pageX,
                        top: c.pageY
                    }, !1), window.Cb = !1, this.Ee(), this.Zd = hoverPageObject.bf(!1, this.se));
                }
            }
        },
        Zm: function(c) {
            var d = this;
            d.bh = "down";
            if (jQuery(c.target).hasClass("flowpaper_note_textarea")) {
                window.b = null, window.a = null;
            } else {
                if (!d.pages.animating) {
                    var e = eb.platform.touchdevice && "undefined" !== typeof c.originalEvent.touches ? c.originalEvent.touches[0].pageX : c.pageX,
                        g = eb.platform.touchdevice && "undefined" !== typeof c.originalEvent.touches ? c.originalEvent.touches[0].pageY : c.pageY;
                    d.Oc = e;
                    d.Pc = g;
                    eb.platform.touchdevice && (eb.platform.touchonlydevice && window.annotations && (d.Fc = !0, d.Ee(!0)), window.clearTimeout(d.Xn), d.Pk = (new Date).getTime(), document.activeElement && jQuery(document.activeElement).hasClass("flowpaper_note_textarea") && document.activeElement.blur(), d.Xn = setTimeout(function() {
                        if (null != d.Pk && c.originalEvent.touches && 0 < c.originalEvent.touches.length) {
                            var e = eb.platform.touchdevice && "undefined" !== typeof c.originalEvent.touches ? c.originalEvent.touches[0].pageX : c.pageX,
                                g = eb.platform.touchdevice && "undefined" !== typeof c.originalEvent.touches ? c.originalEvent.touches[0].pageY : c.pageY;
                            d.Oc + 20 > e && d.Oc - 20 < e && d.Pc + 20 > g && d.Pc - 20 < g && (hoverPage = parseInt(c.target.id.substring(c.target.id.indexOf("_") + 1)), hoverPageObject = T(d.ja), null != hoverPageObject && (null != d.pages.jScrollPane && d.pages.jScrollPane.data("jsp").disable(), window.Cb = !0, d.Ee(!0), window.b = hoverPageObject.match({
                                left: e,
                                top: g
                            }, !1), window.a = hoverPageObject.match({
                                left: e - 1,
                                top: g - 1
                            }, !1), d.Zd = hoverPageObject.bf(!0, d.se)));
                        }
                    }, 800));
                    if (d.Fc || eb.platform.touchdevice) {
                        if (!hoverPageObject) {
                            if (eb.platform.touchdevice) {
                                if (c.target && c.target.id && 0 <= c.target.id.indexOf("page") && 0 <= c.target.id.indexOf("word") && (hoverPage = parseInt(c.target.id.substring(c.target.id.indexOf("_") + 1)), hoverPageObject = T(d.ja)), !hoverPageObject) {
                                    window.a = null;
                                    return;
                                }
                            } else {
                                window.a = null;
                                return;
                            }
                        }
                        d.ba == d.na() && 1 < d.scale ? window.a = hoverPageObject.Qk(c.target.id) : window.a = hoverPageObject.match({
                            left: e,
                            top: g
                        }, !0);
                        if (window.a) {
                            return window.Cb = !0, d.Ee(), d.Zd = hoverPageObject.bf(!1, d.se), !1;
                        }
                        jQuery(c.target).hasClass("flowpaper_tblabelbutton") || jQuery(c.target).hasClass("flowpaper_tbtextbutton") || jQuery(c.target).hasClass("flowpaper_colorselector") || jQuery(c.target).hasClass("flowpaper_tbbutton") || eb.platform.touchdevice || (d.Ee(), d.Zd = hoverPageObject.bf(!1, d.se));
                        window.Cb = !1;
                        return !0;
                    }
                    window.a = hoverPageObject ? hoverPageObject.match({
                        left: e,
                        top: g
                    }, !0) : null;
                }
            }
        },
        de: function() {
            this.width || (this.width = this.ka.width());
            return this.width;
        },
        Yl: function() {
            return null != this.pages ? this.ba != this.na() ? this.pages.la + 1 : this.pages.la : 1;
        },
        bindEvents: function() {
            var c = this;
            hoverPage = 0;
            hoverPageObject = null;
            c.ka.bind("mousemove", function(d) {
                return c.$m(d);
            });
            c.ka.bind("mousedown", function(d) {
                return c.Zm(d);
            });
            c.ka.bind("mouseup", function(d) {
                return c.an(d);
            });
            var d = jQuery._data(jQuery(window)[0], "events");
            eb.platform.android ? jQuery(window).bind("orientationchange", function(d) {
                c.Aj(d);
            }) : jQuery(window).bind("resize", function(d) {
                c.Aj(d);
            });
            jQuery(window).bind("orientationchange", function(d) {
                c.co(d);
            });
            d && d.resize && (c.hl = d.resize[d.resize.length - 1]);
            if (!c.document.DisableOverflow) {
                try {
                    jQuery.get("/include/pdf/"+c.config.localeDirectory + c.document.localeChain + "/FlowPaper.txt", function(d) {
                        c.toolbar.Ok(d);
                        c.oj();
                    }).error(function() {
                        c.oj();
                        O("Failed loading supplied locale (" + c.document.localeChain + ")");
                    }), c.toolbar.Ok("");
                } catch (e) {}
            }
            c.dg || (c.dg = "");
        },
        co: function(c) {
            var d = this;
            d.Vh = !0;
            if (window.zine && d.ba == d.na()) {
                switch (window.orientation) {
                    case -90:
                    case 90:
                        d.ca.Ta = "Flip-SinglePage" != d.config.document.TouchInitViewMode ? !1 : !0;
                        break;
                    default:
                        d.ca.Ta = !0;
                }
                d.ca.yb = d.ca.Wh();
                setTimeout(function() {
                    d.ba = "";
                    d.switchMode(d.na(), d.getCurrPage() - 1);
                    d.Vh = !1;
                    window.scrollTo(0, 0);
                }, 500);
            }
            if ("Portrait" == d.ba || "SinglePage" == d.ba) {
                d.config.document.FitPageOnLoad && d.fitheight(), d.config.document.FitWidthOnLoad && d.fitwidth(), d.ka.height("auto"), setTimeout(function() {
                    requestAnim(function() {
                        d.Aj(c);
                        d.ka.height("auto");
                        d.Vh = !1;
                    });
                }, 1000);
            }
        },
        Aj: function(c) {
            if (!this.document.DisableOverflow && !this.Vh && !jQuery(c.target).hasClass("flowpaper_note")) {
                c = this.ka.width();
                var d = this.ka.height(),
                    e = !1,
                    g = -1;
                this.fj ? g = this.fj : 0 < this.ka[0].style.width.indexOf("%") && (this.fj = g = parseFloat(this.ka[0].style.width.substr(0, this.ka[0].style.width.length - 1) / 100));
                0 < g && (c = 0 == this.ka.parent().width() ? jQuery(document).width() * g : this.ka.parent().width() * g, e = !0);
                g = -1;
                this.ej ? g = this.ej : 0 < this.ka[0].style.height.indexOf("%") && (this.ej = g = parseFloat(this.ka[0].style.height.substr(0, this.ka[0].style.height.length - 1) / 100));
                0 < g && (d = 0 == this.ka.parent().height() ? jQuery(window).height() * g : this.ka.parent().height() * g, e = !0);
                g = document.Cb || document.mozFullScreen || document.webkitIsFullScreen || window.cn || window.Eh;
                e && !g && this.resize(c, d);
            }
        },
        oj: function() {
            var c = this;
            if (!c.document.DisableOverflow) {
                if (c.Oe || (c.Oe = null != c.toolbar && null != c.toolbar.fb ? c.toolbar.Fa(c.toolbar.fb, "LoadingPublication") : "Loading Publication"), null == c.Oe && (c.Oe = "Loading Publication"), c.Ml = window.zine && (c.renderer.config.pageThumbImagePattern && 0 < c.renderer.config.pageThumbImagePattern.length || c.config.document.LoaderImage), c.Ml) {
                    var d = new Image;
                    jQuery(d).bind("load", function() {
                        if (!c.initialized && (!c.hb || c.hb && !c.hb.jquery)) {
                            var d = this.width / 1.5,
                                g = this.height / 1.5;
                            this.width = d;
                            this.height = g;
                            110 < d && (g = this.width / this.height, d = 110, g = d / g);
                            c.hb = jQuery(String.format("<div class='flowpaper_loader' style='position:{1};margin: 0px auto;z-index:100;top:{9};left:{2};color:#ffffff;'><div style='position:relative;'><div class='flowpaper_titleloader_image' style='position:absolute;left:0px;'></div><div class='flowpaper_titleloader_progress' style='position:absolute;left:{7}px;width:{8}px;height:{6}px;background-color:#000000;opacity:0.3;'></div></div></div>", c.ja, "static" == c.ka.css("position") ? "relative" : "absolute", c.ca.Ta && !c.gf ? "35%" : "47%", c.ca.dc, c.renderer.Aa(1, 200), d, g, 0, d, c.ca.Ta && !c.gf ? "30%" : "40%"));
                            c.ka.append(c.hb);
                            jQuery(this).css({
                                width: d + "px",
                                height: g + "px"
                            });
                            c.hb.find(".flowpaper_titleloader_image").append(this);
                        }
                    });
                    c.config.document.LoaderImage ? d.src = c.config.document.LoaderImage : d.src = c.renderer.Aa(1, 200);
                } else {
                    !window.zine || eb.browser.msie && 10 > eb.browser.version ? (c.hb = jQuery(String.format("<div class='flowpaper_loader flowpaper_initloader' style='position:{2};z-index:100;'><div class='flowpaper_initloader_panel' style='{1};background-color:#ffffff;'><img src='{0}' style='vertical-align:middle;margin-top:7px;margin-left:5px;'><div style='float:right;margin-right:25px;margin-top:19px;' class='flowpaper_notifylabel'>" + c.Oe + "<br/><div style='margin-left:30px;' class='flowpaper_notifystatus'>" + c.dg + "</div></div></div></div>", c.rm, "margin: 0px auto;", "static" == c.ka.css("position") ? "relative" : "absolute")), c.ka.append(c.hb)) : (c.hb = jQuery(String.format("<div id='flowpaper_initloader_{0}' class='flowpaper_loader flowpaper_initloader' style='position:{1};margin: 0px auto;z-index:100;top:40%;left:{2}'></div>", c.ja, "static" == c.ka.css("position") ? "relative" : "absolute", eb.platform.iphone ? "40%" : "50%")), c.ka.append(c.hb), c.xc = new CanvasLoader("flowpaper_initloader_" + c.ja), c.xc.setColor("#555555"), c.xc.setShape("square"), c.xc.setDiameter(70), c.xc.setDensity(151), c.xc.setRange(0.8), c.xc.setSpeed(2), c.xc.setFPS(42), c.xc.show());
                }
            }
        },
        initialize: function() {
            var c = this;
            FLOWPAPER.Ak.init();
            c.Jo();
            c.Fi = location.hash && 0 <= location.hash.substr(1).indexOf("inpublisher") ? !0 : !1;
            c.ia = jQuery("#" + c.ja);
            c.toolbar = new ia(this, this.document);
            c.Hk = c.document.ImprovedAccessibility;
            !eb.platform.iphone || c.config.document.InitViewMode || window.zine || (c.Fb = "Portrait");
            "BookView" == c.config.document.InitViewMode && 0 == c.document.StartAtPage % 2 && (c.document.StartAtPage += 1);
            c.config.document.TouchInitViewMode && c.config.document.TouchInitViewMode != c.Fb && eb.platform.touchonlydevice && (c.Fb = c.config.document.TouchInitViewMode);
            c.config.document.TouchInitViewMode || !eb.platform.touchonlydevice || window.zine || (c.Fb = "SinglePage");
            window.zine && !c.document.DisableOverflow ? (c.ca = c.toolbar.ei = new FlowPaperViewer_Zine(c.toolbar, this, c.ia), "Portrait" != c.Fb && "Portrait" != c.config.document.TouchInitViewMode || !eb.platform.touchonlydevice || (c.config.document.TouchInitViewMode = c.config.document.InitViewMode = c.ba = "Flip-SinglePage"), c.ca.initialize(), c.ba != c.na() && (c.ba = c.Fb)) : c.ba = c.Fb;
            "CADView" == c.ba && (c.ba = "SinglePage");
            window.zine && (eb.browser.msie && 9 > eb.browser.version || eb.browser.safari && 5 > eb.browser.Gb) && !eb.platform.touchonlydevice && (c.document.MinZoomSize = c.MinZoomSize = 0.3, c.ba = "BookView");
            "0px" == c.ia.css("width") && c.ia.css("width", "1024px");
            "0px" == c.ia.css("height") && c.ia.css("height", "600px");
            c.Yd = c.ba == c.na() && (eb.platform.iphone || eb.platform.Hb);
            null !== c.ka || c.ca || (0 < c.ia[0].style.width.indexOf("%") && (c.fj = parseFloat(c.ia[0].style.width.substr(0, c.ia[0].style.width.length - 1) / 100)), 0 < c.ia[0].style.height.indexOf("%") && (c.ej = parseFloat(c.ia[0].style.height.substr(0, c.ia[0].style.height.length - 1) / 100)), c.document.DisableOverflow ? (c.config.document.FitPageOnLoad = !1, c.config.document.FitWidthOnLoad = !0, c.ka = jQuery("<div style='width:210mm;height:297mm;position:relative;left:0px;top:0px;' class='flowpaper_viewer_container'/>")) : (c.ka = jQuery("<div style='" + c.ia.attr("style") + ";' class='flowpaper_viewer_wrap flowpaper_viewer_container'/>"), "" != c.ka.css("position") && "static" != c.ka.css("position") || c.ka.css({
                position: "relative"
            })), c.ka = c.ia.wrap(c.ka).parent(), c.document.DisableOverflow ? c.ia.css({
                left: "0px",
                top: "0px",
                position: "relative",
                width: "210mm",
                height: "297mm"
            }).addClass("flowpaper_viewer") : c.ia.css({
                left: "0px",
                top: "0px",
                position: "relative",
                width: "100%",
                height: "100%"
            }).addClass("flowpaper_viewer").addClass("flowpaper_viewer_gradient"), window.annotations && c.config.document.AnnotationToolsVisible && !c.document.DisableOverflow ? (c.Pf = eb.platform.touchdevice ? 15 : 22, c.ia.height(c.ia.height() - c.Pf)) : c.Pf = 0);
            c.Ip = c.ka.html();
            eb.browser.msie && jQuery(".flowpaper_initloader_panel").css("left", c.ia.width() - 500);
            c.document.DisableOverflow || (null == c.config.Toolbar && 0 == jQuery("#" + c.Ya).length ? (c.Toolbar = c.ka.prepend("<div id='" + c.Ya + "' class='flowpaper_toolbarstd' style='z-index:200;overflow-y:hidden;overflow-x:hidden;'></div>").parent(), c.toolbar.create(c.Ya)) : null == c.config.Toolbar || c.Toolbar instanceof jQuery || (c.config.Toolbar = unescape(c.config.Toolbar), c.Toolbar = jQuery(c.config.Toolbar), c.Toolbar.attr("id", c.Ya), c.ka.prepend(c.Toolbar)));
            c.Pj();
            c.document.DisableOverflow || c.resources.initialize();
            hoverPage = 0;
            hoverPageObject = null;
            null != c.ca ? c.ca.Sm(c.Ya) : window.annotations && (c.plugin = new FlowPaperViewerAnnotations_Plugin(this, this.document, c.Ya + "_annotations"), c.plugin.create(c.Ya + "_annotations"), c.plugin.bindEvents(c.aa));
            c.document.DisableOverflow || (eb.platform.touchdevice || c.ka.append("<textarea id='selector' class='flowpaper_selector' rows='0' cols='0'></textarea>"), 0 == jQuery("#printFrame_" + c.ja).length && c.ka.append("<iframe id='printFrame_" + c.ja + "' name='printFrame_" + c.ja + "' class='flowpaper_printFrame'>"));
            jQuery(c.renderer).bind("loadingProgress", function(d, e) {
                c.No(d, e);
            });
            jQuery(c.renderer).bind("labelsLoaded", function(d, e) {
                c.Lo(d, e);
            });
            jQuery(c.renderer).bind("loadingProgressStatusChanged", function(d, e) {
                c.Oo(d, e);
            });
            jQuery(c.renderer).bind("UIBlockingRenderingOperation", function(d, e) {
                c.sd(d, e);
            });
            jQuery(c.renderer).bind("UIBlockingRenderingOperationCompleted", function() {
                c.mc();
            });
            $FlowPaper(c.ja).dispose = c.dispose;
            $FlowPaper(c.ja).highlight = c.highlight;
            $FlowPaper(c.ja).getCurrentRenderingMode = c.getCurrentRenderingMode;
        },
        Pj: function() {
            this.Am || this.document.DisableOverflow || (eb.platform.touchonlydevice && !this.Yd ? eb.platform.touchonlydevice ? (window.zine ? this.ia.height(this.ia.height() - (this.config.BottomToolbar ? 65 : 35)) : this.ia.height(this.ia.height() - (this.config.BottomToolbar ? 65 : 25)), this.config.BottomToolbar && this.ka.height(this.ka.height() - (eb.platform.Hb ? 7 : 18))) : this.ia.height(this.ia.height() - 25) : window.zine || (this.config.BottomToolbar ? this.ia.height(this.ia.height() - jQuery(this.ea).height() + 11) : this.ia.height(this.ia.height() - 13)), this.Am = !0);
        },
        Lo: function(c, d) {
            if (window.zine && this.ca && this.ca.Jc) {
                var e = this.ca.Jc.createElement("labels");
                this.ca.Jc.childNodes[0].appendChild(e);
                try {
                    for (var g = 0; g < d.Kk.length; g++) {
                        var f = d.Kk[g],
                            m = e,
                            k = this.ca.Jc.createElement("node");
                        k.setAttribute("pageNumber", g + 1);
                        k.setAttribute("title", escape(f));
                        m.appendChild(k);
                    }
                } catch (l) {}
                this.labels = jQuery(e);
            }
        },
        No: function(c, d) {
            this.dg = Math.round(100 * d.progress) + "%";
            this.hb && this.hb.find && 0 < this.hb.find(".flowpaper_notifystatus").length && this.hb.find(".flowpaper_notifystatus").html(this.dg);
            if (this.Ml && this.hb && this.hb.find) {
                var e = this.hb.find(".flowpaper_titleloader_progress");
                if (e) {
                    var g = this.hb.find(".flowpaper_titleloader_image");
                    if (0 < g.length) {
                        var f = g.css("width"),
                            f = parseFloat(f.replace("px", ""));
                        requestAnim(function() {
                            e.animate({
                                left: f * d.progress + "px",
                                width: f * (1 - d.progress) + "px"
                            }, 100);
                        });
                    }
                }
            }
        },
        Oo: function(c, d) {
            this.Oe = d.label;
            this.hb.find(".flowpaper_notifylabel").html(d.label);
        },
        sd: function(c, d) {
            var e = this;
            e.document.DisableOverflow || null !== e.Zc || (e.Zc = jQuery("<div style='position:absolute;left:50%;top:50%;'></div>"), e.ka.append(e.Zc), e.Zc.spin({
                color: "#777"
            }), null != e.Vg && (window.clearTimeout(e.Vg), e.Vg = null), d.mo || (e.Vg = setTimeout(function() {
                e.Zc && (e.Zc.remove(), e.Zc = null);
            }, 1000)));
        },
        mc: function() {
            this.Zc && (this.Zc.remove(), this.Zc = null);
        },
        show: function() {
            var c = this;
            jQuery(c.resources).bind("onPostinitialized", function() {
                setTimeout(function() {
                    c.kg();
                    c.document.DisableOverflow || c.toolbar.bindEvents(c.ia);
                    null == c.ca || c.document.DisableOverflow || c.ca.bindEvents(c.ia);
                    c.Oj(c.document.StartAtPage);
                    jQuery(c.ia).trigger("onDocumentLoaded", c.renderer.getNumPages());
                }, 50);
                jQuery(c.resources).unbind("onPostinitialized");
            });
            c.resources.no();
        },
        dispose: function() {
            this.Xm = !0;
            this.ia.unbind();
            this.ia.find("*").unbind();
            this.ka.find("*").unbind();
            this.ka.find("*").remove();
            this.ia.empty();
            this.ka.empty();
            jQuery(this).unbind();
            0 == jQuery(".flowpaper_viewer_container").length && window.PDFJS && delete window.PDFJS;
            this.plugin && (jQuery(this.plugin).unbind(), this.plugin.dispose(), delete this.plugin, this.plugin = null);
            jQuery(this.renderer).unbind();
            this.renderer.dispose();
            delete this.renderer;
            delete this.config;
            jQuery(this.pages).unbind();
            this.pages.dispose();
            delete this.pages;
            delete window["wordPageList_" + this.ja];
            window["wordPageList_" + this.ja] = null;
            this.ka.unbind("mousemove");
            this.ka.unbind("mousedown");
            this.ka.unbind("mouseup");
            jQuery(window).unbind("resize", this.hl);
            delete this.hl;
            jQuery(this.renderer).unbind("loadingProgress");
            jQuery(this.renderer).unbind("labelsLoaded");
            jQuery(this.renderer).unbind("loadingProgressStatusChanged");
            jQuery(this.renderer).unbind("UIBlockingRenderingOperation");
            jQuery(this.renderer).unbind("UIBlockingRenderingOperationCompleted");
            this.ca ? this.ca.dispose() : this.ia.parent().remove();
            var c = this.ka.parent(),
                d = this.ka.attr("style");
            this.ka.remove();
            delete this.ka;
            delete this.ia;
            this.renderer && (delete this.renderer.Wa, delete this.renderer.oa, delete this.renderer.Ua, delete this.renderer.ph, delete this.renderer.mb);
            delete this.renderer;
            var e = jQuery(this.Ip);
            e.attr("style", d);
            e.attr("class", "flowpaper_viewer");
            c.append(e);
            this.plugin && delete this.plugin;
        },
        hh: function() {
            var c = this;
            eb.platform.touchonlydevice ? (c.initialized = !0, (!c.ca && c.config.document.FitWidthOnLoad && "TwoPage" != c.ba && "BookView" != c.ba || "Portrait" == c.ba || "SinglePage" == c.ba) && c.fitwidth(), (c.config.document.FitPageOnLoad || "TwoPage" == c.ba || "BookView" == c.ba || c.ca) && c.fitheight(), c.pages.sg(), c.pages.le()) : (c.initialized = !0, c.Dq || c.toolbar.xm(c.config.document.MinZoomSize, c.config.document.MaxZoomSize), c.config.document.FitPageOnLoad || "TwoPage" == c.ba || "BookView" == c.ba ? c.fitheight() : c.config.document.FitWidthOnLoad && "TwoPage" != c.ba && "BookView" != c.ba ? c.fitwidth() : c.Zoom(c.config.document.Scale));
            c.document.StartAtPage && 1 != c.document.StartAtPage || c.ba == c.na() || c.ia.trigger("onCurrentPageChanged", c.pages.la + 1);
            c.document.StartAtPage && 1 != c.document.StartAtPage && c.pages.scrollTo(c.document.StartAtPage);
            c.ca && c.ca.hh();
            c.hb && c.hb.fadeOut ? c.hb.fadeOut(300, function() {
                c.hb && (c.hb.remove(), c.ka.find(".flowpaper_loader").remove(), c.xc && (c.xc.kill(), delete c.xc), delete c.hb, c.xc = null, jQuery(c.pages.da).fadeIn(300, function() {}), c.PreviewMode && c.ca.pb.Dh(c.pages, c.ia));
            }) : (c.ka.find(".flowpaper_loader").remove(), jQuery(c.pages.da).fadeIn(300, function() {}), c.PreviewMode && c.ca.pb.Dh(c.pages, c.ia));
            c.ia.trigger("onInitializationComplete");
        },
        kg: function() {
            this.renderer.hi = !1;
            if (this.pages) {
                for (var c = 0; c < this.document.numPages; c++) {
                    this.pages.pages[c] && window.clearTimeout(this.pages.pages[c].fc);
                }
            }
            this.ta = 1;
            this.ia.find("*").unbind();
            this.ia.find("*").remove();
            this.ia.empty();
            this.renderer.qf = !1;
            jQuery(this.yj).remove();
            this.ca && this.ca.kg();
        },
        Oj: function(c) {
            this.pages = new V(this.ia, this, this.ja, c);
            this.pages.create(this.ia);
        },
        previous: function() {
            var c = this;
            c.Yi || (c.Yi = setTimeout(function() {
                window.clearTimeout(c.Yi);
                c.Yi = null;
            }, 700), c.pages.previous());
        },
        dn: function() {
            var c = this;
            c.cb && c.rf();
            if (!c.Jb && c.outline && (!c.outline || 0 != c.outline.length)) {
                c.Ca = c.ia.width();
                c.Na = c.ia.height();
                var d = c.Oe = null != c.toolbar && null != c.toolbar.fb ? c.toolbar.Fa(c.toolbar.fb, "TOC", "Table of Contents") : "Table of Contents",
                    e = c.ba == c.na() ? jQuery(c.ea).css("background-color") : "#c8c8c8",
                    g = c.ba == c.na() ? "40px" : jQuery(c.ea).height() + 2;
                c.na();
                var f = c.ba == c.na() ? 30 : 40,
                    m = c.ba == c.na() ? 0 : 41,
                    k = c.ca && !c.ca.rh ? jQuery(c.ea).offset().top + jQuery(c.ea).outerHeight() : 0,
                    l = c.ia.height() - (null != c.Ye ? c.Ye.height() + 20 : 0) - k;
                c.og = c.ka.find(c.ea).css("margin-left");
                "rgba(0, 0, 0, 0)" == e.toString() && (e = "#555");
                c.ka.append(jQuery(String.format("<div class='flowpaper_toc' style='position:absolute;left:0px;top:{8}px;height:{5}px;width:{2};min-width:{3};opacity: 0;z-index:13;'><div style='margin: 20px 20px 20px 20px;padding: 10px 10px 10px 10px;background-color:{6};height:{7}px'><div style='height:25px;width:100%'><div class='flowpaper_tblabel' style='margin-left:10px; width: 100%;height:25px;'><img src='{1}' style='vertical-align: middle;width:14px;height:auto;'><span style='margin-left:10px;vertical-align: middle'>{0}</span><img src='{4}' style='float:right;margin-right:5px;cursor:pointer;' class='flowpaper_toc_close' /></div><hr size='1' color='#ffffff' /></div></div>", d, c.sm, "20%", "250px", c.Ch, l, e, l - 20, k)));
                c.Jb = c.ka.find(".flowpaper_toc");
                jQuery(c.Jb.children()[0]).css({
                    "border-radius": "3px",
                    "-moz-border-radius": "3px"
                });
                jQuery(c.Jb.children()[0]).append("<div class='flowpaper_toc_content' style='display:block;position:relative;height:" + (jQuery(c.Jb.children()[0]).height() - f) + "px;margin-bottom:50px;width:100%;overflow-y: auto;overflow-x: hidden;'><ul class='flowpaper_accordionSkinClear'>" + da(c, c.outline.children()).html() + "</ul></div>");
                d = jQuery(".flowpaper_accordionSkinClear").children();
                0 < d.children().length && (d = jQuery(d.get(0)).children(), 0 < d.children().length && jQuery(d.find("li").get(0)).addClass("cur"));
                c.resize(c.ia.width() - c.Jb.width(), c.ia.height() + m, !1, function() {});
                jQuery(".flowpaper_accordionSkinClear").Pn();
                jQuery(".flowpaper-tocitem").bind("mousedown", function() {
                    c.gotoPage(jQuery(this).data("pagenumber"));
                });
                c.ia.animate({
                    left: c.Jb.width() + "px"
                }, 0);
                m = 0.5 * c.Jb.width();
                jQuery(c.ea).width() + m > c.ka.width() && (m = 0);
                jQuery(c.ea).animate({
                    "margin-left": parseFloat(c.og) + m + "px"
                }, 200, function() {
                    if (window.onresize) {
                        window.onresize();
                    }
                });
                0 == m && c.Jb.css({
                    top: g,
                    height: c.ia.height() - 40 + "px"
                });
                c.ba == c.na() && c.ca.To();
                c.Jb.fadeTo("fast", 1);
                c.ka.find(".flowpaper_toc_close").bind("mousedown", function() {
                    c.Fk();
                });
            }
        },
        Fk: function() {
            var c = this;
            c.Jb.hide();
            c.ka.find(".flowpaper_tocitem, .flowpaper_tocitem_separator").remove();
            c.resize(c.Ca, c.Na + 33, !1);
            c.ia.css({
                left: "0px"
            });
            jQuery(c.ea).animate({
                "margin-left": parseFloat(c.og) + "px"
            }, 200);
            c.ba == c.na() && c.ca.rf();
            c.Jb.fadeTo("fast", 0, function() {
                c.Jb.remove();
                c.Jb = null;
            });
        },
        setCurrentCursor: function(c) {
            "ArrowCursor" == c && (this.Fc = !1, addCSSRule(".flowpaper_pageword", "cursor", "default"), window.annotations || jQuery(".flowpaper_pageword_" + this.ja).remove());
            "TextSelectorCursor" == c && (this.Fc = !0, this.se = "flowpaper_selected_default", addCSSRule(".flowpaper_pageword", "cursor", "text"), window.annotations || (this.pages.getPage(this.pages.la - 1), this.pages.getPage(this.pages.la - 2), this.pages.La()));
            this.ca && this.ca.setCurrentCursor(c);
            this.pages.setCurrentCursor(c);
            jQuery(this.ea).trigger("onCursorChanged", c);
        },
        highlight: function(c) {
            var d = this;
            jQuery.ajax({
                type: "GET",
                url: c,
                dataType: "xml",
                error: function() {},
                success: function(c) {
                    jQuery(c).find("Body").attr("color");
                    c = jQuery(c).find("Highlight");
                    var g = 0,
                        f = -1,
                        m = -1;
                    jQuery(c).find("loc").each(function() {
                        g = parseInt(jQuery(this).attr("pg"));
                        f = parseInt(jQuery(this).attr("pos"));
                        m = parseInt(jQuery(this).attr("len"));
                        d.pages.getPage(g).Ae(f, m, !1);
                    });
                    d.pages.La();
                }
            });
        },
        printPaper: function(c) {
            if (eb.platform.touchonlydevice) {
                c = "current";
            } else {
                if (!c) {
                    jQuery("#modal-print").css("background-color", "#dedede");
                    jQuery("#modal-print").smodal({
                        minHeight: 255,
                        appendTo: this.ka
                    });
                    jQuery("#modal-print").parent().css("background-color", "#dedede");
                    return;
                }
            }
            "current" == c && 0 < jQuery(this.ea).find(".flowpaper_txtPageNumber").val().indexOf("-") && (c = jQuery(this.ea).find(".flowpaper_txtPageNumber").val());
            var d = null,
                e = "ImagePageRenderer";
            if ("ImagePageRenderer" == this.renderer.lf() || this.document.MixedMode || this.renderer.config.pageImagePattern && this.renderer.config.jsonfile) {
                e = "ImagePageRenderer", d = "{key : '" + this.config.key + "',jsonfile : '" + this.renderer.config.jsonfile + "',compressedJsonFormat : " + (this.renderer.Sa ? this.renderer.Sa : !1) + ",pageImagePattern : '" + this.renderer.config.pageImagePattern + "',JSONDataType : '" + this.renderer.config.JSONDataType + "',signature : '" + this.renderer.config.signature + "',UserCollaboration : " + this.config.UserCollaboration + "}";
            }
            "CanvasPageRenderer" == this.renderer.lf() && (e = "CanvasPageRenderer", d = "{key : '" + this.config.key + "',jsonfile : '" + this.renderer.config.jsonfile + "',PdfFile : '" + this.renderer.file + "',compressedJsonFormat : " + (this.renderer.Sa ? this.renderer.Sa : !1) + ",pageThumbImagePattern : '" + this.renderer.config.pageThumbImagePattern + "',pageImagePattern : '" + this.renderer.config.pageImagePattern + "',JSONDataType : '" + this.renderer.config.JSONDataType + "',signature : '" + this.renderer.config.signature + "',UserCollaboration : " + this.config.UserCollaboration + "}");
            if (0 < jQuery("#printFrame_" + this.ja).length) {
                var g = window.printFrame = eb.browser.msie ? window.open().document : jQuery("#printFrame_" + this.ja)[0].contentWindow.document || jQuery("#printFrame_" + this.ja)[0].contentDocument,
                    f = "",
                    m = this.renderer.getDimensions()[0].width,
                    k = this.renderer.getDimensions()[0].height;
                g.open();
                f += "<html><head>";
                f += "<script type='text/javascript' src='" + this.config.jsDirectory + "jquery.min.js'>\x3c/script>";
                f += "<script type='text/javascript' src='" + this.config.jsDirectory + "jquery.extensions.min.js'>\x3c/script>";
                f += '<script type="text/javascript" src="' + this.config.jsDirectory + 'flowpaper.js">\x3c/script>';
                f += '<script type="text/javascript" src="' + this.config.jsDirectory + 'flowpaper_handlers.js">\x3c/script>';
                f += "<script type='text/javascript' src='" + this.config.jsDirectory + "FlowPaperViewer.js'>\x3c/script>";
                f += "<script type='text/javascript'>window.printWidth = '" + m + "pt';window.printHeight = '" + k + "pt';\x3c/script>";
                f += "<style type='text/css' media='print'>html, body { height:100%; } body { margin:0; padding:0; } .flowpaper_ppage { clear:both;display:block;max-width:" + m + "pt;max-height:" + k + "pt;margin-top:0px;} .ppage_break { page-break-after : always; } .ppage_none { page-break-after : avoid; }</style>";
                f += "<style type='text/css' media='print'>@supports ((size:A4) and (size:1pt 1pt)) {@page { margin: 0mm 0mm 0mm 0mm; size: " + m + "pt " + k + "pt;}}</style>";
                f += "<link rel='stylesheet' type='text/css' href='" + this.config.cssDirectory + "flowpaper.css' />";
                f += "</head>";
                f += "<body>";
                f += '<script type="text/javascript">';
                f += "function waitForLoad(){";
                f += "if(window.jQuery && window.$FlowPaper && window.print_flowpaper_Document ){";
                f += "window.focus();";
                f += "window.print_flowpaper_Document('" + e + "'," + d + ",'" + c + "', " + this.Yl() + ", " + this.getTotalPages() + ", '" + this.config.jsDirectory + "');";
                f += "}else{setTimeout(function(){waitForLoad();},1000);}";
                f += "}";
                f += "waitForLoad();";
                f += "\x3c/script>";
                f += "</body></html>";
                g.write(f);
                eb.browser.msie || setTimeout("window['printFrame'].close();", 3000);
                eb.browser.msie && 9 <= eb.browser.version && g.close();
            }
        },
        switchMode: function(c, d) {
            var e = this;
            e.ba == c || ("TwoPage" == c || "BookView" == c) && 2 > e.getTotalPages() || (d > e.getTotalPages() && (d = e.getTotalPages()), e.cb && e.rf(), jQuery(e.pages.da).en(function() {
                e.ca && e.ca.switchMode(c, d);
                "Tile" == c && (e.ba = "ThumbView");
                "Portrait" == c && (e.ba = "SinglePage" == e.Fb ? "SinglePage" : "Portrait");
                "SinglePage" == c && (e.ba = "SinglePage");
                "TwoPage" == c && (e.ba = "TwoPage");
                "BookView" == c && (e.ba = "BookView");
                e.kg();
                e.pages.Bo();
                e.renderer.He = -1;
                e.renderer.Wa && e.renderer.Wa.Ho();
                "TwoPage" != c && "BookView" != c && (null != d ? e.pages.la = d - 1 : d = 1);
                e.Oj(d);
                jQuery(e.ea).trigger("onViewModeChanged", c);
                setTimeout(function() {
                    !eb.platform.touchdevice || eb.platform.touchdevice && ("SinglePage" == c || "Portrait" == c) ? e.fitheight() : "TwoPage" != c && "BookView" != c && c != e.na() && e.fitwidth();
                    "TwoPage" != c && "BookView" != c && e.bd(d);
                }, 100);
            }));
        },
        fitwidth: function() {
            if ("TwoPage" != this.ba && "BookView" != this.ba && "ThumbView" != this.ba) {
                var c = jQuery(this.pages.da).width() - (this.document.DisableOverflow ? 0 : 15),
                    d = 1 < this.getTotalPages() ? this.ta - 1 : 0;
                0 > d && (d = 0);
                this.document.DisplayRange && (d = parseInt(this.document.DisplayRange.split("-")[0]) - 1);
                var e = this.pages.getPage(d).dimensions.Ca / this.pages.getPage(d).dimensions.Na;
                if (eb.platform.touchdevice) {
                    c = c / (this.pages.getPage(d).ab * e) - (this.document.DisableOverflow ? 0 : 0.03), window.FitWidthScale = c, this.lb(c), this.pages.rj();
                } else {
                    c = c / (this.pages.getPage(d).ab * this.document.MaxZoomSize * e) - (this.document.DisableOverflow ? 0 : 0.012);
                    if (90 == this.pages.getPage(d).rotation || 270 == this.pages.getPage(d).rotation) {
                        c = this.Me();
                    }
                    window.FitWidthScale = c;
                    jQuery(this.ea).trigger("onScaleChanged", c / this.document.MaxZoomSize);
                    c * this.document.MaxZoomSize >= this.document.MinZoomSize && c <= this.document.MaxZoomSize && ("Portrait" == this.ba ? this.lb(this.document.MaxZoomSize * c, {
                        Gg: !0
                    }) : this.lb(this.document.MaxZoomSize * c));
                }
            }
        },
        getCurrentRenderingMode: function() {
            return this.renderer instanceof CanvasPageRenderer ? "html5" : "html";
        },
        lb: function(c, d) {
            var e = this;
            if (e.initialized && e.pages) {
                if (!d || d && !d.Gg) {
                    var g = 100 / (100 * e.document.ZoomInterval);
                    c = Math.round(c * g) / g;
                }
                e.ba == e.na() && 1 > c && (c = 1);
                jQuery(e.ea).trigger("onScaleChanged", c / e.document.MaxZoomSize);
                var g = jQuery(e.pages.da).prop("scrollHeight"),
                    f = jQuery(e.pages.da).scrollTop(),
                    g = 0 < f ? f / g : 0;
                null != e.Xe && (window.clearTimeout(e.Xe), e.Xe = null);
                e.pages.zo() && e.scale != c && (jQuery(".flowpaper_annotation_" + e.ja).remove(), jQuery(".flowpaper_pageword_" + e.ja).remove());
                e.Xe = setTimeout(function() {
                    e.ec();
                    e.pages && e.pages.La();
                }, 500);
                if (0 < c) {
                    c < e.config.document.MinZoomSize && (c = this.config.document.MinZoomSize);
                    c > e.config.document.MaxZoomSize && (c = this.config.document.MaxZoomSize);
                    e.pages.Xa(c, d);
                    e.scale = c;
                    !d || d && !d.Cd ? e.pages.pages[0] && e.pages.pages[0].Ce() : e.pages.Fg(d.ic, d.Ic);
                    jQuery(e.ea).trigger("onZoomFactorChanged", {
                        df: c,
                        aa: e
                    });
                    if ("undefined" != window.FitWidthScale && Math.round(100 * window.FitWidthScale) == Math.round(c / e.document.MaxZoomSize * 100)) {
                        if (jQuery(e.ea).trigger("onFitModeChanged", "FitWidth"), window.onFitModeChanged) {
                            window.onFitModeChanged("Fit Width");
                        }
                    } else {
                        if ("undefined" != window.FitHeightScale && Math.round(100 * window.FitHeightScale) == Math.round(c / e.document.MaxZoomSize * 100)) {
                            if (jQuery(e.ea).trigger("onFitModeChanged", "FitHeight"), window.onFitModeChanged) {
                                window.onFitModeChanged("Fit Height");
                            }
                        } else {
                            if (jQuery(e.ea).trigger("onFitModeChanged", "FitNone"), window.onFitModeChanged) {
                                window.onFitModeChanged("Fit None");
                            }
                        }
                    }
                    e.pages.le();
                    e.pages.qd();
                    e.pages.rj();
                    f = jQuery(e.pages.da).prop("scrollHeight");
                    eb.browser.qb.Ab && (!d || d && !d.Cd ? jQuery(e.pages.da).scrollTo({
                        left: "50%",
                        top: f * g + "px"
                    }, 0, {
                        axis: "xy"
                    }) : jQuery(e.pages.da).scrollTo({
                        top: f * g + "px"
                    }, 0, {
                        axis: "y"
                    }));
                }
            }
        },
        ec: function() {
            if (this.renderer) {
                null != this.Xe && (window.clearTimeout(this.Xe), this.Xe = null);
                "CanvasPageRenderer" == this.renderer.lf() && jQuery(".flowpaper_pageword_" + this.ja + ":not(.flowpaper_selected_searchmatch)").remove();
                this.pages.Bf && 0 <= this.pages.Bf && this.pages.pages[this.pages.Bf].ib && this.renderer.Tb(this.pages.pages[this.pages.Bf], !0);
                for (var c = 0; c < this.document.numPages; c++) {
                    this.pages.kb(c) && c != this.pages.Bf && this.pages.pages[c] && (this.pages.pages[c].ib ? this.renderer.Tb(this.pages.pages[c], !0) : this.pages.pages[c].Ga = !1);
                }
            }
        },
        Zoom: function(c, d) {
            !eb.platform.touchonlydevice || "TwoPage" != this.ba && "BookView" != this.ba ? (c > this.document.MaxZoomSize && (c = this.document.MaxZoomSize), c = c / this.document.MaxZoomSize, jQuery(this.ea).trigger("onScaleChanged", c), c * this.document.MaxZoomSize >= this.document.MinZoomSize && c <= this.document.MaxZoomSize && this.lb(this.document.MaxZoomSize * c, d)) : 1 < c ? "TwoPage" == this.ba || "BookView" == this.ba ? this.pages.fe() : "Portrait" != this.ba && "SinglePage" != this.ba || this.fitwidth() : "TwoPage" == this.ba || "BookView" == this.ba ? this.pages.dd() : "Portrait" != this.ba && "SinglePage" != this.ba || this.fitheight();
        },
        ZoomIn: function() {
            this.Zoom(this.scale + 3 * this.document.ZoomInterval);
        },
        ZoomOut: function() {
            if ("Portrait" == this.ba || "SinglePage" == this.ba) {
                null != this.pages.jScrollPane ? (this.pages.jScrollPane.data("jsp").scrollTo(0, 0, !1), this.pages.jScrollPane.data("jsp").reinitialise(this.Nc)) : this.pages.ga(this.pages.da).parent().scrollTo({
                    left: 0,
                    top: 0
                }, 0, {
                    axis: "xy"
                });
            }
            this.Zoom(this.scale - 3 * this.document.ZoomInterval);
        },
        sliderChange: function(c) {
            c > this.document.MaxZoomSize || (c = c / this.document.MaxZoomSize, c * this.document.MaxZoomSize >= this.document.MinZoomSize && c <= this.document.MaxZoomSize && this.lb(this.document.MaxZoomSize * c));
        },
        pj: function() {
            var c = this;
            if (!eb.platform.mobilepreview && (c.Jb && c.Fk(), !c.cb)) {
                c.ka.find(".flowpaper_searchabstract_result, .flowpaper_searchabstract_result_separator").remove();
                var d = c.Oe = null != c.toolbar && null != c.toolbar.fb ? c.toolbar.Fa(c.toolbar.fb, "Search") : "Search",
                    e = c.ca && !c.ca.rh ? jQuery(c.ea).offset().top + jQuery(c.ea).outerHeight() : 0,
                    g = c.ia.height() - (null != c.Ye ? c.Ye.height() + 20 : 0) - e,
                    f = c.ba == c.na() ? jQuery(c.ea).css("background-color") : "#c8c8c8",
                    m = c.ba == c.na() ? "40px" : jQuery(c.ea).height() + 2,
                    k = c.ba == c.na() ? "color:#ededed" : "color:#555555;",
                    l = (c.na(), 40),
                    n = c.ba == c.na() ? 0 : 41;
                "rgba(0, 0, 0, 0)" == f.toString() && (f = "#555");
                c.og = c.ka.find(c.ea).css("margin-left");
                c.ba == c.na() ? (c.ka.append(jQuery(String.format("<div class='flowpaper_searchabstracts' style='position:absolute;left:0px;top:{8}px;height:{5}px;width:{2};min-width:{3};opacity: 0;z-index:13;'><div style='margin: 20px 20px 20px 20px;padding: 10px 10px 10px 10px;background-color:{6};height:{7}px'><div style='height:25px;width:100%'><div class='flowpaper_tblabel' style='margin-left:10px; width: 100%;height:25px;'><img src='{1}' style='vertical-align: middle'><span style='margin-left:10px;vertical-align: middle'>{0}</span><img src='{4}' style='float:right;margin-right:5px;cursor:pointer;' class='flowpaper_searchabstracts_close' /></div><hr size='1' color='#ffffff' /></div></div>", d, c.Gj, "20%", "250px", c.Ch, g, f, g - 20, e))), c.cb = c.ka.find(".flowpaper_searchabstracts"), jQuery(c.cb.children()[0]).css({
                    "border-radius": "3px",
                    "-moz-border-radius": "3px"
                }), jQuery(c.cb.children()[0]).append("<div class='flowpaper_searchabstracts_content' style='display:block;position:relative;height:" + (jQuery(c.cb.children()[0]).height() - l) + "px;margin-bottom:50px;width:100%;overflow-y: auto;overflow-x: hidden;'></div>"), c.resize(c.ia.width() - c.cb.width(), c.ia.height() + n, !1, function() {}), c.ia.animate({
                    left: c.cb.width() + "px"
                }, 0)) : (c.ka.append(jQuery(String.format("<div class='flowpaper_searchabstracts' style='position:absolute;left:0px;top:0px;height:{5}px;width:{2};min-width:{3};opacity: 0;z-index:13;overflow:hidden;'><div style='margin: 0px 0px 0px 0px;padding: 10px 7px 10px 10px;background-color:{6};height:{7}px'><div style='height:25px;width:100%' <div class='flowpaper_tblabel' style='margin-left:10px; width: 100%;height:25px;'><img src='{1}' style='vertical-align: middle'><span style='margin-left:10px;vertical-align: middle'>{0}</span><img src='{4}' style='float:right;margin-right:5px;cursor:pointer;' class='flowpaper_searchabstracts_close' /></div><div class='flowpaper_bottom_fade'></div></div></div>", d, c.Gj, "20%", "250px", c.Ch, c.ia.height(), f, c.ka.height() - 58))), c.cb = c.ka.find(".flowpaper_searchabstracts"), jQuery(c.cb.children()[0]).append("<div class='flowpaper_searchabstracts_content' style='display:block;position:relative;height:" + g + "px;margin-bottom:50px;width:100%;overflow-y: auto;overflow-x: hidden;'></div>"), "TwoPage" != c.ba && c.resize(c.ia.width() - c.cb.width() / 2, c.ka.height() + 1, !1, function() {}), c.ia.animate({
                    left: c.cb.width() / 2 + "px"
                }, 0), c.fitheight());
                d = 0.5 * c.cb.width();
                jQuery(c.ea).width() + d > c.ka.width() && (d = 0);
                jQuery(c.ea).animate({
                    "margin-left": parseFloat(c.og) + d + "px"
                }, 200, function() {
                    if (window.onresize) {
                        window.onresize();
                    }
                });
                0 == d && c.cb.css({
                    top: m,
                    height: c.ia.height() - 40 + "px"
                });
                c.ba == c.na() && c.ca.pj();
                c.cb.fadeTo("fast", 1);
                var v = c.ka.find(".flowpaper_searchabstracts_content");
                jQuery(c).bind("onSearchAbstractAdded", function(d, e) {
                    var f = e.ze.qn;
                    100 < f.length && (f = f.substr(0, 100) + "...");
                    f = f.replace(new RegExp(c.Nd, "g"), "<font style='color:#ffffff'>[" + c.Nd + "]</font>");
                    f = "<b>p." + (e.ze.pageIndex + 1) + "</b> : " + f;
                    v.append(jQuery(String.format("<div id='flowpaper_searchabstract_item_{1}' style='{2}' class='flowpaper_searchabstract_result'>{0}</div><hr size=1 color='#777777' style='margin-top:8px;' class='flowpaper_searchabstract_result_separator' />", f, e.ze.id, k)));
                    jQuery("#flowpaper_searchabstract_item_" + e.ze.id).bind("mousedown", function(d) {
                        c.gb = e.ze.pageIndex + 1;
                        c.me = e.ze.Fo;
                        c.Cc = -1;
                        c.searchText(c.Nd, !1);
                        d.preventDefault && d.preventDefault();
                        d.returnValue = !1;
                    });
                    jQuery("#flowpaper_searchabstract_item_" + e.ze.id).bind("mouseup", function(c) {
                        c.preventDefault && c.preventDefault();
                        c.returnValue = !1;
                    });
                });
                c.ka.find(".flowpaper_searchabstracts_close").bind("mousedown", function() {
                    c.rf();
                });
            }
        },
        rf: function() {
            var c = this;
            c.cb && (c.cb.hide(), c.ka.find(".flowpaper_searchabstract_result, .flowpaper_searchabstract_result_separator").remove(), c.ba == c.na() ? (c.resize(c.ia.width() + c.cb.width(), c.ia.height(), !1), c.ia.css({
                left: "0px"
            })) : "TwoPage" == c.ba ? (c.ia.css({
                left: "0px",
                width: "100%"
            }), c.fitheight()) : (c.resize(c.ia.width() + c.cb.width() / 2, c.ka.height() + 1, !1), c.ia.css({
                left: "0px"
            })), jQuery(c.ea).animate({
                "margin-left": parseFloat(c.og) + "px"
            }, 200), c.ba == c.na() && c.ca.rf(), c.cb.fadeTo("fast", 0, function() {
                c.cb.remove();
                c.cb = null;
            }));
            jQuery(c).unbind("onSearchAbstractAdded");
        },
        Jk: function(c, d) {
            jQuery(".flowpaper_searchabstract_blockspan").remove();
            var e = this.renderer.getNumPages();
            d || (d = 0);
            for (var f = d; f < e; f++) {
                this.vm(f, c);
            }
            this.ba != this.na() && this.ka.find(".flowpaper_searchabstracts_content").append(jQuery("<div class='flowpaper_searchabstract_blockspan' style='display:block;clear:both;height:200px'></div>"));
        },
        vm: function(c, d) {
            var e = this,
                f = e.renderer.mb;
            if (null != f[c]) {
                f[c].toLowerCase().indexOf("actionuri") && (f[c] = f[c].replace("actionURI", ""), f[c] = f[c].replace("):", ")"));
                f[c].toLowerCase().indexOf("actiongotor") && (f[c] = f[c].replace("actionGoToR", ""));
                f[c].toLowerCase().indexOf("actiongoto") && (f[c] = f[c].replace("actionGoTo", ""));
                for (var h = f[c].toLowerCase().indexOf(d), m = 0; 0 < h;) {
                    var k = 0 < h - 50 ? h - 50 : 0,
                        l = h + 75 < f[c].length ? h + 75 : f[c].length,
                        n = e.Bc.length;
                    e.Bc.Re[n] = [];
                    e.Bc.Re[n].pageIndex = c;
                    e.Bc.Re[n].Fo = m;
                    e.Bc.Re[n].id = e.ja + "_" + c + "_" + m;
                    e.Bc.Re[n].qn = f[c].substr(k, l - k);
                    h = f[c].toLowerCase().indexOf(d, h + 1);
                    jQuery(e).trigger("onSearchAbstractAdded", {
                        ze: e.Bc.Re[n]
                    });
                    m++;
                }
            } else {
                null == e.nl && (e.nl = setTimeout(function() {
                    null == e.renderer.hd && e.renderer.fd(c + 1, !1, function() {
                        e.nl = null;
                        e.Jk(d, c);
                    });
                }, 100));
            }
        },
        searchText: function(c, d) {
            var e = this;
            if (null != c) {
                if (void 0 !== d || "Portrait" != e.ba && "TwoPage" != e.ba && e.ba != e.na() || !e.document.EnableSearchAbstracts || eb.platform.mobilepreview || (d = !0), d && e.ba == e.na() && 1 < e.scale && (e.renderer.Vc && e.renderer.Ar(), e.Zoom(1)), jQuery(e.ea).find(".flowpaper_txtSearch").val() != c && jQuery(e.ea).find(".flowpaper_txtSearch").val(c), "ThumbView" == e.ba) {
                    e.switchMode("Portrait"), setTimeout(function() {
                        e.searchText(c);
                    }, 1000);
                } else {
                    var f = e.renderer.mb,
                        h = e.renderer.getNumPages();
                    e.lh || (e.lh = 0);
                    if (0 == e.renderer.Wa.Ua.length && 10 > e.lh) {
                        window.clearTimeout(e.Go), e.Go = setTimeout(function() {
                            e.searchText(c, d);
                        }, 500), e.lh++;
                    } else {
                        e.lh = 0;
                        e.me || (e.me = 0);
                        e.gb || (e.gb = -1);
                        null != c && 0 < c.length && (c = c.toLowerCase());
                        e.Nd != c && (e.Cc = -1, e.Nd = c, e.me = 0, e.gb = -1, e.Bc = [], e.Bc.Re = []); - 1 == e.gb ? e.gb = parseInt(e.ta) : e.Cc = e.Cc + c.length;
                        0 == e.Bc.Re.length && e.Bc.searchText != c && d && (e.Bc.searchText != c && e.ka.find(".flowpaper_searchabstract_result, .flowpaper_searchabstract_result_separator").remove(), e.Bc.searchText = c, e.pj(), e.Jk(c));
                        for (; e.gb - 1 < h;) {
                            var m = f[e.gb - 1];
                            e.renderer.Ia && null == m && (jQuery(e.renderer).trigger("UIBlockingRenderingOperation", e.ja), e.Xo = e.gb, e.renderer.fd(e.gb, !1, function() {
                                m = f[e.gb - 1];
                                e.Xo = null;
                            }));
                            e.Cc = m.indexOf(c, -1 == e.Cc ? 0 : e.Cc);
                            if (0 <= e.Cc) {
                                e.ta == e.gb || !(e.ba == e.na() && e.ta != e.gb + 1 || "BookView" == e.ba && e.ta != e.gb + 1 || "TwoPage" == e.ba && e.ta != e.gb - 1 || "SinglePage" == e.ba && e.ta != e.gb) || "TwoPage" != e.ba && "BookView" != e.ba && "SinglePage" != e.ba && e.ba != e.na() ? (e.me++, e.renderer.vb ? this.pages.getPage(e.gb - 1).load(function() {
                                    e.pages.getPage(e.gb - 1).vc(e.Nd, !1);
                                }) : ("Portrait" == e.ba && this.pages.getPage(e.gb - 1).load(function() {
                                    e.pages.getPage(e.gb - 1).vc(e.Nd, !1);
                                }), "TwoPage" != e.ba && "SinglePage" != e.ba && e.ba != e.na() || this.pages.getPage(e.gb - 1).vc(e.Nd, !1))) : e.gotoPage(e.gb, function() {
                                    e.Cc = e.Cc - c.length;
                                    e.searchText(c);
                                });
                                break;
                            }
                            e.gb++;
                            e.Cc = -1;
                            e.me = 0;
                        } - 1 == e.Cc && (e.Cc = -1, e.me = 0, e.gb = -1, e.mc(), alert(null != e.toolbar && null != e.toolbar.fb ? e.toolbar.Fa(e.toolbar.fb, "Finishedsearching") : "No more search matches."), e.gotoPage(1));
                    }
                }
            }
        },
        fitheight: function() {
            if (this.ba != this.na()) {
                try {
                    if (eb.platform.touchdevice) {
                        if (c = this.Me()) {
                            window.FitHeightScale = c, this.lb(c, {
                                Gg: !0
                            }), this.pages.rj();
                        }
                    } else {
                        var c = this.Me();
                        window.FitHeightScale = c;
                        jQuery(this.ea).trigger("onScaleChanged", c / this.document.MaxZoomSize);
                        c * this.document.MaxZoomSize >= this.document.MinZoomSize && c <= this.document.MaxZoomSize && ("Portrait" == this.ba ? this.lb(this.document.MaxZoomSize * c, {
                            Gg: !0
                        }) : this.lb(this.document.MaxZoomSize * c));
                    }
                } catch (d) {}
            }
        },
        Ng: function() {
            var c = jQuery(this.pages.da).width() - 15,
                d = 1 < this.getTotalPages() ? this.ta - 1 : 0;
            0 > d && (d = 0);
            this.document.DisplayRange && (d = parseInt(this.document.DisplayRange.split("-")[0]) - 1);
            var e = this.pages.getPage(d).dimensions.Ca / this.pages.getPage(d).dimensions.Na;
            return eb.platform.touchdevice ? c / (this.pages.getPage(d).ab * e) - ("SinglePage" == this.ba ? 0.1 : 0.03) : c / (this.pages.getPage(d).ab * this.document.MaxZoomSize * e) - 0.012;
        },
        Me: function() {
            if (this.document.DisableOverflow) {
                return window.FitHeightScale = 1;
            }
            this.ta - 1 && (this.ta = 1);
            if ("Portrait" == this.ba || "SinglePage" == this.ba || "TwoPage" == this.ba || "BookView" == this.ba) {
                var c = this.pages.getPage(this.ta - 1).dimensions.width / this.pages.getPage(this.ta - 1).dimensions.height;
                if (eb.platform.touchdevice) {
                    d = jQuery(this.ia).height() - ("TwoPage" == this.ba || "BookView" == this.ba ? 40 : 0), "SinglePage" == this.ba && (d -= 25), d /= this.pages.getPage(this.ta - 1).ab, e = this.pages.getPage(this.ta - 1), e = e.dimensions.Ca / e.dimensions.Na * e.ab * d, ("TwoPage" == this.ba || "BookView" == this.ba) && 2 * e > this.ia.width() && (d = this.ia.width() - 0, d /= 4 * this.pages.getPage(this.ta - 1).ab);
                } else {
                    var d = jQuery(this.pages.da).height() - ("TwoPage" == this.ba || "BookView" == this.ba ? 25 : 0),
                        d = d / (this.pages.getPage(this.ta - 1).ab * this.document.MaxZoomSize),
                        e = this.pages.getPage(this.ta - 1),
                        e = e.dimensions.Ca / e.dimensions.Na * e.ab * this.document.MaxZoomSize * d;
                    ("TwoPage" == this.ba || "BookView" == this.ba) && 2 * e > this.ia.width() && (d = (jQuery(this.ia).width() - ("TwoPage" == this.ba || "BookView" == this.ba ? 40 : 0)) / 1.48, d = d / 1.6 / (this.pages.getPage(this.ta - 1).ab * this.document.MaxZoomSize * c));
                }
                return window.FitHeightScale = d;
            }
            if (this.ba == this.na()) {
                return d = 1, window.FitHeightScale = d;
            }
        },
        next: function() {
            var c = this;
            c.Ri || c.ba == c.na() || (c.Ri = setTimeout(function() {
                window.clearTimeout(c.Ri);
                c.Ri = null;
            }, 700));
            c.pages.next();
        },
        gotoPage: function(c, d) {
            var e = this;
            e.pages && ("ThumbView" == e.ba ? eb.platform.ios ? e.ca ? e.ca.xp(c) : e.switchMode("Portrait", c) : e.switchMode("Portrait", c) : ("Portrait" == e.ba && e.pages.scrollTo(c), "SinglePage" == e.ba && setTimeout(function() {
                e.pages.Zf(c, d);
            }, 300), "TwoPage" != e.ba && "BookView" != e.ba || setTimeout(function() {
                e.pages.ag(c, d);
            }, 300), e.ca && e.ca.gotoPage(c, d)));
        },
        rotate: function() {
            this.pages.rotate(this.getCurrPage() - 1);
            window.annotations && (jQuery(".flowpaper_pageword_" + this.ja).remove(), this.ec(), this.pages.La());
        },
        getCurrPage: function() {
            return null != this.pages ? this.ba != this.na() ? this.pages.la + 1 : this.pages.la : 1;
        },
        Jo: function() {
            this.version = "2.4.8";
        },
        getTotalPages: function() {
            return this.pages.getTotalPages();
        },
        bd: function(c) {
            var d = this;
            d.ba != d.na() && (this.ta = c, this.pages.la = this.ta - 1);
            c > d.getTotalPages() && (c = c - 1, this.pages.la = c);
            "TwoPage" != this.ba && "BookView" != this.ba || this.pages.la != this.pages.getTotalPages() - 1 || 0 == this.pages.la % 2 || (this.pages.la = this.pages.la + 1);
            d.ca && (0 == c && (c++, this.ta = c), d.ca.bd(c));
            d.ld && (jQuery(".flowpaper_mark_video_maximized").remove(), jQuery(".flowpaper_mark_video_maximized_closebutton").remove(), d.ld = null);
            0 < jQuery(".flowpaper_mark_video").find("iframe,video").length && jQuery(".flowpaper_mark_video").find("iframe,video").each(function() {
                try {
                    var c = jQuery(this).closest(".flowpaper_page").attr("id"),
                        f = parseInt(c.substr(14, c.lastIndexOf("_") - 14));
                    if (0 == f && 0 != d.pages.la - 1 || 0 < f && f != d.pages.la - 1 && f != d.pages.la - 2) {
                        jQuery(this).parent().remove();
                        var h = d.pages.pages[f];
                        h.uf(h.Li ? h.Li : h.scale, h.Kc());
                    }
                } catch (m) {}
            });
            this.toolbar.Cp(c);
            null != d.plugin && ("TwoPage" == this.ba ? (d.plugin.Hg(this.pages.la + 1), d.plugin.Hg(this.pages.la + 2)) : "BookView" == this.ba ? (1 != c && d.plugin.Hg(this.pages.la), d.plugin.Hg(this.pages.la + 1)) : d.plugin.Hg(this.ta));
        },
        addLink: function(c, d, e, f, h, m) {
            window[this.Gi].addLink = this.addLink;
            c = parseInt(c);
            null == this.za[c - 1] && (this.za[c - 1] = []);
            var k = {
                type: "link"
            };
            k.href = d;
            k.Rn = e;
            k.Sn = f;
            k.width = h;
            k.height = m;
            this.za[c - 1][this.za[c - 1].length] = k;
        },
        addVideo: function(c, d, e, f, h, m, k, l) {
            window[this.Gi].addVideo = this.addVideo;
            c = parseInt(c);
            null == this.za[c - 1] && (this.za[c - 1] = []);
            var n = {
                type: "video"
            };
            n.src = d;
            n.url = e;
            n.Pl = f;
            n.Ql = h;
            n.width = m;
            n.height = k;
            n.Zn = l;
            this.za[c - 1][this.za[c - 1].length] = n;
        },
        addImage: function(c, d, e, f, h, m, k, l) {
            c = parseInt(c);
            null == this.za[c - 1] && (this.za[c - 1] = []);
            var n = {
                type: "image"
            };
            n.src = d;
            n.Ai = e;
            n.Bi = f;
            n.width = h;
            n.height = m;
            n.href = k;
            n.Gn = l;
            this.za[c - 1][this.za[c - 1].length] = n;
        },
        openFullScreen: function() {
            var c = this,
                d = document.Cb || document.mozFullScreen || document.webkitIsFullScreen || window.cn || window.Eh,
                e = c.ka.get(0);
            if (d) {
                return document.exitFullscreen ? document.exitFullscreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitExitFullscreen && document.webkitExitFullscreen(), window.Eh && window.close(), !1;
            }
            "0" != c.ka.css("top") && (c.fo = c.ka.css("top"));
            "0" != c.ka.css("left") && (c.eo = c.ka.css("left"));
            c.ba == c.na() && 1 < c.scale && (c.pages.dd(), c.fisheye.show(), c.fisheye.animate({
                opacity: 1
            }, 100));
            c.Ca = c.ka.width();
            c.Na = c.ka.height();
            c.PreviewMode && c.pages.Vk && (c.PreviewMode = !1, c.Ah = !0, c.ca.pb.uo(c.pages, c.ia), c.ca.Uo());
            c.ka.css({
                visibility: "hidden"
            });
            jQuery(document).bind("webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange", function() {
                setTimeout(function() {
                    if (window.navigator.standalone || document.fullScreenElement && null != document.fullScreenElement || document.mozFullScreen || document.webkitIsFullScreen) {
                        eb.browser.safari ? window.zine ? c.resize(screen.width, screen.height) : c.config.BottomToolbar ? c.resize(screen.width, screen.height - jQuery(c.ea).height() - 70) : c.resize(screen.width, screen.height - jQuery(c.ea).height()) : window.zine ? c.resize(window.outerWidth, window.outerHeight) : c.resize(window.innerWidth, window.innerHeight);
                    }
                    window.annotations && (jQuery(".flowpaper_pageword_" + c.ja).remove(), c.ec(), c.pages.La());
                    c.ka.css({
                        visibility: "visible"
                    });
                }, 500);
                jQuery(document).bind("webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange", function() {
                    jQuery(document).unbind("webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange");
                    c.Ci = !1;
                    c.ka.css({
                        top: c.fo,
                        left: c.eo
                    });
                    c.Ah && (c.PreviewMode = !0, c.ca.Dk(), c.ca.Ug(), setTimeout(function() {
                        c.PreviewMode && c.ca.Ug();
                    }, 1000));
                    c.ba == c.na() && 1 < c.scale ? c.pages.dd(function() {
                        c.fisheye.show();
                        c.fisheye.animate({
                            opacity: 1
                        }, 100);
                        c.resize(c.Ca, c.Na - 2);
                        jQuery(c.ea).trigger("onFullscreenChanged", !1);
                    }) : (c.resize(c.Ca, c.Na - 2), jQuery(c.ea).trigger("onFullscreenChanged", !1));
                    jQuery(document).unbind("webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange");
                    c.Ah && (c.Ah = !1, c.ca.pb.Dh(c.pages, c.ia));
                    window.annotations && (jQuery(".flowpaper_pageword_" + c.ja).remove(), c.ec(), c.pages.La());
                });
                window.clearTimeout(c.dj);
                c.dj = setTimeout(function() {
                    !c.PreviewMode && c.ca && c.ca.Sf && c.ca.mj();
                }, 1000);
            });
            d = eb.platform.android && !e.webkitRequestFullScreen;
            c.document.FullScreenAsMaxWindow || !document.documentElement.requestFullScreen || d ? c.document.FullScreenAsMaxWindow || !document.documentElement.mozRequestFullScreen || d ? c.document.FullScreenAsMaxWindow || !document.documentElement.webkitRequestFullScreen || d ? !c.document.FullScreenAsMaxWindow && document.documentElement.msRequestFullscreen ? (c.ka.css({
                visibility: "hidden"
            }), c.Ci ? (c.Ci = !1, window.document.msExitFullscreen()) : (c.Ci = !0, e.msRequestFullscreen()), setTimeout(function() {
                c.ka.css({
                    visibility: "visible"
                });
                c.resize(window.outerWidth, window.outerHeight);
                window.annotations && (jQuery(".flowpaper_pageword_" + c.ja).remove(), c.ec(), c.pages.La());
            }, 500)) : (c.bo(), setTimeout(function() {
                c.ka.css({
                    visibility: "visible"
                });
            }, 500)) : (c.ka.css({
                visibility: "hidden"
            }), e.webkitRequestFullScreen(eb.browser.safari ? 0 : 1), c.ka.css({
                left: "0px",
                top: "0px"
            })) : (c.ka.css({
                visibility: "hidden"
            }), e.mozRequestFullScreen(), c.ka.css({
                left: "0px",
                top: "0px"
            })) : (c.ka.css({
                visibility: "hidden"
            }), e.requestFullScreen(), c.ka.css({
                left: "0px",
                top: "0px"
            }));
            jQuery(c.ea).trigger("onFullscreenChanged", !0);
        },
        bo: function() {
            var c = "",
                c = "toolbar=no, location=no, scrollbars=no, width=" + screen.width,
                c = c + (", height=" + screen.height),
                c = c + ", top=0, left=0, fullscreen=yes";
            nw = this.document.FullScreenAsMaxWindow ? window.open("") : window.open("", "windowname4", c);
            nw.params = c;
            c = "<!doctype html><head>";
            c += '<meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width" />';
            c += '<link rel="stylesheet" type="text/css" href="' + this.config.cssDirectory + (-1 == this.config.cssDirectory.indexOf("flowpaper.css") ? "flowpaper.css" : "") + '" />';
            c += '<script type="text/javascript" src="' + this.config.jsDirectory + 'jquery.min.js">\x3c/script>';
            c += '<script type="text/javascript" src="' + this.config.jsDirectory + 'jquery.extensions.min.js">\x3c/script>';
            c += '<script type="text/javascript" src="' + this.config.jsDirectory + 'flowpaper.js">\x3c/script>';
            c += '<script type="text/javascript" src="' + this.config.jsDirectory + 'flowpaper_handlers.js">\x3c/script>';
            c += '<style type="text/css" media="screen">body{ margin:0; padding:0; overflow-x:hidden;overflow-y:hidden; }</style>';
            c += "</head>";
            c += '<body onload="openViewer();">';
            c += '<div id="documentViewer" class="flowpaper_viewer" style="position:absolute;left:0px;top:0px;width:100%;height:100%;"></div>';
            c += '<script type="text/javascript">';
            c += "function openViewer(){";
            c += 'jQuery("#documentViewer").FlowPaperViewer(';
            c += "{ config : {";
            c += "";
            c += 'SWFFile : "' + this.document.SWFFile + '",';
            c += 'IMGFiles : "' + this.document.IMGFiles + '",';
            c += 'JSONFile : "' + this.document.JSONFile + '",';
            c += 'PDFFile : "' + this.document.PDFFile + '",';
            c += "";
            c += "Scale : " + this.scale + ",";
            c += 'ZoomTransition : "' + this.document.ZoomTransition + '",';
            c += "ZoomTime : " + this.document.ZoomTime + ",";
            c += "ZoomInterval : " + this.document.ZoomInterval + ",";
            c += "FitPageOnLoad : " + this.document.FitPageOnLoad + ",";
            c += "FitWidthOnLoad : " + this.document.FitWidthOnLoad + ",";
            c += "FullScreenAsMaxWindow : " + this.document.FullScreenAsMaxWindow + ",";
            c += "ProgressiveLoading : " + this.document.ProgressiveLoading + ",";
            c += "MinZoomSize : " + this.document.MinZoomSize + ",";
            c += "MaxZoomSize : " + this.document.MaxZoomSize + ",";
            c += "MixedMode : " + this.document.MixedMode + ",";
            c += "SearchMatchAll : " + this.document.SearchMatchAll + ",";
            c += 'InitViewMode : "' + this.document.InitViewMode + '",';
            c += 'RenderingOrder : "' + this.document.RenderingOrder + '",';
            c += "useCustomJSONFormat : " + this.document.useCustomJSONFormat + ",";
            c += 'JSONDataType : "' + this.document.JSONDataType + '",';
            null != this.document.JSONPageDataFormat && (c += "JSONPageDataFormat : {", c += 'pageWidth : "' + this.document.JSONPageDataFormat.Qe + '",', c += 'pageHeight : "' + this.document.JSONPageDataFormat.Pe + '",', c += 'textCollection : "' + this.document.JSONPageDataFormat.ue + '",', c += 'textFragment : "' + this.document.JSONPageDataFormat.Nb + '",', c += 'textFont : "' + this.document.JSONPageDataFormat.ng + '",', c += 'textLeft : "' + this.document.JSONPageDataFormat.vd + '",', c += 'textTop : "' + this.document.JSONPageDataFormat.wd + '",', c += 'textWidth : "' + this.document.JSONPageDataFormat.xd + '",', c += 'textHeight : "' + this.document.JSONPageDataFormat.ud + '"', c += "},");
            c += "ViewModeToolsVisible : " + this.document.ViewModeToolsVisible + ",";
            c += "ZoomToolsVisible : " + this.document.ZoomToolsVisible + ",";
            c += "NavToolsVisible : " + this.document.NavToolsVisible + ",";
            c += "CursorToolsVisible : " + this.document.CursorToolsVisible + ",";
            c += "SearchToolsVisible : " + this.document.SearchToolsVisible + ",";
            window.zine || (c += 'Toolbar : "' + escape(this.config.Toolbar) + '",');
            c += 'BottomToolbar : "' + this.config.BottomToolbar + '",';
            c += 'UIConfig : "' + this.document.UIConfig + '",';
            c += 'jsDirectory : "' + this.config.jsDirectory + '",';
            c += 'cssDirectory : "' + this.config.cssDirectory + '",';
            c += 'localeDirectory : "' + this.config.localeDirectory + '",';
            c += 'key : "' + this.config.key + '",';
            c += "";
            c += 'localeChain: "' + this.document.localeChain + '"';
            c += "}});";
            c += "}";
            c += "document.fullscreen = true;";
            c += "$(document).keyup(function(e) {if (e.keyCode == 27){window.close();}});";
            c += "\x3c/script>";
            c += "</body>";
            c += "</html>";
            nw.document.write(c);
            nw.Eh = !0;
            window.focus && nw.focus();
            nw.document.close();
            return !1;
        },
        resize: function(c, d, e, f) {
            var h = this;
            if (h.initialized) {
                h.width = null;
                if (h.ba == h.na()) {
                    h.ca.resize(c, d, e, f);
                } else {
                    var m = jQuery(h.ea).height() + 1 + 14,
                        k = 0 < h.Pf ? h.Pf + 1 : 0;
                    h.ia.css({
                        width: c,
                        height: d - m - k
                    });
                    null != e && 1 != e || this.ka.css({
                        width: c,
                        height: d
                    });
                    h.pages.resize(c, d - m - k, f);
                    jQuery(".flowpaper_interactiveobject_" + h.ja).remove();
                    jQuery(".flowpaper_pageword_" + h.ja).remove();
                    "TwoPage" != h.ba && "BookView" != h.ba || h.fitheight();
                    window.clearTimeout(h.ko);
                    h.ko = setTimeout(function() {
                        h.pages.La();
                    }, 700);
                }
                h.ca && h.ca.Sf && (window.clearTimeout(h.dj), h.dj = setTimeout(function() {
                    h.PreviewMode || h.ca.mj();
                }, 2500));
                h.cb && !h.ca ? h.ia.animate({
                    left: h.cb.width() / 2 + "px"
                }, 0) : h.cb && h.ca && h.ia.animate({
                    left: h.cb.width() + "px"
                }, 0);
            }
        }
    };
    f.loadFromUrl = f.loadFromUrl;
    return f;
}();
window.print_flowpaper_Document = function(f, c, d, e, g) {
    FLOWPAPER.Ak.init();
    f = Array(g + 1);
    var h = 0;
    if ("all" == d) {
        for (var m = 1; m < g + 1; m++) {
            f[m] = !0;
        }
        h = g;
    } else {
        if ("current" == d) {
            f[e] = !0, h = 1;
        } else {
            if (-1 == d.indexOf(",") && -1 < d.indexOf("-")) {
                for (var k = parseInt(d.substr(0, d.toString().indexOf("-"))), l = parseInt(d.substr(d.toString().indexOf("-") + 1)); k < l + 1; k++) {
                    f[k] = !0, h++;
                }
            } else {
                if (0 < d.indexOf(",")) {
                    for (var n = d.split(","), m = 0; m < n.length; m++) {
                        if (-1 < n[m].indexOf("-")) {
                            for (k = parseInt(n[m].substr(0, n[m].toString().indexOf("-"))), l = parseInt(n[m].substr(n[m].toString().indexOf("-") + 1)); k < l + 1; k++) {
                                f[k] = !0, h++;
                            }
                        } else {
                            f[parseInt(n[m].toString())] = !0, h++;
                        }
                    }
                }
            }
        }
    }
    jQuery(document.body).append("<div id='documentViewer' style='position:absolute;width:100%;height:100%'></div>");
    f = "1-" + g;
    window.Sh = 0;
    "current" == d ? f = e + "-" + e : "all" == d ? f = "1-" + g : f = d; - 1 == f.indexOf("-") && (f = f + "-" + f, h = 1);
    jQuery("#documentViewer").FlowPaperViewer({
        config: {
            IMGFiles: c.pageImagePattern,
            JSONFile: c.jsonfile && "undefined" != c.jsonfile ? c.jsonfile : null,
            PDFFile: c.PdfFile,
            JSONDataType: c.JSONDataType,
            RenderingOrder: null != c.jsonfile && "undefined" != c.jsonfile && 0 < c.jsonfile.length && null != c.pageImagePattern && 0 < c.pageImagePattern.length && "undefined" != c.pageImagePattern ? "html,html" : "html5,html",
            key: c.key,
            UserCollaboration: c.UserCollaboration,
            InitViewMode: "Portrait",
            DisableOverflow: !0,
            DisplayRange: f
        }
    });
    jQuery("#documentViewer").bind("onPageLoaded", function() {
        window.Sh == h - 1 && setTimeout(function() {
            if (window.parent.onPrintRenderingCompleted) {
                window.parent.onPrintRenderingCompleted();
            }
            window.focus && window.focus();
            window.print();
            window.close && window.close();
        }, 2000);
        window.Sh++;
        if (window.parent.onPrintRenderingProgress) {
            window.parent.onPrintRenderingProgress(window.Sh);
        }
    });
};
window.renderPrintPage = function Z(c, d) {
    "CanvasPageRenderer" == c.lf() && (d < c.getNumPages() ? c.Ia ? document.getElementById("ppage_" + d) ? c.Ki(d + 1, function() {
        if (parent.onPrintRenderingProgress) {
            parent.onPrintRenderingProgress(d + 1);
        }
        document.getElementById("ppage_" + d) ? c.Qa[d].getPage(1).then(function(e) {
            var g = document.getElementById("ppage_" + d);
            if (g) {
                var h = g.getContext("2d"),
                    m = e.getViewport(4),
                    h = {
                        canvasContext: h,
                        viewport: m,
                        qh: null,
                        continueCallback: function(c) {
                            c();
                        }
                    };
                g.width = m.width;
                g.height = m.height;
                e.render(h).promise.then(function() {
                    e.destroy();
                    Z(c, d + 1);
                }, function(c) {
                    console.log(c);
                });
            } else {
                Z(c, d + 1);
            }
        }) : Z(c, d + 1);
    }) : Z(c, d + 1) : document.getElementById("ppage_" + d) ? c.Qa.getPage(d + 1).then(function(e) {
        if (parent.onPrintRenderingProgress) {
            parent.onPrintRenderingProgress(d + 1);
        }
        var g = document.getElementById("ppage_" + d);
        if (g) {
            var h = g.getContext("2d"),
                m = e.getViewport(4),
                h = {
                    canvasContext: h,
                    viewport: m,
                    qh: null,
                    continueCallback: function(c) {
                        c();
                    }
                };
            g.width = m.width;
            g.height = m.height;
            e.render(h).promise.then(function() {
                Z(c, d + 1);
                e.destroy();
            }, function(c) {
                console.log(c);
            });
        } else {
            Z(c, d + 1);
        }
    }) : Z(c, d + 1) : (parent.onPrintRenderingCompleted(), window.print()));
};
oa && self.addEventListener("message", function(f) {
    f = f.data;
    if ("undefined" !== f.cmd) {
        switch (f.cmd) {
            case "loadImageResource":
                var c = new XMLHttpRequest;
                c.open("GET", "../../" + f.src);
                c.Cb = c.responseType = "arraybuffer";
                c.onreadystatechange = function() {
                    if (4 == c.readyState && 200 == c.status) {
                        for (var d = new Uint8Array(this.response), e = d.length, f = Array(e); e--;) {
                            f[e] = String.fromCharCode(d[e]);
                        }
                        self.postMessage({
                            status: "ImageResourceLoaded",
                            blob: f.join("")
                        });
                        self.close();
                    }
                };
                c.send(null);
        }
    }
}, !1);