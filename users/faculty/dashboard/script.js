let list = document.querySelectorAll(".navigation li");
let main = document.querySelector('main');
let sections = main.querySelectorAll('section');

const url = window.location.href;
const sectionIdMatch = url.match(/#(.+)$/);

var sectionId = "";

if (sectionIdMatch === null) {
    window.location.href = "../dashboard/dashboard.php#view-attendance";
    sectionId = sections[0].id;
} else {
    sectionId = sectionIdMatch[1];
}

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

function calculateTotalDays() {
    var fromDate = new Date(document.getElementById('fromDate').value);
    var toDate = new Date(document.getElementById('toDate').value);

    var timeDiff = toDate - fromDate;
    var totalDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

    document.getElementById('totalDays').value = totalDays + 1;
}

function updateDate() {
    let fromDateInput = document.querySelector('[name=fromDate]')
    let toDateInput = document.querySelector('[name=toDate]')
    toDateInput.min = new Date(fromDateInput.value).toISOString().split("T")[0]
}

let fromDateInput = document.querySelector('[name=fromDate]')
fromDateInput.min = new Date().toISOString().split("T")[0]

var menteeItems = document.querySelectorAll('.mentee-item');
var menteeDetails = document.querySelectorAll('.mentee');

menteeItems.forEach(function (item, index) {
    item.addEventListener('click', function () {
        menteeItems.forEach(function (item) {
            item.classList.remove('active');
        });
        menteeDetails.forEach(function (detail) {
            detail.classList.remove('active');
        });

        item.classList.add('active');
        menteeDetails[index].classList.add('active');

        toggleSections();
    });
});

function toggleSections() {
    var activeMentee = document.querySelector('.mentee.active');

    var toggleSection = activeMentee.querySelector('.toggle-sections');
    var commentsSection = activeMentee.querySelector('.comments-section');
    var qnaSection = activeMentee.querySelector('.QnA-section');
    var attendanceSection = activeMentee.querySelector('.attendance');
    var marksSection = activeMentee.querySelector('.marks');
    var backlogsSection = activeMentee.querySelector('.backlogs-report');

    sectionsArray = [commentsSection, qnaSection, attendanceSection, marksSection, backlogsSection];

    var lis = toggleSection.querySelectorAll('li');

    lis.forEach(function (item, index) {
        item.addEventListener('click', function () {
            lis.forEach(function (li) {
                li.classList.remove('active');
            });

            sectionsArray.forEach(function (sec) {
                sec.classList.remove('active');
            });

            item.classList.add('active');
            sectionsArray[index].classList.add('active');
        })
    })
}

document.addEventListener('DOMContentLoaded', toggleSections)