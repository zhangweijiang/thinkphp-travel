// JavaScript Document
/*设置弹窗iframe添加修改内容的页面大小*/
var browserVersion = window.navigator.userAgent.toUpperCase();
var isOpera = false, isFireFox = false, isChrome = false, isSafari = false, isIE = false;
function reinitIframe(iframeId, minHeight) {
    try {
        var iframe = document.getElementById(iframeId);
        var bHeight = 0;
        if (isChrome == false && isSafari == false)
            bHeight = iframe.contentWindow.document.body.scrollHeight;

        var dHeight = 0;
        if (isFireFox == true)
            dHeight = iframe.contentWindow.document.documentElement.offsetHeight + 2;
        else if (isIE == false && isOpera == false)
            dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
        else if (isIE == true && ! -[1, ] == false) { } //ie9+
        else
            bHeight += 3;

        var height = Math.max(bHeight, dHeight);
        if (height < minHeight) height = minHeight;
        iframe.style.height = height + "px";
    } catch (ex) { }
}
function startInit(iframeId, minHeight) {
    isOpera = browserVersion.indexOf("OPERA") > -1 ? true : false;
    isFireFox = browserVersion.indexOf("FIREFOX") > -1 ? true : false;
    isChrome = browserVersion.indexOf("CHROME") > -1 ? true : false;
    isSafari = browserVersion.indexOf("SAFARI") > -1 ? true : false;
    if (!!window.ActiveXObject || "ActiveXObject" in window)
        isIE = true;
    window.setInterval("reinitIframe('" + iframeId + "'," + minHeight + ")", 100);
}  
startInit('popup', parseInt($(window).height()-8));



/*内容框 自适应满屏宽度*/
$(document).ready(function(){
	var width = $(window).width();
    var width = width - 183 - 180 - 20;
	$('.main').css('width',width);
	//编辑器宽度
	$('.main .main').css('width','100%');
	$('.left-1').css('min-height',$(document).height() -50);
	$('.left-2').css('min-height',$(document).height() -50);
});

//查找一个字符串在另一个字符串中出现的次数 
function FindCount(targetStr, FindStr) {  
	var start = 0;  
	var aa = 0;  
	var ss =targetStr.indexOf(FindStr, start);  
	while (ss > -1) {  
		start = ss + FindStr.length;  
		aa++;  
		ss = targetStr.indexOf(FindStr, start);  
	}  
	return aa;  
}  

/*左侧分类显示与隐藏*/
$(function (){
	$('.left-1 ul li').click(function () {
		var display = $(this).children("ul").css("display");
		if(display == 'none'){
			$(this).children("ul").css("display",'block');
		}else{
			$(this).children("ul").css("display",'none');
		}
	});
});
//兼容火狐、IE8   
//显示遮罩层    
function showMask(){     
	$("#mask").css("height",$(document).height());     
	$("#mask").css("width",$(document).width()); 
	$(".convenient").css("display","block");
	$("#mask").show();  
}  
//隐藏遮罩层  
function hideMask(){     
	$(".convenient").css("display","none");
	$("#mask").hide();     
}
/*关闭便捷操作*/
$(function (){
	$('#close').click(function () {
		hideMask();
	});
});
/*打开便捷操作*/
$(function (){
	$('#convenient').click(function () {
		showMask();
	});
});

/*关闭便捷操作*/
$(function (){
	$('.anniu-1').click(function () {
		hideMask();
	});
});
/*打开便捷操作*/
$(function (){
	$('#convenient').click(function () {
		showMask();
	});
});


/*添加便捷操作*/
$(function (){
	$("#WXZ").delegate("div","click",function() { //这种写法是为了防止html重写之后click事件失效
		var wxz_id   = $(this).attr('id');   //ID号
		var wxz_html = $(this).prop("outerHTML");//DIV的内容
		var wxz      = $('#WXZ').html();     //未选中区域的全部内容
		var yxz      = $('#YXZ').html();     //已选中区域的全部内容
		var id       = $('#ID').val();       //获得隐藏域ID
		wxz  = wxz.replace(wxz_html,"");      //替换掉选择的内容
		wxz_html      = wxz_html.replace('+','×');      //+替换成x
		yxz  = yxz+wxz_html;                  //添加进已选中区域
		//为空 或只存在一条| 时则初始化参数
		if(id == '' || id == ','){
			$('#ID').val(','+wxz_id+',');
		}else{
			$('#ID').val(id+wxz_id+',');
		}
		$('#WXZ').html(wxz);
		$('#YXZ').html(yxz);
	});
});
/*取消便捷操作*/
$(function (){
	$("#YXZ").delegate("div","click",function() { //这种写法是为了防止html重写之后click事件失效
		var yxz_id   = $(this).attr('id');   //ID号
		var yxz_html = $(this).prop("outerHTML");//DIV的内容
		var wxz      = $('#WXZ').html();     //未选中区域的全部内容
		var yxz      = $('#YXZ').html();     //已选中区域的全部内容
		var id       = $('#ID').val();       //获得隐藏域ID
		yxz  = yxz.replace(yxz_html,"");      //替换掉选择的内容
		yxz_html      = yxz_html.replace('×','+');//x替换成+
		wxz  = wxz+yxz_html;                  //添加进未选中区域
		var num = FindCount(id,','); //获得| 符号在ID中出现的总次数
		
		//|出现次数小于等于2 则清空隐藏域
		if(num <= 2){
			$('#ID').val('');
		}else{
			var vid = id.replace(yxz_id+',','');//删除选中id
			$('#ID').val(vid);
		}
		$('#WXZ').html(wxz);
		$('#YXZ').html(yxz);
	});
});

/*图片上传*/
$(function() {
  $("#Jun-Dian").click(function () {
    $("#Jun-Upload").click();               //隐藏了input:file样式后，点击头像就可以本地上传
     $("#Jun-Upload").on("change",function(){
       var objUrl = getObjectURL(this.files[0]) ;  //获取图片的路径，该路径不是图片在本地的路径
       if (objUrl) {
         $("#Jun-Pic").attr("src", objUrl) ;      //将图片路径存入src中，显示出图片
       }
    });
  });
});
 
//建立一個可存取到該file的url
function getObjectURL(file) {
  var url = null ;
  if (window.createObjectURL!=undefined) { // basic
    url = window.createObjectURL(file) ;
  } else if (window.URL!=undefined) { // mozilla(firefox)
    url = window.URL.createObjectURL(file) ;
  } else if (window.webkitURL!=undefined) { // webkit or chrome
    url = window.webkitURL.createObjectURL(file) ;
  }
  return url ;
}

/*TAB 切换*/
$(function (){
	$('.nav-tabs li').click(function () {
		var ID = $(this).find('a').attr('title');
		//将所有的TAB对应主体先隐藏 并把tab的class样式取消
		$('.nav-tabs li').each(function(){
			var TAB_ID = $(this).find('a').attr('title');   
			$('#'+TAB_ID).css('display','none');
			$(this).removeClass("active");
		}); 
		//再将点击的TAB主体显示
		$('#'+ID).css('display','block');
		$(this).addClass("active");
	});
});

/*打开权限菜单*/
$(function (){
	$('#AUTH-A').click(function () {
		showMask();
	});
});
/*关闭权限菜单*/
$(function (){
	$('#AUTH-ANNIU').click(function () {
		hideMask();
	});
});
/*添加权限操作*/
$(function (){
	$("#AUTH-A-LIST").delegate("div","click",function() { //这种写法是为了防止html重写之后click事件失效											  
		var TXT2 = $(this).find('a').html();//选中的ID名
		var ID   = $(this).attr('id');   //选中ID号
		var HTML = $(this).parents().html();//全部内容
		var HTML = HTML.replace(' class="authclick"','');//删除样式
		var XZ_HTML = $(this).prop("outerHTML");//DIV的内容
		var XZ_HTML = XZ_HTML.replace('<div','<div class="authclick"');//添加样式
		var TXT = HTML.replace($(this).prop("outerHTML"),XZ_HTML);//替换内容
		$("#AUTH-A-ID").val(ID);
		$("#AUTH-A").html(TXT2);
		$("#AUTH-A-LIST").html(TXT);
		//点击确认按钮 - 设置信息
	});
});		


//显示提示插件遮罩层 - 淡入    
function showMask_Kai(){     
	$("#mask2").css("height",$(document).height());     
	$("#mask2").css("width",$(document).width()); 
	$("#mask2").fadeIn("slow");
}
//显示提示插件遮罩层 - 淡出    
function showMask_Out(){   
	setTimeout(function() {
	$("#mask2").fadeOut("slow");
	   $("#JunAlert_1").css("display","none");
	   $("#JunAlert_2").css("display","none");
	}, 2000);
}
/*
 * 验证提示
 * type : 提示类型
 * model: 子类型
 * text : 提示信息
 * title: 头部的信息
*/
function popup(type, model, text, title){
	switch(type){
		case 1: //网页顶部下拉提示
			$(".popup").html(text);
			$(".popupDom").stop().animate({
				"top": "-1px"
			}, 400);
			setTimeout(function() {
				$(".popupDom").stop().animate({
					"top": "-51px"
				}, 400);
			}, 2000);
			break;
		case 2://弹出提示层 带头部
			showMask_Kai();//弹出
			setTimeout(function() {
				$("#JunAlert_1").css('height','148px');	
				$("#Alert_Left_1").css('top','60px');
				$("#Alert_Right_1").css('top','65px');
				$("#Alert_Top_1").html(title);
				$("#Alert_Top_1").css('display','block');
				switch(model){
					case 1: var Num = '0px 0'; var Col = '#eeae00';break;//黄色 叹号
					case 2: var Num = '-30px 0'; var Col = '#009f95';break;//绿色 打勾
					case 3: var Num = '-60px 0'; var Col = '#e85141';break;//红色 打叉
					case 4: var Num = '-90px 0'; var Col = '#eeae00';break;//黄色 问号
					case 5: var Num = '-120px 0'; var Col = '#333';break;//黑色 锁   
					case 6: var Num = '-150px 0'; var Col = '#e85141';break;//红色 难过
					case 7: var Num = '-180px 0'; var Col = '#009f95';break;//绿色 笑脸
				}
				$("#Alert_Left_1").css('background-position',Num);
				$("#Alert_Top_1").css('background-color',Col);
				$("#Alert_Right_1").html(text);
				$("#JunAlert_1").css("display","block");
			}, 0);
			showMask_Out();//隐藏
			break;
		case 3: //不带头部
			showMask_Kai();//弹出
			setTimeout(function() {
				$("#JunAlert_1").css('height','100px');	

				$("#Alert_Top_1").css('display','none');
				switch(model){
					case 1: var Num = '0px 0';break; //黄色 叹号
					case 2: var Num = '-30px 0';break; //绿色 打勾
					case 3: var Num = '-60px 0';break; //红色 打叉
					case 4: var Num = '-90px 0';break; //黄色 问号
					case 5: var Num = '-120px 0';break; //黑色 锁   
					case 6: var Num = '-150px 0';break; //红色 难过
					case 7: var Num = '-180px 0';break; //绿色 笑脸
				}
				$("#Alert_Left_1").css('background-position',Num);
				$("#Alert_Left_1").css('top','30px');
				$("#Alert_Right_1").html(text);
				$("#Alert_Right_1").css('top','35px');
				$("#JunAlert_1").css("display","block");
			}, 0);
			showMask_Out();//隐藏
			break;
		case 4: //AJAX 开始
			showMask_Kai();//弹出
			setTimeout(function() {
				switch(model){
					case 1: var Num = 'public/images/1.gif';break; //
					case 2: var Num = 'public/images/2.gif';break; //
					case 3: var Num = 'public/images/3.gif';break; //
					case 4: var Num = 'public/images/4.gif';break; //
					case 5: var Num = 'public/images/5.gif';break; //
					case 6: var Num = 'public/images/6.gif';break; //
					case 7: var Num = 'public/images/7.gif';break; //
				}
				$("#Alert_Right_2").css('top','0px');
				$("#Alert_Right_2").html('<img style="width:200px" src="'+Num+'">');
				$("#JunAlert_2").css("display","block");
			}, 0);
			break;
		case 5: //AJAX -停止
			showMask_Out();
			break;
	}
	
}

//跨后台框架弹窗
function showdiv(url) {            
	top.document.getElementById("popup").src=url;
	top.document.getElementById("show").style.display ="block";
}
function hidediv() {
	top.document.getElementById("show").style.display ='none';
}