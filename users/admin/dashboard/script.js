let list = document.querySelectorAll(".navigation li");
let main = document.querySelector('main');
let sections = main.querySelectorAll('section');

const url = window.location.href;
const sectionIdMatch = url.match(/#(.+)$/);

let activeNavItem = sectionIdMatch ? document.querySelector(`a[href="#${sectionIdMatch[1]}"]`).parentElement : list[0];
let activeSection = sectionIdMatch ? document.getElementById(sectionIdMatch[1]) : sections[0];

activeNavItem.classList.add("active");
activeSection.classList.add("active");

function activelink() {
    if (activeNavItem) {
        activeNavItem.classList.remove("active");
    }

    if (activeSection) {
        activeSection.classList.remove("active");
    }

    activeNavItem = this;

    let targetSectionId = this.firstChild.getAttribute('href').substring(1);
    activeSection = document.getElementById(targetSectionId);

    activeNavItem.classList.add("active");
    activeSection.classList.add("active");
}

list.forEach((item) => {
    item.addEventListener("click", activelink);
});