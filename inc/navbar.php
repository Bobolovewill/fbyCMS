  <!-- Static navbar -->
  <nav class="navbar navbar-default" style="margin-top: 20px;">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" data-toggle="tooltip" title="<?php echo language('dashboard', $_SESSION['lang']); ?>" href="dashboard.php?lang=<?php echo $selectedLang; ?>"><i class="fa fa-heartbeat fa-2x" aria-hidden="true"></i></a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav icons">
          <li><a data-toggle="tooltip" title="<?php echo language('genders', $_SESSION['lang']); ?>" href="genders.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-mars fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('nationalities', $_SESSION['lang']); ?>" href="nationalities.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-flag fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('natTypes', $_SESSION['lang']); ?>" href="nationality-types.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-home fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('departments', $_SESSION['lang']); ?>" href="departments.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-university fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('cities', $_SESSION['lang']); ?>" href="cities.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-building fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('areas', $_SESSION['lang']); ?>" href="areas.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-building-o fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('machine-types', $_SESSION['lang']); ?>" href="machine-types.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-wheelchair fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('machines', $_SESSION['lang']); ?>" href="machines.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-wheelchair-alt fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('transfers', $_SESSION['lang']); ?>" href="transfers.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-ambulance fa-2x" aria-hidden="true"></i></a></li>                                                  
          <li><a data-toggle="tooltip" title="<?php echo language('positionRoles', $_SESSION['lang']); ?>" href="position-roles.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-briefcase fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('users', $_SESSION['lang']); ?>" href="users.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"></i></a></li>
          <li><a data-toggle="tooltip" title="<?php echo language('employees', $_SESSION['lang']); ?>" href="employees.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-user-md fa-2x" aria-hidden="true"></i></a></li>   
          <li><a data-toggle="tooltip" title="<?php echo language('patients', $_SESSION['lang']); ?>" href="patients.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-users fa-2x" aria-hidden="true"></i></a></li>  
          <li><a data-toggle="tooltip" title="<?php echo language('phoneNumbers', $_SESSION['lang']); ?>" href="phoneNumbers.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-address-book-o fa-2x" aria-hidden="true"></i></a></li>  
          <li><a data-toggle="tooltip" title="<?php echo language('articles', $_SESSION['lang']); ?>" href="articles.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-id-card-o fa-2x" aria-hidden="true"></i></a></li>  
          <li><a data-toggle="tooltip" title="<?php echo language('disabilities', $_SESSION['lang']); ?>" href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-credit-card fa-2x" aria-hidden="true"></i></a></li>  
          <li><a data-toggle="tooltip" title="<?php echo language('financials', $_SESSION['lang']); ?>" href="financials.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-money fa-2x" aria-hidden="true"></i></a></li>  
          <li><a data-toggle="tooltip" title="<?php echo language('receipts', $_SESSION['lang']); ?>" href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-bell-o fa-2x" aria-hidden="true"></i></a></li>  
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo language("dropdown", $_SESSION['lang']); ?> <i class="fa fa-chevron-down fa-2x" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
              <li><a href="genders.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-mars fa-1x" aria-hidden="true"></i> <?php echo language('genders', $_SESSION['lang']); ?></a></li>
              <li><a href="nationalities.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-flag fa-1x" aria-hidden="true"></i> <?php echo language('nationalities', $_SESSION['lang']); ?></a></li>
              <li><a href="nationality-types.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-home fa-1x" aria-hidden="true"></i> <?php echo language('natTypes', $_SESSION['lang']); ?></a></li>
              <li><a href="departments.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-university fa-1x" aria-hidden="true"></i> <?php echo language('departments', $_SESSION['lang']); ?></a></li>
              <li><a href="cities.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-building fa-1x" aria-hidden="true"></i> <?php echo language('cities', $_SESSION['lang']); ?></a></li>
              <li><a href="areas.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-building-o fa-1x" aria-hidden="true"></i> <?php echo language('areas', $_SESSION['lang']); ?></a></li>
              <li><a href="machine-types.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-wheelchair fa-1x" aria-hidden="true"></i> <?php echo language('machine-types', $_SESSION['lang']); ?></a></li>
              <li><a href="machines.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-wheelchair-alt fa-1x" aria-hidden="true"></i> <?php echo language('machines', $_SESSION['lang']); ?></a></li>                                                                                    
              <li><a href="transfers.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-ambulance fa-1x" aria-hidden="true"></i> <?php echo language('transfers', $_SESSION['lang']); ?></a></li>              
              <li><a href="position-roles.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-briefcase fa-1x" aria-hidden="true"></i> <?php echo language('positionRoles', $_SESSION['lang']); ?></a></li>
              <li><a href="phoneNumbers.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-address-book-o fa-1x" aria-hidden="true"></i> <?php echo language('phoneNumbers', $_SESSION['lang']); ?></a></li>
              <li><a href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-credit-card fa-1x" aria-hidden="true"></i> <?php echo language('disabilities', $_SESSION['lang']); ?></a></li>
              <li><a href="financials.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-money fa-1x" aria-hidden="true"></i> <?php echo language('financials', $_SESSION['lang']); ?></a></li>
              <li><a href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-bell-o fa-1x" aria-hidden="true"></i> <?php echo language('receipts', $_SESSION['lang']); ?></a></li>
              <li role="separator" class="divider"></li>
              <li><a href="users.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-circle-o fa-1x" aria-hidden="true"></i> <?php echo language('users', $_SESSION['lang']); ?></a></li>
              <li><a href="employees.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-user-md fa-1x" aria-hidden="true"></i> <?php echo language('employees', $_SESSION['lang']); ?></a></li>
              <li><a href="patients.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-users fa-1x" aria-hidden="true"></i> <?php echo language('patients', $_SESSION['lang']); ?></a></li>
              <li><a href="articles.php?lang=<?php echo $selectedLang; ?>&manage=view"><i class="fa fa-id-card-o fa-1x" aria-hidden="true"></i> <?php echo language('articles', $_SESSION['lang']); ?></a></li>
            </ul>
          </li>
          <li><a data-toggle="tooltip" title="<?php echo language('logout', $_SESSION['lang']); ?>" href="logout.php?lang=<?php echo $selectedLang; ?>"><i class="fa fa-sign-out fa-2x" aria-hidden="true"></i></a></li>
        </ul>
      </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
  </nav>