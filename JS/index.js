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
        }, 2200);
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