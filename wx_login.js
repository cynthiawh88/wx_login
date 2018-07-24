/**
* author : 林虎 
*email : kolinhu@126.com
*savetime :2018/07/23
*/
!function(a,b,c){function d(a){var c="default";a.self_redirect===!0?c="true":a.self_redirect===!1&&(c="false");var d=b.createElement("iframe"),e="https://open.weixin.qq.com/connect/qrconnect?appid="+a.appid+"&scope="+a.scope+"&redirect_uri="+a.redirect_uri+"&response_type="+a.response_type+"&state="+a.state+"#wechat_redirect";e+=a.style?"&style="+a.style:"",e+=a.href?"&href="+a.href:"",d.src=e,d.frameBorder="0",d.allowTransparency="true",d.scrolling="no",d.width="300px",d.height="400px";var f=b.getElementById(a.id);f.innerHTML="",f.appendChild(d)}a.WxLogin=d}(window,document);

var obj = new WxLogin({self_redirect:false,id:WX.__CONTAINERid, appid: WX.__Appid, scope: WX.__Scope, redirect_uri: WX.__Redir,state: WX.__State,response_type:WX.__Retype,style: WX.__Style,href: WX.__Href});