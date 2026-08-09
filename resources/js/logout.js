// Fungsi Logout dengan SweetAlert2
window.handleLogout = function(event) {
    event.preventDefault();
    event.stopPropagation();

    console.log('✅ Logout function triggered!');

    // Cek apakah SweetAlert2 sudah loaded
    if (typeof Swal === 'undefined') {
        console.error('❌ SweetAlert2 tidak loaded!');
        alert('Error: SweetAlert2 library not loaded');
        return false;
    }

    console.log('✅ SweetAlert2 is loaded, showing confirmation...');

    // Tampilkan pop-up konfirmasi
    Swal.fire({
        title: 'Yakin ingin Logout?',
        text: 'Anda akan keluar dari akun ini',
        icon: 'warning',
        iconColor: '#ef4444',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('✅ User confirmed logout');

            // Tampilkan loading 2 detik
            Swal.fire({
                title: 'Logging Out...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                timer: 2000,
                timerProgressBar: true,
            }).then(() => {
                console.log('✅ Submitting logout form...');
                document.getElementById('logout-form').submit();
            });
        } else {
            console.log('❌ User cancelled logout');
        }
    });

    return false;
};
