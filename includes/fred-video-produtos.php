<?php

/**
 * Fred Video Produto - Funcionalidade para Produtos
 * Adiciona campos de vídeo aos produtos WooCommerce
 */

// === Adiciona campos de upload de vídeo no produto ===
add_action('woocommerce_product_options_general_product_data', 'fvp_add_video_upload_fields_to_products');
function fvp_add_video_upload_fields_to_products()
{
    global $post;

    echo '<div class="options_group">';
    ?>
    <p class="form-field">
        <label for="_fred_video_thumbnail">Miniatura do vídeo (MP4)</label>
        <input type="text" class="short" name="_fred_video_thumbnail" id="_fred_video_thumbnail" value="<?php echo esc_attr(get_post_meta($post->ID, '_fred_video_thumbnail', true)); ?>" placeholder="Selecione ou envie um vídeo MP4" />
        <button type="button" class="button upload_video_button" data-target="_fred_video_thumbnail">Enviar/Selecionar</button>
    </p>

    <p class="form-field">
        <label for="_fred_product_video">Vídeo principal (MP4)</label>
        <input type="text" class="short" name="_fred_product_video" id="_fred_product_video" value="<?php echo esc_attr(get_post_meta($post->ID, '_fred_product_video', true)); ?>" placeholder="Selecione ou envie um vídeo MP4" />
        <button type="button" class="button upload_video_button" data-target="_fred_product_video">Enviar/Selecionar</button>
    </p>

    <script>
        jQuery(document).ready(function($) {
            function open_media_uploader(targetInput) {
                let frame = wp.media({
                    title: 'Selecionar vídeo (somente MP4)',
                    button: {
                        text: 'Usar este vídeo'
                    },
                    library: {
                        type: 'video'
                    },
                    multiple: false
                });
                frame.on('select', function() {
                    let attachment = frame.state().get('selection').first().toJSON();
                    if (attachment.url.endsWith('.mp4')) {
                        $('#' + targetInput).val(attachment.url);
                    } else {
                        alert('Por favor, envie um arquivo MP4.');
                    }
                });
                frame.open();
            }

            $('.upload_video_button').on('click', function(e) {
                e.preventDefault();
                let targetInput = $(this).data('target');
                open_media_uploader(targetInput);
            });
        });
    </script>
    <?php
    echo '</div>';
}

// === Salvar metadados do produto ===
add_action('woocommerce_admin_process_product_object', 'fvp_save_video_upload_fields_product');
function fvp_save_video_upload_fields_product($product)
{
    if (isset($_POST['_fred_video_thumbnail'])) {
        $product->update_meta_data('_fred_video_thumbnail', esc_url_raw($_POST['_fred_video_thumbnail']));
    }
    if (isset($_POST['_fred_product_video'])) {
        $product->update_meta_data('_fred_product_video', esc_url_raw($_POST['_fred_product_video']));
    }
}

// === FRONT: Bolinha de vídeo + lightbox para Produtos ===
add_action('wp_footer', 'fvp_display_product_video_bubble');
function fvp_display_product_video_bubble()
{
    if (!is_product()) return;

    global $product;
    if (!$product || !is_a($product, 'WC_Product')) return;

    // Lê as chaves personalizadas
    $thumb_url = get_post_meta($product->get_id(), '_fred_video_thumbnail', true);
    $video_url = get_post_meta($product->get_id(), '_fred_product_video', true);

    // Se estiverem vazias, não mostra nada
    if (empty($thumb_url) || empty($video_url)) return;

    fvp_render_video_bubble('product', $thumb_url, $video_url);
}
