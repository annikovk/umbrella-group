jQuery('body').on('input', '.fio_field', function(){
    this.value = this.value.replace(/[^a-zа-яё\s]/gi, '');
});

jQuery('body').on('input', '.tel_field', function(){
    this.value = this.value.replace(/[^0-9()+-]/gi, '');
});