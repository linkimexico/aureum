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
        <h1>Upgrading Social Network Plugin</h1>
        
        <span style="font-size:14px">
        <p>Please make sure that you backup all files and database before proceeding</p>
        <p>Current version: <span style="color:red"><?php echo $jsnsocial_setting['version']?></span></p>
        <p>Latest version: <span style="color:green"><?php echo $latest_version?></span></p>
        </span>
        
        <a href="<?php echo $this->request->base?>/upgrade/run" class="button button-action">Proceed</a>
    </div>
</div>