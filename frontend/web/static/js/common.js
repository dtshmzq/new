$(document).ready(function () {
    //nav
    var obj = null;
    var As = document.getElementById('navlist').getElementsByTagName('a');
    obj = As[0];
    for (i = 1; i < As.length; i++) {
        if (window.location.href.indexOf(As[i].href) >= 0) {
            obj = As[i];
        }
    }
    obj.id = 'selected';

    //nav
    $('#mnavh').click(function () {
        $('#navlist').toggle();
        $('#mnavh').toggleClass('open');
    });

    //search
    $('.searchico').click(function () {
        $('.search').toggleClass('open');
    });

    //searchclose
    $('.searchclose').click(function () {
        $('.search').removeClass('open');
    });

    //banner
    $('#banner').easyFader();

    //nav menu
    $('.menu').click(function (event) {
        $(this).children('.sub').slideToggle();
    });

    //tab
    $('.tab_buttons li').click(function () {
        $(this).addClass('newscurrent').siblings().removeClass('newscurrent');
        $('.newstab>div:eq(' + $(this).index() + ')').show().siblings().hide();
    });


    //toolbar
    $('.toolbar-open').click(function () {
        $('.toolbar-open').addClass('openviewd');
        $('.toolbar').addClass('guanbi');
    });

    $('#guanbi').click(function () {
        $('.toolbar-open').removeClass('openviewd');
        $('.toolbar').removeClass('guanbi');
        $('#toolbar-menu li').removeClass('current');
    });

    //toolbar-menu
    $('#toolbar-menu li').click(function () {
        $(this).addClass('current').siblings().removeClass('current');
    });

    //endmenu
    $('.endmenu li a').each(function () {
        $this = $(this);
        if ($this[0].href == String(window.location)) {
            $this.parent().addClass('act');
        }
    });
});



(function (a) {
    function h(b) {
        for (var a = ['Webkit', 'Moz', 'O', 'ms'], c = 0; c < a.length; c++) {
            if (a[c] + 'Transition' in b.style) {
                return '-' + a[c].toLowerCase() + '-';
            }
        }
        return 'transition' in b.style ? '' : !1;
    }

    a.fn.removeStyle = function (b) {
        return this.each(function () {
            var h = a(this);
            b = b.replace(/\s+/g, '');
            var c = b.split(',');
            a.each(c, function () {
                var a = RegExp(this.toString() + '[^;]+;?', 'g');
                h.attr('style', function (b, c) {
                    if (c) {
                        return c.replace(a, '');
                    }
                });
            });
        });
    };
    a.fn.postLike = function () {
        if (a(this).hasClass('actived')) {
            return alert('已经点过赞啦！');
        } else {
            a(this).addClass('actived');
            var z = a(this).data('id'), y = a(this).data('action'), x = a(this).children('.count');
            var w = {aid: z};
            w[a('meta[name=csrf-param]').attr('content')] = a('meta[name=csrf-token]').attr('content');
            a.post(a(this).attr('like-url'), w, function (A) {
                a(x).html(A);
            });
            return false;
        }
    };
    a(document).on('click', '#Addlike', function () {
        a(this).postLike();
    });


    a(document).on('click', function (D) {
        D = D || window.event;
        var C = D.target || D.srcElement, A = a(C);
        if (A.hasClass('disabled')) {
            return;
        }
        if (A.parent().attr('data-type')) {
            A = a(A.parent()[0]);
        }
        if (A.parent().parent().attr('data-type')) {
            A = a(A.parent().parent()[0]);
        }
        var z = A.attr('data-type');
        switch (z) {
            case'comment-insert-smilie':
                if (!a('#comment-smilies').length) {
                    a('#commentform .comt-box').append('<div id="comment-smilies" class="hide"></div>');
                    var x = '';
                    for (key in g.smilies) {
                        x += '<img data-simle="' + key + '" data-type="comment-smilie" src="' + _deel.url + 'static/images/smilies/icon_' + g.smilies[key] + '.gif">';
                    }
                    a('#comment-smilies').html(x);
                }
                a('#comment-smilies').slideToggle(100);
                break;
            case'comment-smilie':
                u(A.attr('data-simle'));
                A.parent().slideUp(300);
                break;
            case'switch-author':
                a('.comt-comterinfo').slideToggle(300);
                a('#author').focus();
                break;
        }
    });
    var g = {
        smilies: {
            'mrgreen': 'mrgreen',
            'razz': 'razz',
            'sad': 'sad',
            'smile': 'smile',
            'oops': 'redface',
            'grin': 'biggrin',
            'eek': 'surprised',
            '???': 'confused',
            'cool': 'cool',
            'lol': 'lol',
            'mad': 'mad',
            'twisted': 'twisted',
            'roll': 'rolleyes',
            'wink': 'wink',
            'idea': 'idea',
            'arrow': 'arrow',
            'neutral': 'neutral',
            'cry': 'cry',
            '?': 'question',
            'evil': 'evil',
            'shock': 'eek',
            '!': 'exclaim'
        }
    };
    a('.commentlist .url').attr('target', '_blank');
    a('#comment-author-info p input').focus(function () {
        a(this).parent('p').addClass('on');
    });
    a('#comment-author-info p input').blur(function () {
        a(this).parent('p').removeClass('on');
    });
    a('#comment').focus(function () {
        if (a('#author').val() == '' || a('#email').val() == '') {
            a('.comt-comterinfo').slideDown(300);
        }
    });
    var m = '0', f = '<div class="comt-tip comt-loading">正在提交, 请稍候...</div>', c = '<div class="comt-tip comt-error">#</div>', b = '">提交成功', d = '取消编辑', o, k = 1,
        r = [];
    r.push('');
    $comments = a('#comments-title');
    $cancel = a('#cancel-comment-reply-link');
    cancel_text = $cancel.text();
    $submit = a('#commentform #submit');
    $submit.attr('disabled', false);
    a('.comt-tips').append(f + c);
    a('.comt-loading').hide();
    a('.comt-error').hide();
    $body = (window.opera) ? (document.compatMode == 'CSS1Compat' ? a('html') : a('body')) : a('html,body');
    a('#commentform').submit(function () {
        a('.comt-loading').show();
        $submit.attr('disabled', true).fadeTo('slow', 0.5);
        if (o) {
            a('#comment').after('<input type="text" name="edit_id" id="edit_id" value="' + o + '" style="display:none;" />');
        }
        a.ajax({
            url: _deel.comment_url, data: a(this).serialize(), type: a(this).attr('method'), error: function (w) {
                a('.comt-loading').hide();
                a('.comt-error').show().html(w.responseText);
                setTimeout(function () {
                    $submit.attr('disabled', false).fadeTo('slow', 1);
                    a('.comt-error').fadeOut();
                }, 3000);
            }, success: function (B) {
                a('.comt-loading').hide();
                r.push(a('#comment').val());
                a('textarea').each(function () {
                    this.value = '';
                });
                var y = addComment, A = y.I('cancel-comment-reply-link'), w = y.I('wp-temp-form-div'), C = y.I(y.respondId), x = y.I('comment_post_ID').value,
                    z = y.I('comment_parent').value;
                if (!o && $comments.length) {
                    n = parseInt($comments.text().match(/\d+/));
                    $comments.text($comments.text().replace(n, n + 1));
                }
                new_htm = '" id="new_comm_' + k + '"></';
                new_htm = (z == '0') ? ('<ol style="clear:both;" class="commentlist commentnew' + new_htm + 'ol>') : ('<ul class="children' + new_htm + 'ul>');
                ok_htm = '\n<span id="success_' + k + b;
                ok_htm += '</span><span></span>';
                if (z == '0') {
                    if (a('#postcomments .commentlist').length) {
                        a('#postcomments .commentlist').before(new_htm);
                    } else {
                        a('#respond').after(new_htm);
                    }
                } else {
                    a('#respond').after(new_htm);
                }
                a('#comment-author-info').slideUp();
                a('#new_comm_' + k).hide().append(B);
                a('#new_comm_' + k + ' li').append(ok_htm);
                a('#new_comm_' + k).fadeIn(4000);
                $body.animate({scrollTop: a('#new_comm_' + k).offset().top - 200}, 500);
                a('.comt-avatar .avatar').attr('src', a('.commentnew .avatar:last').attr('src'));
                l();
                k++;
                o = '';
                a('*').remove('#edit_id');
                A.style.display = 'none';
                A.onclick = null;
                y.I('comment_parent').value = '0';
                if (w && C) {
                    w.parentNode.insertBefore(C, w);
                    w.parentNode.removeChild(w);
                }
            }
        });
        return false;
    });
    addComment = {
        moveForm: function (z, A, E, y, B) {
            var I = this, w, D = I.I(z), x = I.I(E), H = I.I('cancel-comment-reply-link'), F = I.I('comment_parent'), G = I.I('comment_post_ID');
            if (o) {
                p();
            }
            B ? (I.I('comment').value = r[B], o = I.I('new_comm_' + B).innerHTML.match(/(comment-)(\d+)/)[2], $new_sucs = a('#success_' + B), $new_sucs.hide(), $new_comm = a('#new_comm_' + B), $new_comm.hide(), $cancel.text(d)) : $cancel.text(cancel_text);
            I.respondId = E;
            y = y || false;
            if (!I.I('wp-temp-form-div')) {
                w = document.createElement('div');
                w.id = 'wp-temp-form-div';
                w.style.display = 'none';
                x.parentNode.insertBefore(w, x);
            }
            !D ? (temp = I.I('wp-temp-form-div'), I.I('comment_parent').value = '0', temp.parentNode.insertBefore(x, temp), temp.parentNode.removeChild(temp)) : D.parentNode.insertBefore(x, D.nextSibling);
            $body.animate({scrollTop: a('#respond').offset().top - 180}, 400);
            if (G && y) {
                G.value = y;
            }
            F.value = A;
            H.style.display = '';
            H.onclick = function () {
                if (o) {
                    p();
                }
                var K = addComment, J = K.I('wp-temp-form-div'), L = K.I(K.respondId);
                K.I('comment_parent').value = '0';
                if (J && L) {
                    J.parentNode.insertBefore(L, J);
                    J.parentNode.removeChild(J);
                }
                this.style.display = 'none';
                this.onclick = null;
                return false;
            };
            try {
                I.I('comment').focus();
            } catch (C) {
            }
            return false;
        }, I: function (w) {
            return document.getElementById(w);
        }
    };

    function p() {
        $new_comm.show();
        $new_sucs.show();
        a('textarea').each(function () {
            this.value = '';
        });
        o = '';
    }

    var q = 15, j = $submit.val();

    function l() {
        if (q > 0) {
            $submit.val(q);
            q--;
            setTimeout(l, 1000);
        } else {
            $submit.val(j).attr('disabled', false).fadeTo('slow', 1);
            q = 15;
        }
    }

    function e(w, x) {
        if (!x) {
            x = 1000;
        }
        if (!w) {
            a('html,body').animate({scrollTop: 0}, x);
        } else {
            if (a(w).length > 0) {
                a('html,body').animate({scrollTop: a(w).offset().top}, x);
            }
        }
    }

    function u(w) {
        w = ' :' + w + ': ';
        myField = document.getElementById('comment');
        document.selection ? (myField.focus(), sel = document.selection.createRange(), sel.text = w, myField.focus()) : s(w);
    }

    function s(w) {
        myField = document.getElementById('comment');
        myField.selectionStart || myField.selectionStart == '0' ? (startPos = myField.selectionStart, endPos = myField.selectionEnd, cursorPos = startPos, myField.value = myField.value.substring(0, startPos) + w + myField.value.substring(endPos, myField.value.length), cursorPos += w.length, myField.focus(), myField.selectionStart = cursorPos, myField.selectionEnd = cursorPos) : (myField.value += w, myField.focus());
    }

    var t = function (b) {
        return this.each(function () {
            function n(a, b) {
                function j() {
                    f.eq(a).removeStyle('opacity, z-index');
                    f.eq(b).removeStyle(h + 'transition, transition');
                    k = b;
                    p = l = !1;
                    q = setTimeout(function () {
                        c('next');
                    }, d.slideDur);
                    'function' == typeof d.onFadeEnd && d.onFadeEnd.call(this, f.eq(k));
                }

                if (l || a == b) {
                    return !1;
                }
                l = !0;
                'function' == typeof d.onFadeStart && !p && d.onFadeStart.call(this, f.eq(e));
                r.removeClass('active').eq(e).addClass('active');
                f.eq(a).css('z-index', 2);
                f.eq(b).css('z-index', 3);
                if (h) {
                    var g = {};
                    g[h + 'transition'] = 'opacity ' + d.fadeDur + 'ms';
                    g.opacity = 1;
                    f.eq(b).css(g);
                    setTimeout(function () {
                        j();
                    }, d.fadeDur);
                } else {
                    f.eq(b).animate({opacity: 1}, d.fadeDur, function () {
                        j();
                    });
                }
            }

            function c(a) {
                'next' == a ? (e = k + 1, e > m - 1 && (e = 0)) : 'prev' == a ? (e = k - 1, 0 > e && (e = m - 1)) : e = a;
                n(k, e);
            }

            var d = {slideDur: 7E3, fadeDur: 800, onFadeStart: null, onFadeEnd: null};
            b && a.extend(d, b);
            this.config = d;
            var j = a(this), l = !1, p = !0, q, k, e, f = j.find('.slide'), m = f.length, s = j.find('.pager_list');
            h = a.support.leadingWhitespace ? h(j[0]) : !1;
            for (var g = 0; g < m; g++) {
                s.append('<li class="page" data-target="' + g + '">' + g + '</li>');
            }
            j.find('.page').bind('click', function () {
                var b = a(this).attr('data-target');
                clearTimeout(q);
                c(b);
            });
            var r = s.find('.page');
            r.eq(0).addClass('active');
            n(1, 0);
        });
    };
    a.fn.easyFader = function (a) {
        return t.apply(this, arguments);
    };
})(jQuery);



/* document.oncontextmenu = function (event) {
  if (window.event) {
    event = window.event;
  }
  try {
    var the = event.srcElement;
    if (!((the.tagName == "INPUT" && the.type.toLowerCase() == "text") || the.tagName == "TEXTAREA")) {
      return false;
    }
    return true;
  } catch (e) {
    return false;
  }
}

document.onpaste = function (event) {
  if (window.event) {
    event = window.event;
  }
  try {
    var the = event.srcElement;
    if (!((the.tagName == "INPUT" && the.type.toLowerCase() == "text") || the.tagName == "TEXTAREA")) {
      return false;
    }
    return true;
  } catch (e) {
    return false;
  }
}

document.oncut = function (event) {
  if (window.event) {
    event = window.event;
  }
  try {
    var the = event.srcElement;
    if (!((the.tagName == "INPUT" && the.type.toLowerCase() == "text") || the.tagName == "TEXTAREA")) {
      return false;
    }
    return true;
  } catch (e) {
    return false;
  }
}

function Forbid12() {
  window.close();
  window.location = "about:blank"; 
}

var arr = [123, 17, 18];
document.oncontextmenu = new Function("event.returnValue=false;"),
window.onkeydown = function (e) {
    var keyCode = e.keyCode || e.which || e.charCode;
    var ctrlKey = e.ctrlKey || e.metaKey;
    if (ctrlKey && keyCode == 85) {
      e.preventDefault();
    }
    if (arr.indexOf(keyCode) > -1) {
      e.preventDefault();
    }
}

function ck() {
  console.profile();
  console.profileEnd();
  if (console.clear) {
    console.clear()
  };
  if (typeof console.profiles == "object") {
    return console.profiles.length > 0;
  }
}
 
function JudgeProfiles() {
  if ((window.console && (console.firebug || console.table && /firebug/i.test(console.table()))) || (
      typeof opera ==
      'object' && typeof opera.postError == 'function' && console.profile.length > 0)) {
    Forbid12();
  }
  if (typeof console.profiles == "object" && console.profiles.length > 0) {
    Forbid12();
  }
}

JudgeProfiles();

window.onresize = function () {
  if ((window.outerHeight - window.innerHeight) > 200)
    Forbid12();
}
 
document.onkeydown = function (event) {
  if ((event.keyCode == 112) ||
    (event.keyCode == 113) ||
    (event.keyCode == 114) ||
    (event.keyCode == 115) ||
    // (event.keyCode == 116)
    (event.keyCode == 117) ||
    (event.keyCode == 118) ||
    (event.keyCode == 119) ||
    (event.keyCode == 120) ||
    (event.keyCode == 121) ||
    (event.keyCode == 122) ||
    (event.keyCode == 123))
  {
    return false;
  }
}

window.onhelp = function () {
  return false;
}

window.addEventListener('keydown', function (e) {
    if(e.keyCode == 83 && (navigator.platform.match('Mac') ? e.metaKey : e.ctrlKey)){
        e.preventDefault();
    }
} */)