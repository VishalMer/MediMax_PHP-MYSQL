
document.getElementById('loginForm')
  .addEventListener('submit', function (event) {
    // Prevent form submission
    event.preventDefault();

    // Reset previous error messages
    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';

    // Get form values
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    // Validation
    var isValid = true;

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