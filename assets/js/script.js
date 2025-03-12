jQuery(document).ready(function ($) {
    function updateCharCount(input, countSpan, warning, maxLength) {
        var remaining = maxLength - input.val().length;
        countSpan.text(remaining);
        if (remaining < 0) {
            warning.show();
        } else {
            warning.hide();
        }
    }

    $('#city_seo_title').on('input', function () {
        updateCharCount($(this), $('#title-char-count'), $('#title-warning'), 160);
    });

    $('#city_seo_desc').on('input', function () {
        updateCharCount($(this), $('#desc-char-count'), $('#desc-warning'), 160);
    });

    updateCharCount($('#city_seo_desc'), $('#desc-char-count'), $('#desc-warning'), 160);
});

jQuery(document).ready(function ($) {
    $('#select-all').on('click', function () {
        $('input[name="city_ids[]"]').prop('checked', this.checked);
        toggleDeleteButton(); 
    });

    $('input[name="city_ids[]"]').on('click', function () {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        }
        toggleDeleteButton(); 
    });

    function toggleDeleteButton() {
        if ($('input[name="city_ids[]"]:checked').length > 0) {
            $('#delete-selected').prop('disabled', false); 
        } else {
            $('#delete-selected').prop('disabled', true); 
        }
    }

    toggleDeleteButton();
});