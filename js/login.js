console.log("JavaScript connected!");
const form = document.getElementById("loginForm");
const username = document.getElementById("enrollId");
const password = document.getElementById("password");
const message = document.getElementById("message");

form.addEventListener("submit", function(event) {

    event.preventDefault();

    const enteredUsername = username.value;
    const enteredPassword = password.value;

    if (enteredUsername === "25DCS075" && enteredPassword === "25dcs075@123") {

        window.location.href = "profile.html";

    } else {

        message.textContent = "Invalid Enrollment ID or Password.";

    }

});