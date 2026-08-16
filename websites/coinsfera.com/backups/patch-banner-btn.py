from pathlib import Path

p = Path("/home/u2095-ezyskmwwfto7/www/coinsfera.com/public_html/wp-content/plugins/coinsfera-plugin/elementor/cryptocurrency-inner-banner.php")
src = p.read_text(encoding="utf-8")
old = "$html .= '<span>' . esc_html($settings['cryptocurrency_inner_banner_btn_lbl']) . '</span></a>';"
new = "$btn_lbl = trim( wp_strip_all_tags( html_entity_decode( (string) $settings['cryptocurrency_inner_banner_btn_lbl'], ENT_QUOTES, 'UTF-8' ) ) );\n                            $html .= '<span>' . esc_html($btn_lbl) . '</span></a>';"
if old not in src:
    raise SystemExit("pattern not found")
p.write_text(src.replace(old, new, 1), encoding="utf-8")
print("patched widget")
