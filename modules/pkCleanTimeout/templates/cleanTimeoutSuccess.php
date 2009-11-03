<script type="text/javascript" charset="utf-8">
<?php // Break out of iframe or AJAX ?>
	top.location.href = "<?php echo url_for("pkCleanTimeout/cleanTimeoutPhase2") ?>";
</script>
<?php // Just in case of surprises ?>
<?php echo link_to("Click here to continue.", "pkCleanTimeout/cleanTimeoutPhase2") ?>