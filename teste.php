<div class="progress-indicator">
        <div id="indicator-1" class="active">1</div>
        <div id="indicator-2">2</div>
        <div id="indicator-3">3</div>
    </div>


    .progress-indicator { display: flex; justify-content: space-around; margin-bottom: 15px; }
        .progress-indicator div { width: 30px; height: 30px; background: #ccc; color: #000; border-radius: 50%; line-height: 30px; font-weight: bold; text-align: center; }
        .progress-indicator .active { background: #628A4C; color: #fff; }
        .btn-submit, .btn-next { background-color: #628A4C; color: #FFF; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-weight: bold; }
        .btn-submit:hover, .btn-next:hover { background-color: #556a3e; }
        .btn-section {
          background: none;
          border: none;
          color: #FFF;
          cursor: pointer;
          font-weight: bold;
          margin: 0 5px;
          padding: 5px;
          transition: color 0.3s, background-color 0.3s;
        }
        .btn-next { margin-top: 10px; }




        let currentSection = 0;
    const sections = document.querySelectorAll('.section');
    const indicators = document.querySelectorAll('.progress-indicator div');

    function nextSection() {
        if (currentSection < sections.length - 1) {
            sections[currentSection].classList.remove('active');
            indicators[currentSection].classList.remove('active');
            currentSection++;
            sections[currentSection].classList.add('active');
            indicators[currentSection].classList.add('active');
        }
    }