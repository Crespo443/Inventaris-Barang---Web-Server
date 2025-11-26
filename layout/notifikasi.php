<!-- Komponen Modal Notifikasi yang bisa dipanggil dari halaman manapun -->
<!-- Gunakan fungsi showNotification(pesan, tipe) untuk menampilkan notifikasi -->

<style>
    .modal-notifikasi .modal-header {
        border: none;
    }

    .modal-notifikasi .modal-body {
        padding: 2rem;
    }

    .notif-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>

<!-- Modal Notifikasi -->
<div class="modal fade modal-notifikasi" id="modalNotifikasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" id="modalNotifHeader">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modalNotifBody">
                <div class="notif-icon" id="modalNotifIcon"></div>
                <h4 class="mb-3" id="modalNotifTitle"></h4>
                <p class="text-muted" id="modalNotifMessage"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk menampilkan notifikasi modal
    function showNotification(pesan, tipe = 'success', judul = '') {
        var icon, headerClass, iconClass, defaultTitle;

        if (tipe === 'success') {
            icon = 'fas fa-check-circle';
            headerClass = 'bg-success';
            iconClass = 'text-success';
            defaultTitle = 'Berhasil!';
        } else if (tipe === 'error' || tipe === 'danger') {
            icon = 'fas fa-times-circle';
            headerClass = 'bg-danger';
            iconClass = 'text-danger';
            defaultTitle = 'Gagal!';
        } else if (tipe === 'warning') {
            icon = 'fas fa-exclamation-triangle';
            headerClass = 'bg-warning';
            iconClass = 'text-warning';
            defaultTitle = 'Peringatan!';
        } else if (tipe === 'info') {
            icon = 'fas fa-info-circle';
            headerClass = 'bg-info';
            iconClass = 'text-info';
            defaultTitle = 'Informasi';
        }

        $('#modalNotifHeader').removeClass().addClass('modal-header ' + headerClass + ' text-white');
        $('#modalNotifIcon').html('<i class="' + icon + ' ' + iconClass + '"></i>');
        $('#modalNotifTitle').text(judul || defaultTitle);
        $('#modalNotifMessage').text(pesan);

        $('#modalNotifikasi').modal('show');
    }

    // Toast notification (notifikasi kecil di pojok)
    function showToast(pesan, tipe = 'success') {
        var bgClass = tipe === 'success' ? 'bg-success' :
            tipe === 'error' || tipe === 'danger' ? 'bg-danger' :
            tipe === 'warning' ? 'bg-warning' : 'bg-info';

        var icon = tipe === 'success' ? 'fa-check-circle' :
            tipe === 'error' || tipe === 'danger' ? 'fa-times-circle' :
            tipe === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

        var toast = $('<div class="toast-notification ' + bgClass + ' text-white">' +
            '<i class="fas ' + icon + ' mr-2"></i>' + pesan + '</div>');

        $('body').append(toast);

        setTimeout(function() {
            toast.addClass('show');
        }, 100);

        setTimeout(function() {
            toast.removeClass('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 3000);
    }
</script>

<style>
    /* Style untuk toast notification */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        opacity: 0;
        transform: translateX(400px);
        transition: all 0.3s ease;
        max-width: 350px;
        font-weight: 500;
    }

    .toast-notification.show {
        opacity: 1;
        transform: translateX(0);
    }
</style>