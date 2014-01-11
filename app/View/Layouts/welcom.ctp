<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        PYNS TODO(仮)(β)
    </title>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="description" content="">
    <meta name="author" content="Shungo Ishino">

    <?php echo $this->Html->css('bootstrap.min'); ?>
    <?php echo $this->Html->css('bootstrap-responsive.min'); ?>
    <?php echo $this->Html->css('zebra-default'); ?>
    <?php echo $this->Html->css('style'); ?>
    <?php echo $this->Html->script('jquery.min'); ?>
    <?php echo $this->Html->script('bootstrap.min'); ?>
    <?php echo $this->Html->script('zebra_datepicker'); ?>
    <?php echo $this->Html->script('script'); ?>
</head>
<body>
<div class="container">
    <div id="welcom" class="row clearfix">
        <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content'); ?>
    </div>
    <div id="footer" class="row clearfix">
        <div class="col-md-12 column">
            <p class="about">
                <a href="/">ホーム</a>
                <span>|</span>
                <a href="/suports/rule">このサイトについて</a>
            </p>
            <p class="text-center">
                &copy;2013-2014 PYNS CREATE. All rights Reserved.
            </p>
        </div>
    </div>
</div>
<!-- ajax用 -->
<?php echo isset($token)?"<script>var token = '{$token}'</script>":"";?>
<?php echo $this->Html->script('ajax'); ?>
<?php echo $this->Js->writeBuffer(array( 'inline' => 'true')); ?>
<?php echo $this->Html->script('jquery-ui-1.10.3.custom.min'); ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19902347-17']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
