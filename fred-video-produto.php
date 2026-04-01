<?php

/**
 * Plugin Name: Fred Video Produtos e Páginas
 * Description: Plugin para adicionar vídeos aos produtos WooCommerce e páginas.
 * Author: Frederico de Castro
 * Author URI: https://www.fredericodecastro.com.br
 * Version: 1.3.0
 */

// Carrega as funcionalidades comuns (CSS e renderização)
require_once plugin_dir_path(__FILE__) . 'includes/fred-video-common.php';

// Carrega o módulo de vídeos para produtos
require_once plugin_dir_path(__FILE__) . 'includes/fred-video-produtos.php';

// Carrega o módulo de vídeos para páginas
require_once plugin_dir_path(__FILE__) . 'includes/fred-video-pages.php';
