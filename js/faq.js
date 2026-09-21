let questions = document.querySelectorAll(".question");

questions.forEach(function(question) {

    question.addEventListener("click", function() {

        let answer = this.nextElementSibling;

        if (answer.style.maxHeight) {
            answer.style.maxHeight = null;
            answer.style.padding = "0";
            answer.style.border = "none";
            question.style.backgroundColor = "white";
            question.style.color = "black";
        } 
        else {
            answer.style.maxHeight = answer.scrollHeight + "px";
            answer.style.padding = "5px";
            answer.style.border = "1px solid #c6daee";
            question.style.backgroundColor = "#040e2b";
            question.style.color = "white";
        }

    });

});
let menuBtn = document.getElementById("menuBtn");
let navMenu = document.getElementById("navMenu");

menuBtn.addEventListener("click", function() {

    navMenu.classList.toggle("show");

});