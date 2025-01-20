<?php
/*
Plugin Name: Whats Post
Description: Adiciona Botão do WhatsApp aos posts e páginas selecionados.
Version: 1.0
Author: Carlos Rogerio Orioli
*/

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'whatsapp_share_plugin_menu');
add_action('admin_menu', 'whatsapp_share_plugin_menu');

function whatsapp_share_plugin_menu() {
    add_menu_page(
        'Configurar Botões WhatsApp',
        'WhatsApp Button',
        'manage_options',
        'whatsapp-share-plugin',
        'whatsapp_share_plugin_page',
        'dashicons-whatsapp',
        10
    );
}

function whatsapp_share_plugin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['selected_posts']) || isset($_POST['default_phone'])) {
        $selected_posts = array_map('intval', $_POST['selected_posts'] ?? []);
        $selected_pages = array_map('intval', $_POST['selected_pages'] ?? []);
        $default_phone = sanitize_text_field($_POST['default_phone'] ?? '');
        update_option('whatsapp_share_selected_posts', $selected_posts);
        update_option('whatsapp_share_selected_pages', $selected_pages);
        update_option('whatsapp_default_phone', $default_phone);
        echo '<div class="updated"><p>Configurações salvas!</p></div>';
    }

    $selected_posts = get_option('whatsapp_share_selected_posts', []);
    $selected_pages = get_option('whatsapp_share_selected_pages', []);
    $default_phone = get_option('whatsapp_default_phone', '');
    
    $posts_per_page = 10;
    $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = ($paged - 1) * $posts_per_page;

    $posts = get_posts([
        'numberposts' => $posts_per_page,
        'offset' => $offset,
        'post_type' => 'post',
    ]);

    $pages = get_posts([
        'numberposts' => $posts_per_page,
        'offset' => $offset,
        'post_type' => 'page',
    ]);
    
    $total_posts = wp_count_posts()->publish;
    $total_pages = ceil($total_posts / $posts_per_page);

    echo '<div class="wrap">';
    echo '<h1>Selecionar Posts e Páginas para WhatsApp</h1>';
    echo '<form method="POST">';
    echo '<h2>Configurar Número Padrão</h2>';
    echo "<p><input type='text' name='default_phone' value='{$default_phone}' placeholder='Número de telefone (somente dígitos)' style='width: 300px;'> Ex: 5511999999999</p>";
    echo '<h2>Selecionar Posts</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th style="width: 5%;">Selecionar</th><th>Título do Post</th><th>Data</th></tr></thead>';
    echo '<tbody>';

    foreach ($posts as $post) {
        $checked = in_array($post->ID, $selected_posts) ? 'checked' : '';
        echo "<tr>";
        echo "<td><input type='checkbox' name='selected_posts[]' value='{$post->ID}' $checked></td>";
        echo "<td>{$post->post_title}</td>";
        echo "<td>" . get_the_date('d/m/Y', $post->ID) . "</td>";
        echo "</tr>";
    }

    echo '</tbody>';
    echo '</table>';

    echo '<h2>Selecionar Páginas</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th style="width: 5%;">Selecionar</th><th>Título da Página</th><th>Data</th></tr></thead>';
    echo '<tbody>';

    foreach ($pages as $page) {
        $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
        echo "<tr>";
        echo "<td><input type='checkbox' name='selected_pages[]' value='{$page->ID}' $checked></td>";
        echo "<td>{$page->post_title}</td>";
        echo "<td>" . get_the_date('d/m/Y', $page->ID) . "</td>";
        echo "</tr>";
    }

    echo '</tbody>';
    echo '</table>';

    echo '<button type="submit" class="button button-primary" style="margin-top: 15px;">Salvar</button>';
    echo '</form>';

    echo '<div class="tablenav"><div class="tablenav-pages">';
    echo '<span class="displaying-num">' . $total_posts . ' posts</span>';
    echo '<span class="pagination-links">';
    
    if ($paged > 1) {
        echo '<a href="?page=whatsapp-share-plugin&paged=' . ($paged - 1) . '" class="prev-page">« Anterior</a>';
    }
    
    if ($paged < $total_pages) {
        echo '<a href="?page=whatsapp-share-plugin&paged=' . ($paged + 1) . '" class="next-page">Próxima »</a>';
    }
    
    echo '</span>';
    echo '</div></div>';
    
    echo '</div>';
}

add_filter('the_content', 'add_whatsapp_button_to_selected_posts_and_pages');

function add_whatsapp_button_to_selected_posts_and_pages($content) {
    if (is_singular('post') || is_singular('page')) {
        $selected_posts = get_option('whatsapp_share_selected_posts', []);
        $selected_pages = get_option('whatsapp_share_selected_pages', []);
        $default_phone = get_option('whatsapp_default_phone', '');
        
        if ((in_array(get_the_ID(), $selected_posts) || in_array(get_the_ID(), $selected_pages)) && !empty($default_phone)) {
            $post_url = urlencode(get_permalink());
            $post_title = urlencode(get_the_title());
            $whatsapp_link = "https://wa.me/{$default_phone}?text=Gostaria%20de%20mais%20info%20sobre%20-{$post_title}%20{$post_url}";
            $whatsapp_button = "<a href='{$whatsapp_link}' target='_blank' class='whatsapp-button'>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z'/></svg>
            </a>";

            $content .= $whatsapp_button;
        }
    }
    return $content;
}

function load_css() {
    wp_enqueue_style('meu-plugin-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'load_css');
