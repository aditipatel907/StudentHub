let form = document.getElementById("registerForm");

form.addEventListener("submit", function(event) {

    let firstName = document.getElementById("firstName").value;
    let lastName = document.getElementById("lastName").value;
    let email = document.getElementById("email").value;
    let mobile = document.getElementById("mobile").value;
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let course = document.getElementById("course").value;
    let year = document.getElementById("year").value;
    let terms = document.getElementById("terms").checked;

    let namePattern = /^[A-Za-z]+$/;
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let mobilePattern = /^[0-9]{10}$/;
    let passwordPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

    if (!namePattern.test(firstName)) {
        alert("Please enter a valid first name");
        return;
    }

    if (!namePattern.test(lastName)) {
        alert("Please enter a valid last name");
        return;
    }

    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address");
        return;
    }

    if (!mobilePattern.test(mobile)) {
        alert("Mobile number must contain exactly 10 digits");
        return;
    }

    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters and contain a letter and a number");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return;
    }


    if (year === "") {
        alert("Please select a year");
        return;
    }

    let gender = document.querySelector('input[name="gender"]:checked');

    if (!gender) {
        alert("Please select your gender");
        return;
    }

    if (!terms) {
        alert("Please accept the terms and conditions");
        return;
    }

    form.submit();

});