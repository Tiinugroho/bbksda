<?php
$base_url = 'http://localhost/bbksda'; // ganti sesuai folder kamu
// include __DIR__ . '/check_login.php';
?>

<!-- <footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.3.0
    </div>
    <strong>Copyright &copy; 2014-2015 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
    reserved.
</footer> -->

</div><!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="<?= $base_url ?>/public/adminlte/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?= $base_url ?>/public/adminlte/bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="<?= $base_url ?>/public/adminlte/plugins/fastclick/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= $base_url ?>/public/adminlte/dist/js/app.min.js"></script>
<!-- Sparkline -->
<script src="<?= $base_url ?>/public/adminlte/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?= $base_url ?>/public/adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?= $base_url ?>/public/adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll 1.3.0 -->
<script src="<?= $base_url ?>/public/adminlte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="<?= $base_url ?>/public/adminlte/plugins/chartjs/Chart.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- AdminLTE for demo purposes -->
<script src="<?= $base_url ?>/public/adminlte/dist/js/demo.js"></script>
<!-- DataTables -->
<script src="<?= $base_url ?>/public/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $base_url ?>/public/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
    $(function() {
        $("#example1").DataTable();
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
    });
</script>
</body>

</html>
