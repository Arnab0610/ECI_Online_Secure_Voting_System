<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?php echo (!empty($user['photo'])) ? '../images/'.$user['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo isset($user['firstname']) ? htmlspecialchars($user['firstname'].' '.$user['lastname']) : 'Admin User'; ?></p>
        <a><i class="fa fa-circle text-success"></i> <?php echo isset($user['role']) ? ucfirst($user['role']) : 'Admin'; ?></a>
      </div>
    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">CORE MATRIX</li>
      
      <!-- ১. ড্যাশবোর্ড -->
      <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
        <a href="home.php"><i class="fa fa-dashboard"></i> <span>Dashboard & Analytics</span></a>
      </li>


      <!-- ২. ভোটার ব্যবস্থাপনা -->
      <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['kyc_workflow.php', 'voter_approval.php', 'voters.php', 'voter_tracking.php']) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-id-card"></i>
          <span>Voter Management</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <!-- নতুন BLO / ERO Approval Board মেনু -->
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'kyc_workflow.php' ? 'active' : ''; ?>">
            <a href="kyc_workflow.php"><i class="fa fa-id-badge"></i> BLO / ERO Approval Board</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'voter_approval.php' ? 'active' : ''; ?>">
            <a href="voter_approval.php"><i class="fa fa-check-circle"></i> Verification & KYC</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'voters.php' ? 'active' : ''; ?>">
            <a href="voters.php"><i class="fa fa-users"></i> Voter Registry</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'voter_tracking.php' ? 'active' : ''; ?>">
            <a href="voter_tracking.php"><i class="fa fa-pie-chart"></i> Turnout Tracking</a>
          </li>
        </ul>
      </li>

      <!-- ৩. নির্বাচন ও প্রার্থী ব্যবস্থাপনা -->
      <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['election_schedule.php', 'positions.php', 'candidates.php', 'ballot.php']) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-bullhorn"></i>
          <span>Elections & Slate</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'election_schedule.php' ? 'active' : ''; ?>">
            <a href="election_schedule.php"><i class="fa fa-calendar"></i> Schedule Elections</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'positions.php' ? 'active' : ''; ?>">
            <a href="positions.php"><i class="fa fa-tasks"></i> Category / Positions</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'candidates.php' ? 'active' : ''; ?>">
            <a href="candidates.php"><i class="fa fa-black-tie"></i> Candidates List</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'ballot.php' ? 'active' : ''; ?>">
            <a href="ballot.php"><i class="fa fa-file-text"></i> Ballot Position</a>
          </li>
        </ul>
      </li>

      <!-- ৪. সিকিউরিটি ও অ্যাক্সেস কন্ট্রোল -->
      <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['rbac_roles.php', 'two_factor.php', 'kill_switch.php']) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-shield"></i>
          <span>Security & Control</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'rbac_roles.php' ? 'active' : ''; ?>">
            <a href="rbac_roles.php"><i class="fa fa-user-secret"></i> RBAC Admin Roles</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'two_factor.php' ? 'active' : ''; ?>">
            <a href="two_factor.php"><i class="fa fa-key"></i> 2FA / MFA Settings</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'kill_switch.php' ? 'active' : ''; ?>">
            <a href="kill_switch.php"><i class="fa fa-power-off text-danger"></i> Emergency Freeze</a>
          </li>
        </ul>
      </li>

      <!-- ৫. অডিট লগ ও ভেরিফিকেশন -->
      <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['audit_logs.php', 'blockchain_ledger.php']) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-link"></i>
          <span>Audit & Ledger</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'voter_face_logs.php' ? 'active' : ''; ?>">
            <a href="voter_face_logs.php"><i class="fa fa-camera text-aqua"></i> <span>Biometric Face Logs</span></a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'audit_logs.php' ? 'active' : ''; ?>">
            <a href="audit_logs.php"><i class="fa fa-history"></i> Immutable Audit Logs</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'blockchain_ledger.php' ? 'active' : ''; ?>">
            <a href="blockchain_ledger.php"><i class="fa fa-cubes"></i> Vote Hash Verification</a>
          </li>
        </ul>
      </li>

      <!-- ৬. ফলাফল ও রিপোর্ট -->
      <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['votes.php', 'decrypt_vault.php', 'tally_encrypted_votes.php']) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-lock"></i>
          <span>Results & Vault</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'tally_encrypted_votes.php' ? 'active' : ''; ?>">
            <a href="tally_encrypted_votes.php"><i class="fa fa-calculator text-yellow"></i> ভোট গণনা ও ফলাফল</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'votes.php' ? 'active' : ''; ?>">
            <a href="votes.php"><i class="fa fa-edit"></i> Total Votes Tally</a>
          </li>
          <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'decrypt_vault.php' ? 'active' : ''; ?>">
            <a href="decrypt_vault.php"><i class="fa fa-unlock-alt"></i> Decrypt Vault Key</a>
          </li>
        </ul>
      </li>

    </ul>
  </section>
  <!-- /.sidebar -->
</aside>