/*---- function for profile options button ----*/
document.addEventListener('DOMContentLoaded', () => {
    const optionsDiv = document.querySelector('.pr-options');
    const optionsButton = document.getElementById('options');

    if (optionsDiv) {
        optionsDiv.classList.add('hide'); // hide by default
    }

    // Function to hide options
    function hideProfileOptions() {
        if (optionsDiv && !optionsDiv.classList.contains('hide')) {
            optionsDiv.classList.add('hide');
        }
    }

    if (optionsButton) {
        window.addEventListener('click', (event) => {
            if (optionsDiv && !optionsDiv.classList.contains('hide') &&
                event.target !== optionsButton &&
                !optionsButton.contains(event.target) &&
                !optionsDiv.contains(event.target)) {
                hideProfileOptions();
            }
        });

        window.addEventListener('scroll', hideProfileOptions);

        optionsButton.addEventListener('click', (event) => {
            if (optionsDiv) {
                optionsDiv.classList.toggle('hide');
            }
            event.stopPropagation(); // Prevent the click from bubbling up to the window
        });
    }
});


/*---- function for remove the msg after 3 seconds ----*/
document.addEventListener('DOMContentLoaded', (event) => {
    const messageDiv = document.querySelector('.message');
    if (messageDiv) {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 2000);
    }
});


/*---- function for profile image colors ----*/
document.addEventListener('DOMContentLoaded', () => {
    const colors = ['#0360a4', '#f33a39', '#c68f3f', '#d64b94'];
    const prPicElement = document.querySelector('.pr-pic');
    if (prPicElement) { // Check if the element exists
        prPicElement.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
    }
});


/*--- function for User name splitting ---*/
document.addEventListener('DOMContentLoaded', () => {
    let userNameElement = document.getElementById("userName");
    if (userNameElement) {
        let userName = userNameElement.textContent || userNameElement.innerText;

        userName = userName.split(' ')[0];

        if (userName.length > 6) {
            userName = userName.substring(0, 4) + "..";
        }
        userNameElement.textContent = userName;
    }
});


// Assuming this script is for a registration form or similar where username is validated.
document.addEventListener('DOMContentLoaded', () => {
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) { // Only run if the form exists on the page
        registrationForm.addEventListener('submit', function (event) {
            // Prevent form submission initially to run client-side validations
            event.preventDefault();

            // Reset previous error messages
            document.getElementById('usernameError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';

            // Get form values (ensure 'username' matches the ID of your username/full name input)
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;

            // Validation flag
            var isValid = true;

            // --- Client-side Username Validation ---
            if (username.trim() === '') {
                document.getElementById('usernameError').textContent = 'Username is required';
                isValid = false;
            } else if (username.length < 4) {
                document.getElementById('usernameError').textContent = 'Username should be longer than 3 characters';
                isValid = false;
            }
            // NEW: Check for any special characters EXCEPT underscore and dot
            else if (/[^a-zA-Z0-9_.]/.test(username)) { // Matches any character NOT in a-z, A-Z, 0-9, _, or .
                document.getElementById('usernameError').textContent = ' No special characters (except _ .)';
                isValid = false;
            }
            // Then, if the characters are valid, check if it starts with an alphabet
            else if (!/^[a-zA-Z]/.test(username)) { // Checks if the first character is NOT an alphabet
                document.getElementById('usernameError').textContent = 'Username must start with an alphabet.';
                isValid = false;
            }


            // Client-side Email Validation
            if (email.trim() === '') {
                document.getElementById('emailError').textContent = 'Email is required';
                isValid = false;
            } else if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'Invalid email address';
                isValid = false;
            }

            // --- Client-side Password Validation ---
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
                this.submit(); // This will submit the form
            }
        });
    }
});

// Email validation function
function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}


// --- Existing code for product boxes animation ---
const boxesAni = document.querySelectorAll('.box');

function checkBoxes() {
    const triggerBottom = window.innerHeight / 5 * 4; // 80% from the bottom

    boxesAni.forEach(box => {
        const boxTop = box.getBoundingClientRect().top;

        if (boxTop < triggerBottom) {
            box.classList.add('visible');
        } else {
            box.classList.remove('visible');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('scroll', checkBoxes);
    checkBoxes(); // Initial check
});


/*---- About Us - Read more functionality ----*/
document.addEventListener('DOMContentLoaded', () => {
    const AbReadMore = document.querySelector(".section #read-more");
    const text = document.querySelector('.section .show-more');
    if (AbReadMore && text) { // Check if elements exist
        let readMore = true;

        AbReadMore.addEventListener("click", function() {
            if (readMore == true) {
                text.classList.remove("hide");
                readMore = false;
                AbReadMore.innerHTML = "Read Less";
            } else {
                text.classList.add("hide");
                readMore = true;
                AbReadMore.textContent = "Read More";
            }
        });
    }
});