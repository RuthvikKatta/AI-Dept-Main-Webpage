const appheaderElement = document.getElementsByTagName('app-header')[0];

appheaderElement.innerHTML = `
    <div class="logo"> </div>
    <h1 class="dept-name">Department of Artificial Intelligence</span></h1>
    <ul class="navigation-bar">
        <li><a href="/AI-Main-Page/"> Home </a></li>
        <li class="dropdown">Staff
            <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
            <div class="dropdown-content">
                <a href="/AI-Main-Page/Staff/">Teaching</a>
                <a href="/AI-Main-Page/Staff/">Non Teaching</a>
            </div>
        </li>
        <li><a href="/AI-Main-Page/Publications/"> Publications </a></li>
        <li><a href="/AI-Main-Page/Projects/"> Projects </a></li>
        <li><a href="/AI-Main-Page/Mentoring/"> Mentoring </a></li>
        <li class="dropdown">Download Material
            <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
            <div class="dropdown-content">
                <a href="/AI-Main-Page/Materials/"> Material</a>
                <a href="/AI-Main-Page/Materials/"> Question paper</a>
                <a href="/AI-Main-Page/Materials/"> E-Books</a>
            </div>
        </li>
    </ul> `    

const dropDowns = document.querySelectorAll('.dropdown');

dropDowns.forEach((dropDown) => {
    const arrowElement = dropDown.querySelector('.dropdown-icons');
    dropDown.addEventListener('mouseover', () => {
        arrowElement.src = '/AI-Main-Page/assets/Icons/caret-down-solid.svg';
    })
    
    dropDown.addEventListener('mouseout', () => {
        arrowElement.src = '/AI-Main-Page/assets/Icons/caret-up-solid.svg';
    })
})