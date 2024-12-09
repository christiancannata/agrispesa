

<div class="container-fluid meteofetch">
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


<div class="row firstInfo align-items-center">

  <div class="col-md-12 col-lg-6">
    <p class="h4 tempril"><br class="d-block d-lg-none"><strong><?php echo $temperatura; ?>°C</strong></p>
    <br class="d-block d-lg-none">
  </div>
  <div class="col-md-12 col-lg-6">
    <div class="row microinfo">
      <div class="col-md-12"><p><span class="evid">Minima: <br class="d-block d-lg-none"><?php echo $minima; ?></span> alle <?php echo $minimaTempo24; ?></p></div>
      <div class="col-md-12"><p><span class="evid">Massima: <br class="d-block d-lg-none"><?php echo $massima; ?></span> alle <?php echo $massimaTempo24; ?></p></div>

    </div>
  </div>

</div>

<div class="row altreinfo align-items-center">
  <div class="col-lg-3 col-md-12"><p><span class="evid"><i class="fas fa-wind fa-fw"></i><br class="d-block">Vento</span> </p></div>
  <div class="col-lg-3 col-md-12"><p>Velocità:<br class="d-block"> <?php echo $velocitaVento; ?> km/h</p></div>
  <div class="col-lg-6 col-md-12"><p>Direzione:<br class="d-block"><?php echo $direzioneVento; ?></p></div>
  <div class="w-100"></div>
  <div class="sep"></div>
  <div class="col-lg-3 col-md-12"><p><span class="evid"><i class="fas fa-tint fa-fw"></i><br class="d-block">Umidità</span> </p></div>
  <div class="col-lg-3 col-md-12"><p><?php echo $umidita; ?>%</p></div>
  <div class="col-lg-6 col-md-12"><p>Punto di Rugiada:<br class="d-block d-lg-none"> <?php echo $rugiada; ?>°C</p></div>
  <div class="w-100"></div>
  <div class="sep"></div>
  <div class="col-lg-3 col-md-12"><p><span class="evid"><i class="fas fa-cloud-rain fa-fw"></i><br class="d-block">Pioggia</span> </p></div>
  <div class="col-lg-3 col-md-12"><p>In questo momento:<br class="d-block"><?php echo $pioggia; ?>mm</p></div>
  <div class="col-lg-6 col-md-12"><p>Questo mese:<br class="d-block"><?php echo $pioggiamese; ?>mm</p></div>
  <div class="w-100"></div>
  <div class="sep"></div>
  <div class="col-lg-3 col-md-12"><p><span class="evid"><i class="fas fa-signal fa-fw"></i><br class="d-block">Pressione</span> </p></div>
  <div class="col-lg-3 col-md-12"><p><?php echo $pressione; ?> millibar</p></div>
  <div class="col-lg-6 col-md-12"><p>Temperatura Percepita:<br class="d-block"><?php echo $percepita; ?>°C</p></div>
  <div class="w-100"></div>
  <div class="sep"></div>

</div>

<div class="row pluginfooter align-items-center icone">

  <!--<div class="col-lg-4 col-md-12 icone">-->
    <!--<div class="row align-items-center icone">-->
    <?php ciclaCaso($ic); ?>

<!--  </div>-->
<!--  </div>-->
<div class="col-lg-12 col-md-12 portate">
  <h4>Portate con voi:</h4>
<p>
<?php echo nl2br($portate); ?>
<?php if ($isRaining): ?>
  <i class="fas fa-umbrella ombrello"></i>
<p><strong>(Non dimenticate l'ombrello).</strong></p>
<?php endif; ?>
</p>

</div>

<div class="sep"></div>
</div>
<div class="row consigli align-items-center text-center">
<div class="col-md-12">
  <h4>Caprimeteo.it<br class="d-block d-lg-none"> ti consiglia:</h4>
  <p>
<?php echo nl2br(random_consiglio($dir));?>
</p>
</div>

</div>

</div>
