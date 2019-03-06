/**
 * Created by Gallonce on 2016/12/26.
 * Updated by Gallonce on 2017/2/13.
 */

//全选的实现
$(".checkAll").click(function () {
    $(".checkItem").prop("checked", this.checked);
});
$(".checkItem").click(function () {
    var option = $(".checkItem");
    option.each(function (i) {
        if (!this.checked) {
            $(".checkAll").prop("checked", false);
            return false;
        } else {
            $(".checkAll").prop("checked", true);
        }
    });
});

//ajax get请求
$('.ajax-get').click(function () {
    var target;
    var that = this;
    if ($(this).hasClass('confirm')) {
        if (!confirm('确认要执行该操作吗?')) {
            return false;
        }
    }
    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        $.get(target).success(function (data) {
            if (data.code == 1) {
                if (data.url) {
                    updateAlert(data.msg + ' 页面即将自动跳转~', 'alert-success');
                } else {
                    updateAlert(data.msg, 'alert-success');
                }
                setTimeout(function () {
                    if (data.url && data.url !== 'javascript:history.back(-1);') {
                        location.href = data.url;
                    } else if ($(that).hasClass('no-refresh')) {
                        $('#top-alert').find('button').click();
                    } else {
                        location.reload();
                    }
                }, 1500);
            } else {
                updateAlert(data.msg);
                setTimeout(function () {
                    if (data.url) {
                        // location.href=data.url;
                    } else {
                        $('#top-alert').find('button').click();
                    }
                }, 1500);
            }
        });

    }
    return false;
});

//ajax post submit请求
$('.ajax-post').click(function () {
    var target, query, form;
    var target_form = $(this).attr('target-form');
    var that = this;
    var nead_confirm = false;
    if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        form = $('.' + target_form);

        if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
            form = $('.hide-data');
            query = form.serialize();
        } else if (form.get(0) == undefined) {
            return false;
        } else if (form.get(0).nodeName == 'FORM') {
            if ($(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            if ($(this).attr('url') !== undefined) {
                target = $(this).attr('url');
            } else {
                target = form.get(0).action;
            }
            query = form.serialize();
        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
            form.each(function (k, v) {
                if (v.type == 'checkbox' && v.checked == true) {
                    nead_confirm = true;
                }
            })
            if (nead_confirm && $(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.serialize();
        } else {
            if ($(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.find('input,select,textarea').serialize();
        }
        $(that).addClass('disabled').attr('autocomplete', 'off').prop('disabled', true);
        $.post(target, query).success(function (data) {
            if (data.code == 1) {
                if (data.url) {
                    updateAlert(data.msg + ' 页面即将自动跳转~', 'alert-success');
                } else {
                    updateAlert(data.msg, 'alert-success');
                }
                setTimeout(function () {
                    $(that).removeClass('disabled').prop('disabled', false);
                    if (data.url && data.url !== 'javascript:history.back(-1);') {
                        location.href = data.url;
                    } else if ($(that).hasClass('no-refresh')) {
                        $('#top-alert').find('button').click();
                    } else {
                        location.reload();
                    }
                }, 1500);
            } else {
                updateAlert(data.msg);
                setTimeout(function () {
                    $(that).removeClass('disabled').prop('disabled', false);
                    if (data.url && data.url !== 'javascript:history.back(-1);') {
                        location.href = data.url;
                    } else {
                        $('#top-alert').find('button').click();
                    }
                }, 1500);
            }
        });
    }
    return false;
});

/**顶部警告栏*/
var content = $('#main');
var top_alert = $('#top-alert');
top_alert.find('.close').on('click', function () {
    top_alert.removeClass('block').slideUp(200);
    // content.animate({paddingTop:'-=55'},200);
});

window.updateAlert = function (text, c) {
    text = text || 'default';
    c = c || false;
    if (text != 'default') {
        top_alert.find('.alert-content').text(text);
        if (top_alert.hasClass('block')) {
        } else {
            top_alert.addClass('block').slideDown(200);
            // content.animate({paddingTop:'+=55'},200);
        }
    } else {
        if (top_alert.hasClass('block')) {
            top_alert.removeClass('block').slideUp(200);
            // content.animate({paddingTop:'-=55'},200);
        }
    }
    if (c != false) {
        top_alert.removeClass('alert-error alert-warn alert-info alert-success').addClass(c);
    }
};


// 独立域表单获取焦点样式
$(".text").focus(function () {
    $(this).addClass("focus");
}).blur(function () {
    $(this).removeClass('focus');
});
$("textarea").focus(function () {
    $(this).closest(".textarea").addClass("focus");
}).blur(function () {
    $(this).closest(".textarea").removeClass("focus");
});

//自定义伸缩框ibox
$(".collapse-link").on('click', function () {
    var ibox = $(this).closest("div.ibox"), i = $(this).find("i"), iboxContent = $(ibox).find("div.ibox-content");
    iboxContent.slideToggle(200);
    i.toggleClass("fa-chevron-up").toggleClass("fa-chevron-down");
    ibox.toggleClass("").toggleClass("border-bottom");
});

//返回顶部滚动监听
$(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
        $('#back2top').fadeIn();
    } else {
        $('#back2top').fadeOut();
    }
});
//返回顶部点击事件
$('#back2top').on('click', function (e) {
    $('html,body').animate({scrollTop: 0}, 1000);
});
//照片墙随机旋转角度
$(".photo-item a").each(function () {
    $(this).css('transform', 'rotate(' + Math.round((-Math.random() * 10) + 5) + 'deg) translateZ(0px)');
});

