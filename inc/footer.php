  <!-- jQuery -->
  <script src="<?php echo $js; ?>jquery-3.2.1.min.js"></script>
  <!-- moment.js for datetimepicker -->
  <script src="<?php echo $js; ?>moment.min.js"></script>
  <!-- Bootstrap -->
  <script src="<?php echo $js; ?>bootstrap.min.js"></script>
  <!-- Bootstrap datetimepicker -->
  <script src="<?php echo $js; ?>bootstrap-datetimepicker.min.js"></script>
  <!-- DataTable scripts -->
  <script src="<?php echo $js; ?>jquery.dataTables.min.js"></script>
  <script src="<?php echo $js; ?>dataTables.bootstrap.min.js"></script>
  <script>
    $('.table').dataTable();
  </script>
  <!-- Custom script -->
  <script src="<?php echo $js; ?>custom.js"></script>
  <?php if (isset($dashboard)) { ?>
    <!-- CUSTOM DASHBOARD SCRIPT -->
    <script src="<?php echo $js; ?>dashboard.js"></script>
  <?php } ?>
  <!-- Parsley Form Validation -->
  <script src="<?php echo $js; ?>parsley.min.js"></script>
  <!-- Bootstrap Tooltip -->
  <script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
  <!-- Bootstrap-datepicker -->
  <script src="<?php echo $js; ?>bootstrap-datepicker.min.js"></script>
  <!-- <script>
      $(document).ready(function(){
          var date_input=$('input[name="date"]'); //our date input has the name "date"
          var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
          date_input.datepicker({
              format: 'mm-dd-yyyy',
              container: container,
              todayHighlight: true,
              autoclose: true,
          })
      })
  </script> -->
  <!-- Bootstrap-datetimepicker -->
  <script type="text/javascript">
      $(function () {
          // $('#datetimepickerempdob').datetimepicker();
          // $('#datetimepickeremphierDate').datetimepicker();
          $('#datetimepickerempdob').datetimepicker({
              format: 'YYYY-MM-DD',
          });
          $('#datetimepickeremphierDate').datetimepicker({
              format: 'YYYY-MM-DD',
          });
          $('#datetimepickerpatdob').datetimepicker({
              format: 'YYYY-MM-DD',
          });
          $('#datetimepickerarticleDate').datetimepicker({
              format: 'YYYY-MM-DD',
          });
          $('#datetimepickerreceiptDate').datetimepicker({
              format: 'YYYY-MM-DD',
          });
          $('#datetimepickerreceiptdeliveryDate').datetimepicker({
              format: 'YYYY-MM-DD',
          });
      });
  </script>
  <footer class="footer">
    <div class="container">
    &copy;  2017 Mohamed Alansary
      <span class="text-muted">
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?lang=ar">العربية</a>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?lang=en">English</a>
      </span>
    </div>
  </footer>
</body>
</html>
<?php ob_end_flush(); ?>