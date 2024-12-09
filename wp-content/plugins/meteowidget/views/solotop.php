<?php

include(MY_PLUGIN_PATH. './inc/loop.php');
//$meteoFetcherBE::makeHistory();

?>
<div class="row">
<div class="col-md-12">
  <h3 class="fetcherTitle"><?php echo $stationName; ?></h3>
  <div class="fetcherInfo">
  <small class="text-muted">Ultimo aggiornamento: <br class="d-block d-lg-none"><strong><?php echo $lastUpdateDay; ?> alle <?php echo $lastUpdateHour; ?></strong><br/>
  Rilevazione effettuata a: <br class="d-block d-lg-none"><strong><?php echo $location; ?></strong></small><br/>
  <small class="text-muted">Alba alle ore: <strong><?php echo $alba24; ?></strong><br class="d-block d-lg-none"><span class="d-sm-none d-none d-md-none d-lg-inline"> |</span> Tramonto alle ore: <strong><?php echo $tramonto24; ?></strong></small>
  </div>
</div>
</div>


<div class="row firstInfo align-items-center <?php echo $allerta; if ($isChill) : echo " chill"; endif;?>">

  <div class="col-md-12 col-lg-6">
<div class="row">
  <div class="col-md-12 col-lg-4">
    <?php if ($isRaining) : ?>
      <i class="fas fa-cloud-rain cloud"></i>
    <?php endif; ?>
    <?php if ($isWindy) : ?>
    <?php endif; ?>
    <?php if ($isChill) : ?>
      <i class="fas fa-snowflake snow"></i>
    <?php endif; ?>
  </div>
  <div class="col-md-12 col-lg-8 temp">
    <p class="h4 tempril"><br class="d-block d-lg-none"><strong><?php echo $temperatura; ?>°C</strong></p>
    <br class="d-block d-lg-none">
  </div>

</div>

  </div>



  <div class="col-md-12 col-lg-6">
    <div class="row microinfo">
      <div class="col-md-12"><p><span class="evid">Minima: <br class="d-block d-lg-none"><?php echo $minima; ?></span> alle <?php echo $minimaTempo24; ?></p></div>
      <div class="col-md-12"><p><span class="evid">Massima: <br class="d-block d-lg-none"><?php echo $massima; ?></span> alle <?php echo $massimaTempo24; ?></p></div>

    </div>
  </div>


  <?php if ($isChill) : ?>
      <div class="col-md-12 col-12 avvisoall">
  <p><?php echo $alertmessagec; ?></p>
      </div>
  <?php endif; ?>

<?php if ($allerta) : ?>
    <div class="col-md-12 col-12 avvisoall <?php echo $allerta; ?>">
<p><?php echo $alertIntro; ?><?php echo $alertmessagep; ?> <?php echo $alertmessagev; ?> <?php echo $alertmessagec; ?></p>
    </div>
<?php endif; ?>



<div class="col-12 history clearfix">
  <p>Stati di attenzione nelle ultime 24 ore</p>
        <?php include(MY_PLUGIN_PATH .'views/last24.php'); ?>
</div>


</div>
