<script>
<?php if ( $version == $latest_version ): ?>
setTimeout('window.location = "<?php echo $this->request->base?>/upgrade/index/done"', 5000);
<?php else: ?>
setTimeout('window.location = "<?php echo $this->request->base?>/upgrade/run"', 5000);
<?php endif; ?>
</script>

<link rel="stylesheet" type="text/css" href="<?php echo $this->request->webroot?>theme/light/css/main.css" />

<div id="header">
    <div class="wrapper">
        <div id="logo" style="float:none;padding: 10px 0;margin:0px;">
            <a id="logo_default" href="<?php echo $this->request->webroot?>"></a>
        </div>
    </div>
</div>

<div class="wrapper">   
    <div id="content">
        <h1>Upgrading Social Network to version <?php echo $version?></h1>        
        <img src="<?php echo $this->request->webroot?>img/indicator.gif" align="absmiddle"> Please wait...
    </div>
</div>