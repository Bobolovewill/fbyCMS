<?php $dashboard = true; ?>
<?php require_once "layouts/header.php"; ?>
<div id="page-inner">
    <div class="row text-center pad-top">
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="genders.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-mars fa-5x"></i>
                      <h4><?php echo language('genders', $_SESSION['lang']); ?></h4>
                </a>
            </div>
        </div>       
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="nationalities.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-flag fa-5x"></i>
                    <h4><?php echo language('nationalities', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="nationality-types.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-home fa-5x"></i>
                    <h4><?php echo language('natTypes', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="departments.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-university fa-5x"></i>
                    <h4><?php echo language('departments', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="cities.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-building fa-5x"></i>
                    <h4><?php echo language('cities', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="areas.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-building-o fa-5x"></i>
                    <h4><?php echo language('areas', $_SESSION['lang']); ?></h4>
                </a>
            </div>        
        </div>
    </div>
    <!-- /. ROW  --> 
    <div class="row text-center pad-top">
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="machine-types.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-wheelchair fa-5x"></i>
                      <h4><?php echo language('machine-types', $_SESSION['lang']); ?></h4>
                </a>
            </div>
        </div>       
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="machines.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-wheelchair-alt fa-5x"></i>
                    <h4><?php echo language('machines', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="transfers.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-ambulance fa-5x"></i>
                    <h4><?php echo language('transfers', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="position-roles.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-briefcase fa-5x"></i>
                    <h4><?php echo language('positionRoles', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="users.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-user-circle-o fa-5x"></i>
                    <h4><?php echo language('users', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="employees.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-user-md fa-5x"></i>
                    <h4><?php echo language('employees', $_SESSION['lang']); ?></h4>
                </a>
            </div>        
        </div>
    </div>
    <!-- /. ROW  --> 
    <div class="row text-center pad-top">
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="patients.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-users fa-5x"></i>
                      <h4><?php echo language('patients', $_SESSION['lang']); ?></h4>
                </a>
            </div>
        </div>       
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="phoneNumbers.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-address-book-o fa-5x"></i>
                    <h4><?php echo language('phoneNumbers', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="articles.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-id-card-o fa-5x"></i>
                    <h4><?php echo language('articles', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="disabilities.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-credit-card fa-5x"></i>
                    <h4><?php echo language('disabilities', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="financials.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-money fa-5x"></i>
                    <h4><?php echo language('financials', $_SESSION['lang']); ?></h4>
                </a>
            </div>         
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
            <div class="div-square">
                <a href="receipts.php?lang=<?php echo $selectedLang; ?>&manage=view" >
                    <i class="fa fa-bell-o fa-5x"></i>
                    <h4><?php echo language('receipts', $_SESSION['lang']); ?></h4>
                </a>
            </div>        
        </div>
    </div>
    <!-- /. ROW  --> 
</div>
<?php require_once "layouts/footer.php"; ?>