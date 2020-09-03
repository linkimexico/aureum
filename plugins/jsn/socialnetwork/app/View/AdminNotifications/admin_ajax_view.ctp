<?php echo $this->Jsnsocial->getName( $notification['User'] )?> <?php echo $notification['AdminNotification']['text']?>
<p>"<?php echo $this->Jsnsocial->formatText($notification['AdminNotification']['message'])?>"</p>
<div align="center">
    <a href="<?php echo $notification['AdminNotification']['url']?>" class="button button-action">View</a>
</div> 