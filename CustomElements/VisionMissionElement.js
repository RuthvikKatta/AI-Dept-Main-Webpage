class VisionMissionElement extends HTMLElement {
	constructor() {
		super();

		this.innerHTML = `
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
        </div>`;
	}
}

window.customElements.define('vision-mission', VisionMissionElement);