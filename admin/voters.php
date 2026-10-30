<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        ভোটার রেজিস্ট্রি তালিকা
        <small>Voter Management & Turnout Status</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> হোম</a></li>
        <li class="active">Voters</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> ত্রুটি!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> সফল!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">নিবন্ধিত ভোটার ও ভোটদানের স্ট্যাটাস</h3>
            </div>
            <div class="box-body table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr style="background:#0f172a; color:#fff;">
                    <th>ছবি</th>
                    <th>ভোটার আইডি (EPIC)</th>
                    <th>ভোটারের নাম</th>
                    <th>মোবাইল নম্বর</th>
                    <th>বিধানসভা ক্ষেত্র</th>
                    <th>ভোটিং স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM voters ORDER BY id DESC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo']) && file_exists('../images/'.$row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      
                      // ফর্ম ৬ থেকে বিধানসভা বের করা যদি ভোটারের টেবিলে না থাকে
                      $ac = $row['constituency'] ?? '';
                      if(empty($ac)){
                        $ph = $row['phone'];
                        $fn = $row['firstname'];
                        $ac_q = $conn->query("SELECT constituency FROM voter_applications WHERE mobile = '$ph' OR firstname = '$fn' LIMIT 1");
                        if($ac_r = $ac_q->fetch_assoc()){
                          $ac = $ac_r['constituency'];
                        }
                      }
                      $ac_display = !empty($ac) ? $ac : 'General';

                      // ভোটিং ব্যাজ
                      $status_badge = ($row['has_voted'] == 1) 
                        ? "<span class='label label-danger' style='font-size:12px;'><i class='fa fa-lock'></i> Voted (Locked)</span>" 
                        : "<span class='label label-success' style='font-size:12px;'><i class='fa fa-check'></i> Eligible</span>";

                      echo "
                        <tr>
                          <td style='text-align:center;'>
                            <img src='".$image."' width='40px' height='40px' style='border-radius:6px; object-fit:cover;'>
                          </td>
                          <td><b style='font-family:monospace; color:#0b63c5; font-size:14px;'>".$row['voters_id']."</b></td>
                          <td>".$row['firstname']." ".$row['lastname']."</td>
                          <td>".htmlspecialchars($row['phone'] ?? 'N/A')."</td>
                          <td><span class='label label-primary' style='font-size:12px;'>".htmlspecialchars($ac_display)."</span></td>
                          <td>".$status_badge."</td>
                          <td>
                            <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> মুছে ফেলুন</button>
                          </td>
                        </tr>
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/voters_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'voters_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.id);
      $('#del_voter').html(response.firstname+' '+response.lastname);
    }
  });
}
</script>
</body>
</html>