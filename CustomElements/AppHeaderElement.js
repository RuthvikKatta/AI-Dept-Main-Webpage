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
            <div class="logo" onclick="location.href='/AI-Main-Page/index.html'"></div>
            <h1 class="dept-name">Department of Artificial Intelligence</h1>
            <ul class="navigation-bar">
                <li><a href="/AI-Main-Page/AI-Department/"> Home </a></li>
                <li class="dropdown">Staff
                <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
                <div class="dropdown-content">
                    <a href="/AI-Main-Page/Staff/index.php?role=Teaching">Teaching</a>
                    <a href="/AI-Main-Page/Staff/index.php?role=Non Teaching">Non Teaching</a>
                </div>
                </li>
                <li><a href="/AI-Main-Page/Publications/"> Publications </a></li>
                <li><a href="/AI-Main-Page/Projects/"> Projects </a></li>
                <li class="dropdown">Download Material
                    <img class="dropdown-icons" src="/AI-Main-Page/assets/Icons/caret-up-solid.svg" width="10">
                    <div class="dropdown-content">
                        <a href="/AI-Main-Page/Materials/index.php?type=AC"> Academic Calendars </a>
                        <a href="/AI-Main-Page/Materials/index.php?type=SM"> Study Material </a>
                        <a href="/AI-Main-Page/Materials/index.php?type=PQP"> Previous Question paper </a>
                    </div>
                </li>
                <li><a href="../users/login.php">Login</a></li>
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