const quizData = [
    {
        question: "1. He has ( ) friends",
        a: "a few",
        b: "few",
        c: "a little",
        correct: "b"
    },
    {
        question: "2. When did you see Clare last? ( ) days ago",
        a: "A Few",
        b: "Few",
        c: "A little",
        correct: "a"
    },
    {
        question: "3. In Japan we have ( ) rain in June.",
        a: "many",
        b: "much",
        c: "lot",
        correct: "b"
    },
    {
        question: "4. It costs ( ) to repair a car.",
        a: "many",
        b: "a lot",
        c: "lot",
        correct: "b"
    },
    {
        question: "5. Ann has ( ) free time.",
        a: "a little",
        b: "a few",
        c: "little",
        correct: "c"
    },
    {
        question: "6. Your English is very ( ).",
        a: "well",
        b: "good",
        correct: "b"
    },
    {
        question: "7. Chris did very ( ) in his exam.",
        a: "well",
        b: "good",
        correct: "a"
    },
    {
        question: "8. Did you have a ( ) holiday?",
        a: "well",
        b: "good",
        correct: "b"
    },
    {
        question: "9. How are your parents? Are they ( )?",
        a: "well",
        b: "good",
        correct: "a"
    },
    {
        question: "10. I enjoy the book very ( )?",
        a: "well",
        b: "much",
        correct: "b"
    },
    {
        question: "9. How are your parents? Are they ( )?",
        a: "well",
        b: "good",
        correct: "a"
    },
    {
        question: "9. How are your parents? Are they ( )?",
        a: "well",
        b: "good",
        correct: "a"
    }
];

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

shuffleArray(quizData);

const quiz = document.getElementById('quiz');
const submitBtn = document.getElementById('submit');
const result = document.getElementById('result');

function loadQuiz() {
    quiz.innerHTML = '';
    quizData.forEach((q, index) => {
        const questionEl = document.createElement('div');
        questionEl.classList.add('question');
        questionEl.innerHTML = `
            <p>${q.question}</p>
            <div class="answers">
                ${q.a ? `<label><input type="radio" name="q${index}" value="a"> ${q.a}</label>` : ""}
                ${q.b ? `<label><input type="radio" name="q${index}" value="b"> ${q.b}</label>` : ""}
                ${q.c ? `<label><input type="radio" name="q${index}" value="c"> ${q.c}</label>` : ""}
                ${q.d ? `<label><input type="radio" name="q${index}" value="d"> ${q.d}</label>` : ""}
            </div>
        `;
        quiz.appendChild(questionEl);
    });
}

submitBtn.addEventListener('click', () => {
    let score = 0;
    quizData.forEach((q, index) => {
        const selected = document.querySelector(`input[name="q${index}"]:checked`);
        if (selected && selected.value === q.correct) {
            score++;
        }
    });
    result.innerHTML = `<h2>Bạn trả lời đúng ${score} / ${quizData.length} câu</h2>`;
});

loadQuiz();
