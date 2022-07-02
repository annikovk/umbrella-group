const displays = document.querySelectorAll('.img_mac-up-phone');
const dotsPhone = document.querySelectorAll('.dotPhone');

document.getElementById('up-phone').addEventListener('click', move_up);
document.getElementById('down-phone').addEventListener('click', move_down);

document.getElementById('btn-dot-phone-1').addEventListener('click', function () {
    addClassActivePhone(displays[0]);
});
document.getElementById('btn-dot-phone-2').addEventListener('click', function () {
    addClassActivePhone(displays[1]);
});
document.getElementById('btn-dot-phone-3').addEventListener('click', function () {
    addClassActivePhone(displays[2]);
});
document.getElementById('phone_slider_on_call').addEventListener('wheel', function () {
    console.log(event.wheelDeltaY)
    //console.log(event)
    if (event.wheelDeltaY > 0) {
        move_up()
    } else if (event.wheelDeltaY < 0) {
        move_down();
    }

});


function move_up() {
    switch (returnIndexActivePhone()) {
        case 0:
            addClassActivePhone(displays[0]);
            break
        case 1:
            addClassActivePhone(displays[0]);
            break
        case 2:
            addClassActivePhone(displays[1])
            break
    }
}

function move_down() {
    switch (returnIndexActivePhone()) {
        case 0:
            addClassActivePhone(displays[1]);
            break
        case 1:
            addClassActivePhone(displays[2]);
            break
        case 2:
            addClassActivePhone(displays[2])
            break
    }
}

function addClassActivePhone(element) {
    containsActivePhone();
    element.classList.add("active_mac-up-phone");
    mappingSlidAndDotPhone(returnIndexActivePhone());

}

function returnIndexActivePhone() {
    for (let i = 0; i < displays.length; i++) {
        if (displays[i].classList.contains('active_mac-up-phone')) {
            return i
        }
    }
}

function containsActivePhone() {
    for (let i = 0; i < displays.length; i++) {
        if (displays[i].classList.contains('active_mac-up-phone')) {
            displays[i].classList.remove('active_mac-up-phone')
        }
    }
}

function containsSelectPhone() {
    for (let i = 0; i < dotsPhone.length; i++) {
        if (dotsPhone[i].classList.contains('select_phone')) {
            dotsPhone[i].classList.remove('select_phone')
        }
    }
}

function mappingSlidAndDotPhone(index) {
    switch (index) {
        case 2:
            addClassSelectPhone(dotsPhone[2])
            break
        case 1:
            addClassSelectPhone(dotsPhone[1])
            break
        case 0:
            addClassSelectPhone(dotsPhone[0])
            break
    }
}

function addClassSelectPhone(element) {
    containsSelectPhone();
    element.classList.add("select_phone");
}

