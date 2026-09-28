let images = [
    "../images/img1.png",
    "../images/img2.jpg",
    "../images/img3.jpg",
    "../images/img4.jpg",
    "../images/sports1.jpg",
    "../images/cultural1.jpg"
];

let index = 0;

let slideImage = document.getElementById("slideImage");

let nextBtn = document.getElementById("next");
let prevBtn = document.getElementById("prev");

if (slideImage && nextBtn && prevBtn) {
    nextBtn.addEventListener("click", function() {
        index++;
        if (index >= images.length) {
            index = 0;
        }
        slideImage.src = images[index];
    });

    prevBtn.addEventListener("click", function() {
        index--;
        if (index < 0) {
            index = images.length - 1;
        }
        slideImage.src = images[index];
    });
}
