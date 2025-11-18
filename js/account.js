$(document).ready(function() {
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        const fullName = $('#full_name').val().trim();

        if (!fullName) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Full name is required'
            });
            return;
        }

        $.ajax({
            url: 'php/controllers/account_controller.php',
            type: 'POST',
            data: {
                action: 'update_profile',
                full_name: fullName
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });

    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = $('#current_password').val();
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();

        if (!currentPassword || !newPassword || !confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'All fields are required'
            });
            return;
        }

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'New passwords do not match'
            });
            return;
        }

        if (newPassword.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Password must be at least 6 characters long'
            });
            return;
        }

        $.ajax({
            url: 'php/controllers/account_controller.php',
            type: 'POST',
            data: {
                action: 'change_password',
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#passwordForm')[0].reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });
});
