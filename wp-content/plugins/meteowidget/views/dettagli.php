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
        </p>
        <p><strong>(Non dimenticate l'ombrello).</strong></p>
        <?php endif; ?>

    </div>

    <div class="sep"></div>
</div>
