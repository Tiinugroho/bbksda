<?php
$base_url = 'http://localhost/bbksda';
?>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; <?= date('Y') ?> <a href="<?= $base_url ?>">BBKSDA Riau</a>.</strong> All rights reserved.
    </footer>

</div><!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="<?= $base_url ?>/public/adminlte/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= $base_url ?>/public/adminlte/bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="<?= $base_url ?>/public/adminlte/plugins/fastclick/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= $base_url ?>/public/adminlte/dist/js/app.min.js"></script>
<!-- DataTables -->
<script src="<?= $base_url ?>/public/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $base_url ?>/public/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= $base_url ?>/public/adminlte/dist/js/demo.js"></script>

<script>
// Auto logout setelah tidak aktif 30 menit
let inactivityTime = function () {
    let time;
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onclick = resetTimer;
    document.onscroll = resetTimer;

    function logout() {
        alert("Anda akan logout karena tidak aktif selama 30 menit.");
        window.location.href = '<?= $base_url ?>/logout.php';
    }

    function resetTimer() {
        clearTimeout(time);
        time = setTimeout(logout, 1800000); // 30 menit
    }
};

inactivityTime();
</script>
</body>
</html>
