
let modal = document.getElementById("modal");
let openModal = document.getElementById("openModal");
let closeModal = document.getElementById("closeModal");

openModal.addEventListener("click", function() {
    modal.style.display = "block";
});

closeModal.addEventListener("click", function() {
    modal.style.display = "none";
});


function createSlider(images, slideImage, nextBtn, prevBtn) {
    let index = 0;

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

let culturalImages = [
    "../images/cultural1.jpg",
    "../images/cultural2.jpg",
    "../images/cultural3.jpg",
    "../images/cultural4.jpg"

];

let culturalSlide = document.getElementById("slideImage");
let culturalNext = document.getElementById("next");
let culturalPrev = document.getElementById("prev");

createSlider(
    culturalImages,
    culturalSlide,
    culturalNext,
    culturalPrev
);

let sportsImages = [
    "../images/sports1.jpg",
    "../images/sports2.png",
    "../images/sports3.jpg",
    "../images/sports4.jpg",
    "../images/sports5.jpg"

];


let sportsSlide = document.getElementById("sportsSlideImage");
let sportsNext = document.getElementById("sportsNext");
let sportsPrev = document.getElementById("sportsPrev");


createSlider(sportsImages, sportsSlide, sportsNext, sportsPrev);