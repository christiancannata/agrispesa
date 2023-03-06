<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'wp-import-export-lite' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php' ) ) {
        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php');
}
$wpie_export = new \wpie\export\WPIE_Export();

$export_type = $wpie_export->get_export_type();

$wpie_taxonomies_list = $wpie_export->wpie_get_taxonomies();

$templates = array();

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-common-action.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-common-action.php');
        $cmm_act   = new WPIE_Common_Actions();
        $templates = $cmm_act->get_export_list();
        unset( $cmm_act );
}

$ext_tab_files = apply_filters( 'wpie_manage_export_tab_files', array() );

?>

<div class="wpie_main_container">
        <div class="wpie_content_header">
                <div class="wpie_content_header_inner_wrapper">
                        <div class="wpie_content_header_title"><?php esc_html_e( 'Manage Export', 'wp-import-export-lite' ); ?></div>
                        <a class="wpie_btn wpie_btn_primary ms-4" href="https://1.envato.market/1krom" target="_blank">
                            <?php esc_html_e( 'Upgrade Pro', 'wp-import-export-lite' ); ?>
                        </a>
                </div>
        </div>
        <div class="wpie_content_wrapper">
                <div class="wpie_section_wrapper">
                        <div class="wpie_content_data_header wpie_section_wrapper_selected">
                                <div class="wpie_content_title"><?php esc_html_e( 'Export Log', 'wp-import-export-lite' ); ?></div>
                                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
                        </div>
                        <div class="wpie_section_content wpie_show">
                                <div class="wpie_table_action_wrapper">
                                        <div class="wpie_table_action_container">
                                                <select class="wpie_content_data_select wpie_log_bulk_action">
                                                        <option value=""><?php esc_html_e( 'Bulk Actions', 'wp-import-export-lite' ); ?></option>   
                                                        <option value="delete"><?php esc_html_e( 'Delete', 'wp-import-export-lite' ); ?></option>   
                                                </select>
                                        </div>
                                        <div class="wpie_table_action_btn_container">
                                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_log_action_btn">
                                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Apply', 'wp-import-export-lite' ); ?>
                                                </div>
                                        </div>
                                </div>
                                <table class="wpie_log_table table table-bordered">
                                        <thead>
                                                <tr>
                                                        <td class="wpie_log_check_wrapper">
                                                                <input type="checkbox" class="wpie_checkbox wpie_log_check_all" id="wpie_log_check_all" value="1"/>
                                                                <label for="wpie_log_check_all" class="wpie_checkbox_label"></label>
                                                        </td>
                                                        <td class="wpie_log_lable"><?php esc_html_e( 'File Name', 'wp-import-export-lite' ); ?></td>
                                                        <td class="wpie_log_lable"><?php esc_html_e( 'Query', 'wp-import-export-lite' ); ?></td>
                                                        <td class="wpie_log_lable"><?php esc_html_e( 'Summary', 'wp-import-export-lite' ); ?></td>
                                                        <td class="wpie_log_lable"><?php esc_html_e( 'Date', 'wp-import-export-lite' ); ?></td>
                                                        <td class="wpie_log_lable"><?php esc_html_e( 'Status', 'wp-import-export-lite' ); ?></td>
                                                        <td class="wpie_log_lable"><?php esc_html_e( 'actions', 'wp-import-export-lite' ); ?></td>
                                                </tr>
                                        </thead>
                                        <tbody>
                                                <?php
                                                $is_empty_template = "";
                                                if ( !empty( $templates ) ) {

                                                        $date_format = get_option( 'date_format' );

                                                        $time_format = get_option( 'time_format' );

                                                        $date_time_format = $date_format . " " . $time_format;

                                                        $is_empty_template = "wpie_hidden";

                                                        foreach ( $templates as $template ) {

                                                                $date = isset( $template->create_date ) ? $template->create_date : "";

                                                                $id = isset( $template->id ) ? $template->id : 0;

                                                                $opration_type = isset( $template->opration_type ) ? $template->opration_type : "";

                                                                $last_update_date = isset( $template->last_update_date ) ? $template->last_update_date : "";

                                                                $process_log = isset( $template->process_log ) ? maybe_unserialize( $template->process_log ) : array();

                                                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                                                $fileName = isset( $options[ 'fileName' ] ) ? $options[ 'fileName' ] : "";

                                                                $status = isset( $template->status ) ? $template->status : "";

                                                                $process_status = __( 'Processing', 'wp-import-export-lite' );

                                                                if ( $status == "completed" ) {
                                                                        $process_status = __( 'Completed', 'wp-import-export-lite' );
                                                                } elseif ( $status == "paused" ) {
                                                                        $process_status = __( 'Paused', 'wp-import-export-lite' );
                                                                } elseif ( $status == "stopped" ) {
                                                                        $process_status = __( 'Stopped', 'wp-import-export-lite' );
                                                                }

                                                                $opration_subtype = "";

                                                                if ( $opration_type === "taxonomies" ) {
                                                                        $typeLabel        = $opration_type;
                                                                        $opration_subtype = isset( $options[ 'wpie_taxonomy_type' ] ) && !empty( $options[ 'wpie_taxonomy_type' ] ) ? $options[ 'wpie_taxonomy_type' ] : "";

                                                                        $opration_subtype = !empty( $opration_subtype ) && isset( $wpie_taxonomies_list[ $opration_subtype ] ) ? $wpie_taxonomies_list[ $opration_subtype ] : $opration_subtype;
                                                                } else {
                                                                        $typeLabel = isset( $export_type[ $opration_type ] ) ? $export_type[ $opration_type ] : $opration_type;
                                                                }

                                                                $newType = $typeLabel . (empty( $opration_subtype ) ? "" : " > " . $opration_subtype);

                                                                ?>
                                                                <tr class="wpie_log_wrapper wpie_log_wrapper_<?php echo esc_attr( $id ); ?>">
                                                                        <td class="wpie_log_check_wrapper">
                                                                                <input type="checkbox" class="wpie_checkbox wpie_log_check" id="wpie_log_check_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $id ); ?>"/>
                                                                                <label for="wpie_log_check_<?php echo esc_attr( $id ); ?>" class="wpie_checkbox_label"></label>
                                                                        </td>
                                                                        <td class="wpie_log_data"><?php echo esc_html( $fileName ); ?></td>
                                                                        <td class="wpie_log_data"><?php echo empty( $newType ) ? "" : esc_html( ucwords( $newType ) ); ?></td>
                                                                        <td class="wpie_log_data">
                                                                                <?php
                                                                                echo esc_html( __( "Last run", 'wp-import-export-lite' ) . " : " . date_i18n( $date_time_format, strtotime( $last_update_date ) ) );

                                                                                ?>
                                                                                <br /> 
                                                                                <?php
                                                                                echo esc_html( (isset( $process_log[ 'exported' ] ) ? $process_log[ 'exported' ] : 0 ) . " " . __( "Records Exported", 'wp-import-export-lite' ) );

                                                                                ?>
                                                                                <br /> 
                                                                                <?php
                                                                                echo esc_html( (isset( $process_log[ 'total' ] ) ? $process_log[ 'total' ] : 0 ) . " " . __( "Total", 'wp-import-export-lite' ) );

                                                                                ?>
                                                                        </td>
                                                                        <td class = "wpie_log_data"><?php echo esc_html( date_i18n( $date_time_format, strtotime( $date ) ) );

                                                                                ?></td>
                                                                        <td class="wpie_log_data wpie_log_status"><?php echo esc_html( $process_status ); ?></td>
                                                                        <td class="wpie_log_data wpie_action_<?php echo esc_attr( $status ); ?>" >
                                                                                <div class="wpie_log_action_btns wpie_delete_template_btn"><i class="fas fa-trash wpie_general_btn_icon wpie_data_tipso" data-tipso="<?php esc_attr_e( 'Delete', 'wp-import-export-lite' ); ?>" aria-hidden="true"></i></div>
                                                                                <div class="wpie_log_action_btns wpie_download_template_file_btn"><i class="fas fa-download wpie_general_btn_icon wpie_data_tipso" data-tipso="<?php esc_attr_e( 'Download', 'wp-import-export-lite' ); ?>"  aria-hidden="true"></i></div>
                                                                                <div class="wpie_log_action_btns wpie_process_pause_btn"><i class="fas fa-pause wpie_general_btn_icon wpie_data_tipso" data-tipso="<?php esc_attr_e( 'Pause', 'wp-import-export-lite' ); ?>"  aria-hidden="true"></i></div>
                                                                                <div class="wpie_log_action_btns wpie_process_stop_btn"><i class="fas fa-stop-circle wpie_general_btn_icon wpie_data_tipso" data-tipso="<?php esc_attr_e( 'Stop', 'wp-import-export-lite' ); ?>" aria-hidden="true"></i></div>
                                                                                <div class="wpie_log_action_btns wpie_process_resume_btn"><i class="fas fa-play wpie_general_btn_icon wpie_data_tipso" data-tipso="<?php esc_attr_e( 'Resume', 'wp-import-export-lite' ); ?>" aria-hidden="true"></i></div>
                                                                        </td>
                                                                </tr>
                                                                <?php
                                                                unset( $date, $id, $opration_type, $last_update_date, $process_log, $options, $fileName, $status, $process_status );
                                                        }

                                                        ?>
                                                <?php } ?>
                                                <tr class="<?php echo esc_attr( $is_empty_template ); ?> wpie_log_empty">
                                                        <td colspan="6">
                                                                <div class="wpie_empty_records"><?php esc_html_e( 'No Templates Found', 'wp-import-export-lite' ); ?></div>
                                                        </td>
                                                </tr>
                                                <?php unset( $is_empty_template ); ?>
                                        </tbody>
                                </table>
                        </div>
                </div>
                <?php
                if ( !empty( $ext_tab_files ) ) {

                        foreach ( $ext_tab_files as $_file ) {

                                if ( file_exists( $_file ) ) {
                                        include $_file;
                                }
                        }
                }

                ?>
        </div>
</div>
<div class="wpie_doc_wrapper">
        <div class="wpie_doc_container">
                <a class="wpie_doc_url" href="<?php echo esc_url( WPIE_SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e( 'Support', 'wp-import-export-lite' ); ?></a>
                <div class="wpie_doc_url_delim">|</div>
                <a class="wpie_doc_url" href="<?php echo esc_url( WPIE_DOC_URL ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'wp-import-export-lite' ); ?></a>
        </div>
</div>
<div class="modal fade wpie_error_model" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
                <div class="modal-content wpie_error">
                        <div class="modal-header">
                                <h5 class="modal-title"><?php esc_html_e( 'ERROR', 'wp-import-export-lite' ); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_error_content"></div>
                        </div>
                        <div class="modal-footer">
                                <div class="wpie_btn wpie_btn_red wpie_btn_radius " data-bs-dismiss="modal">
                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Ok', 'wp-import-export-lite' ); ?>
                                </div>
                        </div>
                </div>
        </div>
</div>
<div class="wpie_loader wpie_hidden">
        <div></div>
        <div></div>
</div>
<div class="modal fade wpie_delete_templates_data" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title wpie_import_proccess_title" ><?php esc_html_e( 'Confirm', 'wp-import-export-lite' ); ?></h5>
                        </div>
                        <div class="modal-body">
                                <div class="wpie_delete_text_msg"><?php esc_html_e( 'Are you sure want to delete?', 'wp-import-export-lite' ); ?></div>
                        </div>
                        <div class="modal-footer">
                                <div class="wpie_btn wpie_btn_primary wpie_btn_radius " data-bs-dismiss="modal">
                                        <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'cancel', 'wp-import-export-lite' ); ?>
                                </div>
                                <div class="wpie_btn  wpie_btn_primary wpie_btn_radius wpie_delete_templates" data-bs-dismiss="modal" >
                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Ok', 'wp-import-export-lite' ); ?>
                                </div>
                        </div>
                </div>
        </div>
</div>
<form class="wpie_download_file_frm" method="post">
        <input type="hidden" class="wpieSecurity" name="wpieSecurity" value="<?php echo wp_create_nonce( "wpie-security" ); ?>">
        <input type="hidden" class="wpie_download_file" name="wpie_download_export_id" value="" />
</form>
<?php
unset( $templates );
