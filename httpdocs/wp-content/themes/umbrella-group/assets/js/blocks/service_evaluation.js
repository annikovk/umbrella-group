const cards = document.querySelectorAll('.card');
const dots = document.querySelectorAll('.dot');
const currentSlide = document.getElementById('current-slide');

document.querySelector('.btn-next').addEventListener('click', nextSlide);
document.querySelector('.btn-prev').addEventListener('click', prevSlide);

document.getElementById('btn-dot-1').addEventListener('click', function () {
    addClassActive(cards[2]);
});
document.getElementById('btn-dot-2').addEventListener('click', function () {
    addClassActive(cards[1]);
});
document.getElementById('btn-dot-3').addEventListener('click', function () {
    addClassActive(cards[0]);
});


document.addEventListener('DOMContentLoaded', main);

function main() {
    addClassActive(cards[2]);
    innerText(cards[2].childNodes[1].alt);
}

cards.forEach((item) => {
    item.addEventListener('mouseover', () => {
        addClassActive(item)
    });
});

function innerText(text) {
    currentSlide.innerHTML = text
}

function containsActive() {
    for (let i = 0; i < cards.length; i++) {
        if (cards[i].classList.contains('active')) {
            cards[i].classList.remove('active')
        }
    }
}

function addClassActive(element) {
    containsActive();
    element.classList.add("active");
    innerText(element.childNodes[1].alt);
    mappingSlidAndDot(returnIndexActive());
}

function returnIndexActive() {
    for (let i = 0; i < cards.length; i++) {
        if (cards[i].classList.contains('active')) {
            return i
        }
    }
}

function nextSlide() {
    switch (returnIndexActive()) {
        case 2:
            addClassActive(cards[1]);
            break
        case 1:
            addClassActive(cards[0]);
            break
        case 0:
            addClassActive(cards[2])
            break
    }
}

function prevSlide() {
    switch (returnIndexActive()) {
        case 2:
            addClassActive(cards[0]);
            break
        case 1:
            addClassActive(cards[2]);
            break
        case 0:
            addClassActive(cards[1])
            break
    }
}

function addClassSelect(element) {
    containsSelect();
    element.classList.add("select");
}

function containsSelect() {
    for (let i = 0; i < dots.length; i++) {
        if (dots[i].classList.contains('select')) {
            dots[i].classList.remove('select')
        }
    }
}

function mappingSlidAndDot(index) {
    switch (index) {
        case 2:
            addClassSelect(dots[0])
            break
        case 1:
            addClassSelect(dots[1])
            break
        case 0:
            addClassSelect(dots[2])
            break
    }
}