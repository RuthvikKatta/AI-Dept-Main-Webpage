const slides = document.getElementsByClassName("mySlides");

var slideIndex = 0;

function showAdjacentSlides(isLeft) {
    slideIndex = isLeft ? slideIndex - 1 : slideIndex + 1;

    for (var i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }


    if (slideIndex >= slides.length) { 
        slideIndex = 0 
    } else if (slideIndex < 0) { 
        slideIndex = slides.length - 1;
    }

    slides[slideIndex].style.display = "block";
}

function showSlides() {
    for (var i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    slideIndex = ( slideIndex + 1 ) % slides.length;
    slides[slideIndex].style.display = "block";
    setTimeout(showSlides, 4000);
}

showSlides();