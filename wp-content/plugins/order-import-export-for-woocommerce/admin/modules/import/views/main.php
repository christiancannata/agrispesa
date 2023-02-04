<?php
/**
 * Main view file of import section
 *
 * @link            
 *
 * @package  Wt_Import_Export_For_Woo
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php
do_action('wt_iew_importer_before_head');
$wf_admin_view_path=plugin_dir_path(WT_O_IEW_PLUGIN_FILENAME).'admin/views/';
?>
<style type="text/css">
.wt_iew_import_step{ display:none; }
.wt_iew_import_step_loader{ width:100%; height:400px; text-align:center; line-height:400px; font-size:14px; }
.wt_iew_import_step_main{ float:left; box-sizing:border-box; padding:15px; padding-bottom:0px; width:95%; margin:30px 2.5%; background:#fff; box-shadow:0px 2px 2px #ccc; border:solid 1px #efefef; }
.wt_iew_import_main{ padding:20px 0px; }
select[name=wt_iew_file_from]{visibility: hidden;}
</style>
<div class="wt_iew_view_log wt_iew_popup" style="text-align:left">
	<div class="wt_iew_popup_hd">
		<span style="line-height:40px;" class="dashicons dashicons-media-text"></span>
		<span class="wt_iew_popup_hd_label"><?php _e('History Details');?></span>
		<div class="wt_iew_popup_close">X</div>
	</div>
    <div class="wt_iew_log_container" style="padding:25px;">
		
	</div>
</div>

<div class="wt_iew_import_progress_wrap wt_iew_popup">
		<div class="wt_iew_popup_hd wt_iew_import_progress_header">
			<span style="line-height:40px;" class="dashicons dashicons-media-text"></span>
			<span class="wt_iew_popup_hd_label"><?php _e('Import progress');?></span>
			<div class="wt_iew_popup_close">X</div>
		</div>
		<div class="wt_iew_import_progress_content"  style="max-height:620px;overflow: auto;">
					<table id="wt_iew_import_progress" class="widefat_importer widefat wt_iew_import_progress wp-list-table fixed striped history_list_tb log_list_tb">
						<thead>
							<tr>
								<th  style="width:15%" class="row"><?php _e( 'Row' ); ?></th>
								<th  style="width:20%"><?php _e( 'Item' ); ?></th>
								<th  style="width:50%"><?php _e( 'Message' ); ?></th>
								<th  style="width:20%" class="reason"><?php _e( 'Status' ); ?></th>
							</tr>
						</thead>
						<tbody id="wt_iew_import_progress_tbody"></tbody>
					</table>
		</div>
		<br/>
		<div id="wt_iew_import_progress_end"></div>
		<div class="progressa">
			<div class="progressab" style="background-color: rgb(178, 222, 75);width:5px; "></div>
		</div>
		
	<div class="wt-iew-import-completed" style="display:none;border-top: 1px outset;">
		<h3><?php _e('Import Completed'); ?><span style="color:green" class="dashicons dashicons-yes-alt"></span></h3>
		<div class="wt-iew-import-results">
			<div class="wt-iew-import-result-row">
			<div class="wt-iew-import-results-total wt-iew-import-result-column"><?php _e('Total records identified'); ?>:<span id="wt-iew-import-results-total-count"></span></div>
			<div style="color:green" class="wt-iew-import-results-imported wt-iew-import-result-column"><?php _e('Imported successfully'); ?>:<span id="wt-iew-import-results-imported-count"></span></div>
			<div style="color:red" class="wt-iew-import-results-failed wt-iew-import-result-column"><?php _e('Failed/Skipped'); ?>:<span id="wt-iew-import-results-failed-count"></span></div>
			</div>
		</div>
	</div>
	
	
	<div class="wt-iew-plugin-toolbar bottom" style="padding:5px;margin-left:-10px;">
		<div style="float: left">
			<div class="wt-iew-import-time" style="display:none;padding-left: 40px;margin-top:10px;" ><?php _e( 'Time taken to complete' );?>:<span id="wt-iew-import-time-taken"></span></div>
		</div>
		<div style="float:right;">
			<div style="float:right;">
				<a target="_blank" href="#" class="button button-primary wt_iew_view_imported_items" data-log-file="" style="display:none"  type="button" style="margin-right:10px;"><?php _e( 'View Item' );?></a>
				<button class="button button-primary wt_iew_view_log_btn" data-log-file="" style="display:none"  type="button" style="margin-right:10px;"><?php _e( 'View Log' );?></button>
				<button class="button button-primary wt_iew_popup_cancel_btn"  type="button" style="margin-right:10px;"><?php _e( 'Cancel' );?></button>
				<button class="button button-primary wt_iew_popup_close_btn" style="display:none"  type="button" style="margin-right:10px;"><?php _e( 'Close' );?></button>
			</div>
		</div>
	</div>
</div>

<?php
Wt_Iew_IE_Basic_Helper::debug_panel($this->module_base);
?>
<?php include WT_O_IEW_PLUGIN_PATH."/admin/views/_save_template_popup.php"; ?>

<h2 class="wt_iew_page_hd"><?php _e('Import'); ?><span class="wt_iew_post_type_name"></span></h2>
<span class="wt-webtoffee-icon" style="float: <?php echo (!is_rtl()) ? 'right' : 'left'; ?>; padding-<?php echo (!is_rtl()) ? 'right' : 'left'; ?>:30px; margin-top: -25px;">
    <?php _e('Developed by'); ?> <a target="_blank" href="https://www.webtoffee.com">
        <img src="<?php echo WT_O_IEW_PLUGIN_URL.'/assets/images/webtoffee-logo_small.png';?>" style="max-width:100px;">
    </a>
</span>

<?php
	if($requested_rerun_id>0 && $this->rerun_id==0)
	{
		?>
		<div class="wt_iew_warn wt_iew_rerun_warn">
			<?php _e('Unable to handle Re-Run request.');?>
		</div>
		<?php
	}
?>

<div class="wt_iew_loader_info_box"></div>
<div class="wt_iew_overlayed_loader"></div>

<div class="wt_iew_import_step_main_wrapper" style="width:68%; float: left">
<div class="wt_iew_import_step_main" style = "width:100%; float: left">
	<?php
	foreach($this->steps as $stepk=>$stepv)
	{
		?>
		<div class="wt_iew_import_step wt_iew_import_step_<?php echo $stepk;?>" data-loaded="0"></div>
		<?php
	}
	?>	
</div>
	
<?php include $wf_admin_view_path."market_front.php" ?>
	</div>
<?php
include $wf_admin_view_path."market.php";
?>
<script type="text/javascript">
/* external modules can hook */
function wt_iew_importer_validate_basic(action, action_type, is_previous_step)
{
	var is_continue=true;
	<?php
	do_action('wt_iew_importer_validate_basic');
	?>
	return is_continue;
}
function wt_iew_importer_reset_form_data_basic()
{
	<?php
	do_action('wt_iew_importer_reset_form_data_basic');
	?>
}
</script>