WinPos.UserSetup = (function(){
    var initPasswordValidation = function() {
        // Password strength validation
        $('#password').on('input', function() {
            var password = $(this).val();
            var strength = WinPos.Common.checkPasswordStrength(password);
            WinPos.Common.displayPasswordStrength(strength, '#password-strength');
        });

        // Password confirmation validation
        $('#password_confirmation').on('input', function() {
            var password = $('#password').val();
            var confirmPassword = $(this).val();
            WinPos.Common.checkPasswordMatch(password, confirmPassword, '#password-match');
        });

        // Form submission validation
        $('#createUserForm').on('submit', function(e) {
            var password = $('#password').val();
            var confirmPassword = $('#password_confirmation').val();
            
            if (!WinPos.Common.validatePasswordStrength(password)) {
                e.preventDefault();
                toastr.error('Password does not meet strength requirements.');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                toastr.error('Passwords do not match.');
                return false;
            }
        });
    }

    return {
        initPasswordValidation: initPasswordValidation
    }
})();
