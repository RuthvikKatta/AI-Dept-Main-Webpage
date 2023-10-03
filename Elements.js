const appheaderElement = document.getElementsByTagName('app-header')[0];

appheaderElement.innerHTML = `
    <div class="logo"> </div>
    <h1 class="dept-name">Department of Artificial Intelligence</span></h1>
    <ul class="navigation-bar">
        <li><a href="/AI-Main-Page/"> Home </a></li>
        <li class="dropdown">Staff
            <img class="dropdown-icons" src="/AI-Main-Page/assets/caret-up-solid.svg" width="10">
            <div class="dropdown-content">
                <a href="#">Teaching</a>
                <a href="#">Non Teaching</a>
            </div>
        </li>
        <li><a href="#"> Publications </a></li>
        <li><a href="/AI-Main-Page/Projects/"> Projects </a></li>
        <li><a href="#"> Mentoring </a></li>
        <li class="dropdown">Download Material
            <img class="dropdown-icons" src="/AI-Main-Page/assets/caret-up-solid.svg" width="10">
            <div class="dropdown-content">
                <a href="#">Material</a>
                <a href="#">Question paper</a>
                <a href="#">E-Books</a>
            </div>
        </li>
    </ul> `    

const dropDowns = document.querySelectorAll('.dropdown');

dropDowns.forEach((dropDown) => {
    const arrowElement = dropDown.querySelector('.dropdown-icons');
    dropDown.addEventListener('mouseover', () => {
        arrowElement.src = '/AI-Main-Page/assets/caret-down-solid.svg';
    })
    
    dropDown.addEventListener('mouseout', () => {
        arrowElement.src = '/AI-Main-Page/assets/caret-up-solid.svg';
    })
})