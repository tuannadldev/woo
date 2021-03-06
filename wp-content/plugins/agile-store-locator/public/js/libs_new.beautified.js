function MarkerClusterer(t, e, i) {
    this.extend(MarkerClusterer, google.maps.OverlayView), this.map_ = t, this.markers_ = [], 
    this.clusters_ = [], this.sizes = [ 53, 56, 66, 78, 90 ], this.styles_ = [], this.ready_ = !1;
    var n = i || {};
    this.gridSize_ = n.gridSize || 60, this.minClusterSize_ = n.minimumClusterSize || 2, 
    this.maxZoom_ = n.maxZoom || null, this.styles_ = n.styles || [], this.imagePath_ = n.imagePath || this.MARKER_CLUSTER_IMAGE_PATH_, 
    this.imageExtension_ = n.imageExtension || this.MARKER_CLUSTER_IMAGE_EXTENSION_, 
    this.zoomOnClick_ = !0, void 0 != n.zoomOnClick && (this.zoomOnClick_ = n.zoomOnClick), 
    this.averageCenter_ = !1, void 0 != n.averageCenter && (this.averageCenter_ = n.averageCenter), 
    this.setupStyles_(), this.setMap(t), this.prevZoom_ = this.map_.getZoom();
    var r = this;
    google.maps.event.addListener(this.map_, "zoom_changed", function() {
        var t = r.map_.getZoom();
        r.prevZoom_ != t && (r.prevZoom_ = t, r.resetViewport());
    }), google.maps.event.addListener(this.map_, "idle", function() {
        r.redraw();
    }), e && e.length && this.addMarkers(e, !1);
}

function Cluster(t) {
    this.markerClusterer_ = t, this.map_ = t.getMap(), this.gridSize_ = t.getGridSize(), 
    this.minClusterSize_ = t.getMinClusterSize(), this.averageCenter_ = t.isAverageCenter(), 
    this.center_ = null, this.markers_ = [], this.bounds_ = null, this.clusterIcon_ = new ClusterIcon(this, t.getStyles(), t.getGridSize());
}

function ClusterIcon(t, e, i) {
    t.getMarkerClusterer().extend(ClusterIcon, google.maps.OverlayView), this.styles_ = e, 
    this.padding_ = i || 0, this.cluster_ = t, this.center_ = null, this.map_ = t.getMap(), 
    this.div_ = null, this.sums_ = null, this.visible_ = !1, this.setMap(this.map_);
}

var asl_jQuery = jQuery;

!function(t, e) {
    var i = e.jQuery;
    "object" == typeof exports ? module.exports = i ? t(e, i) : function(i) {
        if (i && !i.fn) throw "Provide jQuery or null";
        return t(e, i);
    } : "function" == typeof define && define.amd ? define(function() {
        return t(e);
    }) : t(e, !1);
}(function(t, e) {
    "use strict";
    function i(t, e) {
        return function() {
            var i, n = this, r = n.base;
            return n.base = t, i = e.apply(n, arguments), n.base = r, i;
        };
    }
    function n(t, e) {
        return Q(e) && (e = i(t ? t._d ? t : i(o, t) : o, e), e._d = 1), e;
    }
    function r(t, e) {
        for (var i in e.props) Me.test(i) && (t[i] = n(t[i], e.props[i]));
    }
    function s(t) {
        return t;
    }
    function o() {
        return "";
    }
    function l(t) {
        try {
            throw console.log("JsRender dbg breakpoint: " + t), "dbg breakpoint";
        } catch (e) {}
        return this.base ? this.baseApply(arguments) : t;
    }
    function a(t) {
        this.name = (e.link ? "JsViews" : "JsRender") + " Error", this.message = t || this.name;
    }
    function u(t, e) {
        var i;
        for (i in e) t[i] = e[i];
        return t;
    }
    function h(t, e, i) {
        return t ? (re.delimiters = [ t, e, pe = i ? i.charAt(0) : pe ], le = t.charAt(0), 
        ae = t.charAt(1), ue = e.charAt(0), he = e.charAt(1), t = "\\" + le + "(\\" + pe + ")?\\" + ae, 
        e = "\\" + ue + "\\" + he, Z = "(?:(\\w+(?=[\\/\\s\\" + ue + "]))|(\\w+)?(:)|(>)|(\\*))\\s*((?:[^\\" + ue + "]|\\" + ue + "(?!\\" + he + "))*?)", 
        ne.rTag = "(?:" + Z + ")", Z = new RegExp("(?:" + t + Z + "(\\/)?|\\" + le + "(\\" + pe + ")?\\" + ae + "(?:(?:\\/(\\w+))\\s*|!--[\\s\\S]*?--))" + e, "g"), 
        U = new RegExp("<.*>|([^\\\\]|^)[{}]|" + t + ".*" + e), oe) : re.delimiters;
    }
    function p(t, e) {
        e || t === !0 || (e = t, t = void 0);
        var i, n, r, s, o = this, l = !e || "root" === e;
        if (t) {
            if (s = e && o.type === e && o, !s) if (i = o.views, o._.useKey) {
                for (n in i) if (s = e ? i[n].get(t, e) : i[n]) break;
            } else for (n = 0, r = i.length; !s && r > n; n++) s = e ? i[n].get(t, e) : i[n];
        } else if (l) for (;o.parent; ) s = o, o = o.parent; else for (;o && !s; ) s = o.type === e ? o : void 0, 
        o = o.parent;
        return s;
    }
    function c() {
        var t = this.get("item");
        return t ? t.index : void 0;
    }
    function d() {
        return this.index;
    }
    function f(e) {
        var i, n = this, r = n.linkCtx, s = (n.ctx || {})[e];
        return void 0 === s && r && r.ctx && (s = r.ctx[e]), void 0 === s && (s = ee[e]), 
        s && Q(s) && !s._wrp && (i = function() {
            return s.apply(this && this !== t ? this : n, arguments);
        }, i._wrp = n, u(i, s)), i || s;
    }
    function g(t) {
        return t && (t.fn ? t : this.getRsc("templates", t) || Y(t));
    }
    function m(t, e, i, n) {
        var s, o, l = "number" == typeof i && e.tmpl.bnds[i - 1], a = e.linkCtx;
        return void 0 !== n ? i = n = {
            props: {},
            args: [ n ]
        } : l && (i = l(e.data, e, ne)), o = i.args[0], (t || l) && (s = a && a.tag, s || (s = u(new ne._tg(), {
            _: {
                inline: !a,
                bnd: l,
                unlinked: !0
            },
            tagName: ":",
            cvt: t,
            flow: !0,
            tagCtx: i
        }), a && (a.tag = s, s.linkCtx = a), i.ctx = F(i.ctx, (a ? a.view : e).ctx)), s._er = n && o, 
        r(s, i), i.view = e, s.ctx = i.ctx || {}, i.ctx = void 0, o = s.cvtArgs(s.convert || "true" !== t && t)[0], 
        o = l && e._.onRender ? e._.onRender(o, e, s) : o), void 0 != o ? o : "";
    }
    function v(t) {
        var e = this, i = e.tagCtx, n = i.view, r = i.args;
        return t = e.convert || t, t = t && ("" + t === t ? n.getRsc("converters", t) || j("Unknown converter: '" + t + "'") : t), 
        r = r.length || i.index ? t ? r.slice() : r : [ n.data ], t && (t.depends && (e.depends = ne.getDeps(e.depends, e, t.depends, t)), 
        r[0] = t.apply(e, r)), r;
    }
    function y(t, e) {
        for (var i, n, r = this; void 0 === i && r; ) n = r.tmpl && r.tmpl[t], i = n && n[e], 
        r = r.parent;
        return i || J[t][e];
    }
    function b(t, e, i, n, s, o) {
        e = e || K;
        var l, a, u, h, p, c, d, f, g, m, v, y, b, C, _, x, k, w, M, S = "", A = e.linkCtx || 0, $ = e.ctx, I = i || e.tmpl, O = "number" == typeof n && e.tmpl.bnds[n - 1];
        for ("tag" === t._is ? (l = t, t = l.tagName, n = l.tagCtxs, u = l.template) : (a = e.getRsc("tags", t) || j("Unknown tag: {{" + t + "}} "), 
        u = a.template), void 0 !== o ? (S += o, n = o = [ {
            props: {},
            args: []
        } ]) : O && (n = O(e.data, e, ne)), f = n.length, d = 0; f > d; d++) m = n[d], (!A || !A.tag || d && !A.tag._.inline || l._er) && ((y = I.tmpls && m.tmpl) && (y = m.content = I.tmpls[y - 1]), 
        m.index = d, m.tmpl = y, m.render = T, m.view = e, m.ctx = F(m.ctx, $)), (i = m.props.tmpl) && (m.tmpl = e.getTmpl(i)), 
        l || (l = new a._ctr(), b = !!l.init, l.parent = c = $ && $.tag, l.tagCtxs = n, 
        M = l.dataMap, A && (l._.inline = !1, A.tag = l, l.linkCtx = A), (l._.bnd = O || A.fn) ? l._.arrVws = {} : l.dataBoundOnly && j("{^{" + t + "}} tag must be data-bound")), 
        n = l.tagCtxs, M = l.dataMap, m.tag = l, M && n && (m.map = n[d].map), l.flow || (v = m.ctx = m.ctx || {}, 
        h = l.parents = v.parentTags = $ && F(v.parentTags, $.parentTags) || {}, c && (h[c.tagName] = c), 
        h[l.tagName] = v.tag = l);
        if (!(l._er = o)) {
            for (r(l, n[0]), l.rendering = {}, d = 0; f > d; d++) m = l.tagCtx = n[d], k = m.props, 
            x = l.cvtArgs(), (C = k.dataMap || M) && (x.length || k.dataMap) && (_ = m.map, 
            (!_ || _.src !== x[0] || s) && (_ && _.src && _.unmap(), _ = m.map = C.map(x[0], k, void 0, !l._.bnd)), 
            x = [ _.tgt ]), l.ctx = m.ctx, d || (b && (w = l.template, l.init(m, A, l.ctx), 
            b = void 0), A && (A.attr = l.attr = A.attr || l.attr), p = l.attr, l._.noVws = p && p !== Te), 
            g = void 0, l.render && (g = l.render.apply(l, x)), x.length || (x = [ e ]), void 0 === g && (g = m.render(x[0], !0) || (s ? void 0 : "")), 
            S = S ? S + (g || "") : g;
            l.rendering = void 0;
        }
        return l.tagCtx = n[0], l.ctx = l.tagCtx.ctx, l._.noVws && l._.inline && (S = "text" === p ? te.html(S) : ""), 
        O && e._.onRender ? e._.onRender(S, e, l) : S;
    }
    function C(t, e, i, n, r, s, o, l) {
        var a, u, h, p = this, d = "array" === e;
        p.content = l, p.views = d ? [] : {}, p.parent = i, p.type = e || "top", p.data = n, 
        p.tmpl = r, h = p._ = {
            key: 0,
            useKey: d ? 0 : 1,
            id: "" + Ae++,
            onRender: o,
            bnds: {}
        }, p.linked = !!o, i ? (a = i.views, u = i._, u.useKey ? (a[h.key = "_" + u.useKey++] = p, 
        p.index = Ee, p.getIndex = c) : a.length === (h.key = p.index = s) ? a.push(p) : a.splice(s, 0, p), 
        p.ctx = t || i.ctx) : p.ctx = t;
    }
    function _(t) {
        var e, i, n, r, s, o, l;
        for (e in Ve) if (s = Ve[e], (o = s.compile) && (i = t[e + "s"])) for (n in i) r = i[n] = o(n, i[n], t, 0), 
        r._is = e, r && (l = ne.onStore[e]) && l(n, r, o);
    }
    function x(t, e, i) {
        function r() {
            var e = this;
            e._ = {
                inline: !0,
                unlinked: !0
            }, e.tagName = t;
        }
        var s, o, l, a = new ne._tg();
        if (Q(e) ? e = {
            depends: e.depends,
            render: e
        } : "" + e === e && (e = {
            template: e
        }), o = e.baseTag) {
            e.flow = !!e.flow, e.baseTag = o = "" + o === o ? i && i.tags[o] || ie[o] : o, a = u(a, o);
            for (l in e) a[l] = n(o[l], e[l]);
        } else a = u(a, e);
        return void 0 !== (s = a.template) && (a.template = "" + s === s ? Y[s] || Y(s) : s), 
        a.init !== !1 && ((r.prototype = a).constructor = a._ctr = r), i && (a._parentTmpl = i), 
        a;
    }
    function k(t) {
        return this.base.apply(this, t);
    }
    function w(t, i, n, r) {
        function s(i) {
            var s, l;
            if ("" + i === i || i.nodeType > 0 && (o = i)) {
                if (!o) if (/^\.\/[^\\:*?"<>]*$/.test(i)) (l = Y[t = t || i]) ? i = l : o = document.getElementById(i); else if (e.fn && !U.test(i)) try {
                    o = e(document).find(i)[0];
                } catch (a) {}
                o && (r ? i = o.innerHTML : (s = o.getAttribute(Oe), s ? s !== je ? (i = Y[s], delete Y[s]) : e.fn && (i = e.data(o)[je]) : (t = t || (e.fn ? je : i), 
                i = w(t, o.innerHTML, n, r)), i.tmplName = t = t || s, t !== je && (Y[t] = i), o.setAttribute(Oe, t), 
                e.fn && e.data(o, je, i))), o = void 0;
            } else i.fn || (i = void 0);
            return i;
        }
        var o, l, a = i = i || "";
        return 0 === r && (r = void 0, a = s(a)), r = r || (i.markup ? i : {}), r.tmplName = t, 
        n && (r._parentTmpl = n), !a && i.markup && (a = s(i.markup)) && a.fn && (a = a.markup), 
        void 0 !== a ? (a.fn || i.fn ? a.fn && (l = a) : (i = S(a, r), z(a.replace(be, "\\$&"), i)), 
        l || (_(r), l = u(function() {
            return i.render.apply(i, arguments);
        }, i)), t && !n && t !== je && (ze[t] = l), l) : void 0;
    }
    function M(t) {
        function e(e, i) {
            this.tgt = t.getTgt(e, i);
        }
        return Q(t) && (t = {
            getTgt: t
        }), t.baseMap && (t = u(u({}, t.baseMap), t)), t.map = function(t, i) {
            return new e(t, i);
        }, t;
    }
    function S(t, i) {
        var n, r = se._wm || {}, s = u({
            tmpls: [],
            links: {},
            bnds: [],
            _is: "template",
            render: T
        }, i);
        return s.markup = t, i.htmlTag || (n = xe.exec(t), s.htmlTag = n ? n[1].toLowerCase() : ""), 
        n = r[s.htmlTag], n && n !== r.div && (s.markup = e.trim(s.markup)), s;
    }
    function A(t, e) {
        function i(r, s, o) {
            var l, a, u, h;
            if (r && typeof r === Ie && !r.nodeType && !r.markup && !r.getTgt) {
                for (u in r) i(u, r[u], s);
                return J;
            }
            return void 0 === s && (s = r, r = void 0), r && "" + r !== r && (o = s, s = r, 
            r = void 0), h = o ? o[n] = o[n] || {} : i, a = e.compile, null === s ? r && delete h[r] : (s = a ? a.call(h, r, s, o, 0) : s, 
            r && (h[r] = s)), a && s && (s._is = t), s && (l = ne.onStore[t]) && l(r, s, a), 
            s;
        }
        var n = t + "s";
        J[n] = i;
    }
    function $(t) {
        oe[t] = function(e) {
            return arguments.length ? (re[t] = e, oe) : re[t];
        };
    }
    function T(t, e, i, n, r, s) {
        var o, l, a, u, h, p, c, d, f = n, g = "";
        if (e === !0 ? (i = e, e = void 0) : typeof e !== Ie && (e = void 0), (a = this.tag) ? (h = this, 
        f = f || h.view, u = f.getTmpl(a.template || h.tmpl), arguments.length || (t = f)) : u = this, 
        u) {
            if (!f && t && "view" === t._is && (f = t), f && t === f && (t = f.data), p = !f, 
            de = de || p, f || ((e = e || {}).root = t), !de || se.useViews || u.useViews || f && f !== K) g = I(u, t, e, i, f, r, s, a); else {
                if (f ? (c = f.data, d = f.index, f.index = Ee) : (f = K, f.data = t, f.ctx = e), 
                X(t) && !i) for (o = 0, l = t.length; l > o; o++) f.index = o, f.data = t[o], g += u.fn(t[o], f, ne); else f.data = t, 
                g += u.fn(t, f, ne);
                f.data = c, f.index = d;
            }
            p && (de = void 0);
        }
        return g;
    }
    function I(t, e, i, n, r, s, o, l) {
        function a(t) {
            _ = u({}, i), _[b] = t;
        }
        var h, p, c, d, f, g, m, v, y, b, _, x, k = "";
        if (l && (y = l.tagName, x = l.tagCtx, i = i ? F(i, l.ctx) : l.ctx, t === r.content ? m = t !== r.ctx._wrp ? r.ctx._wrp : void 0 : t !== x.content ? t === l.template ? (m = x.tmpl, 
        i._wrp = x.content) : m = x.content || r.content : m = r.content, x.props.link === !1 && (i = i || {}, 
        i.link = !1), (b = x.props.itemVar) && ("~" !== b.charAt(0) && E("Use itemVar='~myItem'"), 
        b = b.slice(1))), r && (o = o || r._.onRender, i = F(i, r.ctx)), s === !0 && (g = !0, 
        s = 0), o && (i && i.link === !1 || l && l._.noVws) && (o = void 0), v = o, o === !0 && (v = void 0, 
        o = r._.onRender), i = t.helpers ? F(t.helpers, i) : i, _ = i, X(e) && !n) for (c = g ? r : void 0 !== s && r || new C(i, "array", r, e, t, s, o), 
        r && r._.useKey && (c._.bnd = !l || l._.bnd && l), b && (c.it = b), b = c.it, h = 0, 
        p = e.length; p > h; h++) b && a(e[h]), d = new C(_, "item", c, e[h], t, (s || 0) + h, o, m), 
        f = t.fn(e[h], d, ne), k += c._.onRender ? c._.onRender(f, d) : f; else b && a(e), 
        c = g ? r : new C(_, y || "data", r, e, t, s, o, m), l && !l.flow && (c.tag = l), 
        k += t.fn(e, c, ne);
        return v ? v(k, c) : k;
    }
    function O(t, e, i) {
        var n = void 0 !== i ? Q(i) ? i.call(e.data, t, e) : i || "" : "{Error: " + t.message + "}";
        return re.onError && void 0 !== (i = re.onError.call(e.data, t, i && n, e)) && (n = i), 
        e && !e.linkCtx ? te.html(n) : n;
    }
    function j(t) {
        throw new ne.Err(t);
    }
    function E(t) {
        j("Syntax error\n" + t);
    }
    function z(t, e, i, n, r) {
        function s(e) {
            e -= d, e && g.push(t.substr(d, e).replace(ve, "\\n"));
        }
        function o(e, i) {
            e && (e += "}}", E((i ? "{{" + i + "}} block has {{/" + e + " without {{" + e : "Unmatched or missing {{/" + e) + ", in template:\n" + t));
        }
        function l(l, a, c, v, y, b, C, _, x, k, w, M) {
            (C && a || x && !c || _ && ":" === _.slice(-1) || k) && E(l), b && (y = ":", v = Te), 
            x = x || i && !r;
            var S = (a || i) && [ [] ], A = "", $ = "", T = "", I = "", O = "", j = "", z = "", P = "", V = !x && !y;
            c = c || (_ = _ || "#data", y), s(M), d = M + l.length, C ? p && g.push([ "*", "\n" + _.replace(/^:/, "ret+= ").replace(ye, "$1") + ";\n" ]) : c ? ("else" === c && (_e.test(_) && E('for "{{else if expr}}" use "{{else expr}}"'), 
            S = m[7] && [ [] ], m[8] = t.substring(m[8], M), m = f.pop(), g = m[2], V = !0), 
            _ && L(_.replace(ve, " "), S, e).replace(Ce, function(t, e, i, n, r, s, o, l) {
                return n = "'" + r + "':", o ? ($ += s + ",", I += "'" + l + "',") : i ? (T += n + s + ",", 
                j += n + "'" + l + "',") : e ? z += s : ("trigger" === r && (P += s), A += n + s + ",", 
                O += n + "'" + l + "',", h = h || Me.test(r)), "";
            }).slice(0, -1), S && S[0] && S.pop(), u = [ c, v || !!n || h || "", V && [], B(I || (":" === c ? "'#data'," : ""), O, j), B($ || (":" === c ? "data," : ""), A, T), z, P, S || 0 ], 
            g.push(u), V && (f.push(m), m = u, m[8] = d)) : w && (o(w !== m[0] && "else" !== m[0] && w, m[0]), 
            m[8] = t.substring(m[8], M), m = f.pop()), o(!m && w), g = m[2];
        }
        var a, u, h, p = re.allowCode || e && e.allowCode || oe.allowCode === !0, c = [], d = 0, f = [], g = c, m = [ , , c ];
        return p && (e.allowCode = p), i && (void 0 !== n && (t = t.slice(0, -n.length - 2) + he), 
        t = le + t + he), o(f[0] && f[0][2].pop()[0]), t.replace(Z, l), s(t.length), (d = c[c.length - 1]) && o("" + d !== d && +d[8] === d[8] && d[0]), 
        i ? (a = R(c, t, i), P(a, [ c[0][7] ])) : a = R(c, e), a;
    }
    function P(t, e) {
        var i, n, r = 0, s = e.length;
        for (t.deps = []; s > r; r++) {
            n = e[r];
            for (i in n) "_jsvto" !== i && n[i].length && (t.deps = t.deps.concat(n[i]));
        }
        t.paths = n;
    }
    function B(t, e, i) {
        return [ t.slice(0, -1), e.slice(0, -1), i.slice(0, -1) ];
    }
    function V(t, e) {
        return "\n\t" + (e ? e + ":{" : "") + "args:[" + t[0] + "]" + (t[1] || !e ? ",\n\tprops:{" + t[1] + "}" : "") + (t[2] ? ",\n\tctx:{" + t[2] + "}" : "");
    }
    function L(t, e, i) {
        function n(n, v, y, b, C, _, x, k, w, M, S, A, $, T, I, O, j, P, B, V) {
            function L(t, i, n, o, l, a, p, c) {
                var d = "." === n;
                if (n && (C = C.slice(i.length), /^\.?constructor$/.test(c || C) && E(t), d || (t = (o ? 'view.hlp("' + o + '")' : l ? "view" : "data") + (c ? (a ? "." + a : o ? "" : l ? "" : "." + n) + (p || "") : (c = o ? "" : l ? a || "" : n, 
                "")), t += c ? "." + c : "", t = i + ("view.data" === t.slice(0, 9) ? t.slice(5) : t)), 
                u)) {
                    if (D = "linkTo" === r ? s = e._jsvto = e._jsvto || [] : h.bd, N = d && D[D.length - 1]) {
                        if (N._jsv) {
                            for (;N.sb; ) N = N.sb;
                            N.bnd && (C = "^" + C.slice(1)), N.sb = C, N.bnd = N.bnd || "^" === C.charAt(0);
                        }
                    } else D.push(C);
                    m[f] = B + (d ? 1 : 0);
                }
                return t;
            }
            b = u && b, b && !k && (C = b + C), _ = _ || "", y = y || v || A, C = C || w, M = M || j || "";
            var R, F, D, N, H;
            if (!x || a || l) {
                if (u && O && !a && !l && (!r || o || s) && (R = m[f - 1], V.length - 1 > B - (R || 0))) {
                    if (R = V.slice(R, B + n.length), F !== !0) if (D = s || p[f - 1].bd, N = D[D.length - 1], 
                    N && N.prm) {
                        for (;N.sb && N.sb.prm; ) N = N.sb;
                        H = N.sb = {
                            path: N.sb,
                            bnd: N.bnd
                        };
                    } else D.push(H = {
                        path: D.pop()
                    });
                    O = ae + ":" + R + " onerror=''" + ue, F = d[O], F || (d[O] = !0, d[O] = F = z(O, i, !0)), 
                    F !== !0 && H && (H._jsv = F, H.prm = h.bd, H.bnd = H.bnd || H.path && H.path.indexOf("^") >= 0);
                }
                return a ? (a = !$, a ? n : A + '"') : l ? (l = !T, l ? n : A + '"') : (y ? (m[f] = B++, 
                h = p[++f] = {
                    bd: []
                }, y) : "") + (P ? f ? "" : (c = V.slice(c, B), (r ? (r = o = s = !1, "\b") : "\b,") + c + (c = B + n.length, 
                u && e.push(h.bd = []), "\b")) : k ? (f && E(t), u && e.pop(), r = C, o = b, c = B + n.length, 
                b && (u = h.bd = e[r] = []), C + ":") : C ? C.split("^").join(".").replace(ge, L) + (M ? (h = p[++f] = {
                    bd: []
                }, g[f] = !0, M) : _) : _ ? _ : I ? (g[f] = !1, h = p[--f], I + (M ? (h = p[++f], 
                g[f] = !0, M) : "")) : S ? (g[f] || E(t), ",") : v ? "" : (a = $, l = T, '"'));
            }
            E(t);
        }
        var r, s, o, l, a, u = e && e[0], h = {
            bd: u
        }, p = {
            0: h
        }, c = 0, d = i ? i.links : u && (u.links = u.links || {}), f = 0, g = {}, m = {}, v = (t + (i ? " " : "")).replace(me, n);
        return !f && v || E(t);
    }
    function R(t, e, i) {
        var n, r, s, o, l, a, u, h, p, c, d, f, g, m, v, y, b, C, _, x, k, w, M, A, $, T, I, O, j, z, B = 0, L = se.useViews || e.useViews || e.tags || e.templates || e.helpers || e.converters, F = "", D = {}, N = t.length;
        for ("" + e === e ? (C = i ? 'data-link="' + e.replace(ve, " ").slice(1, -1) + '"' : e, 
        e = 0) : (C = e.tmplName || "unnamed", e.allowCode && (D.allowCode = !0), e.debug && (D.debug = !0), 
        d = e.bnds, b = e.tmpls), n = 0; N > n; n++) if (r = t[n], "" + r === r) F += '\n+"' + r + '"'; else if (s = r[0], 
        "*" === s) F += ";\n" + r[1] + "\nret=ret"; else {
            if (o = r[1], k = !i && r[2], l = V(r[3], "params") + "}," + V(g = r[4]), O = r[5], 
            z = r[6], w = r[8] && r[8].replace(ye, "$1"), ($ = "else" === s) ? f && f.push(r[7]) : (B = 0, 
            d && (f = r[7]) && (f = [ f ], B = d.push(1))), L = L || g[1] || g[2] || f || /view.(?!index)/.test(g[0]), 
            (T = ":" === s) ? (o && (s = o === Te ? ">" : o + s), z = r[6] || re.trigger) : (k && (_ = S(w, D), 
            _.tmplName = C + "/" + s, _.useViews = _.useViews || L, R(k, _), L = _.useViews, 
            b.push(_)), $ || (x = s, L = L || s && (!ie[s] || !ie[s].flow), A = F, F = ""), 
            M = t[n + 1], M = M && "else" === M[0]), j = O ? ";\ntry{\nret+=" : "\n+", m = "", 
            v = "", T && (f || z || o && o !== Te)) {
                if (I = "return {" + l + "};", y = 'c("' + o + '",view,', I = new Function("data,view,j,u", " // " + C + " " + B + " " + s + "\n" + I), 
                I._er = O, m = y + B + ",", v = ")", I._tag = s, i) return I;
                P(I, f), c = !0;
            }
            if (F += T ? (i ? (O ? "\ntry{\n" : "") + "return " : j) + (c ? (c = void 0, L = p = !0, 
            y + (f ? (d[B - 1] = I, B) : "{" + l + "}") + ")") : ">" === s ? (u = !0, "h(" + g[0] + ")") : (h = !0, 
            "((v=" + g[0] + ')!=null?v:"")')) : (a = !0, "\n{view:view,tmpl:" + (k ? b.length : "0") + "," + l + "},"), 
            x && !M) {
                if (F = "[" + F.slice(0, -1) + "]", y = 't("' + x + '",view,this,', i || f) {
                    if (F = new Function("data,view,j,u", " // " + C + " " + B + " " + x + "\nreturn " + F + ";"), 
                    F._er = O, F._tag = x, f && P(d[B - 1] = F, f), i) return F;
                    m = y + B + ",undefined,", v = ")";
                }
                F = A + j + y + (B || F) + ")", f = 0, x = 0;
            }
            O && (L = !0, F += ";\n}catch(e){ret" + (i ? "urn " : "+=") + m + "j._err(e,view," + O + ")" + v + ";}\n" + (i ? "" : "ret=ret"));
        }
        F = "// " + C + "\nvar v" + (a ? ",t=j._tag" : "") + (p ? ",c=j._cnvt" : "") + (u ? ",h=j._html" : "") + (i ? ";\n" : ',ret=""\n') + (D.debug ? "debugger;" : "") + F + (i ? "\n" : ";\nreturn ret;"), 
        re.debugMode !== !1 && (F = "try {\n" + F + "\n}catch(e){\nreturn j._err(e, view);\n}");
        try {
            F = new Function("data,view,j,u", F);
        } catch (H) {
            E("Compiled template code:\n\n" + F + '\n: "' + H.message + '"');
        }
        return e && (e.fn = F, e.useViews = !!L), F;
    }
    function F(t, e) {
        return t && t !== e ? e ? u(u({}, e), t) : t : e && u({}, e);
    }
    function D(t) {
        return $e[t] || ($e[t] = "&#" + t.charCodeAt(0) + ";");
    }
    function N(t) {
        var e, i, n = [];
        if (typeof t === Ie) for (e in t) i = t[e], i && i.toJSON && !i.toJSON() || Q(i) || n.push({
            key: e,
            prop: i
        });
        return n;
    }
    function H(t, i, n) {
        var r = this.jquery && (this[0] || j('Unknown template: "' + this.selector + '"')), s = r.getAttribute(Oe);
        return T.call(s ? e.data(r)[je] : Y(r), t, i, n);
    }
    function G(t) {
        return void 0 != t ? we.test(t) && ("" + t).replace(Se, D) || t : "";
    }
    var q = e === !1;
    e = e && e.fn ? e : t.jQuery;
    var W, Z, U, K, J, Q, X, Y, te, ee, ie, ne, re, se, oe, le, ae, ue, he, pe, ce, de, fe = "v0.9.75", ge = /^(!*?)(?:null|true|false|\d[\d.]*|([\w$]+|\.|~([\w$]+)|#(view|([\w$]+))?)([\w$.^]*?)(?:[.[^]([\w$]+)\]?)?)$/g, me = /(\()(?=\s*\()|(?:([([])\s*)?(?:(\^?)(!*?[#~]?[\w$.^]+)?\s*((\+\+|--)|\+|-|&&|\|\||===|!==|==|!=|<=|>=|[<>%*:?\/]|(=))\s*|(!*?[#~]?[\w$.^]+)([([])?)|(,\s*)|(\(?)\\?(?:(')|("))|(?:\s*(([)\]])(?=\s*[.^]|\s*$|[^([])|[)\]])([([]?))|(\s+)/g, ve = /[ \t]*(\r\n|\n|\r)/g, ye = /\\(['"])/g, be = /['"\\]/g, Ce = /(?:\x08|^)(onerror:)?(?:(~?)(([\w$_\.]+):)?([^\x08]+))\x08(,)?([^\x08]+)/gi, _e = /^if\s/, xe = /<(\w+)[>\s]/, ke = /[\x00`><"'&]/g, we = /[\x00`><\"'&]/, Me = /^on[A-Z]|^convert(Back)?$/, Se = ke, Ae = 0, $e = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "\0": "&#0;",
        "'": "&#39;",
        '"': "&#34;",
        "`": "&#96;"
    }, Te = "html", Ie = "object", Oe = "data-jsv-tmpl", je = "jsvTmpl", Ee = "For #index in nested block use #getIndex().", ze = {}, Pe = t.jsrender, Be = Pe && e && !e.render, Ve = {
        template: {
            compile: w
        },
        tag: {
            compile: x
        },
        helper: {},
        converter: {}
    };
    if (J = {
        jsviews: fe,
        sub: {
            View: C,
            Err: a,
            tmplFn: z,
            parse: L,
            extend: u,
            extendCtx: F,
            syntaxErr: E,
            onStore: {},
            addSetting: $,
            settings: {
                allowCode: !1
            },
            advSet: o,
            _ths: r,
            _tg: function() {},
            _cnvt: m,
            _tag: b,
            _er: j,
            _err: O,
            _html: G
        },
        settings: {
            delimiters: h,
            advanced: function(t) {
                return t ? (u(se, t), ne.advSet(), oe) : se;
            }
        },
        map: M
    }, (a.prototype = new Error()).constructor = a, c.depends = function() {
        return [ this.get("item"), "index" ];
    }, d.depends = "index", C.prototype = {
        get: p,
        getIndex: d,
        getRsc: y,
        getTmpl: g,
        hlp: f,
        _is: "view"
    }, !(Pe || e && e.render)) {
        for (W in Ve) A(W, Ve[W]);
        Y = J.templates, te = J.converters, ee = J.helpers, ie = J.tags, ne = J.sub, oe = J.settings, 
        ne._tg.prototype = {
            baseApply: k,
            cvtArgs: v
        }, K = ne.topView = new C(), e ? (e.fn.render = H, e.observable && (u(ne, e.views.sub), 
        J.map = e.views.map)) : (e = {}, q && (t.jsrender = e), e.renderFile = e.__express = e.compile = function() {
            throw "Node.js: use npm jsrender, or jsrender-node.js";
        }, e.isFunction = function(t) {
            return "function" == typeof t;
        }, e.isArray = Array.isArray || function(t) {
            return "[object Array]" === {}.toString.call(t);
        }, ne._jq = function(t) {
            t !== e && (u(t, e), e = t, e.fn.render = H, delete e.jsrender);
        }, e.jsrender = fe), re = ne.settings, re.allowCode = !1, Q = e.isFunction, X = e.isArray, 
        e.render = ze, e.views = J, e.templates = Y = J.templates;
        for (ce in re) $(ce);
        (oe.debugMode = function(t) {
            return void 0 === t ? re.debugMode : (re.debugMode = t, re.onError = t + "" === t ? new Function("", "return '" + t + "';") : Q(t) ? t : void 0, 
            oe);
        })(!1), se = re.advanced = {
            useViews: !1,
            _jsv: !1
        }, ie({
            if: {
                render: function(t) {
                    var e = this, i = e.tagCtx, n = e.rendering.done || !t && (arguments.length || !i.index) ? "" : (e.rendering.done = !0, 
                    e.selected = i.index, i.render(i.view, !0));
                    return n;
                },
                flow: !0
            },
            for: {
                render: function(t) {
                    var e, i = !arguments.length, n = this, r = n.tagCtx, s = "", o = 0;
                    return n.rendering.done || (e = i ? r.view.data : t, void 0 !== e && (s += r.render(e, i), 
                    o += X(e) ? e.length : 1), (n.rendering.done = o) && (n.selected = r.index)), s;
                },
                flow: !0
            },
            props: {
                baseTag: "for",
                dataMap: M(N),
                flow: !0
            },
            include: {
                flow: !0
            },
            "*": {
                render: s,
                flow: !0
            },
            ":*": {
                render: s,
                flow: !0
            },
            dbg: ee.dbg = te.dbg = l
        }), te({
            html: G,
            attr: G,
            url: function(t) {
                return void 0 != t ? encodeURI("" + t) : null === t ? t : "";
            }
        }), oe.delimiters("{{", "}}", "^");
    }
    return Be && Pe.views.sub._jq(e), e || Pe;
}, window), function() {
    var t = this, e = t._, i = {}, n = Array.prototype, r = Object.prototype, s = Function.prototype, o = n.push, l = n.slice, a = n.concat, u = r.toString, h = r.hasOwnProperty, p = n.forEach, c = n.map, d = n.reduce, f = n.reduceRight, g = n.filter, m = n.every, v = n.some, y = n.indexOf, b = n.lastIndexOf, C = Array.isArray, _ = Object.keys, x = s.bind, k = function(t) {
        return t instanceof k ? t : this instanceof k ? void (this._wrapped = t) : new k(t);
    };
    "undefined" != typeof exports ? ("undefined" != typeof module && module.exports && (exports = module.exports = k), 
    exports._ = k) : t._ = k, k.VERSION = "1.6.0";
    var w = k.each = k.forEach = function(t, e, n) {
        if (null == t) return t;
        if (p && t.forEach === p) t.forEach(e, n); else if (t.length === +t.length) {
            for (var r = 0, s = t.length; s > r; r++) if (e.call(n, t[r], r, t) === i) return;
        } else for (var o = k.keys(t), r = 0, s = o.length; s > r; r++) if (e.call(n, t[o[r]], o[r], t) === i) return;
        return t;
    };
    k.map = k.collect = function(t, e, i) {
        var n = [];
        return null == t ? n : c && t.map === c ? t.map(e, i) : (w(t, function(t, r, s) {
            n.push(e.call(i, t, r, s));
        }), n);
    };
    var M = "Reduce of empty array with no initial value";
    k.reduce = k.foldl = k.inject = function(t, e, i, n) {
        var r = arguments.length > 2;
        if (null == t && (t = []), d && t.reduce === d) return n && (e = k.bind(e, n)), 
        r ? t.reduce(e, i) : t.reduce(e);
        if (w(t, function(t, s, o) {
            r ? i = e.call(n, i, t, s, o) : (i = t, r = !0);
        }), !r) throw new TypeError(M);
        return i;
    }, k.reduceRight = k.foldr = function(t, e, i, n) {
        var r = arguments.length > 2;
        if (null == t && (t = []), f && t.reduceRight === f) return n && (e = k.bind(e, n)), 
        r ? t.reduceRight(e, i) : t.reduceRight(e);
        var s = t.length;
        if (s !== +s) {
            var o = k.keys(t);
            s = o.length;
        }
        if (w(t, function(l, a, u) {
            a = o ? o[--s] : --s, r ? i = e.call(n, i, t[a], a, u) : (i = t[a], r = !0);
        }), !r) throw new TypeError(M);
        return i;
    }, k.find = k.detect = function(t, e, i) {
        var n;
        return S(t, function(t, r, s) {
            return e.call(i, t, r, s) ? (n = t, !0) : void 0;
        }), n;
    }, k.filter = k.select = function(t, e, i) {
        var n = [];
        return null == t ? n : g && t.filter === g ? t.filter(e, i) : (w(t, function(t, r, s) {
            e.call(i, t, r, s) && n.push(t);
        }), n);
    }, k.reject = function(t, e, i) {
        return k.filter(t, function(t, n, r) {
            return !e.call(i, t, n, r);
        }, i);
    }, k.every = k.all = function(t, e, n) {
        e || (e = k.identity);
        var r = !0;
        return null == t ? r : m && t.every === m ? t.every(e, n) : (w(t, function(t, s, o) {
            return (r = r && e.call(n, t, s, o)) ? void 0 : i;
        }), !!r);
    };
    var S = k.some = k.any = function(t, e, n) {
        e || (e = k.identity);
        var r = !1;
        return null == t ? r : v && t.some === v ? t.some(e, n) : (w(t, function(t, s, o) {
            return r || (r = e.call(n, t, s, o)) ? i : void 0;
        }), !!r);
    };
    k.contains = k.include = function(t, e) {
        return null == t ? !1 : y && t.indexOf === y ? -1 != t.indexOf(e) : S(t, function(t) {
            return t === e;
        });
    }, k.invoke = function(t, e) {
        var i = l.call(arguments, 2), n = k.isFunction(e);
        return k.map(t, function(t) {
            return (n ? e : t[e]).apply(t, i);
        });
    }, k.pluck = function(t, e) {
        return k.map(t, k.property(e));
    }, k.where = function(t, e) {
        return k.filter(t, k.matches(e));
    }, k.findWhere = function(t, e) {
        return k.find(t, k.matches(e));
    }, k.max = function(t, e, i) {
        if (!e && k.isArray(t) && t[0] === +t[0] && t.length < 65535) return Math.max.apply(Math, t);
        var n = -1 / 0, r = -1 / 0;
        return w(t, function(t, s, o) {
            var l = e ? e.call(i, t, s, o) : t;
            l > r && (n = t, r = l);
        }), n;
    }, k.min = function(t, e, i) {
        if (!e && k.isArray(t) && t[0] === +t[0] && t.length < 65535) return Math.min.apply(Math, t);
        var n = 1 / 0, r = 1 / 0;
        return w(t, function(t, s, o) {
            var l = e ? e.call(i, t, s, o) : t;
            r > l && (n = t, r = l);
        }), n;
    }, k.shuffle = function(t) {
        var e, i = 0, n = [];
        return w(t, function(t) {
            e = k.random(i++), n[i - 1] = n[e], n[e] = t;
        }), n;
    }, k.sample = function(t, e, i) {
        return null == e || i ? (t.length !== +t.length && (t = k.values(t)), t[k.random(t.length - 1)]) : k.shuffle(t).slice(0, Math.max(0, e));
    };
    var A = function(t) {
        return null == t ? k.identity : k.isFunction(t) ? t : k.property(t);
    };
    k.sortBy = function(t, e, i) {
        return e = A(e), k.pluck(k.map(t, function(t, n, r) {
            return {
                value: t,
                index: n,
                criteria: e.call(i, t, n, r)
            };
        }).sort(function(t, e) {
            var i = t.criteria, n = e.criteria;
            if (i !== n) {
                if (i > n || void 0 === i) return 1;
                if (n > i || void 0 === n) return -1;
            }
            return t.index - e.index;
        }), "value");
    };
    var $ = function(t) {
        return function(e, i, n) {
            var r = {};
            return i = A(i), w(e, function(s, o) {
                var l = i.call(n, s, o, e);
                t(r, l, s);
            }), r;
        };
    };
    k.groupBy = $(function(t, e, i) {
        k.has(t, e) ? t[e].push(i) : t[e] = [ i ];
    }), k.indexBy = $(function(t, e, i) {
        t[e] = i;
    }), k.countBy = $(function(t, e) {
        k.has(t, e) ? t[e]++ : t[e] = 1;
    }), k.sortedIndex = function(t, e, i, n) {
        i = A(i);
        for (var r = i.call(n, e), s = 0, o = t.length; o > s; ) {
            var l = s + o >>> 1;
            i.call(n, t[l]) < r ? s = l + 1 : o = l;
        }
        return s;
    }, k.toArray = function(t) {
        return t ? k.isArray(t) ? l.call(t) : t.length === +t.length ? k.map(t, k.identity) : k.values(t) : [];
    }, k.size = function(t) {
        return null == t ? 0 : t.length === +t.length ? t.length : k.keys(t).length;
    }, k.first = k.head = k.take = function(t, e, i) {
        return null == t ? void 0 : null == e || i ? t[0] : 0 > e ? [] : l.call(t, 0, e);
    }, k.initial = function(t, e, i) {
        return l.call(t, 0, t.length - (null == e || i ? 1 : e));
    }, k.last = function(t, e, i) {
        return null == t ? void 0 : null == e || i ? t[t.length - 1] : l.call(t, Math.max(t.length - e, 0));
    }, k.rest = k.tail = k.drop = function(t, e, i) {
        return l.call(t, null == e || i ? 1 : e);
    }, k.compact = function(t) {
        return k.filter(t, k.identity);
    };
    var T = function(t, e, i) {
        return e && k.every(t, k.isArray) ? a.apply(i, t) : (w(t, function(t) {
            k.isArray(t) || k.isArguments(t) ? e ? o.apply(i, t) : T(t, e, i) : i.push(t);
        }), i);
    };
    k.flatten = function(t, e) {
        return T(t, e, []);
    }, k.without = function(t) {
        return k.difference(t, l.call(arguments, 1));
    }, k.partition = function(t, e) {
        var i = [], n = [];
        return w(t, function(t) {
            (e(t) ? i : n).push(t);
        }), [ i, n ];
    }, k.uniq = k.unique = function(t, e, i, n) {
        k.isFunction(e) && (n = i, i = e, e = !1);
        var r = i ? k.map(t, i, n) : t, s = [], o = [];
        return w(r, function(i, n) {
            (e ? n && o[o.length - 1] === i : k.contains(o, i)) || (o.push(i), s.push(t[n]));
        }), s;
    }, k.union = function() {
        return k.uniq(k.flatten(arguments, !0));
    }, k.intersection = function(t) {
        var e = l.call(arguments, 1);
        return k.filter(k.uniq(t), function(t) {
            return k.every(e, function(e) {
                return k.contains(e, t);
            });
        });
    }, k.difference = function(t) {
        var e = a.apply(n, l.call(arguments, 1));
        return k.filter(t, function(t) {
            return !k.contains(e, t);
        });
    }, k.zip = function() {
        for (var t = k.max(k.pluck(arguments, "length").concat(0)), e = new Array(t), i = 0; t > i; i++) e[i] = k.pluck(arguments, "" + i);
        return e;
    }, k.object = function(t, e) {
        if (null == t) return {};
        for (var i = {}, n = 0, r = t.length; r > n; n++) e ? i[t[n]] = e[n] : i[t[n][0]] = t[n][1];
        return i;
    }, k.indexOf = function(t, e, i) {
        if (null == t) return -1;
        var n = 0, r = t.length;
        if (i) {
            if ("number" != typeof i) return n = k.sortedIndex(t, e), t[n] === e ? n : -1;
            n = 0 > i ? Math.max(0, r + i) : i;
        }
        if (y && t.indexOf === y) return t.indexOf(e, i);
        for (;r > n; n++) if (t[n] === e) return n;
        return -1;
    }, k.lastIndexOf = function(t, e, i) {
        if (null == t) return -1;
        var n = null != i;
        if (b && t.lastIndexOf === b) return n ? t.lastIndexOf(e, i) : t.lastIndexOf(e);
        for (var r = n ? i : t.length; r--; ) if (t[r] === e) return r;
        return -1;
    }, k.range = function(t, e, i) {
        arguments.length <= 1 && (e = t || 0, t = 0), i = arguments[2] || 1;
        for (var n = Math.max(Math.ceil((e - t) / i), 0), r = 0, s = new Array(n); n > r; ) s[r++] = t, 
        t += i;
        return s;
    };
    var I = function() {};
    k.bind = function(t, e) {
        var i, n;
        if (x && t.bind === x) return x.apply(t, l.call(arguments, 1));
        if (!k.isFunction(t)) throw new TypeError();
        return i = l.call(arguments, 2), n = function() {
            if (!(this instanceof n)) return t.apply(e, i.concat(l.call(arguments)));
            I.prototype = t.prototype;
            var r = new I();
            I.prototype = null;
            var s = t.apply(r, i.concat(l.call(arguments)));
            return Object(s) === s ? s : r;
        };
    }, k.partial = function(t) {
        var e = l.call(arguments, 1);
        return function() {
            for (var i = 0, n = e.slice(), r = 0, s = n.length; s > r; r++) n[r] === k && (n[r] = arguments[i++]);
            for (;i < arguments.length; ) n.push(arguments[i++]);
            return t.apply(this, n);
        };
    }, k.bindAll = function(t) {
        var e = l.call(arguments, 1);
        if (0 === e.length) throw new Error("bindAll must be passed function names");
        return w(e, function(e) {
            t[e] = k.bind(t[e], t);
        }), t;
    }, k.memoize = function(t, e) {
        var i = {};
        return e || (e = k.identity), function() {
            var n = e.apply(this, arguments);
            return k.has(i, n) ? i[n] : i[n] = t.apply(this, arguments);
        };
    }, k.delay = function(t, e) {
        var i = l.call(arguments, 2);
        return setTimeout(function() {
            return t.apply(null, i);
        }, e);
    }, k.defer = function(t) {
        return k.delay.apply(k, [ t, 1 ].concat(l.call(arguments, 1)));
    }, k.throttle = function(t, e, i) {
        var n, r, s, o = null, l = 0;
        i || (i = {});
        var a = function() {
            l = i.leading === !1 ? 0 : k.now(), o = null, s = t.apply(n, r), n = r = null;
        };
        return function() {
            var u = k.now();
            l || i.leading !== !1 || (l = u);
            var h = e - (u - l);
            return n = this, r = arguments, 0 >= h ? (clearTimeout(o), o = null, l = u, s = t.apply(n, r), 
            n = r = null) : o || i.trailing === !1 || (o = setTimeout(a, h)), s;
        };
    }, k.debounce = function(t, e, i) {
        var n, r, s, o, l, a = function() {
            var u = k.now() - o;
            e > u ? n = setTimeout(a, e - u) : (n = null, i || (l = t.apply(s, r), s = r = null));
        };
        return function() {
            s = this, r = arguments, o = k.now();
            var u = i && !n;
            return n || (n = setTimeout(a, e)), u && (l = t.apply(s, r), s = r = null), l;
        };
    }, k.once = function(t) {
        var e, i = !1;
        return function() {
            return i ? e : (i = !0, e = t.apply(this, arguments), t = null, e);
        };
    }, k.wrap = function(t, e) {
        return k.partial(e, t);
    }, k.compose = function() {
        var t = arguments;
        return function() {
            for (var e = arguments, i = t.length - 1; i >= 0; i--) e = [ t[i].apply(this, e) ];
            return e[0];
        };
    }, k.after = function(t, e) {
        return function() {
            return --t < 1 ? e.apply(this, arguments) : void 0;
        };
    }, k.keys = function(t) {
        if (!k.isObject(t)) return [];
        if (_) return _(t);
        var e = [];
        for (var i in t) k.has(t, i) && e.push(i);
        return e;
    }, k.values = function(t) {
        for (var e = k.keys(t), i = e.length, n = new Array(i), r = 0; i > r; r++) n[r] = t[e[r]];
        return n;
    }, k.pairs = function(t) {
        for (var e = k.keys(t), i = e.length, n = new Array(i), r = 0; i > r; r++) n[r] = [ e[r], t[e[r]] ];
        return n;
    }, k.invert = function(t) {
        for (var e = {}, i = k.keys(t), n = 0, r = i.length; r > n; n++) e[t[i[n]]] = i[n];
        return e;
    }, k.functions = k.methods = function(t) {
        var e = [];
        for (var i in t) k.isFunction(t[i]) && e.push(i);
        return e.sort();
    }, k.extend = function(t) {
        return w(l.call(arguments, 1), function(e) {
            if (e) for (var i in e) t[i] = e[i];
        }), t;
    }, k.pick = function(t) {
        var e = {}, i = a.apply(n, l.call(arguments, 1));
        return w(i, function(i) {
            i in t && (e[i] = t[i]);
        }), e;
    }, k.omit = function(t) {
        var e = {}, i = a.apply(n, l.call(arguments, 1));
        for (var r in t) k.contains(i, r) || (e[r] = t[r]);
        return e;
    }, k.defaults = function(t) {
        return w(l.call(arguments, 1), function(e) {
            if (e) for (var i in e) void 0 === t[i] && (t[i] = e[i]);
        }), t;
    }, k.clone = function(t) {
        return k.isObject(t) ? k.isArray(t) ? t.slice() : k.extend({}, t) : t;
    }, k.tap = function(t, e) {
        return e(t), t;
    };
    var O = function(t, e, i, n) {
        if (t === e) return 0 !== t || 1 / t == 1 / e;
        if (null == t || null == e) return t === e;
        t instanceof k && (t = t._wrapped), e instanceof k && (e = e._wrapped);
        var r = u.call(t);
        if (r != u.call(e)) return !1;
        switch (r) {
          case "[object String]":
            return t == String(e);

          case "[object Number]":
            return t != +t ? e != +e : 0 == t ? 1 / t == 1 / e : t == +e;

          case "[object Date]":
          case "[object Boolean]":
            return +t == +e;

          case "[object RegExp]":
            return t.source == e.source && t.global == e.global && t.multiline == e.multiline && t.ignoreCase == e.ignoreCase;
        }
        if ("object" != typeof t || "object" != typeof e) return !1;
        for (var s = i.length; s--; ) if (i[s] == t) return n[s] == e;
        var o = t.constructor, l = e.constructor;
        if (o !== l && !(k.isFunction(o) && o instanceof o && k.isFunction(l) && l instanceof l) && "constructor" in t && "constructor" in e) return !1;
        i.push(t), n.push(e);
        var a = 0, h = !0;
        if ("[object Array]" == r) {
            if (a = t.length, h = a == e.length) for (;a-- && (h = O(t[a], e[a], i, n)); ) ;
        } else {
            for (var p in t) if (k.has(t, p) && (a++, !(h = k.has(e, p) && O(t[p], e[p], i, n)))) break;
            if (h) {
                for (p in e) if (k.has(e, p) && !a--) break;
                h = !a;
            }
        }
        return i.pop(), n.pop(), h;
    };
    k.isEqual = function(t, e) {
        return O(t, e, [], []);
    }, k.isEmpty = function(t) {
        if (null == t) return !0;
        if (k.isArray(t) || k.isString(t)) return 0 === t.length;
        for (var e in t) if (k.has(t, e)) return !1;
        return !0;
    }, k.isElement = function(t) {
        return !(!t || 1 !== t.nodeType);
    }, k.isArray = C || function(t) {
        return "[object Array]" == u.call(t);
    }, k.isObject = function(t) {
        return t === Object(t);
    }, w([ "Arguments", "Function", "String", "Number", "Date", "RegExp" ], function(t) {
        k["is" + t] = function(e) {
            return u.call(e) == "[object " + t + "]";
        };
    }), k.isArguments(arguments) || (k.isArguments = function(t) {
        return !(!t || !k.has(t, "callee"));
    }), "function" != typeof /./ && (k.isFunction = function(t) {
        return "function" == typeof t;
    }), k.isFinite = function(t) {
        return isFinite(t) && !isNaN(parseFloat(t));
    }, k.isNaN = function(t) {
        return k.isNumber(t) && t != +t;
    }, k.isBoolean = function(t) {
        return t === !0 || t === !1 || "[object Boolean]" == u.call(t);
    }, k.isNull = function(t) {
        return null === t;
    }, k.isUndefined = function(t) {
        return void 0 === t;
    }, k.has = function(t, e) {
        return h.call(t, e);
    }, k.noConflict = function() {
        return t._ = e, this;
    }, k.identity = function(t) {
        return t;
    }, k.constant = function(t) {
        return function() {
            return t;
        };
    }, k.property = function(t) {
        return function(e) {
            return e[t];
        };
    }, k.matches = function(t) {
        return function(e) {
            if (e === t) return !0;
            for (var i in t) if (t[i] !== e[i]) return !1;
            return !0;
        };
    }, k.times = function(t, e, i) {
        for (var n = Array(Math.max(0, t)), r = 0; t > r; r++) n[r] = e.call(i, r);
        return n;
    }, k.random = function(t, e) {
        return null == e && (e = t, t = 0), t + Math.floor(Math.random() * (e - t + 1));
    }, k.now = Date.now || function() {
        return new Date().getTime();
    };
    var j = {
        escape: {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#x27;"
        }
    };
    j.unescape = k.invert(j.escape);
    var E = {
        escape: new RegExp("[" + k.keys(j.escape).join("") + "]", "g"),
        unescape: new RegExp("(" + k.keys(j.unescape).join("|") + ")", "g")
    };
    k.each([ "escape", "unescape" ], function(t) {
        k[t] = function(e) {
            return null == e ? "" : ("" + e).replace(E[t], function(e) {
                return j[t][e];
            });
        };
    }), k.result = function(t, e) {
        if (null == t) return void 0;
        var i = t[e];
        return k.isFunction(i) ? i.call(t) : i;
    }, k.mixin = function(t) {
        w(k.functions(t), function(e) {
            var i = k[e] = t[e];
            k.prototype[e] = function() {
                var t = [ this._wrapped ];
                return o.apply(t, arguments), L.call(this, i.apply(k, t));
            };
        });
    };
    var z = 0;
    k.uniqueId = function(t) {
        var e = ++z + "";
        return t ? t + e : e;
    }, k.templateSettings = {
        evaluate: /<%([\s\S]+?)%>/g,
        interpolate: /<%=([\s\S]+?)%>/g,
        escape: /<%-([\s\S]+?)%>/g
    };
    var P = /(.)^/, B = {
        "'": "'",
        "\\": "\\",
        "\r": "r",
        "\n": "n",
        "   ": "t",
        "\u2028": "u2028",
        "\u2029": "u2029"
    }, V = /\\|'|\r|\n|\t|\u2028|\u2029/g;
    k.template = function(t, e, i) {
        var n;
        i = k.defaults({}, i, k.templateSettings);
        var r = new RegExp([ (i.escape || P).source, (i.interpolate || P).source, (i.evaluate || P).source ].join("|") + "|$", "g"), s = 0, o = "__p+='";
        t.replace(r, function(e, i, n, r, l) {
            return o += t.slice(s, l).replace(V, function(t) {
                return "\\" + B[t];
            }), i && (o += "'+\n((__t=(" + i + "))==null?'':_.escape(__t))+\n'"), n && (o += "'+\n((__t=(" + n + "))==null?'':__t)+\n'"), 
            r && (o += "';\n" + r + "\n__p+='"), s = l + e.length, e;
        }), o += "';\n", i.variable || (o = "with(obj||{}){\n" + o + "}\n"), o = "var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};\n" + o + "return __p;\n";
        try {
            n = new Function(i.variable || "obj", "_", o);
        } catch (l) {
            throw l.source = o, l;
        }
        if (e) return n(e, k);
        var a = function(t) {
            return n.call(this, t, k);
        };
        return a.source = "function(" + (i.variable || "obj") + "){\n" + o + "}", a;
    }, k.chain = function(t) {
        return k(t).chain();
    };
    var L = function(t) {
        return this._chain ? k(t).chain() : t;
    };
    k.mixin(k), w([ "pop", "push", "reverse", "shift", "sort", "splice", "unshift" ], function(t) {
        var e = n[t];
        k.prototype[t] = function() {
            var i = this._wrapped;
            return e.apply(i, arguments), "shift" != t && "splice" != t || 0 !== i.length || delete i[0], 
            L.call(this, i);
        };
    }), w([ "concat", "join", "slice" ], function(t) {
        var e = n[t];
        k.prototype[t] = function() {
            return L.call(this, e.apply(this._wrapped, arguments));
        };
    }), k.extend(k.prototype, {
        chain: function() {
            return this._chain = !0, this;
        },
        value: function() {
            return this._wrapped;
        }
    }), "function" == typeof define && define.amd && define("underscore", [], function() {
        return k;
    });
}.call(this);

var asl_underscore = _.noConflict();

MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_ = "../images/m", MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_ = "png", 
MarkerClusterer.prototype.extend = function(t, e) {
    return function(t) {
        for (var e in t.prototype) this.prototype[e] = t.prototype[e];
        return this;
    }.apply(t, [ e ]);
}, MarkerClusterer.prototype.onAdd = function() {
    this.setReady_(!0);
}, MarkerClusterer.prototype.draw = function() {}, MarkerClusterer.prototype.setupStyles_ = function() {
    if (!this.styles_.length) for (var t, e = 0; t = this.sizes[e]; e++) this.styles_.push({
        url: this.imagePath_ + (e + 1) + "." + this.imageExtension_,
        height: t,
        width: t
    });
}, MarkerClusterer.prototype.fitMapToMarkers = function() {
    for (var t, e = this.getMarkers(), i = new google.maps.LatLngBounds(), n = 0; t = e[n]; n++) i.extend(t.getPosition());
    this.map_.fitBounds(i);
}, MarkerClusterer.prototype.setStyles = function(t) {
    this.styles_ = t;
}, MarkerClusterer.prototype.getStyles = function() {
    return this.styles_;
}, MarkerClusterer.prototype.isZoomOnClick = function() {
    return this.zoomOnClick_;
}, MarkerClusterer.prototype.isAverageCenter = function() {
    return this.averageCenter_;
}, MarkerClusterer.prototype.getMarkers = function() {
    return this.markers_;
}, MarkerClusterer.prototype.getTotalMarkers = function() {
    return this.markers_.length;
}, MarkerClusterer.prototype.setMaxZoom = function(t) {
    this.maxZoom_ = t;
}, MarkerClusterer.prototype.getMaxZoom = function() {
    return this.maxZoom_;
}, MarkerClusterer.prototype.calculator_ = function(t, e) {
    for (var i = 0, n = t.length, r = n; 0 !== r; ) r = parseInt(r / 10, 10), i++;
    return i = Math.min(i, e), {
        text: n,
        index: i
    };
}, MarkerClusterer.prototype.setCalculator = function(t) {
    this.calculator_ = t;
}, MarkerClusterer.prototype.getCalculator = function() {
    return this.calculator_;
}, MarkerClusterer.prototype.addMarkers = function(t, e) {
    for (var i, n = 0; i = t[n]; n++) this.pushMarkerTo_(i);
    e || this.redraw();
}, MarkerClusterer.prototype.pushMarkerTo_ = function(t) {
    if (t.isAdded = !1, t.draggable) {
        var e = this;
        google.maps.event.addListener(t, "dragend", function() {
            t.isAdded = !1, e.repaint();
        });
    }
    this.markers_.push(t);
}, MarkerClusterer.prototype.addMarker = function(t, e) {
    this.pushMarkerTo_(t), e || this.redraw();
}, MarkerClusterer.prototype.removeMarker_ = function(t) {
    var e = -1;
    if (this.markers_.indexOf) e = this.markers_.indexOf(t); else for (var i, n = 0; i = this.markers_[n]; n++) if (i == t) {
        e = n;
        break;
    }
    return -1 == e ? !1 : (t.setMap(null), this.markers_.splice(e, 1), !0);
}, MarkerClusterer.prototype.removeMarker = function(t, e) {
    var i = this.removeMarker_(t);
    return !e && i ? (this.resetViewport(), this.redraw(), !0) : !1;
}, MarkerClusterer.prototype.removeMarkers = function(t, e) {
    for (var i, n = !1, r = 0; i = t[r]; r++) {
        var s = this.removeMarker_(i);
        n = n || s;
    }
    return !e && n ? (this.resetViewport(), this.redraw(), !0) : void 0;
}, MarkerClusterer.prototype.setReady_ = function(t) {
    this.ready_ || (this.ready_ = t, this.createClusters_());
}, MarkerClusterer.prototype.getTotalClusters = function() {
    return this.clusters_.length;
}, MarkerClusterer.prototype.getMap = function() {
    return this.map_;
}, MarkerClusterer.prototype.setMap = function(t) {
    this.map_ = t;
}, MarkerClusterer.prototype.getGridSize = function() {
    return this.gridSize_;
}, MarkerClusterer.prototype.setGridSize = function(t) {
    this.gridSize_ = t;
}, MarkerClusterer.prototype.getMinClusterSize = function() {
    return this.minClusterSize_;
}, MarkerClusterer.prototype.setMinClusterSize = function(t) {
    this.minClusterSize_ = t;
}, MarkerClusterer.prototype.getExtendedBounds = function(t) {
    var e = this.getProjection(), i = new google.maps.LatLng(t.getNorthEast().lat(), t.getNorthEast().lng()), n = new google.maps.LatLng(t.getSouthWest().lat(), t.getSouthWest().lng()), r = e.fromLatLngToDivPixel(i);
    r.x += this.gridSize_, r.y -= this.gridSize_;
    var s = e.fromLatLngToDivPixel(n);
    s.x -= this.gridSize_, s.y += this.gridSize_;
    var o = e.fromDivPixelToLatLng(r), l = e.fromDivPixelToLatLng(s);
    return t.extend(o), t.extend(l), t;
}, MarkerClusterer.prototype.isMarkerInBounds_ = function(t, e) {
    return e.contains(t.getPosition());
}, MarkerClusterer.prototype.clearMarkers = function() {
    this.resetViewport(!0), this.markers_ = [];
}, MarkerClusterer.prototype.resetViewport = function(t) {
    for (var e, i = 0; e = this.clusters_[i]; i++) e.remove();
    for (var n, i = 0; n = this.markers_[i]; i++) n.isAdded = !1, t && n.setMap(null);
    this.clusters_ = [];
}, MarkerClusterer.prototype.repaint = function() {
    var t = this.clusters_.slice();
    this.clusters_.length = 0, this.resetViewport(), this.redraw(), window.setTimeout(function() {
        for (var e, i = 0; e = t[i]; i++) e.remove();
    }, 0);
}, MarkerClusterer.prototype.redraw = function() {
    this.createClusters_();
}, MarkerClusterer.prototype.distanceBetweenPoints_ = function(t, e) {
    if (!t || !e) return 0;
    var i = 6371, n = (e.lat() - t.lat()) * Math.PI / 180, r = (e.lng() - t.lng()) * Math.PI / 180, s = Math.sin(n / 2) * Math.sin(n / 2) + Math.cos(t.lat() * Math.PI / 180) * Math.cos(e.lat() * Math.PI / 180) * Math.sin(r / 2) * Math.sin(r / 2), o = 2 * Math.atan2(Math.sqrt(s), Math.sqrt(1 - s)), l = i * o;
    return l;
}, MarkerClusterer.prototype.addToClosestCluster_ = function(t) {
    for (var e, i = 4e4, n = null, r = (t.getPosition(), 0); e = this.clusters_[r]; r++) {
        var s = e.getCenter();
        if (s) {
            var o = this.distanceBetweenPoints_(s, t.getPosition());
            i > o && (i = o, n = e);
        }
    }
    if (n && n.isMarkerInClusterBounds(t)) n.addMarker(t); else {
        var e = new Cluster(this);
        e.addMarker(t), this.clusters_.push(e);
    }
}, MarkerClusterer.prototype.createClusters_ = function() {
    if (this.ready_) for (var t, e = new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(), this.map_.getBounds().getNorthEast()), i = this.getExtendedBounds(e), n = 0; t = this.markers_[n]; n++) !t.isAdded && this.isMarkerInBounds_(t, i) && this.addToClosestCluster_(t);
}, Cluster.prototype.isMarkerAlreadyAdded = function(t) {
    if (this.markers_.indexOf) return -1 != this.markers_.indexOf(t);
    for (var e, i = 0; e = this.markers_[i]; i++) if (e == t) return !0;
    return !1;
}, Cluster.prototype.addMarker = function(t) {
    if (this.isMarkerAlreadyAdded(t)) return !1;
    if (this.center_) {
        if (this.averageCenter_) {
            var e = this.markers_.length + 1, i = (this.center_.lat() * (e - 1) + t.getPosition().lat()) / e, n = (this.center_.lng() * (e - 1) + t.getPosition().lng()) / e;
            this.center_ = new google.maps.LatLng(i, n), this.calculateBounds_();
        }
    } else this.center_ = t.getPosition(), this.calculateBounds_();
    t.isAdded = !0, this.markers_.push(t);
    var r = this.markers_.length;
    if (r < this.minClusterSize_ && t.getMap() != this.map_ && t.setMap(this.map_), 
    r == this.minClusterSize_) for (var s = 0; r > s; s++) this.markers_[s].setMap(null);
    return r >= this.minClusterSize_ && t.setMap(null), this.updateIcon(), !0;
}, Cluster.prototype.getMarkerClusterer = function() {
    return this.markerClusterer_;
}, Cluster.prototype.getBounds = function() {
    for (var t, e = new google.maps.LatLngBounds(this.center_, this.center_), i = this.getMarkers(), n = 0; t = i[n]; n++) e.extend(t.getPosition());
    return e;
}, Cluster.prototype.remove = function() {
    this.clusterIcon_.remove(), this.markers_.length = 0, delete this.markers_;
}, Cluster.prototype.getSize = function() {
    return this.markers_.length;
}, Cluster.prototype.getMarkers = function() {
    return this.markers_;
}, Cluster.prototype.getCenter = function() {
    return this.center_;
}, Cluster.prototype.calculateBounds_ = function() {
    var t = new google.maps.LatLngBounds(this.center_, this.center_);
    this.bounds_ = this.markerClusterer_.getExtendedBounds(t);
}, Cluster.prototype.isMarkerInClusterBounds = function(t) {
    return this.bounds_.contains(t.getPosition());
}, Cluster.prototype.getMap = function() {
    return this.map_;
}, Cluster.prototype.updateIcon = function() {
    var t = this.map_.getZoom(), e = this.markerClusterer_.getMaxZoom();
    if (e && t > e) for (var i, n = 0; i = this.markers_[n]; n++) i.setMap(this.map_); else {
        if (this.markers_.length < this.minClusterSize_) return void this.clusterIcon_.hide();
        var r = this.markerClusterer_.getStyles().length, s = this.markerClusterer_.getCalculator()(this.markers_, r);
        this.clusterIcon_.setCenter(this.center_), this.clusterIcon_.setSums(s), this.clusterIcon_.show();
    }
}, ClusterIcon.prototype.triggerClusterClick = function(t) {
    var e = this.cluster_.getMarkerClusterer();
    google.maps.event.trigger(e, "clusterclick", this.cluster_, t), e.isZoomOnClick() && this.map_.fitBounds(this.cluster_.getBounds());
}, ClusterIcon.prototype.onAdd = function() {
    if (this.div_ = document.createElement("DIV"), this.visible_) {
        var t = this.getPosFromLatLng_(this.center_);
        this.div_.style.cssText = this.createCss(t), this.div_.innerHTML = this.sums_.text;
    }
    var e = this.getPanes();
    e.overlayMouseTarget.appendChild(this.div_);
    var i = this;
    google.maps.event.addDomListener(this.div_, "click", function(t) {
        i.triggerClusterClick(t);
    });
}, ClusterIcon.prototype.getPosFromLatLng_ = function(t) {
    var e = this.getProjection().fromLatLngToDivPixel(t);
    return "object" == typeof this.iconAnchor_ && 2 === this.iconAnchor_.length ? (e.x -= this.iconAnchor_[0], 
    e.y -= this.iconAnchor_[1]) : (e.x -= parseInt(this.width_ / 2, 10), e.y -= parseInt(this.height_ / 2, 10)), 
    e;
}, ClusterIcon.prototype.draw = function() {
    if (this.visible_) {
        var t = this.getPosFromLatLng_(this.center_);
        this.div_.style.top = t.y + "px", this.div_.style.left = t.x + "px";
    }
}, ClusterIcon.prototype.hide = function() {
    this.div_ && (this.div_.style.display = "none"), this.visible_ = !1;
}, ClusterIcon.prototype.show = function() {
    if (this.div_) {
        var t = this.getPosFromLatLng_(this.center_);
        this.div_.style.cssText = this.createCss(t), this.div_.style.display = "";
    }
    this.visible_ = !0;
}, ClusterIcon.prototype.remove = function() {
    this.setMap(null);
}, ClusterIcon.prototype.onRemove = function() {
    this.div_ && this.div_.parentNode && (this.hide(), this.div_.parentNode.removeChild(this.div_), 
    this.div_ = null);
}, ClusterIcon.prototype.setSums = function(t) {
    this.sums_ = t, this.text_ = t.text, this.index_ = t.index, this.div_ && (this.div_.innerHTML = t.text), 
    this.useStyle();
}, ClusterIcon.prototype.useStyle = function() {
    var t = Math.max(0, this.sums_.index - 1);
    t = Math.min(this.styles_.length - 1, t);
    var e = this.styles_[t];
    this.url_ = e.url, this.height_ = e.height, this.width_ = e.width, this.textColor_ = e.textColor, 
    this.anchor_ = e.anchor, this.textSize_ = e.textSize, this.backgroundPosition_ = e.backgroundPosition, 
    this.iconAnchor_ = e.iconAnchor;
}, ClusterIcon.prototype.setCenter = function(t) {
    this.center_ = t;
}, ClusterIcon.prototype.createCss = function(t) {
    var e = [];
    e.push("background-image:url(" + this.url_ + ");");
    var i = this.backgroundPosition_ ? this.backgroundPosition_ : "0 0";
    e.push("background-position:" + i + ";"), "object" == typeof this.anchor_ ? (e.push("number" == typeof this.anchor_[0] && this.anchor_[0] > 0 && this.anchor_[0] < this.height_ ? "height:" + (this.height_ - this.anchor_[0]) + "px; padding-top:" + this.anchor_[0] + "px;" : "number" == typeof this.anchor_[0] && this.anchor_[0] < 0 && -this.anchor_[0] < this.height_ ? "height:" + this.height_ + "px; line-height:" + (this.height_ + this.anchor_[0]) + "px;" : "height:" + this.height_ + "px; line-height:" + this.height_ + "px;"), 
    e.push("number" == typeof this.anchor_[1] && this.anchor_[1] > 0 && this.anchor_[1] < this.width_ ? "width:" + (this.width_ - this.anchor_[1]) + "px; padding-left:" + this.anchor_[1] + "px;" : "width:" + this.width_ + "px; text-align:center;")) : e.push("height:" + this.height_ + "px; line-height:" + this.height_ + "px; width:" + this.width_ + "px; text-align:center;");
    var n = this.textColor_ ? this.textColor_ : "black", r = this.textSize_ ? this.textSize_ : 11;
    return e.push("cursor:pointer; top:" + t.y + "px; left:" + t.x + "px; color:" + n + "; position:absolute; font-size:" + r + "px; font-family:Arial,sans-serif; font-weight:bold"), 
    e.join("");
}, window.MarkerClusterer = MarkerClusterer, MarkerClusterer.prototype.addMarker = MarkerClusterer.prototype.addMarker, 
MarkerClusterer.prototype.addMarkers = MarkerClusterer.prototype.addMarkers, MarkerClusterer.prototype.clearMarkers = MarkerClusterer.prototype.clearMarkers, 
MarkerClusterer.prototype.fitMapToMarkers = MarkerClusterer.prototype.fitMapToMarkers, 
MarkerClusterer.prototype.getCalculator = MarkerClusterer.prototype.getCalculator, 
MarkerClusterer.prototype.getGridSize = MarkerClusterer.prototype.getGridSize, MarkerClusterer.prototype.getExtendedBounds = MarkerClusterer.prototype.getExtendedBounds, 
MarkerClusterer.prototype.getMap = MarkerClusterer.prototype.getMap, MarkerClusterer.prototype.getMarkers = MarkerClusterer.prototype.getMarkers, 
MarkerClusterer.prototype.getMaxZoom = MarkerClusterer.prototype.getMaxZoom, MarkerClusterer.prototype.getStyles = MarkerClusterer.prototype.getStyles, 
MarkerClusterer.prototype.getTotalClusters = MarkerClusterer.prototype.getTotalClusters, 
MarkerClusterer.prototype.getTotalMarkers = MarkerClusterer.prototype.getTotalMarkers, 
MarkerClusterer.prototype.redraw = MarkerClusterer.prototype.redraw, MarkerClusterer.prototype.removeMarker = MarkerClusterer.prototype.removeMarker, 
MarkerClusterer.prototype.removeMarkers = MarkerClusterer.prototype.removeMarkers, 
MarkerClusterer.prototype.resetViewport = MarkerClusterer.prototype.resetViewport, 
MarkerClusterer.prototype.repaint = MarkerClusterer.prototype.repaint, MarkerClusterer.prototype.setCalculator = MarkerClusterer.prototype.setCalculator, 
MarkerClusterer.prototype.setGridSize = MarkerClusterer.prototype.setGridSize, MarkerClusterer.prototype.setMaxZoom = MarkerClusterer.prototype.setMaxZoom, 
MarkerClusterer.prototype.onAdd = MarkerClusterer.prototype.onAdd, MarkerClusterer.prototype.draw = MarkerClusterer.prototype.draw, 
Cluster.prototype.getCenter = Cluster.prototype.getCenter, Cluster.prototype.getSize = Cluster.prototype.getSize, 
Cluster.prototype.getMarkers = Cluster.prototype.getMarkers, ClusterIcon.prototype.onAdd = ClusterIcon.prototype.onAdd, 
ClusterIcon.prototype.draw = ClusterIcon.prototype.draw, ClusterIcon.prototype.onRemove = ClusterIcon.prototype.onRemove, 

!function(t) {
    "use strict";
    function e(t, e) {
        for (var i = 0; i < t.length; ++i) e(t[i], i);
    }
    function i(e, i) {
        this.$select = t(e), this.$select.attr("data-placeholder") && (i.nonSelectedText = this.$select.data("placeholder")), 
        this.options = this.mergeOptions(t.extend({}, i, this.$select.data())), this.originalOptions = this.$select.clone()[0].options, 
        this.query = "", this.searchTimeout = null, this.lastToggledInput = null, this.options.multiple = "multiple" === this.$select.attr("multiple"), 
        this.options.onChange = t.proxy(this.options.onChange, this), this.options.onDropdownShow = t.proxy(this.options.onDropdownShow, this), 
        this.options.onDropdownHide = t.proxy(this.options.onDropdownHide, this), this.options.onDropdownShown = t.proxy(this.options.onDropdownShown, this), 
        this.options.onDropdownHidden = t.proxy(this.options.onDropdownHidden, this), this.options.onInitialized = t.proxy(this.options.onInitialized, this), 
        this.buildContainer(), this.buildButton(), this.buildDropdown(), this.buildSelectAll(), 
        this.buildDropdownOptions(), this.buildFilter(), this.updateButtonText(), this.updateSelectAll(!0), 
        this.options.disableIfEmpty && t("option", this.$select).length <= 0 && this.disable(), 
        this.$select.hide().after(this.$container), this.options.onInitialized(this.$select, this.$container);
    }
    "undefined" != typeof ko && ko.bindingHandlers && !ko.bindingHandlers.multiselect && (ko.bindingHandlers.multiselect = {
        after: [ "options", "value", "selectedOptions", "enable", "disable" ],
        init: function(e, i, n) {
            var r = t(e), s = ko.toJS(i());
            if (r.multiselect(s), n.has("options")) {
                var o = n.get("options");
                ko.isObservable(o) && ko.computed({
                    read: function() {
                        o(), setTimeout(function() {
                            var t = r.data("multiselect");
                            t && t.updateOriginalOptions(), r.multiselect("rebuild");
                        }, 1);
                    },
                    disposeWhenNodeIsRemoved: e
                });
            }
            if (n.has("value")) {
                var l = n.get("value");
                ko.isObservable(l) && ko.computed({
                    read: function() {
                        l(), setTimeout(function() {
                            r.multiselect("refresh");
                        }, 1);
                    },
                    disposeWhenNodeIsRemoved: e
                }).extend({
                    rateLimit: 100,
                    notifyWhenChangesStop: !0
                });
            }
            if (n.has("selectedOptions")) {
                var a = n.get("selectedOptions");
                ko.isObservable(a) && ko.computed({
                    read: function() {
                        a(), setTimeout(function() {
                            r.multiselect("refresh");
                        }, 1);
                    },
                    disposeWhenNodeIsRemoved: e
                }).extend({
                    rateLimit: 100,
                    notifyWhenChangesStop: !0
                });
            }
            var u = function(t) {
                setTimeout(function() {
                    r.multiselect(t ? "enable" : "disable");
                });
            };
            if (n.has("enable")) {
                var h = n.get("enable");
                ko.isObservable(h) ? ko.computed({
                    read: function() {
                        u(h());
                    },
                    disposeWhenNodeIsRemoved: e
                }).extend({
                    rateLimit: 100,
                    notifyWhenChangesStop: !0
                }) : u(h);
            }
            if (n.has("disable")) {
                var p = n.get("disable");
                ko.isObservable(p) ? ko.computed({
                    read: function() {
                        u(!p());
                    },
                    disposeWhenNodeIsRemoved: e
                }).extend({
                    rateLimit: 100,
                    notifyWhenChangesStop: !0
                }) : u(!p);
            }
            ko.utils.domNodeDisposal.addDisposeCallback(e, function() {
                r.multiselect("destroy");
            });
        },
        update: function(e, i) {
            var n = t(e), r = ko.toJS(i());
            n.multiselect("setOptions", r), n.multiselect("rebuild");
        }
    }), i.prototype = {
        defaults: {
            buttonText: function(e, i) {
                if (this.disabledText.length > 0 && (this.disableIfEmpty || i.prop("disabled")) && 0 == e.length) return this.disabledText;
                if (0 === e.length) return this.nonSelectedText;
                if (this.allSelectedText && e.length === t("option", t(i)).length && 1 !== t("option", t(i)).length && this.multiple) return this.selectAllNumber ? this.allSelectedText + " (" + e.length + ")" : this.allSelectedText;
                if (e.length > this.numberDisplayed) return e.length + " " + this.nSelectedText;
                var n = "", r = this.delimiterText;
                return e.each(function() {
                    var e = void 0 !== t(this).attr("label") ? t(this).attr("label") : t(this).text();
                    n += e + r;
                }), n.substr(0, n.length - 2);
            },
            buttonTitle: function(e) {
                if (0 === e.length) return this.nonSelectedText;
                var i = "", n = this.delimiterText;
                return e.each(function() {
                    var e = void 0 !== t(this).attr("label") ? t(this).attr("label") : t(this).text();
                    i += e + n;
                }), i.substr(0, i.length - 2);
            },
            optionLabel: function(e) {
                return t(e).attr("label") || t(e).text();
            },
            optionClass: function(e) {
                return t(e).attr("class") || "";
            },
            onChange: function() {},
            onDropdownShow: function() {},
            onDropdownHide: function() {},
            onDropdownShown: function() {},
            onDropdownHidden: function() {},
            onSelectAll: function() {},
            onInitialized: function() {},
            enableHTML: !1,
            buttonClass: "btn btn-default",
            inheritClass: !1,
            buttonWidth: "auto",
            buttonContainer: '<div class="btn-group" />',
            dropRight: !1,
            dropUp: !1,
            selectedClass: "active",
            maxHeight: !1,
            checkboxName: !1,
            includeSelectAllOption: !1,
            includeSelectAllIfMoreThan: 0,
            selectAllText: " Select all",
            selectAllValue: "multiselect-all",
            selectAllName: !1,
            selectAllNumber: !0,
            selectAllJustVisible: !0,
            enableFiltering: !1,
            enableCaseInsensitiveFiltering: !1,
            enableFullValueFiltering: !1,
            enableClickableOptGroups: !1,
            enableCollapsibelOptGroups: !1,
            filterPlaceholder: "Search",
            filterBehavior: "text",
            includeFilterClearBtn: !0,
            preventInputChangeEvent: !1,
            nonSelectedText: "None selected",
            nSelectedText: "selected",
            allSelectedText: "All selected",
            numberDisplayed: 3,
            disableIfEmpty: !1,
            disabledText: "",
            delimiterText: ", ",
            templates: {
                button: '<button type="button" class="multiselect adropdown-toggle style-btn" data-toggle="adropdown"><span class="multiselect-selected-text"></span> <b class="caret"></b></button>',
                ul: '<ul class="multiselect-container adropdown-menu"></ul>',
                filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="glyphicon glyphicon-remove-circle"></i></button></span>',
                li: '<li><a tabindex="0"><label></label></a></li>',
                divider: '<li class="multiselect-item divider"></li>',
                liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
            }
        },
        constructor: i,
        buildContainer: function() {
            this.$container = t(this.options.buttonContainer), this.$container.on("show.bs.adropdown", this.options.onDropdownShow), 
            this.$container.on("hide.bs.adropdown", this.options.onDropdownHide), this.$container.on("shown.bs.adropdown", this.options.onDropdownShown), 
            this.$container.on("hidden.bs.adropdown", this.options.onDropdownHidden);
        },
        buildButton: function() {
            this.$button = t(this.options.templates.button).addClass(this.options.buttonClass), 
            this.$select.attr("class") && this.options.inheritClass && this.$button.addClass(this.$select.attr("class")), 
            this.$select.prop("disabled") ? this.disable() : this.enable(), this.options.buttonWidth && "auto" !== this.options.buttonWidth && (this.$button.css({
                width: this.options.buttonWidth,
                overflow: "hidden",
                "text-overflow": "ellipsis"
            }), this.$container.css({
                width: this.options.buttonWidth
            }));
            var e = this.$select.attr("tabindex");
            e && this.$button.attr("tabindex", e), this.$container.prepend(this.$button);
        },
        buildDropdown: function() {
            if (this.$ul = t(this.options.templates.ul), this.options.dropRight && this.$ul.addClass("pull-right"), 
            this.options.maxHeight && this.$ul.css({
                "max-height": this.options.maxHeight + "px",
                "overflow-y": "auto",
                "overflow-x": "hidden"
            }), this.options.dropUp) {
                var e = Math.min(this.options.maxHeight, 26 * t('option[data-role!="divider"]', this.$select).length + 19 * t('option[data-role="divider"]', this.$select).length + (this.options.includeSelectAllOption ? 26 : 0) + (this.options.enableFiltering || this.options.enableCaseInsensitiveFiltering ? 44 : 0)), i = e + 34;
                this.$ul.css({
                    "max-height": e + "px",
                    "overflow-y": "auto",
                    "overflow-x": "hidden",
                    "margin-top": "-" + i + "px"
                });
            }
            this.$container.append(this.$ul);
        },
        buildDropdownOptions: function() {
            this.$select.children().each(t.proxy(function(e, i) {
                var n = t(i), r = n.prop("tagName").toLowerCase();
                n.prop("value") !== this.options.selectAllValue && ("optgroup" === r ? this.createOptgroup(i) : "option" === r && ("divider" === n.data("role") ? this.createDivider() : this.createOptionValue(i)));
            }, this)), t("li input", this.$ul).on("change", t.proxy(function(e) {
                var i = t(e.target), n = i.prop("checked") || !1, r = i.val() === this.options.selectAllValue;
                this.options.selectedClass && (n ? i.closest("li").addClass(this.options.selectedClass) : i.closest("li").removeClass(this.options.selectedClass));
                var s = i.val(), o = this.getOptionByValue(s), l = t("option", this.$select).not(o), a = t("input", this.$container).not(i);
                return r ? n ? this.selectAll(this.options.selectAllJustVisible) : this.deselectAll(this.options.selectAllJustVisible) : (n ? (o.prop("selected", !0), 
                this.options.multiple ? o.prop("selected", !0) : (this.options.selectedClass && t(a).closest("li").removeClass(this.options.selectedClass), 
                t(a).prop("checked", !1), l.prop("selected", !1), this.$button.click()), "active" === this.options.selectedClass && l.closest("a").css("outline", "")) : o.prop("selected", !1), 
                this.options.onChange(o, n)), this.$select.change(), this.updateButtonText(), this.updateSelectAll(), 
                this.options.preventInputChangeEvent ? !1 : void 0;
            }, this)), t("li a", this.$ul).on("mousedown", function(t) {
                return t.shiftKey ? !1 : void 0;
            }), t("li a", this.$ul).on("touchstart click", t.proxy(function(e) {
                e.stopPropagation();
                var i = t(e.target);
                if (e.shiftKey && this.options.multiple) {
                    i.is("label") && (e.preventDefault(), i = i.find("input"), i.prop("checked", !i.prop("checked")));
                    var n = i.prop("checked") || !1;
                    if (null !== this.lastToggledInput && this.lastToggledInput !== i) {
                        var r = i.closest("li").index(), s = this.lastToggledInput.closest("li").index();
                        if (r > s) {
                            var o = s;
                            s = r, r = o;
                        }
                        ++s;
                        var l = this.$ul.find("li").slice(r, s).find("input");
                        l.prop("checked", n), this.options.selectedClass && l.closest("li").toggleClass(this.options.selectedClass, n);
                        for (var a = 0, u = l.length; u > a; a++) {
                            var h = t(l[a]), p = this.getOptionByValue(h.val());
                            p.prop("selected", n);
                        }
                    }
                    i.trigger("change");
                }
                i.is("input") && !i.closest("li").is(".multiselect-item") && (this.lastToggledInput = i), 
                i.blur();
            }, this)), this.$container.off("keydown.multiselect").on("keydown.multiselect", t.proxy(function(e) {

                if (!t('input[type="text"]', this.$container).is(":focus")) if (9 === e.keyCode && this.$container.hasClass("open")) this.$button.click(); else {
                    var i = t(this.$container).find("li:not(.divider):not(.disabled) a").filter(":visible");
                    if (!i.length) return;
                    var n = i.index(i.filter(":focus"));
                    38 === e.keyCode && n > 0 ? n-- : 40 === e.keyCode && n < i.length - 1 ? n++ : ~n || (n = 0);
                    var r = i.eq(n);
                    if (r.focus(), 32 === e.keyCode || 13 === e.keyCode) {
                        var s = r.find("input");
                        s.prop("checked", !s.prop("checked")), s.change();
                    }
                    e.stopPropagation(), e.preventDefault();
                }
            }, this)), this.options.enableClickableOptGroups && this.options.multiple && t("li.multiselect-group", this.$ul).on("click", t.proxy(function(e) {
                e.stopPropagation();
                var i = t(e.target).parent(), n = i.nextUntil("li.multiselect-group"), r = n.filter(":visible:not(.disabled)"), s = !0, o = r.find("input"), l = [];
                o.each(function() {
                    s = s && t(this).prop("checked"), l.push(t(this).val());
                }), s ? this.deselect(l, !1) : this.select(l, !1), this.options.onChange(o, !s);
            }, this)), this.options.enableCollapsibleOptGroups && this.options.multiple && (t("li.multiselect-group input", this.$ul).off(), 
            t("li.multiselect-group", this.$ul).siblings().not("li.multiselect-group, li.multiselect-all", this.$ul).each(function() {
                t(this).toggleClass("hidden", !0);
            }), t("li.multiselect-group", this.$ul).on("click", t.proxy(function(t) {
                t.stopPropagation();
            }, this)), t("li.multiselect-group > a > b", this.$ul).on("click", t.proxy(function(e) {
                e.stopPropagation();
                var i = t(e.target).closest("li"), n = i.nextUntil("li.multiselect-group"), r = !0;
                n.each(function() {
                    r = r && t(this).hasClass("hidden");
                }), n.toggleClass("hidden", !r);
            }, this)), console.log(t("li.multiselect-group > a > input", this.$ul)), t("li.multiselect-group > a > input", this.$ul).on("change", t.proxy(function(e) {
                e.stopPropagation();
                var i = t(e.target).closest("li"), n = i.nextUntil("li.multiselect-group", ":not(.disabled)"), r = n.find("input"), s = !0;
                r.each(function() {
                    s = s && t(this).prop("checked");
                }), r.prop("checked", !s).trigger("change");
            }, this)), t("li.multiselect-group", this.$ul).each(function() {
                var e = t(this).nextUntil("li.multiselect-group", ":not(.disabled)"), i = e.find("input"), n = !0;
                i.each(function() {
                    n = n && t(this).prop("checked");
                }), t(this).find("input").prop("checked", n);
            }), t("li input", this.$ul).on("change", t.proxy(function(e) {
                e.stopPropagation();
                var i = t(e.target).closest("li"), n = i.prevUntil("li.multiselect-group", ":not(.disabled)"), r = i.nextUntil("li.multiselect-group", ":not(.disabled)"), s = n.find("input"), o = r.find("input"), l = t(e.target).prop("checked");
                s.each(function() {
                    l = l && t(this).prop("checked");
                }), o.each(function() {
                    l = l && t(this).prop("checked");
                }), i.prevAll(".multiselect-group").find("input").prop("checked", l);
            }, this)), t("li.multiselect-all", this.$ul).css("background", "#f3f3f3").css("border-bottom", "1px solid #eaeaea"), 
            t("li.multiselect-group > a, li.multiselect-all > a > label.checkbox", this.$ul).css("padding", "3px 20px 3px 35px"), 
            t("li.multiselect-group > a > input", this.$ul).css("margin", "4px 0px 5px -20px"));
        },
        createOptionValue: function(e) {
            var i = t(e);
            i.is(":selected") && i.prop("selected", !0);
            var n = this.options.optionLabel(e), r = this.options.optionClass(e), s = i.val(), o = this.options.multiple ? "checkbox" : "radio", l = t(this.options.templates.li), a = t("label", l);
            a.addClass(o), l.addClass(r), this.options.enableHTML ? a.html(" " + n) : a.text(" " + n);
            var u = t("<input/>").attr("type", o);
            this.options.checkboxName && u.attr("name", this.options.checkboxName), a.prepend(u);
            var h = i.prop("selected") || !1;
            u.val(s), s === this.options.selectAllValue && (l.addClass("multiselect-item multiselect-all"), 
            u.parent().parent().addClass("multiselect-all")), a.attr("title", i.attr("title")), 
            this.$ul.append(l), i.is(":disabled") && u.attr("disabled", "disabled").prop("disabled", !0).closest("a").attr("tabindex", "-1").closest("li").addClass("disabled"), 
            u.prop("checked", h), h && this.options.selectedClass && u.closest("li").addClass(this.options.selectedClass);
        },
        createDivider: function() {
            var e = t(this.options.templates.divider);
            this.$ul.append(e);
        },
        createOptgroup: function(e) {
            if (this.options.enableCollapsibleOptGroups && this.options.multiple) {
                var i = t(e).attr("label"), n = t(e).attr("value"), r = t('<li class="multiselect-item multiselect-group"><a href="javascript:void(0);"><input type="checkbox" value="' + n + '"/><b> ' + i + '<b class="caret"></b></b></a></li>');
                this.options.enableClickableOptGroups && r.addClass("multiselect-group-clickable"), 
                this.$ul.append(r), t(e).is(":disabled") && r.addClass("disabled"), t("option", e).each(t.proxy(function(t, e) {
                    this.createOptionValue(e);
                }, this));
            } else {
                var s = t(e).prop("label"), o = t(this.options.templates.liGroup);
                this.options.enableHTML ? t("label", o).html(s) : t("label", o).text(s), this.options.enableClickableOptGroups && o.addClass("multiselect-group-clickable"), 
                this.$ul.append(o), t(e).is(":disabled") && o.addClass("disabled"), t("option", e).each(t.proxy(function(t, e) {
                    this.createOptionValue(e);
                }, this));
            }
        },
        buildSelectAll: function() {
            "number" == typeof this.options.selectAllValue && (this.options.selectAllValue = this.options.selectAllValue.toString());
            var e = this.hasSelectAll();
            if (!e && this.options.includeSelectAllOption && this.options.multiple && t("option", this.$select).length > this.options.includeSelectAllIfMoreThan) {
                this.options.includeSelectAllDivider && this.$ul.prepend(t(this.options.templates.divider));
                var i = t(this.options.templates.li);
                t("label", i).addClass("checkbox"), this.options.enableHTML ? t("label", i).html(" " + this.options.selectAllText) : t("label", i).text(" " + this.options.selectAllText), 
                t("label", i).prepend(this.options.selectAllName ? '<input type="checkbox" name="' + this.options.selectAllName + '" />' : '<input type="checkbox" />');
                var n = t("input", i);
                n.val(this.options.selectAllValue), i.addClass("multiselect-item multiselect-all"), 
                n.parent().parent().addClass("multiselect-all"), this.$ul.prepend(i), n.prop("checked", !1);
            }
        },
        buildFilter: function() {
            if (this.options.enableFiltering || this.options.enableCaseInsensitiveFiltering) {
                var e = Math.max(this.options.enableFiltering, this.options.enableCaseInsensitiveFiltering);
                if (this.$select.find("option").length >= e) {
                    if (this.$filter = t(this.options.templates.filter), t("input", this.$filter).attr("placeholder", this.options.filterPlaceholder), 
                    this.options.includeFilterClearBtn) {
                        var i = t(this.options.templates.filterClearBtn);
                        i.on("click", t.proxy(function() {
                            clearTimeout(this.searchTimeout), this.$filter.find(".multiselect-search").val(""), 
                            t("li", this.$ul).show().removeClass("filter-hidden"), this.updateSelectAll();
                        }, this)), this.$filter.find(".input-group").append(i);
                    }
                    this.$ul.prepend(this.$filter), this.$filter.val(this.query).on("click", function(t) {
                        t.stopPropagation();
                    }).on("input keydown", t.proxy(function(e) {
                        13 === e.which && e.preventDefault(), clearTimeout(this.searchTimeout), this.searchTimeout = this.asyncFunction(t.proxy(function() {
                            if (this.query !== e.target.value) {
                                this.query = e.target.value;
                                var i, n;
                                t.each(t("li", this.$ul), t.proxy(function(e, r) {
                                    var s = t("input", r).length > 0 ? t("input", r).val() : "", o = t("label", r).text(), l = "";
                                    if ("text" === this.options.filterBehavior ? l = o : "value" === this.options.filterBehavior ? l = s : "both" === this.options.filterBehavior && (l = o + "\n" + s), 
                                    s !== this.options.selectAllValue && o) {
                                        var a = !1;
                                        if (this.options.enableCaseInsensitiveFiltering && (l = l.toLowerCase(), this.query = this.query.toLowerCase()), 
                                        this.options.enableFullValueFiltering && "both" !== this.options.filterBehavior) {
                                            var u = l.trim().substring(0, this.query.length);
                                            this.query.indexOf(u) > -1 && (a = !0);
                                        } else l.indexOf(this.query) > -1 && (a = !0);
                                        t(r).toggle(a).toggleClass("filter-hidden", !a), t(r).hasClass("multiselect-group") ? (i = r, 
                                        n = a) : (a && t(i).show().removeClass("filter-hidden"), !a && n && t(r).show().removeClass("filter-hidden"));
                                    }
                                }, this));
                            }
                            this.updateSelectAll();
                        }, this), 300, this);
                    }, this));
                }
            }
        },
        destroy: function() {
            this.$container.remove(), this.$select.show(), this.$select.data("multiselect", null);
        },
        refresh: function() {
            var e = t.map(t("li input", this.$ul), t);
            t("option", this.$select).each(t.proxy(function(i, n) {
                for (var r, s = t(n), o = s.val(), l = e.length; 0 < l--; ) if (o === (r = e[l]).val()) {
                    s.is(":selected") ? (r.prop("checked", !0), this.options.selectedClass && r.closest("li").addClass(this.options.selectedClass)) : (r.prop("checked", !1), 
                    this.options.selectedClass && r.closest("li").removeClass(this.options.selectedClass)), 
                    s.is(":disabled") ? r.attr("disabled", "disabled").prop("disabled", !0).closest("li").addClass("disabled") : r.prop("disabled", !1).closest("li").removeClass("disabled");
                    break;
                }
            }, this)), this.updateButtonText(), this.updateSelectAll();
        },
        select: function(e, i) {
            t.isArray(e) || (e = [ e ]);
            for (var n = 0; n < e.length; n++) {
                var r = e[n];
                if (null !== r && void 0 !== r) {
                    var s = this.getOptionByValue(r), o = this.getInputByValue(r);
                    void 0 !== s && void 0 !== o && (this.options.multiple || this.deselectAll(!1), 
                    this.options.selectedClass && o.closest("li").addClass(this.options.selectedClass), 
                    o.prop("checked", !0), s.prop("selected", !0), i && this.options.onChange(s, !0));
                }
            }
            this.updateButtonText(), this.updateSelectAll();
        },
        clearSelection: function() {
            this.deselectAll(!1), this.updateButtonText(), this.updateSelectAll();
        },
        deselect: function(e, i) {
            t.isArray(e) || (e = [ e ]);
            for (var n = 0; n < e.length; n++) {
                var r = e[n];
                if (null !== r && void 0 !== r) {
                    var s = this.getOptionByValue(r), o = this.getInputByValue(r);
                    void 0 !== s && void 0 !== o && (this.options.selectedClass && o.closest("li").removeClass(this.options.selectedClass), 
                    o.prop("checked", !1), s.prop("selected", !1), i && this.options.onChange(s, !1));
                }
            }
            this.updateButtonText(), this.updateSelectAll();
        },
        selectAll: function(e, i) {
            e = this.options.enableCollapsibleOptGroups && this.options.multiple ? !1 : e;
            var e = "undefined" == typeof e ? !0 : e, n = t("li input[type='checkbox']:enabled", this.$ul), r = n.filter(":visible"), s = n.length, o = r.length;
            if (e ? (r.prop("checked", !0), t("li:not(.divider):not(.disabled)", this.$ul).filter(":visible").addClass(this.options.selectedClass)) : (n.prop("checked", !0), 
            t("li:not(.divider):not(.disabled)", this.$ul).addClass(this.options.selectedClass)), 
            s === o || e === !1) t("option:not([data-role='divider']):enabled", this.$select).prop("selected", !0); else {
                var l = r.map(function() {
                    return t(this).val();
                }).get();
                t("option:enabled", this.$select).filter(function() {
                    return -1 !== t.inArray(t(this).val(), l);
                }).prop("selected", !0);
            }
            i && this.options.onSelectAll();
        },
        deselectAll: function(e) {
            if (e = this.options.enableCollapsibleOptGroups && this.options.multiple ? !1 : e, 
            e = "undefined" == typeof e ? !0 : e) {
                var i = t("li input[type='checkbox']:not(:disabled)", this.$ul).filter(":visible");
                i.prop("checked", !1);
                var n = i.map(function() {
                    return t(this).val();
                }).get();
                t("option:enabled", this.$select).filter(function() {
                    return -1 !== t.inArray(t(this).val(), n);
                }).prop("selected", !1), this.options.selectedClass && t("li:not(.divider):not(.disabled)", this.$ul).filter(":visible").removeClass(this.options.selectedClass);
            } else t("li input[type='checkbox']:enabled", this.$ul).prop("checked", !1), t("option:enabled", this.$select).prop("selected", !1), 
            this.options.selectedClass && t("li:not(.divider):not(.disabled)", this.$ul).removeClass(this.options.selectedClass);
        },
        rebuild: function() {
            this.$ul.html(""), this.options.multiple = "multiple" === this.$select.attr("multiple"), 
            this.buildSelectAll(), this.buildDropdownOptions(), this.buildFilter(), this.updateButtonText(), 
            this.updateSelectAll(!0), this.options.disableIfEmpty && t("option", this.$select).length <= 0 ? this.disable() : this.enable(), 
            this.options.dropRight && this.$ul.addClass("pull-right");
        },
        dataprovider: function(i) {
            var n = 0, r = this.$select.empty();
            t.each(i, function(i, s) {
                var o;
                t.isArray(s.children) ? (n++, o = t("<optgroup/>").attr({
                    label: s.label || "Group " + n,
                    disabled: !!s.disabled
                }), e(s.children, function(e) {
                    o.append(t("<option/>").attr({
                        value: e.value,
                        label: e.label || e.value,
                        title: e.title,
                        selected: !!e.selected,
                        disabled: !!e.disabled
                    }));
                })) : (o = t("<option/>").attr({
                    value: s.value,
                    label: s.label || s.value,
                    title: s.title,
                    class: s["class"],
                    selected: !!s.selected,
                    disabled: !!s.disabled
                }), o.text(s.label || s.value)), r.append(o);
            }), this.rebuild();
        },
        enable: function() {
            this.$select.prop("disabled", !1), this.$button.prop("disabled", !1).removeClass("disabled");
        },
        disable: function() {
            this.$select.prop("disabled", !0), this.$button.prop("disabled", !0).addClass("disabled");
        },
        setOptions: function(t) {
            this.options = this.mergeOptions(t);
        },
        mergeOptions: function(e) {
            return t.extend(!0, {}, this.defaults, this.options, e);
        },
        hasSelectAll: function() {
            return t("li.multiselect-all", this.$ul).length > 0;
        },
        updateSelectAll: function(e) {
            if (this.hasSelectAll()) {
                var i = t("li:not(.multiselect-item):not(.filter-hidden) input:enabled", this.$ul), n = i.length, r = i.filter(":checked").length, s = t("li.multiselect-all", this.$ul), o = s.find("input");
                r > 0 && r === n ? (o.prop("checked", !0), s.addClass(this.options.selectedClass), 
                this.options.onSelectAll(!0)) : (o.prop("checked", !1), s.removeClass(this.options.selectedClass), 
                0 === r && (e || this.options.onSelectAll(!1)));
            }
        },
        updateButtonText: function() {
            var e = this.getSelected();
            this.options.enableHTML ? t(".multiselect .multiselect-selected-text", this.$container).html(this.options.buttonText(e, this.$select)) : t(".multiselect .multiselect-selected-text", this.$container).text(this.options.buttonText(e, this.$select)), 
            t(".multiselect", this.$container).attr("title", this.options.buttonTitle(e, this.$select));
        },
        getSelected: function() {
            return t("option", this.$select).filter(":selected");
        },
        getOptionByValue: function(e) {
            for (var i = t("option", this.$select), n = e.toString(), r = 0; r < i.length; r += 1) {
                var s = i[r];
                if (s.value === n) return t(s);
            }
        },
        getInputByValue: function(e) {
            for (var i = t("li input", this.$ul), n = e.toString(), r = 0; r < i.length; r += 1) {
                var s = i[r];
                if (s.value === n) return t(s);
            }
        },
        updateOriginalOptions: function() {
            this.originalOptions = this.$select.clone()[0].options;
        },
        asyncFunction: function(t, e, i) {
            var n = Array.prototype.slice.call(arguments, 3);
            return setTimeout(function() {
                t.apply(i || window, n);
            }, e);
        },
        setAllSelectedText: function(t) {
            this.options.allSelectedText = t, this.updateButtonText();
        }
    }, t.fn.multiselect = function(e, n, r) {
        return this.each(function() {
            var s = t(this).data("multiselect"), o = "object" == typeof e && e;
            s || (s = new i(this, o), t(this).data("multiselect", s)), "string" == typeof e && (s[e](n, r), 
            "destroy" === e && t(this).data("multiselect", !1));
        });
    }, t.fn.multiselect.Constructor = i, t(function() {
        t("select[data-role=multiselect]").multiselect();
    });
}(asl_jQuery);



!function(t) {
    var e = function(e, i) {
        this.element = t(e), this.picker = t('<div class="slider"><div class="slider-track"><div class="slider-selection"></div><div class="slider-handle"></div><div class="slider-handle"></div></div><div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div></div>').insertBefore(this.element).append(this.element), 
        this.id = this.element.data("slider-id") || i.id, this.id && (this.picker[0].id = this.id), 
        "undefined" != typeof Modernizr && Modernizr.touch && (this.touchCapable = !0);
        var n = this.element.data("slider-tooltip") || i.tooltip;
        switch (this.tooltip = this.picker.find(".tooltip"), this.tooltipInner = this.tooltip.find("div.tooltip-inner"), 
        this.orientation = this.element.data("slider-orientation") || i.orientation, this.orientation) {
          case "vertical":
            this.picker.addClass("slider-vertical"), this.stylePos = "top", this.mousePos = "pageY", 
            this.sizePos = "offsetHeight", this.tooltip.addClass("right")[0].style.left = "100%";
            break;

          default:
            this.picker.addClass("slider-horizontal").css("width", this.element.outerWidth()), 
            this.orientation = "horizontal", this.stylePos = "left", this.mousePos = "pageX", 
            this.sizePos = "offsetWidth", this.tooltip.addClass("top")[0].style.top = -this.tooltip.outerHeight() - 14 + "px";
        }
        this.min = this.element.data("slider-min") || i.min, this.max = this.element.data("slider-max") || i.max, 
        this.step = this.element.data("slider-step") || i.step, this.value = this.element.data("slider-value") || i.value, 
        this.value[1] && (this.range = !0), this.selection = this.element.data("slider-selection") || i.selection, 
        this.selectionEl = this.picker.find(".slider-selection"), "none" === this.selection && this.selectionEl.addClass("hide"), 
        this.selectionElStyle = this.selectionEl[0].style, this.handle1 = this.picker.find(".slider-handle:first"), 
        this.handle1Stype = this.handle1[0].style, this.handle2 = this.picker.find(".slider-handle:last"), 
        this.handle2Stype = this.handle2[0].style;
        var r = this.element.data("slider-handle") || i.handle;
        switch (r) {
          case "round":
            this.handle1.addClass("round"), this.handle2.addClass("round");
            break;

          case "triangle":
            this.handle1.addClass("triangle"), this.handle2.addClass("triangle");
        }
        this.range ? (this.value[0] = Math.max(this.min, Math.min(this.max, this.value[0])), 
        this.value[1] = Math.max(this.min, Math.min(this.max, this.value[1]))) : (this.value = [ Math.max(this.min, Math.min(this.max, this.value)) ], 
        this.handle2.addClass("hide"), this.value[1] = "after" == this.selection ? this.max : this.min), 
        this.diff = this.max - this.min, this.percentage = [ 100 * (this.value[0] - this.min) / this.diff, 100 * (this.value[1] - this.min) / this.diff, 100 * this.step / this.diff ], 
        this.offset = this.picker.offset(), this.size = this.picker[0][this.sizePos], this.formater = i.formater, 
        this.layout(), this.picker.on(this.touchCapable ? {
            touchstart: t.proxy(this.mousedown, this)
        } : {
            mousedown: t.proxy(this.mousedown, this)
        }), "show" === n ? this.picker.on({
            mouseenter: t.proxy(this.showTooltip, this),
            mouseleave: t.proxy(this.hideTooltip, this)
        }) : this.tooltip.addClass("hide");
    };
    e.prototype = {
        constructor: e,
        over: !1,
        inDrag: !1,
        showTooltip: function() {
            this.tooltip.addClass("in"), this.over = !0;
        },
        hideTooltip: function() {
            this.inDrag === !1 && this.tooltip.removeClass("in"), this.over = !1;
        },
        layout: function() {
            this.handle1Stype[this.stylePos] = this.percentage[0] + "%", this.handle2Stype[this.stylePos] = this.percentage[1] + "%", 
            "vertical" == this.orientation ? (this.selectionElStyle.top = Math.min(this.percentage[0], this.percentage[1]) + "%", 
            this.selectionElStyle.height = Math.abs(this.percentage[0] - this.percentage[1]) + "%") : (this.selectionElStyle.left = Math.min(this.percentage[0], this.percentage[1]) + "%", 
            this.selectionElStyle.width = Math.abs(this.percentage[0] - this.percentage[1]) + "%"), 
            this.range ? (this.tooltipInner.text(this.formater(this.value[0]) + " : " + this.formater(this.value[1])), 
            this.tooltip[0].style[this.stylePos] = this.size * (this.percentage[0] + (this.percentage[1] - this.percentage[0]) / 2) / 100 - ("vertical" === this.orientation ? this.tooltip.outerHeight() / 2 : this.tooltip.outerWidth() / 2) + "px") : (this.tooltipInner.text(this.formater(this.value[0])), 
            this.tooltip[0].style[this.stylePos] = this.size * this.percentage[0] / 100 - ("vertical" === this.orientation ? this.tooltip.outerHeight() / 2 : this.tooltip.outerWidth() / 2) + "px");
        },
        mousedown: function(e) {
            this.touchCapable && "touchstart" === e.type && (e = e.originalEvent), this.offset = this.picker.offset(), 
            this.size = this.picker[0][this.sizePos];
            var i = this.getPercentage(e);
            if (this.range) {
                var n = Math.abs(this.percentage[0] - i), r = Math.abs(this.percentage[1] - i);
                this.dragged = r > n ? 0 : 1;
            } else this.dragged = 0;
            this.percentage[this.dragged] = i, this.layout(), t(document).on(this.touchCapable ? {
                touchmove: t.proxy(this.mousemove, this),
                touchend: t.proxy(this.mouseup, this)
            } : {
                mousemove: t.proxy(this.mousemove, this),
                mouseup: t.proxy(this.mouseup, this)
            }), this.inDrag = !0;
            var s = this.calculateValue();
            return this.element.trigger({
                type: "slideStart",
                value: s
            }).trigger({
                type: "slide",
                value: s
            }), !1;
        },
        mousemove: function(t) {
            this.touchCapable && "touchmove" === t.type && (t = t.originalEvent);
            var e = this.getPercentage(t);
            this.range && (0 === this.dragged && this.percentage[1] < e ? (this.percentage[0] = this.percentage[1], 
            this.dragged = 1) : 1 === this.dragged && this.percentage[0] > e && (this.percentage[1] = this.percentage[0], 
            this.dragged = 0)), this.percentage[this.dragged] = e, this.layout();
            var i = this.calculateValue();
            return this.element.trigger({
                type: "slide",
                value: i
            }).data("value", i).prop("value", i), !1;
        },
        mouseup: function() {
            t(document).off(this.touchCapable ? {
                touchmove: this.mousemove,
                touchend: this.mouseup
            } : {
                mousemove: this.mousemove,
                mouseup: this.mouseup
            }), this.inDrag = !1, 0 == this.over && this.hideTooltip(), this.element;
            var e = this.calculateValue();
            return this.element.trigger({
                type: "slideStop",
                value: e
            }).data("value", e).prop("value", e), !1;
        },
        calculateValue: function() {
            var t;
            return this.range ? (t = [ this.min + Math.round(this.diff * this.percentage[0] / 100 / this.step) * this.step, this.min + Math.round(this.diff * this.percentage[1] / 100 / this.step) * this.step ], 
            this.value = t) : (t = this.min + Math.round(this.diff * this.percentage[0] / 100 / this.step) * this.step, 
            this.value = [ t, this.value[1] ]), t;
        },
        getPercentage: function(t) {
            this.touchCapable && (t = t.touches[0]);
            var e = 100 * (t[this.mousePos] - this.offset[this.stylePos]) / this.size;
            return e = Math.round(e / this.percentage[2]) * this.percentage[2], Math.max(0, Math.min(100, e));
        },
        getValue: function() {
            return this.range ? this.value : this.value[0];
        },
        setValue: function(t) {
            this.value = t, this.range ? (this.value[0] = Math.max(this.min, Math.min(this.max, this.value[0])), 
            this.value[1] = Math.max(this.min, Math.min(this.max, this.value[1]))) : (this.value = [ Math.max(this.min, Math.min(this.max, this.value)) ], 
            this.handle2.addClass("hide"), this.value[1] = "after" == this.selection ? this.max : this.min), 
            this.diff = this.max - this.min, this.percentage = [ 100 * (this.value[0] - this.min) / this.diff, 100 * (this.value[1] - this.min) / this.diff, 100 * this.step / this.diff ], 
            this.layout();
        }
    }, t.fn.bslider = function(i, n) {
        return this.each(function() {
            var r = t(this), s = r.data("slider"), o = "object" == typeof i && i;
            s || r.data("slider", s = new e(this, t.extend({}, t.fn.bslider.defaults, o))), 
            "string" == typeof i && s[i](n);
        });
    }, t.fn.bslider.defaults = {
        min: 0,
        max: 10,
        step: 1,
        orientation: "horizontal",
        value: 5,
        selection: "before",
        tooltip: "show",
        handle: "round",
        formater: function(t) {
            return t;
        }
    }, t.fn.bslider.Constructor = e;
}(asl_jQuery);

/*!
 * typeahead.js 0.11.1
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2015 Twitter, Inc. and other contributors; Licensed MIT
 */
!function(a, b) {
    "function" == typeof define && define.amd ? define("bloodhound", [ "jquery" ], function(c) {
        return a.Bloodhound = b(c);
    }) : "object" == typeof exports ? module.exports = b(require("jquery")) : a.Bloodhound = b(jQuery);
}(this, function(a) {
    var b = function() {
        "use strict";
        return {
            isMsie: function() {
                return /(msie|trident)/i.test(navigator.userAgent) ? navigator.userAgent.match(/(msie |rv:)(\d+(.\d+)?)/i)[2] : !1;
            },
            isBlankString: function(a) {
                return !a || /^\s*$/.test(a);
            },
            escapeRegExChars: function(a) {
                return a.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
            },
            isString: function(a) {
                return "string" == typeof a;
            },
            isNumber: function(a) {
                return "number" == typeof a;
            },
            isArray: a.isArray,
            isFunction: a.isFunction,
            isObject: a.isPlainObject,
            isUndefined: function(a) {
                return "undefined" == typeof a;
            },
            isElement: function(a) {
                return !(!a || 1 !== a.nodeType);
            },
            isJQuery: function(b) {
                return b instanceof a;
            },
            toStr: function(a) {
                return b.isUndefined(a) || null === a ? "" : a + "";
            },
            bind: a.proxy,
            each: function(b, c) {
                function d(a, b) {
                    return c(b, a);
                }
                a.each(b, d);
            },
            map: a.map,
            filter: a.grep,
            every: function(b, c) {
                var d = !0;
                return b ? (a.each(b, function(a, e) {
                    return (d = c.call(null, e, a, b)) ? void 0 : !1;
                }), !!d) : d;
            },
            some: function(b, c) {
                var d = !1;
                return b ? (a.each(b, function(a, e) {
                    return (d = c.call(null, e, a, b)) ? !1 : void 0;
                }), !!d) : d;
            },
            mixin: a.extend,
            identity: function(a) {
                return a;
            },
            clone: function(b) {
                return a.extend(!0, {}, b);
            },
            getIdGenerator: function() {
                var a = 0;
                return function() {
                    return a++;
                };
            },
            templatify: function(b) {
                function c() {
                    return String(b);
                }
                return a.isFunction(b) ? b : c;
            },
            defer: function(a) {
                setTimeout(a, 0);
            },
            debounce: function(a, b, c) {
                var d, e;
                return function() {
                    var f, g, h = this, i = arguments;
                    return f = function() {
                        d = null, c || (e = a.apply(h, i));
                    }, g = c && !d, clearTimeout(d), d = setTimeout(f, b), g && (e = a.apply(h, i)), 
                    e;
                };
            },
            throttle: function(a, b) {
                var c, d, e, f, g, h;
                return g = 0, h = function() {
                    g = new Date(), e = null, f = a.apply(c, d);
                }, function() {
                    var i = new Date(), j = b - (i - g);
                    return c = this, d = arguments, 0 >= j ? (clearTimeout(e), e = null, g = i, f = a.apply(c, d)) : e || (e = setTimeout(h, j)), 
                    f;
                };
            },
            stringify: function(a) {
                return b.isString(a) ? a : JSON.stringify(a);
            },
            noop: function() {}
        };
    }(), c = "0.11.1", d = function() {
        "use strict";
        function a(a) {
            return a = b.toStr(a), a ? a.split(/\s+/) : [];
        }
        function c(a) {
            return a = b.toStr(a), a ? a.split(/\W+/) : [];
        }
        function d(a) {
            return function(c) {
                return c = b.isArray(c) ? c : [].slice.call(arguments, 0), function(d) {
                    var e = [];
                    return b.each(c, function(c) {
                        e = e.concat(a(b.toStr(d[c])));
                    }), e;
                };
            };
        }
        return {
            nonword: c,
            whitespace: a,
            obj: {
                nonword: d(c),
                whitespace: d(a)
            }
        };
    }(), e = function() {
        "use strict";
        function c(c) {
            this.maxSize = b.isNumber(c) ? c : 100, this.reset(), this.maxSize <= 0 && (this.set = this.get = a.noop);
        }
        function d() {
            this.head = this.tail = null;
        }
        function e(a, b) {
            this.key = a, this.val = b, this.prev = this.next = null;
        }
        return b.mixin(c.prototype, {
            set: function(a, b) {
                var c, d = this.list.tail;
                this.size >= this.maxSize && (this.list.remove(d), delete this.hash[d.key], this.size--), 
                (c = this.hash[a]) ? (c.val = b, this.list.moveToFront(c)) : (c = new e(a, b), this.list.add(c), 
                this.hash[a] = c, this.size++);
            },
            get: function(a) {
                var b = this.hash[a];
                return b ? (this.list.moveToFront(b), b.val) : void 0;
            },
            reset: function() {
                this.size = 0, this.hash = {}, this.list = new d();
            }
        }), b.mixin(d.prototype, {
            add: function(a) {
                this.head && (a.next = this.head, this.head.prev = a), this.head = a, this.tail = this.tail || a;
            },
            remove: function(a) {
                a.prev ? a.prev.next = a.next : this.head = a.next, a.next ? a.next.prev = a.prev : this.tail = a.prev;
            },
            moveToFront: function(a) {
                this.remove(a), this.add(a);
            }
        }), c;
    }(), f = function() {
        "use strict";
        function c(a, c) {
            this.prefix = [ "__", a, "__" ].join(""), this.ttlKey = "__ttl__", this.keyMatcher = new RegExp("^" + b.escapeRegExChars(this.prefix)), 
            this.ls = c || h, !this.ls && this._noop();
        }
        function d() {
            return new Date().getTime();
        }
        function e(a) {
            return JSON.stringify(b.isUndefined(a) ? null : a);
        }
        function f(b) {
            return a.parseJSON(b);
        }
        function g(a) {
            var b, c, d = [], e = h.length;
            for (b = 0; e > b; b++) (c = h.key(b)).match(a) && d.push(c.replace(a, ""));
            return d;
        }
        var h;
        try {
            h = window.localStorage, h.setItem("~~~", "!"), h.removeItem("~~~");
        } catch (i) {
            h = null;
        }
        return b.mixin(c.prototype, {
            _prefix: function(a) {
                return this.prefix + a;
            },
            _ttlKey: function(a) {
                return this._prefix(a) + this.ttlKey;
            },
            _noop: function() {
                this.get = this.set = this.remove = this.clear = this.isExpired = b.noop;
            },
            _safeSet: function(a, b) {
                try {
                    this.ls.setItem(a, b);
                } catch (c) {
                    "QuotaExceededError" === c.name && (this.clear(), this._noop());
                }
            },
            get: function(a) {
                return this.isExpired(a) && this.remove(a), f(this.ls.getItem(this._prefix(a)));
            },
            set: function(a, c, f) {
                return b.isNumber(f) ? this._safeSet(this._ttlKey(a), e(d() + f)) : this.ls.removeItem(this._ttlKey(a)), 
                this._safeSet(this._prefix(a), e(c));
            },
            remove: function(a) {
                return this.ls.removeItem(this._ttlKey(a)), this.ls.removeItem(this._prefix(a)), 
                this;
            },
            clear: function() {
                var a, b = g(this.keyMatcher);
                for (a = b.length; a--; ) this.remove(b[a]);
                return this;
            },
            isExpired: function(a) {
                var c = f(this.ls.getItem(this._ttlKey(a)));
                return b.isNumber(c) && d() > c ? !0 : !1;
            }
        }), c;
    }(), g = function() {
        "use strict";
        function c(a) {
            a = a || {}, this.cancelled = !1, this.lastReq = null, this._send = a.transport, 
            this._get = a.limiter ? a.limiter(this._get) : this._get, this._cache = a.cache === !1 ? new e(0) : h;
        }
        var d = 0, f = {}, g = 6, h = new e(10);
        return c.setMaxPendingRequests = function(a) {
            g = a;
        }, c.resetCache = function() {
            h.reset();
        }, b.mixin(c.prototype, {
            _fingerprint: function(b) {
                return b = b || {}, b.url + b.type + a.param(b.data || {});
            },
            _get: function(a, b) {
                function c(a) {
                    b(null, a), k._cache.set(i, a);
                }
                function e() {
                    b(!0);
                }
                function h() {
                    d--, delete f[i], k.onDeckRequestArgs && (k._get.apply(k, k.onDeckRequestArgs), 
                    k.onDeckRequestArgs = null);
                }
                var i, j, k = this;
                i = this._fingerprint(a), this.cancelled || i !== this.lastReq || ((j = f[i]) ? j.done(c).fail(e) : g > d ? (d++, 
                f[i] = this._send(a).done(c).fail(e).always(h)) : this.onDeckRequestArgs = [].slice.call(arguments, 0));
            },
            get: function(c, d) {
                var e, f;
                d = d || a.noop, c = b.isString(c) ? {
                    url: c
                } : c || {}, f = this._fingerprint(c), this.cancelled = !1, this.lastReq = f, (e = this._cache.get(f)) ? d(null, e) : this._get(c, d);
            },
            cancel: function() {
                this.cancelled = !0;
            }
        }), c;
    }(), h = window.SearchIndex = function() {
        "use strict";
        function c(c) {
            c = c || {}, c.datumTokenizer && c.queryTokenizer || a.error("datumTokenizer and queryTokenizer are both required"), 
            this.identify = c.identify || b.stringify, this.datumTokenizer = c.datumTokenizer, 
            this.queryTokenizer = c.queryTokenizer, this.reset();
        }
        function d(a) {
            return a = b.filter(a, function(a) {
                return !!a;
            }), a = b.map(a, function(a) {
                return a.toLowerCase();
            });
        }
        function e() {
            var a = {};
            return a[i] = [], a[h] = {}, a;
        }
        function f(a) {
            for (var b = {}, c = [], d = 0, e = a.length; e > d; d++) b[a[d]] || (b[a[d]] = !0, 
            c.push(a[d]));
            return c;
        }
        function g(a, b) {
            var c = 0, d = 0, e = [];
            a = a.sort(), b = b.sort();
            for (var f = a.length, g = b.length; f > c && g > d; ) a[c] < b[d] ? c++ : a[c] > b[d] ? d++ : (e.push(a[c]), 
            c++, d++);
            return e;
        }
        var h = "c", i = "i";
        return b.mixin(c.prototype, {
            bootstrap: function(a) {
                this.datums = a.datums, this.trie = a.trie;
            },
            add: function(a) {
                var c = this;
                a = b.isArray(a) ? a : [ a ], b.each(a, function(a) {
                    var f, g;
                    c.datums[f = c.identify(a)] = a, g = d(c.datumTokenizer(a)), b.each(g, function(a) {
                        var b, d, g;
                        for (b = c.trie, d = a.split(""); g = d.shift(); ) b = b[h][g] || (b[h][g] = e()), 
                        b[i].push(f);
                    });
                });
            },
            get: function(a) {
                var c = this;
                return b.map(a, function(a) {
                    return c.datums[a];
                });
            },
            search: function(a) {
                var c, e, j = this;
                return c = d(this.queryTokenizer(a)), b.each(c, function(a) {
                    var b, c, d, f;
                    if (e && 0 === e.length) return !1;
                    for (b = j.trie, c = a.split(""); b && (d = c.shift()); ) b = b[h][d];
                    return b && 0 === c.length ? (f = b[i].slice(0), void (e = e ? g(e, f) : f)) : (e = [], 
                    !1);
                }), e ? b.map(f(e), function(a) {
                    return j.datums[a];
                }) : [];
            },
            all: function() {
                var a = [];
                for (var b in this.datums) a.push(this.datums[b]);
                return a;
            },
            reset: function() {
                this.datums = {}, this.trie = e();
            },
            serialize: function() {
                return {
                    datums: this.datums,
                    trie: this.trie
                };
            }
        }), c;
    }(), i = function() {
        "use strict";
        function a(a) {
            this.url = a.url, this.ttl = a.ttl, this.cache = a.cache, this.prepare = a.prepare, 
            this.transform = a.transform, this.transport = a.transport, this.thumbprint = a.thumbprint, 
            this.storage = new f(a.cacheKey);
        }
        var c;
        return c = {
            data: "data",
            protocol: "protocol",
            thumbprint: "thumbprint"
        }, b.mixin(a.prototype, {
            _settings: function() {
                return {
                    url: this.url,
                    type: "GET",
                    dataType: "json"
                };
            },
            store: function(a) {
                this.cache && (this.storage.set(c.data, a, this.ttl), this.storage.set(c.protocol, location.protocol, this.ttl), 
                this.storage.set(c.thumbprint, this.thumbprint, this.ttl));
            },
            fromCache: function() {
                var a, b = {};
                return this.cache ? (b.data = this.storage.get(c.data), b.protocol = this.storage.get(c.protocol), 
                b.thumbprint = this.storage.get(c.thumbprint), a = b.thumbprint !== this.thumbprint || b.protocol !== location.protocol, 
                b.data && !a ? b.data : null) : null;
            },
            fromNetwork: function(a) {
                function b() {
                    a(!0);
                }
                function c(b) {
                    a(null, e.transform(b));
                }
                var d, e = this;
                a && (d = this.prepare(this._settings()), this.transport(d).fail(b).done(c));
            },
            clear: function() {
                return this.storage.clear(), this;
            }
        }), a;
    }(), j = function() {
        "use strict";
        function a(a) {
            this.url = a.url, this.prepare = a.prepare, this.transform = a.transform, this.transport = new g({
                cache: a.cache,
                limiter: a.limiter,
                transport: a.transport
            });
        }
        return b.mixin(a.prototype, {
            _settings: function() {
                return {
                    url: this.url,
                    type: "GET",
                    dataType: "json"
                };
            },
            get: function(a, b) {
                function c(a, c) {
                    b(a ? [] : e.transform(c));
                }
                var d, e = this;
                if (b) return a = a || "", d = this.prepare(a, this._settings()), this.transport.get(d, c);
            },
            cancelLastRequest: function() {
                this.transport.cancel();
            }
        }), a;
    }(), k = function() {
        "use strict";
        function d(d) {
            var e;
            return d ? (e = {
                url: null,
                ttl: 864e5,
                cache: !0,
                cacheKey: null,
                thumbprint: "",
                prepare: b.identity,
                transform: b.identity,
                transport: null
            }, d = b.isString(d) ? {
                url: d
            } : d, d = b.mixin(e, d), !d.url && a.error("prefetch requires url to be set"), 
            d.transform = d.filter || d.transform, d.cacheKey = d.cacheKey || d.url, d.thumbprint = c + d.thumbprint, 
            d.transport = d.transport ? h(d.transport) : a.ajax, d) : null;
        }
        function e(c) {
            var d;
            if (c) return d = {
                url: null,
                cache: !0,
                prepare: null,
                replace: null,
                wildcard: null,
                limiter: null,
                rateLimitBy: "debounce",
                rateLimitWait: 300,
                transform: b.identity,
                transport: null
            }, c = b.isString(c) ? {
                url: c
            } : c, c = b.mixin(d, c), !c.url && a.error("remote requires url to be set"), c.transform = c.filter || c.transform, 
            c.prepare = f(c), c.limiter = g(c), c.transport = c.transport ? h(c.transport) : a.ajax, 
            delete c.replace, delete c.wildcard, delete c.rateLimitBy, delete c.rateLimitWait, 
            c;
        }
        function f(a) {
            function b(a, b) {
                return b.url = f(b.url, a), b;
            }
            function c(a, b) {
                return b.url = b.url.replace(g, encodeURIComponent(a)), b;
            }
            function d(a, b) {
                return b;
            }
            var e, f, g;
            return e = a.prepare, f = a.replace, g = a.wildcard, e ? e : e = f ? b : a.wildcard ? c : d;
        }
        function g(a) {
            function c(a) {
                return function(c) {
                    return b.debounce(c, a);
                };
            }
            function d(a) {
                return function(c) {
                    return b.throttle(c, a);
                };
            }
            var e, f, g;
            return e = a.limiter, f = a.rateLimitBy, g = a.rateLimitWait, e || (e = /^throttle$/i.test(f) ? d(g) : c(g)), 
            e;
        }
        function h(c) {
            return function(d) {
                function e(a) {
                    b.defer(function() {
                        g.resolve(a);
                    });
                }
                function f(a) {
                    b.defer(function() {
                        g.reject(a);
                    });
                }
                var g = a.Deferred();
                return c(d, e, f), g;
            };
        }
        return function(c) {
            var f, g;
            return f = {
                initialize: !0,
                identify: b.stringify,
                datumTokenizer: null,
                queryTokenizer: null,
                sufficient: 5,
                sorter: null,
                local: [],
                prefetch: null,
                remote: null
            }, c = b.mixin(f, c || {}), !c.datumTokenizer && a.error("datumTokenizer is required"), 
            !c.queryTokenizer && a.error("queryTokenizer is required"), g = c.sorter, c.sorter = g ? function(a) {
                return a.sort(g);
            } : b.identity, c.local = b.isFunction(c.local) ? c.local() : c.local, c.prefetch = d(c.prefetch), 
            c.remote = e(c.remote), c;
        };
    }(), l = function() {
        "use strict";
        function c(a) {
            a = k(a), this.sorter = a.sorter, this.identify = a.identify, this.sufficient = a.sufficient, 
            this.local = a.local, this.remote = a.remote ? new j(a.remote) : null, this.prefetch = a.prefetch ? new i(a.prefetch) : null, 
            this.index = new h({
                identify: this.identify,
                datumTokenizer: a.datumTokenizer,
                queryTokenizer: a.queryTokenizer
            }), a.initialize !== !1 && this.initialize();
        }
        var e;
        return e = window && window.Bloodhound, c.noConflict = function() {
            return window && (window.Bloodhound = e), c;
        }, c.tokenizers = d, b.mixin(c.prototype, {
            __ttAdapter: function() {
                function a(a, b, d) {
                    return c.search(a, b, d);
                }
                function b(a, b) {
                    return c.search(a, b);
                }
                var c = this;
                return this.remote ? a : b;
            },
            _loadPrefetch: function() {
                function b(a, b) {
                    return a ? c.reject() : (e.add(b), e.prefetch.store(e.index.serialize()), void c.resolve());
                }
                var c, d, e = this;
                return c = a.Deferred(), this.prefetch ? (d = this.prefetch.fromCache()) ? (this.index.bootstrap(d), 
                c.resolve()) : this.prefetch.fromNetwork(b) : c.resolve(), c.promise();
            },
            _initialize: function() {
                function a() {
                    b.add(b.local);
                }
                var b = this;
                return this.clear(), (this.initPromise = this._loadPrefetch()).done(a), this.initPromise;
            },
            initialize: function(a) {
                return !this.initPromise || a ? this._initialize() : this.initPromise;
            },
            add: function(a) {
                return this.index.add(a), this;
            },
            get: function(a) {
                return a = b.isArray(a) ? a : [].slice.call(arguments), this.index.get(a);
            },
            search: function(a, c, d) {
                function e(a) {
                    var c = [];
                    b.each(a, function(a) {
                        !b.some(f, function(b) {
                            return g.identify(a) === g.identify(b);
                        }) && c.push(a);
                    }), d && d(c);
                }
                var f, g = this;
                return f = this.sorter(this.index.search(a)), c(this.remote ? f.slice() : f), this.remote && f.length < this.sufficient ? this.remote.get(a, e) : this.remote && this.remote.cancelLastRequest(), 
                this;
            },
            all: function() {
                return this.index.all();
            },
            clear: function() {
                return this.index.reset(), this;
            },
            clearPrefetchCache: function() {
                return this.prefetch && this.prefetch.clear(), this;
            },
            clearRemoteCache: function() {
                return g.resetCache(), this;
            },
            ttAdapter: function() {
                return this.__ttAdapter();
            }
        }), c;
    }();
    return l;
}), function(a, b) {
    "function" == typeof define && define.amd ? define("typeahead.js", [ "jquery" ], function(a) {
        return b(a);
    }) : "object" == typeof exports ? module.exports = b(require("jquery")) : b(jQuery);
}(this, function(a) {
    var b = function() {
        "use strict";
        return {
            isMsie: function() {
                return /(msie|trident)/i.test(navigator.userAgent) ? navigator.userAgent.match(/(msie |rv:)(\d+(.\d+)?)/i)[2] : !1;
            },
            isBlankString: function(a) {
                return !a || /^\s*$/.test(a);
            },
            escapeRegExChars: function(a) {
                return a.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
            },
            isString: function(a) {
                return "string" == typeof a;
            },
            isNumber: function(a) {
                return "number" == typeof a;
            },
            isArray: a.isArray,
            isFunction: a.isFunction,
            isObject: a.isPlainObject,
            isUndefined: function(a) {
                return "undefined" == typeof a;
            },
            isElement: function(a) {
                return !(!a || 1 !== a.nodeType);
            },
            isJQuery: function(b) {
                return b instanceof a;
            },
            toStr: function(a) {
                return b.isUndefined(a) || null === a ? "" : a + "";
            },
            bind: a.proxy,
            each: function(b, c) {
                function d(a, b) {
                    return c(b, a);
                }
                a.each(b, d);
            },
            map: a.map,
            filter: a.grep,
            every: function(b, c) {
                var d = !0;
                return b ? (a.each(b, function(a, e) {
                    return (d = c.call(null, e, a, b)) ? void 0 : !1;
                }), !!d) : d;
            },
            some: function(b, c) {
                var d = !1;
                return b ? (a.each(b, function(a, e) {
                    return (d = c.call(null, e, a, b)) ? !1 : void 0;
                }), !!d) : d;
            },
            mixin: a.extend,
            identity: function(a) {
                return a;
            },
            clone: function(b) {
                return a.extend(!0, {}, b);
            },
            getIdGenerator: function() {
                var a = 0;
                return function() {
                    return a++;
                };
            },
            templatify: function(b) {
                function c() {
                    return String(b);
                }
                return a.isFunction(b) ? b : c;
            },
            defer: function(a) {
                setTimeout(a, 0);
            },
            debounce: function(a, b, c) {
                var d, e;
                return function() {
                    var f, g, h = this, i = arguments;
                    return f = function() {
                        d = null, c || (e = a.apply(h, i));
                    }, g = c && !d, clearTimeout(d), d = setTimeout(f, b), g && (e = a.apply(h, i)), 
                    e;
                };
            },
            throttle: function(a, b) {
                var c, d, e, f, g, h;
                return g = 0, h = function() {
                    g = new Date(), e = null, f = a.apply(c, d);
                }, function() {
                    var i = new Date(), j = b - (i - g);
                    return c = this, d = arguments, 0 >= j ? (clearTimeout(e), e = null, g = i, f = a.apply(c, d)) : e || (e = setTimeout(h, j)), 
                    f;
                };
            },
            stringify: function(a) {
                return b.isString(a) ? a : JSON.stringify(a);
            },
            noop: function() {}
        };
    }(), c = function() {
        "use strict";
        function a(a) {
            var g, h;
            return h = b.mixin({}, f, a), g = {
                css: e(),
                classes: h,
                html: c(h),
                selectors: d(h)
            }, {
                css: g.css,
                html: g.html,
                classes: g.classes,
                selectors: g.selectors,
                mixin: function(a) {
                    b.mixin(a, g);
                }
            };
        }
        function c(a) {
            return {
                wrapper: '<span class="' + a.wrapper + '"></span>',
                menu: '<div class="' + a.menu + '"></div>'
            };
        }
        function d(a) {
            var c = {};
            return b.each(a, function(a, b) {
                c[b] = "." + a;
            }), c;
        }
        function e() {
            var a = {
                wrapper: {
                    position: "relative",
                    display: "inline-block"
                },
                hint: {
                    position: "absolute",
                    top: "0",
                    left: "0",
                    borderColor: "transparent",
                    boxShadow: "none",
                    opacity: "1"
                },
                input: {
                    position: "relative",
                    verticalAlign: "top",
                    backgroundColor: "transparent"
                },
                inputWithNoHint: {
                    position: "relative",
                    verticalAlign: "top"
                },
                menu: {
                    position: "absolute",
                    top: "100%",
                    left: "0",
                    zIndex: "100",
                    display: "none"
                },
                ltr: {
                    left: "0",
                    right: "auto"
                },
                rtl: {
                    left: "auto",
                    right: " 0"
                }
            };
            return b.isMsie() && b.mixin(a.input, {
                backgroundImage: "url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)"
            }), a;
        }
        var f = {
            wrapper: "twitter-typeahead",
            input: "tt-input",
            hint: "tt-hint",
            menu: "tt-menu",
            dataset: "tt-dataset",
            suggestion: "tt-suggestion",
            selectable: "tt-selectable",
            empty: "tt-empty",
            open: "tt-open",
            cursor: "tt-cursor",
            highlight: "tt-highlight"
        };
        return a;
    }(), d = function() {
        "use strict";
        function c(b) {
            b && b.el || a.error("EventBus initialized without el"), this.$el = a(b.el);
        }
        var d, e;
        return d = "typeahead:", e = {
            render: "rendered",
            cursorchange: "cursorchanged",
            select: "selected",
            autocomplete: "autocompleted"
        }, b.mixin(c.prototype, {
            _trigger: function(b, c) {
                var e;
                return e = a.Event(d + b), (c = c || []).unshift(e), this.$el.trigger.apply(this.$el, c), 
                e;
            },
            before: function(a) {
                var b, c;
                return b = [].slice.call(arguments, 1), c = this._trigger("before" + a, b), c.isDefaultPrevented();
            },
            trigger: function(a) {
                var b;
                this._trigger(a, [].slice.call(arguments, 1)), (b = e[a]) && this._trigger(b, [].slice.call(arguments, 1));
            }
        }), c;
    }(), e = function() {
        "use strict";
        function a(a, b, c, d) {
            var e;
            if (!c) return this;
            for (b = b.split(i), c = d ? h(c, d) : c, this._callbacks = this._callbacks || {}; e = b.shift(); ) this._callbacks[e] = this._callbacks[e] || {
                sync: [],
                async: []
            }, this._callbacks[e][a].push(c);
            return this;
        }
        function b(b, c, d) {
            return a.call(this, "async", b, c, d);
        }
        function c(b, c, d) {
            return a.call(this, "sync", b, c, d);
        }
        function d(a) {
            var b;
            if (!this._callbacks) return this;
            for (a = a.split(i); b = a.shift(); ) delete this._callbacks[b];
            return this;
        }
        function e(a) {
            var b, c, d, e, g;
            if (!this._callbacks) return this;
            for (a = a.split(i), d = [].slice.call(arguments, 1); (b = a.shift()) && (c = this._callbacks[b]); ) e = f(c.sync, this, [ b ].concat(d)), 
            g = f(c.async, this, [ b ].concat(d)), e() && j(g);
            return this;
        }
        function f(a, b, c) {
            function d() {
                for (var d, e = 0, f = a.length; !d && f > e; e += 1) d = a[e].apply(b, c) === !1;
                return !d;
            }
            return d;
        }
        function g() {
            var a;
            return a = window.setImmediate ? function(a) {
                setImmediate(function() {
                    a();
                });
            } : function(a) {
                setTimeout(function() {
                    a();
                }, 0);
            };
        }
        function h(a, b) {
            return a.bind ? a.bind(b) : function() {
                a.apply(b, [].slice.call(arguments, 0));
            };
        }
        var i = /\s+/, j = g();
        return {
            onSync: c,
            onAsync: b,
            off: d,
            trigger: e
        };
    }(), f = function(a) {
        "use strict";
        function c(a, c, d) {
            for (var e, f = [], g = 0, h = a.length; h > g; g++) f.push(b.escapeRegExChars(a[g]));
            return e = d ? "\\b(" + f.join("|") + ")\\b" : "(" + f.join("|") + ")", c ? new RegExp(e) : new RegExp(e, "i");
        }
        var d = {
            node: null,
            pattern: null,
            tagName: "strong",
            className: null,
            wordsOnly: !1,
            caseSensitive: !1
        };
        return function(e) {
            function f(b) {
                var c, d, f;
                return (c = h.exec(b.data)) && (f = a.createElement(e.tagName), e.className && (f.className = e.className), 
                d = b.splitText(c.index), d.splitText(c[0].length), f.appendChild(d.cloneNode(!0)), 
                b.parentNode.replaceChild(f, d)), !!c;
            }
            function g(a, b) {
                for (var c, d = 3, e = 0; e < a.childNodes.length; e++) c = a.childNodes[e], c.nodeType === d ? e += b(c) ? 1 : 0 : g(c, b);
            }
            var h;
            e = b.mixin({}, d, e), e.node && e.pattern && (e.pattern = b.isArray(e.pattern) ? e.pattern : [ e.pattern ], 
            h = c(e.pattern, e.caseSensitive, e.wordsOnly), g(e.node, f));
        };
    }(window.document), g = function() {
        "use strict";
        function c(c, e) {
            c = c || {}, c.input || a.error("input is missing"), e.mixin(this), this.$hint = a(c.hint), 
            this.$input = a(c.input), this.query = this.$input.val(), this.queryWhenFocused = this.hasFocus() ? this.query : null, 
            this.$overflowHelper = d(this.$input), this._checkLanguageDirection(), 0 === this.$hint.length && (this.setHint = this.getHint = this.clearHint = this.clearHintIfInvalid = b.noop);
        }
        function d(b) {
            return a('<pre aria-hidden="true"></pre>').css({
                position: "absolute",
                visibility: "hidden",
                whiteSpace: "pre",
                fontFamily: b.css("font-family"),
                fontSize: b.css("font-size"),
                fontStyle: b.css("font-style"),
                fontVariant: b.css("font-variant"),
                fontWeight: b.css("font-weight"),
                wordSpacing: b.css("word-spacing"),
                letterSpacing: b.css("letter-spacing"),
                textIndent: b.css("text-indent"),
                textRendering: b.css("text-rendering"),
                textTransform: b.css("text-transform")
            }).insertAfter(b);
        }
        function f(a, b) {
            return c.normalizeQuery(a) === c.normalizeQuery(b);
        }
        function g(a) {
            return a.altKey || a.ctrlKey || a.metaKey || a.shiftKey;
        }
        var h;
        return h = {
            9: "tab",
            27: "esc",
            37: "left",
            39: "right",
            13: "enter",
            38: "up",
            40: "down"
        }, c.normalizeQuery = function(a) {
            return b.toStr(a).replace(/^\s*/g, "").replace(/\s{2,}/g, " ");
        }, b.mixin(c.prototype, e, {
            _onBlur: function() {
                this.resetInputValue(), this.trigger("blurred");
            },
            _onFocus: function() {
                this.queryWhenFocused = this.query, this.trigger("focused");
            },
            _onKeydown: function(a) {
                var b = h[a.which || a.keyCode];
                this._managePreventDefault(b, a), b && this._shouldTrigger(b, a) && this.trigger(b + "Keyed", a);
            },
            _onInput: function() {
                this._setQuery(this.getInputValue()), this.clearHintIfInvalid(), this._checkLanguageDirection();
            },
            _managePreventDefault: function(a, b) {
                var c;
                switch (a) {
                  case "up":
                  case "down":
                    c = !g(b);
                    break;

                  default:
                    c = !1;
                }
                c && b.preventDefault();
            },
            _shouldTrigger: function(a, b) {
                var c;
                switch (a) {
                  case "tab":
                    c = !g(b);
                    break;

                  default:
                    c = !0;
                }
                return c;
            },
            _checkLanguageDirection: function() {
                var a = (this.$input.css("direction") || "ltr").toLowerCase();
                this.dir !== a && (this.dir = a, this.$hint.attr("dir", a), this.trigger("langDirChanged", a));
            },
            _setQuery: function(a, b) {
                var c, d;
                c = f(a, this.query), d = c ? this.query.length !== a.length : !1, this.query = a, 
                b || c ? !b && d && this.trigger("whitespaceChanged", this.query) : this.trigger("queryChanged", this.query);
            },
            bind: function() {
                var a, c, d, e, f = this;
                return a = b.bind(this._onBlur, this), c = b.bind(this._onFocus, this), d = b.bind(this._onKeydown, this), 
                e = b.bind(this._onInput, this), this.$input.on("blur.tt", a).on("focus.tt", c).on("keydown.tt", d), 
                !b.isMsie() || b.isMsie() > 9 ? this.$input.on("input.tt", e) : this.$input.on("keydown.tt keypress.tt cut.tt paste.tt", function(a) {
                    h[a.which || a.keyCode] || b.defer(b.bind(f._onInput, f, a));
                }), this;
            },
            focus: function() {
                this.$input.focus();
            },
            blur: function() {
                this.$input.blur();
            },
            getLangDir: function() {
                return this.dir;
            },
            getQuery: function() {
                return this.query || "";
            },
            setQuery: function(a, b) {
                this.setInputValue(a), this._setQuery(a, b);
            },
            hasQueryChangedSinceLastFocus: function() {
                return this.query !== this.queryWhenFocused;
            },
            getInputValue: function() {
                return this.$input.val();
            },
            setInputValue: function(a) {
                this.$input.val(a), this.clearHintIfInvalid(), this._checkLanguageDirection();
            },
            resetInputValue: function() {
                this.setInputValue(this.query);
            },
            getHint: function() {
                return this.$hint.val();
            },
            setHint: function(a) {
                this.$hint.val(a);
            },
            clearHint: function() {
                this.setHint("");
            },
            clearHintIfInvalid: function() {
                var a, b, c, d;
                a = this.getInputValue(), b = this.getHint(), c = a !== b && 0 === b.indexOf(a), 
                d = "" !== a && c && !this.hasOverflow(), !d && this.clearHint();
            },
            hasFocus: function() {
                return this.$input.is(":focus");
            },
            hasOverflow: function() {
                var a = this.$input.width() - 2;
                return this.$overflowHelper.text(this.getInputValue()), this.$overflowHelper.width() >= a;
            },
            isCursorAtEnd: function() {
                var a, c, d;
                return a = this.$input.val().length, c = this.$input[0].selectionStart, b.isNumber(c) ? c === a : document.selection ? (d = document.selection.createRange(), 
                d.moveStart("character", -a), a === d.text.length) : !0;
            },
            destroy: function() {
                this.$hint.off(".tt"), this.$input.off(".tt"), this.$overflowHelper.remove(), this.$hint = this.$input = this.$overflowHelper = a("<div>");
            }
        }), c;
    }(), h = function() {
        "use strict";
        function c(c, e) {
            c = c || {}, c.templates = c.templates || {}, c.templates.notFound = c.templates.notFound || c.templates.empty, 
            c.source || a.error("missing source"), c.node || a.error("missing node"), c.name && !h(c.name) && a.error("invalid dataset name: " + c.name), 
            e.mixin(this), this.highlight = !!c.highlight, this.name = c.name || j(), this.limit = c.limit || 5, 
            this.displayFn = d(c.display || c.displayKey), this.templates = g(c.templates, this.displayFn), 
            this.source = c.source.__ttAdapter ? c.source.__ttAdapter() : c.source, this.async = b.isUndefined(c.async) ? this.source.length > 2 : !!c.async, 
            this._resetLastSuggestion(), this.$el = a(c.node).addClass(this.classes.dataset).addClass(this.classes.dataset + "-" + this.name);
        }
        function d(a) {
            function c(b) {
                return b[a];
            }
            return a = a || b.stringify, b.isFunction(a) ? a : c;
        }
        function g(c, d) {
            function e(b) {
                return a("<div>").text(d(b));
            }
            return {
                notFound: c.notFound && b.templatify(c.notFound),
                pending: c.pending && b.templatify(c.pending),
                header: c.header && b.templatify(c.header),
                footer: c.footer && b.templatify(c.footer),
                suggestion: c.suggestion || e
            };
        }
        function h(a) {
            return /^[_a-zA-Z0-9-]+$/.test(a);
        }
        var i, j;
        return i = {
            val: "tt-selectable-display",
            obj: "tt-selectable-object"
        }, j = b.getIdGenerator(), c.extractData = function(b) {
            var c = a(b);
            return c.data(i.obj) ? {
                val: c.data(i.val) || "",
                obj: c.data(i.obj) || null
            } : null;
        }, b.mixin(c.prototype, e, {
            _overwrite: function(a, b) {
                b = b || [], b.length ? this._renderSuggestions(a, b) : this.async && this.templates.pending ? this._renderPending(a) : !this.async && this.templates.notFound ? this._renderNotFound(a) : this._empty(), 
                this.trigger("rendered", this.name, b, !1);
            },
            _append: function(a, b) {
                b = b || [], b.length && this.$lastSuggestion.length ? this._appendSuggestions(a, b) : b.length ? this._renderSuggestions(a, b) : !this.$lastSuggestion.length && this.templates.notFound && this._renderNotFound(a), 
                this.trigger("rendered", this.name, b, !0);
            },
            _renderSuggestions: function(a, b) {
                var c;
                c = this._getSuggestionsFragment(a, b), this.$lastSuggestion = c.children().last(), 
                this.$el.html(c).prepend(this._getHeader(a, b)).append(this._getFooter(a, b));
            },
            _appendSuggestions: function(a, b) {
                var c, d;
                c = this._getSuggestionsFragment(a, b), d = c.children().last(), this.$lastSuggestion.after(c), 
                this.$lastSuggestion = d;
            },
            _renderPending: function(a) {
                var b = this.templates.pending;
                this._resetLastSuggestion(), b && this.$el.html(b({
                    query: a,
                    dataset: this.name
                }));
            },
            _renderNotFound: function(a) {
                var b = this.templates.notFound;
                this._resetLastSuggestion(), b && this.$el.html(b({
                    query: a,
                    dataset: this.name
                }));
            },
            _empty: function() {
                this.$el.empty(), this._resetLastSuggestion();
            },
            _getSuggestionsFragment: function(c, d) {
                var e, g = this;
                return e = document.createDocumentFragment(), b.each(d, function(b) {
                    var d, f;
                    f = g._injectQuery(c, b), d = a(g.templates.suggestion(f)).data(i.obj, b).data(i.val, g.displayFn(b)).addClass(g.classes.suggestion + " " + g.classes.selectable), 
                    e.appendChild(d[0]);
                }), this.highlight && f({
                    className: this.classes.highlight,
                    node: e,
                    pattern: c
                }), a(e);
            },
            _getFooter: function(a, b) {
                return this.templates.footer ? this.templates.footer({
                    query: a,
                    suggestions: b,
                    dataset: this.name
                }) : null;
            },
            _getHeader: function(a, b) {
                return this.templates.header ? this.templates.header({
                    query: a,
                    suggestions: b,
                    dataset: this.name
                }) : null;
            },
            _resetLastSuggestion: function() {
                this.$lastSuggestion = a();
            },
            _injectQuery: function(a, c) {
                return b.isObject(c) ? b.mixin({
                    _query: a
                }, c) : c;
            },
            update: function(b) {
                function c(a) {
                    g || (g = !0, a = (a || []).slice(0, e.limit), h = a.length, e._overwrite(b, a), 
                    h < e.limit && e.async && e.trigger("asyncRequested", b));
                }
                function d(c) {
                    c = c || [], !f && h < e.limit && (e.cancel = a.noop, h += c.length, e._append(b, c.slice(0, e.limit - h)), 
                    e.async && e.trigger("asyncReceived", b));
                }
                var e = this, f = !1, g = !1, h = 0;
                this.cancel(), this.cancel = function() {
                    f = !0, e.cancel = a.noop, e.async && e.trigger("asyncCanceled", b);
                }, this.source(b, c, d), !g && c([]);
            },
            cancel: a.noop,
            clear: function() {
                this._empty(), this.cancel(), this.trigger("cleared");
            },
            isEmpty: function() {
                return this.$el.is(":empty");
            },
            destroy: function() {
                this.$el = a("<div>");
            }
        }), c;
    }(), i = function() {
        "use strict";
        function c(c, d) {
            function e(b) {
                var c = f.$node.find(b.node).first();
                return b.node = c.length ? c : a("<div>").appendTo(f.$node), new h(b, d);
            }
            var f = this;
            c = c || {}, c.node || a.error("node is required"), d.mixin(this), this.$node = a(c.node), 
            this.query = null, this.datasets = b.map(c.datasets, e);
        }
        return b.mixin(c.prototype, e, {
            _onSelectableClick: function(b) {
                this.trigger("selectableClicked", a(b.currentTarget));
            },
            _onRendered: function(a, b, c, d) {
                this.$node.toggleClass(this.classes.empty, this._allDatasetsEmpty()), this.trigger("datasetRendered", b, c, d);
            },
            _onCleared: function() {
                this.$node.toggleClass(this.classes.empty, this._allDatasetsEmpty()), this.trigger("datasetCleared");
            },
            _propagate: function() {
                this.trigger.apply(this, arguments);
            },
            _allDatasetsEmpty: function() {
                function a(a) {
                    return a.isEmpty();
                }
                return b.every(this.datasets, a);
            },
            _getSelectables: function() {
                return this.$node.find(this.selectors.selectable);
            },
            _removeCursor: function() {
                var a = this.getActiveSelectable();
                a && a.removeClass(this.classes.cursor);
            },
            _ensureVisible: function(a) {
                var b, c, d, e;
                b = a.position().top, c = b + a.outerHeight(!0), d = this.$node.scrollTop(), e = this.$node.height() + parseInt(this.$node.css("paddingTop"), 10) + parseInt(this.$node.css("paddingBottom"), 10), 
                0 > b ? this.$node.scrollTop(d + b) : c > e && this.$node.scrollTop(d + (c - e));
            },
            bind: function() {
                var a, c = this;
                return a = b.bind(this._onSelectableClick, this), this.$node.on("click.tt", this.selectors.selectable, a), 
                b.each(this.datasets, function(a) {
                    a.onSync("asyncRequested", c._propagate, c).onSync("asyncCanceled", c._propagate, c).onSync("asyncReceived", c._propagate, c).onSync("rendered", c._onRendered, c).onSync("cleared", c._onCleared, c);
                }), this;
            },
            isOpen: function() {
                return this.$node.hasClass(this.classes.open);
            },
            open: function() {
                this.$node.addClass(this.classes.open);
            },
            close: function() {
                this.$node.removeClass(this.classes.open), this._removeCursor();
            },
            setLanguageDirection: function(a) {
                this.$node.attr("dir", a);
            },
            selectableRelativeToCursor: function(a) {
                var b, c, d, e;
                return c = this.getActiveSelectable(), b = this._getSelectables(), d = c ? b.index(c) : -1, 
                e = d + a, e = (e + 1) % (b.length + 1) - 1, e = -1 > e ? b.length - 1 : e, -1 === e ? null : b.eq(e);
            },
            setCursor: function(a) {
                this._removeCursor(), (a = a && a.first()) && (a.addClass(this.classes.cursor), 
                this._ensureVisible(a));
            },
            getSelectableData: function(a) {
                return a && a.length ? h.extractData(a) : null;
            },
            getActiveSelectable: function() {
                var a = this._getSelectables().filter(this.selectors.cursor).first();
                return a.length ? a : null;
            },
            getTopSelectable: function() {
                var a = this._getSelectables().first();
                return a.length ? a : null;
            },
            update: function(a) {
                function c(b) {
                    b.update(a);
                }
                var d = a !== this.query;
                return d && (this.query = a, b.each(this.datasets, c)), d;
            },
            empty: function() {
                function a(a) {
                    a.clear();
                }
                b.each(this.datasets, a), this.query = null, this.$node.addClass(this.classes.empty);
            },
            destroy: function() {
                function c(a) {
                    a.destroy();
                }
                this.$node.off(".tt"), this.$node = a("<div>"), b.each(this.datasets, c);
            }
        }), c;
    }(), j = function() {
        "use strict";
        function a() {
            i.apply(this, [].slice.call(arguments, 0));
        }
        var c = i.prototype;
        return b.mixin(a.prototype, i.prototype, {
            open: function() {
                return !this._allDatasetsEmpty() && this._show(), c.open.apply(this, [].slice.call(arguments, 0));
            },
            close: function() {
                return this._hide(), c.close.apply(this, [].slice.call(arguments, 0));
            },
            _onRendered: function() {
                return this._allDatasetsEmpty() ? this._hide() : this.isOpen() && this._show(), 
                c._onRendered.apply(this, [].slice.call(arguments, 0));
            },
            _onCleared: function() {
                return this._allDatasetsEmpty() ? this._hide() : this.isOpen() && this._show(), 
                c._onCleared.apply(this, [].slice.call(arguments, 0));
            },
            setLanguageDirection: function(a) {
                return this.$node.css("ltr" === a ? this.css.ltr : this.css.rtl), c.setLanguageDirection.apply(this, [].slice.call(arguments, 0));
            },
            _hide: function() {
                this.$node.hide();
            },
            _show: function() {
                this.$node.css("display", "block");
            }
        }), a;
    }(), k = function() {
        "use strict";
        function c(c, e) {
            var f, g, h, i, j, k, l, m, n, o, p;
            c = c || {}, c.input || a.error("missing input"), c.menu || a.error("missing menu"), 
            c.eventBus || a.error("missing event bus"), e.mixin(this), this.eventBus = c.eventBus, 
            this.minLength = b.isNumber(c.minLength) ? c.minLength : 1, this.input = c.input, 
            this.menu = c.menu, this.enabled = !0, this.active = !1, this.input.hasFocus() && this.activate(), 
            this.dir = this.input.getLangDir(), this._hacks(), this.menu.bind().onSync("selectableClicked", this._onSelectableClicked, this).onSync("asyncRequested", this._onAsyncRequested, this).onSync("asyncCanceled", this._onAsyncCanceled, this).onSync("asyncReceived", this._onAsyncReceived, this).onSync("datasetRendered", this._onDatasetRendered, this).onSync("datasetCleared", this._onDatasetCleared, this), 
            f = d(this, "activate", "open", "_onFocused"), g = d(this, "deactivate", "_onBlurred"), 
            h = d(this, "isActive", "isOpen", "_onEnterKeyed"), i = d(this, "isActive", "isOpen", "_onTabKeyed"), 
            j = d(this, "isActive", "_onEscKeyed"), k = d(this, "isActive", "open", "_onUpKeyed"), 
            l = d(this, "isActive", "open", "_onDownKeyed"), m = d(this, "isActive", "isOpen", "_onLeftKeyed"), 
            n = d(this, "isActive", "isOpen", "_onRightKeyed"), o = d(this, "_openIfActive", "_onQueryChanged"), 
            p = d(this, "_openIfActive", "_onWhitespaceChanged"), this.input.bind().onSync("focused", f, this).onSync("blurred", g, this).onSync("enterKeyed", h, this).onSync("tabKeyed", i, this).onSync("escKeyed", j, this).onSync("upKeyed", k, this).onSync("downKeyed", l, this).onSync("leftKeyed", m, this).onSync("rightKeyed", n, this).onSync("queryChanged", o, this).onSync("whitespaceChanged", p, this).onSync("langDirChanged", this._onLangDirChanged, this);
        }
        function d(a) {
            var c = [].slice.call(arguments, 1);
            return function() {
                var d = [].slice.call(arguments);
                b.each(c, function(b) {
                    return a[b].apply(a, d);
                });
            };
        }
        return b.mixin(c.prototype, {
            _hacks: function() {
                var c, d;
                c = this.input.$input || a("<div>"), d = this.menu.$node || a("<div>"), c.on("blur.tt", function(a) {
                    var e, f, g;
                    e = document.activeElement, f = d.is(e), g = d.has(e).length > 0, b.isMsie() && (f || g) && (a.preventDefault(), 
                    a.stopImmediatePropagation(), b.defer(function() {
                        c.focus();
                    }));
                }), d.on("mousedown.tt", function(a) {
                    a.preventDefault();
                });
            },
            _onSelectableClicked: function(a, b) {
                this.select(b);
            },
            _onDatasetCleared: function() {
                this._updateHint();
            },
            _onDatasetRendered: function(a, b, c, d) {
                this._updateHint(), this.eventBus.trigger("render", c, d, b);
            },
            _onAsyncRequested: function(a, b, c) {
                this.eventBus.trigger("asyncrequest", c, b);
            },
            _onAsyncCanceled: function(a, b, c) {
                this.eventBus.trigger("asynccancel", c, b);
            },
            _onAsyncReceived: function(a, b, c) {
                this.eventBus.trigger("asyncreceive", c, b);
            },
            _onFocused: function() {
                this._minLengthMet() && this.menu.update(this.input.getQuery());
            },
            _onBlurred: function() {
                this.input.hasQueryChangedSinceLastFocus() && this.eventBus.trigger("change", this.input.getQuery());
            },
            _onEnterKeyed: function(a, b) {
                var c;
                (c = this.menu.getActiveSelectable()) && this.select(c) && b.preventDefault();
            },
            _onTabKeyed: function(a, b) {
                var c;
                (c = this.menu.getActiveSelectable()) ? this.select(c) && b.preventDefault() : (c = this.menu.getTopSelectable()) && this.autocomplete(c) && b.preventDefault();
            },
            _onEscKeyed: function() {
                this.close();
            },
            _onUpKeyed: function() {
                this.moveCursor(-1);
            },
            _onDownKeyed: function() {
                this.moveCursor(1);
            },
            _onLeftKeyed: function() {
                "rtl" === this.dir && this.input.isCursorAtEnd() && this.autocomplete(this.menu.getTopSelectable());
            },
            _onRightKeyed: function() {
                "ltr" === this.dir && this.input.isCursorAtEnd() && this.autocomplete(this.menu.getTopSelectable());
            },
            _onQueryChanged: function(a, b) {
                this._minLengthMet(b) ? this.menu.update(b) : this.menu.empty();
            },
            _onWhitespaceChanged: function() {
                this._updateHint();
            },
            _onLangDirChanged: function(a, b) {
                this.dir !== b && (this.dir = b, this.menu.setLanguageDirection(b));
            },
            _openIfActive: function() {
                this.isActive() && this.open();
            },
            _minLengthMet: function(a) {
                return a = b.isString(a) ? a : this.input.getQuery() || "", a.length >= this.minLength;
            },
            _updateHint: function() {
                var a, c, d, e, f, h, i;
                a = this.menu.getTopSelectable(), c = this.menu.getSelectableData(a), d = this.input.getInputValue(), 
                !c || b.isBlankString(d) || this.input.hasOverflow() ? this.input.clearHint() : (e = g.normalizeQuery(d), 
                f = b.escapeRegExChars(e), h = new RegExp("^(?:" + f + ")(.+$)", "i"), i = h.exec(c.val), 
                i && this.input.setHint(d + i[1]));
            },
            isEnabled: function() {
                return this.enabled;
            },
            enable: function() {
                this.enabled = !0;
            },
            disable: function() {
                this.enabled = !1;
            },
            isActive: function() {
                return this.active;
            },
            activate: function() {
                return this.isActive() ? !0 : !this.isEnabled() || this.eventBus.before("active") ? !1 : (this.active = !0, 
                this.eventBus.trigger("active"), !0);
            },
            deactivate: function() {
                return this.isActive() ? this.eventBus.before("idle") ? !1 : (this.active = !1, 
                this.close(), this.eventBus.trigger("idle"), !0) : !0;
            },
            isOpen: function() {
                return this.menu.isOpen();
            },
            open: function() {
                return this.isOpen() || this.eventBus.before("open") || (this.menu.open(), this._updateHint(), 
                this.eventBus.trigger("open")), this.isOpen();
            },
            close: function() {
                return this.isOpen() && !this.eventBus.before("close") && (this.menu.close(), this.input.clearHint(), 
                this.input.resetInputValue(), this.eventBus.trigger("close")), !this.isOpen();
            },
            setVal: function(a) {
                this.input.setQuery(b.toStr(a));
            },
            getVal: function() {
                return this.input.getQuery();
            },
            select: function(a) {
                var b = this.menu.getSelectableData(a);
                return b && !this.eventBus.before("select", b.obj) ? (this.input.setQuery(b.val, !0), 
                this.eventBus.trigger("select", b.obj), this.close(), !0) : !1;
            },
            autocomplete: function(a) {
                var b, c, d;
                return b = this.input.getQuery(), c = this.menu.getSelectableData(a), d = c && b !== c.val, 
                d && !this.eventBus.before("autocomplete", c.obj) ? (this.input.setQuery(c.val), 
                this.eventBus.trigger("autocomplete", c.obj), !0) : !1;
            },
            moveCursor: function(a) {
                var b, c, d, e, f;
                return b = this.input.getQuery(), c = this.menu.selectableRelativeToCursor(a), d = this.menu.getSelectableData(c), 
                e = d ? d.obj : null, f = this._minLengthMet() && this.menu.update(b), f || this.eventBus.before("cursorchange", e) ? !1 : (this.menu.setCursor(c), 
                d ? this.input.setInputValue(d.val) : (this.input.resetInputValue(), this._updateHint()), 
                this.eventBus.trigger("cursorchange", e), !0);
            },
            destroy: function() {
                this.input.destroy(), this.menu.destroy();
            }
        }), c;
    }();
    !function() {
        "use strict";
        function e(b, c) {
            b.each(function() {
                var b, d = a(this);
                (b = d.data(p.typeahead)) && c(b, d);
            });
        }
        function f(a, b) {
            return a.clone().addClass(b.classes.hint).removeData().css(b.css.hint).css(l(a)).prop("readonly", !0).removeAttr("id name placeholder required").attr({
                autocomplete: "off",
                spellcheck: "false",
                tabindex: -1
            });
        }
        function h(a, b) {
            a.data(p.attrs, {
                dir: a.attr("dir"),
                autocomplete: a.attr("autocomplete"),
                spellcheck: a.attr("spellcheck"),
                style: a.attr("style")
            }), a.addClass(b.classes.input).attr({
                autocomplete: "off",
                spellcheck: !1
            });
            try {
                !a.attr("dir") && a.attr("dir", "auto");
            } catch (c) {}
            return a;
        }
        function l(a) {
            return {
                backgroundAttachment: a.css("background-attachment"),
                backgroundClip: a.css("background-clip"),
                backgroundColor: a.css("background-color"),
                backgroundImage: a.css("background-image"),
                backgroundOrigin: a.css("background-origin"),
                backgroundPosition: a.css("background-position"),
                backgroundRepeat: a.css("background-repeat"),
                backgroundSize: a.css("background-size")
            };
        }
        function m(a) {
            var c, d;
            c = a.data(p.www), d = a.parent().filter(c.selectors.wrapper), b.each(a.data(p.attrs), function(c, d) {
                b.isUndefined(c) ? a.removeAttr(d) : a.attr(d, c);
            }), a.removeData(p.typeahead).removeData(p.www).removeData(p.attr).removeClass(c.classes.input), 
            d.length && (a.detach().insertAfter(d), d.remove());
        }
        function n(c) {
            var d, e;
            return d = b.isJQuery(c) || b.isElement(c), e = d ? a(c).first() : [], e.length ? e : null;
        }
        var o, p, q;
        o = a.fn.typeahead, p = {
            www: "tt-www",
            attrs: "tt-attrs",
            typeahead: "tt-typeahead"
        }, q = {
            initialize: function(e, l) {
                function m() {
                    var c, m, q, r, s, t, u, v, w, x, y;
                    b.each(l, function(a) {
                        a.highlight = !!e.highlight;
                    }), c = a(this), m = a(o.html.wrapper), q = n(e.hint), r = n(e.menu), s = e.hint !== !1 && !q, 
                    t = e.menu !== !1 && !r, s && (q = f(c, o)), t && (r = a(o.html.menu).css(o.css.menu)), 
                    q && q.val(""), c = h(c, o), (s || t) && (m.css(o.css.wrapper), c.css(s ? o.css.input : o.css.inputWithNoHint), 
                    c.wrap(m).parent().prepend(s ? q : null).append(t ? r : null)), y = t ? j : i, u = new d({
                        el: c
                    }), v = new g({
                        hint: q,
                        input: c
                    }, o), w = new y({
                        node: r,
                        datasets: l
                    }, o), x = new k({
                        input: v,
                        menu: w,
                        eventBus: u,
                        minLength: e.minLength
                    }, o), c.data(p.www, o), c.data(p.typeahead, x);
                }
                var o;
                return l = b.isArray(l) ? l : [].slice.call(arguments, 1), e = e || {}, o = c(e.classNames), 
                this.each(m);
            },
            isEnabled: function() {
                var a;
                return e(this.first(), function(b) {
                    a = b.isEnabled();
                }), a;
            },
            enable: function() {
                return e(this, function(a) {
                    a.enable();
                }), this;
            },
            disable: function() {
                return e(this, function(a) {
                    a.disable();
                }), this;
            },
            isActive: function() {
                var a;
                return e(this.first(), function(b) {
                    a = b.isActive();
                }), a;
            },
            activate: function() {
                return e(this, function(a) {
                    a.activate();
                }), this;
            },
            deactivate: function() {
                return e(this, function(a) {
                    a.deactivate();
                }), this;
            },
            isOpen: function() {
                var a;
                return e(this.first(), function(b) {
                    a = b.isOpen();
                }), a;
            },
            open: function() {
                return e(this, function(a) {
                    a.open();
                }), this;
            },
            close: function() {
                return e(this, function(a) {
                    a.close();
                }), this;
            },
            select: function(b) {
                var c = !1, d = a(b);
                return e(this.first(), function(a) {
                    c = a.select(d);
                }), c;
            },
            autocomplete: function(b) {
                var c = !1, d = a(b);
                return e(this.first(), function(a) {
                    c = a.autocomplete(d);
                }), c;
            },
            moveCursor: function(a) {
                var b = !1;
                return e(this.first(), function(c) {
                    b = c.moveCursor(a);
                }), b;
            },
            val: function(a) {
                var b;
                return arguments.length ? (e(this, function(b) {
                    b.setVal(a);
                }), this) : (e(this.first(), function(a) {
                    b = a.getVal();
                }), b);
            },
            destroy: function() {
                return e(this, function(a, b) {
                    m(b), a.destroy();
                }), this;
            }
        }, a.fn.typeahead = function(a) {
            return q[a] ? q[a].apply(this, [].slice.call(arguments, 1)) : q.initialize.apply(this, arguments);
        }, a.fn.typeahead.noConflict = function() {
            return a.fn.typeahead = o, this;
        };
    }();
});

//var asl_typeahead = jQuery.fn.typeahead.noConflict();
/*Template js*/
console.log(asl_jQuery.templates);