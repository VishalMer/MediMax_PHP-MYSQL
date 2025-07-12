/*---- function for profile options button ----*/ 
const optionsDiv = document.querySelector('.pr-options');
const optionsButton = document.getElementById('options');

  // Add click event listener to the window
  function hideOptions(event) {
    if (event.target !== optionsButton && !optionsDiv.contains(event.target)) {
        optionsDiv.classList.add('hide'); // Add hide class
    }
  }
  
  window.addEventListener('click', hideOptions);
  window.addEventListener('scroll', hideOptions);

  // Add click event listener to the options button
  optionsButton.addEventListener('click', function(event) {
    optionsDiv.classList.toggle('hide'); // Remove hide class
    event.stopPropagation(); // Prevent the click from bubbling up to the window
  });



/*---- function for remove the msg after 3 seconds ----*/
    // Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', (event) => {
    const messageDiv = document.querySelector('.message');
    if (messageDiv) {
        // Hide the div after 3 seconds 
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 3000);
    }
});




/*---- function for profile image colors ----*/ 
const colors = ['#0360a4', '#f33a39', '#c68f3f', '#d64b94'];
document.querySelector('.pr-pic').style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];


/*--- funcrion for User name splitting ---*/ 
window.onload = function() {
  // Get the value from the span
  let userNameElement = document.getElementById("userName");
  let userName = userNameElement.textContent || userNameElement.innerText;

  userName = userName.split(' ')[0]; // Take the part before the first space

  // Check the length and apply the necessary transformation
  if (userName.length > 6) {
      userName = userName.substring(0, 5) + ".."; // Trim to 7 characters
  }

  //set it back to username and display it.
  userNameElement.textContent = userName;
}


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

window.addEventListener('scroll', checkBoxes);
checkBoxes(); // Initial check



/*---- About Us - Read more functionality ----*/
  const AbReadMore = document.querySelector(".section #read-more");
  const text = document.querySelector('.section .show-more');
  let readMore = true;
    
  AbReadMore.addEventListener("click", function() {
    if (readMore==true){
      text.classList.remove("hide");
      readMore = false;
      AbReadMore.innerHTML = "Read Less";
      } else {
      text.classList.add("hide");
      readMore = true;
      AbReadMore.textContent = "Read More";
      }
    });
