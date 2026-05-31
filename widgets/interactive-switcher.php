<?php
if (!defined('ABSPATH')) {
    exit;  // Exit if accessed directly.
}

class Nexus_Interactive_Switcher_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'nexus_interactive_switcher';
    }

    public function get_title()
    {
        return esc_html__('Interactive Switcher', 'nexus-elements');
    }

    public function get_icon()
    {
        return 'eicon-tabs';
    }

    public function get_categories()
    {
        return ['nexus-category'];
    }

    public function get_keywords()
    {
        return ['nexus', 'tab', 'switcher', 'interactive', 'panel', 'toggle'];
    }

    protected function register_controls()
    {
        // ==============================
        // CONTENT TAB
        // ==============================
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Switcher Items', 'nexus-elements'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'tab_title',
            [
                'label' => esc_html__('Tab Title', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Feature Name', 'nexus-elements'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'tab_description',
            [
                'label' => esc_html__('Tab Description', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__('A brief description of this feature or service.', 'nexus-elements'),
            ]
        );

        $repeater->add_control(
            'tab_icon',
            [
                'label' => esc_html__('Icon', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'card_image',
            [
                'label' => esc_html__('Background Image', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'card_tag',
            [
                'label' => esc_html__('Small Tag Text', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Highlight', 'nexus-elements'),
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Learn More', 'nexus-elements'),
            ]
        );

        $repeater->add_control(
            'button_link',
            [
                'label' => esc_html__('Button Link', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', 'nexus-elements'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'items_list',
            [
                'label' => esc_html__('Items List', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ tab_title }}}',
            ]
        );

        $this->end_controls_section();

        // ==============================
        // STYLE TAB
        // ==============================
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Colors & Styling', 'nexus-elements'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Primary Accent Color', 'nexus-elements'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#d63384',  // Default Wilby pink, easily changeable now
                'selectors' => [
                    // This injects a CSS variable directly into the Elementor wrapper
                    '{{WRAPPER}} .nexus-switcher-wrapper' => '--nexus-primary: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ==============================
        // TYPOGRAPHY SECTION
        // ==============================
        $this->start_controls_section(
            'typography_section',
            [
                'label' => esc_html__('Typography', 'nexus-elements'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'           => 'tab_title_typography',
                'label'          => esc_html__('Tab Title', 'nexus-elements'),
                'selector'       => '{{WRAPPER}} .nexus-tab-text h4',
                'fields_options' => [
                    'typography'  => ['default' => 'yes'],
                    'font_size'   => ['default' => ['unit' => 'px', 'size' => 18]],
                    'font_weight' => ['default' => '600'],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'           => 'tab_desc_typography',
                'label'          => esc_html__('Tab Description', 'nexus-elements'),
                'selector'       => '{{WRAPPER}} .nexus-tab-text p',
                'fields_options' => [
                    'typography'  => ['default' => 'yes'],
                    'font_size'   => ['default' => ['unit' => 'px', 'size' => 14]],
                    'font_weight' => ['default' => '400'],
                    'line_height' => ['default' => ['unit' => 'em', 'size' => 1.5]],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'           => 'card_heading_typography',
                'label'          => esc_html__('Card Heading (H2)', 'nexus-elements'),
                'selector'       => '{{WRAPPER}} .nexus-card-inner h2',
                'fields_options' => [
                    'typography'  => ['default' => 'yes'],
                    'font_size'   => ['default' => ['unit' => 'px', 'size' => 36]],
                    'font_weight' => ['default' => '700'],
                    'line_height' => ['default' => ['unit' => 'em', 'size' => 1.2]],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'           => 'card_body_typography',
                'label'          => esc_html__('Card Body Text', 'nexus-elements'),
                'selector'       => '{{WRAPPER}} .nexus-card-inner p',
                'fields_options' => [
                    'typography'  => ['default' => 'yes'],
                    'font_size'   => ['default' => ['unit' => 'px', 'size' => 17]],
                    'font_weight' => ['default' => '400'],
                    'line_height' => ['default' => ['unit' => 'em', 'size' => 1.6]],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'           => 'tag_typography',
                'label'          => esc_html__('Tag / Label', 'nexus-elements'),
                'selector'       => '{{WRAPPER}} .nexus-tag',
                'fields_options' => [
                    'typography'      => ['default' => 'yes'],
                    'font_size'       => ['default' => ['unit' => 'px', 'size' => 12]],
                    'font_weight'     => ['default' => '600'],
                    'letter_spacing'  => ['default' => ['unit' => 'em', 'size' => 0.1]],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'           => 'button_typography',
                'label'          => esc_html__('Button Text', 'nexus-elements'),
                'selector'       => '{{WRAPPER}} .nexus-btn',
                'fields_options' => [
                    'typography'  => ['default' => 'yes'],
                    'font_size'   => ['default' => ['unit' => 'px', 'size' => 15]],
                    'font_weight' => ['default' => '600'],
                ],
            ]
        );

        $this->end_controls_section();

        // ==============================
        // TAB & CARD COLORS SECTION
        // ==============================
        $this->start_controls_section(
            'tab_colors_section',
            [
                'label' => esc_html__('Tab & Card Colors', 'nexus-elements'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tab_heading_normal',
            [
                'label'     => esc_html__('Tab — Normal State', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tab_bg_color',
            [
                'label'     => esc_html__('Tab Background', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#f9fafb',
                'selectors' => [ '{{WRAPPER}}' => '--nexus-tab-bg: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'tab_border_color',
            [
                'label'     => esc_html__('Tab Border Color', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#f3f4f6',
                'selectors' => [ '{{WRAPPER}}' => '--nexus-tab-border: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'tab_title_color',
            [
                'label'     => esc_html__('Tab Title Color', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [ '{{WRAPPER}}' => '--nexus-tab-title-color: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'tab_desc_color',
            [
                'label'     => esc_html__('Tab Description Color', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#6b7280',
                'selectors' => [ '{{WRAPPER}}' => '--nexus-tab-desc-color: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'card_colors_heading',
            [
                'label'     => esc_html__('Card / Overlay', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'card_overlay_color',
            [
                'label'     => esc_html__('Overlay Start Color', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(17,24,39,0.95)',
                'selectors' => [ '{{WRAPPER}}' => '--nexus-overlay-color: {{VALUE}};' ],
            ]
        );

        $this->end_controls_section();

        // ==============================
        // SPACING & SHAPE SECTION
        // ==============================
        $this->start_controls_section(
            'shape_section',
            [
                'label' => esc_html__('Spacing & Shape', 'nexus-elements'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'tab_btn_border_radius',
            [
                'label'      => esc_html__('Tab Button Border Radius', 'nexus-elements'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'rem'],
                'default'    => [
                    'top'      => '16',
                    'right'    => '16',
                    'bottom'   => '16',
                    'left'     => '16',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .nexus-tab-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_btn_padding',
            [
                'label'      => esc_html__('Tab Button Padding', 'nexus-elements'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem'],
                'default'    => [
                    'top'      => '20',
                    'right'    => '20',
                    'bottom'   => '20',
                    'left'     => '20',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .nexus-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label'      => esc_html__('Content Card Border Radius', 'nexus-elements'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'rem'],
                'default'    => [
                    'top'      => '24',
                    'right'    => '24',
                    'bottom'   => '24',
                    'left'     => '24',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .nexus-switcher-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_inner_padding',
            [
                'label'      => esc_html__('Card Inner Padding', 'nexus-elements'),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem'],
                'default'    => [
                    'top'      => '40',
                    'right'    => '40',
                    'bottom'   => '40',
                    'left'     => '40',
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .nexus-card-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_section_heading',
            [
                'label'     => esc_html__('Icon', 'nexus-elements'),
                'type'      => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label'      => esc_html__('Icon Size', 'nexus-elements'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'rem'],
                'range'      => [
                    'px'  => ['min' => 12, 'max' => 80, 'step' => 1],
                    'rem' => ['min' => 0.5, 'max' => 5, 'step' => 0.1],
                ],
                'default'    => ['unit' => 'px', 'size' => 24],
                'selectors'  => [
                    '{{WRAPPER}}' => '--nexus-icon-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_text_gap',
            [
                'label'      => esc_html__('Icon / Text Gap', 'nexus-elements'),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'rem'],
                'range'      => [
                    'px'  => ['min' => 0, 'max' => 60, 'step' => 1],
                    'rem' => ['min' => 0, 'max' => 4, 'step' => 0.1],
                ],
                'default'    => ['unit' => 'px', 'size' => 20],
                'selectors'  => [
                    '{{WRAPPER}}' => '--nexus-tab-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $uid = 'nexus-switcher-' . $this->get_id();
        ?>

		<?php if ( empty( $settings['items_list'] ) ) return; ?>

		<div id="<?php echo esc_attr($uid); ?>" class="nexus-switcher-wrapper">
			<div class="nexus-switcher-tabs">
				<?php
                foreach ($settings['items_list'] as $index => $item):
                    $active_class = (0 === $index) ? 'active' : '';
                    $target_id = $uid . '-tab-' . $index;
                    ?>
					<button type="button" class="nexus-tab-btn <?php echo esc_attr($active_class); ?>" onclick="switchNexusTab_<?php echo esc_js($this->get_id()); ?>(event, '<?php echo esc_attr($target_id); ?>')">
						<div class="nexus-icon">
							<?php \Elementor\Icons_Manager::render_icon($item['tab_icon'], ['aria-hidden' => 'true']); ?>
						</div>
						<div class="nexus-tab-text">
							<h4><?php echo esc_html($item['tab_title']); ?></h4>
							<p><?php echo esc_html($item['tab_description']); ?></p>
						</div>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="nexus-switcher-content">
				<?php
                foreach ($settings['items_list'] as $index => $item):
                    $active_class = (0 === $index) ? 'active' : '';
                    $target_id = $uid . '-tab-' . $index;
                    // Sanitise link attributes safely
                    $btn_target = ! empty( $item['button_link']['is_external'] ) ? ' target="_blank"' : '';
                    $btn_rel    = ! empty( $item['button_link']['nofollow'] )    ? ' rel="nofollow"'  : '';
                    ?>
					<div id="<?php echo esc_attr($target_id); ?>" class="nexus-card <?php echo esc_attr($active_class); ?>">
						<img src="<?php echo esc_url($item['card_image']['url']); ?>" class="nexus-bg-img" alt="<?php echo esc_attr($item['tab_title']); ?>">
						<div class="nexus-overlay"></div>
						<div class="nexus-card-inner">
							<span class="nexus-tag"><?php echo esc_html($item['card_tag']); ?></span>
							<h2><?php echo esc_html($item['tab_title']); ?></h2>
							<p><?php echo esc_html($item['tab_description']); ?></p>
							<?php if ( ! empty( $item['button_text'] ) ) : ?>
								<a href="<?php echo esc_url($item['button_link']['url']); ?>" class="nexus-btn"<?php echo $btn_target . $btn_rel; ?>><?php echo esc_html($item['button_text']); ?></a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<style>
		/* CSS Variables are used here, fed directly from Elementor's color picker */
		#<?php echo esc_attr($uid); ?> {
			display: grid; grid-template-columns: 1fr; gap: 2rem;
			--nexus-primary: #d63384; /* Fallback if not set via Elementor color control */
		}
		@media (min-width: 768px) { #<?php echo esc_attr($uid); ?> { grid-template-columns: 1.2fr 2fr; min-height: 500px; } }
		
		/* --- Layout & Structure (non-Elementor-controlled) --- */
		#<?php echo esc_attr($uid); ?> .nexus-switcher-tabs { display: flex; flex-direction: column; gap: 0.75rem; }
		/* Padding & border-radius are controlled via Elementor Spacing & Shape panel */
		#<?php echo esc_attr($uid); ?> .nexus-tab-btn { display: flex; align-items: center; gap: var(--nexus-tab-gap, 20px); background: var(--nexus-tab-bg, #f9fafb); border: 1px solid var(--nexus-tab-border, #f3f4f6); text-align: left; cursor: pointer; transition: all 0.3s ease; width: 100%; outline: none; }
		#<?php echo esc_attr($uid); ?> .nexus-tab-btn:hover { background: #f3f4f6; transform: translateX(-4px); }
		#<?php echo esc_attr($uid); ?> .nexus-tab-btn.active { background: var(--nexus-primary); border-color: var(--nexus-primary); color: white; transform: scale(1.02); box-shadow: 0 10px 15px -3px rgba(0,0,0, 0.1); }
		
		#<?php echo esc_attr($uid); ?> .nexus-icon { flex-shrink: 0; color: var(--nexus-primary); transition: color 0.3s ease; font-size: var(--nexus-icon-size, 24px); display: flex; align-items: center; justify-content: center; width: var(--nexus-icon-size, 24px); height: var(--nexus-icon-size, 24px); }
		#<?php echo esc_attr($uid); ?> .nexus-icon svg { width: 100%; height: 100%; fill: currentColor; }
		#<?php echo esc_attr($uid); ?> .nexus-tab-btn.active .nexus-icon { color: white; }
		
		/* font-size & font-weight controlled via Elementor Typography panel */
		#<?php echo esc_attr($uid); ?> .nexus-tab-text h4 { margin: 0 0 0.25rem 0; color: var(--nexus-tab-title-color, #111827); }
		#<?php echo esc_attr($uid); ?> .nexus-tab-text p { margin: 0; color: var(--nexus-tab-desc-color, #6b7280); }
		#<?php echo esc_attr($uid); ?> .nexus-tab-btn.active .nexus-tab-text h4, #<?php echo esc_attr($uid); ?> .nexus-tab-btn.active .nexus-tab-text p { color: white; }
		
		/* border-radius controlled via Elementor Spacing & Shape panel */
		#<?php echo esc_attr($uid); ?> .nexus-switcher-content { position: relative; overflow: hidden; min-height: 400px; }
		@media (min-width: 768px) { #<?php echo esc_attr($uid); ?> .nexus-switcher-content { min-height: 100%; } }
		
		#<?php echo esc_attr($uid); ?> .nexus-card { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; visibility: hidden; transition: opacity 0.4s ease, visibility 0.4s ease; display: flex; flex-direction: column; justify-content: flex-end; }
		#<?php echo esc_attr($uid); ?> .nexus-card.active { opacity: 1; visibility: visible; position: relative; }
		
		#<?php echo esc_attr($uid); ?> .nexus-bg-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
		#<?php echo esc_attr($uid); ?> .nexus-card.active .nexus-bg-img { transform: scale(1.05); }
		
		#<?php echo esc_attr($uid); ?> .nexus-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, var(--nexus-overlay-color, rgba(17, 24, 39, 0.95)), rgba(17, 24, 39, 0.2), transparent); }
		
		/* padding controlled via Elementor Spacing & Shape panel */
		#<?php echo esc_attr($uid); ?> .nexus-card-inner { position: relative; z-index: 10; }
		/* font-size, font-weight, letter-spacing controlled via Elementor Typography panel */
		#<?php echo esc_attr($uid); ?> .nexus-tag { display: inline-block; color: var(--nexus-primary); text-transform: uppercase; margin-bottom: 0.75rem; }
		#<?php echo esc_attr($uid); ?> .nexus-card-inner h2 { color: white; margin: 0 0 0.75rem 0; }
		#<?php echo esc_attr($uid); ?> .nexus-card-inner p { color: #e5e7eb; margin: 0 0 1.5rem 0; max-width: 90%; }
		#<?php echo esc_attr($uid); ?> .nexus-btn { display: inline-block; background: white; color: var(--nexus-primary); padding: 0.6rem 1.5rem; border-radius: 9999px; text-decoration: none; transition: background 0.3s ease; }
		#<?php echo esc_attr($uid); ?> .nexus-btn:hover { background: #f3f4f6; }
		</style>

		<script>
		function switchNexusTab_<?php echo esc_js($this->get_id()); ?>(event, targetId) {
			event.preventDefault();
			const wrapper = document.getElementById('<?php echo esc_js($uid); ?>');
			
			const tablinks = wrapper.querySelectorAll(".nexus-tab-btn");
			tablinks.forEach(btn => btn.classList.remove("active"));
			
			const tabcontent = wrapper.querySelectorAll(".nexus-card");
			tabcontent.forEach(card => card.classList.remove("active"));
			
			event.currentTarget.classList.add("active");
			document.getElementById(targetId).classList.add("active");
		}
		</script>
		<?php
    }
}
