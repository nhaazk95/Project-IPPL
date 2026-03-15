<?php
if (isset($response) && is_array($response) && @$response['response'] == "negative") { ?>
    <script>
        swal('Error', '<?php echo $response['alert'] ?>', 'error');
    </script>
<?php } else if (isset($response) && is_array($response) && @$response['response'] == "positive") { ?>
    <script>
        <?php if (!empty($response['redirect'])): ?>
        // FIX: Auto redirect langsung tanpa perlu klik tombol
        swal({
            title: "Berhasil",
            text: "Mengalihkan...",
            type: "success",
            showConfirmButton: false,
            timer: 1000
        });
        setTimeout(function() {
            window.location.href = "<?php echo $response['redirect'] ?>";
        }, 1000);
        <?php else: ?>
        swal({
            title: "Berhasil",
            text: "<?php echo $response['alert'] ?? '' ?>",
            type: "success",
            showCancelButton: false,
            confirmButtonText: "OK",
            closeOnConfirm: true
        });
        <?php endif; ?>
    </script>
<?php } ?>