<?php
/**
 * Main view file of export section
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
do_action('wt_iew_exporter_before_head');
$wf_admin_view_path=plugin_dir_path(WT_O_IEW_PLUGIN_FILENAME).'admin/views/';
?>
<style type="text/css">
.wt_iew_export_step{ display:none; }
.wt_iew_export_step_loader{ width:100%; height:400px; text-align:center; line-height:400px; font-size:14px; }
.wt_iew_export_step_main{ float:left; box-sizing:border-box; padding:15px; padding-bottom:0px; width:95%; margin:30px 2.5%; background:#fff; box-shadow:0px 2px 2px #ccc; border:solid 1px #efefef; }
.wt_iew_export_main{ padding:20px 0px; }
.wt_iew_file_ext_info_td{ vertical-align:top !important; }
.wt_iew_file_ext_info{ display:inline-block; margin-top:3px; }
</style>
<?php
Wt_Iew_IE_Basic_Helper::debug_panel($this->module_base);
?>
<?php include WT_O_IEW_PLUGIN_PATH."/admin/views/_save_template_popup.php"; ?>
<h2 class="wt_iew_page_hd"><?php _e('Export'); ?><span class="wt_iew_post_type_name"></span></h2>
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
			<?php esc_html_e('Unable to handle Re-Run request.');?>
		</div>
		<?php
	}
?>

<div class="wt_iew_loader_info_box"></div>
<div class="wt_iew_overlayed_loader"></div>
<div class="wt_iew_export_step_main_wrapper" style="width:68%; float: left">
	
	<div class="wt-advt-banner-cover" style="position:relative;display:none;">
		<div class="wt-advt-banner" style="display:none;position: absolute; margin-top: 150px; left:25%;color:#FFF;width:375px;height:150px;background: #3199F0;padding: 20px; text-align: center">
			<p class="wt-advt-close" style="float:right;margin-top: 0px !important;line-height: 0;"><a style="color:#FFF;" href="javascript:void(0)" onclick='wt_iew_basic_export.hide_export_ad_box();'>X</a></p>
			<h3 style="color:#fff;"><?php esc_html_e('All variable products from your site is not included in the CSV!'); ?></h3>
			<p><?php esc_html_e('Free version only supports simple, grouped, and external/affiliate product types. Variable product type support is a '); ?><a target="_blank" style="color:#FFF;" href="https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp_variation&utm_campaign=Product_Import_Export"><?php esc_html_e('premium feature'); ?></a>.
			</p>
		</div>
	</div>

	
<div class="wt_iew_export_step_main" style = "width:100%; float: left">
	<?php
	foreach($this->steps as $stepk=>$stepv)
	{
		?>
		<div class="wt_iew_export_step wt_iew_export_step_<?php echo $stepk;?>" data-loaded="0"></div>
		<?php
	}
	?>	
</div>
	
<?php include $wf_admin_view_path."market_front.php" ?>
	</div>
<?php
include $wf_admin_view_path."market.php";