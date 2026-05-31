<?php
if (!defined('ABSPATH'))
    exit;

// ── Shared video helpers ────────────────────────────────────────────────────────

if (!function_exists('nexus_video_embed_url')) {
    function nexus_video_embed_url($url)
    {
        if (empty($url))
            return '';
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m))
            return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0';
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m))
            return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1';
        if (preg_match('/dailymotion\.com\/video\/([a-z0-9]+)/i', $url, $m))
            return 'https://www.dailymotion.com/embed/video/' . $m[1] . '?autoplay=1';
        if (preg_match('/videopress\.com\/v\/([a-zA-Z0-9]+)/', $url, $m))
            return 'https://videopress.com/embed/' . $m[1] . '?autoplay=1';
        return $url;
    }
}

if (!function_exists('nexus_video_thumbnail_url')) {
    function nexus_video_thumbnail_url($url, $custom = '')
    {
        if (!empty($custom))
            return $custom;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m))
            return 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg';
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m))
            return 'https://vumbnail.com/' . $m[1] . '.jpg';
        if (preg_match('/dailymotion\.com\/video\/([a-z0-9]+)/i', $url, $m))
            return 'https://www.dailymotion.com/thumbnail/video/' . $m[1];
        return \Elementor\Utils::get_placeholder_image_src();
    }
}

// ── Widget ─────────────────────────────────────────────────────────────────────

class Nexus_Gallery_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'nexus_gallery';
    }

    public function get_title()
    {
        return esc_html__('Nexus Gallery', 'nexus-elements');
    }

    public function get_icon()
    {
        return 'eicon-gallery-masonry';
    }

    public function get_categories()
    {
        return ['nexus-category'];
    }

    public function get_keywords()
    {
        return ['nexus', 'gallery', 'photo', 'video', 'mixed', 'grid', 'masonry', 'metro', 'lightbox', 'filter', 'featured'];
    }

    protected function register_controls()
    {
        $addon_active = defined('NEXUS_GALLERY_MANAGER');

        // ── CONTENT: Gallery Mode ───────────────────────────────────────────
        $this->start_controls_section('mode_section', [
            'label' => esc_html__('Gallery Mode', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('gallery_mode', [
            'label' => esc_html__('Gallery Type', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'mixed',
            'options' => [
                'mixed' => esc_html__('Mixed — Photos & Videos', 'nexus-elements'),
                'photos' => esc_html__('Photos Only', 'nexus-elements'),
                'videos' => esc_html__('Videos Only', 'nexus-elements'),
            ],
        ]);
        $this->end_controls_section();

        // ── CONTENT: Gallery Items (Repeater — shown when addon NOT active) ─
        if (!$addon_active) {
            $this->start_controls_section('items_section', [
                'label' => esc_html__('Gallery Items', 'nexus-elements'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]);

            $rep = new \Elementor\Repeater();

            $rep->add_control('item_type', [
                'label' => esc_html__('Item Type', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'photo',
                'options' => [
                    'photo' => esc_html__('Photo', 'nexus-elements'),
                    'video' => esc_html__('Video', 'nexus-elements'),
                ],
            ]);
            $rep->add_control('item_image', [
                'label' => esc_html__('Image / Video Thumbnail', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                'description' => esc_html__('For photos: the full display image. For videos: custom thumbnail (auto-detected for YouTube if empty).', 'nexus-elements'),
            ]);
            $rep->add_control('item_category', [
                'label' => esc_html__('Category', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('General', 'nexus-elements'),
                'label_block' => true,
                'description' => esc_html__('Used for the filter bar. Case-sensitive.', 'nexus-elements'),
            ]);
            $rep->add_control('item_title', [
                'label' => esc_html__('Title', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]);
            $rep->add_control('item_description', [
                'label' => esc_html__('Description (in lightbox)', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
            ]);

            // Video-only fields
            $rep->add_control('video_heading', [
                'label' => esc_html__('Video Settings', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => ['item_type' => 'video'],
            ]);
            $rep->add_control('item_video_provider', [
                'label' => esc_html__('Provider', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => 'YouTube',
                    'vimeo' => 'Vimeo',
                    'dailymotion' => 'Dailymotion',
                    'videopress' => 'VideoPress',
                    'self_hosted' => esc_html__('Self-Hosted', 'nexus-elements'),
                ],
                'condition' => ['item_type' => 'video'],
            ]);
            $rep->add_control('item_video_url', [
                'label' => esc_html__('Video URL', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::URL,
                'label_block' => true,
                'options' => false,
                'placeholder' => 'https://www.youtube.com/watch?v=...',
                'default' => ['url' => 'https://www.youtube.com/watch?v=XHOmBV4js_E'],
                'condition' => ['item_type' => 'video'],
            ]);
            $rep->add_control('item_duration', [
                'label' => esc_html__('Duration Badge', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => '3:42',
                'condition' => ['item_type' => 'video'],
            ]);

            // Photo-only fields
            $rep->add_control('item_alt', [
                'label' => esc_html__('Alt Text', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__('For accessibility and SEO.', 'nexus-elements'),
                'condition' => ['item_type' => 'photo'],
            ]);

            $this->add_control('gallery_items', [
                'label' => esc_html__('Items', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $rep->get_controls(),
                'title_field' => '<# var icon = item_type === "video" ? "▶ " : "🖼 "; #>{{{ icon + ( item_title || item_category || "Item" ) }}}',
            ]);

            $this->add_control('addon_notice', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div style="background:#1e2d40;border-left:3px solid #f59e0b;padding:10px 14px;border-radius:0 6px 6px 0;font-size:12px;line-height:1.8;margin-top:6px;color:#e2e8f0;">'
                    . '<strong style="color:#fbbf24;display:block;margin-bottom:3px;">💡 ' . esc_html__('Managing 100+ items?', 'nexus-elements') . '</strong>'
                    . '<span style="color:#cbd5e1;">' . esc_html__('Install the free Nexus Gallery Manager addon to manage your gallery from the WordPress dashboard with search, pagination, and bulk editing.', 'nexus-elements') . '</span>'
                    . '</div>',
            ]);

            $this->end_controls_section();
        } else {
            // ── CONTENT: Query (shown when addon IS active) ─────────────────
            $this->start_controls_section('query_section', [
                'label' => esc_html__('Query', 'nexus-elements'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]);

            // Build category options dynamically from taxonomy
            $cat_options = ['' => esc_html__('— All Categories —', 'nexus-elements')];
            $terms = get_terms(['taxonomy' => 'nexus_gallery_cat', 'hide_empty' => false]);
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $cat_options[$term->slug] = $term->name;
                }
            }

            $this->add_control('query_categories', [
                'label' => esc_html__('Filter by Category', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $cat_options,
                'multiple' => true,
                'label_block' => true,
                'description' => esc_html__('Leave empty to show all categories.', 'nexus-elements'),
            ]);

            $this->add_control('query_per_page', [
                'label' => esc_html__('Items to Show', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '',
                'min' => 0,
                'max' => 500,
                'description' => esc_html__('Leave empty to show all items.', 'nexus-elements'),
            ]);

            $this->add_control('show_more_enabled', [
                'label' => esc_html__('Show More Button', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'return_value' => 'yes',
                'description' => esc_html__('Show a "Show More" button to progressively reveal items.', 'nexus-elements'),
                'condition' => ['query_per_page!' => ''],
            ]);

            $this->add_control('show_more_text', [
                'label' => esc_html__('Button Text', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Show More', 'nexus-elements'),
                'condition' => [
                    'query_per_page!' => '',
                    'show_more_enabled' => 'yes',
                ],
            ]);

            $this->add_control('show_more_increment', [
                'label' => esc_html__('Items Per Click', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
                'description' => esc_html__('Number of additional items to reveal each time.', 'nexus-elements'),
                'condition' => [
                    'query_per_page!' => '',
                    'show_more_enabled' => 'yes',
                ],
            ]);

            $this->add_control('query_orderby', [
                'label' => esc_html__('Order By', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => esc_html__('Date', 'nexus-elements'),
                    'title' => esc_html__('Title', 'nexus-elements'),
                    'menu_order' => esc_html__('Menu Order', 'nexus-elements'),
                    'rand' => esc_html__('Random', 'nexus-elements'),
                ],
            ]);

            $this->add_control('query_order', [
                'label' => esc_html__('Order', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => esc_html__('Newest First', 'nexus-elements'),
                    'ASC' => esc_html__('Oldest First', 'nexus-elements'),
                ],
            ]);

            $this->add_control('query_exclude', [
                'label' => esc_html__('Exclude Item IDs', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => '15, 22, 109',
                'label_block' => true,
                'description' => esc_html__('Comma-separated post IDs to exclude.', 'nexus-elements'),
            ]);

            $this->end_controls_section();
        }

        // ── CONTENT: Layout ─────────────────────────────────────────────────
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('gallery_show_filter', [
            'label' => esc_html__('Show Category Filter', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'return_value' => 'yes',
        ]);
        $this->add_responsive_control('filter_align', [
            'label' => esc_html__('Filter Alignment', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => ['title' => 'Left', 'icon' => 'eicon-text-align-left'],
                'center' => ['title' => 'Center', 'icon' => 'eicon-text-align-center'],
                'flex-end' => ['title' => 'Right', 'icon' => 'eicon-text-align-right'],
            ],
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter' => 'justify-content: {{VALUE}};'],
            'default' => 'flex-start',
            'condition' => ['gallery_show_filter' => 'yes'],
        ]);
        $this->add_control('gallery_layout', [
            'label' => esc_html__('Layout Style', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'grid',
            'options' => [
                'grid' => esc_html__('Grid', 'nexus-elements'),
                'masonry' => esc_html__('Masonry (CSS Columns)', 'nexus-elements'),
                'justified' => esc_html__('Justified (Flex Rows)', 'nexus-elements'),
                'metro' => esc_html__('Metro (Hero + Tiles)', 'nexus-elements'),
            ],
        ]);
        $this->add_responsive_control('gallery_columns', [
            'label' => esc_html__('Columns', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 1, 'max' => 6, 'step' => 1]],
            'default' => ['size' => 3],
            'tablet_default' => ['size' => 2],
            'mobile_default' => ['size' => 1],
            'selectors' => ['{{WRAPPER}}' => '--ng-cols: {{SIZE}};'],
            'condition' => ['gallery_layout!' => 'metro'],
        ]);
        $this->add_responsive_control('gallery_gap', [
            'label' => esc_html__('Gap Between Items', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 80]],
            'default' => ['unit' => 'px', 'size' => 16],
            'selectors' => ['{{WRAPPER}}' => '--ng-gap: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('gallery_image_height', [
            'label' => esc_html__('Item Height', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => ['px' => ['min' => 80, 'max' => 800]],
            'selectors' => ['{{WRAPPER}}' => '--ng-height: {{SIZE}}{{UNIT}};'],
            'condition' => ['gallery_layout!' => 'masonry'],
            'description' => esc_html__('Leave empty to auto-size into perfect squares.', 'nexus-elements'),
        ]);
        $this->add_control('gallery_image_fit', [
            'label' => esc_html__('Image Fit', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'cover',
            'options' => [
                'cover' => esc_html__('Cover', 'nexus-elements'),
                'contain' => esc_html__('Contain', 'nexus-elements'),
            ],
            'selectors' => ['{{WRAPPER}}' => '--ng-fit: {{VALUE}};'],
        ]);
        $this->add_control('gallery_show_captions', [
            'label' => esc_html__('Show Captions', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'return_value' => 'yes',
        ]);
        $this->end_controls_section();

        // ── CONTENT: Lightbox Customization ─────────────────────────────────
        $this->start_controls_section('lightbox_section', [
            'label' => esc_html__('Lightbox Customization', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('lightbox_show_title', [
            'label' => esc_html__('Show Title in Lightbox', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'return_value' => 'yes',
        ]);
        $this->add_control('lightbox_show_desc', [
            'label' => esc_html__('Show Description in Lightbox', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
            'return_value' => 'yes',
        ]);
        $this->end_controls_section();

        // ── STYLE: Items ────────────────────────────────────────────────────
        $this->start_controls_section('style_item', [
            'label' => esc_html__('Gallery Items', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('gallery_border_radius', [
            'label' => esc_html__('Border Radius', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'default' => ['top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true],
            'selectors' => ['{{WRAPPER}} .nexus-gallery-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;'],
        ]);
        $this->end_controls_section();

        // ── STYLE: Overlay ──────────────────────────────────────────────────
        $this->start_controls_section('style_overlay', [
            'label' => esc_html__('Hover Overlay', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('gallery_overlay_color', [
            'label' => esc_html__('Overlay Color', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.55)',
            'selectors' => ['{{WRAPPER}}' => '--ng-overlay: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        // ── STYLE: Play Button ──────────────────────────────────────────────
        $this->start_controls_section('style_play', [
            'label' => esc_html__('Play Button (Video Items)', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control('gallery_play_size', [
            'label' => esc_html__('Button Size', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 24, 'max' => 120]],
            'default' => ['unit' => 'px', 'size' => 52],
            'selectors' => ['{{WRAPPER}}' => '--ng-play-size: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_control('gallery_play_color', [
            'label' => esc_html__('Icon Color', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-play' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('gallery_play_bg', [
            'label' => esc_html__('Button Background', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => 'rgba(255,255,255,0.2)',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-play' => 'background: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        // ── STYLE: Duration Badge ───────────────────────────────────────────
        $this->start_controls_section('style_duration', [
            'label' => esc_html__('Duration Badge', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_control('gallery_badge_bg', [
            'label' => esc_html__('Background', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.75)',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-duration' => 'background: {{VALUE}};'],
        ]);
        $this->add_control('gallery_badge_color', [
            'label' => esc_html__('Text Color', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-duration' => 'color: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        // ── STYLE: Caption ──────────────────────────────────────────────────
        $this->start_controls_section('style_caption', [
            'label' => esc_html__('Caption', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'gallery_caption_typo',
            'selector' => '{{WRAPPER}} .nexus-gallery-caption-title',
        ]);
        $this->add_control('gallery_caption_color', [
            'label' => esc_html__('Title Color', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-caption-title' => 'color: {{VALUE}};'],
        ]);
        $this->end_controls_section();

        // ── STYLE: Filter Bar ───────────────────────────────────────────────
        $this->start_controls_section('style_filter', [
            'label' => esc_html__('Filter Bar', 'nexus-elements'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'gallery_filter_typo',
            'selector' => '{{WRAPPER}} .nexus-gallery-filter-btn',
        ]);
        $this->add_control('gallery_filter_bg', [
            'label' => esc_html__('Button Background', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#f3f4f6',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter-btn' => 'background: {{VALUE}};'],
        ]);
        $this->add_control('gallery_filter_color', [
            'label' => esc_html__('Button Text Color', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#374151',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter-btn' => 'color: {{VALUE}};'],
        ]);
        $this->add_control('gallery_filter_active_bg', [
            'label' => esc_html__('Active Background', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#d63384',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter-btn.active' => 'background: {{VALUE}};'],
        ]);
        $this->add_control('gallery_filter_active_color', [
            'label' => esc_html__('Active Text Color', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter-btn.active' => 'color: {{VALUE}};'],
        ]);
        $this->add_responsive_control('gallery_filter_spacing', [
            'label' => esc_html__('Buttons Gap', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 40]],
            'default' => ['unit' => 'px', 'size' => 8],
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter' => 'gap: {{SIZE}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('gallery_filter_radius', [
            'label' => esc_html__('Border Radius', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'default' => ['top' => 9999, 'right' => 9999, 'bottom' => 9999, 'left' => 9999, 'unit' => 'px', 'isLinked' => true],
            'selectors' => ['{{WRAPPER}}' => '--ng-filter-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
        ]);
        $this->add_responsive_control('gallery_filter_mb', [
            'label' => esc_html__('Filter Bar Spacing Below', 'nexus-elements'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => ['px' => ['min' => 0, 'max' => 80]],
            'default' => ['unit' => 'px', 'size' => 24],
            'selectors' => ['{{WRAPPER}} .nexus-gallery-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};'],
        ]);
        $this->end_controls_section();
    }

    // ── render() is in the section below ───────────────────────────────────────

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $addon_active = defined('NEXUS_GALLERY_MANAGER');
        $gallery_mode = $settings['gallery_mode'] ?? 'mixed';
        $uid = 'nexus-gallery-' . $this->get_id();
        $layout = $settings['gallery_layout'] ?? 'grid';
        $show_filter = 'yes' === ($settings['gallery_show_filter'] ?? 'yes');
        $show_captions = 'yes' === ($settings['gallery_show_captions'] ?? 'yes');

        $lb_show_title = 'yes' === ($settings['lightbox_show_title'] ?? 'yes');
        $lb_show_desc = 'yes' === ($settings['lightbox_show_desc'] ?? 'yes');

        // ── Normalise items from either data source into a flat array ────────
        $items_data = [];

        if ($addon_active) {
            // Build meta query for type filtering
            $meta_query = [];
            if ($gallery_mode === 'photos') {
                $meta_query[] = ['key' => '_nexus_item_type', 'value' => 'photo', 'compare' => '='];
            } elseif ($gallery_mode === 'videos') {
                $meta_query[] = ['key' => '_nexus_item_type', 'value' => 'video', 'compare' => '='];
            }

            // Build tax query for selected categories
            $tax_query = [];
            $cat_slugs = $settings['query_categories'] ?? [];
            if (!empty($cat_slugs)) {
                $tax_query[] = [
                    'taxonomy' => 'nexus_gallery_cat',
                    'field' => 'slug',
                    'terms' => (array) $cat_slugs,
                ];
            }

            // Excluded IDs
            $exclude_ids = [];
            $raw_exclude = trim($settings['query_exclude'] ?? '');
            if ($raw_exclude !== '') {
                $exclude_ids = array_filter(array_map('intval', explode(',', $raw_exclude)));
            }

            $raw_per_page = $settings['query_per_page'] ?? '';
            $per_page     = ($raw_per_page === '' || $raw_per_page === null) ? -1 : max(1, intval($raw_per_page));
            $show_more_on = ('yes' === ($settings['show_more_enabled'] ?? '')) && $per_page > 0;

            // When Show More is enabled we need ALL matching posts so JS can paginate
            $query_args = [
                'post_type'      => 'nexus_gallery_item',
                'posts_per_page' => $show_more_on ? -1 : $per_page,
                'orderby'        => sanitize_key($settings['query_orderby'] ?? 'date'),
                'order'          => in_array($settings['query_order'] ?? 'DESC', ['ASC', 'DESC'], true) ? $settings['query_order'] : 'DESC',
                'no_found_rows'  => true,
            ];
            if (!empty($meta_query))
                $query_args['meta_query'] = $meta_query;
            if (!empty($tax_query))
                $query_args['tax_query'] = $tax_query;
            if (!empty($exclude_ids))
                $query_args['post__not_in'] = $exclude_ids;

            $query = new WP_Query($query_args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $post_id = get_the_ID();
                    $type = get_post_meta($post_id, '_nexus_item_type', true) ?: 'photo';

                    // Use first assigned taxonomy term as category (skipping 'All')
                    $terms = get_the_terms($post_id, 'nexus_gallery_cat');
                    $cat_name = '';
                    if ($terms && !is_wp_error($terms)) {
                        foreach ($terms as $t) {
                            if (strtolower($t->name) !== 'all') {
                                $cat_name = $t->name;
                                break;
                            }
                        }
                    }

                    if ($type === 'video') {
                        $raw_url = get_post_meta($post_id, '_nexus_video_url', true) ?: '';
                        $thumb_url = get_the_post_thumbnail_url($post_id, 'large') ?: '';
                        $items_data[] = [
                            'type' => 'video',
                            'href' => $raw_url,
                            'thumb' => nexus_video_thumbnail_url($raw_url, $thumb_url),
                            'category' => $cat_name,
                            'title' => get_the_title(),
                            'desc' => '',
                            'duration' => get_post_meta($post_id, '_nexus_video_duration', true) ?: '',
                            'alt' => get_the_title(),
                        ];
                    } else {
                        $full_url = get_the_post_thumbnail_url($post_id, 'full') ?: \Elementor\Utils::get_placeholder_image_src();
                        $items_data[] = [
                            'type' => 'photo',
                            'href' => $full_url,
                            'thumb' => $full_url,
                            'category' => $cat_name,
                            'title' => get_the_title(),
                            'desc' => '',
                            'duration' => '',
                            'alt' => get_post_meta($post_id, '_nexus_item_alt', true) ?: get_the_title(),
                        ];
                    }
                }
                wp_reset_postdata();
            }
        } else {
            // ── Repeater path ───────────────────────────────────────────────
            foreach ($settings['gallery_items'] ?? [] as $item) {
                $type = $item['item_type'] ?? 'photo';
                $img_url = $item['item_image']['url'] ?? '';
                $cat = $item['item_category'] ?? 'General';
                $title = $item['item_title'] ?? '';
                $desc = $item['item_description'] ?? '';

                if ($type === 'video') {
                    $raw_url = $item['item_video_url']['url'] ?? '';
                    $items_data[] = [
                        'type' => 'video',
                        'href' => $raw_url,
                        'thumb' => nexus_video_thumbnail_url($raw_url, $img_url),
                        'category' => $cat,
                        'title' => $title,
                        'desc' => $desc,
                        'duration' => $item['item_duration'] ?? '',
                        'alt' => $title ?: __('Video thumbnail', 'nexus-elements'),
                    ];
                } else {
                    $fallback = \Elementor\Utils::get_placeholder_image_src();
                    $href = $img_url ?: $fallback;
                    $items_data[] = [
                        'type' => 'photo',
                        'href' => $href,
                        'thumb' => $href,
                        'category' => $cat,
                        'title' => $title,
                        'desc' => $desc,
                        'duration' => '',
                        'alt' => $item['item_alt'] ?? $title,
                    ];
                }
            }
        }

        if (empty($items_data))
            return;

        // Show More settings (addon path only)
        $show_more_on   = $addon_active && ('yes' === ($settings['show_more_enabled'] ?? ''));
        $raw_per_page   = $settings['query_per_page'] ?? '';
        $initial_count  = ($raw_per_page !== '' && $raw_per_page !== null && intval($raw_per_page) > 0) ? intval($raw_per_page) : 0;
        $show_more_on   = $show_more_on && $initial_count > 0;
        $increment      = max(1, intval($settings['show_more_increment'] ?? 12));
        $show_more_text = $settings['show_more_text'] ?? esc_html__('Show More', 'nexus-elements');

        // ── Build unique category list for filter bar ────────────────────────
        $categories = [];
        foreach ($items_data as $item) {
            $cat = trim($item['category']);
            if ($cat !== '' && !in_array($cat, $categories, true)) {
                $categories[] = $cat;
            }
        }
        $has_filter = $show_filter && count($categories) > 0;
        ?>
        <div id="<?php echo esc_attr($uid); ?>" class="nexus-gallery-wrapper"
             <?php if ($show_more_on): ?>
             data-initial="<?php echo esc_attr($initial_count); ?>"
             data-increment="<?php echo esc_attr($increment); ?>"
             <?php endif; ?>>

            <?php if ($has_filter): ?>
            <div class="nexus-gallery-filter" role="group" aria-label="<?php esc_attr_e('Filter gallery', 'nexus-elements'); ?>">
                <button type="button" class="nexus-gallery-filter-btn active" data-filter="all"><?php esc_html_e('All', 'nexus-elements'); ?></button>
                <?php foreach ($categories as $cat): ?>
                <button type="button" class="nexus-gallery-filter-btn" data-filter="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="nexus-gallery-grid" data-layout="<?php echo esc_attr($layout); ?>">
                <?php
                foreach ($items_data as $idx => $item):
                    $is_video = ('video' === $item['type']);
                    ?>
                <div class="nexus-gallery-item nexus-gallery-type-<?php echo esc_attr($item['type']); ?><?php if ($show_more_on && $idx >= $initial_count) echo ' nexus-gallery-hidden'; ?>"
                     data-category="<?php echo esc_attr($item['category']); ?>">
                    <?php
                    $lb_href = $item['href'];
                    if ($is_video) {
                        $embed_url = nexus_video_embed_url($item['href']);

                        $action_settings = [
                            'type' => 'video',
                            'url' => $embed_url
                        ];

                        $lb_href = '#elementor-action:action=lightbox&settings=' . base64_encode(wp_json_encode($action_settings));
                    }
                    ?>
                    <a href="<?php echo esc_attr($lb_href); ?>"
                       class="elementor-clickable"
                       data-elementor-open-lightbox="yes"
                       data-elementor-lightbox-slideshow="<?php echo esc_attr($uid); ?>"
                       <?php if ($lb_show_title && $item['title']) echo 'data-elementor-lightbox-title="' . esc_attr($item['title']) . '"'; ?>
                       <?php if ($lb_show_desc && $item['desc']) echo 'data-elementor-lightbox-description="' . esc_attr($item['desc']) . '"'; ?>>
                        <div class="nexus-gallery-thumb">
                            <img src="<?php echo esc_url($item['thumb']); ?>"
                                 alt="<?php echo esc_attr($item['alt']); ?>"
                                 loading="lazy">

                            <div class="nexus-gallery-overlay">
                                <?php if ($is_video): ?>
                                <div class="nexus-gallery-play" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                                <?php endif; ?>
                                <?php if ($show_captions && $item['title']): ?>
                                <div class="nexus-gallery-caption">
                                    <span class="nexus-gallery-caption-title"><?php echo esc_html($item['title']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($item['duration']): ?>
                            <span class="nexus-gallery-duration"><?php echo esc_html($item['duration']); ?></span>
                            <?php endif; ?>

                            <?php if ($is_video): ?>
                            <span class="nexus-gallery-type-badge" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($show_more_on && count($items_data) > $initial_count): ?>
            <div class="nexus-gallery-show-more-wrap">
                <button type="button" class="nexus-gallery-show-more-btn"><?php echo esc_html($show_more_text); ?></button>
            </div>
            <?php endif; ?>
        </div>

        <style>
        /* ── Filter ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-filter { display:flex; flex-wrap:wrap; align-items:center; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-filter-btn { border:none; cursor:pointer; padding:.5rem 1.25rem; border-radius:var(--ng-filter-radius, 9999px); font-weight:500; transition:background .25s,color .25s; }

        /* ── Grid ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="grid"] { display:grid; grid-template-columns:repeat(var(--ng-cols,3),1fr); gap:var(--ng-gap,16px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="grid"] .nexus-gallery-thumb { height:var(--ng-height,auto); aspect-ratio:1/1; }

        /* ── Masonry ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="masonry"] { columns:var(--ng-cols,3); column-gap:var(--ng-gap,16px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="masonry"] .nexus-gallery-item { break-inside:avoid; margin-bottom:var(--ng-gap,16px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="masonry"] .nexus-gallery-thumb { height:auto; }

        /* ── Justified ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="justified"] { display:flex; flex-wrap:wrap; gap:var(--ng-gap,16px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="justified"] .nexus-gallery-item { flex:1 1 calc(100%/var(--ng-cols,3) - var(--ng-gap,16px)); min-width:150px; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="justified"] .nexus-gallery-thumb { height:var(--ng-height,auto); aspect-ratio:1/1; }

        /* ── Metro ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="metro"] { display:grid; grid-template-columns:repeat(4,1fr); grid-auto-rows:var(--ng-height,220px); gap:var(--ng-gap,16px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="metro"] .nexus-gallery-item:nth-child(5n+1) { grid-column:span 2; grid-row:span 2; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="metro"] .nexus-gallery-thumb { height:100%; }

        /* ── Thumb / Item ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-item { overflow:hidden; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-item a { display:block; text-decoration:none; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-thumb { position:relative; overflow:hidden; width:100%; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-thumb img { display:block; width:100%; height:100%; object-fit:var(--ng-fit, cover); transition:transform .45s ease; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-item:hover .nexus-gallery-thumb img { transform:scale(1.06); }

        /* ── Overlay ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-overlay { position:absolute; inset:0; background:var(--ng-overlay,rgba(0,0,0,.55)); opacity:0; transition:opacity .3s ease; display:flex; flex-direction:column; align-items:center; justify-content:center; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-item:hover .nexus-gallery-overlay { opacity:1; }

        /* ── Play button ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-play { width:var(--ng-play-size,52px); height:var(--ng-play-size,52px); border-radius:50%; display:flex; align-items:center; justify-content:center; transition:transform .25s; flex-shrink:0; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-play svg { width:55%; height:55%; margin-left:10%; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-item:hover .nexus-gallery-play { transform:scale(1.1); }

        /* ── Caption ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-caption { position:absolute; bottom:0; left:0; right:0; padding:.75rem 1rem; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-caption-title { display:block; font-size:.95rem; font-weight:600; }

        /* ── Badges ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-duration { position:absolute; bottom:.5rem; right:.5rem; padding:.2rem .5rem; border-radius:4px; font-size:.75rem; font-weight:600; z-index:2; }

        /* ── Show More ── */
        #<?php echo esc_attr($uid); ?> .nexus-gallery-hidden { display:none !important; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-show-more-wrap { text-align:center; margin-top:var(--ng-gap,16px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-show-more-btn { border:none; cursor:pointer; padding:.75rem 2.5rem; border-radius:9999px; font-size:.95rem; font-weight:600; background:linear-gradient(135deg,#d63384,#6f42c1); color:#fff; transition:opacity .25s,transform .25s; }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-show-more-btn:hover { opacity:.9; transform:translateY(-1px); }
        #<?php echo esc_attr($uid); ?> .nexus-gallery-type-badge { position:absolute; top:.5rem; left:.5rem; width:24px; height:24px; background:rgba(0,0,0,.6); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; z-index:2; }

        /* ── Responsive ── */
        @media(max-width:1024px){
            #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="metro"] { grid-template-columns:repeat(2,1fr); }
        }
        @media(max-width:767px){
            #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="grid"],
            #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="justified"] { grid-template-columns:1fr; }
            #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="masonry"] { columns:var(--ng-cols,1); }
            #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="metro"] { grid-template-columns:1fr; }
            #<?php echo esc_attr($uid); ?> .nexus-gallery-grid[data-layout="metro"] .nexus-gallery-item:nth-child(5n+1) { grid-column:1; grid-row:auto; }
        }
        </style>

        <script>
        (function(){
            var wrapper = document.getElementById('<?php echo esc_js($uid); ?>');
            if(!wrapper) return;

            var allItems    = Array.prototype.slice.call(wrapper.querySelectorAll('.nexus-gallery-item'));
            var btns        = wrapper.querySelectorAll('.nexus-gallery-filter-btn');
            var moreBtn     = wrapper.querySelector('.nexus-gallery-show-more-btn');
            var initial     = parseInt(wrapper.getAttribute('data-initial')) || 0;
            var increment   = parseInt(wrapper.getAttribute('data-increment')) || 12;
            var showMoreOn  = initial > 0 && moreBtn;
            var activeFilter= 'all';
            var visibleCount= initial;

            function getFiltered(){
                return allItems.filter(function(item){
                    return activeFilter === 'all' || item.getAttribute('data-category') === activeFilter;
                });
            }

            function applyVisibility(){
                var filtered = getFiltered();
                var shown = 0;
                allItems.forEach(function(item){
                    var matchesFilter = activeFilter === 'all' || item.getAttribute('data-category') === activeFilter;
                    if(!matchesFilter){
                        item.style.display = 'none';
                        item.classList.add('nexus-gallery-hidden');
                        return;
                    }
                    if(showMoreOn && shown >= visibleCount){
                        item.style.display = 'none';
                        item.classList.add('nexus-gallery-hidden');
                    } else {
                        item.style.display = '';
                        item.classList.remove('nexus-gallery-hidden');
                    }
                    shown++;
                });
                if(moreBtn){
                    var totalFiltered = filtered.length;
                    var currentlyShown = showMoreOn ? Math.min(visibleCount, totalFiltered) : totalFiltered;
                    moreBtn.parentElement.style.display = (showMoreOn && currentlyShown < totalFiltered) ? '' : 'none';
                }
            }

            /* Filter bar */
            btns.forEach(function(btn){
                btn.addEventListener('click', function(){
                    btns.forEach(function(b){ b.classList.remove('active'); });
                    btn.classList.add('active');
                    activeFilter = btn.getAttribute('data-filter');
                    visibleCount = initial; /* reset on filter change */
                    applyVisibility();
                });
            });

            /* Show More button */
            if(moreBtn){
                moreBtn.addEventListener('click', function(){
                    visibleCount += increment;
                    applyVisibility();
                });
            }

            /* Initial render */
            applyVisibility();
        })();
        </script>
        <?php
    }
}
