const appheaderElement = document.getElementsByTagName('app-header')[0];

appheaderElement.innerHTML = `
    <div class="logo"> </div>
    <h1 class="dept-name">Dept of<span>AI</span><span>Artificial Intelligence</span></h1>
    <i class="fa-solid fa-bars fa-2x"></i>
    <ul class="navigation-bar">
        <li><a href="AI-Dept-Main-Webpage-development/index.html"> Home </a></li>
        <li><a href="./ProjectsModule/projects_index.php" class=""> Projects </a></li>
        <li><a href="#Publications" class=""> Publications </a></li>
        <li><a href="#Mentoring" class=""> Mentoring </a></li>
        <li class="dropdown">Download Material
            <i class="fa-solid fa-caret-up"></i>
            <div class="dropdown-content">
                <a href="#">Material</a>
                <a href="#">Question paper</a>
                <a href="#">E-Books</a>
            </div>
        </li>
    </ul> `    