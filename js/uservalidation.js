function formhash(form) {
    // Create a new element input, this will be our hashed password field. 
    var p=document.createElement("input");
 
    // Add the new element to our form. 
    form.appendChild(p);
    p.name="p";
    p.type="hidden";
    p.value=hex_sha512(form.password.value);
 
    // Make sure the plaintext password doesn't get sent. 
    form.password.value="";
 
    // Finally submit the form. 
    form.submit();
}
 
function regformhash(form) {
	// Check each field has a value
	if(form.pwd1.value=='' || form.pwd2.value=='') {
		alert('You must provide all the requested details. Please try again');
		return false;
	}
	
	// Check that the password is sufficiently long (min 6 chars)
	// The check is duplicated below, but this is included to give more
	// specific guidance to the user
	if(form.pwd1.value.length<6) {
		alert('Passwords must be at least 6 characters long.  Please try again');
		form.pwd1.focus();
		return false;
	}
	
	// At least one number, one lowercase and one uppercase letter 
	// At least six characters 
	var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
	if(!re.test(form.pwd1.value)) {
		alert('Passwords must contain at least one number, one lowercase and one uppercase letter.  Please try again');
		return false;
	}
	
	// Check password and confirmation are the same
	if(form.pwd1.value!=form.pwd2.value) {
		alert('Your password and confirmation do not match. Please try again');
		form.pwd1.focus();
		return false;
	}
	
	// Create a new element input, this will be our hashed password field. 
	var p=document.createElement("input");
	
	// Add the new element to our form. 
	form.appendChild(p);
	p.name="p";
	p.type="hidden";
	p.value=hex_sha512(form.pwd1.value);
	
	// Make sure the plaintext password doesn't get sent. 
	form.pwd1.value="";
	form.pwd2.value="";
	
	// Finally submit the form. 
	form.submit();
}