<script>

function dmt_createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function dmt_readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function dmt_eraseCookie(name) {
	dmt_createCookie(name,"",-1);
}


function dmt_send (op) {
	
	var dmt_user = dmt_readCookie('dmt_user');
	if (dmt_user == null) {
		var date = new Date();
		dmt_user = date.getTime();
		dmt_createCookie('dmt_user', dmt_user, 365);
		alert ("DMT User created: " + dmt_user);
	} else {
		alert ("DMT User found: " + dmt_user);

	}
	
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "http://localhost/DMT/dmt/setuserdata.txt", true);
	xhr.send(null);

	/*
	jQuery.ajax({
		  url: "http://localhost/DMT/dmt/setuserdata"
	});
	*/
	/*
	jQuery.ajax({
		  url: "http://localhost/DMT/dmt/setuserdata",
		  type: "get", 
		  data: { 
		    op: op,
		    user: dmt_user,
		    href: window.location.href 
		  }
		});
	*/
	
	
}

</script>