define(function(require){
    require("jquery");
    require("mwt");
    require("site/main");
    var ajax=require("ajax");
    var refer,o={};

    function initpage(res) {
        var secquestions = res.secquestions;
		var platlogins = res.login_platform;

        $("#frame-body").css("background-color","#eee");
        var urlsfx = getUrlSubfix();
        var code = "";
        var headdiv = "<table class='tablay'><tr>"+
                "<td class='title' onclick='window.history.go(-1);'>用户登录</td>"+
                "<td style='text-align:right'><a href='regist.html"+urlsfx+"'>注册</a></td>"+
              "</tr></table>";
        var footdiv = "<table class='tablay' style='margin-top:10px;'><tr>"+
                 "<td style='text-align:right'><a href='findpass.html"+urlsfx+"'>忘记密码？</a></td></tr></table>";

        var fields = [
            {type:'html',html:headdiv},
            {type:'html',html:"<div id='errmsg' style='padding-bottom:5px;color:red;font-size:15px;'></div>"},
            {type:'text',icon:"am-icon-user",id:'fm-phone',placeholder:"输入注册用户名或手机号"},
            {type:'password',icon:"am-icon-lock",id:'fm-passwd',placeholder:"输入密码"},
            {type:'select',icon:"am-icon-question",id:'fm-questionid',options:secquestions},
            {type:'text',icon:"am-icon-hand-o-right",id:'fm-answer',placeholder:"答案",divid:"answer-div"},
            {type:'seccode',icon:"am-icon-sun-o",id:"fm-seccode",placeholder:"输入验证码"},
            {type:'button',id:"login-btn",text:"登录"},
            {type:'html',html:footdiv}
        ];

        if (platlogins.length) {
            for (var i=0; i<platlogins.length; ++i) {
                var item = platlogins[i];
                var btnid = 'login-btn-'+i;
                fields.push({type:'button', id:btnid, text:item.text, icon:item.icon, cls:'am-btn-default', url:item.url});
            }
        }

        code += show_fieldset(fields);
		$("#frame-body").html(code);
        // bundle event
        $("#imgcode").click(function(){
            var d = new Date();
            var url = ajaxapi+"?version=4&module=seccode&tm="+d.getTime();
            $(this).attr("src", url);
        });
        
        $("#fm-questionid").change(function(){
            var v = $(this).val();
            if (v>0) $("#answer-div").show();
            else $("#answer-div").hide();
        });
        $("#login-btn").click(function(res){
            $("#errmsg").html("");
            var params = {
                username   : get_text_value("fm-phone"),
                password   : get_text_value("fm-passwd"),
                seccode    : get_text_value("fm-seccode"),
                questionid : get_select_value("fm-questionid")
            };
            if (params.questionid==0) {
                params.answer = "";
            } else {
                var answer = get_text_value("fm-answer");
                params.answer = encodeURI(answer);
            }
            params.username = encodeURI(params.username);
            ajax.post("login",params,function(res){
                if(res.retcode!=0) $("#errmsg").html(res.retmsg);
                else {
					if (refer) {
					    window.location = refer;
					} else {
				        window.location.reload();
					}
                }
            });
        });
    };

    o.init=function(){
        refer = getRefer();
        ajax.loadcache("profile",function(res){
            if (res.uid>0) {
                o.jump(res);
            } else {
			    initpage(res);
            }
        });
    };

    o.jump = function(res){
		if (refer) {
			window.location = refer;
		} else {
			welcome(res);
        }
    };

    return o;
});
