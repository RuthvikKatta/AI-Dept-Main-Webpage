const dropDowns = document.querySelectorAll('.dropdown');
const slides = document.getElementsByClassName("mySlides");
const dots = document.getElementsByClassName("dot");
const deptName = document.querySelector('.dept-name');
const hamburger = document.querySelector('.fa-bars');
const close = document.querySelector('.fa-xmark');
const navBar = document.querySelector('.navigation-bar');

dropDowns.forEach((dropDown) => {
    const arrowElement = dropDown.querySelector('.fa-solid');
    dropDown.addEventListener('mouseover', () => {
        arrowElement.classList = 'fa-solid fa-caret-down';
    })

    dropDown.addEventListener('mouseout', () => {
        arrowElement.classList = 'fa-solid fa-caret-up';
    })
})

var slideIndex = 0;
var slideIndexCurrent = 0;
showSlides();

function currentSlide(n) {
    showSlidesCurrent(slideIndexCurrent = n);

}

function showSlidesCurrent(n) {
    var i;

    if (n > slides.length) { slideIndexCurrent = 1 }

    if (n < 0) { slideIndexCurrent = slides.length }
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" current", "");
    }
    slides[slideIndexCurrent - 1].style.display = "block";
    dots[slideIndexCurrent - 1].className += " current";
    slideIndexCurrent++;
}


function showSlides() {
    var i;

    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" current", "");
    }
    slides[slideIndex - 1].style.display = "block";
    dots[slideIndex - 1].className += " current";

    setTimeout(showSlides, 4000);
}

hamburger.addEventListener('click', () => {
    navBar.classList.toggle('expand');
})