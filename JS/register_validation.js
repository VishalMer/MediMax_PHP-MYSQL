document.getElementById('registrationForm')
  .addEventListener('submit', function (event) {
    // Prevent form submission
    event.preventDefault();

    // Reset previous error messages
    document.getElementById('usernameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    // Get form values
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    // Validation
    var isValid = true;

    if (username.trim() === '') {
      document.getElementById('usernameError').textContent =
        'Username is required';
      isValid = false;
    } else if (username.length < 4) {
      document.getElementById('usernameError').textContent =
        'Username should be longer than 3 characters';
      isValid = false;
    } else if (!/^[a-zA-Z ]+$/.test(username)) {
      document.getElementById('usernameError').textContent =
        'Username should not contain special characters';
      isValid = false;
    }

    if (email.trim() === '') {
      document.getElementById('emailError').textContent =
        'Email is required';
      isValid = false;
    } else if (!validateEmail(email)) {
      document.getElementById('emailError').textContent =
        'Invalid email address';
      isValid = false;
    }

    if (password.trim() === '') {
      document.getElementById('passwordError').textContent =
        'Password is required';
      isValid = false;
    } else if (password.length < 6) {
      document.getElementById('passwordError').textContent =
        'Password should be at least 6 characters long';
      isValid = false;
    }

    // If all validations pass, submit the form
    if (isValid) {
      this.submit();
    }
  });

// Email validation function
function validateEmail(email) {
  var re = /\S+@\S+\.\S+/;
  return re.test(email);
}
