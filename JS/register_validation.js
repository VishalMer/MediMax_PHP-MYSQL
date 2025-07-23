document.getElementById('registrationForm')
  .addEventListener('submit', function (event) {
    // Prevent form submission initially to run client-side validations
    event.preventDefault();

    // Reset previous error messages
    document.getElementById('usernameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    // Get form values
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    // Validation flag
    var isValid = true;

    // --- Client-side Username Validation (Starts with alphabet, then alphanumeric + Underscore, Length) ---
    if (username.trim() === '') {
      document.getElementById('usernameError').textContent = 'Username is required';
      isValid = false;
    } else if (username.length < 4) {
      document.getElementById('usernameError').textContent = 'Username should be longer than 3 characters';
      isValid = false;
    } else if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(username)) { // NEW REGEX: Starts with letter, then alphanumeric or underscore
      document.getElementById('usernameError').textContent = 'Username must start with a alphabet.';
      isValid = false;
    }

    // Client-side Email Validation (No change here)
    if (email.trim() === '') {
      document.getElementById('emailError').textContent = 'Email is required';
      isValid = false;
    } else if (!validateEmail(email)) {
      document.getElementById('emailError').textContent = 'Invalid email address';
      isValid = false;
    }

    // --- Client-side Password Validation (Min 6, Max 12, Special Char, Number, Alphabet) ---
    if (password.trim() === '') {
      document.getElementById('passwordError').textContent = 'Password is required';
      isValid = false;
    } else if (password.length < 6) {
      document.getElementById('passwordError').textContent = 'Password should be at least 6 characters long';
      isValid = false;
    } else if (password.length > 12) { // Max length 12
      document.getElementById('passwordError').textContent = 'Password cannot be longer than 12 characters';
      isValid = false;
    } else if (!/[a-zA-Z]/.test(password)) { // Requires at least one alphabet
      document.getElementById('passwordError').textContent = 'Password must contain at least one letter.';
      isValid = false;
    } else if (!/\d/.test(password)) { // Requires at least one number
      document.getElementById('passwordError').textContent = 'Password must contain at least one number.';
      isValid = false;
    } else if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test(password)) { // Requires at least one special character
      document.getElementById('passwordError').textContent = 'Needs special char.';
      isValid = false;
    }


    // If all client-side validations pass, allow the form to submit to the server
    if (isValid) {
      this.submit(); // This will submit the form to register_form.php for server-side validation
    }
  });

// Email validation function (No change here)
function validateEmail(email) {
  var re = /\S+@\S+\.\S+/;
  return re.test(email);
}