/**
 * Created with JetBrains PhpStorm.
 * User: ivoinov
 * Date: 6/11/13
 * Time: 9:06 PM
 * To change this template use File | Settings | File Templates.
 */

var userVoice = {
    formUrl: 'user/voice/add',
    init: function() {
        var self = this;
        $.getJSON(_url(self.formUrl,{'ajax' : 1 }), function(data){self._setHtmlForm(data)} );
    },
    _setHtmlForm: function(data) {

        var self = this;
        var form = data.form;
        $('#user-voice-form div.modal-body').append(form);
        $('#user-voice-form form').attr('action',_url(this.formUrl,{'ajax':1}));
        $('#user-voice-school-names').parents('div.field-block-select').css('display','none');
        $('#submit-user-voice').click(function(){self.submitUserVoice($('#user-voice-form form'))});
        $('#user-voice-form-type').change(function(){
            self.changeType($('#user-voice-form-type').val(), $('#user-voice-school-names'));
        })
    },
    changeType: function(typeId, element) {
        if(typeId == 1 || typeId == 2) {
            element.parents('div.field-block-select').css('display','block');
            element.css('display','block')
        } else {
            element.parents('div.field-block-select').css('display','none');
            element.css('display','none')
        }
    },
    _isValid: function(formElement) {
        var messageElement = $('#user-voice-message', formElement);
        if(messageElement.val() == '' || messageElement.val().length < 10) {
            $('div.validation-error', messageElement.parents('div.field-block')).html('Минимальная длина сообщения 10 символов')
            return false;
        }
        return true;
    },
    _clearForm: function(formElement) {
        var typeElement = $('#user-voice-form-type', formElement);
        var messageElement = $('#user-voice-message', formElement);
        typeElement.val(3);
        this.changeType(3, $('#user-voice-school-names'))
        messageElement.val('');
        $('div.validation-error').each(function(){
            $(this).html('');
        })
    },
    submitUserVoice: function(userVoiceForm) {
        var self = this;
        if(this._isValid(userVoiceForm)) {
            var typeId = $('#user-voice-form-type', userVoiceForm).val();
            var schoolName = $('#user-voice-school-names', userVoiceForm).val();
            var message = $('#user-voice-message', userVoiceForm).val();
            userVoiceForm.submit(function(){
                $.ajax({
                    type: 'POST',
                    url: _url(self.formUrl),
                    data: {'ajax':1,'type_id':typeId,'message': message,'additional_information': schoolName },
                    async: false,
                    error: function() {
                        self._clearForm();
                    },
                    success: function() {
                        self._clearForm();
                        $('#user-voice-form').modal('hide')

                    }
                })
                return false;
            });
            userVoiceForm.submit();
        } else {
            return false;
        }
    }

}
$(function(){
    userVoice.init();
});
