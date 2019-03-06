/**
 * Created by Administrator on 2017/3/6.
 */
/*----选项卡组件JS-----*/
function iTabs() {
    $(".J_menuItem").each(function (t) {
        $(this).attr("data-index") || $(this).attr("data-index", t);
    });
    $(".J_menuItem").on('click', addTab); //绑定添加并显示选项卡事件
    $(".J_menuTabs").on('click', ".J_menuTab", selectTab);//绑定选项卡选中事件
    $(".J_menuTabs").on('click', ".J_menuTab i", closeTab);//绑定选项卡关闭事件
    $(".J_tabLeft").on('click', moveTabsLeft);//绑定选项卡列表左移事件
    $(".J_tabRight").on('click', moveTabsRight);//绑定选项卡列表右移事件
    $(".J_tabRefresh").on('click', refreshTab);//绑定刷新选项卡事件
    $(".J_tabShowActive").on('click', showActiveTab);//绑定定位选项卡事件
    $(".J_tabCloseOther").on('click', closeOtherTabs);//绑定关闭其他选项卡事件
    $(".J_tabCloseAll").on('click', closeAllTabs);//绑定关闭所有选项卡事件

}

//计算选项卡列表的宽度
function countWidth(element) {
    var count = 0;
    return $(element).each(function () {
        count += $(this).outerWidth(true);
    }), count;
}

//选项卡列表移动
function moveTabs(element) {
    var prevWidth = countWidth($(element).prevAll());
    var nextWidth = countWidth($(element).nextAll());
    var buttonsWidth = countWidth($(".content-tabs").children().not(".J_menuTabs"));
    var tabsWidth = $(".content-tabs").outerWidth() - buttonsWidth;
    var moveWidth = 0;
    if ($(".page-tabs-content").outerWidth() < tabsWidth) {
        moveWidth = 0;
    } else if (nextWidth <= tabsWidth - $(element).outerWidth(true) - $(element).next().outerWidth(true)) {
        if (tabsWidth - $(element).next().outerWidth(true) > nextWidth) {
            moveWidth = prevWidth;
            for (var i = element; moveWidth - $(i).outerWidth() > $(".page-tabs-content").outerWidth() - tabsWidth;) {
                moveWidth -= $(i).prev().outerWidth();
                i = $(i).prev();
            }
        }
    } else {
        prevWidth > tabsWidth - $(element).outerWidth(true) - $(element).prev().outerWidth(true) && (moveWidth = prevWidth - $(element).prev().outerWidth(true));
    }
    $(".page-tabs-content").animate({marginLeft: 0 - moveWidth + "px"}, "fast");
}

//选项卡列表左移
function moveTabsLeft() {

    var currentWidth = Math.abs(parseInt($(".page-tabs-content").css("margin-left")));
    var buttonsWidth = countWidth($(".content-tabs").children().not(".J_menuTabs"));
    var tabsWidth = $(".content-tabs").outerWidth() - buttonsWidth;
    var moveWidth = 0;
    if ($(".page-tabs-content").width() < tabsWidth) {
        return !1;
    }
    for (var s = $(".J_menuTab:first"), r = 0; r + $(s).outerWidth(true) <= currentWidth;) {
        r += $(s).outerWidth(true);
        s = $(s).next();
    }
    if (r = 0, countWidth($(s).prevAll()) > tabsWidth) {
        for (; r + $(s).outerWidth(true) < tabsWidth && s.length > 0;) {
            r += $(s).outerWidth(true);
            s = $(s).prev();
        }
        moveWidth = countWidth($(s).prevAll());
    }
    $(".page-tabs-content").animate({marginLeft: 0 - moveWidth + "px"}, "fast");
}

//选项卡列表右移
function moveTabsRight() {
    var currentWidth = Math.abs(parseInt($(".page-tabs-content").css("margin-left")));
    var buttonsWidth = countWidth($(".content-tabs").children().not(".J_menuTabs"));
    var tabsWidth = $(".content-tabs").outerWidth() - buttonsWidth;
    var moveWidth = 0;
    if ($(".page-tabs-content").width() < tabsWidth) {
        return !1;
    }
    for (var s = $(".J_menuTab:first"), r = 0; r + $(s).outerWidth(true) <= currentWidth;) {
        r += $(s).outerWidth(true);
        s = $(s).next();
    }
    for (r = 0; r + $(s).outerWidth(true) < tabsWidth && s.length > 0;) {
        r += $(s).outerWidth(true);
        s = $(s).next();
    }
    moveWidth = countWidth($(s).prevAll());
    moveWidth > 0 && $(".page-tabs-content").animate({marginLeft: 0 - moveWidth + "px"}, "fast");

}

//添加并显示选项卡
function addTab() {
    var id = $(this).attr('href'), a = $(this).data('index'), text = $.trim($(this).text()), flag = !0;
    if (void 0 == id || 0 == $.trim(id).length) return !1;
    if ($(".J_menuTab").each(function () {
            return $(this).data("id") == id ? ($(this).hasClass("active") || $(this).addClass("active").siblings(".J_menuTab").removeClass("active"), $(".J_mainContent .J_iframe").each(function () {
                return $(this).data("id") == id ? ($(this).show().siblings(".J_iframe").hide(), !1) : void 0
            }), flag = !1, !1) : void 0;
        }), flag) {
        var tab = "<a class='active J_menuTab' href='javascript:;' data-id='" + id + "'>" + text + "<i class='fa fa-times-circle'></i></a>";
        var iframe = "<iframe class='J_iframe' name='iframe" + a + "' height='100%' width='100%' src='" + id + "' data-id='" + id + "' frameborder='0' seamless></iframe>";
        $(".J_mainContent").find(".J_iframe").hide().parents(".J_mainContent").append(iframe);
        $(".J_menuTab").removeClass("active");
        $(".J_menuTabs .page-tabs-content").append(tab);
    }
    moveTabs($(".J_menuTab.active"));
    return !1;
}

//选中选项卡
function selectTab() {
    var id = $(this).data("id");
    $(this).hasClass("active") ? void 0 : $(this).addClass("active").siblings(".J_menuTab").removeClass("active");
    $(".J_mainContent .J_iframe").each(function () {
        return $(this).data("id") == id ? $(this).show().siblings(".J_iframe").hide() : void 0;
    });
    moveTabs(this);
}

//关闭选项卡
function closeTab() {
    var id = $(this).parent(".J_menuTab").data("id");
    if ($(this).parent(".J_menuTab").hasClass("active")) {
        if ($(this).parent(".J_menuTab").next(".J_menuTab").size()) {
            var nextId = $(this).parent(".J_menuTab").next(".J_menuTab:eq(0)").data("id");
            $(this).parent(".J_menuTab").next(".J_menuTab:eq(0)").addClass("active");
            $(this).parent(".J_menuTab").remove();
            $(".J_mainContent .J_iframe[data-id='" + id + "']").remove();
            $(".J_mainContent .J_iframe").each(function () {
                return $(this).data("id") == nextId ? ($(this).show().siblings(".J_iframe").hide()) : void 0;
            });
        }
        if ($(this).parent(".J_menuTab").prev(".J_menuTab").size()) {
            var prevId = $(this).parent(".J_menuTab").prev(".J_menuTab:last").data("id");
            $(this).parent(".J_menuTab").prev(".J_menuTab:last").addClass("active");
            $(this).parent(".J_menuTab").remove();
            $(".J_mainContent .J_iframe[data-id='" + id + "']").remove();
            $(".J_mainContent .J_iframe").each(function () {
                return $(this).data("id") == prevId ? ($(this).show().siblings(".J_iframe").hide()) : void 0;
            });
        }
    } else {
        $(this).parent(".J_menuTab").remove();
        $(".J_mainContent .J_iframe").each(function () {
            return $(this).data("id") == id ? ($(this).remove()) : void 0;
        });
    }

    moveTabs($(".J_menuTab.active"));
    return !1;
}

//刷新选项卡
function refreshTab() {
    var id = $(".J_menuTab.active").data("id");
    var iframe = $(".J_mainContent .J_iframe[data-id='" + id + "']");
    var src = iframe.attr("src");
    iframe.attr("src", src);
}

//定位当前选项卡
function showActiveTab() {
    moveTabs($(".J_menuTab.active"));
}

//关闭其他选项卡
function closeOtherTabs() {
    $(".page-tabs-content").children(".J_menuTab").not(":first").not(".active").each(function () {
        $(".J_mainContent .J_iframe[data-id='" + $(this).data("id") + "']").remove(), $(this).remove();
    });
    $(".page-tabs-content").css("margin-left", "0");
}

//关闭所有选项卡
function closeAllTabs() {
    $(".page-tabs-content").children(".J_menuTab").not(":first").each(function () {
        $(".J_mainContent .J_iframe[data-id='" + $(this).data("id") + "']").remove(), $(this).remove();
    }), $(".page-tabs-content").children(".J_menuTab:first").each(function () {
        $(".J_mainContent .J_iframe[data-id='" + $(this).data("id") + "']").show(), $(this).addClass("active");
    });
    $(".page-tabs-content").css("margin-left", "0");
}

