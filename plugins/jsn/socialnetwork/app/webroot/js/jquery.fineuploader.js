/*!
* Fine Uploader
*
* Copyright 2013, Widen Enterprises, Inc. info@fineuploader.com
*
* Version: 4.3.1
*
* Homepage: http://fineuploader.com
*
* Repository: git://github.com/Widen/fine-uploader.git
*
* Licensed under GNU GPL v3, see LICENSE
*/


var qq = function(a) {
    "use strict";
    return {
        hide: function() {
            return a.style.display = "none", this
        },
        attach: function(b, c) {
            return a.addEventListener ? a.addEventListener(b, c, !1) : a.attachEvent && a.attachEvent("on" + b, c), function() {
                qq(a).detach(b, c)
            }
        },
        detach: function(b, c) {
            return a.removeEventListener ? a.removeEventListener(b, c, !1) : a.attachEvent && a.detachEvent("on" + b, c), this
        },
        contains: function(b) {
            return b ? a === b?!0 : a.contains ? a.contains(b) : !!(8 & b.compareDocumentPosition(a)) : !1
        },
        insertBefore: function(b) {
            return b.parentNode.insertBefore(a, b), this
        },
        remove: function() {
            return a.parentNode.removeChild(a), this
        },
        css: function(b) {
            if (null == a.style)
                throw new qq.Error("Can't apply style to node as it is not on the HTMLElement prototype chain!");
            return null != b.opacity && "string" != typeof a.style.opacity && "undefined" != typeof a.filters && (b.filter = "alpha(opacity=" + Math.round(100 * b.opacity) + ")"), qq.extend(a.style, b), this
        },
        hasClass: function(b) {
            var c = new RegExp("(^| )" + b + "( |$)");
            return c.test(a.className)
        },
        addClass: function(b) {
            return qq(a).hasClass(b) || (a.className += " " + b), this
        },
        removeClass: function(b) {
            var c = new RegExp("(^| )" + b + "( |$)");
            return a.className = a.className.replace(c, " ").replace(/^\s+|\s+$/g, ""), this
        },
        getByClass: function(b) {
            var c, d = [];
            return a.querySelectorAll ? a.querySelectorAll("." + b) : (c = a.getElementsByTagName("*"), qq.each(c, function(a, c) {
                qq(c).hasClass(b) && d.push(c)
            }), d)
        },
        children: function() {
            for (var b = [], c = a.firstChild; c;)
                1 === c.nodeType && b.push(c), c = c.nextSibling;
            return b
        },
        setText: function(b) {
            return a.innerText = b, a.textContent = b, this
        },
        clearText: function() {
            return qq(a).setText("")
        },
        hasAttribute: function(b) {
            var c;
            return a.hasAttribute ? a.hasAttribute(b) ? null == /^false$/i.exec(a.getAttribute(b)) : !1 : (c = a[b], void 0 === c?!1 : null == /^false$/i.exec(c))
        }
    }
};
!function() {
    "use strict";
    qq.log = function(a, b) {
        window.console && (b && "info" !== b ? window.console[b] ? window.console[b](a) : window.console.log("<" + b + "> " + a) : window.console.log(a))
    }, qq.isObject = function(a) {
        return a&&!a.nodeType && "[object Object]" === Object.prototype.toString.call(a)
    }, qq.isFunction = function(a) {
        return "function" == typeof a
    }, qq.isArray = function(a) {
        return "[object Array]" === Object.prototype.toString.call(a) || a && window.ArrayBuffer && a.buffer && a.buffer.constructor === ArrayBuffer
    }, qq.isItemList = function(a) {
        return "[object DataTransferItemList]" === Object.prototype.toString.call(a)
    }, qq.isNodeList = function(a) {
        return "[object NodeList]" === Object.prototype.toString.call(a) || a.item && a.namedItem
    }, qq.isString = function(a) {
        return "[object String]" === Object.prototype.toString.call(a)
    }, qq.trimStr = function(a) {
        return String.prototype.trim ? a.trim() : a.replace(/^\s+|\s+$/g, "")
    }, qq.format = function(a) {
        var b = Array.prototype.slice.call(arguments, 1), c = a, d = c.indexOf("{}");
        return qq.each(b, function(a, b) {
            var e = c.substring(0, d), f = c.substring(d + 2);
            return c = e + b + f, d = c.indexOf("{}", d + b.length), 0 > d?!1 : void 0
        }), c
    }, qq.isFile = function(a) {
        return window.File && "[object File]" === Object.prototype.toString.call(a)
    }, qq.isFileList = function(a) {
        return window.FileList && "[object FileList]" === Object.prototype.toString.call(a)
    }, qq.isFileOrInput = function(a) {
        return qq.isFile(a) || qq.isInput(a)
    }, qq.isInput = function(a, b) {
        var c = function(a) {
            var c = a.toLowerCase();
            return b ? "file" !== c : "file" === c
        };
        return window.HTMLInputElement && "[object HTMLInputElement]" === Object.prototype.toString.call(a) && a.type && c(a.type)?!0 : a.tagName && "input" === a.tagName.toLowerCase() && a.type && c(a.type)?!0 : !1
    }, qq.isBlob = function(a) {
        return window.Blob && "[object Blob]" === Object.prototype.toString.call(a)
    }, qq.isXhrUploadSupported = function() {
        var a = document.createElement("input");
        return a.type = "file", void 0 !== a.multiple && "undefined" != typeof File && "undefined" != typeof FormData && "undefined" != typeof qq.createXhrInstance().upload
    }, qq.createXhrInstance = function() {
        if (window.XMLHttpRequest)
            return new XMLHttpRequest;
        try {
            return new ActiveXObject("MSXML2.XMLHTTP.3.0")
        } catch (a) {
            return qq.log("Neither XHR or ActiveX are supported!", "error"), null
        }
    }, qq.isFolderDropSupported = function(a) {
        return a.items && a.items[0].webkitGetAsEntry
    }, qq.isFileChunkingSupported = function() {
        return !qq.android() && qq.isXhrUploadSupported() && (void 0 !== File.prototype.slice || void 0 !== File.prototype.webkitSlice || void 0 !== File.prototype.mozSlice)
    }, qq.sliceBlob = function(a, b, c) {
        var d = a.slice || a.mozSlice || a.webkitSlice;
        return d.call(a, b, c)
    }, qq.arrayBufferToHex = function(a) {
        var b = "", c = new Uint8Array(a);
        return qq.each(c, function(a, c) {
            var d = c.toString(16);
            d.length < 2 && (d = "0" + d), b += d
        }), b
    }, qq.readBlobToHex = function(a, b, c) {
        var d = qq.sliceBlob(a, b, b + c), e = new FileReader, f = new qq.Promise;
        return e.onload = function() {
            f.success(qq.arrayBufferToHex(e.result))
        }, e.readAsArrayBuffer(d), f
    }, qq.extend = function(a, b, c) {
        return qq.each(b, function(b, d) {
            c && qq.isObject(d) ? (void 0 === a[b] && (a[b] = {}), qq.extend(a[b], d, !0)) : a[b] = d
        }), a
    }, qq.override = function(a, b) {
        var c = {}, d = b(c);
        return qq.each(d, function(b, d) {
            void 0 !== a[b] && (c[b] = a[b]), a[b] = d
        }), a
    }, qq.indexOf = function(a, b, c) {
        if (a.indexOf)
            return a.indexOf(b, c);
        c = c || 0;
        var d = a.length;
        for (0 > c && (c += d); d > c; c += 1)
            if (a.hasOwnProperty(c) && a[c] === b)
                return c;
        return -1
    }, qq.getUniqueId = function() {
        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(a) {
            var b = 0 | 16 * Math.random(), c = "x" == a ? b: 8 | 3 & b;
            return c.toString(16)
        })
    }, qq.ie = function() {
        return -1 !== navigator.userAgent.indexOf("MSIE")
    }, qq.ie7 = function() {
        return -1 !== navigator.userAgent.indexOf("MSIE 7")
    }, qq.ie10 = function() {
        return -1 !== navigator.userAgent.indexOf("MSIE 10")
    }, qq.ie11 = function() {
        return -1 !== navigator.userAgent.indexOf("Trident")&&-1 !== navigator.userAgent.indexOf("rv:11")
    }, qq.safari = function() {
        return void 0 !== navigator.vendor&&-1 !== navigator.vendor.indexOf("Apple")
    }, qq.chrome = function() {
        return void 0 !== navigator.vendor&&-1 !== navigator.vendor.indexOf("Google")
    }, qq.opera = function() {
        return void 0 !== navigator.vendor&&-1 !== navigator.vendor.indexOf("Opera")
    }, qq.firefox = function() {
        return !qq.ie11()&&-1 !== navigator.userAgent.indexOf("Mozilla") && void 0 !== navigator.vendor && "" === navigator.vendor
    }, qq.windows = function() {
        return "Win32" === navigator.platform
    }, qq.android = function() {
        return -1 !== navigator.userAgent.toLowerCase().indexOf("android")
    }, qq.ios7 = function() {
        return qq.ios()&&-1 !== navigator.userAgent.indexOf(" OS 7_")
    }, qq.ios = function() {
        return -1 !== navigator.userAgent.indexOf("iPad")||-1 !== navigator.userAgent.indexOf("iPod")||-1 !== navigator.userAgent.indexOf("iPhone")
    }, qq.preventDefault = function(a) {
        a.preventDefault ? a.preventDefault() : a.returnValue=!1
    }, qq.toElement = function() {
        var a = document.createElement("div");
        return function(b) {
            a.innerHTML = b;
            var c = a.firstChild;
            return a.removeChild(c), c
        }
    }(), qq.each = function(a, b) {
        var c, d;
        if (a)
            if (window.Storage && a.constructor === window.Storage)
                for (c = 0; c < a.length && (d = b(a.key(c), a.getItem(a.key(c))), d!==!1); c++);
            else if (qq.isArray(a) || qq.isItemList(a) || qq.isNodeList(a))
                for (c = 0; c < a.length && (d = b(c, a[c]), d!==!1); c++);
            else if (qq.isString(a))
                for (c = 0; c < a.length && (d = b(c, a.charAt(c)), d!==!1); c++);
            else 
                for (c in a)
                    if (Object.prototype.hasOwnProperty.call(a, c) && (d = b(c, a[c]), d===!1))
                        break
    }, qq.bind = function(a, b) {
        if (qq.isFunction(a)) {
            var c = Array.prototype.slice.call(arguments, 2);
            return function() {
                var d = qq.extend([], c);
                return arguments.length && (d = d.concat(Array.prototype.slice.call(arguments))), a.apply(b, d)
            }
        }
        throw new Error("first parameter must be a function!")
    }, qq.obj2url = function(a, b, c) {
        var d = [], e = "&", f = function(a, c) {
            var e = b ? /\[\]$/.test(b) ? b: b + "[" + c + "]": c;
            "undefined" !== e && "undefined" !== c && d.push("object" == typeof a ? qq.obj2url(a, e, !0) : "[object Function]" === Object.prototype.toString.call(a) ? encodeURIComponent(e) + "=" + encodeURIComponent(a()) : encodeURIComponent(e) + "=" + encodeURIComponent(a))
        };
        return !c && b ? (e = /\?/.test(b) ? /\?$/.test(b) ? "" : "&" : "?", d.push(b), d.push(qq.obj2url(a))) : "[object Array]" === Object.prototype.toString.call(a) && "undefined" != typeof a ? qq.each(a, function(a, b) {
            f(b, a)
        }) : "undefined" != typeof a && null !== a && "object" == typeof a ? qq.each(a, function(a, b) {
            f(b, a)
        }) : d.push(encodeURIComponent(b) + "=" + encodeURIComponent(a)), b ? d.join(e) : d.join(e).replace(/^&/, "").replace(/%20/g, "+")
    }, qq.obj2FormData = function(a, b, c) {
        return b || (b = new FormData), qq.each(a, function(a, d) {
            a = c ? c + "[" + a + "]" : a, qq.isObject(d) ? qq.obj2FormData(d, b, a) : qq.isFunction(d) ? b.append(a, d()) : b.append(a, d)
        }), b
    }, qq.obj2Inputs = function(a, b) {
        var c;
        return b || (b = document.createElement("form")), qq.obj2FormData(a, {
            append: function(a, d) {
                c = document.createElement("input"), c.setAttribute("name", a), c.setAttribute("value", d), b.appendChild(c)
            }
        }), b
    }, qq.setCookie = function(a, b, c) {
        var d = new Date, e = "";
        c && (d.setTime(d.getTime() + 1e3 * 60 * 60 * 24 * c), e = "; expires=" + d.toGMTString()), document.cookie = a + "=" + b + e + "; path=/"
    }, qq.getCookie = function(a) {
        var b, c = a + "=", d = document.cookie.split(";");
        return qq.each(d, function(a, d) {
            for (var e = d; " " == e.charAt(0);)
                e = e.substring(1, e.length);
            return 0 === e.indexOf(c) ? (b = e.substring(c.length, e.length), !1) : void 0
        }), b
    }, qq.getCookieNames = function(a) {
        var b = document.cookie.split(";"), c = [];
        return qq.each(b, function(b, d) {
            d = qq.trimStr(d);
            var e = d.indexOf("=");
            d.match(a) && c.push(d.substr(0, e))
        }), c
    }, qq.deleteCookie = function(a) {
        qq.setCookie(a, "", -1)
    }, qq.areCookiesEnabled = function() {
        var a = 1e5 * Math.random(), b = "qqCookieTest:" + a;
        return qq.setCookie(b, 1), qq.getCookie(b) ? (qq.deleteCookie(b), !0) : !1
    }, qq.parseJson = function(json) {
        return window.JSON && qq.isFunction(JSON.parse) ? JSON.parse(json) : eval("(" + json + ")")
    }, qq.getExtension = function(a) {
        var b = a.lastIndexOf(".") + 1;
        return b > 0 ? a.substr(b, a.length - b) : void 0
    }, qq.getFilename = function(a) {
        return qq.isInput(a) ? a.value.replace(/.*(\/|\\)/, "") : qq.isFile(a) && null !== a.fileName && void 0 !== a.fileName ? a.fileName : a.name
    }, qq.DisposeSupport = function() {
        var a = [];
        return {
            dispose: function() {
                var b;
                do 
                    b = a.shift(), b && b();
                while (b)
                }, attach: function() {
                var a = arguments;
                this.addDisposer(qq(a[0]).attach.apply(this, Array.prototype.slice.call(arguments, 1)))
            }, addDisposer: function(b) {
                a.push(b)
            }
        }
    }
}(), function() {
    "use strict";
    qq.Error = function(a) {
        this.message = "[Fine Uploader " + qq.version + "] " + a
    }, qq.Error.prototype = new Error
}(), qq.version = "4.3.1", qq.supportedFeatures = function() {
    "use strict";
    function a() {
        var a, b=!0;
        try {
            a = document.createElement("input"), a.type = "file", qq(a).hide(), a.disabled && (b=!1)
        } catch (c) {
            b=!1
        }
        return b
    }
    function b() {
        return (qq.chrome() || qq.opera()) && void 0 !== navigator.userAgent.match(/Chrome\/[2][1-9]|Chrome\/[3-9][0-9]/)
    }
    function c() {
        return (qq.chrome() || qq.opera()) && void 0 !== navigator.userAgent.match(/Chrome\/[1][4-9]|Chrome\/[2-9][0-9]/)
    }
    function d() {
        if (window.XMLHttpRequest) {
            var a = qq.createXhrInstance();
            return void 0 !== a.withCredentials
        }
        return !1
    }
    function e() {
        return void 0 !== window.XDomainRequest
    }
    function f() {
        return d()?!0 : e()
    }
    function g() {
        return void 0 !== document.createElement("input").webkitdirectory
    }
    var h, i, j, k, l, m, n, o, p, q, r, s;
    return h = a(), i = h && qq.isXhrUploadSupported(), j = i && b(), k = i && qq.isFileChunkingSupported(), l = i && k && qq.areCookiesEnabled(), m = i && c(), n = h && (void 0 !== window.postMessage || i), p = d(), o = e(), q = f(), r = g(), s = i && void 0 !== window.FileReader, {
        uploading: h, ajaxUploading: i, fileDrop: i, folderDrop: j, chunking: k, resume: l, uploadCustomHeaders: i, uploadNonMultipart: i, itemSizeValidation: i, uploadViaPaste: m, progressBar: i, uploadCors: n, deleteFileCorsXhr: p, deleteFileCorsXdr: o, deleteFileCors: q, canDetermineSize: i, folderSelection: r, imagePreviews: s, imageValidation: s, pause: k
    }
}(), qq.Promise = function() {
    "use strict";
    var a, b, c = [], d = [], e = [], f = 0;
    qq.extend(this, {
        then: function(e, g) {
            return 0 === f ? (e && c.push(e), g && d.push(g)) : -1 === f ? g && g.apply(null, b) : e && e.apply(null, a), this
        },
        done: function(c) {
            return 0 === f ? e.push(c) : c.apply(null, void 0 === b ? a : b), this
        },
        success: function() {
            return f = 1, a = arguments, c.length && qq.each(c, function(b, c) {
                c.apply(null, a)
            }), e.length && qq.each(e, function(b, c) {
                c.apply(null, a)
            }), this
        },
        failure: function() {
            return f =- 1, b = arguments, d.length && qq.each(d, function(a, c) {
                c.apply(null, b)
            }), e.length && qq.each(e, function(a, c) {
                c.apply(null, b)
            }), this
        }
    })
}, qq.UploadButton = function(a) {
    "use strict";
    function b() {
        var a = document.createElement("input");
        return a.setAttribute(qq.UploadButton.BUTTON_ID_ATTR_NAME, d), f.multiple && a.setAttribute("multiple", ""), f.folders && qq.supportedFeatures.folderSelection && a.setAttribute("webkitdirectory", ""), f.acceptFiles && a.setAttribute("accept", f.acceptFiles), a.setAttribute("type", "file"), a.setAttribute("name", f.name), qq(a).css({
            position: "absolute",
            right: 0,
            top: 0,
            fontFamily: "Arial",
            fontSize: "118px",
            margin: 0,
            padding: 0,
            cursor: "pointer",
            opacity: 0
        }), f.element.appendChild(a), e.attach(a, "change", function() {
            f.onChange(a)
        }), e.attach(a, "mouseover", function() {
            qq(f.element).addClass(f.hoverClass)
        }), e.attach(a, "mouseout", function() {
            qq(f.element).removeClass(f.hoverClass)
        }), e.attach(a, "focus", function() {
            qq(f.element).addClass(f.focusClass)
        }), e.attach(a, "blur", function() {
            qq(f.element).removeClass(f.focusClass)
        }), window.attachEvent && a.setAttribute("tabIndex", "-1"), a
    }
    var c, d, e = new qq.DisposeSupport, f = {
        element: null,
        multiple: !1,
        acceptFiles: null,
        folders: !1,
        name: "qqfile",
        onChange: function() {},
        hoverClass: "qq-upload-button-hover",
        focusClass: "qq-upload-button-focus"
    };
    qq.extend(f, a), d = qq.getUniqueId(), qq(f.element).css({
        position: "relative",
        overflow: "hidden",
        direction: "ltr"
    }), c = b(), qq.extend(this, {
        getInput: function() {
            return c
        },
        getButtonId: function() {
            return d
        },
        setMultiple: function(a) {
            a !== f.multiple && (a ? c.setAttribute("multiple", "") : c.removeAttribute("multiple"))
        },
        setAcceptFiles: function(a) {
            a !== f.acceptFiles && c.setAttribute("accept", a)
        },
        reset: function() {
            c.parentNode && qq(c).remove(), qq(f.element).removeClass(f.focusClass), c = b()
        }
    })
}, qq.UploadButton.BUTTON_ID_ATTR_NAME = "qq-button-id", qq.UploadData = function(a) {
    "use strict";
    function b(a) {
        if (qq.isArray(a)) {
            var b = [];
            return qq.each(a, function(a, c) {
                b.push(e[c])
            }), b
        }
        return e[a]
    }
    function c(a) {
        if (qq.isArray(a)) {
            var b = [];
            return qq.each(a, function(a, c) {
                b.push(e[f[c]])
            }), b
        }
        return e[f[a]]
    }
    function d(a) {
        var b = [], c = [].concat(a);
        return qq.each(c, function(a, c) {
            var d = g[c];
            void 0 !== d && qq.each(d, function(a, c) {
                b.push(e[c])
            })
        }), b
    }
    var e = [], f = {}, g = {};
    qq.extend(this, {
        addFile: function(b, c, d, h) {
            h = h || qq.status.SUBMITTING;
            var i = e.push({
                name: c,
                originalName: c,
                uuid: b,
                size: d,
                status: h
            })-1;
            return e[i].id = i, f[b] = i, void 0 === g[h] && (g[h] = []), g[h].push(i), a.onStatusChange(i, null, h), i
        },
        retrieve: function(a) {
            return qq.isObject(a) && e.length ? void 0 !== a.id ? b(a.id) : void 0 !== a.uuid ? c(a.uuid) : a.status ? d(a.status) : void 0 : qq.extend([], e, !0)
        },
        reset: function() {
            e = [], f = {}, g = {}
        },
        setStatus: function(b, c) {
            var d = e[b].status, f = qq.indexOf(g[d], b);
            g[d].splice(f, 1), e[b].status = c, void 0 === g[c] && (g[c] = []), g[c].push(b), a.onStatusChange(b, d, c)
        },
        uuidChanged: function(a, b) {
            var c = e[a].uuid;
            e[a].uuid = b, f[b] = a, delete f[c]
        },
        updateName: function(a, b) {
            e[a].name = b
        }
    })
}, qq.status = {
    SUBMITTING: "submitting",
    SUBMITTED: "submitted",
    REJECTED: "rejected",
    QUEUED: "queued",
    CANCELED: "canceled",
    PAUSED: "paused",
    UPLOADING: "uploading",
    UPLOAD_RETRYING: "retrying upload",
    UPLOAD_SUCCESSFUL: "upload successful",
    UPLOAD_FAILED: "upload failed",
    DELETE_FAILED: "delete failed",
    DELETING: "deleting",
    DELETED: "deleted"
}, function() {
    "use strict";
    qq.basePublicApi = {
        log: function(a, b) {
            !this._options.debug || b && "info" !== b ? b && "info" !== b && qq.log("[Fine Uploader " + qq.version + "] " + a, b) : qq.log("[Fine Uploader " + qq.version + "] " + a)
        },
        setParams: function(a, b) {
            this._paramsStore.set(a, b)
        },
        setDeleteFileParams: function(a, b) {
            this._deleteFileParamsStore.set(a, b)
        },
        setEndpoint: function(a, b) {
            this._endpointStore.set(a, b)
        },
        getInProgress: function() {
            return this._uploadData.retrieve({
                status: [qq.status.UPLOADING, qq.status.UPLOAD_RETRYING, qq.status.QUEUED]
            }).length
        },
        getNetUploads: function() {
            return this._netUploaded
        },
        uploadStoredFiles: function() {
            var a;
            if (0 === this._storedIds.length)
                this._itemError("noFilesError");
            else 
                for (; this._storedIds.length;)
                    a = this._storedIds.shift(), this._uploadFile(a)
        },
        clearStoredFiles: function() {
            this._storedIds = []
        },
        retry: function(a) {
            return this._manualRetry(a)
        },
        cancel: function(a) {
            this._handler.cancel(a)
        },
        cancelAll: function() {
            var a = [], b = this;
            qq.extend(a, this._storedIds), qq.each(a, function(a, c) {
                b.cancel(c)
            }), this._handler.cancelAll()
        },
        reset: function() {
            this.log("Resetting uploader..."), this._handler.reset(), this._storedIds = [], this._autoRetries = [], this._retryTimeouts = [], this._preventRetries = [], this._thumbnailUrls = [], qq.each(this._buttons, function(a, b) {
                b.reset()
            }), this._paramsStore.reset(), this._endpointStore.reset(), this._netUploadedOrQueued = 0, this._netUploaded = 0, this._uploadData.reset(), this._buttonIdsForFileIds = [], this._pasteHandler && this._pasteHandler.reset(), this._options.session.refreshOnReset && this._refreshSessionData(), this._succeededSinceLastAllComplete = [], this._failedSinceLastAllComplete = []
        },
        addFiles: function(a, b, c) {
            var d, e, f, g = [];
            if (a) {
                for (qq.isFileList(a) || (a = [].concat(a)), d = 0; d < a.length; d += 1)
                    if (e = a[d], qq.isFileOrInput(e)
                        )if (qq.isInput(e) && qq.supportedFeatures.ajaxUploading)
                    for (f = 0; f < e.files.length; f++)
                        this._handleNewFile(e.files[f], g);
                else 
                    this._handleNewFile(e, g);
                else 
                    this.log(e + " is not a File or INPUT element!  Ignoring!", "warn");
                this.log("Received " + g.length + " files or inputs."), this._prepareItemsForUpload(g, b, c)
            }
        },
        addBlobs: function(a, b, c) {
            if (a) {
                var d = [].concat(a), e = [], f = this;
                qq.each(d, function(a, b) {
                    var c;
                    qq.isBlob(b)&&!qq.isFileOrInput(b) ? c = {
                        blob: b,
                        name: f._options.blobs.defaultName
                    } : qq.isObject(b) && b.blob && b.name ? c = b : f.log("addBlobs: entry at index " + a + " is not a Blob or a BlobData object", "error"), c && f._handleNewFile(c, e)
                }), this._prepareItemsForUpload(e, b, c)
            } else 
                this.log("undefined or non-array parameter passed into addBlobs", "error")
        },
        getUuid: function(a) {
            return this._uploadData.retrieve({
                id: a
            }).uuid
        },
        setUuid: function(a, b) {
            return this._uploadData.uuidChanged(a, b)
        },
        getResumableFilesData: function() {
            return this._handler.getResumableFilesData()
        },
        getSize: function(a) {
            return this._uploadData.retrieve({
                id: a
            }).size
        },
        getName: function(a) {
            return this._uploadData.retrieve({
                id: a
            }).name
        },
        setName: function(a, b) {
            this._uploadData.updateName(a, b)
        },
        getFile: function(a) {
            return this._handler.getFile(a)
        },
        deleteFile: function(a) {
            return this._onSubmitDelete(a)
        },
        setDeleteFileEndpoint: function(a, b) {
            this._deleteFileEndpointStore.set(a, b)
        },
        doesExist: function(a) {
            return this._handler.isValid(a)
        },
        getUploads: function(a) {
            return this._uploadData.retrieve(a)
        },
        getButton: function(a) {
            return this._getButton(this._buttonIdsForFileIds[a])
        },
        drawThumbnail: function(a, b, c, d) {
            if (this._imageGenerator) {
                var e = this._thumbnailUrls[a], f = {
                    scale: c > 0,
                    maxSize: c > 0 ? c: null
                };
                return !d && qq.supportedFeatures.imagePreviews && (e = this.getFile(a)), null == e ? (new qq.Promise).failure(b, "File or URL not found.") : this._imageGenerator.generate(e, b, f)
            }
        },
        pauseUpload: function(a) {
            var b = this._uploadData.retrieve({
                id: a
            });
            if (!qq.supportedFeatures.pause ||!this._options.chunking.enabled)
                return !1;
            if (qq.indexOf([qq.status.UPLOADING, qq.status.UPLOAD_RETRYING], b.status) >= 0) {
                if (this._handler.pause(a))
                    return this._uploadData.setStatus(a, qq.status.PAUSED), !0;
                qq.log(qq.format("Unable to pause file ID {} ({}).", a, this.getName(a)), "error")
            } else 
                qq.log(qq.format("Ignoring pause for file ID {} ({}).  Not in progress.", a, this.getName(a)), "error");
            return !1
        },
        continueUpload: function(a) {
            var b = this._uploadData.retrieve({
                id: a
            });
            return qq.supportedFeatures.pause && this._options.chunking.enabled ? b.status === qq.status.PAUSED ? (qq.log(qq.format("Paused file ID {} ({}) will be continued.  Not paused.", a, this.getName(a))), this._uploadFile(a), !0) : (qq.log(qq.format("Ignoring continue for file ID {} ({}).  Not paused.", a, this.getName(a)), "error"), !1) : !1
        },
        getRemainingAllowedItems: function() {
            var a = this._options.validation.itemLimit;
            return a > 0 ? this._options.validation.itemLimit - this._netUploadedOrQueued : null
        }
    }, qq.basePrivateApi = {
        _initFormSupportAndParams: function() {
            this._formSupport = qq.FormSupport && new qq.FormSupport(this._options.form, qq.bind(this.uploadStoredFiles, this), qq.bind(this.log, this)), this._formSupport && this._formSupport.attachedToForm ? (this._paramsStore = this._createStore(this._options.request.params, this._formSupport.getFormInputsAsObject), this._options.autoUpload = this._formSupport.newAutoUpload, this._formSupport.newEndpoint && (this._options.request.endpoint = this._formSupport.newEndpoint)) : this._paramsStore = this._createStore(this._options.request.params)
        },
        _uploadFile: function(a) {
            this._handler.upload(a) || this._uploadData.setStatus(a, qq.status.QUEUED)
        },
        _refreshSessionData: function() {
            var a = this, b = this._options.session;
            qq.Session && null != this._options.session.endpoint && (this._session || (qq.extend(b, this._options.cors), b.log = qq.bind(this.log, this), b.addFileRecord = qq.bind(this._addCannedFile, this), this._session = new qq.Session(b)), setTimeout(function() {
                a._session.refresh().then(function(b, c) {
                    a._options.callbacks.onSessionRequestComplete(b, !0, c)
                }, function(b, c) {
                    a._options.callbacks.onSessionRequestComplete(b, !1, c)
                })
            }, 0))
        },
        _addCannedFile: function(a) {
            var b = this._uploadData.addFile(a.uuid, a.name, a.size, qq.status.UPLOAD_SUCCESSFUL);
            return a.deleteFileEndpoint && this.setDeleteFileEndpoint(a.deleteFileEndpoint, b), a.deleteFileParams && this.setDeleteFileParams(a.deleteFileParams, b), a.thumbnailUrl && (this._thumbnailUrls[b] = a.thumbnailUrl), this._netUploaded++, this._netUploadedOrQueued++, b
        },
        _handleNewFile: function(a, b) {
            var c, d =- 1, e = qq.getUniqueId(), f = qq.getFilename(a);
            a.size >= 0 ? d = a.size : a.blob && (d = a.blob.size), c = this._uploadData.addFile(e, f, d), this._handler.add(c, a), this._trackButton(c), this._netUploadedOrQueued++, b.push({
                id: c,
                file: a
            })
        },
        _trackButton: function(a) {
            var b;
            b = qq.supportedFeatures.ajaxUploading ? this._handler.getFile(a).qqButtonId : this._getButtonId(this._handler.getInput(a)), b && (this._buttonIdsForFileIds[a] = b)
        },
        _generateExtraButtonSpecs: function() {
            var a = this;
            this._extraButtonSpecs = {}, qq.each(this._options.extraButtons, function(b, c) {
                var d = c.multiple, e = qq.extend({}, a._options.validation, !0), f = qq.extend({}, c);
                void 0 === d && (d = a._options.multiple), f.validation && qq.extend(e, c.validation, !0), qq.extend(f, {
                    multiple: d,
                    validation: e
                }, !0), a._initExtraButton(f)
            })
        },
        _initExtraButton: function(a) {
            var b = this._createUploadButton({
                element: a.element,
                multiple: a.multiple,
                accept: a.validation.acceptFiles,
                folders: a.folders,
                allowedExtensions: a.validation.allowedExtensions
            });
            this._extraButtonSpecs[b.getButtonId()] = a
        },
        _getButtonId: function(a) {
            var b, c;
            if (a&&!a.blob&&!qq.isBlob(a)) {
                if (qq.isFile(a))
                    return a.qqButtonId;
                if ("input" === a.tagName.toLowerCase() && "file" === a.type.toLowerCase())
                    return a.getAttribute(qq.UploadButton.BUTTON_ID_ATTR_NAME);
                if (b = a.getElementsByTagName("input"), qq.each(b, function(a, b) {
                    return "file" === b.getAttribute("type") ? (c = b, !1) : void 0
                }), c)return c.getAttribute(qq.UploadButton.BUTTON_ID_ATTR_NAME)
            }
        },
        _annotateWithButtonId: function(a, b) {
            qq.isFile(a) && (a.qqButtonId = this._getButtonId(b))
        },
        _getButton: function(a) {
            var b = this._extraButtonSpecs[a];
            return b ? b.element : a === this._defaultButtonId ? this._options.button : void 0
        },
        _handleCheckedCallback: function(a) {
            var b = this, c = a.callback();
            return c instanceof qq.Promise ? (this.log(a.name + " - waiting for " + a.name + " promise to be fulfilled for " + a.identifier), c.then(function(c) {
                b.log(a.name + " promise success for " + a.identifier), a.onSuccess(c)
            }, function() {
                a.onFailure ? (b.log(a.name + " promise failure for " + a.identifier), a.onFailure()) : b.log(a.name + " promise failure for " + a.identifier)
            })): (c!==!1 ? a.onSuccess(c): a.onFailure ? (this.log(a.name + " - return value was 'false' for " + a.identifier + ".  Invoking failure callback."),
            a.onFailure()): this.log(a.name + " - return value was 'false' for " + a.identifier + ".  Will not proceed."),
            c)
        }, _createUploadButton: function(a) {
            function b() {
                return qq.supportedFeatures.ajaxUploading ? qq.ios7() && c._isAllowedExtension(e, ".mov")?!1 : void 0 === a.multiple ? c._options.multiple : a.multiple : !1
            }
            var c = this, d = a.accept || this._options.validation.acceptFiles, e = a.allowedExtensions || this._options.validation.allowedExtensions, f = new qq.UploadButton({
                element: a.element,
                folders: a.folders,
                name: this._options.request.inputName,
                multiple: b(),
                acceptFiles: d,
                onChange: function(a) {
                    c._onInputChange(a)
                },
                hoverClass: this._options.classes.buttonHover,
                focusClass: this._options.classes.buttonFocus
            });
            return this._disposeSupport.addDisposer(function() {
                f.dispose()
            }), c._buttons.push(f), f
        }, _createUploadHandler: function(a, b) {
            var c = this, d = {
                debug: this._options.debug,
                maxConnections: this._options.maxConnections,
                cors: this._options.cors,
                demoMode: this._options.demoMode,
                paramsStore: this._paramsStore,
                endpointStore: this._endpointStore,
                chunking: this._options.chunking,
                resume: this._options.resume,
                blobs: this._options.blobs,
                log: qq.bind(c.log, c),
                onProgress: function(a, b, d, e) {
                    c._onProgress(a, b, d, e), c._options.callbacks.onProgress(a, b, d, e)
                },
                onComplete: function(a, b, d, e) {
                    var f = c._onComplete(a, b, d, e);
                    f instanceof qq.Promise ? f.done(function() {
                        c._options.callbacks.onComplete(a, b, d, e)
                    }) : c._options.callbacks.onComplete(a, b, d, e)
                },
                onCancel: function(a, b) {
                    return c._handleCheckedCallback({
                        name: "onCancel",
                        callback: qq.bind(c._options.callbacks.onCancel, c, a, b),
                        onSuccess: qq.bind(c._onCancel, c, a, b),
                        identifier: a
                    })
                },
                onUpload: function(a, b) {
                    c._onUpload(a, b), c._options.callbacks.onUpload(a, b)
                },
                onUploadChunk: function(a, b, d) {
                    c._onUploadChunk(a, d), c._options.callbacks.onUploadChunk(a, b, d)
                },
                onUploadChunkSuccess: function() {
                    c._options.callbacks.onUploadChunkSuccess.apply(c, arguments)
                },
                onResume: function(a, b, d) {
                    return c._options.callbacks.onResume(a, b, d)
                },
                onAutoRetry: function() {
                    return c._onAutoRetry.apply(c, arguments)
                },
                onUuidChanged: function(a, b) {
                    c.log("Server requested UUID change from '" + c.getUuid(a) + "' to '" + b + "'"), c.setUuid(a, b)
                },
                getName: qq.bind(c.getName, c),
                getUuid: qq.bind(c.getUuid, c),
                getSize: qq.bind(c.getSize, c)
            };
            return qq.each(this._options.request, function(a, b) {
                d[a] = b
            }), a && qq.each(a, function(a, b) {
                d[a] = b
            }), new qq.UploadHandler(d, b)
        }, _createDeleteHandler: function() {
            var a = this;
            return new qq.DeleteFileAjaxRequester({
                method: this._options.deleteFile.method.toUpperCase(),
                maxConnections: this._options.maxConnections,
                uuidParamName: this._options.request.uuidName,
                customHeaders: this._options.deleteFile.customHeaders,
                paramsStore: this._deleteFileParamsStore,
                endpointStore: this._deleteFileEndpointStore,
                demoMode: this._options.demoMode,
                cors: this._options.cors,
                log: qq.bind(a.log, a),
                onDelete: function(b) {
                    a._onDelete(b), a._options.callbacks.onDelete(b)
                },
                onDeleteComplete: function(b, c, d) {
                    a._onDeleteComplete(b, c, d), a._options.callbacks.onDeleteComplete(b, c, d)
                }
            })
        }, _createPasteHandler: function() {
            var a = this;
            return new qq.PasteSupport({
                targetElement: this._options.paste.targetElement,
                callbacks: {
                    log: qq.bind(a.log, a),
                    pasteReceived: function(b) {
                        a._handleCheckedCallback({
                            name: "onPasteReceived",
                            callback: qq.bind(a._options.callbacks.onPasteReceived, a, b),
                            onSuccess: qq.bind(a._handlePasteSuccess, a, b),
                            identifier: "pasted image"
                        })
                    }
                }
            })
        }, _createUploadDataTracker: function() {
            var a = this;
            return new qq.UploadData({
                getName: function(b) {
                    return a.getName(b)
                },
                getUuid: function(b) {
                    return a.getUuid(b)
                },
                getSize: function(b) {
                    return a.getSize(b)
                },
                onStatusChange: function(b, c, d) {
                    a._onUploadStatusChange(b, c, d), a._options.callbacks.onStatusChange(b, c, d), a._maybeAllComplete(b, d)
                }
            })
        }, _onUploadStatusChange: function(a, b, c) {
            c === qq.status.PAUSED && clearTimeout(this._retryTimeouts[a])
        }, _handlePasteSuccess: function(a, b) {
            var c = a.type.split("/")[1], d = b;
            null == d && (d = this._options.paste.defaultName), d += "." + c, this.addBlobs({
                name: d,
                blob: a
            })
        }, _preventLeaveInProgress: function() {
            var a = this;
            this._disposeSupport.attach(window, "beforeunload", function(b) {
                return a.getInProgress() ? (b = b || window.event, b.returnValue = a._options.messages.onLeave, a._options.messages.onLeave) : void 0
            })
        }, _onSubmit: function() {}, _onProgress: function() {}, _onComplete: function(a, b, c, d) {
            return c.success ? (c.thumbnailUrl && (this._thumbnailUrls[a] = c.thumbnailUrl), this._netUploaded++, this._uploadData.setStatus(a, qq.status.UPLOAD_SUCCESSFUL)) : (this._netUploadedOrQueued--, this._uploadData.setStatus(a, qq.status.UPLOAD_FAILED)), this._maybeParseAndSendUploadError(a, b, c, d), c.success?!0 : !1
        }, _maybeAllComplete: function(a, b) {
            var c = this, d = this._uploadData.retrieve({
                status: [qq.status.UPLOADING, qq.status.UPLOAD_RETRYING, qq.status.QUEUED, qq.status.SUBMITTING, qq.status.SUBMITTED, qq.status.PAUSED]
            }).length;
            b === qq.status.UPLOAD_SUCCESSFUL ? this._succeededSinceLastAllComplete.push(a) : b === qq.status.UPLOAD_FAILED && this._failedSinceLastAllComplete.push(a), 0 === d && (this._succeededSinceLastAllComplete.length || this._failedSinceLastAllComplete.length) && setTimeout(function() {
                c._options.callbacks.onAllComplete(qq.extend([], c._succeededSinceLastAllComplete), qq.extend([], c._failedSinceLastAllComplete)), c._succeededSinceLastAllComplete = [], c._failedSinceLastAllComplete = []
            }, 0)
        }, _onCancel: function(a) {
            this._netUploadedOrQueued--, clearTimeout(this._retryTimeouts[a]);
            var b = qq.indexOf(this._storedIds, a);
            !this._options.autoUpload && b >= 0 && this._storedIds.splice(b, 1), this._uploadData.setStatus(a, qq.status.CANCELED)
        }, _isDeletePossible: function() {
            return qq.DeleteFileAjaxRequester && this._options.deleteFile.enabled ? this._options.cors.expected ? qq.supportedFeatures.deleteFileCorsXhr?!0 : qq.supportedFeatures.deleteFileCorsXdr && this._options.cors.allowXdr?!0 : !1 : !0 : !1
        }, _onSubmitDelete: function(a, b, c) {
            var d, e = this.getUuid(a);
            return b && (d = qq.bind(b, this, a, e, c)), this._isDeletePossible() ? (this._handleCheckedCallback({
                name: "onSubmitDelete",
                callback: qq.bind(this._options.callbacks.onSubmitDelete, this, a),
                onSuccess: d || qq.bind(this._deleteHandler.sendDelete, this, a, e, c),
                identifier: a
            }), !0) : (this.log("Delete request ignored for ID " + a + ", delete feature is disabled or request not possible " + "due to CORS on a user agent that does not support pre-flighting.", "warn"), !1)
        }, _onDelete: function(a) {
            this._uploadData.setStatus(a, qq.status.DELETING)
        }, _onDeleteComplete: function(a, b, c) {
            var d = this.getName(a);
            c ? (this._uploadData.setStatus(a, qq.status.DELETE_FAILED), this.log("Delete request for '" + d + "' has failed.", "error"), void 0 === b.withCredentials ? this._options.callbacks.onError(a, d, "Delete request failed", b) : this._options.callbacks.onError(a, d, "Delete request failed with response code " + b.status, b)) : (this._netUploadedOrQueued--, this._netUploaded--, this._handler.expunge(a), this._uploadData.setStatus(a, qq.status.DELETED), this.log("Delete request for '" + d + "' has succeeded."))
        }, _onUpload: function(a) {
            this._uploadData.setStatus(a, qq.status.UPLOADING)
        }, _onUploadChunk: function() {}, _onInputChange: function(a) {
            var b;
            if (qq.supportedFeatures.ajaxUploading) {
                for (b = 0; b < a.files.length; b++)
                    this._annotateWithButtonId(a.files[b], a);
                this.addFiles(a.files)
            } else 
                a.value.length > 0 && this.addFiles(a);
            qq.each(this._buttons, function(a, b) {
                b.reset()
            })
        }, _onBeforeAutoRetry: function(a, b) {
            this.log("Waiting " + this._options.retry.autoAttemptDelay + " seconds before retrying " + b + "...")
        }, _onAutoRetry: function(a, b, c, d, e) {
            var f = this;
            return f._preventRetries[a] = c[f._options.retry.preventRetryResponseProperty], f._shouldAutoRetry(a, b, c) ? (f._maybeParseAndSendUploadError.apply(f, arguments), f._options.callbacks.onAutoRetry(a, b, f._autoRetries[a] + 1), f._onBeforeAutoRetry(a, b), f._retryTimeouts[a] = setTimeout(function() {
                f.log("Retrying " + b + "..."), f._autoRetries[a]++, f._uploadData.setStatus(a, qq.status.UPLOAD_RETRYING), e ? e(a) : f._handler.retry(a)
            }, 1e3 * f._options.retry.autoAttemptDelay), !0) : void 0
        }, _shouldAutoRetry: function(a) {
            var b = this._uploadData.retrieve({
                id: a
            });
            return !this._preventRetries[a] && this._options.retry.enableAuto && b.status !== qq.status.PAUSED ? (void 0 === this._autoRetries[a] && (this._autoRetries[a] = 0), this._autoRetries[a] < this._options.retry.maxAutoAttempts) : !1
        }, _onBeforeManualRetry: function(a) {
            var b = this._options.validation.itemLimit;
            if (this._preventRetries[a])
                return this.log("Retries are forbidden for id " + a, "warn"), !1;
            if (this._handler.isValid(a)) {
                var c = this.getName(a);
                return this._options.callbacks.onManualRetry(a, c)===!1?!1 : b > 0 && this._netUploadedOrQueued + 1 > b ? (this._itemError("retryFailTooManyItems"), !1) : (this.log("Retrying upload for '" + c + "' (id: " + a + ")..."), !0)
            }
            return this.log("'" + a + "' is not a valid file ID", "error"), !1
        }, _manualRetry: function(a, b) {
            return this._onBeforeManualRetry(a) ? (this._netUploadedOrQueued++, this._uploadData.setStatus(a, qq.status.UPLOAD_RETRYING), b ? b(a) : this._handler.retry(a), !0) : void 0
        }, _maybeParseAndSendUploadError: function(a, b, c, d) {
            if (!c.success)
                if (d && 200 !== d.status&&!c.error)
                    this._options.callbacks.onError(a, b, "XHR returned response code " + d.status, d);
                else {
                    var e = c.error ? c.error: this._options.text.defaultResponseError;
                    this._options.callbacks.onError(a, b, e, d)
                }
        }, _prepareItemsForUpload: function(a, b, c) {
            if (0 === a.length)
                return this._itemError("noFilesError"), void 0;
            var d = this._getValidationDescriptors(a), e = this._getButtonId(a[0].file), f = this._getButton(e);
            this._handleCheckedCallback({
                name: "onValidateBatch",
                callback: qq.bind(this._options.callbacks.onValidateBatch, this, d, f),
                onSuccess: qq.bind(this._onValidateBatchCallbackSuccess, this, d, a, b, c, f),
                onFailure: qq.bind(this._onValidateBatchCallbackFailure, this, a),
                identifier: "batch validation"
            })
        }, _upload: function(a, b, c) {
            var d = this.getName(a);
            b && this.setParams(b, a), c && this.setEndpoint(c, a), this._handleCheckedCallback({
                name: "onSubmit",
                callback: qq.bind(this._options.callbacks.onSubmit, this, a, d),
                onSuccess: qq.bind(this._onSubmitCallbackSuccess, this, a, d),
                onFailure: qq.bind(this._fileOrBlobRejected, this, a, d),
                identifier: a
            })
        }, _onSubmitCallbackSuccess: function(a) {
            this._onSubmit.apply(this, arguments), this._uploadData.setStatus(a, qq.status.SUBMITTED), this._onSubmitted.apply(this, arguments), this._options.callbacks.onSubmitted.apply(this, arguments), this._options.autoUpload ? this._uploadFile(a) : this._storeForLater(a)
        }, _onSubmitted: function() {}, _storeForLater: function(a) {
            this._storedIds.push(a)
        }, _onValidateBatchCallbackSuccess: function(a, b, c, d, e) {
            var f, g = this._options.validation.itemLimit, h = this._netUploadedOrQueued;
            0 === g || g >= h ? b.length > 0 ? this._handleCheckedCallback({
                name: "onValidate",
                callback: qq.bind(this._options.callbacks.onValidate, this, a[0], e),
                onSuccess: qq.bind(this._onValidateCallbackSuccess, this, b, 0, c, d),
                onFailure: qq.bind(this._onValidateCallbackFailure, this, b, 0, c, d),
                identifier: "Item '" + b[0].file.name + "', size: " + b[0].file.size
            }) : this._itemError("noFilesError") : (this._onValidateBatchCallbackFailure(b), f = this._options.messages.tooManyItemsError.replace(/\{netItems\}/g, h).replace(/\{itemLimit\}/g, g), this._batchError(f))
        }, _onValidateBatchCallbackFailure: function(a) {
            var b = this;
            qq.each(a, function(a, c) {
                b._fileOrBlobRejected(c.id)
            })
        }, _onValidateCallbackSuccess: function(a, b, c, d) {
            var e = this, f = b + 1, g = this._getValidationDescriptor(a[b].file);
            this._validateFileOrBlobData(a[b], g).then(function() {
                e._upload(a[b].id, c, d), e._maybeProcessNextItemAfterOnValidateCallback(!0, a, f, c, d)
            }, function() {
                e._maybeProcessNextItemAfterOnValidateCallback(!1, a, f, c, d)
            })
        }, _onValidateCallbackFailure: function(a, b, c, d) {
            var e = b + 1;
            this._fileOrBlobRejected(a[0].id, a[0].file.name), this._maybeProcessNextItemAfterOnValidateCallback(!1, a, e, c, d)
        }, _maybeProcessNextItemAfterOnValidateCallback: function(a, b, c, d, e) {
            var f = this;
            if (b.length > c)
                if (a ||!this._options.validation.stopOnFirstInvalidFile)
                    setTimeout(function() {
                        var a = f._getValidationDescriptor(b[c].file);
                        f._handleCheckedCallback({
                            name: "onValidate",
                            callback: qq.bind(f._options.callbacks.onValidate, f, b[c].file),
                            onSuccess: qq.bind(f._onValidateCallbackSuccess, f, b, c, d, e),
                            onFailure: qq.bind(f._onValidateCallbackFailure, f, b, c, d, e),
                            identifier: "Item '" + a.name + "', size: " + a.size
                        })
                    }, 0);
                else if (!a)
                    for (; c < b.length; c++)
                        f._fileOrBlobRejected(b[c].id)
        }, _validateFileOrBlobData: function(a, b) {
            var c = this, d = a.file, e = b.name, f = b.size, g = this._getButtonId(d), h = this._getValidationBase(g), i = new qq.Promise;
            return i.then(function() {}, function() {
                c._fileOrBlobRejected(a.id, e)
            }), qq.isFileOrInput(d)&&!this._isAllowedExtension(h.allowedExtensions, e) ? (this._itemError("typeError", e, d), i.failure()) : 0 === f ? (this._itemError("emptyError", e, d), i.failure()) : f && h.sizeLimit && f > h.sizeLimit ? (this._itemError("sizeError", e, d), i.failure()) : f && f < h.minSizeLimit ? (this._itemError("minSizeError", e, d), i.failure()) : (qq.ImageValidation && qq.supportedFeatures.imagePreviews && qq.isFile(d) ? new qq.ImageValidation(d, qq.bind(c.log, c)).validate(h.image).then(i.success, function(a) {
                c._itemError(a + "ImageError", e, d), i.failure()
            }) : i.success(), i)
        }, _fileOrBlobRejected: function(a) {
            this._netUploadedOrQueued--, this._uploadData.setStatus(a, qq.status.REJECTED)
        }, _itemError: function(a, b, c) {
            function d(a, b) {
                g = g.replace(a, b)
            }
            var e, f, g = this._options.messages[a], h = [], i = [].concat(b), j = i[0], k = this._getButtonId(c), l = this._getValidationBase(k);
            return qq.each(l.allowedExtensions, function(a, b) {
                qq.isString(b) && h.push(b)
            }), e = h.join(", ").toLowerCase(), d("{file}", this._options.formatFileName(j)), d("{extensions}", e), d("{sizeLimit}", this._formatSize(l.sizeLimit)), d("{minSizeLimit}", this._formatSize(l.minSizeLimit)), f = g.match(/(\{\w+\})/g), null !== f && qq.each(f, function(a, b) {
                d(b, i[a])
            }), this._options.callbacks.onError(null, j, g, void 0), g
        }, _batchError: function(a) {
            this._options.callbacks.onError(null, null, a, void 0)
        }, _isAllowedExtension: function(a, b) {
            var c=!1;
            return a.length ? (qq.each(a, function(a, d) {
                if (qq.isString(d)) {
                    var e = new RegExp("\\." + d + "$", "i");
                    if (null != b.match(e))
                        return c=!0, !1
                }
            }), c) : !0
        }, _formatSize: function(a) {
            var b =- 1;
            do 
                a/=1e3, b++;
            while (a > 999);
            return Math.max(a, .1).toFixed(1) + this._options.text.sizeSymbols[b]
        }, _wrapCallbacks: function() {
            var a, b;
            a = this, b = function(b, c, d) {
                var e;
                try {
                    return c.apply(a, d)
                } catch (f) {
                    e = f.message || f.toString(), a.log("Caught exception in '" + b + "' callback - " + e, "error")
                }
            };
            for (var c in this._options.callbacks)
                !function() {
                    var d, e;
                    d = c, e = a._options.callbacks[d], a._options.callbacks[d] = function() {
                        return b(d, e, arguments)
                    }
                }()
        }, _parseFileOrBlobDataName: function(a) {
            var b;
            return b = qq.isFileOrInput(a) ? a.value ? a.value.replace(/.*(\/|\\)/, "") : null !== a.fileName && void 0 !== a.fileName ? a.fileName : a.name : a.name
        }, _parseFileOrBlobDataSize: function(a) {
            var b;
            return qq.isFileOrInput(a) ? void 0 === a.value && (b = null !== a.fileSize && void 0 !== a.fileSize ? a.fileSize : a.size) : b = a.blob.size, b
        }, _getValidationDescriptor: function(a) {
            var b = {}, c = this._parseFileOrBlobDataName(a), d = this._parseFileOrBlobDataSize(a);
            return b.name = c, void 0 !== d && (b.size = d), b
        }, _getValidationDescriptors: function(a) {
            var b = this, c = [];
            return qq.each(a, function(a, d) {
                c.push(b._getValidationDescriptor(d.file))
            }), c
        }, _createStore: function(a, b) {
            var c = {}, d = a, e = function(a) {
                return qq.isObject(a) ? qq.extend({}, a) : a
            }, f = function() {
                return qq.isFunction(b) ? b() : b
            }, g = function(a) {
                b && qq.isObject(a) && qq.extend(a, f())
            };
            return {
                set: function(a, b) {
                    null == b ? (c = {}, d = e(a)) : c[b] = e(a)
                },
                get: function(a) {
                    var b;
                    return b = null != a && c[a] ? c[a] : d, g(b), e(b)
                },
                remove: function(a) {
                    return delete c[a]
                },
                reset: function() {
                    c = {}, d = a
                }
            }
        }, _handleCameraAccess: function() {
            if (this._options.camera.ios && qq.ios()) {
                var a = "image/*;capture=camera", b = this._options.camera.button, c = b ? this._getButtonId(b): this._defaultButtonId, d = this._options;
                c && c !== this._defaultButtonId && (d = this._extraButtonSpecs[c]), d.multiple=!1, null === d.validation.acceptFiles ? d.validation.acceptFiles = a : d.validation.acceptFiles += "," + a, qq.each(this._buttons, function(a, b) {
                    return b.getButtonId() === c ? (b.setMultiple(d.multiple), b.setAcceptFiles(d.acceptFiles), !1) : void 0
                })
            }
        }, _getValidationBase: function(a) {
            var b = this._extraButtonSpecs[a];
            return b ? b.validation : this._options.validation
        }
    }
}(), function() {
    "use strict";
    qq.FineUploaderBasic = function(a) {
        this._options = {
            debug: !1,
            button: null,
            multiple: !0,
            maxConnections: 3,
            disableCancelForFormUploads: !1,
            autoUpload: !0,
            request: {
                endpoint: "/server/upload",
                params: {},
                paramsInBody: !0,
                customHeaders: {},
                forceMultipart: !0,
                inputName: "qqfile",
                uuidName: "qquuid",
                totalFileSizeName: "qqtotalfilesize",
                filenameParam: "qqfilename"
            },
            validation: {
                allowedExtensions: [],
                sizeLimit: 0,
                minSizeLimit: 0,
                itemLimit: 0,
                stopOnFirstInvalidFile: !0,
                acceptFiles: null,
                image: {
                    maxHeight: 0,
                    maxWidth: 0,
                    minHeight: 0,
                    minWidth: 0
                }
            },
            callbacks: {
                onSubmit: function() {},
                onSubmitted: function() {},
                onComplete: function() {},
                onAllComplete: function() {},
                onCancel: function() {},
                onUpload: function() {},
                onUploadChunk: function() {},
                onUploadChunkSuccess: function() {},
                onResume: function() {},
                onProgress: function() {},
                onError: function() {},
                onAutoRetry: function() {},
                onManualRetry: function() {},
                onValidateBatch: function() {},
                onValidate: function() {},
                onSubmitDelete: function() {},
                onDelete: function() {},
                onDeleteComplete: function() {},
                onPasteReceived: function() {},
                onStatusChange: function() {},
                onSessionRequestComplete: function() {}
            },
            messages: {
                typeError: "{file} has an invalid extension. Valid extension(s): {extensions}.",
                sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError: "{file} is empty, please select files again without it.",
                noFilesError: "No files to upload.",
                tooManyItemsError: "Too many items ({netItems}) would be uploaded.  Item limit is {itemLimit}.",
                maxHeightImageError: "Image is too tall.",
                maxWidthImageError: "Image is too wide.",
                minHeightImageError: "Image is not tall enough.",
                minWidthImageError: "Image is not wide enough.",
                retryFailTooManyItems: "Retry failed - you have reached your file limit.",
                onLeave: "The files are being uploaded, if you leave now the upload will be canceled."
            },
            retry: {
                enableAuto: !1,
                maxAutoAttempts: 3,
                autoAttemptDelay: 5,
                preventRetryResponseProperty: "preventRetry"
            },
            classes: {
                buttonHover: "qq-upload-button-hover",
                buttonFocus: "qq-upload-button-focus"
            },
            chunking: {
                enabled: !1,
                partSize: 2e6,
                paramNames: {
                    partIndex: "qqpartindex",
                    partByteOffset: "qqpartbyteoffset",
                    chunkSize: "qqchunksize",
                    totalFileSize: "qqtotalfilesize",
                    totalParts: "qqtotalparts"
                }
            },
            resume: {
                enabled: !1,
                id: null,
                cookiesExpireIn: 7,
                paramNames: {
                    resuming: "qqresume"
                }
            },
            formatFileName: function(a) {
                return void 0 !== a && a.length > 33 && (a = a.slice(0, 19) + "..." + a.slice(-14)), a
            },
            text: {
                defaultResponseError: "Upload failure reason unknown",
                sizeSymbols: ["kB", "MB", "GB", "TB", "PB", "EB"]
            },
            deleteFile: {
                enabled: !1,
                method: "DELETE",
                endpoint: "/server/upload",
                customHeaders: {},
                params: {}
            },
            cors: {
                expected: !1,
                sendCredentials: !1,
                allowXdr: !1
            },
            blobs: {
                defaultName: "misc_data"
            },
            paste: {
                targetElement: null,
                defaultName: "pasted_image"
            },
            camera: {
                ios: !1,
                button: null
            },
            extraButtons: [],
            session: {
                endpoint: null,
                params: {},
                customHeaders: {},
                refreshOnReset: !0
            },
            form: {
                element: "qq-form",
                autoUpload: !1,
                interceptSubmit: !0
            }
        }, qq.extend(this._options, a, !0), this._buttons = [], this._extraButtonSpecs = {}, this._buttonIdsForFileIds = [], this._wrapCallbacks(), this._disposeSupport = new qq.DisposeSupport, this._storedIds = [], this._autoRetries = [], this._retryTimeouts = [], this._preventRetries = [], this._thumbnailUrls = [], this._netUploadedOrQueued = 0, this._netUploaded = 0, this._uploadData = this._createUploadDataTracker(), this._initFormSupportAndParams(), this._deleteFileParamsStore = this._createStore(this._options.deleteFile.params), this._endpointStore = this._createStore(this._options.request.endpoint), this._deleteFileEndpointStore = this._createStore(this._options.deleteFile.endpoint), this._handler = this._createUploadHandler(), this._deleteHandler = qq.DeleteFileAjaxRequester && this._createDeleteHandler(), this._options.button && (this._defaultButtonId = this._createUploadButton({
            element: this._options.button
        }).getButtonId()), this._generateExtraButtonSpecs(), this._handleCameraAccess(), this._options.paste.targetElement && (qq.PasteSupport ? this._pasteHandler = this._createPasteHandler() : qq.log("Paste support module not found", "info")), this._preventLeaveInProgress(), this._imageGenerator = qq.ImageGenerator && new qq.ImageGenerator(qq.bind(this.log, this)), this._refreshSessionData(), this._succeededSinceLastAllComplete = [], this._failedSinceLastAllComplete = []
    }, qq.FineUploaderBasic.prototype = qq.basePublicApi, qq.extend(qq.FineUploaderBasic.prototype, qq.basePrivateApi)
}(), qq.AjaxRequester = function(a) {
    "use strict";
    function b() {
        return qq.indexOf(["GET", "POST", "HEAD"], w.method) >= 0
    }
    function c() {
        var a=!1;
        return qq.each(a, function(b, c) {
            return qq.indexOf(["Accept", "Accept-Language", "Content-Language", "Content-Type"], c) < 0 ? (a=!0, !1) : void 0
        }), a
    }
    function d(a) {
        return w.cors.expected && void 0 === a.withCredentials
    }
    function e() {
        var a;
        return (window.XMLHttpRequest || window.ActiveXObject) && (a = qq.createXhrInstance(), void 0 === a.withCredentials && (a = new XDomainRequest)), a
    }
    function f(a, b) {
        var c = v[a].xhr;
        return c || b || (c = w.cors.expected ? e() : qq.createXhrInstance(), v[a].xhr = c), c
    }
    function g(a) {
        var b, c = qq.indexOf(u, a), d = w.maxConnections;
        delete v[a], u.splice(c, 1), u.length >= d && d > c && (b = u[d-1], j(b))
    }
    function h(a, b) {
        var c = f(a), e = w.method, h = b===!0;
        g(a), h ? s(e + " request for " + a + " has failed", "error") : d(c) || q(c.status) || (h=!0, s(e + " request for " + a + " has failed - response code " + c.status, "error")), w.onComplete(a, c, h)
    }
    function i(a) {
        var b, c = v[a].additionalParams, d = w.mandatedParams;
        return w.paramsStore.get && (b = w.paramsStore.get(a)), c && qq.each(c, function(a, c) {
            b = b || {}, b[a] = c
        }), d && qq.each(d, function(a, c) {
            b = b || {}, b[a] = c
        }), b
    }
    function j(a) {
        var b, c = f(a), e = w.method, g = i(a), h = v[a].payload;
        return w.onSend(a), b = k(a, g), d(c) ? (c.onload = n(a), c.onerror = o(a)) : c.onreadystatechange = l(a), m(a), c.open(e, b, !0), w.cors.expected && w.cors.sendCredentials&&!d(c) && (c.withCredentials=!0), p(a), s("Sending " + e + " request for " + a), h ? c.send(h) : t ||!g ? c.send() : g && w.contentType && w.contentType.toLowerCase().indexOf("application/x-www-form-urlencoded") >= 0 ? c.send(qq.obj2url(g, "")) : g && w.contentType && w.contentType.toLowerCase().indexOf("application/json") >= 0 ? c.send(JSON.stringify(g)) : c.send(g), c
    }
    function k(a, b) {
        var c = w.endpointStore.get(a), d = v[a].addToPath;
        return void 0 != d && (c += "/" + d), t && b ? qq.obj2url(b, c) : c
    }
    function l(a) {
        return function() {
            4 === f(a).readyState && h(a)
        }
    }
    function m(a) {
        var b = w.onProgress;
        b && (f(a).upload.onprogress = function(c) {
            c.lengthComputable && b(a, c.loaded, c.total)
        })
    }
    function n(a) {
        return function() {
            h(a)
        }
    }
    function o(a) {
        return function() {
            h(a, !0)
        }
    }
    function p(a) {
        var e = f(a), g = w.customHeaders, h = v[a].additionalHeaders || {}, i = w.method, j = {};
        d(e) || (w.allowXRequestedWithAndCacheControl && (w.cors.expected && b()&&!c(g) || (e.setRequestHeader("X-Requested-With", "XMLHttpRequest"), e.setRequestHeader("Cache-Control", "no-cache"))), !w.contentType || "POST" !== i && "PUT" !== i || e.setRequestHeader("Content-Type", w.contentType), qq.extend(j, qq.isFunction(g) ? g(a) : g), qq.extend(j, h), qq.each(j, function(a, b) {
            e.setRequestHeader(a, b)
        }))
    }
    function q(a) {
        return qq.indexOf(w.successfulResponseCodes[w.method], a) >= 0
    }
    function r(a, b, c, d, e) {
        v[a] = {
            addToPath: b,
            additionalParams: c,
            additionalHeaders: d,
            payload: e
        };
        var f = u.push(a);
        return f <= w.maxConnections ? j(a) : void 0
    }
    var s, t, u = [], v = {}, w = {
        validMethods: ["POST"],
        method: "POST",
        contentType: "application/x-www-form-urlencoded",
        maxConnections: 3,
        customHeaders: {},
        endpointStore: {},
        paramsStore: {},
        mandatedParams: {},
        allowXRequestedWithAndCacheControl: !0,
        successfulResponseCodes: {
            DELETE: [200, 202, 204],
            POST: [200, 204],
            GET: [200]
        },
        cors: {
            expected: !1,
            sendCredentials: !1
        },
        log: function() {},
        onSend: function() {},
        onComplete: function() {},
        onProgress: null
    };
    if (qq.extend(w, a), s = w.log, qq.indexOf(w.validMethods, w.method) < 0)throw new Error("'" + w.method + "' is not a supported method for this type of request!");
    t = "GET" === w.method || "DELETE" === w.method, qq.extend(this, {
        initTransport: function(a) {
            var b, c, d, e, f;
            return {
                withPath: function(a) {
                    return b = a, this
                },
                withParams: function(a) {
                    return c = a, this
                },
                withHeaders: function(a) {
                    return d = a, this
                },
                withPayload: function(a) {
                    return e = a, this
                },
                withCacheBuster: function() {
                    return f=!0, this
                },
                send: function() {
                    return f && qq.indexOf(["GET", "DELETE"], w.method) >= 0 && (c.qqtimestamp = (new Date).getTime()), r(a, b, c, d, e)
                }
            }
        },
        canceled: function(a) {
            g(a)
        }
    })
}, qq.UploadHandler = function(a, b) {
    "use strict";
    function c(a) {
        var b, c = qq.indexOf(i, a), d = f.maxConnections;
        c >= 0 && (i.splice(c, 1), i.length >= d && d > c && (b = i[d-1], h.upload(b)))
    }
    function d(a) {
        g("Cancelling " + a), f.paramsStore.remove(a), c(a)
    }
    function e() {
        var a = b ? qq[b]: qq, d = qq.supportedFeatures.ajaxUploading ? "Xhr": "Form";
        h = new a["UploadHandler" + d](f, {
            onUploadComplete: c,
            onUuidChanged: f.onUuidChanged,
            getName: f.getName,
            getUuid: f.getUuid,
            getSize: f.getSize,
            log: g
        })
    }
    var f, g, h, i = [];
    f = {
        debug: !1,
        forceMultipart: !0,
        paramsInBody: !1,
        paramsStore: {},
        endpointStore: {},
        filenameParam: "qqfilename",
        cors: {
            expected: !1,
            sendCredentials: !1
        },
        maxConnections: 3,
        uuidName: "qquuid",
        totalFileSizeName: "qqtotalfilesize",
        chunking: {
            enabled: !1,
            partSize: 2e6,
            paramNames: {
                partIndex: "qqpartindex",
                partByteOffset: "qqpartbyteoffset",
                chunkSize: "qqchunksize",
                totalParts: "qqtotalparts",
                filename: "qqfilename"
            }
        },
        resume: {
            enabled: !1,
            id: null,
            cookiesExpireIn: 7,
            paramNames: {
                resuming: "qqresume"
            }
        },
        log: function() {},
        onProgress: function() {},
        onComplete: function() {},
        onCancel: function() {},
        onUpload: function() {},
        onUploadChunk: function() {},
        onUploadChunkSuccess: function() {},
        onAutoRetry: function() {},
        onResume: function() {},
        onUuidChanged: function() {},
        getName: function() {}
    }, qq.extend(f, a), g = f.log, qq.extend(this, {
        add: function() {
            return h.add.apply(this, arguments)
        },
        upload: function(a) {
            var b = i.push(a);
            return b <= f.maxConnections ? (h.upload(a), !0) : !1
        },
        retry: function(a) {
            var b = qq.indexOf(i, a);
            return b >= 0 ? h.upload(a, !0) : this.upload(a)
        },
        cancel: function(a) {
            var b = h.cancel(a);
            b instanceof qq.Promise ? b.then(function() {
                d(a)
            }) : b!==!1 && d(a)
        },
        cancelAll: function() {
            var a = this, b = [];
            qq.extend(b, i), qq.each(b, function(b, c) {
                a.cancel(c)
            }), i = []
        },
        getFile: function(a) {
            return h.getFile ? h.getFile(a) : void 0
        },
        getInput: function(a) {
            return h.getInput ? h.getInput(a) : void 0
        },
        reset: function() {
            g("Resetting upload handler"), this.cancelAll(), i = [], h.reset()
        },
        expunge: function(a) {
            return this.isValid(a) ? h.expunge(a) : void 0
        },
        isValid: function(a) {
            return h.isValid(a)
        },
        getResumableFilesData: function() {
            return h.getResumableFilesData ? h.getResumableFilesData() : []
        },
        getThirdPartyFileId: function(a) {
            return h.getThirdPartyFileId && this.isValid(a) ? h.getThirdPartyFileId(a) : void 0
        },
        pause: function(a) {
            return h.pause && this.isValid(a) && h.pause(a) ? (c(a), !0) : void 0
        }
    }), e()
}, qq.AbstractUploadHandlerForm = function(a) {
    "use strict";
    function b(a) {
        delete k[a], delete n[a], m && (clearTimeout(l[a]), delete l[a], u.stopReceivingMessages(a));
        var b = document.getElementById(g._getIframeName(a));
        b && (b.setAttribute("src", "java" + String.fromCharCode(115) + "cript:false;"), qq(b).remove())
    }
    function c(a, b) {
        var c = a.id, d = e(c), f = s(d);
        j[f] = b, k[d] = qq(a).attach("load", function() {
            n[d].input && (t("Received iframe load event for CORS upload request (iframe name " + c + ")"), l[c] = setTimeout(function() {
                var a = "No valid message received from loaded iframe for iframe name " + c;
                t(a, "error"), b({
                    error: a
                })
            }, 1e3))
        }), u.receiveMessage(c, function(a) {
            t("Received the following window message: '" + a + "'");
            var b, d = e(c), f = g._parseJsonResponse(d, a), h = f.uuid;
            h && j[h] ? (t("Handling response for iframe name " + c), clearTimeout(l[c]), delete l[c], g._detachLoadEvent(c), b = j[h], delete j[h], u.stopReceivingMessages(c), b(f)) : h || t("'" + a + "' does not contain a UUID - ignoring.")
        })
    }
    function d(a) {
        var b = qq.toElement("<iframe src='javascript:false;' name='" + a + "' />");
        return b.setAttribute("id", a), b.style.display = "none", document.body.appendChild(b), b
    }
    function e(a) {
        return a.split("_")[0]
    }
    var f = a.options, g = this, h = a.proxy, i = qq.getUniqueId(), j = {}, k = {}, l = {}, m = f.isCors, n = {}, o = f.inputName, p = h.onCancel, q = h.onUuidChanged, r = h.getName, s = h.getUuid, t = h.log, u = new qq.WindowReceiveMessage({
        log: t
    });
    qq.extend(this, {
        add: function(a, b) {
            n[a] = {
                input: b
            }, b.setAttribute("name", o), b.parentNode && qq(b).remove()
        },
        getInput: function(a) {
            return n[a].input
        },
        isValid: function(a) {
            return void 0 !== n[a] && void 0 !== n[a].input
        },
        reset: function() {
            n.length = 0
        },
        expunge: function(a) {
            return b(a)
        },
        cancel: function(a) {
            var b = p(a, r(a));
            return b instanceof qq.Promise ? b.then(function() {
                this.expunge(a)
            }) : b!==!1 ? (this.expunge(a), !0) : !1
        },
        upload: function() {},
        _getIframeName: function(a) {
            return a + "_" + i
        },
        _createIframe: function(a) {
            var b = g._getIframeName(a);
            return d(b)
        },
        _parseJsonResponse: function(a, b) {
            var c;
            try {
                c = qq.parseJson(b), void 0 !== c.newUuid && q(a, c.newUuid)
            } catch (d) {
                t("Error when attempting to parse iframe upload response (" + d.message + ")", "error"), c = {}
            }
            return c
        },
        _initFormForUpload: function(a) {
            var b = a.method, c = a.endpoint, d = a.params, e = a.paramsInBody, f = a.targetName, g = qq.toElement("<form method='" + b + "' enctype='multipart/form-data'></form>"), h = c;
            return e ? qq.obj2Inputs(d, g) : h = qq.obj2url(d, c), g.setAttribute("action", h), g.setAttribute("target", f), g.style.display = "none", document.body.appendChild(g), g
        },
        _attachLoadEvent: function(a, b) {
            var d;
            m ? c(a, b) : k[a.id] = qq(a).attach("load", function() {
                if (t("Received response for " + a.id), a.parentNode) {
                    try {
                        if (a.contentDocument && a.contentDocument.body && "false" == a.contentDocument.body.innerHTML)
                            return 
                    } catch (c) {
                        t("Error when attempting to access iframe during handling of upload response (" + c.message + ")", "error"), d = {
                            success: !1
                        }
                    }
                    b(d)
                }
            })
        },
        _detachLoadEvent: function(a) {
            void 0 !== k[a] && (k[a](), delete k[a])
        },
        _getFileState: function(a) {
            return n[a]
        }
    })
}, qq.AbstractUploadHandlerXhr = function(a) {
    "use strict";
    function b(a, b, c) {
        return a.slice ? a.slice(b, c) : a.mozSlice ? a.mozSlice(b, c) : a.webkitSlice ? a.webkitSlice(b, c) : void 0
    }
    function c(a) {
        var b = g[a].xhr, c = g[a].currentAjaxRequester;
        b.onreadystatechange = null, b.upload.onprogress = null, b.abort(), c && c.canceled && c.canceled(a)
    }
    var d = this, e = a.options, f = a.proxy, g = {}, h = e.chunking, i = f.onUpload, j = f.onCancel, k = f.getName, l = f.getSize, m = f.log;
    qq.extend(this, {
        add: function(a, b) {
            if (qq.isFile(b))
                g[a] = {
                    file: b
                };
            else {
                if (!qq.isBlob(b.blob))
                    throw new Error("Passed obj is not a File or BlobData (in qq.UploadHandlerXhr)");
                g[a] = {
                    blobData: b
                }
            }
        },
        getFile: function(a) {
            return g[a] ? g[a].file || g[a].blobData.blob : void 0
        },
        isValid: function(a) {
            return void 0 !== g[a]
        },
        reset: function() {
            g.length = 0
        },
        expunge: function(a) {
            var b = g[a].xhr;
            b && c(a), delete g[a]
        },
        upload: function(a, b) {
            return g[a] && delete g[a].paused, i(a, b)
        },
        cancel: function(a) {
            var b = j(a, k(a));
            return b instanceof qq.Promise ? b.then(function() {
                g[a].canceled=!0, this.expunge(a)
            }) : b!==!1 ? (g[a].canceled=!0, this.expunge(a), !0) : !1
        },
        pause: function(a) {
            var b = g[a].xhr;
            return b ? (m(qq.format("Aborting XHR upload for {} '{}' due to pause instruction.", a, k(a))), g[a].paused=!0, c(a), !0) : void 0
        },
        _createXhr: function(a) {
            return this._registerXhr(a, qq.createXhrInstance())
        },
        _registerXhr: function(a, b, c) {
            return g[a].xhr = b, g[a].currentAjaxRequester = c, b
        },
        _getMimeType: function(a) {
            return d.getFile(a).type
        },
        _getTotalChunks: function(a) {
            if (h) {
                var b = l(a), c = h.partSize;
                return Math.ceil(b / c)
            }
        },
        _getChunkData: function(a, c) {
            var e = h.partSize, f = l(a), g = d.getFile(a), i = e * c, j = i + e >= f ? f: i + e, k = this._getTotalChunks(a);
            return {
                part: c,
                start: i,
                end: j,
                count: k,
                blob: b(g, i, j),
                size: j - i
            }
        },
        _getChunkDataForCallback: function(a) {
            return {
                partIndex: a.part,
                startByte: a.start + 1,
                endByte: a.end,
                totalParts: a.count
            }
        },
        _getFileState: function(a) {
            return g[a]
        }
    })
}, qq.WindowReceiveMessage = function(a) {
    "use strict";
    var b = {
        log: function() {}
    }, c = {};
    qq.extend(b, a), qq.extend(this, {
        receiveMessage: function(a, b) {
            var d = function(a) {
                b(a.data)
            };
            window.postMessage ? c[a] = qq(window).attach("message", d) : log("iframe message passing not supported in this browser!", "error")
        },
        stopReceivingMessages: function(a) {
            if (window.postMessage) {
                var b = c[a];
                b && b()
            }
        }
    })
}, function() {
    "use strict";
    qq.uiPublicApi = {
        clearStoredFiles: function() {
            this._parent.prototype.clearStoredFiles.apply(this, arguments), this._templating.clearFiles()
        },
        addExtraDropzone: function(a) {
            this._dnd && this._dnd.setupExtraDropzone(a)
        },
        removeExtraDropzone: function(a) {
            return this._dnd ? this._dnd.removeDropzone(a) : void 0
        },
        getItemByFileId: function(a) {
            return this._templating.getFileContainer(a)
        },
        reset: function() {
            this._parent.prototype.reset.apply(this, arguments), this._templating.reset(), !this._options.button && this._templating.getButton() && (this._defaultButtonId = this._createUploadButton({
                element: this._templating.getButton()
            }).getButtonId()), this._dnd && (this._dnd.dispose(), this._dnd = this._setupDragAndDrop()), this._totalFilesInBatch = 0, this._filesInBatchAddedToUi = 0, this._setupClickAndEditEventHandlers()
        },
        pauseUpload: function(a) {
            var b = this._parent.prototype.pauseUpload.apply(this, arguments);
            return b && this._templating.uploadPaused(a), b
        },
        continueUpload: function(a) {
            var b = this._parent.prototype.continueUpload.apply(this, arguments);
            return b && this._templating.uploadContinued(a), b
        },
        getId: function(a) {
            return this._templating.getFileId(a)
        },
        getDropTarget: function(a) {
            var b = this.getFile(a);
            return b.qqDropTarget
        }
    }, qq.uiPrivateApi = {
        _getButton: function(a) {
            var b = this._parent.prototype._getButton.apply(this, arguments);
            return b || a === this._defaultButtonId && (b = this._templating.getButton()), b
        },
        _removeFileItem: function(a) {
            this._templating.removeFile(a)
        },
        _setupClickAndEditEventHandlers: function() {
            this._fileButtonsClickHandler = qq.FileButtonsClickHandler && this._bindFileButtonsClickEvent(), this._focusinEventSupported=!qq.firefox(), this._isEditFilenameEnabled() && (this._filenameClickHandler = this._bindFilenameClickEvent(), this._filenameInputFocusInHandler = this._bindFilenameInputFocusInEvent(), this._filenameInputFocusHandler = this._bindFilenameInputFocusEvent())
        },
        _setupDragAndDrop: function() {
            var a = this, b = this._options.dragAndDrop.extraDropzones, c = this._templating, d = c.getDropZone();
            return d && b.push(d), new qq.DragAndDrop({
                dropZoneElements: b,
                allowMultipleItems: this._options.multiple,
                classes: {
                    dropActive: this._options.classes.dropActive
                },
                callbacks: {
                    processingDroppedFiles: function() {
                        c.showDropProcessing()
                    },
                    processingDroppedFilesComplete: function(b, d) {
                        c.hideDropProcessing(), qq.each(b, function(a, b) {
                            b.qqDropTarget = d
                        }), b.length && a.addFiles(b, null, null)
                    },
                    dropError: function(b, c) {
                        a._itemError(b, c)
                    },
                    dropLog: function(b, c) {
                        a.log(b, c)
                    }
                }
            })
        },
        _bindFileButtonsClickEvent: function() {
            var a = this;
            return new qq.FileButtonsClickHandler({
                templating: this._templating,
                log: function(b, c) {
                    a.log(b, c)
                },
                onDeleteFile: function(b) {
                    a.deleteFile(b)
                },
                onCancel: function(b) {
                    a.cancel(b)
                },
                onRetry: function(b) {
                    qq(a._templating.getFileContainer(b)).removeClass(a._classes.retryable), a.retry(b)
                },
                onPause: function(b) {
                    a.pauseUpload(b)
                },
                onContinue: function(b) {
                    a.continueUpload(b)
                },
                onGetName: function(b) {
                    return a.getName(b)
                }
            })
        },
        _isEditFilenameEnabled: function() {
            return this._templating.isEditFilenamePossible()&&!this._options.autoUpload && qq.FilenameClickHandler && qq.FilenameInputFocusHandler && qq.FilenameInputFocusHandler
        },
        _filenameEditHandler: function() {
            var a = this, b = this._templating;
            return {
                templating: b,
                log: function(b, c) {
                    a.log(b, c)
                },
                onGetUploadStatus: function(b) {
                    return a.getUploads({
                        id: b
                    }).status
                },
                onGetName: function(b) {
                    return a.getName(b)
                },
                onSetName: function(c, d) {
                    var e = a._options.formatFileName(d);
                    b.updateFilename(c, e), a.setName(c, d)
                },
                onEditingStatusChange: function(a, c) {
                    var d = qq(b.getEditInput(a)), e = qq(b.getFileContainer(a));
                    c ? (d.addClass("qq-editing"), b.hideFilename(a), b.hideEditIcon(a)) : (d.removeClass("qq-editing"), b.showFilename(a), b.showEditIcon(a)), e.addClass("qq-temp").removeClass("qq-temp")
                }
            }
        },
        _onUploadStatusChange: function(a, b, c) {
            this._parent.prototype._onUploadStatusChange.apply(this, arguments), this._isEditFilenameEnabled() && this._templating.getFileContainer(a) && c !== qq.status.SUBMITTED && (this._templating.markFilenameEditable(a), this._templating.hideEditIcon(a))
        },
        _bindFilenameInputFocusInEvent: function() {
            var a = qq.extend({}, this._filenameEditHandler());
            return new qq.FilenameInputFocusInHandler(a)
        },
        _bindFilenameInputFocusEvent: function() {
            var a = qq.extend({}, this._filenameEditHandler());
            return new qq.FilenameInputFocusHandler(a)
        },
        _bindFilenameClickEvent: function() {
            var a = qq.extend({}, this._filenameEditHandler());
            return new qq.FilenameClickHandler(a)
        },
        _storeForLater: function(a) {
            this._parent.prototype._storeForLater.apply(this, arguments), this._templating.hideSpinner(a)
        },
        _onSubmit: function(a, b) {
            this._parent.prototype._onSubmit.apply(this, arguments), this._addToList(a, b)
        },
        _onSubmitted: function(a) {
            this._isEditFilenameEnabled() && (this._templating.markFilenameEditable(a), this._templating.showEditIcon(a), this._focusinEventSupported || this._filenameInputFocusHandler.addHandler(this._templating.getEditInput(a)))
        },
        _onProgress: function(a, b, c, d) {
            this._parent.prototype._onProgress.apply(this, arguments), this._templating.updateProgress(a, c, d), c === d ? (this._templating.hideCancel(a), this._templating.hidePause(a), this._templating.setStatusText(a, this._options.text.waitingForResponse), this._displayFileSize(a)) : this._displayFileSize(a, c, d)
        },
        _onComplete: function(a, b, c) {
            function d(b) {
                f.setStatusText(a), qq(f.getFileContainer(a)).removeClass(g._classes.retrying), f.hideProgress(a), (!g._options.disableCancelForFormUploads || qq.supportedFeatures.ajaxUploading) && f.hideCancel(a), f.hideSpinner(a), b.success ? g._markFileAsSuccessful(a) : (qq(f.getFileContainer(a)).addClass(g._classes.fail), g._templating.isRetryPossible()&&!g._preventRetries[a] && qq(f.getFileContainer(a)).addClass(g._classes.retryable), g._controlFailureTextDisplay(a, b))
            }
            var e = this._parent.prototype._onComplete.apply(this, arguments), f = this._templating, g = this;
            return e instanceof qq.Promise ? e.done(function(a) {
                d(a)
            }) : d(c), e
        },
        _markFileAsSuccessful: function(a) {
            var b = this._templating;
            this._isDeletePossible() && b.showDeleteButton(a), qq(b.getFileContainer(a)).addClass(this._classes.success), this._maybeUpdateThumbnail(a)
        },
        _onUpload: function(a) {
            var b = this._parent.prototype._onUpload.apply(this, arguments);
            return this._templating.showSpinner(a), b
        },
        _onUploadChunk: function(a, b) {
            this._parent.prototype._onUploadChunk.apply(this, arguments), b.partIndex > 0 && this._templating.allowPause(a)
        },
        _onCancel: function(a) {
            this._parent.prototype._onCancel.apply(this, arguments), this._removeFileItem(a)
        },
        _onBeforeAutoRetry: function(a) {
            var b, c, d;
            this._parent.prototype._onBeforeAutoRetry.apply(this, arguments), this._showCancelLink(a), this._options.retry.showAutoRetryNote && (b = this._autoRetries[a] + 1, c = this._options.retry.maxAutoAttempts, d = this._options.retry.autoRetryNote.replace(/\{retryNum\}/g, b), d = d.replace(/\{maxAuto\}/g, c), this._templating.setStatusText(a, d), qq(this._templating.getFileContainer(a)).addClass(this._classes.retrying))
        },
        _onBeforeManualRetry: function(a) {
            return this._parent.prototype._onBeforeManualRetry.apply(this, arguments) ? (this._templating.resetProgress(a), qq(this._templating.getFileContainer(a)).removeClass(this._classes.fail), this._templating.setStatusText(a), this._templating.showSpinner(a), this._showCancelLink(a), !0) : (qq(this._templating.getFileContainer(a)).addClass(this._classes.retryable), !1)
        },
        _onSubmitDelete: function(a) {
            var b = qq.bind(this._onSubmitDeleteSuccess, this);
            this._parent.prototype._onSubmitDelete.call(this, a, b)
        },
        _onSubmitDeleteSuccess: function() {
            this._options.deleteFile.forceConfirm ? this._showDeleteConfirm.apply(this, arguments) : this._sendDeleteRequest.apply(this, arguments)
        },
        _onDeleteComplete: function(a, b, c) {
            this._parent.prototype._onDeleteComplete.apply(this, arguments), this._templating.hideSpinner(a), c ? (this._templating.setStatusText(a, this._options.deleteFile.deletingFailedText), this._templating.showDeleteButton(a)) : this._removeFileItem(a)
        },
        _sendDeleteRequest: function(a) {
            this._templating.hideDeleteButton(a), this._templating.showSpinner(a), this._templating.setStatusText(a, this._options.deleteFile.deletingStatusText), this._deleteHandler.sendDelete.apply(this, arguments)
        },
        _showDeleteConfirm: function(a) {
            var b, c = this.getName(a), d = this._options.deleteFile.confirmMessage.replace(/\{filename\}/g, c), e = (this.getUuid(a), arguments), f = this;
            b = this._options.showConfirm(d), b instanceof qq.Promise ? b.then(function() {
                f._sendDeleteRequest.apply(f, e)
            }) : b!==!1 && f._sendDeleteRequest.apply(f, e)
        },
        _addToList: function(a, b, c) {
            var d, e = 0;
            this._options.display.prependFiles && (this._totalFilesInBatch > 1 && this._filesInBatchAddedToUi > 0 && (e = this._filesInBatchAddedToUi-1), d = {
                index : e
            }), c || (this._options.disableCancelForFormUploads&&!qq.supportedFeatures.ajaxUploading && this._templating.disableCancel(), this._options.multiple || (this._handler.cancelAll(), this._clearList())), this._templating.addFile(a, this._options.formatFileName(b), d), c ? this._thumbnailUrls[a] && this._templating.updateThumbnail(a, this._thumbnailUrls[a], !0) : this._templating.generatePreview(a, this.getFile(a)), this._filesInBatchAddedToUi += 1, (c || this._options.display.fileSizeOnSubmit && qq.supportedFeatures.ajaxUploading) && this._displayFileSize(a)
        },
        _clearList: function() {
            this._templating.clearFiles(), this.clearStoredFiles()
        },
        _displayFileSize: function(a, b, c) {
            var d = this.getSize(a), e = this._formatSize(d);
            d >= 0 && (void 0 !== b && void 0 !== c && (e = this._formatProgress(b, c)), this._templating.updateSize(a, e))
        },
        _formatProgress: function(a, b) {
            function c(a, b) {
                d = d.replace(a, b)
            }
            var d = this._options.text.formatProgress;
            return c("{percent}", Math.round(100 * (a / b))), c("{total_size}", this._formatSize(b)), d
        },
        _controlFailureTextDisplay: function(a, b) {
            var c, d, e, f, g;
            c = this._options.failedUploadTextDisplay.mode, d = this._options.failedUploadTextDisplay.maxChars, e = this._options.failedUploadTextDisplay.responseProperty, "custom" === c ? (f = b[e], f ? f.length > d && (g = f.substring(0, d) + "...") : (f = this._options.text.failUpload, this.log("'" + e + "' is not a valid property on the server response.", "warn")), this._templating.setStatusText(a, g || f), this._options.failedUploadTextDisplay.enableTooltip && this._showTooltip(a, f)) : "default" === c ? this._templating.setStatusText(a, this._options.text.failUpload) : "none" !== c && this.log("failedUploadTextDisplay.mode value of '" + c + "' is not valid", "warn")
        },
        _showTooltip: function(a, b) {
            this._templating.getFileContainer(a).title = b
        },
        _showCancelLink: function(a) {
            (!this._options.disableCancelForFormUploads || qq.supportedFeatures.ajaxUploading) && this._templating.showCancel(a)
        },
        _itemError: function() {
            var a = this._parent.prototype._itemError.apply(this, arguments);
            this._options.showMessage(a)
        },
        _batchError: function(a) {
            this._parent.prototype._batchError.apply(this, arguments), this._options.showMessage(a)
        },
        _setupPastePrompt: function() {
            var a = this;
            this._options.callbacks.onPasteReceived = function() {
                var b = a._options.paste.namePromptMessage, c = a._options.paste.defaultName;
                return a._options.showPrompt(b, c)
            }
        },
        _fileOrBlobRejected: function() {
            this._totalFilesInBatch -= 1, this._parent.prototype._fileOrBlobRejected.apply(this, arguments)
        },
        _prepareItemsForUpload: function(a) {
            this._totalFilesInBatch = a.length, this._filesInBatchAddedToUi = 0, this._parent.prototype._prepareItemsForUpload.apply(this, arguments)
        },
        _maybeUpdateThumbnail: function(a) {
            var b = this._thumbnailUrls[a];
            this._templating.updateThumbnail(a, b)
        },
        _addCannedFile: function() {
            var a = this._parent.prototype._addCannedFile.apply(this, arguments);
            return this._addToList(a, this.getName(a), !0), this._templating.hideSpinner(a), this._templating.hideCancel(a), this._markFileAsSuccessful(a), a
        }
    }
}(), qq.FineUploader = function(a, b) {
    "use strict";
    this._parent = b ? qq[b].FineUploaderBasic : qq.FineUploaderBasic, this._parent.apply(this, arguments), qq.extend(this._options, {
        element: null,
        button: null,
        listElement: null,
        dragAndDrop: {
            extraDropzones: []
        },
        text: {
            formatProgress: "{percent}% of {total_size}",
            failUpload: "Upload failed",
            waitingForResponse: "Processing...",
            paused: "Paused"
        },
        template: "qq-template",
        classes: {
            retrying: "qq-upload-retrying",
            retryable: "qq-upload-retryable",
            success: "qq-upload-success",
            fail: "qq-upload-fail",
            editable: "qq-editable",
            hide: "qq-hide",
            dropActive: "qq-upload-drop-area-active"
        },
        failedUploadTextDisplay: {
            mode: "default",
            maxChars: 50,
            responseProperty: "error",
            enableTooltip: !0
        },
        messages: {
            tooManyFilesError: "You may only drop one file",
            unsupportedBrowser: "Unrecoverable error - this browser does not permit file uploading of any kind."
        },
        retry: {
            showAutoRetryNote: !0,
            autoRetryNote: "Retrying {retryNum}/{maxAuto}..."
        },
        deleteFile: {
            forceConfirm: !1,
            confirmMessage: "Are you sure you want to delete {filename}?",
            deletingStatusText: "Deleting...",
            deletingFailedText: "Delete failed"
        },
        display: {
            fileSizeOnSubmit: !1,
            prependFiles: !1
        },
        paste: {
            promptForName: !1,
            namePromptMessage: "Please name this image"
        },
        thumbnails: {
            placeholders: {
                waitUntilResponse: !1,
                notAvailablePath: null,
                waitingPath: null
            }
        },
        showMessage: function(a) {
            setTimeout(function() {
                window.alert(a)
            }, 0)
        },
        showConfirm: function(a) {
            return window.confirm(a)
        },
        showPrompt: function(a, b) {
            return window.prompt(a, b)
        }
    }, !0), qq.extend(this._options, a, !0), this._templating = new qq.Templating({
        log: qq.bind(this.log, this),
        templateIdOrEl: this._options.template,
        containerEl: this._options.element,
        fileContainerEl: this._options.listElement,
        button: this._options.button,
        imageGenerator: this._imageGenerator,
        classes: {
            hide: this._options.classes.hide,
            editable: this._options.classes.editable
        },
        placeholders: {
            waitUntilUpdate: this._options.thumbnails.placeholders.waitUntilResponse,
            thumbnailNotAvailable: this._options.thumbnails.placeholders.notAvailablePath,
            waitingForThumbnail: this._options.thumbnails.placeholders.waitingPath
        },
        text: this._options.text
    }), !qq.supportedFeatures.uploading || this._options.cors.expected&&!qq.supportedFeatures.uploadCors ? this._templating.renderFailure(this._options.messages.unsupportedBrowser) : (this._wrapCallbacks(), this._templating.render(), this._classes = this._options.classes, !this._options.button && this._templating.getButton() && (this._defaultButtonId = this._createUploadButton({
        element: this._templating.getButton()
    }).getButtonId()), this._setupClickAndEditEventHandlers(), qq.DragAndDrop && qq.supportedFeatures.fileDrop && (this._dnd = this._setupDragAndDrop()), this._options.paste.targetElement && this._options.paste.promptForName && (qq.PasteSupport ? this._setupPastePrompt() : qq.log("Paste support module not found.", "info")), this._totalFilesInBatch = 0, this._filesInBatchAddedToUi = 0)
}, qq.extend(qq.FineUploader.prototype, qq.basePublicApi), qq.extend(qq.FineUploader.prototype, qq.basePrivateApi), qq.extend(qq.FineUploader.prototype, qq.uiPublicApi), qq.extend(qq.FineUploader.prototype, qq.uiPrivateApi), qq.Templating = function(a) {
    "use strict";
    function b() {
        var a, b, c, d, e, f, g, h, i;
        if (y("Parsing template"), null == N.templateIdOrEl)
            throw new Error("You MUST specify either a template element or ID!");
        if (qq.isString(N.templateIdOrEl)) {
            if (a = document.getElementById(N.templateIdOrEl), null === a)
                throw new Error(qq.format("Cannot find template script at ID '{}'!", N.templateIdOrEl));
            b = a.innerHTML
        } else {
            if (void 0 === N.templateIdOrEl.innerHTML)
                throw new Error("You have specified an invalid value for the template option!  It must be an ID or an Element.");
            b = N.templateIdOrEl.innerHTML
        }
        if (b = qq.trimStr(b), d = document.createElement("div")
            , d.appendChild(qq.toElement(b)), N.button && (f = qq(d).getByClass(O.button)[0], f && qq(f).remove()), qq.DragAndDrop && qq.supportedFeatures.fileDrop || (i = qq(d).getByClass(O.dropProcessing)[0], i && qq(i).remove()), g = qq(d).getByClass(O.drop)[0], g&&!qq.DragAndDrop && (qq.log("DnD module unavailable.", "info"), qq(g).remove()), g&&!qq.supportedFeatures.fileDrop && qq(g).hasAttribute(K) && qq(g).css({
            display: "none"
        }), h = qq(d).getByClass(O.thumbnail)[0], E ? h && (M = parseInt(h.getAttribute(I)), M = M > 0 ? M : null, F = qq(h).hasAttribute(J)) : h && qq(h).remove(), E = E && h, z = qq(d).getByClass(O.editFilenameInput).length > 0, A = qq(d).getByClass(O.retry).length > 0, c = qq(d).getByClass(O.list)[0], null == c)throw new Error("Could not find the file list container in the template!");
        return e = c.innerHTML, c.innerHTML = "", y("Template parsing complete"), {
            template: qq.trimStr(d.innerHTML), fileTemplate: qq.trimStr(e)
        }
    }
    function c(a) {
        return qq(D).getByClass(H + a)[0]
    }
    function d(a, b) {
        return qq(a).getByClass(b)[0]
    }
    function e(a, b) {
        var c = D, d = c.firstChild;
        b > 0 && (d = qq(c).children()[b].nextSibling), c.insertBefore(a, d)
    }
    function f(a) {
        return d(c(a), O.cancel)
    }
    function g(a) {
        return d(c(a), O.pause)
    }
    function h(a) {
        return d(c(a), O.continueButton)
    }
    function i(a) {
        return d(c(a), O.progressBarContainer) || d(c(a), O.progressBar)
    }
    function j(a) {
        return d(c(a), O.spinner)
    }
    function k(a) {
        return d(c(a), O.editNameIcon)
    }
    function l(a) {
        return d(c(a), O.size)
    }
    function m(a) {
        return d(c(a), O.deleteButton)
    }
    function n(a) {
        return d(c(a), O.retry)
    }
    function o(a) {
        return d(c(a), O.file)
    }
    function p() {
        return d(C, O.dropProcessing)
    }
    function q(a) {
        return E && d(c(a), O.thumbnail)
    }
    function r(a) {
        a && qq(a).addClass(N.classes.hide)
    }
    function s(a) {
        a && qq(a).removeClass(N.classes.hide)
    }
    function t(a, b) {
        var c = i(a);
        c&&!qq(c).hasClass(O.progressBar) && (c = qq(c).getByClass(O.progressBar)[0]), c && qq(c).css({
            width: b + "%"
        })
    }
    function u() {
        var a = N.placeholders.thumbnailNotAvailable, b = N.placeholders.waitingForThumbnail, c = {
            maxSize: M,
            scale: F
        };
        E && (a ? N.imageGenerator.generate(a, new Image, c).then(function(a) {
            Q.success(a)
        }, function() {
            Q.failure(), y("Problem loading 'not available' placeholder image at " + a, "error")
        }) : Q.failure(), b ? N.imageGenerator.generate(b, new Image, c).then(function(a) {
            R.success(a)
        }, function() {
            R.failure(), y("Problem loading 'waiting for thumbnail' placeholder image at " + b, "error")
        }) : R.failure())
    }
    function v(a) {
        R.then(function(b) {
            x(b, a), null == a.getAttribute("src") && (a.src = b.src, s(a))
        }, function() {
            r(a)
        })
    }
    function w(a, b) {
        var c = P[a] || (new qq.Promise).failure();
        Q.then(function(d) {
            c.then(function() {
                delete P[a]
            }, function() {
                x(d, b), b.src = d.src, s(b), delete P[a]
            })
        })
    }
    function x(a, b) {
        var c = a.style.maxWidth, d = a.style.maxHeight;
        d && c&&!b.style.maxWidth&&!b.style.maxHeight && qq(b).css({
            maxWidth: c,
            maxHeight: d
        })
    }
    var y, z, A, B, C, D, E, F, G = "qq-file-id", H = "qq-file-id-", I = "qq-max-size", J = "qq-server-scale", K = "qq-hide-dropzone", L=!1, M =- 1, N = {
        log: null,
        templateIdOrEl: "qq-template",
        containerEl: null,
        fileContainerEl: null,
        button: null,
        imageGenerator: null,
        classes: {
            hide: "qq-hide",
            editable: "qq-editable"
        },
        placeholders: {
            waitUntilUpdate: !1,
            thumbnailNotAvailable: null,
            waitingForThumbnail: null
        },
        text: {
            paused: "Paused"
        }
    }, O = {
        button: "qq-upload-button-selector",
        drop: "qq-upload-drop-area-selector",
        list: "qq-upload-list-selector",
        progressBarContainer: "qq-progress-bar-container-selector",
        progressBar: "qq-progress-bar-selector",
        file: "qq-upload-file-selector",
        spinner: "qq-upload-spinner-selector",
        size: "qq-upload-size-selector",
        cancel: "qq-upload-cancel-selector",
        pause: "qq-upload-pause-selector",
        continueButton: "qq-upload-continue-selector",
        deleteButton: "qq-upload-delete-selector",
        retry: "qq-upload-retry-selector",
        statusText: "qq-upload-status-text-selector",
        editFilenameInput: "qq-edit-filename-selector",
        editNameIcon: "qq-edit-filename-icon-selector",
        dropProcessing: "qq-drop-processing-selector",
        dropProcessingSpinner: "qq-drop-processing-spinner-selector",
        thumbnail: "qq-thumbnail-selector"
    }, P = {}, Q = new qq.Promise, R = new qq.Promise;
    qq.extend(N, a), y = N.log, C = N.containerEl, E = void 0 !== N.imageGenerator, B = b(), u(), qq.extend(this, {
        render: function() {
            y("Rendering template in DOM."), C.innerHTML = B.template, r(p()), D = N.fileContainerEl || d(C, O.list), y("Template rendering complete")
        },
        renderFailure: function(a) {
            var b = qq.toElement(a);
            C.innerHTML = "", C.appendChild(b)
        },
        reset: function() {
            this.render()
        },
        clearFiles: function() {
            D.innerHTML = ""
        },
        disableCancel: function() {
            L=!0
        },
        addFile: function(a, b, c) {
            var f = qq.toElement(B.fileTemplate), j = d(f, O.file);
            qq(f).addClass(H + a), j && qq(j).setText(b), f.setAttribute(G, a), c ? e(f, c.index) : D.appendChild(f), r(i(a)), r(l(a)), r(m(a)), r(n(a)), r(g(a)), r(h(a)), L && this.hideCancel(a)
        },
        removeFile: function(a) {
            qq(c(a)).remove()
        },
        getFileId: function(a) {
            var b = a;
            if (b) {
                for (; null == b.getAttribute(G);)
                    b = b.parentNode;
                return parseInt(b.getAttribute(G))
            }
        },
        getFileList: function() {
            return D
        },
        markFilenameEditable: function(a) {
            var b = o(a);
            b && qq(b).addClass(N.classes.editable)
        },
        updateFilename: function(a, b) {
            var c = o(a);
            c && qq(c).setText(b)
        },
        hideFilename: function(a) {
            r(o(a))
        },
        showFilename: function(a) {
            s(o(a))
        },
        isFileName: function(a) {
            return qq(a).hasClass(O.file)
        },
        getButton: function() {
            return N.button || d(C, O.button)
        },
        hideDropProcessing: function() {
            r(p())
        },
        showDropProcessing: function() {
            s(p())
        },
        getDropZone: function() {
            return d(C, O.drop)
        },
        isEditFilenamePossible: function() {
            return z
        },
        isRetryPossible: function() {
            return A
        },
        getFileContainer: function(a) {
            return c(a)
        },
        showEditIcon: function(a) {
            var b = k(a);
            b && qq(b).addClass(N.classes.editable)
        },
        hideEditIcon: function(a) {
            var b = k(a);
            b && qq(b).removeClass(N.classes.editable)
        },
        isEditIcon: function(a) {
            return qq(a).hasClass(O.editNameIcon)
        },
        getEditInput: function(a) {
            return d(c(a), O.editFilenameInput)
        },
        isEditInput: function(a) {
            return qq(a).hasClass(O.editFilenameInput)
        },
        updateProgress: function(a, b, c) {
            var d, e = i(a);
            e && (d = Math.round(100 * (b / c)), b === c ? r(e) : s(e), t(a, d))
        },
        hideProgress: function(a) {
            var b = i(a);
            b && r(b)
        },
        resetProgress: function(a) {
            t(a, 0)
        },
        showCancel: function(a) {
            if (!L) {
                var b = f(a);
                b && qq(b).removeClass(N.classes.hide)
            }
        },
        hideCancel: function(a) {
            r(f(a))
        },
        isCancel: function(a) {
            return qq(a).hasClass(O.cancel)
        },
        allowPause: function(a) {
            s(g(a)), r(h(a))
        },
        uploadPaused: function(a) {
            this.setStatusText(a, N.text.paused), this.allowContinueButton(a), r(j(a))
        },
        hidePause: function(a) {
            r(g(a))
        },
        isPause: function(a) {
            return qq(a).hasClass(O.pause)
        },
        isContinueButton: function(a) {
            return qq(a).hasClass(O.continueButton)
        },
        allowContinueButton: function(a) {
            s(h(a)), r(g(a))
        },
        uploadContinued: function(a) {
            this.setStatusText(a, ""), this.allowPause(a), s(j(a))
        },
        showDeleteButton: function(a) {
            s(m(a))
        },
        hideDeleteButton: function(a) {
            r(m(a))
        },
        isDeleteButton: function(a) {
            return qq(a).hasClass(O.deleteButton)
        },
        isRetry: function(a) {
            return qq(a).hasClass(O.retry)
        },
        updateSize: function(a, b) {
            var c = l(a);
            c && (s(c), qq(c).setText(b))
        },
        setStatusText: function(a, b) {
            var e = d(c(a), O.statusText);
            e && (null == b ? qq(e).clearText() : qq(e).setText(b))
        },
        hideSpinner: function(a) {
            r(j(a))
        },
        showSpinner: function(a) {
            s(j(a))
        },
        generatePreview: function(a, b) {
            var c = q(a), d = {
                maxSize: M,
                scale: !0,
                orient: !0
            };
            if (qq.supportedFeatures.imagePreviews) {
                if (P[a] = new qq.Promise, c)
                    return v(c), N.imageGenerator.generate(b, c, d).then(function() {
                    s(c), P[a].success()
                }, function() {
                    P[a].failure(), N.placeholders.waitUntilUpdate || w(a, c)
                })
            } else 
                c && v(c)
        },
        updateThumbnail: function(a, b, c) {
            var d = q(a), e = {
                maxSize: M,
                scale: F
            };
            if (d) {
                if (b)
                    return c && v(d), N.imageGenerator.generate(b, d, e).then(function() {
                    s(d)
                }, function() {
                    w(a, d)
                });
                w(a, d)
            }
        }
    })
}, qq.UploadHandlerForm = function(a, b) {
    "use strict";
    function c(a, b) {
        var c;
        try {
            var d = b.contentDocument || b.contentWindow.document, f = d.body.innerHTML;
            k("converting iframe's innerHTML to JSON"), k("innerHTML = " + f), f && f.match(/^<pre/i) && (f = d.body.firstChild.firstChild.nodeValue), c = e._parseJsonResponse(a, f)
        } catch (g) {
            k("Error when attempting to parse form upload response (" + g.message + ")", "error"), c = {
                success: !1
            }
        }
        return c
    }
    function d(b, c) {
        var d = a.paramsStore.get(b), f = a.demoMode ? "GET": "POST", g = a.endpointStore.get(b), j = h(b);
        return d[a.uuidName] = i(b), d[a.filenameParam] = j, e._initFormForUpload({
            method: f,
            endpoint: g,
            params: d,
            paramsInBody: a.paramsInBody,
            targetName: c.name
        })
    }
    var e = this, f = b.onUploadComplete, g = b.onUuidChanged, h = b.getName, i = b.getUuid, j = f, k = b.log;
    qq.extend(this, new qq.AbstractUploadHandlerForm({
        options: {
            isCors: a.cors.expected,
            inputName: a.inputName
        },
        proxy: {
            onCancel: a.onCancel,
            onUuidChanged: g,
            getName: h,
            getUuid: i,
            log: k
        }
    })), qq.extend(this, {
        upload: function(b) {
            var f, g = e._getFileState(b).input, i = h(b), l = e._createIframe(b);
            if (!g)
                throw new Error("file with passed id was not added, or already uploaded or canceled");
            a.onUpload(b, h(b)), f = d(b, l), f.appendChild(g), e._attachLoadEvent(l, function(d) {
                k("iframe loaded");
                var f = d ? d: c(b, l);
                e._detachLoadEvent(b), a.cors.expected || qq(l).remove(), (f.success ||!a.onAutoRetry(b, i, f)) && (a.onComplete(b, i, f), j(b))
            }), k("Sending upload request for " + b), f.submit(), qq(f).remove()
        }
    })
}, qq.UploadHandlerXhr = function(a, b) {
    "use strict";
    function c() {
        return null === a.resume.id || void 0 === a.resume.id || qq.isFunction(a.resume.id) || qq.isObject(a.resume.id) ? void 0 : a.resume.id
    }
    function d(b, c, d) {
        var e = I(b), f = G(b);
        c[a.chunking.paramNames.partIndex] = d.part, c[a.chunking.paramNames.partByteOffset] = d.start, c[a.chunking.paramNames.chunkSize] = d.size, c[a.chunking.paramNames.totalParts] = d.count, c[a.totalFileSizeName] = e, N && (c[a.filenameParam] = f)
    }
    function e(b) {
        b[a.resume.paramNames.resuming]=!0
    }
    function f(b, c, d, e) {
        var f = new FormData, g = a.demoMode ? "GET": "POST", h = a.endpointStore.get(e), i = h, j = G(e), k = I(e);
        return b[a.uuidName] = H(e), b[a.filenameParam] = j, N && (b[a.totalFileSizeName] = k), a.paramsInBody || (N || (b[a.inputName] = j), i = qq.obj2url(b, h)), c.open(g, i, !0), a.cors.expected && a.cors.sendCredentials && (c.withCredentials=!0), N ? (a.paramsInBody && qq.obj2FormData(b, f), f.append(a.inputName, d), f) : d
    }
    function g(b, c) {
        var d = a.customHeaders, e = O._getFileState(b).file || O._getFileState(b).blobData.blob;
        c.setRequestHeader("X-Requested-With", "XMLHttpRequest"), c.setRequestHeader("Cache-Control", "no-cache"), N || (c.setRequestHeader("Content-Type", "application/octet-stream"), c.setRequestHeader("X-Mime-Type", e.type)), qq.each(d, function(a, b) {
            c.setRequestHeader(a, b)
        })
    }
    function h(b, c, d) {
        var e = G(b), f = I(b);
        O._getFileState(b).attemptingResume=!1, a.onProgress(b, e, f, f), a.onComplete(b, e, c, d), O._getFileState(b) && delete O._getFileState(b).xhr, E(b)
    }
    function i(b) {
        var c, h, i = O._getFileState(b).remainingChunkIdxs[0], k = O._getChunkData(b, i), l = O._createXhr(b), m = I(b), n = G(b);
        void 0 === O._getFileState(b).loaded && (O._getFileState(b).loaded = 0), M && O._getFileState(b).file && t(b, k), l.onreadystatechange = s(b, l), l.upload.onprogress = function(c) {
            if (c.lengthComputable) {
                var d = c.loaded + O._getFileState(b).loaded, e = j(b, i, c.total);
                a.onProgress(b, n, d, e)
            }
        }, a.onUploadChunk(b, n, O._getChunkDataForCallback(k)), h = a.paramsStore.get(b), d(b, h, k), O._getFileState(b).attemptingResume && e(h), c = f(h, l, k.blob, b), g(b, l), J("Sending chunked upload request for item " + b + ": bytes " + (k.start + 1) + "-" + k.end + " of " + m), l.send(c)
    }
    function j(a, b, c) {
        var d = O._getChunkData(a, b), e = d.size, f = c - e, g = I(a), h = d.count, i = O._getFileState(a).initialRequestOverhead, j = f - i;
        return O._getFileState(a).lastRequestOverhead = f, 0 === b ? (O._getFileState(a).lastChunkIdxProgress = 0, O._getFileState(a).initialRequestOverhead = f, O._getFileState(a).estTotalRequestsSize = g + h * f) : O._getFileState(a).lastChunkIdxProgress !== b && (O._getFileState(a).lastChunkIdxProgress = b, O._getFileState(a).estTotalRequestsSize += j), O._getFileState(a).estTotalRequestsSize
    }
    function k(a) {
        return N ? O._getFileState(a).lastRequestOverhead : 0
    }
    function l(b, c, d) {
        var e = O._getFileState(b).remainingChunkIdxs.shift(), f = O._getChunkData(b, e);
        O._getFileState(b).attemptingResume=!1, O._getFileState(b).loaded += f.size + k(b), a.onUploadChunkSuccess(b, O._getChunkDataForCallback(f), c, d), O._getFileState(b).remainingChunkIdxs.length > 0 ? i(b) : (M && u(b), h(b, c, d))
    }
    function m(a, b) {
        return 200 !== a.status ||!b.success || b.reset
    }
    function n(a, b) {
        var c;
        try {
            J(qq.format("Received response status {} with body: {}", b.status, b.responseText)), c = qq.parseJson(b.responseText), void 0 !== c.newUuid && F(a, c.newUuid)
        } catch (d) {
            J("Error when attempting to parse xhr response text (" + d.message + ")", "error"), c = {}
        }
        return c
    }
    function o(a) {
        J("Server has ordered chunking effort to be restarted on next attempt for item ID " + a, "error"), M && (u(a), O._getFileState(a).attemptingResume=!1), O._getFileState(a).remainingChunkIdxs = [], delete O._getFileState(a).loaded, delete O._getFileState(a).estTotalRequestsSize, delete O._getFileState(a).initialRequestOverhead
    }
    function p(a) {
        O._getFileState(a).attemptingResume=!1, J("Server has declared that it cannot handle resume for item ID " + a + " - starting from the first chunk", "error"), o(a), O.upload(a, !0)
    }
    function q(b, c, d) {
        var e = G(b);
        a.onAutoRetry(b, e, c, d) || (200 !== d.status && (c.success=!1), h(b, c, d))
    }
    function r(a, b) {
        var c, d = O._getFileState(a), e = d && d.attemptingResume, f = d && d.paused;
        d&&!f && (J("xhr - server response received for " + a), J("responseText = " + b.responseText), c = n(a, b), m(b, c) ? (c.reset && o(a), e && c.reset ? p(a) : q(a, c, b)) : L ? l(a, c, b) : h(a, c, b))
    }
    function s(a, b) {
        return function() {
            4 === b.readyState && r(a, b)
        }
    }
    function t(b, c) {
        var d = H(b), e = O._getFileState(b).loaded, f = O._getFileState(b).initialRequestOverhead, g = O._getFileState(b).estTotalRequestsSize, h = w(b), i = d + K + c.part + K + e + K + f + K + g, j = a.resume.cookiesExpireIn;
        qq.setCookie(h, i, j)
    }
    function u(a) {
        if (O._getFileState(a).file) {
            var b = w(a);
            qq.deleteCookie(b)
        }
    }
    function v(a) {
        var b, c, d, e, f, g, h = qq.getCookie(w(a)), i = G(a);
        if (h) {
            if (b = h.split(K), 5 === b.length)
                return c = b[0], d = parseInt(b[1], 10), e = parseInt(b[2], 10), f = parseInt(b[3], 10), g = parseInt(b[4], 10), {
                uuid: c, part: d, lastByteSent: e, initialRequestOverhead: f, estTotalRequestsSize: g
            };
            J("Ignoring previously stored resume/chunk cookie for " + i + " - old cookie format", "warn")
        }
    }
    function w(b) {
        var c, d = G(b), e = I(b), f = a.chunking.partSize;
        return c = "qqfilechunk" + K + encodeURIComponent(d) + K + e + K + f, void 0 !== D && (c += K + D), c
    }
    function x(a, b) {
        var c;
        for (c = O._getTotalChunks(a)-1; c >= b; c -= 1)
            O._getFileState(a).remainingChunkIdxs.unshift(c);
        i(a)
    }
    function y(a, b, c, d) {
        c = d.part, O._getFileState(a).loaded = d.lastByteSent, O._getFileState(a).estTotalRequestsSize = d.estTotalRequestsSize, O._getFileState(a).initialRequestOverhead = d.initialRequestOverhead, O._getFileState(a).attemptingResume=!0, J("Resuming " + b + " at partition index " + c), x(a, c)
    }
    function z(b, c, d) {
        var e, f = G(b), g = O._getChunkData(b, c.part);
        e = a.onResume(b, f, O._getChunkDataForCallback(g)), e instanceof qq.Promise ? (J("Waiting for onResume promise to be fulfilled for " + b), e.then(function() {
            y(b, f, d, c)
        }, function() {
            J("onResume promise fulfilled - failure indicated.  Will not resume."), x(b, d)
        })) : e!==!1 ? y(b, f, d, c) : (J("onResume callback returned false.  Will not resume."), x(b, d))
    }
    function A(a, b) {
        var c, d = 0;
        O._getFileState(a).remainingChunkIdxs && 0 !== O._getFileState(a).remainingChunkIdxs.length ? i(a) : (O._getFileState(a).remainingChunkIdxs = [], M&&!b && O._getFileState(a).file ? (c = v(a), c ? z(a, c, d) : x(a, d)) : x(a, d))
    }
    function B(b) {
        var c, d, e, h = O._getFileState(b).file || O._getFileState(b).blobData.blob, i = G(b);
        O._getFileState(b).loaded = 0, c = O._createXhr(b), c.upload.onprogress = function(c) {
            c.lengthComputable && (O._getFileState(b).loaded = c.loaded, a.onProgress(b, i, c.loaded, c.total))
        }, c.onreadystatechange = s(b, c), d = a.paramsStore.get(b), e = f(d, c, h, b), g(b, c), J("Sending upload request for " + b), c.send(e)
    }
    function C(b, c) {
        var d = G(b);
        O.isValid(b) && (a.onUpload(b, d), L ? A(b, c) : B(b))
    }
    var D, E = b.onUploadComplete, F = b.onUuidChanged, G = b.getName, H = b.getUuid, I = b.getSize, J = b.log, K = "|", L = a.chunking.enabled && qq.supportedFeatures.chunking, M = a.resume.enabled && L && qq.supportedFeatures.resume, N = a.forceMultipart || a.paramsInBody, O = this;
    D = c(), qq.extend(this, new qq.AbstractUploadHandlerXhr({
        options: {
            chunking: L ? a.chunking: null
        },
        proxy: {
            onUpload: C,
            onCancel: a.onCancel,
            onUuidChanged: F,
            getName: G,
            getSize: I,
            getUuid: H,
            log: J
        }
    })), qq.override(this, function(b) {
        return {
            add: function(a) {
                var c;
                return b.add.apply(this, arguments), M && (c = v(a), c && F(a, c.uuid)), a
            },
            getResumableFilesData: function() {
                var b = [], c = [];
                return L && M ? (b = void 0 === D ? qq.getCookieNames(new RegExp("^qqfilechunk\\" + K + ".+\\" + K + "\\d+\\" + K + a.chunking.partSize + "=")) : qq.getCookieNames(new RegExp("^qqfilechunk\\" + K + ".+\\" + K + "\\d+\\" + K + a.chunking.partSize + "\\" + K + D + "=")), qq.each(b, function(a, b) {
                    var d = b.split(K), e = qq.getCookie(b).split(K);
                    c.push({
                        name: decodeURIComponent(d[1]),
                        size: d[2],
                        uuid: e[0],
                        partIdx: e[1]
                    })
                }), c) : []
            },
            expunge: function(a) {
                M && u(a), b.expunge(a)
            }
        }
    })
}, qq.PasteSupport = function(a) {
    "use strict";
    function b(a) {
        return a.type && 0 === a.type.indexOf("image/")
    }
    function c() {
        qq(e.targetElement).attach("paste", function(a) {
            var c = a.clipboardData;
            c && qq.each(c.items, function(a, c) {
                if (b(c)) {
                    var d = c.getAsFile();
                    e.callbacks.pasteReceived(d)
                }
            })
        })
    }
    function d() {
        f && f()
    }
    var e, f;
    e = {
        targetElement: null,
        callbacks: {
            log: function() {},
            pasteReceived: function() {}
        }
    }, qq.extend(e, a), c(), qq.extend(this, {
        reset: function() {
            d()
        }
    })
}, qq.DragAndDrop = function(a) {
    "use strict";
    function b(a, b) {
        var c = Array.prototype.slice.call(a);
        i.callbacks.dropLog("Grabbed " + a.length + " dropped files."), b.dropDisabled(!1), i.callbacks.processingDroppedFilesComplete(c, b.getElement())
    }
    function c(a) {
        var b, d = new qq.Promise;
        return a.isFile ? a.file(function(a) {
            m.push(a), d.success()
        }, function(b) {
            i.callbacks.dropLog("Problem parsing '" + a.fullPath + "'.  FileError code " + b.code + ".", "error"), d.failure()
        }) : a.isDirectory && (b = a.createReader(), b.readEntries(function(a) {
            var b = a.length;
            qq.each(a, function(a, e) {
                c(e).done(function() {
                    b -= 1, 0 === b && d.success()
                })
            }), a.length || d.success()
        }, function(b) {
            i.callbacks.dropLog("Problem parsing '" + a.fullPath + "'.  FileError code " + b.code + ".", "error"), d.failure()
        })), d
    }
    function d(a, b) {
        var d = [], e = new qq.Promise;
        return i.callbacks.processingDroppedFiles(), b.dropDisabled(!0), a.files.length > 1&&!i.allowMultipleItems ? (i.callbacks.processingDroppedFilesComplete([]), i.callbacks.dropError("tooManyFilesError", ""), b.dropDisabled(!1), e.failure()) : (m = [], qq.isFolderDropSupported(a) ? qq.each(a.items, function(a, b) {
            var f = b.webkitGetAsEntry();
            f && (f.isFile ? m.push(b.getAsFile()) : d.push(c(f).done(function() {
                d.pop(), 0 === d.length && e.success()
            })))
        }) : m = a.files, 0 === d.length && e.success()), e
    }
    function e(a) {
        var c = new qq.UploadDropZone({
            HIDE_ZONES_EVENT_NAME: j,
            element: a,
            onEnter: function(b) {
                qq(a).addClass(i.classes.dropActive), b.stopPropagation()
            },
            onLeaveNotDescendants: function() {
                qq(a).removeClass(i.classes.dropActive)
            },
            onDrop: function(a) {
                d(a.dataTransfer, c).then(function() {
                    b(m, c)
                }, function() {
                    i.callbacks.dropLog("Drop event DataTransfer parsing failed.  No files will be uploaded.", "error")
                })
            }
        });
        return n.addDisposer(function() {
            c.dispose()
        }), qq(a).hasAttribute(k) && qq(a).hide(), l.push(c), c
    }
    function f(a) {
        var b;
        return qq.each(a.dataTransfer.types, function(a, c) {
            return "Files" === c ? (b=!0, !1) : void 0
        }), b
    }
    function g(a) {
        return (qq.chrome() || qq.safari() && qq.windows()) && 0 == a.clientX && 0 == a.clientY || qq.firefox()&&!a.relatedTarget
    }
    function h() {
        var a = i.dropZoneElements;
        qq.each(a, function(b, c) {
            var d = e(c);
            !a.length || qq.ie()&&!qq.ie10() || n.attach(document, "dragenter", function(b) {
                !d.dropDisabled() && f(b) && qq.each(a, function(a, b) {
                    b instanceof HTMLElement && qq(b).css({
                        display: "block"
                    })
                })
            })
        }), n.attach(document, "dragleave", function(b) {
            g(b) && qq.each(a, function(a, b) {
                qq(b).hasAttribute(k) && qq(b).hide()
            })
        }), n.attach(document, "drop", function(b) {
            qq.each(a, function(a, b) {
                qq(b).hasAttribute(k) && qq(b).hide()
            }), b.preventDefault()
        }), n.attach(document, j, function() {
            qq.each(i.dropZoneElements, function(a, b) {
                qq(b).hasAttribute(k) && qq(b).hide(), qq(b).removeClass(i.classes.dropActive)
            })
        })
    }
    var i, j = "qq-hidezones", k = "qq-hide-dropzone", l = [], m = [], n = new qq.DisposeSupport;
    i = {
        dropZoneElements: [],
        allowMultipleItems: !0,
        classes: {
            dropActive: null
        },
        callbacks: new qq.DragAndDrop.callbacks
    }, qq.extend(i, a, !0), h(), qq.extend(this, {
        setupExtraDropzone: function(a) {
            i.dropZoneElements.push(a), e(a)
        },
        removeDropzone: function(a) {
            var b, c = i.dropZoneElements;
            for (b in c)
                if (c[b] === a)
                    return c.splice(b, 1)
        },
        dispose: function() {
            n.dispose(), qq.each(l, function(a, b) {
                b.dispose()
            })
        }
    })
}, qq.DragAndDrop.callbacks = function() {
    "use strict";
    return {
        processingDroppedFiles: function() {},
        processingDroppedFilesComplete: function() {},
        dropError: function(a, b) {
            qq.log("Drag & drop error code '" + a + " with these specifics: '" + b + "'", "error")
        },
        dropLog: function(a, b) {
            qq.log(a, b)
        }
    }
}, qq.UploadDropZone = function(a) {
    "use strict";
    function b() {
        return qq.safari() || qq.firefox() && qq.windows()
    }
    function c() {
        k || (b ? l.attach(document, "dragover", function(a) {
            a.preventDefault()
        }) : l.attach(document, "dragover", function(a) {
            a.dataTransfer && (a.dataTransfer.dropEffect = "none", a.preventDefault())
        }), k=!0)
    }
    function d(a) {
        if (qq.ie()&&!qq.ie10())
            return !1;
        var b, c = a.dataTransfer, d = qq.safari();
        return b = qq.ie10() || qq.ie11()?!0 : "none" !== c.effectAllowed, c && b && (c.files ||!d && c.types.contains && c.types.contains("Files"))
    }
    function e(a) {
        return void 0 !== a && (j = a), j
    }
    function f() {
        function a() {
            b = document.createEvent("Event"), b.initEvent(h.HIDE_ZONES_EVENT_NAME, !0, !0)
        }
        var b;
        if (window.CustomEvent)
            try {
                b = new CustomEvent(h.HIDE_ZONES_EVENT_NAME)
        } catch (c) {
            a()
        } else 
            a();
        document.dispatchEvent(b)
    }
    function g() {
        l.attach(i, "dragover", function(a) {
            if (d(a)) {
                var b = qq.ie() || qq.ie11() ? null: a.dataTransfer.effectAllowed;
                a.dataTransfer.dropEffect = "move" === b || "linkMove" === b ? "move" : "copy", a.stopPropagation(), a.preventDefault()
            }
        }), l.attach(i, "dragenter", function(a) {
            if (!e()) {
                if (!d(a))
                    return;
                h.onEnter(a)
            }
        }), l.attach(i, "dragleave", function(a) {
            if (d(a)) {
                h.onLeave(a);
                var b = document.elementFromPoint(a.clientX, a.clientY);
                qq(this).contains(b) || h.onLeaveNotDescendants(a)
            }
        }), l.attach(i, "drop", function(a) {
            if (!e()) {
                if (!d(a))
                    return;
                a.preventDefault(), a.stopPropagation(), h.onDrop(a), f()
            }
        })
    }
    var h, i, j, k, l = new qq.DisposeSupport;
    h = {
        element: null,
        onEnter: function() {},
        onLeave: function() {},
        onLeaveNotDescendants: function() {},
        onDrop: function() {}
    }, qq.extend(h, a), i = h.element, c(), g(), qq.extend(this, {
        dropDisabled: function(a) {
            return e(a)
        },
        dispose: function() {
            l.dispose()
        },
        getElement: function() {
            return i
        }
    })
}, qq.DeleteFileAjaxRequester = function(a) {
    "use strict";
    function b() {
        return "POST" === d.method.toUpperCase() ? {
            _method: "DELETE"
        } : {}
    }
    var c, d = {
        method: "DELETE",
        uuidParamName: "qquuid",
        endpointStore: {},
        maxConnections: 3,
        customHeaders: {},
        paramsStore: {},
        demoMode: !1,
        cors: {
            expected: !1,
            sendCredentials: !1
        },
        log: function() {},
        onDelete: function() {},
        onDeleteComplete: function() {}
    };
    qq.extend(d, a), c = qq.extend(this, new qq.AjaxRequester({
        validMethods: ["POST", "DELETE"],
        method: d.method,
        endpointStore: d.endpointStore,
        paramsStore: d.paramsStore,
        mandatedParams: b(),
        maxConnections: d.maxConnections,
        customHeaders: d.customHeaders,
        demoMode: d.demoMode,
        log: d.log,
        onSend: d.onDelete,
        onComplete: d.onDeleteComplete,
        cors: d.cors
    })), qq.extend(this, {
        sendDelete: function(a, b, e) {
            var f = e || {};
            d.log("Submitting delete file request for " + a), "DELETE" === d.method ? c.initTransport(a).withPath(b).withParams(f).send() : (f[d.uuidParamName] = b, c.initTransport(a).withParams(f).send())
        }
    })
}, function() {
    function a(a) {
        var b = a.naturalWidth, c = a.naturalHeight;
        if (b * c > 1048576) {
            var d = document.createElement("canvas");
            d.width = d.height = 1;
            var e = d.getContext("2d");
            return e.drawImage(a, - b + 1, 0), 0 === e.getImageData(0, 0, 1, 1).data[3]
        }
        return !1
    }
    function b(a, b, c) {
        var d = document.createElement("canvas");
        d.width = 1, d.height = c;
        var e = d.getContext("2d");
        e.drawImage(a, 0, 0);
        for (var f = e.getImageData(0, 0, 1, c).data, g = 0, h = c, i = c; i > g;) {
            var j = f[4 * (i-1) + 3];
            0 === j ? h = i : g = i, i = h + g>>1
        }
        var k = i / c;
        return 0 === k ? 1 : k
    }
    function c(a, b, c) {
        var e = document.createElement("canvas"), f = b.mime || "image/jpeg";
        return d(a, e, b, c), e.toDataURL(f, b.quality || .8)
    }
    function d(c, d, f, g) {
        var h = c.naturalWidth, i = c.naturalHeight, j = f.width, k = f.height, l = d.getContext("2d");
        if (l.save(), e(d, j, k, f.orientation)
            , qq.ios()) {
            var m = a(c);
            m && (h/=2, i/=2);
            var n = 1024, o = document.createElement("canvas");
            o.width = o.height = n;
            for (var p = o.getContext("2d"), q = g ? b(c, h, i) : 1, r = Math.ceil(n * j / h), s = Math.ceil(n * k / i / q), t = 0, u = 0; i > t;) {
                for (var v = 0, w = 0; h > v;)
                    p.clearRect(0, 0, n, n), p.drawImage(c, - v, - t), l.drawImage(o, 0, 0, n, n, w, u, r, s), v += n, w += r;
                t += n, u += s
            }
            l.restore(), o = p = null
        } else 
            l.drawImage(c, 0, 0, j, k)
    }
    function e(a, b, c, d) {
        switch (d) {
        case 5:
        case 6:
        case 7:
        case 8:
            a.width = c, a.height = b;
            break;
        default:
            a.width = b, a.height = c
        }
        var e = a.getContext("2d");
        switch (d) {
        case 2:
            e.translate(b, 0), e.scale(-1, 1);
            break;
        case 3:
            e.translate(b, c), e.rotate(Math.PI);
            break;
        case 4:
            e.translate(0, c), e.scale(1, -1);
            break;
        case 5:
            e.rotate(.5 * Math.PI), e.scale(1, -1);
            break;
        case 6:
            e.rotate(.5 * Math.PI), e.translate(0, - c);
            break;
        case 7:
            e.rotate(.5 * Math.PI), e.translate(b, - c), e.scale(-1, 1);
            break;
        case 8:
            e.rotate( - .5 * Math.PI), e.translate( - b, 0)
        }
    }
    function f(a, b) {
        if (window.Blob && a instanceof Blob) {
            var c = new Image, d = window.URL && window.URL.createObjectURL ? window.URL: window.webkitURL && window.webkitURL.createObjectURL ? window.webkitURL: null;
            if (!d)
                throw Error("No createObjectURL function found to create blob url");
            c.src = d.createObjectURL(a), this.blob = a, a = c
        }
        if (!a.naturalWidth&&!a.naturalHeight) {
            var e = this;
            a.onload = function() {
                var a = e.imageLoadListeners;
                if (a) {
                    e.imageLoadListeners = null;
                    for (var b = 0, c = a.length; c > b; b++)
                        a[b]()
                }
            }, a.onerror = b, this.imageLoadListeners = []
        }
        this.srcImage = a
    }
    f.prototype.render = function(a, b) {
        if (this.imageLoadListeners) {
            var e = this;
            return this.imageLoadListeners.push(function() {
                e.render(a, b)
            }), void 0
        }
        b = b || {};
        var f = this.srcImage.naturalWidth, g = this.srcImage.naturalHeight, h = b.width, i = b.height, j = b.maxWidth, k = b.maxHeight, l=!this.blob || "image/jpeg" === this.blob.type;
        h&&!i ? i = g * h / f<<0 : i&&!h ? h = f * i / g<<0 : (h = f, i = g), j && h > j && (h = j, i = g * h / f<<0), k && i > k && (i = k, h = f * i / g<<0);
        var m = {
            width: h,
            height: i
        };
        for (var n in b)
            m[n] = b[n];
        var o = a.tagName.toLowerCase();
        "img" === o ? a.src = c(this.srcImage, m, l) : "canvas" === o && d(this.srcImage, a, m, l), "function" == typeof this.onrender && this.onrender(a)
    }, "function" == typeof define && define.amd ? define([], function() {
        return f
    }) : this.MegaPixImage = f
}(), qq.ImageGenerator = function(a) {
    "use strict";
    function b(a) {
        return "img" === a.tagName.toLowerCase()
    }
    function c(a) {
        return "canvas" === a.tagName.toLowerCase()
    }
    function d() {
        return void 0 !== (new Image).crossOrigin
    }
    function e() {
        var a = document.createElement("canvas");
        return a.getContext && a.getContext("2d")
    }
    function f(a) {
        var b = a.split("/"), c = b[b.length-1], d = qq.getExtension(c);
        switch (d = d && d.toLowerCase()) {
        case"jpeg":
        case"jpg":
            return "image/jpeg";
        case"png":
            return "image/png";
        case"bmp":
            return "image/bmp";
        case"gif":
            return "image/gif";
        case"tiff":
        case"tif":
            return "image/tiff"
        }
    }
    function g(a) {
        var b, c, d, e = document.createElement("a");
        return e.href = a, b = e.protocol, d = e.port, c = e.hostname, b.toLowerCase() !== window.location.protocol.toLowerCase()?!0 : c.toLowerCase() !== window.location.hostname.toLowerCase()?!0 : d === window.location.port || qq.ie()?!1 : !0
    }
    function h(b, c) {
        b.onload = function() {
            b.onload = null, b.onerror = null, c.success(b)
        }, b.onerror = function() {
            b.onload = null, b.onerror = null, a("Problem drawing thumbnail!", "error"), c.failure(b, "Problem drawing thumbnail!")
        }
    }
    function i(a, b) {
        var c = a.getContext("2d"), d = c.drawImage;
        c.drawImage = function() {
            d.apply(this, arguments), b.success(a), c.drawImage = d
        }
    }
    function j(d, e) {
        var f = b(d) || c(d);
        return b(d) ? h(d, e) : c(d) ? i(d, e) : (e.failure(d), a(qq.format("Element container of type {} is not supported!", d.tagName), "error")), f
    }
    function k(b, c, d) {
        var e = new qq.Promise, f = new qq.Identify(b, a), g = d.maxSize, h = function() {
            c.onerror = null, c.onload = null, a("Could not render preview, file may be too large!", "error"), e.failure(c, "Browser cannot render image!")
        };
        return f.isPreviewable().then(function(d) {
            var f = new qq.Exif(b, a), i = new MegaPixImage(b, h);
            j(c, e) && f.parse().then(function(a) {
                var b = a.Orientation;
                i.render(c, {
                    maxWidth: g,
                    maxHeight: g,
                    orientation: b,
                    mime: d
                })
            }, function(b) {
                a(qq.format("EXIF data could not be parsed ({}).  Assuming orientation = 1.", b)), i.render(c, {
                    maxWidth: g,
                    maxHeight: g,
                    mime: d
                })
            })
        }, function() {
            a("Not previewable"), e.failure(c, "Not previewable")
        }), e
    }
    function l(a, b, c, d) {
        var e = new Image, h = new qq.Promise;
        j(e, h), g(a) && (e.crossOrigin = "anonymous"), e.src = a, h.then(function() {
            j(b, c);
            var g = new MegaPixImage(e);
            g.render(b, {
                maxWidth: d,
                maxHeight: d,
                mime: f(a)
            })
        })
    }
    function m(a, b, c, d) {
        j(b, c), qq(b).css({
            maxWidth: d + "px",
            maxHeight: d + "px"
        }), b.src = a
    }
    function n(a, f, h) {
        var i = new qq.Promise, k = h.scale, n = k ? h.maxSize: null;
        return k && b(f) ? e() ? g(a)&&!d() ? m(a, f, i, n) : l(a, f, i, n) : m(a, f, i, n) : c(f) ? l(a, f, i, n) : j(f, i) && (f.src = a), i
    }
    qq.extend(this, {
        generate: function(b, c, d) {
            return qq.isString(b) ? (a("Attempting to update thumbnail based on server response."), n(b, c, d || {})) : (a("Attempting to draw client-side image preview."), k(b, c, d || {}))
        }
    }), this._testing = {}, this._testing.isImg = b, this._testing.isCanvas = c, this._testing.isCrossOrigin = g, this._testing.determineMimeOfFileName = f
}, qq.Exif = function(a, b) {
    "use strict";
    function c(a) {
        for (var b = 0, c = 0; a.length > 0;)
            b += parseInt(a.substring(0, 2), 16) * Math.pow(2, c), a = a.substring(2, a.length), c += 8;
        return b
    }
    function d(b, c) {
        var e = b, f = c;
        return void 0 === e && (e = 2, f = new qq.Promise), qq.readBlobToHex(a, e, 4).then(function(a) {
            var b = /^ffe([0-9])/.exec(a);
            if (b)
                if ("1" !== b[1]) {
                    var c = parseInt(a.slice(4, 8), 16);
                    d(e + c + 2, f)
                } else 
                    f.success(e);
            else 
                f.failure("No EXIF header to be found!")
        }), f
    }
    function e() {
        var b = new qq.Promise;
        return qq.readBlobToHex(a, 0, 6).then(function(a) {
            0 !== a.indexOf("ffd8") ? b.failure("Not a valid JPEG!") : d().then(function(a) {
                b.success(a)
            }, function(a) {
                b.failure(a)
            })
        }), b
    }
    function f(b) {
        var c = new qq.Promise;
        return qq.readBlobToHex(a, b + 10, 2).then(function(a) {
            c.success("4949" === a)
        }), c
    }
    function g(b, d) {
        var e = new qq.Promise;
        return qq.readBlobToHex(a, b + 18, 2).then(function(a) {
            return d ? e.success(c(a)) : (e.success(parseInt(a, 16)), void 0)
        }), e
    }
    function h(b, c) {
        var d = b + 20, e = 12 * c;
        return qq.readBlobToHex(a, d, e)
    }
    function i(a) {
        for (var b = [], c = 0; c + 24 <= a.length;)
            b.push(a.slice(c, c + 24)), c += 24;
        return b
    }
    function j(a, b) {
        var d = 16, e = qq.extend([], k), f = {};
        return qq.each(b, function(b, g) {
            var h, i, j, k = g.slice(0, 4), m = a ? c(k): parseInt(k, 16), n = e.indexOf(m);
            return n >= 0 && (i = l[m].name, j = l[m].bytes, h = g.slice(d, d + 2 * j), f[i] = a ? c(h) : parseInt(h, 16), e.splice(n, 1)), 0 === e.length?!1 : void 0
        }), f
    }
    var k = [274], l = {
        274: {
            name: "Orientation",
            bytes: 2
        }
    };
    qq.extend(this, {
        parse: function() {
            var c = new qq.Promise, d = function(a) {
                b(qq.format("EXIF header parse failed: '{}' ", a)), c.failure(a)
            };
            return e().then(function(e) {
                b(qq.format("Moving forward with EXIF header parsing for '{}'", void 0 === a.name ? "blob" : a.name)), f(e).then(function(a) {
                    b(qq.format("EXIF Byte order is {} endian", a ? "little" : "big")), g(e, a).then(function(f) {
                        b(qq.format("Found {} APP1 directory entries", f)), h(e, f).then(function(d) {
                            var e = i(d), f = j(a, e);
                            b("Successfully parsed some EXIF tags"), c.success(f)
                        }, d)
                    }, d)
                }, d)
            }, d), c
        }
    }), this._testing = {}, this._testing.parseLittleEndian = c
}, qq.Identify = function(a, b) {
    "use strict";
    function c(a, b) {
        var c=!1, d = [].concat(a);
        return qq.each(d, function(a, d) {
            return 0 === b.indexOf(d) ? (c=!0, !1) : void 0
        }), c
    }
    var d = {
        "image/jpeg": "ffd8ff",
        "image/gif": "474946",
        "image/png": "89504e",
        "image/bmp": "424d",
        "image/tiff": ["49492a00", "4d4d002a"]
    };
    qq.extend(this, {
        isPreviewable: function() {
            var e = new qq.Promise, f=!1, g = void 0 === a.name ? "blob" : a.name;
            return b(qq.format("Attempting to determine if {} can be rendered in this browser", g)), qq.readBlobToHex(a, 0, 4).then(function(a) {
                qq.each(d, function(b, d) {
                    return c(d, a) ? (("image/tiff" !== b || qq.safari()) && (f=!0, e.success(b)), !1) : void 0
                }), b(qq.format("'{}' is {} able to be rendered in this browser", g, f ? "" : "NOT")), f || e.failure()
            }), e
        }
    })
}, qq.ImageValidation = function(a, b) {
    "use strict";
    function c(a) {
        var b=!1;
        return qq.each(a, function(a, c) {
            return c > 0 ? (b=!0, !1) : void 0
        }), b
    }
    function d() {
        var c = new qq.Promise;
        return new qq.Identify(a, b).isPreviewable().then(function() {
            var d = new Image, e = window.URL && window.URL.createObjectURL ? window.URL: window.webkitURL && window.webkitURL.createObjectURL ? window.webkitURL: null;
            e ? (d.onerror = function() {
                b("Cannot determine dimensions for image.  May be too large.", "error"), c.failure()
            }, d.onload = function() {
                c.success({
                    width : this.width, height: this.height
                })
            }, d.src = e.createObjectURL(a)) : (b("No createObjectURL function available to generate image URL!", "error"), c.failure())
        }, c.failure), c
    }
    function e(a, b) {
        var c;
        return qq.each(a, function(a, d) {
            if (d > 0) {
                var e = /(max|min)(Width|Height)/.exec(a), f = e[2].charAt(0).toLowerCase() + e[2].slice(1), g = b[f];
                switch (e[1]) {
                case"min":
                    if (d > g)
                        return c = a, !1;
                    break;
                case"max":
                    if (g > d)
                        return c = a, !1
                }
            }
        }), c
    }
    this.validate = function(a) {
        var f = new qq.Promise;
        return b("Attempting to validate image."), c(a) ? d().then(function(b) {
            var c = e(a, b);
            c ? f.failure(c) : f.success()
        }, f.success) : f.success(), f
    }
}, qq.Session = function(a) {
    "use strict";
    function b(a) {
        return qq.isArray(a)?!0 : (d.log("Session response is not an array.", "error"), void 0)
    }
    function c(a, c, e, f) {
        var g=!1;
        c = c && b(a), c && qq.each(a, function(a, b) {
            if (null == b.uuid)
                g=!0, d.log(qq.format("Session response item {} did not include a valid UUID - ignoring.", a), "error");
            else if (null == b.name)
                g=!0, d.log(qq.format("Session response item {} did not include a valid name - ignoring.", a), "error");
            else 
                try {
                    return d.addFileRecord(b), !0
            } catch (c) {
                g=!0, d.log(c.message, "error")
            }
            return !1
        }), f[c&&!g ? "success": "failure"](a, e)
    }
    var d = {
        endpoint: null,
        params: {},
        customHeaders: {},
        cors: {},
        addFileRecord: function() {},
        log: function() {}
    };
    qq.extend(d, a, !0), this.refresh = function() {
        var a = new qq.Promise, b = function(b, d, e) {
            c(b, d, e, a)
        }, e = qq.extend({}, d), f = new qq.SessionAjaxRequester(qq.extend(e, {
            onComplete: b
        }));
        return f.queryServer(), a
    }
}, qq.SessionAjaxRequester = function(a) {
    "use strict";
    function b(a, b, c) {
        var e = null;
        if (null != b.responseText)
            try {
                e = qq.parseJson(b.responseText)
        } catch (f) {
            d.log("Problem parsing session response: " + f.message, "error"), c=!0
        }
        d.onComplete(e, !c, b)
    }
    var c, d = {
        endpoint: null,
        customHeaders: {},
        params: {},
        cors: {
            expected: !1,
            sendCredentials: !1
        },
        onComplete: function() {},
        log: function() {}
    };
    qq.extend(d, a), c = qq.extend(this, new qq.AjaxRequester({
        validMethods: ["GET"],
        method: "GET",
        endpointStore: {
            get: function() {
                return d.endpoint
            }
        },
        customHeaders: d.customHeaders,
        log: d.log,
        onComplete: b,
        cors: d.cors
    })), qq.extend(this, {
        queryServer: function() {
            var a = qq.extend({}, d.params);
            d.log("Session query request."), c.initTransport("sessionRefresh").withParams(a).withCacheBuster().send()
        }
    })
}, qq.FormSupport = function(a, b, c) {
    "use strict";
    function d(a) {
        a.getAttribute("action") && (h.newEndpoint = a.getAttribute("action"))
    }
    function e(a, b) {
        return !a.checkValidity || a.checkValidity()?!0 : (c("Form did not pass validation checks - will not upload.", "error"), b(), void 0)
    }
    function f(a) {
        var c = a.submit;
        qq(a).attach("submit", function(d) {
            d = d || window.event, d.preventDefault ? d.preventDefault() : d.returnValue=!1, e(a, c) && b()
        }), a.submit = function() {
            e(a, c) && b()
        }
    }
    function g(a) {
        return a && (qq.isString(a) && (a = document.getElementById(a)), a && (c("Attaching to form element."), d(a), i && f(a))), a
    }
    var h = this, i = a.interceptSubmit, j = a.element, k = a.autoUpload;
    qq.extend(this, {
        newEndpoint: null,
        newAutoUpload: k,
        attachedToForm: !1,
        getFormInputsAsObject: function() {
            return null == j ? null : h._form2Obj(j)
        }
    }), j = g(j), this.attachedToForm=!!j
}, qq.extend(qq.FormSupport.prototype, {
    _form2Obj: function(a) {
        "use strict";
        var b = {}, c = function(a) {
            var b = ["button", "image", "reset", "submit"];
            return qq.indexOf(b, a.toLowerCase()) < 0
        }, d = function(a) {
            return qq.indexOf(["checkbox", "radio"], a.toLowerCase()) >= 0
        }, e = function(a) {
            return d(a.type)&&!a.checked?!0 : a.disabled && "hidden" !== a.type.toLowerCase()
        }, f = function(a) {
            var b = null;
            return qq.each(qq(a).children(), function(a, c) {
                return "option" === c.tagName.toLowerCase() && c.selected ? (b = c.value, !1) : void 0
            }), b
        };
        return qq.each(a.elements, function(a, d) {
            if (qq.isInput(d, !0) && c(d.type)&&!e(d))
                b[d.name] = d.value;
            else if ("select" === d.tagName.toLowerCase()&&!e(d)) {
                var g = f(d);
                null !== g && (b[d.name] = g)
            }
        }), b
    }
}), qq.UiEventHandler = function(a, b) {
    "use strict";
    function c(a) {
        d.attach(a, e.eventType, function(a) {
            a = a || window.event;
            var b = a.target || a.srcElement;
            e.onHandled(b, a)
        })
    }
    var d = new qq.DisposeSupport, e = {
        eventType: "click",
        attachTo: null,
        onHandled: function() {}
    };
    qq.extend(this, {
        addHandler: function(a) {
            c(a)
        },
        dispose: function() {
            d.dispose()
        }
    }), qq.extend(b, {
        getFileIdFromItem: function(a) {
            return a.qqFileId
        },
        getDisposeSupport: function() {
            return d
        }
    }), qq.extend(e, a), e.attachTo && c(e.attachTo)
}, qq.FileButtonsClickHandler = function(a) {
    "use strict";
    function b(a, b) {
        qq.each(e, function(c, e) {
            var f, g = c.charAt(0).toUpperCase() + c.slice(1);
            return d.templating["is" + g](a) ? (f = d.templating.getFileId(a), qq.preventDefault(b), d.log(qq.format("Detected valid file button click event on file '{}', ID: {}.", d.onGetName(f), f)), e(f), !1) : void 0
        })
    }
    var c = {}, d = {
        templating: null,
        log: function() {},
        onDeleteFile: function() {},
        onCancel: function() {},
        onRetry: function() {},
        onPause: function() {},
        onContinue: function() {},
        onGetName: function() {}
    }, e = {
        cancel: function(a) {
            d.onCancel(a)
        },
        retry: function(a) {
            d.onRetry(a)
        },
        deleteButton: function(a) {
            d.onDeleteFile(a)
        },
        pause: function(a) {
            d.onPause(a)
        },
        continueButton: function(a) {
            d.onContinue(a)
        }
    };
    qq.extend(d, a), d.eventType = "click", d.onHandled = b, d.attachTo = d.templating.getFileList(), qq.extend(this, new qq.UiEventHandler(d, c))
}, qq.FilenameClickHandler = function(a) {
    "use strict";
    function b(a, b) {
        if (d.templating.isFileName(a) || d.templating.isEditIcon(a)) {
            var e = d.templating.getFileId(a), f = d.onGetUploadStatus(e);
            f === qq.status.SUBMITTED && (d.log(qq.format("Detected valid filename click event on file '{}', ID: {}.", d.onGetName(e), e)), qq.preventDefault(b), c.handleFilenameEdit(e, a, !0))
        }
    }
    var c = {}, d = {
        templating: null,
        log: function() {},
        classes: {
            file: "qq-upload-file",
            editNameIcon: "qq-edit-filename-icon"
        },
        onGetUploadStatus: function() {},
        onGetName: function() {}
    };
    qq.extend(d, a), d.eventType = "click", d.onHandled = b, qq.extend(this, new qq.FilenameEditHandler(d, c))
}, qq.FilenameInputFocusInHandler = function(a, b) {
    "use strict";
    function c(a) {
        if (d.templating.isEditInput(a)) {
            var c = d.templating.getFileId(a), e = d.onGetUploadStatus(c);
            e === qq.status.SUBMITTED && (d.log(qq.format("Detected valid filename input focus event on file '{}', ID: {}.", d.onGetName(c), c)), b.handleFilenameEdit(c, a))
        }
    }
    var d = {
        templating: null,
        onGetUploadStatus: function() {},
        log: function() {}
    };
    b || (b = {}), d.eventType = "focusin", d.onHandled = c, qq.extend(d, a), qq.extend(this, new qq.FilenameEditHandler(d, b))
}, qq.FilenameInputFocusHandler = function(a) {
    "use strict";
    a.eventType = "focus", a.attachTo = null, qq.extend(this, new qq.FilenameInputFocusInHandler(a, {}))
}, qq.FilenameEditHandler = function(a, b) {
    "use strict";
    function c(a) {
        var b = h.onGetName(a), c = b.lastIndexOf(".");
        return c > 0 && (b = b.substr(0, c)), b
    }
    function d(a) {
        var b = h.onGetName(a);
        return qq.getExtension(b)
    }
    function e(a, b) {
        var c, e = a.value;
        void 0 !== e && qq.trimStr(e).length > 0 && (c = d(b), void 0 !== c && (e = e + "." + c), h.onSetName(b, e)), h.onEditingStatusChange(b, !1)
    }
    function f(a, c) {
        b.getDisposeSupport().attach(a, "blur", function() {
            e(a, c)
        })
    }
    function g(a, c) {
        b.getDisposeSupport().attach(a, "keyup", function(b) {
            var d = b.keyCode || b.which;
            13 === d && e(a, c)
        })
    }
    var h = {
        templating: null,
        log: function() {},
        onGetUploadStatus: function() {},
        onGetName: function() {},
        onSetName: function() {},
        onEditingStatusChange: function() {}
    };
    qq.extend(h, a), h.attachTo = h.templating.getFileList(), qq.extend(this, new qq.UiEventHandler(h, b)), qq.extend(b, {
        handleFilenameEdit: function(a, b, d) {
            var e = h.templating.getEditInput(a);
            h.onEditingStatusChange(a, !0), e.value = c(a), d && e.focus(), f(e, a), g(e, a)
        }
    })
}, function(a) {
    "use strict";
    function b(a) {
        var b = h(a || {}), d = c(b);
        return e(d), g(b, d), l
    }
    function c(a) {
        var b = f("uploaderType"), c = f("endpointType");
        return b ? (b = b.charAt(0).toUpperCase() + b.slice(1).toLowerCase(), c ? new qq[c]["FineUploader" + b](a) : new qq["FineUploader" + b](a)) : c ? new qq[c].FineUploader(a) : new qq.FineUploader(a)
    }
    function d(a, b) {
        var c = l.data("fineuploader");
        return b ? (void 0 === c && (c = {}), c[a] = b, l.data("fineuploader", c), void 0) : void 0 === c ? null : c[a]
    }
    function e(a) {
        return d("uploader", a)
    }
    function f(a, b) {
        return d(a, b)
    }
    function g(b, c) {
        var d = b.callbacks = {};
        a.each(c._options.callbacks, function(b, c) {
            var e, f;
            e = /^on(\w+)/.exec(b)[1], e = e.substring(0, 1).toLowerCase() + e.substring(1), f = l, d[b] = function() {
                var b, d, g = Array.prototype.slice.call(arguments), h = [];
                a.each(g, function(a, b) {
                    h.push(k(b))
                }), b = c.apply(this, g);
                try {
                    d = f.triggerHandler(e, h)
                } catch (i) {
                    qq.log("Caught error in Fine Uploader jQuery event handler: " + i.message, "error")
                }
                return null != b ? b : d
            }
        }), c._options.callbacks = d
    }
    function h(b, c) {
        var d, e;
        return d = void 0 === c ? "basic" !== b.uploaderType ? {
            element: l[0]
        } : {} : c, a.each(b, function(b, c) {
            a.inArray(b, m) >= 0 ? f(b, c) : c instanceof a ? d[b] = c[0] : a.isPlainObject(c) ? (d[b] = {}, h(c, d[b])) : a.isArray(c) ? (e = [], a.each(c, function(b, c) {
                var d = {};
                c instanceof a ? a.merge(e, c) : a.isPlainObject(c) ? (h(c, d), e.push(d)) : e.push(c)
            }), d[b] = e) : d[b] = c
        }), void 0 === c ? d : void 0
    }
    function i(b) {
        return "string" === a.type(b)&&!b.match(/^_/) && void 0 !== e()[b]
    }
    function j(a) {
        var b, c = [], d = Array.prototype.slice.call(arguments, 1);
        return h(d, c), b = e()[a].apply(e(), c), k(b)
    }
    function k(b) {
        var c = b;
        return null == b || "object" != typeof b || 1 !== b.nodeType && 9 !== b.nodeType ||!b.cloneNode || (c = a(b)), c
    }
    var l, m = ["uploaderType", "endpointType"];
    a.fn.fineUploader = function(c) {
        var d = this, f = arguments, g = [];
        return this.each(function(h, k) {
            if (l = a(k), e() && i(c)
                ) {
                if (g.push(j.apply(d, f)), 1 === d.length)
                    return !1
            } else 
                "object" != typeof c && c ? a.error("Method " + c + " does not exist on jQuery.fineUploader") : b.apply(d, f)
        }), 1 === g.length ? g[0] : g.length > 1 ? g : this
    }
}(jQuery), function(a) {
    "use strict";
    function b(a) {
        a || (a = {}), a.dropZoneElements = [i];
        var b = f(a);
        return e(b), d(new qq.DragAndDrop(b)), i
    }
    function c(a, b) {
        var c = i.data(j);
        return b ? (void 0 === c && (c = {}), c[a] = b, i.data(j, c), void 0) : void 0 === c ? null : c[a]
    }
    function d(a) {
        return c("dndInstance", a)
    }
    function e(b) {
        var c = b.callbacks = {};
        new qq.FineUploaderBasic, a.each(new qq.DragAndDrop.callbacks, function(a) {
            var b, d = a;
            b = i, c[a] = function() {
                var a = Array.prototype.slice.call(arguments), c = b.triggerHandler(d, a);
                return c
            }
        })
    }
    function f(b, c) {
        var d, e;
        return d = void 0 === c ? {} : c, a.each(b, function(b, c) {
            c instanceof a ? d[b] = c[0] : a.isPlainObject(c) ? (d[b] = {}, f(c, d[b])) : a.isArray(c) ? (e = [], a.each(c, function(b, c) {
                c instanceof a ? a.merge(e, c) : e.push(c)
            }), d[b] = e) : d[b] = c
        }), void 0 === c ? d : void 0
    }
    function g(b) {
        return "string" === a.type(b) && "dispose" === b && void 0 !== d()[b]
    }
    function h(a) {
        var b = [], c = Array.prototype.slice.call(arguments, 1);
        return f(c, b), d()[a].apply(d(), b)
    }
    var i, j = "fineUploaderDnd";
    a.fn.fineUploaderDnd = function(c) {
        var e = this, f = arguments, j = [];
        return this.each(function(k, l) {
            if (i = a(l), d() && g(c)
                ) {
                if (j.push(h.apply(e, f)), 1 === e.length)
                    return !1
            } else 
                "object" != typeof c && c ? a.error("Method " + c + " does not exist in Fine Uploader's DnD module.") : b.apply(e, f)
        }), 1 === j.length ? j[0] : j.length > 1 ? j : this
    }
}(jQuery);
/*! 2014-02-11 */

