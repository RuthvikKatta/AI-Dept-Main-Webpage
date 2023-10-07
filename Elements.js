const visionMissionElement = document.getElementsByTagName('vision-mission')[0];
const appheaderElement = document.getElementsByTagName('app-header')[0];

if(visionMissionElement){
    visionMissionElement.innerHTML = `
        <div class="vision">
            <h1>Vision</h1>
            <p>To evolve as a renowned department, producing artificial intelligence developers with excellence
                in
                education, interdisciplinary participation, industry preparedness and research for greater cause
                of
                society.</p>
        </div>
        <div class="mission">
            <h1>Mission</h1>
            <ul>
                <li>Provide ideal training using inventive concepts and technologies in Artificial Intelligence
                    (AI)</li>
                <li>Transform the students into technically competent and socially responsible professionals
                </li>
                <li>Inculcate professional ethics and values, leadership and team building skills to address
                    industrial and societal concerns</li>
                <li>Model the department as a front-runner in AI education and research by establishing centers
                    of excellence</li>
            </ul>
        </div>`
}

if(appheaderElement) {
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
            <li class="dropdown">Publications
                <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
                <div class="dropdown-content">
                    <a href="/AI-Main-Page/Publications/"> Faculty </a>
                    <a href="/AI-Main-Page/Publications/"> Student </a>
                </div>
            </li>
            <li><a href="/AI-Main-Page/Projects/"> Projects </a></li>
            <li><a href="/AI-Main-Page/Mentoring/"> Mentoring </a></li>
            <li class="dropdown">Download Material
                <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
                <div class="dropdown-content">
                    <a href="/AI-Main-Page/Materials/"> Material </a>
                    <a href="/AI-Main-Page/Materials/"> Question paper </a>
                    <a href="/AI-Main-Page/Materials/"> E-Books </a>
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
}
