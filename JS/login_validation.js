document.getElementById('loginForm')
  .addEventListener('submit', function (event) {
    // Prevent form submission initially to allow for JavaScript validation
    event.preventDefault();

    // Reset previous error messages
    document.getElementById('identifierError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    // Get form values
    var identifier = document.getElementById('identifier').value;
    var password = document.getElementById('password').value;

    // Validation
    var isValid = true;

    // Identifier Validation
    if (identifier.trim() === '') {
      document.getElementById('identifierError').textContent =
        'Username or Email is required';
      isValid = false;
    }

    // --- Password Validation (Updated to match Register_validation.js) ---
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
      document.getElementById('passwordError').textContent = 'Needs special character.';
      isValid = false;
    }

    // If all validations pass, submit the form programmatically
    if (isValid) {
      this.submit(); // This line ensures the form is submitted
    }
  });