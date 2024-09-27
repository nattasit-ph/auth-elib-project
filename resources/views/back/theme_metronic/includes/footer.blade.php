<?php
	$filename = 'version.txt';
	$vs = '';
	if (file_exists($filename)) {
		$handle = fopen($filename, "r");
		$vs = fread($handle, filesize($filename));
		fclose($handle);
		$txt_version = "version ".$vs;
	}
?>
<!-- begin:: Footer -->
<div class="kt-footer  kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop" id="kt_footer">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-footer__copyright">
			<?=(!empty($vs) ? $txt_version . shell_exec("git log -1 --pretty=format:'%h'") : '')?>
		</div>
		<div class="kt-footer__menu d-none">
			<a href="#" target="_blank" class="kt-footer__menu-link kt-link">Contact</a>
		</div>
	</div>
</div>
<!-- end:: Footer -->