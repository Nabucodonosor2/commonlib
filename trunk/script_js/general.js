var url_protocolo= window.location.protocol;
var url_hostname = window.location.hostname;
var url_pathname = window.location.pathname;
var array_url = url_pathname.split("trunk");

var K_ROOT_URL= url_protocolo +'//'+ url_hostname + array_url[0] + "trunk/";
document.write('<script type="text/javascript" src="http://200.74.191.42/sysbiggi_new/commonlib/trunk/script_js/jquery.min.js"></script>');
document.write('<script type="text/javascript" src="http://200.74.191.42/sysbiggi_new/commonlib/trunk/script_js/jquery.js"></script>');
document.write('<script type="text/javascript" src="http://200.74.191.42/sysbiggi_new/commonlib/trunk/script_js/general.incluir.js?v=1"></script>');
document.write('<link rel="stylesheet" href="http://200.74.191.42/sysbiggi_new/commonlib/trunk/css/jquery-ui.css">');

