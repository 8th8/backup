
画像スライドショー


const slide = document.getElementById("slide");

const 画像 = [
「images/top1.jpg」
「images/top2.jpg」
「images/top3.jpg」
];

インデックスを0とします。

function changeSlide() {

インデックス++;

if (index >= images.length) {

インデックス = 0;

}

slide.src = images[index];

}

setInterval(changeSlide, 3000);


/* ==========================
ライトボックス
=========================== */

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

closeBtn.onclick = function () {

lightbox.style.display = "none";

};

lightbox.onclick = function (e) {

if (e.target === lightbox) {

lightbox.style.display = "none";

}

};


/* ==========================
ページトップへ戻る
=========================== */

const topBtn = document.getElementById("topBtn");

window.addEventListener("scroll", function () {

if (window.scrollY > 400) {

topBtn.style.display = "block";

} それ以外 {

topBtn.style.display = "none";

}

});

topBtn.addEventListener("click", function () {

window.scrollTo({

上: 0、

動作：「スムーズ」

});

});


/* ==========================
フェードインアニメーション
=========================== */

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


/* ==========================
アクティブメニュー
=========================== */

const sections = document.querySelectorAll("section");

const navLinks = document.querySelectorAll("nav a");

window.addEventListener("scroll", function () {

let current = "";

sections.forEach(function (section) {

const sectionTop = section.offsetTop - 120;

const sectionHeight = section.clientHeight;

if (pageYOffset >= sectionTop &&
pageYOffset < sectionTop + sectionHeight) {

current = section.getAttribute("id");

}

});

navLinks.forEach(function (link) {

link.classList.remove("active");

if (link.getAttribute("href") === "#" + current) {

link.classList.add("active");

}

});

});


/* ==========================
スムーズスクロール
=========================== */

navLinks.forEach(function (link) {

link.addEventListener("click", function (e) {

e.preventDefault();

const target = document.querySelector(this.getAttribute("href"));

target.scrollIntoView({

動作：「スムーズ」

});

});

});


/* ==========================
ヒーローフェードエフェクト
=========================== */

window.addEventListener("scroll", function () {

const hero = document.querySelector(".hero");

hero.style.opacity = 1 - window.scrollY / 700;

});


/* ==========================
画像ホバーエフェクト
=========================== */

popupImages.forEach(function (img) {

img.addEventListener("mouseenter", function () {

this.style.transform = "scale(1.05)";

this.style.transition = "0.3s";

});

img.addEventListener("mouseleave", function () {

this.style.transform = "scale(1)";

});

});


/* ==========================
負荷効果
=========================== */

window.addEventListener("load", function () {

document.body.style.opacity = "1";

});/* このファイルはサイト内のJavaScriptを書くためのものです */

const button = document.getElementById("postBtn");

button.addEventListener("click", function(){

const text = document.getElementById("postText").value;
const file = document.getElementById("postImage").files[0];

if(text==="" && !file){
alert("内容を入力してください。");
戻る;
}

const post = document.createElement("div");
post.className = "post";

const p = document.createElement("p");
p.textContent = テキスト;

post.appendChild(p);

if(file){

const reader = new FileReader();

reader.onload = function(e){

const img = document.createElement("img");

img.src = e.target.result;

post.appendChild(img);

}

reader.readAsDataURL(file);

}

document.getElementById("postList").prepend(post);

document.getElementById("postText").value = "";

document.getElementById("postImage").value = "";

});
