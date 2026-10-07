
const slide = document.getElementById("slide");
const images = [
    "./image/news_58.jpg",
    "./image/ni1 (2).jpg",
    "./image/ni1 (4).jpg",
    "./image/ni1 (8).jpg",
    "./image/ni1 (9).jpg",
    "./image/ni1 (12).jpg",
];

let index = 0;

function changeSlide() {
    index++;

    if (index >= images.length) {
        index = 0;
    }

    slide.src = images[index];
}

setInterval(changeSlide, 1500);


/* ==========================ライトボックス========================== */

const popupImages = document.querySelectorAll(".popup");
const lightbox = document.getElementById("lightbox");
const lightboxImg = document.getElementById("lightbox-img");
const closeBtn = document.getElementById("close");

popupImages.forEach(function (img) {

    img.addEventListener("click", function () {

        lightbox.style.display = "flex";
        lightboxImg.src = this.src;

    });

});

closeBtn.addEventListener("click", function () {
    lightbox.style.display = "none";
});

lightbox.addEventListener("click", function (e) {
    if (e.target === lightbox) {
        lightbox.style.display = "none";
    }
});


/* ========================== ページトップへ戻る========================== */

const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", function () {

    if (window.scrollY > 400) {
        topBtn.style.display = "block";
    } else {
        topBtn.style.display = "none";
    }
});

topBtn.addEventListener("click", function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

});


/* ==========================フェードイン========================== */

const fadeElements = document.querySelectorAll(".fade");

function showFade() {

    const trigger = window.innerHeight * 0.85;

    fadeElements.forEach(function (element) {

        const top = element.getBoundingClientRect().top;

        if (top < trigger) {

            element.classList.add("show");

        }

    });

}

window.addEventListener("scroll", showFade);

showFade();