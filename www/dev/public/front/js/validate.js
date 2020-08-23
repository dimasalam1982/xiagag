const ErrorClass = {
    errorClass: 'error',
    errorTextClass: 'error-hint'
};

function validateField(field)
{
    if (field.val().length == 0){
        field.addClass(ErrorClass.errorClass);
        if (field.parent().find('.' + ErrorClass.errorTextClass).length == 0) {
            field.parent().append('<div class="' + ErrorClass.errorTextClass + '">Can not empty</div>');
        }
    }else{
        field.removeClass(ErrorClass.errorClass);
        field.parent().find('.' + ErrorClass.errorTextClass).remove();
    }
}

function objectFormValid(ObjectContent)
{
    return ObjectContent.find('.' + ErrorClass.errorClass).length == 0;
}