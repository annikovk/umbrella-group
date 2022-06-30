function spoilerContent() {
    if (document.getElementById('spoiler').style.display === 'none') {
        document.getElementById('spoiler').style.display = ''
        document.getElementById('spoiler-btn-hide').style.display = ''
        document.getElementById('spoiler-btn-show').style.display = 'none'
    } else {
        document.getElementById('spoiler').style.display = 'none'
        document.getElementById('spoiler-btn-show').style.display = ''
        document.getElementById('spoiler-btn-hide').style.display = 'none'
    }
}