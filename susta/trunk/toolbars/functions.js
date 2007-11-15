
function chk() {
	if (document.forms[0].user.value=="") {
	 	alert("Inserisci la propria username!");
		document.forms[0].user.focus();
		return false;
	 }
	if (document.forms[0].pwd.value=="") {
	 	alert("Inserisci la propria password!");
		document.forms[0].pwd.focus();
		return false;
	 }
	return true;
 }