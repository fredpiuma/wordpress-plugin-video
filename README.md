# Fred Video Produto

Um plugin WordPress modular para adicionar vídeos em produtos WooCommerce e páginas estáticas.

## 🎥 Funcionalidades

- **Vídeos em Produtos**: Adicione dois vídeos a cada produto WooCommerce (thumbnail e vídeo principal)
- **Vídeos em Páginas**: Adicione vídeos também em páginas estáticas do WordPress
- **Bolha Flutuante**: Exibe um vídeo em miniatura em uma bolha fixa na tela
- **Lightbox Interativo**: Clique na bolha para abrir o vídeo em tela cheia com overlay escuro
- **Responsivo**: Design adaptado para desktop e mobile
- **Suporte a MP4**: Validação de formato de arquivo
- **Modular**: Arquitetura extensível para adicionar suporte a novos tipos de conteúdo

## 📋 Requisitos

- WordPress 5.0+
- PHP 7.2+
- WooCommerce 3.0+ (apenas para funcionalidade de produtos)

## 🚀 Instalação

1. Clone o repositório para a pasta de plugins:
```bash
git clone https://github.com/fredpiuma/wordpress-plugin-video.git wp-content/plugins/fred-video-produto
```

2. Ative o plugin no painel administrativo do WordPress em **Plugins**

3. Pronto! Os campos de vídeo aparecerão automaticamente nos produtos e páginas

## 📖 Como Usar

### Para Produtos WooCommerce

1. Acesse **Produtos** > **Todos os Produtos** e edite um produto
2. Na seção **Dados Gerais do Produto**, procure por:
   - **Miniatura do vídeo (MP4)**: Vídeo exibido na bolha flutuante
   - **Vídeo principal (MP4)**: Vídeo exibido no lightbox ao clicar
3. Clique em **Enviar/Selecionar** para usar o uploader de mídia
4. Selecione um arquivo MP4
5. Publique o produto

Quando o cliente visita a página do produto, verá uma bolha de vídeo no canto superior esquerdo.

### Para Páginas

1. Acesse **Páginas** > **Todas as Páginas** e edite uma página
2. Procure pela meta box **Vídeos da Página**
3. Preenchha os campos:
   - **Miniatura do vídeo (MP4)**
   - **Vídeo principal (MP4)**
4. Publique a página

O comportamento é idêntico ao dos produtos.

## 🎨 Interface do Usuário

### Bolha de Vídeo
- **Desktop**: Círculo de 90px no topo esquerdo (20% do topo)
- **Mobile**: Círculo responsivo no canto inferior esquerdo
- **Hover**: Aumenta 8% com efeito smooth

### Botões
- **X na bolha**: Fecha e esconde a bolha
- **X no overlay**: Fecha o lightbox
- **Clique fora do vídeo**: Fecha o lightbox
- **Tecla ESC**: Fecha o lightbox

### Vídeos
- **Autoplay**: Ativado com som muted (compatível com políticas de navegador)
- **Loop**: Ativado
- **Controles**: HTML5 padrão do navegador

## 📁 Estrutura do Projeto

```
fred-video-produto/
├── fred-video-produto.php              # Plugin principal (loader)
├── includes/
│   ├── fred-video-common.php           # Código compartilhado (CSS, JS, rendering)
│   ├── fred-video-produtos.php         # Funcionalidade WooCommerce
│   └── fred-video-pages.php            # Funcionalidade de páginas
├── CLAUDE.md                           # Documentação técnica
└── README.md                           # Este arquivo
```

## 🔧 Desenvolvimento

### Adicionar Suporte para Novo Tipo de Conteúdo

Para adicionar suporte a um novo tipo de post (exemplo: CPT customizado):

1. Crie `includes/fred-video-[posttype].php`
2. Registre os campos admin (meta box ou outro método)
3. Implemente salvamento dos metadados
4. Crie função de display que:
   - Verifique `is_singular('[posttype]')`
   - Recupere os vídeos com prefixo `_fred_[posttype]_`
   - Chame `fvp_render_video_bubble('[posttype]', $thumb, $video)`
5. Carregue o arquivo no `fred-video-produto.php`

Exemplo:
```php
// includes/fred-video-posts.php
function fvp_display_post_video_bubble() {
    if (!is_singular('post')) return;
    $thumb = get_post_meta(get_the_ID(), '_fred_post_video_thumbnail', true);
    $video = get_post_meta(get_the_ID(), '_fred_post_video', true);
    if (!empty($thumb) && !empty($video)) {
        fvp_render_video_bubble('post', $thumb, $video);
    }
}
add_action('wp_footer', 'fvp_display_post_video_bubble');
```

### Testar Localmente

```bash
# Verificar sintaxe PHP
php -l fred-video-produto.php
php -l includes/*.php

# Ativar debug no wp-config.php se precisar
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 🐛 Troubleshooting

**P: Os vídeos não aparecem nas páginas de produto**
- Confirme que os dois campos (thumbnail e principal) estão preenchidos
- Verifique que os arquivos são MP4 válidos
- Limpe o cache do navegador

**P: O autoplay não funciona em alguns navegadores**
- É uma limitação de segurança do navegador (exige vídeo muted)
- O plugin já vem com `muted` ativado automaticamente

**P: Múltiplos vídeos na mesma página causam conflito**
- O plugin gera IDs únicos para cada vídeo
- Não deve haver conflito, mas limpe cache se houver

## 📝 Notas de Versão

### v2.0 (Atual)
- ✨ Adicionado suporte a vídeos em páginas
- 🔧 Refatorado em arquitetura modular
- 📦 Separado funcionalidade de produtos e páginas
- 🎯 IDs DOM únicos para evitar conflitos

### v1.2
- 🎬 Versão inicial com suporte a produtos WooCommerce

## 👨‍💻 Autor

**Frederico de Castro**
- Website: https://www.fredericodecastro.com.br

## 📄 Licença

Este plugin é fornecido como está. Sinta-se livre para usar, modificar e distribuir.

## 🤝 Contribuições

Contribuições são bem-vindas! Se encontrar bugs ou tiver sugestões, sinta-se livre para:
1. Abrir uma issue
2. Fazer um fork e enviar um pull request
3. Entrar em contato com o autor

## 📚 Recursos Adicionais

- [Documentação Técnica (CLAUDE.md)](./CLAUDE.md)
- [WordPress Plugin Development](https://developer.wordpress.org/plugins/)
- [WooCommerce Product Hooks](https://woocommerce.com/document/hooks/)
- [HTML5 Video Element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video)
