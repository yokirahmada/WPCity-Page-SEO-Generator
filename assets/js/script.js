jQuery(document).ready(function ($) {
    // Contoh: Tambahkan efek hover pada tombol
    $('.button-primary').hover(
        function () {
            $(this).css('background-color', '#005177');
        },
        function () {
            $(this).css('background-color', '#0073aa');
        }
    );
});

jQuery(document).ready(function ($) {
    // Fungsi untuk menghitung karakter
    function updateCharCount(input, countSpan, warning, maxLength) {
        var remaining = maxLength - input.val().length;
        countSpan.text(remaining);
        if (remaining < 0) {
            warning.show();
        } else {
            warning.hide();
        }
    }

    // Pantau input SEO Title
    $('#city_seo_title').on('input', function () {
        updateCharCount($(this), $('#title-char-count'), $('#title-warning'), 160);
    });

    // Pantau input SEO Description
    $('#city_seo_desc').on('input', function () {
        updateCharCount($(this), $('#desc-char-count'), $('#desc-warning'), 160);
    });

    // Inisialisasi hitungan karakter saat halaman dimuat
    updateCharCount($('#city_seo_desc'), $('#desc-char-count'), $('#desc-warning'), 160);
});

jQuery(document).ready(function ($) {
    // Fungsi untuk memilih semua checkbox
    $('#select-all').on('click', function () {
        $('input[name="city_ids[]"]').prop('checked', this.checked);
        toggleDeleteButton(); // Perbarui status tombol
    });

    // Fungsi untuk memastikan "Select All" tidak tercentang jika ada checkbox yang tidak terpilih
    $('input[name="city_ids[]"]').on('click', function () {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        }
        toggleDeleteButton(); // Perbarui status tombol
    });

    // Fungsi untuk mengaktifkan/nonaktifkan tombol "Delete Selected"
    function toggleDeleteButton() {
        if ($('input[name="city_ids[]"]:checked').length > 0) {
            $('#delete-selected').prop('disabled', false); // Aktifkan tombol
        } else {
            $('#delete-selected').prop('disabled', true); // Nonaktifkan tombol
        }
    }

    // Panggil fungsi saat halaman dimuat
    toggleDeleteButton();
});