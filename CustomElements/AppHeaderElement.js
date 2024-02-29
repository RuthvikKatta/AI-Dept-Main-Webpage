const appHeader = {
    'logoLink': "./assets/Images/VJIT_logo_2023.png",
    'deptName': "Department of Artificial Intelligence",
    'navigationBar': {
        'home': {
            'title': 'Home',
            'location': './index.html'
        },

    }
}

class AppHeaderElement extends HTMLElement {
	constructor() {
		super();

		this.innerHTML = `
            <div class="logo"></div>
            <h1 class="dept-name">Department of Artificial Intelligence</h1>
            <ul class="navigation-bar">
                <li><a href="/AI-Main-Page/AI-Department/"> Home </a></li>
                <li class="dropdown">Staff
                <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
                <div class="dropdown-content">
                    <a href="/AI-Main-Page/Staff/staff.php?role=Teaching">Teaching</a>
                    <a href="/AI-Main-Page/Staff/staff.php?role=Non Teaching">Non Teaching</a>
                </div>
                </li>
                <li><a href="/AI-Main-Page/Publications/"> Publication </a></li>
                <li><a href="/AI-Main-Page/Projects/"> Projects </a></li>
                <li><a href="/AI-Main-Page/Mentoring/"> Mentoring </a></li>
                <li class="dropdown">Download Material
                <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
                <div class="dropdown-content">
                <a href="/AI-Main-Page/Materials/"> Study Material </a>
                <a href="/AI-Main-Page/Materials/"> Academic Calendars </a>
                <a href="/AI-Main-Page/Materials/"> Previous Question paper </a>
                    <a href="/AI-Main-Page/Materials/"> E-Books </a>
                </div>
                </li>
            </ul>`;
	}

	connectedCallback() {
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
}

window.customElements.define('app-header', AppHeaderElement);