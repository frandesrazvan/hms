var hms = {};
const CLIENT_PHOTO = "/uploads/photos/client.png";
const MANAGER_PHOTO = "/uploads/photos/manager.png";
const OWNER_PHOTO = "/uploads/photos/owner.png";
const EMPLOYEE_PHOTO = "/uploads/photos/employee.png";
const CURRENT_URL = window.location.href;
const photos = [CLIENT_PHOTO, MANAGER_PHOTO, OWNER_PHOTO, EMPLOYEE_PHOTO];
const numberStaff = $('#numberStaff').text();
const numberOnPage = $('#numberOnPage').text();
/* DISABLE MY ACCOUNT INPUTS
 -------------------------------*/
hms.disableEdit = () => {
    $('.editable').prop('disabled', true);
    $('#my_account_gender_0').prop('disabled', true);
    $('#my_account_gender_1').prop('disabled', true);
}

/* Handle picture buttons
 -------------------------------*/
hms.showPictureButtons = () => {
    const image = $("#user_photo").attr("src");
    if (photos.includes(image)) {
        $('.delete_photo').addClass("hidden");
        $('.add_photo').removeClass("hidden");
    } else {
        $('.add_photo').addClass("hidden");
        $('.delete_photo').removeClass("hidden");
    }
    $('.add_photo_browser').addClass("hidden");
}
/* ACTIVATE MY ACCOUNT INPUTS
 -------------------------------*/
hms.activateEdit = () => {
    $('#edit_button').on('click', function () {
        $('.beforeEdit').toggleClass("hidden");
        $('.editing').toggleClass("hidden");
        $('.editable').prop('disabled', false);
        $('#my_account_gender_0').prop('disabled', false);
        $('#my_account_gender_1').prop('disabled', false);
        window.sessionStorage.setItem('user', JSON.stringify(hms.saveData()));
    });
    $('#cancel_button').on('click', function () {
        hms.showPictureButtons();
        hms.disableEdit();
        $('.beforeEdit').toggleClass("hidden");
        $('.editing').toggleClass("hidden");
        const user = JSON.parse(window.sessionStorage.getItem('user'));
        hms.restoreData(user);
    });
}

hms.saveData = () => {
    const user = {
        email: $('#register_email').val(),
        address: $('#register_address').val(),
        birth: $('#register_dateOfBirth').val(),
        bio: $('#register_bio').val(),
        gender: $('#my_account_gender_0:checked').val(),
    }
    return user;
}

hms.restoreData = (user) => {
    $('#register_email').val(user.email);
    $('#register_address').val(user.address);
    $('#register_dateOfBirth').val(user.birth);
    $('#register_bio').val(user.bio);
    if (user.gender) {
        $('#my_account_gender_0').prop("checked", true);
    } else {
        $('#my_account_gender_1').prop("checked", true);
    }
}

hms.deletePhoto = () => {
    $('#delete_photo_button').on('click', function () {
        $.ajax({
            url: "/my-account/delete-my-image",
            success: function () {
                $('.add_photo').toggleClass("hidden");
                $("#user_photo").attr("src", hms.getPhoto());
                $('.delete_photo').toggleClass("hidden");
            }
        });

    });
}

hms.getPhoto = () => {
    switch ($('#photoP').text()) {
        case 'ROLE_MANAGER':
            return MANAGER_PHOTO;
        case 'ROLE_OWNER':
            return OWNER_PHOTO;
        case 'ROLE_EMPLOYEE':
            return EMPLOYEE_PHOTO;
        default:
            return CLIENT_PHOTO;
    }
}

hms.addPhoto = () => {
    $('#add_photo_button').on('click', function () {
        $('.add_photo').toggleClass('hidden');
        $('.add_photo_browser').toggleClass('hidden');
        $('#edit_button').trigger("click");
    })
}

hms.createButtons = () => {
    for (let i = 1; i < numberStaff / numberOnPage + 1; i++) {
        if (CURRENT_URL === $('.table-items').data('userManagement')) {
            $(".pagination").append('<li class="page_buttons" id="page_button_' + i + '"><a href=' + window.location.href + '/all/' + i + '>' + i + '</li>');
        } else {
            const currentPageReg = new RegExp('^'+$('.table-items').data('userManagement')+'/([0-9]+|all)$');
            url = CURRENT_URL;
            while (!url.match(currentPageReg)) {
                url = url.slice(0, -1);
            }
            $(".pagination").append('<li class="page_buttons" id="page_button_' + i + '"><a href=' + url + '/' + i + '>' + i + '</li>');
        }
    }
}
hms.highlightButtons = () => {
    $('.page_buttons').on('click', function () {
        $('.page_buttons').removeClass('active');
        $(this).addClass('active');
    })
}

/* DOCUMENT READY
 -------------------------------*/
$(document).ready(function () {
    hms.showPictureButtons();
    hms.disableEdit();
    hms.activateEdit();
    hms.deletePhoto();
    hms.addPhoto();
    hms.createButtons();
    hms.highlightButtons();
});