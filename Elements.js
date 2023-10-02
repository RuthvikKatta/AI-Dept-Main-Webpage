const appheaderElement = document.getElementsByTagName('app-header')[0];

appheaderElement.innerHTML = `
    <div class="logo"> </div>
    <h1 class="dept-name">Dept of<span>AI</span><span>Artificial Intelligence</span></h1>
    <i class="fa-solid fa-bars fa-2x"></i>
    <ul class="navigation-bar">
        <li href="#Home" class="active">Home</li>
        <li onclick="location.href='./ProjectsModule/projects_index.php'" class="">Projects</li>
        <li href="#Publications" class="">Publications</li>
        <li href="#Mentoring" class="">Mentoring</li>
        <li class="dropdown">Download Material
            <i class="fa-solid fa-caret-up"></i>
            <div class="dropdown-content">
                <a href="#">Material</a>
                <a href="#">Question paper</a>
                <a href="#">E-Books</a>
            </div>
        </li>
    </ul> `    