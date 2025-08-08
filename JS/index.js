/*--- Merged JavaScript for All Functionalities ---*/

document.addEventListener('DOMContentLoaded', () => {

    /*==================================================*/
    /* Scrolling Animation Logic */
    /*==================================================*/

    const generalObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            } else {
                entry.target.classList.remove('show');
            }
        });
    }, {
        threshold: 0.1
    });

    const products = document.querySelectorAll('.shopping .box');

    // Function to check initial visibility and apply 'show' class on page load
    function initialCheck() {
        const viewportHeight = window.innerHeight;
        products.forEach((product) => {
            const rect = product.getBoundingClientRect();
            if (rect.top < viewportHeight && rect.bottom > 0) {
                product.classList.add('show');
            } else {
                product.classList.add('hidden');
            }
        });
    }

    // Run the initial check once the page loads
    initialCheck();

    // Ensure the first few products are immediately visible and don't animate awkwardly
    for (let i = 0; i < 5 && i < products.length; i++) {
        products[i].classList.remove('hidden');
        products[i].classList.add('show');
    }

    let lastScrollY = window.scrollY;
    let isTicking = false;
    const maxDelay = 0.4;
    const speedMultiplier = 0.005;

    // Function to handle the product animations based on scroll speed and direction
    function updateProducts() {
        const currentScrollY = window.scrollY;
        const scrollSpeed = Math.abs(currentScrollY - lastScrollY);
        const viewportHeight = window.innerHeight;

        products.forEach((product) => {
            const rect = product.getBoundingClientRect();
            const isInViewport = (rect.top <= viewportHeight) && (rect.bottom >= 0);

            // If a product enters the viewport and is currently hidden, animate it
            if (isInViewport && !product.classList.contains('show')) {
                const calculatedDelay = Math.max(0, maxDelay - (scrollSpeed * speedMultiplier));
                product.style.transitionDelay = `${calculatedDelay}s`;
                product.classList.remove('hidden');
                product.classList.add('show');
            }
        });
        
        lastScrollY = currentScrollY;
        isTicking = false;
    }

    window.addEventListener('scroll', () => {
        if (!isTicking) {
            window.requestAnimationFrame(updateProducts);
            isTicking = true;
        }
    });

    const otherAnimatedElements = document.querySelectorAll(
        '.mainText, ' +
        '.best-sellings h2, ' +
        '.container, ' +
        '.content-section, ' +
        '.image-section, ' +
        '.container-contact, ' +
        '.orders table, ' +
        '.footer'
    );

    otherAnimatedElements.forEach((element) => {
        element.classList.add('hidden');
        generalObserver.observe(element);
    });

    /*==================================================*/
    /* Profile Options Button Logic */
    /*==================================================*/

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

    /*==================================================*/
    /* Other Functionalities */
    /*==================================================*/

    // Remove message after 2.2 seconds
    const messageDiv = document.querySelector('.message');
    if (messageDiv) {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 2200);
    }

    // Random profile image colors
    const colors = ['#0360a4', '#f33a39', '#c68f3f', '#d64b94'];
    const prPicElement = document.querySelector('.pr-pic');
    if (prPicElement) {
        prPicElement.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
    }

    // Username splitting and shortening
    let userNameElement = document.getElementById("userName");
    if (userNameElement) {
        let userName = userNameElement.textContent || userNameElement.innerText;
        userName = userName.split(' ')[0];
        if (userName.length > 6) {
            userName = userName.substring(0, 4) + "..";
        }
        userNameElement.textContent = userName;
    }

    // About Us "Read More"
    const AbReadMore = document.querySelector(".section #read-more");
    const text = document.querySelector('.section .show-more');
    if (AbReadMore && text) {
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