<?php

/**
 * Fred Video Produto - Funcionalidade para Páginas
 * Adiciona campos de vídeo às páginas estáticas
 */

// === Adiciona meta box para vídeos nas páginas ===
add_action('add_meta_boxes', 'fvp_add_page_video_meta_box');
function fvp_add_page_video_meta_box()
{
    add_meta_box(
        'fvp_page_video_meta_box',
        'Vídeos da Página',
        'fvp_page_video_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}

// === Callback para exibir o meta box ===
function fvp_page_video_meta_box_callback($post)
{
    ?>
    <p class="form-field">
        <label for="_fred_page_video_thumbnail">Miniatura do vídeo (MP4)</label>
        <input type="text" class="short" name="_fred_page_video_thumbnail" id="_fred_page_video_thumbnail" value="<?php echo esc_attr(get_post_meta($post->ID, '_fred_page_video_thumbnail', true)); ?>" placeholder="Selecione ou envie um vídeo MP4" style="width: 100%; margin-bottom: 10px;" />
        <button type="button" class="button upload_video_button_page" data-target="_fred_page_video_thumbnail">Enviar/Selecionar</button>
    </p>

    <p class="form-field">
        <label for="_fred_page_video">Vídeo principal (MP4)</label>
        <input type="text" class="short" name="_fred_page_video" id="_fred_page_video" value="<?php echo esc_attr(get_post_meta($post->ID, '_fred_page_video', true)); ?>" placeholder="Selecione ou envie um vídeo MP4" style="width: 100%; margin-bottom: 10px;" />
        <button type="button" class="button upload_video_button_page" data-target="_fred_page_video">Enviar/Selecionar</button>
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

            $('.upload_video_button_page').on('click', function(e) {
                e.preventDefault();
                let targetInput = $(this).data('target');
                open_media_uploader(targetInput);
            });
        });
    </script>
    <?php
}

// === Salvar metadados da página ===
add_action('save_post_page', 'fvp_save_video_upload_fields_page');
function fvp_save_video_upload_fields_page($post_id)
{
    // Verificar nonce e permissões
    if (!isset($_POST) || empty($_POST)) {
        return;
    }

    // Salvar thumbnail do vídeo
    if (isset($_POST['_fred_page_video_thumbnail'])) {
        update_post_meta($post_id, '_fred_page_video_thumbnail', esc_url_raw($_POST['_fred_page_video_thumbnail']));
    }

    // Salvar vídeo principal
    if (isset($_POST['_fred_page_video'])) {
        update_post_meta($post_id, '_fred_page_video', esc_url_raw($_POST['_fred_page_video']));
    }
}

// === FRONT: Bolinha de vídeo + lightbox para Páginas ===
add_action('wp_footer', 'fvp_display_page_video_bubble');
function fvp_display_page_video_bubble()
{
    if (!is_page()) return;

    global $post;
    if (!$post) return;

    // Lê as chaves personalizadas
    $thumb_url = get_post_meta($post->ID, '_fred_page_video_thumbnail', true);
    $video_url = get_post_meta($post->ID, '_fred_page_video', true);

    // Se estiverem vazias, não mostra nada
    if (empty($thumb_url) || empty($video_url)) return;

    fvp_render_video_bubble('page', $thumb_url, $video_url);
}
