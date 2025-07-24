/*---- function for profile options button ----*/
document.addEventListener('DOMContentLoaded', () => {
    const optionsDiv = document.querySelector('.pr-options');
    const optionsButton = document.getElementById('options');

    // Ensure optionsDiv exists and is initially hidden
    if (optionsDiv) {
        optionsDiv.classList.add('hide'); // Add hide class to ensure it's hidden by default
    }

    // Function to hide options
    function hideProfileOptions() {
        if (optionsDiv && !optionsDiv.classList.contains('hide')) {
            optionsDiv.classList.add('hide');
        }
    }

    // Add click event listener to the window to hide options
    // This listener will only be active if optionsButton exists
    if (optionsButton) {
        window.addEventListener('click', (event) => {
            // Check if the click was outside the options button and the options div
            // and if the options div is currently visible (not hidden)
            if (optionsDiv && !optionsDiv.classList.contains('hide') &&
                event.target !== optionsButton &&
                !optionsButton.contains(event.target) &&
                !optionsDiv.contains(event.target)) {
                hideProfileOptions();
            }
        });

        // Add scroll event listener to the window to hide options
        window.addEventListener('scroll', hideProfileOptions);

        // Add click event listener to the options button
        optionsButton.addEventListener('click', (event) => {
            if (optionsDiv) {
                optionsDiv.classList.toggle('hide'); // Toggle hide class
            }
            event.stopPropagation(); // Prevent the click from bubbling up to the window
        });
    }
});


/*---- function for remove the msg after 3 seconds ----*/
document.addEventListener('DOMContentLoaded', (event) => {
    const messageDiv = document.querySelector('.message');
    if (messageDiv) {
        // Hide the div after 3 seconds
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
// Using DOMContentLoaded instead of window.onload for better practice,
// as window.onload waits for all resources (images, etc.)
document.addEventListener('DOMContentLoaded', () => {
    let userNameElement = document.getElementById("userName");
    if (userNameElement) { // Check if the element exists
        let userName = userNameElement.textContent || userNameElement.innerText;

        userName = userName.split(' ')[0]; // Take the part before the first space

        // Check the length and apply the necessary transformation
        if (userName.length > 6) {
            userName = userName.substring(0, 5) + ".."; // Trim to 5 characters + ".."
        }

        //set it back to username and display it.
        userNameElement.textContent = userName;
    }
});


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

// Initial check and scroll listener for product boxes
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