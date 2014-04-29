<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        Higgs
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
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-19902347-18', '8705.co');
  ga('send', 'pageview');

</script>
</body>
</html>
