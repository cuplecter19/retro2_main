<?php
if (!defined('_GNUBOARD_')) exit;

if (!defined('MAIN_SKIN_NAME')) {
    define('MAIN_SKIN_NAME', 'retro2_main');
}

if (!defined('MAIN_SKIN_DIR')) {
    define('MAIN_SKIN_DIR', dirname(__FILE__));
}

if (!defined('MAIN_SKIN_URL')) {
    if (defined('G5_SKIN_URL')) {
        define('MAIN_SKIN_URL', G5_SKIN_URL . '/board/' . MAIN_SKIN_NAME);
    } elseif (defined('G5_URL')) {
        define('MAIN_SKIN_URL', G5_URL . '/skin/board/' . MAIN_SKIN_NAME);
    } else {
        define('MAIN_SKIN_URL', '/skin/board/' . MAIN_SKIN_NAME);
    }
}

/* ══════════════════════════════════════════════
   이미지 업로드 제한 상수
══════════════════════════════════════════════ */
if (!defined('MAIN_SKIN_MAX_UPLOAD_SIZE')) {
    define('MAIN_SKIN_MAX_UPLOAD_SIZE', 2 * 1024 * 1024);
}
if (!defined('MAIN_SKIN_MAX_GIF_SIZE')) {
    define('MAIN_SKIN_MAX_GIF_SIZE', 1 * 1024 * 1024);
}
if (!defined('MAIN_SKIN_MAX_IMAGE_WIDTH')) {
    define('MAIN_SKIN_MAX_IMAGE_WIDTH', 800);
}
if (!defined('MAIN_SKIN_JPEG_QUALITY')) {
    define('MAIN_SKIN_JPEG_QUALITY', 80);
}
if (!defined('MAIN_SKIN_WEBP_QUALITY')) {
    define('MAIN_SKIN_WEBP_QUALITY', 85);
}
if (!defined('MAIN_SKIN_PNG_COMPRESSION')) {
    define('MAIN_SKIN_PNG_COMPRESSION', 8);
}
if (!defined('MAIN_SKIN_CACHE_MAX_AGE')) {
    define('MAIN_SKIN_CACHE_MAX_AGE', 2592000);
}

/* ══════════════════════════════════════════════
   반응형 텍스트 기준 크기
══════════════════════════════════════════════ */
if (!defined('MAIN_SKIN_BASE_WIDTH')) {
    define('MAIN_SKIN_BASE_WIDTH', 700);
}
if (!defined('MAIN_SKIN_BASE_HEIGHT')) {
    define('MAIN_SKIN_BASE_HEIGHT', 850);
}

function main_skin_storage_root_path() {
    if (defined('G5_DATA_PATH')) {
        return G5_DATA_PATH . '/file/main_skin';
    }
    return MAIN_SKIN_DIR . '/data';
}

function main_skin_storage_root_url() {
    if (defined('G5_DATA_URL')) {
        return G5_DATA_URL . '/file/main_skin';
    }
    return MAIN_SKIN_URL . '/data';
}

function main_skin_asset_types() {
    return array('visual', 'banner', 'sticker', 'parallax', 'background', 'fonts');
}

function main_skin_asset_dir($type) {
    return main_skin_storage_root_path() . '/' . $type;
}

function main_skin_asset_url($type) {
    return main_skin_storage_root_url() . '/' . $type;
}

function main_skin_write_security_file($dir) {
    $htaccess = $dir . '/.htaccess';
    if (!file_exists($htaccess)) {
        $max_age = (int)MAIN_SKIN_CACHE_MAX_AGE;
        file_put_contents(
            $htaccess,
            "Options -Indexes\n" .
            "<FilesMatch \"\\.(json|php)$\">\n" .
            "  Deny from all\n" .
            "</FilesMatch>\n" .
            "<IfModule mod_expires.c>\n" .
            "  ExpiresActive On\n" .
            "  ExpiresByType image/png \"access plus 30 days\"\n" .
            "  ExpiresByType image/gif \"access plus 30 days\"\n" .
            "  ExpiresByType image/jpeg \"access plus 30 days\"\n" .
            "  ExpiresByType image/webp \"access plus 30 days\"\n" .
            "  ExpiresByType video/mp4 \"access plus 30 days\"\n" .
            "</IfModule>\n" .
            "<IfModule mod_headers.c>\n" .
            "  <FilesMatch \"\\.(png|gif|jpg|jpeg|webp|bmp|mp4)$\">\n" .
            "    Header set Cache-Control \"public, max-age=" . $max_age . "\"\n" .
            "  </FilesMatch>\n" .
            "</IfModule>\n"
        );
    }
}

function main_skin_ensure_storage() {
    $root = main_skin_storage_root_path();
    if (!is_dir($root)) {
        mkdir($root, 0755, true);
    }
    main_skin_write_security_file($root);

    foreach (main_skin_asset_types() as $type) {
        $dir = main_skin_asset_dir($type);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($dir . '/.gitkeep')) {
            file_put_contents($dir . '/.gitkeep', '');
        }
        main_skin_write_security_file($dir);
    }
    return $root;
}

/* ── 텍스트 오버레이 키 목록 ── */
function main_skin_text_keys() {
    return array('bg_title', 'title1', 'title2', 'title3', 'title3_body', 'general');
}

/* ── 날짜 위젯 파트 키 목록 ── */
function main_skin_date_widget_parts() {
    return array(
        'issue' => '발행호 (년·월호)',
        'pub'   => '발간일 (날짜)',
        'clock' => '시계',
    );
}

function main_skin_default_config() {
    $cfg = array(
        /* 배경 이미지 */
        'bg_image' => '',
        'bg_image_source_type' => 'url',
        'bg_fit' => 'cover',

        /* 레트로 창 */
        'window_title' => '최신글',
        'banner_title' => '배너',
        'latest_rows' => 8,
        'latest_boards' => 'free',

        /* 윈도우 위치 */
        'latest_win_top' => 120,
        'latest_win_left' => -260,
        'banner_win_top' => 80,
        'banner_win_left' => 730,

        /* 날짜 위젯 — 공통 */
        'date_widget_enabled' => 1,
        'date_widget_top' => 20,
        'date_widget_right' => 20,
        'date_widget_stroke_color' => '',
        'date_widget_stroke_width' => 0,

        /* 날짜 위젯 — 파트별 색상·폰트 */
        'date_widget_issue_color' => '#000000',
        'date_widget_issue_font'  => '',
        'date_widget_pub_color'   => '#000000',
        'date_widget_pub_font'    => '',
        'date_widget_clock_color' => '#000000',
        'date_widget_clock_font'  => '',

        /* 레거시 호환 (구버전 단일 색상/폰트 → 마이그레이션 시 사용) */
        'date_widget_color' => '#000000',
        'date_widget_font'  => '',

        /* 패럴랙스 */
        'parallax_fg_image' => '', 'parallax_fg_source_type' => 'url',
        'parallax_fg_pos_v' => 'center', 'parallax_fg_pos_h' => 'center',
        'parallax_fg_offset_x' => 0, 'parallax_fg_offset_y' => 0,
        'parallax_ng_image' => '', 'parallax_ng_source_type' => 'url',
        'parallax_ng_pos_v' => 'center', 'parallax_ng_pos_h' => 'center',
        'parallax_ng_offset_x' => 0, 'parallax_ng_offset_y' => 0,
        'parallax_bg_image' => '', 'parallax_bg_source_type' => 'url',
        'parallax_bg_pos_v' => 'center', 'parallax_bg_pos_h' => 'center',
        'parallax_bg_offset_x' => 0, 'parallax_bg_offset_y' => 0,

        /* 레거시 폴라로이드 */
        'visual_image' => '', 'visual_alt' => '',
        'polaroid_1_image' => '', 'polaroid_1_alt' => '', 'polaroid_1_caption' => '', 'polaroid_1_rotate' => '-3',
        'polaroid_2_image' => '', 'polaroid_2_alt' => '', 'polaroid_2_caption' => '', 'polaroid_2_rotate' => '2',
    );

    /* 텍스트 오버레이 */
    foreach (main_skin_text_keys() as $tk) {
        $d = main_skin_text_defaults($tk);
        $cfg['text_' . $tk]                    = $d['content'];
        $cfg['text_' . $tk . '_font']          = $d['font'];
        $cfg['text_' . $tk . '_size']          = $d['size'];
        $cfg['text_' . $tk . '_size_unit']     = $d['size_unit'];
        $cfg['text_' . $tk . '_spacing']       = $d['spacing'];
        $cfg['text_' . $tk . '_line_height']   = $d['line_height'];
        $cfg['text_' . $tk . '_color']         = $d['color'];
        $cfg['text_' . $tk . '_top']           = $d['top'];
        $cfg['text_' . $tk . '_left']          = $d['left'];
        $cfg['text_' . $tk . '_bold']          = $d['bold'];
        $cfg['text_' . $tk . '_italic']        = $d['italic'];
        $cfg['text_' . $tk . '_stroke_color']  = $d['stroke_color'];
        $cfg['text_' . $tk . '_stroke_width']  = $d['stroke_width'];
        $cfg['text_' . $tk . '_scale_y']       = $d['scale_y'];
    }

    return $cfg;
}

function main_skin_text_defaults($key) {
    $map = array(
        'bg_title'    => array('content' => '', 'font' => '', 'size' => 48, 'size_unit' => 'px', 'spacing' => 0, 'line_height' => '1.2', 'color' => '#ffffff', 'top' => 400, 'left' => 50,  'bold' => 1, 'italic' => 0, 'stroke_color' => '', 'stroke_width' => 0, 'scale_y' => 100),
        'title1'      => array('content' => '', 'font' => '', 'size' => 72, 'size_unit' => 'px', 'spacing' => 0, 'line_height' => '1.0', 'color' => '#cc0000', 'top' => 780, 'left' => 10,  'bold' => 1, 'italic' => 0, 'stroke_color' => '', 'stroke_width' => 0, 'scale_y' => 100),
        'title2'      => array('content' => '', 'font' => '', 'size' => 36, 'size_unit' => 'px', 'spacing' => 0, 'line_height' => '1.3', 'color' => '#0000cc', 'top' => 380, 'left' => 350, 'bold' => 1, 'italic' => 0, 'stroke_color' => '', 'stroke_width' => 0, 'scale_y' => 100),
        'title3'      => array('content' => '', 'font' => '', 'size' => 24, 'size_unit' => 'px', 'spacing' => 0, 'line_height' => '1.4', 'color' => '#008800', 'top' => 480, 'left' => 350, 'bold' => 1, 'italic' => 0, 'stroke_color' => '', 'stroke_width' => 0, 'scale_y' => 100),
        'title3_body' => array('content' => '', 'font' => '', 'size' => 14, 'size_unit' => 'px', 'spacing' => 0, 'line_height' => '1.6', 'color' => '#008800', 'top' => 520, 'left' => 350, 'bold' => 0, 'italic' => 0, 'stroke_color' => '', 'stroke_width' => 0, 'scale_y' => 100),
        'general'     => array('content' => '', 'font' => '', 'size' => 14, 'size_unit' => 'px', 'spacing' => 0, 'line_height' => '1.6', 'color' => '#cc44cc', 'top' => 640, 'left' => 350, 'bold' => 0, 'italic' => 0, 'stroke_color' => '', 'stroke_width' => 0, 'scale_y' => 100),
    );
    return isset($map[$key]) ? $map[$key] : $map['general'];
}

function main_skin_legacy_config_to_current($config) {
    $legacy = main_skin_default_config();

    if (isset($config['visual']['src'])) {
        $legacy['visual_image'] = $config['visual']['src'];
        $legacy['visual_alt'] = isset($config['visual']['alt']) ? $config['visual']['alt'] : $legacy['visual_alt'];
    }
    if (isset($config['polaroid1']['src'])) {
        $legacy['polaroid_1_image'] = $config['polaroid1']['src'];
        $legacy['polaroid_1_alt'] = isset($config['polaroid1']['alt']) ? $config['polaroid1']['alt'] : $legacy['polaroid_1_alt'];
        $legacy['polaroid_1_caption'] = isset($config['polaroid1']['caption']) ? $config['polaroid1']['caption'] : '';
        $legacy['polaroid_1_rotate'] = isset($config['polaroid1']['rotate']) ? $config['polaroid1']['rotate'] : $legacy['polaroid_1_rotate'];
    }
    if (isset($config['polaroid2']['src'])) {
        $legacy['polaroid_2_image'] = $config['polaroid2']['src'];
        $legacy['polaroid_2_alt'] = isset($config['polaroid2']['alt']) ? $config['polaroid2']['alt'] : $legacy['polaroid_2_alt'];
        $legacy['polaroid_2_caption'] = isset($config['polaroid2']['caption']) ? $config['polaroid2']['caption'] : '';
        $legacy['polaroid_2_rotate'] = isset($config['polaroid2']['rotate']) ? $config['polaroid2']['rotate'] : $legacy['polaroid_2_rotate'];
    }
    if (isset($config['retro_window'])) {
        $legacy['window_title'] = isset($config['retro_window']['title']) ? $config['retro_window']['title'] : $legacy['window_title'];
        $legacy['latest_rows'] = isset($config['retro_window']['limit']) ? (int)$config['retro_window']['limit'] : $legacy['latest_rows'];
        if (!empty($config['retro_window']['board_ids']) && is_array($config['retro_window']['board_ids'])) {
            $legacy['latest_boards'] = implode(',', $config['retro_window']['board_ids']);
        }
    }
    return array_replace($legacy, $config);
}

/* ── 구버전 단일 색상/폰트 → 파트별 마이그레이션 ── */
function main_skin_migrate_date_widget($config) {
    $parts = array_keys(main_skin_date_widget_parts());
    $needs_migrate = true;
    foreach ($parts as $p) {
        if (isset($config['date_widget_' . $p . '_color']) && $config['date_widget_' . $p . '_color'] !== '') {
            $needs_migrate = false;
            break;
        }
    }
    if (!$needs_migrate) return $config;

    $old_color = isset($config['date_widget_color']) ? $config['date_widget_color'] : '#000000';
    $old_font  = isset($config['date_widget_font'])  ? $config['date_widget_font']  : '';
    foreach ($parts as $p) {
        if (!isset($config['date_widget_' . $p . '_color']) || $config['date_widget_' . $p . '_color'] === '') {
            $config['date_widget_' . $p . '_color'] = $old_color;
        }
        if (!isset($config['date_widget_' . $p . '_font']) || $config['date_widget_' . $p . '_font'] === '') {
            $config['date_widget_' . $p . '_font'] = $old_font;
        }
    }
    return $config;
}

function get_main_skin_config() {
    $file = main_skin_storage_root_path() . '/config.json';
    if (!file_exists($file)) {
        return main_skin_default_config();
    }
    $config = json_decode(file_get_contents($file), true);
    if (!is_array($config)) {
        return main_skin_default_config();
    }
    if (isset($config['retro_window']) || isset($config['visual']) || isset($config['polaroid1'])) {
        $config = main_skin_legacy_config_to_current($config);
    }
    $config = array_replace(main_skin_default_config(), $config);
    $config = main_skin_migrate_date_widget($config);
    return $config;
}

function save_main_skin_config($config) {
    main_skin_ensure_storage();
    return file_put_contents(
        main_skin_storage_root_path() . '/config.json',
        json_encode(array_replace(main_skin_default_config(), $config), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

/* ══════════════════════════════════════════════
   텍스트 테두리(stroke) CSS 생성
   -webkit-text-stroke + text-shadow 폴백
══════════════════════════════════════════════ */
function main_skin_text_stroke_css($stroke_color, $stroke_width) {
    $stroke_color = trim((string)$stroke_color);
    $stroke_width = (int)$stroke_width;
    if ($stroke_color === '' || $stroke_width <= 0) return '';

    $esc_color = main_skin_esc($stroke_color);
    $css = '-webkit-text-stroke:' . $stroke_width . 'px ' . $esc_color . ';';
    $css .= 'paint-order:stroke fill;';

    $shadows = array();
    $w = $stroke_width;
    for ($angle = 0; $angle < 360; $angle += 30) {
        $rad = deg2rad($angle);
        $x = round(cos($rad) * $w, 2);
        $y = round(sin($rad) * $w, 2);
        $shadows[] = $x . 'px ' . $y . 'px 0 ' . $esc_color;
    }
    $css .= 'text-shadow:' . implode(',', $shadows) . ';';

    return $css;
}

/* ══════════════════════════════════════════════
   반응형 텍스트 스타일 생성 헬퍼
══════════════════════════════════════════════ */
function main_skin_responsive_text_style($tk, $config) {
    $prefix = 'text_' . $tk;
    $base_w = (int)MAIN_SKIN_BASE_WIDTH;
    $base_h = (int)MAIN_SKIN_BASE_HEIGHT;

    $font    = isset($config[$prefix . '_font'])        ? $config[$prefix . '_font']        : '';
    $size    = isset($config[$prefix . '_size'])         ? $config[$prefix . '_size']         : 14;
    $size_unit = isset($config[$prefix . '_size_unit'])  ? $config[$prefix . '_size_unit']    : 'px';
    $spacing = isset($config[$prefix . '_spacing'])      ? (int)$config[$prefix . '_spacing'] : 0;
    $lh      = isset($config[$prefix . '_line_height'])  ? $config[$prefix . '_line_height']  : '1.4';
    $color   = isset($config[$prefix . '_color'])        ? $config[$prefix . '_color']        : '#000000';
    $top     = isset($config[$prefix . '_top'])          ? (int)$config[$prefix . '_top']     : 0;
    $left    = isset($config[$prefix . '_left'])         ? (int)$config[$prefix . '_left']    : 0;
    $bold    = !empty($config[$prefix . '_bold']);
    $italic  = !empty($config[$prefix . '_italic']);
    $stroke_color = isset($config[$prefix . '_stroke_color']) ? $config[$prefix . '_stroke_color'] : '';
    $stroke_width = isset($config[$prefix . '_stroke_width']) ? (int)$config[$prefix . '_stroke_width'] : 0;
    $scale_y = isset($config[$prefix . '_scale_y'])      ? (int)$config[$prefix . '_scale_y'] : 100;

    $top_pct = round($top / $base_h * 100, 4);
    $style = 'top:' . $top_pct . '%;';

    if ($tk === 'title1' || $tk === 'bg_title') {
        $left_pct = round($left / $base_w * 100, 4);
        $style .= 'left:' . $left_pct . '%;';
    } else {
        $right_pct = round($left / $base_w * 100, 4);
        $style .= 'right:' . $right_pct . '%;';
    }

    /* ── 폰트 사이즈: 단위별 처리 ── */
    if ($size_unit === 'cw%') {
        $style .= 'font-size:0;';
    } elseif ($size_unit === 'px') {
        $vw_size  = round((int)$size / $base_w * 100, 4);
        $min_size = max(10, (int)((int)$size * 0.5));
        $style .= 'font-size:clamp(' . $min_size . 'px,' . $vw_size . 'vw,' . (int)$size . 'px);';
    } else {
        $style .= 'font-size:' . main_skin_esc($size . $size_unit) . ';';
    }

    if ($spacing !== 0) {
        $sp_vw  = round(abs($spacing) / $base_w * 100, 4);
        $sp_min = max(0, (int)(abs($spacing) * 0.5));
        $sp_max = abs($spacing);
        $sign   = ($spacing < 0) ? '-' : '';
        $style .= 'letter-spacing:clamp(' . $sign . $sp_max . 'px,' . $sign . $sp_vw . 'vw,' . $sign . $sp_min . 'px);';
    } else {
        $style .= 'letter-spacing:0;';
    }

    $style .= 'line-height:' . main_skin_esc($lh) . ';';
    $style .= 'color:' . main_skin_esc($color) . ';';
    if ($bold)   $style .= 'font-weight:bold;';
    if ($italic) $style .= 'font-style:italic;';
    if ($font !== '') $style .= "font-family:'" . main_skin_esc($font) . "',sans-serif;";
    $style .= main_skin_text_stroke_css($stroke_color, $stroke_width);

    /* ── scaleY ── */
    if ($scale_y !== 100 && $scale_y > 0) {
        $sy = round($scale_y / 100, 4);
        if ($tk === 'title1') {
            $style .= 'transform:rotate(-90deg) scaleY(' . $sy . ');';
        } else {
            $style .= 'transform:scaleY(' . $sy . ');';
        }
    }

    return $style;
}
/* ══════════════════════════════════════════════
   날짜 위젯 파트별 인라인 스타일 생성
══════════════════════════════════════════════ */
function main_skin_date_widget_part_style($config, $part) {
    $color = isset($config['date_widget_' . $part . '_color']) ? $config['date_widget_' . $part . '_color'] : '#000000';
    $font  = isset($config['date_widget_' . $part . '_font'])  ? $config['date_widget_' . $part . '_font']  : '';

    $style = 'color:' . main_skin_esc($color) . ';';
    if ($font !== '') {
        $style .= "font-family:'" . main_skin_esc($font) . "',sans-serif;";
    }
    return $style;
}

/* ══════════════════════════════════════════════
   커스텀 폰트
══════════════════════════════════════════════ */
function main_skin_default_font() {
    return array('id' => '', 'name' => '', 'file' => '', 'format' => 'woff2', 'source_type' => 'file');
}

function get_main_fonts() {
    $file = main_skin_storage_root_path() . '/fonts.json';
    if (!file_exists($file)) {
        return array();
    }
    $fonts = json_decode(file_get_contents($file), true);
    return is_array($fonts) ? $fonts : array();
}

function save_main_fonts($fonts) {
    main_skin_ensure_storage();
    return file_put_contents(
        main_skin_storage_root_path() . '/fonts.json',
        json_encode(array_values($fonts), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

function main_skin_upload_font($file_arr, $name) {
    if (!isset($file_arr['error']) || $file_arr['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    $allowed = array('ttf' => 'truetype', 'otf' => 'opentype', 'woff' => 'woff', 'woff2' => 'woff2');
    $ext = strtolower(pathinfo($file_arr['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) {
        return false;
    }
    main_skin_ensure_storage();
    $filename = 'font_' . main_skin_generate_id() . '.' . $ext;
    $destination = main_skin_asset_dir('fonts') . '/' . $filename;
    if (!move_uploaded_file($file_arr['tmp_name'], $destination)) {
        return false;
    }
    return array(
        'id'          => 'font_' . main_skin_generate_id(),
        'name'        => main_skin_limit_text($name, 60),
        'file'        => main_skin_asset_url('fonts') . '/' . $filename,
        'format'      => $allowed[$ext],
        'source_type' => 'file'
    );
}

/* ── @font-face / @import 코드로 폰트 추가 ── */
function main_skin_add_font_by_code($name, $code) {
    $code = trim($code);
    if ($code === '' || $name === '') return false;

    /* @import 또는 @font-face 만 허용 */
    if (strpos($code, '@import') !== 0 && strpos($code, '@font-face') !== 0) {
        /* <link> 태그 → @import로 변환 */
        if (preg_match('/href=["\']([^"\']+)["\']/', $code, $m)) {
            $code = "@import url('" . $m[1] . "');";
        } else {
            return false;
        }
    }

    /* 위험 키워드 차단 */
    $lower = strtolower($code);
    if (strpos($lower, 'expression(') !== false || strpos($lower, 'javascript:') !== false || strpos($lower, 'behavior:') !== false) {
        return false;
    }

    return array(
        'id'          => 'font_' . main_skin_generate_id(),
        'name'        => main_skin_limit_text($name, 60),
        'file'        => '',
        'css_code'    => $code,
        'format'      => 'code',
        'source_type' => 'code'
    );
}

/* ── 서버 경로로 폰트 등록 (중복 업로드 방지) ── */
function main_skin_add_font_by_path($name, $server_path) {
    $name = trim($name);
    $server_path = trim($server_path);
    if ($name === '' || $server_path === '') return false;

    /* 경로 조작 방지 */
    if (strpos($server_path, '..') !== false) return false;
    if (strpos($server_path, "\0") !== false) return false;

    /* 절대경로 또는 DOCUMENT_ROOT 기준 상대경로 해석 */
    if ($server_path[0] !== '/') {
        /* 상대경로 → 웹루트 기준으로 해석 */
        $abs_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $server_path;
    } else {
        /* /로 시작하면 웹 경로일 수 있으므로 DOCUMENT_ROOT 붙여서 확인 */
        $abs_path = $_SERVER['DOCUMENT_ROOT'] . $server_path;
        if (!file_exists($abs_path)) {
            /* 이미 절대 파일시스템 경로인 경우 */
            $abs_path = $server_path;
        }
    }

    $abs_path = realpath($abs_path);
    if ($abs_path === false || !file_exists($abs_path) || !is_file($abs_path)) {
        return false;
    }

    /* 확장자 확인 */
    $allowed = array('ttf' => 'truetype', 'otf' => 'opentype', 'woff' => 'woff', 'woff2' => 'woff2');
    $ext = strtolower(pathinfo($abs_path, PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) return false;

    /* 이미 fonts 디렉토리에 있는 파일인지 확인 → 있으면 그대로 사용 */
    $fonts_dir = realpath(main_skin_asset_dir('fonts'));
    if ($fonts_dir !== false && strpos($abs_path, $fonts_dir) === 0) {
        /* 이미 스킨 폰트 디렉토리 안에 있는 파일 → URL 직접 생성 */
        $rel_name = basename($abs_path);
        $file_url = main_skin_asset_url('fonts') . '/' . $rel_name;
    } else {
        /* 외부 경로 → 웹 접근 가능한 URL 생성 */
        $doc_root = realpath($_SERVER['DOCUMENT_ROOT']);
        if ($doc_root !== false && strpos($abs_path, $doc_root) === 0) {
            $file_url = substr($abs_path, strlen($doc_root));
            /* 윈도우 환경 대비 역슬래시 변환 */
            $file_url = str_replace('\\', '/', $file_url);
        } else {
            /* DOCUMENT_ROOT 밖의 파일은 웹에서 접근 불가 */
            return false;
        }
    }

    /* 기존 등록 폰트와 중복 확인 (같은 파일 URL) */
    $existing = get_main_fonts();
    foreach ($existing as $f) {
        if (isset($f['file']) && $f['file'] === $file_url) {
            return 'duplicate';
        }
    }

    return array(
        'id'          => 'font_' . main_skin_generate_id(),
        'name'        => main_skin_limit_text($name, 60),
        'file'        => $file_url,
        'format'      => $allowed[$ext],
        'source_type' => 'path'
    );
}

function main_skin_render_font_faces() {
    $fonts = get_main_fonts();
    if (empty($fonts)) return '';
    $css = '';
    foreach ($fonts as $font) {
        if (empty($font['name'])) continue;

        /* code 타입: @font-face 또는 @import 코드 그대로 출력 */
        if (isset($font['source_type']) && $font['source_type'] === 'code' && !empty($font['css_code'])) {
            $css .= $font['css_code'] . "\n";
            continue;
        }

        /* 파일 타입 */
        if (empty($font['file'])) continue;
        $name = main_skin_esc($font['name']);
        $url  = main_skin_esc($font['file']);
        $fmt  = isset($font['format']) ? $font['format'] : 'woff2';
        $css .= "@font-face{font-family:'" . $name . "';src:url('" . $url . "') format('" . $fmt . "');font-display:swap;}\n";
    }
    return $css;
}

/* 폰트 preload <link> 태그 생성 (파일 타입만) */
function main_skin_render_font_preloads() {
    $fonts = get_main_fonts();
    if (empty($fonts)) return '';

    /* 실제로 config에서 사용 중인 폰트만 preload */
    $config = get_main_skin_config();
    $used_fonts = array();
    foreach (main_skin_text_keys() as $tk) {
        $f = isset($config['text_' . $tk . '_font']) ? $config['text_' . $tk . '_font'] : '';
        if ($f !== '') $used_fonts[$f] = true;
    }
    foreach (array_keys(main_skin_date_widget_parts()) as $p) {
        $f = isset($config['date_widget_' . $p . '_font']) ? $config['date_widget_' . $p . '_font'] : '';
        if ($f !== '') $used_fonts[$f] = true;
    }

    $html = '';
    foreach ($fonts as $font) {
        if (empty($font['name']) || empty($font['file'])) continue;
        if (!isset($font['source_type']) || $font['source_type'] !== 'file') continue;
        if (!isset($used_fonts[$font['name']])) continue;

        $ext = strtolower(pathinfo($font['file'], PATHINFO_EXTENSION));
        $type_map = array('woff2' => 'font/woff2', 'woff' => 'font/woff', 'ttf' => 'font/ttf', 'otf' => 'font/otf');
        $type = isset($type_map[$ext]) ? $type_map[$ext] : 'font/woff2';

        $html .= '<link rel="preload" href="' . main_skin_esc($font['file']) . '" as="font" type="' . $type . '" crossorigin>' . "\n";
    }
    return $html;
}

function main_skin_font_options() {
    $system = array(
        '' => '기본 폰트',
        'VT323' => 'VT323 (레트로)',
        'Arial' => 'Arial',
        'Georgia' => 'Georgia',
        'Times New Roman' => 'Times New Roman',
        'Courier New' => 'Courier New',
        'Verdana' => 'Verdana',
    );
    $custom = get_main_fonts();
    foreach ($custom as $font) {
        if (!empty($font['name'])) {
            $system[$font['name']] = $font['name'] . ' (커스텀)';
        }
    }
    return $system;
}

/* ══════════════════════════════════════════════
   배너
══════════════════════════════════════════════ */
function main_skin_default_banner() {
    return array('image' => '', 'link' => '', 'target' => '_blank', 'alt' => '', 'enabled' => 1, 'sort' => 0);
}

function main_skin_normalize_banner($banner) {
    $normalized = array_replace(main_skin_default_banner(), is_array($banner) ? $banner : array());
    if (isset($normalized['src']) && empty($normalized['image'])) $normalized['image'] = $normalized['src'];
    if (isset($normalized['href']) && empty($normalized['link'])) $normalized['link'] = $normalized['href'];
    $normalized['image'] = main_skin_image_url($normalized['image']);
    $normalized['link'] = main_skin_sanitize_link($normalized['link']);
    $normalized['target'] = ($normalized['target'] === '_self') ? '_self' : '_blank';
    $normalized['alt'] = main_skin_limit_text(isset($normalized['alt']) ? $normalized['alt'] : '', 100);
    $normalized['enabled'] = empty($normalized['enabled']) ? 0 : 1;
    $normalized['sort'] = (int)$normalized['sort'];
    return $normalized;
}

function get_main_banners() {
    $file = main_skin_storage_root_path() . '/banners.json';
    if (file_exists($file)) {
        $banners = json_decode(file_get_contents($file), true);
    } else {
        $config = get_main_skin_config();
        $banners = isset($config['retro_window']['banners']) ? $config['retro_window']['banners'] : array();
    }
    if (!is_array($banners)) return array();
    $normalized = array();
    foreach ($banners as $banner) {
        $banner = main_skin_normalize_banner($banner);
        if (!empty($banner['image'])) $normalized[] = $banner;
    }
    usort($normalized, 'main_skin_compare_banner');
    return $normalized;
}

function main_skin_compare_banner($left, $right) {
    $l = isset($left['sort']) ? (int)$left['sort'] : 0;
    $r = isset($right['sort']) ? (int)$right['sort'] : 0;
    return ($l === $r) ? 0 : (($l < $r) ? -1 : 1);
}

function save_main_banners($banners) {
    main_skin_ensure_storage();
    $normalized = array();
    foreach ((array)$banners as $banner) {
        $banner = main_skin_normalize_banner($banner);
        if (!empty($banner['image'])) $normalized[] = $banner;
    }
    usort($normalized, 'main_skin_compare_banner');
    return file_put_contents(
        main_skin_storage_root_path() . '/banners.json',
        json_encode(array_values($normalized), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

/* ══════════════════════════════════════════════
   스티커
══════════════════════════════════════════════ */
function main_skin_default_sticker() {
    return array('id' => '', 'source_type' => 'url', 'image' => '', 'left' => '100px', 'top' => '100px',
        'width' => '160px', 'height' => 'auto', 'rotate' => '0', 'z_index' => 20, 'enabled' => 1, 'alt' => '');
}

function main_skin_normalize_length($value, $default, $allow_auto) {
    $value = trim((string)$value);
    if ($value === '') return $default;
    if ($allow_auto && strtolower($value) === 'auto') return 'auto';
    if (preg_match('/^-?\d+(?:\.\d+)?$/', $value)) return $value . 'px';
    if (preg_match('/^-?\d+(?:\.\d+)?(?:px|%)$/i', $value)) return strtolower($value);
    return $default;
}

function main_skin_length_to_number($value, $default) {
    if (preg_match('/-?\d+(?:\.\d+)?/', (string)$value, $match)) return (int)round((float)$match[0]);
    return (int)$default;
}

function main_skin_normalize_sticker($sticker) {
    $normalized = array_replace(main_skin_default_sticker(), is_array($sticker) ? $sticker : array());
    if (isset($normalized['src']) && empty($normalized['image'])) $normalized['image'] = $normalized['src'];
    if (isset($normalized['src_type']) && empty($normalized['source_type'])) $normalized['source_type'] = $normalized['src_type'];
    $normalized['image'] = main_skin_image_url($normalized['image']);
    $normalized['source_type'] = ($normalized['source_type'] === 'file' || $normalized['source_type'] === 'upload') ? 'file' : 'url';
    $normalized['left'] = main_skin_normalize_length($normalized['left'], '100px', false);
    $normalized['top'] = main_skin_normalize_length($normalized['top'], '100px', false);
    $normalized['width'] = main_skin_normalize_length($normalized['width'], '160px', true);
    $normalized['height'] = main_skin_normalize_length($normalized['height'], 'auto', true);
    $normalized['rotate'] = (string)round((float)$normalized['rotate'], 2);
    $normalized['z_index'] = (int)$normalized['z_index'];
    $normalized['enabled'] = empty($normalized['enabled']) ? 0 : 1;
    $normalized['alt'] = main_skin_limit_text(isset($normalized['alt']) ? $normalized['alt'] : '', 100);
    if (empty($normalized['id'])) $normalized['id'] = 'sticker_' . main_skin_generate_id();
    return $normalized;
}

function get_main_stickers() {
    $file = main_skin_storage_root_path() . '/stickers.json';
    if (!file_exists($file)) return array();
    $stickers = json_decode(file_get_contents($file), true);
    if (!is_array($stickers)) return array();
    $normalized = array();
    foreach ($stickers as $sticker) {
        $sticker = main_skin_normalize_sticker($sticker);
        if (!empty($sticker['image'])) $normalized[] = $sticker;
    }
    return $normalized;
}

function save_main_stickers($stickers) {
    main_skin_ensure_storage();
    $normalized = array();
    foreach ((array)$stickers as $sticker) {
        $sticker = main_skin_normalize_sticker($sticker);
        if (!empty($sticker['image'])) $normalized[] = $sticker;
    }
    return file_put_contents(
        main_skin_storage_root_path() . '/stickers.json',
        json_encode(array_values($normalized), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

/* ══════════════════════════════════════════════
   에셋
══════════════════════════════════════════════ */
function main_skin_default_asset() {
    return array('id' => '', 'image' => '', 'alt' => '', 'source_type' => 'url');
}

function main_skin_normalize_asset($asset) {
    $normalized = array_replace(main_skin_default_asset(), is_array($asset) ? $asset : array());
    $normalized['image'] = main_skin_image_url($normalized['image']);
    $normalized['alt'] = main_skin_limit_text(isset($normalized['alt']) ? $normalized['alt'] : '', 100);
    $normalized['source_type'] = ($normalized['source_type'] === 'file' || $normalized['source_type'] === 'upload') ? 'file' : 'url';
    if (empty($normalized['id'])) $normalized['id'] = 'asset_' . main_skin_generate_id();
    return $normalized;
}

function get_main_assets() {
    $file = main_skin_storage_root_path() . '/assets.json';
    if (!file_exists($file)) return array();
    $assets = json_decode(file_get_contents($file), true);
    if (!is_array($assets)) return array();
    $normalized = array();
    foreach ($assets as $asset) {
        $asset = main_skin_normalize_asset($asset);
        if (!empty($asset['image'])) $normalized[] = $asset;
    }
    return $normalized;
}

function save_main_assets($assets) {
    main_skin_ensure_storage();
    $normalized = array();
    foreach ((array)$assets as $asset) {
        $asset = main_skin_normalize_asset($asset);
        if (!empty($asset['image'])) $normalized[] = $asset;
    }
    return file_put_contents(
        main_skin_storage_root_path() . '/assets.json',
        json_encode(array_values($normalized), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

/* ══════════════════════════════════════════════
   이미지 최적화
══════════════════════════════════════════════ */

function main_skin_no_resize_types() {
    return array('parallax', 'background');
}

function main_skin_supports_webp() {
    if (!function_exists('imagewebp')) return false;
    if (!function_exists('gd_info'))   return true;
    $info = gd_info();
    return !empty($info['WebP Support']);
}

function main_skin_is_animated_gif($filepath) {
    $fh = fopen($filepath, 'rb');
    if (!$fh) return false;
    $count = 0;
    while (!feof($fh) && $count < 2) {
        $chunk = fread($fh, 1024 * 100);
        $count += preg_match_all('/\x00\x21\xF9/', $chunk);
    }
    fclose($fh);
    return $count > 1;
}

function main_skin_gd_load($filepath, $ext) {
    switch ($ext) {
        case 'png':  return function_exists('imagecreatefrompng')  ? @imagecreatefrompng($filepath)  : false;
        case 'jpg':
        case 'jpeg': return function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($filepath) : false;
        case 'webp': return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filepath) : false;
        case 'bmp':  return function_exists('imagecreatefrombmp')  ? @imagecreatefrombmp($filepath)  : false;
    }
    return false;
}

function main_skin_gd_save($img, $filepath, $ext) {
    switch ($ext) {
        case 'png':
            imagesavealpha($img, true);
            return imagepng($img, $filepath, MAIN_SKIN_PNG_COMPRESSION);
        case 'jpg':
        case 'jpeg':
            return imagejpeg($img, $filepath, MAIN_SKIN_JPEG_QUALITY);
        case 'webp':
            return function_exists('imagewebp') ? imagewebp($img, $filepath, MAIN_SKIN_WEBP_QUALITY) : false;
    }
    return false;
}

function main_skin_resize_if_needed($filepath, $ext) {
    $max_width = (int)MAIN_SKIN_MAX_IMAGE_WIDTH;
    if ($max_width <= 0) return;
    if ($ext === 'gif' || $ext === 'mp4') return;

    $info = @getimagesize($filepath);
    if ($info === false || $info[0] <= $max_width) return;

    $orig_w = $info[0];
    $orig_h = $info[1];
    $new_w  = $max_width;
    $new_h  = (int)round($orig_h * ($max_width / $orig_w));

    $src = main_skin_gd_load($filepath, $ext);
    if (!$src) return;

    $dst = imagecreatetruecolor($new_w, $new_h);

    if ($ext === 'png' || $ext === 'webp') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $orig_w, $orig_h);
    main_skin_gd_save($dst, $filepath, $ext);

    imagedestroy($src);
    imagedestroy($dst);
}

function main_skin_try_convert_to_webp($filepath, $ext) {
    if (!in_array($ext, array('png', 'jpg', 'jpeg'))) return $filepath;
    if (!main_skin_supports_webp()) return $filepath;

    $src = main_skin_gd_load($filepath, $ext);
    if ($src === false) return $filepath;

    /* PNG 투명도 보존 */
    if ($ext === 'png') {
        imagesavealpha($src, true);
    }

    $webp_path = preg_replace('/\.(png|jpe?g)$/i', '.webp', $filepath);

    if (imagewebp($src, $webp_path, MAIN_SKIN_WEBP_QUALITY)) {
        imagedestroy($src);
        /* WebP가 더 작을 때만 교체 */
        if (filesize($webp_path) < filesize($filepath)) {
            @unlink($filepath);
            return $webp_path;
        } else {
            @unlink($webp_path);
            return $filepath;
        }
    }

    imagedestroy($src);
    return $filepath;
}
function main_skin_compress_jpeg($filepath) {
    if (!function_exists('imagecreatefromjpeg')) return;
    $src = @imagecreatefromjpeg($filepath);
    if ($src === false) return;
    imagejpeg($src, $filepath, MAIN_SKIN_JPEG_QUALITY);
    imagedestroy($src);
}

function main_skin_optimize_uploaded_image($filepath, $ext, $type = '') {
    if ($ext === 'gif') {
        if (filesize($filepath) > MAIN_SKIN_MAX_GIF_SIZE) {
            @unlink($filepath);
            return false;
        }
        return $filepath;
    }

    if ($ext === 'mp4') {
        return $filepath;
    }

    if (!in_array($type, main_skin_no_resize_types())) {
        main_skin_resize_if_needed($filepath, $ext);
    }

    if ($ext === 'jpg' || $ext === 'jpeg') {
        main_skin_compress_jpeg($filepath);
    }

    $filepath = main_skin_try_convert_to_webp($filepath, $ext);

    return $filepath;
}

/* ══════════════════════════════════════════════
   WebP 파일 헤더 검증 (PHP 5.6 finfo 미지원 대응)
══════════════════════════════════════════════ */
function main_skin_is_valid_webp($filepath) {
    $fh = @fopen($filepath, 'rb');
    if (!$fh) return false;
    $header = fread($fh, 12);
    fclose($fh);
    if (strlen($header) < 12) return false;
    /* WebP 파일은 RIFF....WEBP 헤더를 가짐 */
    return (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP');
}

/* ══════════════════════════════════════════════
   유틸리티
══════════════════════════════════════════════ */
function main_skin_limit_text($value, $length) {
    $value = trim(strip_tags((string)$value));
    if ($value === '') return '';
    if (function_exists('mb_substr')) return mb_substr($value, 0, $length, 'UTF-8');
    if (preg_match_all('/./us', $value, $chars) && isset($chars[0])) return implode('', array_slice($chars[0], 0, $length));
    return substr($value, 0, $length);
}

function main_skin_limit_text_raw($value, $length) {
    $value = trim((string)$value);
    if ($value === '') return '';
    if (function_exists('mb_substr')) return mb_substr($value, 0, $length, 'UTF-8');
    return substr($value, 0, $length);
}

function main_skin_sanitize_link($value) {
    $value = trim((string)$value);
    if ($value === '') return '';
    if (preg_match('/^(https?:)?\/\//i', $value)) return $value;
    if ($value[0] === '/') return $value;
    return '';
}

function main_skin_image_url($path_or_url) {
    $path_or_url = trim((string)$path_or_url);
    if ($path_or_url === '') return '';
    if (preg_match('/^(https?:)?\/\//i', $path_or_url)) return $path_or_url;
    if ($path_or_url[0] === '/' || strpos($path_or_url, './') === 0 || strpos($path_or_url, '../') === 0) return $path_or_url;
    return $path_or_url;
}

function main_skin_is_video($url) {
    $ext = strtolower(pathinfo(trim((string)$url), PATHINFO_EXTENSION));
    return ($ext === 'mp4');
}

function main_skin_generate_id() {
    if (function_exists('random_bytes')) return bin2hex(random_bytes(8));
    $strong = false;
    $bytes = function_exists('openssl_random_pseudo_bytes') ? openssl_random_pseudo_bytes(8, $strong) : false;
    if ($bytes !== false && $strong) return bin2hex($bytes);
    return substr(md5(uniqid('', true) . mt_rand()), 0, 16);
}

function main_skin_upload_image($file_arr, $type, $prefix) {
    if (!isset($file_arr['error']) || $file_arr['error'] !== UPLOAD_ERR_OK) return false;

    if ($file_arr['size'] > MAIN_SKIN_MAX_UPLOAD_SIZE) return false;

    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'mp4');
    $ext = strtolower(pathinfo($file_arr['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;

    if ($ext === 'gif' && $file_arr['size'] > MAIN_SKIN_MAX_GIF_SIZE) return false;

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $file_arr['tmp_name']);
            finfo_close($finfo);
            $allowed_mimes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/x-ms-bmp', 'image/x-bmp', 'video/mp4');
            if (!in_array($mime, $allowed_mimes)) {
                /*
                 * PHP 5.6의 libmagic이 WebP를 인식하지 못하면
                 * application/octet-stream 등으로 반환될 수 있으므로,
                 * 확장자가 webp인 경우 파일 헤더(RIFF...WEBP)를 직접 검증하여 허용한다.
                 */
                if ($ext === 'webp' && main_skin_is_valid_webp($file_arr['tmp_name'])) {
                    /* WebP 헤더 확인됨 — 통과 */
                } else {
                    return false;
                }
            }
        }
    }

    main_skin_ensure_storage();
    $filename = $prefix . '_' . date('YmdHis') . '_' . main_skin_generate_id() . '.' . $ext;
    $destination = main_skin_asset_dir($type) . '/' . $filename;

    if (!move_uploaded_file($file_arr['tmp_name'], $destination)) return false;

    $optimized = main_skin_optimize_uploaded_image($destination, $ext, $type);
    if ($optimized === false) {
        return false;
    }

    $final_filename = basename($optimized);

    return main_skin_asset_url($type) . '/' . $final_filename;
}

function main_skin_save_base64_image($data_url, $type, $prefix) {
    if (empty($data_url) || strpos($data_url, 'data:image/') !== 0) return false;
    if (!preg_match('/^data:image\/(png|jpeg|gif|webp);base64,(.+)$/s', $data_url, $m)) return false;
    $ext_map = array('png' => 'png', 'jpeg' => 'jpg', 'gif' => 'gif', 'webp' => 'webp');
    $ext = isset($ext_map[$m[1]]) ? $ext_map[$m[1]] : 'png';
    $binary = base64_decode($m[2]);
    if ($binary === false) return false;

    if (strlen($binary) > MAIN_SKIN_MAX_UPLOAD_SIZE) return false;
    if ($ext === 'gif' && strlen($binary) > MAIN_SKIN_MAX_GIF_SIZE) return false;

    main_skin_ensure_storage();
    $filename = $prefix . '_' . date('YmdHis') . '_' . main_skin_generate_id() . '.' . $ext;
    $destination = main_skin_asset_dir($type) . '/' . $filename;
    if (file_put_contents($destination, $binary) === false) return false;

    $optimized = main_skin_optimize_uploaded_image($destination, $ext, $type);
    if ($optimized === false) {
        return false;
    }

    $final_filename = basename($optimized);
    return main_skin_asset_url($type) . '/' . $final_filename;
}

function main_skin_delete_uploaded_asset($src, $type) {
    $src = trim((string)$src);
    if ($src === '') return;
    $base_url = main_skin_asset_url($type);
    if (strpos($src, $base_url) !== 0) return;
    $file = main_skin_asset_dir($type) . '/' . basename($src);
    if (file_exists($file)) @unlink($file);
}

function main_skin_board_ids_from_config($config) {
    $board_text = isset($config['latest_boards']) ? $config['latest_boards'] : '';
    $parts = explode(',', $board_text);
    $board_ids = array();
    foreach ($parts as $part) {
        $part = preg_replace('/[^a-z0-9_]/', '', strtolower(trim($part)));
        if ($part !== '') $board_ids[] = $part;
    }
    return empty($board_ids) ? array('free') : array_values(array_unique($board_ids));
}

function main_skin_normalize_board_ids_text($board_text) {
    return implode(',', main_skin_board_ids_from_config(array('latest_boards' => $board_text)));
}

function main_skin_get_latest_posts($bo_table, $limit) {
    $bo_table = preg_replace('/[^a-z0-9_]/', '', strtolower($bo_table));
    if ($bo_table === '') return array();
    $limit = max(1, min(20, (int)$limit));
    $prefix = defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_';
    $table = $prefix . 'write_' . $bo_table;
    $check = sql_query("SHOW TABLES LIKE '" . addslashes($table) . "'", false);
    if (!$check || sql_num_rows($check) === 0) return array();
    $result = sql_query("SELECT wr_id, wr_subject, wr_datetime, wr_name FROM `" . $table . "` WHERE wr_is_comment = 0 ORDER BY wr_datetime DESC LIMIT " . $limit, false);
    $rows = array();
    if ($result) {
        while ($row = sql_fetch_array($result)) {
            $row['bo_table'] = $bo_table;
            $rows[] = $row;
        }
    }
    return $rows;
}

function main_skin_get_board_name($bo_table) {
    $bo_table = preg_replace('/[^a-z0-9_]/', '', strtolower($bo_table));
    if ($bo_table === '') return '';
    $prefix = defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_';
    $row = sql_fetch("SELECT bo_subject FROM `" . $prefix . "board` WHERE bo_table = '" . addslashes($bo_table) . "'", false);
    return (!empty($row['bo_subject'])) ? $row['bo_subject'] : $bo_table;
}

function main_skin_cut_str($str, $len, $suffix) {
    if (function_exists('cut_str')) return cut_str($str, $len, $suffix);
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($str, 'UTF-8') <= $len) return $str;
        return mb_substr($str, 0, $len, 'UTF-8') . $suffix;
    }
    if (strlen($str) <= $len) return $str;
    return substr($str, 0, $len) . $suffix;
}

function render_main_latest($config) {
    $board_ids = main_skin_board_ids_from_config($config);
    $limit = isset($config['latest_rows']) ? (int)$config['latest_rows'] : 8;
    $bbs_url = defined('G5_BBS_URL') ? G5_BBS_URL : (defined('G5_URL') ? G5_URL . '/bbs' : '/bbs');

    $board_names = array();
    foreach ($board_ids as $bo_table) {
        $board_names[$bo_table] = main_skin_get_board_name($bo_table);
    }

    $all_posts = array();
    foreach ($board_ids as $bo_table) {
        $posts = main_skin_get_latest_posts($bo_table, $limit);
        foreach ($posts as $post) $all_posts[] = $post;
    }
    usort($all_posts, 'main_skin_compare_post_datetime');
    $all_posts = array_slice($all_posts, 0, $limit);

    ob_start();
    if (empty($all_posts)) {
        echo '<p class="win95-no-posts win95-no-posts-empty">게시판 설정을 확인해 주세요.</p>';
    } else { ?>
    <ul class="win95-post-list">
      <?php foreach ($all_posts as $post) {
          $bo_table = isset($post['bo_table']) ? $post['bo_table'] : '';
          $post_url = $bbs_url . '/board.php?bo_table=' . urlencode($bo_table) . '&wr_id=' . (int)$post['wr_id'];
          $subject = trim(strip_tags($post['wr_subject']));
          if ($subject === '' || $subject === null) $subject = '제목 없음';
          $subject = main_skin_cut_str($subject, 24, '…');
          $date = !empty($post['wr_datetime']) ? date('Y.m.d H:i', strtotime($post['wr_datetime'])) : '';
          $name = isset($board_names[$bo_table]) ? $board_names[$bo_table] : $bo_table;
      ?>
      <li class="win95-post-item">
        <a href="<?php echo main_skin_esc($post_url); ?>" class="win95-post-link">
          <div class="win95-post-front">
            <span class="win95-post-subject"><?php echo main_skin_esc($subject); ?></span>
            <span class="win95-post-date"><?php echo main_skin_esc($date); ?></span>
          </div>
          <div class="win95-post-back">
            <span class="win95-post-name"><?php echo main_skin_esc($name); ?></span>
          </div>
        </a>
      </li>
      <?php } ?>
    </ul>
    <?php }
    return ob_get_clean();
}

function main_skin_compare_post_datetime($a, $b) {
    $a_time = isset($a['wr_datetime']) ? $a['wr_datetime'] : '';
    $b_time = isset($b['wr_datetime']) ? $b['wr_datetime'] : '';
    if ($a_time === $b_time) return 0;
    return ($a_time > $b_time) ? -1 : 1;
}

function main_skin_parallax_img_style($config, $layer_prefix) {
    $pos_v = isset($config[$layer_prefix . '_pos_v']) ? $config[$layer_prefix . '_pos_v'] : 'center';
    $pos_h = isset($config[$layer_prefix . '_pos_h']) ? $config[$layer_prefix . '_pos_h'] : 'center';
    $offset_x = isset($config[$layer_prefix . '_offset_x']) ? (int)$config[$layer_prefix . '_offset_x'] : 0;
    $offset_y = isset($config[$layer_prefix . '_offset_y']) ? (int)$config[$layer_prefix . '_offset_y'] : 0;
    $styles = array();
    switch ($pos_v) {
        case 'top': $styles[] = 'top:0'; break;
        case 'bottom': $styles[] = 'bottom:0'; break;
        default: $styles[] = 'top:50%'; break;
    }
    switch ($pos_h) {
        case 'left': $styles[] = 'left:0'; break;
        case 'right': $styles[] = 'right:0'; break;
        default: $styles[] = 'left:50%'; break;
    }
    $transforms = array();
    if ($pos_h === 'center') $transforms[] = 'translateX(-50%)';
    if ($pos_v === 'center') $transforms[] = 'translateY(-50%)';
    if ($offset_x !== 0 || $offset_y !== 0) $transforms[] = 'translate(' . $offset_x . 'px,' . $offset_y . 'px)';
    if (!empty($transforms)) $styles[] = 'transform:' . implode(' ', $transforms);
    return implode(';', $styles);
}

function main_skin_is_admin() {
    global $is_admin, $member, $config, $g5;
    if (isset($is_admin) && (string)$is_admin === 'super') return true;
    if (!empty($is_admin)) return true;
    if (function_exists('is_admin')) {
        $chk = is_admin('super');
        if ($chk) return true;
    }
    if (!empty($member['mb_id'])) {
        if (!empty($config['cf_admin']) && $member['mb_id'] === $config['cf_admin']) return true;
        if ($member['mb_id'] === 'admin') return true;
        if (isset($member['mb_level']) && (int)$member['mb_level'] >= 10) return true;
    }
    if (isset($_SESSION['ss_mb_id']) && $_SESSION['ss_mb_id'] === 'admin') return true;
    return false;
}

function main_skin_esc($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function main_skin_get_token() {
    if (function_exists('get_token')) return get_token();
    if (!isset($_SESSION['main_skin_token'])) $_SESSION['main_skin_token'] = main_skin_generate_id() . main_skin_generate_id();
    return $_SESSION['main_skin_token'];
}

function main_skin_check_token($token) {
    if (function_exists('check_token')) { check_token(); return true; }
    if (!isset($_SESSION['main_skin_token'])) return false;
    return hash_equals($_SESSION['main_skin_token'], (string)$token);
}

function main_skin_json_response($data) {
    while (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function main_skin_json_ok($extra = array()) {
    main_skin_json_response(array_merge(array('ok' => true), $extra));
}

function main_skin_json_error($message) {
    main_skin_json_response(array('ok' => false, 'error' => $message));
}

function main_skin_strip_magic_quotes() {
    $_POST = array_map('main_skin_strip_slashes_deep', $_POST);
}

function main_skin_strip_slashes_deep($value) {
    if (is_array($value)) {
        return array_map('main_skin_strip_slashes_deep', $value);
    }
    return stripslashes($value);
}

/* ── 레거시 호환 별칭 ── */
function retro_main_get_config() { return get_main_skin_config(); }
function retro_main_save_config($config) { return save_main_skin_config($config); }
function retro_main_get_stickers() { return get_main_stickers(); }
function retro_main_save_stickers($stickers) { return save_main_stickers($stickers); }
function retro_main_get_latest($bo_table, $limit) { return main_skin_get_latest_posts($bo_table, $limit); }
function retro_main_get_board_name($bo_table) { return main_skin_get_board_name($bo_table); }
function retro_main_cut_str($str, $len = 25, $suffix = '…') { return main_skin_cut_str($str, $len, $suffix); }
function retro_main_is_admin() { return main_skin_is_admin(); }
function retro_main_esc($value) { return main_skin_esc($value); }
function retro_main_get_token() { return main_skin_get_token(); }
function retro_main_check_token($token) { return main_skin_check_token($token); }
function retro_main_data_dir() { return main_skin_storage_root_path(); }
function retro_main_data_url() { return main_skin_storage_root_url(); }
function retro_main_ensure_data_dir() { return main_skin_ensure_storage(); }
function retro_main_upload_image($file_arr, $prefix = 'img') {
    $map = array(
        'visual'  => 'visual',
        'banner'  => 'banner',
        'sticker' => 'sticker',
        'img'     => 'visual',
        'bg'      => 'background'
    );
    $type = isset($map[$prefix]) ? $map[$prefix] : 'visual';
    return main_skin_upload_image($file_arr, $type, $prefix);
}
