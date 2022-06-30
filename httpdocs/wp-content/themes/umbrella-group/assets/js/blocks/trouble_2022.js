function spoilerContentTrouble2022() {
    if (document.getElementById('spoiler-trouble-2022').style.display === 'none') {
        document.getElementById('spoiler-trouble-2022').style.display = ''
        document.getElementById('spoiler-btn-hide-trouble-2022').style.display = ''
        document.getElementById('spoiler-btn-show-trouble-2022').style.display = 'none'
    } else {
        document.getElementById('spoiler-trouble-2022').style.display = 'none'
        document.getElementById('spoiler-btn-show-trouble-2022').style.display = ''
        document.getElementById('spoiler-btn-hide-trouble-2022').style.display = 'none'
    }
}