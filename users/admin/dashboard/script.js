let list = document.querySelectorAll(".navigation li");
let main = document.querySelector('main');
let sections = main.querySelectorAll('section');

const url = window.location.href;
const sectionIdMatch = url.match(/#(.+)$/);

var sectionId = "";

if(sectionIdMatch === null){
    sectionId = sections[0].id;
    window.location.href = "../dashboard/dashboard.php#" + sectionId;
} else {
    sectionId = sectionIdMatch[1];
}

let activeNavItem = sectionIdMatch ? document.querySelector(`a[href="#${sectionId}"]`).parentElement : list[0];
let activeSection = sectionIdMatch ? document.getElementById(sectionId) : sections[0];

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

// let table = new DataTable('#cummulative-attendance', {
//     responsive: true,
//     paging: false,
// });