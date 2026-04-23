<?php
$g5_path = dirname(dirname(dirname(dirname(__FILE__))));
if (!defined('_GNUBOARD_')) define('_GNUBOARD_', true);
@ob_start();
include_once($g5_path . '/common.php');
while (ob_get_level()) ob_end_clean();
include_once(dirname(__FILE__) . '/main.lib.php');

if (!main_skin_is_admin()) {
    main_skin_json_error('권한이 없습니다.');
}

$token = isset($_POST['token']) ? $_POST['token'] : '';
if (!main_skin_check_token($token)) {
    main_skin_json_error('보안 토큰이 유효하지 않습니다.');
}

main_skin_strip_magic_quotes();

$action = isset($_POST['action']) ? $_POST['action'] : '';
$config = get_main_skin_config();

function main_skin_resolve_image($file_key, $url_key, $type, $prefix, $current_value) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $uploaded = main_skin_upload_image($_FILES[$file_key], $type, $prefix);
        if ($uploaded !== false) {
            return $uploaded;
        }
    }
    if (!empty($_POST[$url_key])) {
        return main_skin_image_url($_POST[$url_key]);
    }
    return $current_value;
}

switch ($action) {

    /* ══════════════════════════════════════════════
       배경 이미지
    ══════════════════════════════════════════════ */
    case 'update_bg':
        $old_image = isset($config['bg_image']) ? $config['bg_image'] : '';
        $old_src   = isset($config['bg_image_source_type']) ? $config['bg_image_source_type'] : 'url';
        $new_image = $old_image;
        $new_src   = $old_src;

        $cropped = isset($_POST['bg_cropped_data']) ? trim($_POST['bg_cropped_data']) : '';
        if (!empty($cropped) && strpos($cropped, 'data:image/') === 0) {
            $saved = main_skin_save_base64_image($cropped, 'background', 'bg_crop');
            if ($saved !== false) {
                if (!empty($old_image) && $old_src === 'file') {
                    main_skin_delete_uploaded_asset($old_image, 'background');
                }
                $new_image = $saved;
                $new_src   = 'file';
            }
        } else {
            if (isset($_FILES['bg_file']) && $_FILES['bg_file']['error'] === UPLOAD_ERR_OK) {
                $uploaded = main_skin_upload_image($_FILES['bg_file'], 'background', 'bg');
                if ($uploaded !== false) {
                    if (!empty($old_image) && $old_src === 'file') {
                        main_skin_delete_uploaded_asset($old_image, 'background');
                    }
                    $new_image = $uploaded;
                    $new_src   = 'file';
                }
            } elseif (isset($_POST['bg_url'])) {
                $url = main_skin_image_url(trim($_POST['bg_url']));
                if ($url !== $old_image) {
                    if (!empty($old_image) && $old_src === 'file') {
                        main_skin_delete_uploaded_asset($old_image, 'background');
                    }
                    $new_image = $url;
                    $new_src   = 'url';
                }
            }
        }

        $config['bg_image']             = $new_image;
        $config['bg_image_source_type'] = $new_src;

        $valid_fit = array('cover', 'contain', 'original');
        $fit = isset($_POST['bg_fit']) ? $_POST['bg_fit'] : 'cover';
        $config['bg_fit'] = in_array($fit, $valid_fit) ? $fit : 'cover';

        if (!save_main_skin_config($config)) {
            main_skin_json_error('배경 이미지 저장에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    case 'delete_bg':
        if (!empty($config['bg_image']) && isset($config['bg_image_source_type']) && $config['bg_image_source_type'] === 'file') {
            main_skin_delete_uploaded_asset($config['bg_image'], 'background');
        }
        $config['bg_image']             = '';
        $config['bg_image_source_type'] = 'url';
        if (!save_main_skin_config($config)) {
            main_skin_json_error('배경 이미지 삭제에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    /* ══════════════════════════════════════════════
       텍스트 오버레이
    ══════════════════════════════════════════════ */
    case 'update_texts':
        $text_keys = main_skin_text_keys();
        $debug_received = array();
        $debug_saved    = array();

        foreach ($text_keys as $tk) {
            $prefix = 'text_' . $tk;

            if (isset($_POST[$prefix])) {
                $config[$prefix] = main_skin_limit_text_raw($_POST[$prefix], 500);
            }
            $debug_received[$prefix] = isset($_POST[$prefix]) ? $_POST[$prefix] : '(not in POST)';

            if (isset($_POST[$prefix . '_font'])) {
                $config[$prefix . '_font'] = main_skin_limit_text($_POST[$prefix . '_font'], 60);
            }
            if (isset($_POST[$prefix . '_size'])) {
                $config[$prefix . '_size'] = max(8, min(200, (int)$_POST[$prefix . '_size']));
            }
            if (isset($_POST[$prefix . '_spacing'])) {
                $config[$prefix . '_spacing'] = max(-20, min(50, (int)$_POST[$prefix . '_spacing']));
            }
            if (isset($_POST[$prefix . '_line_height'])) {
                $lh = trim($_POST[$prefix . '_line_height']);
                if (preg_match('/^\d+(\.\d+)?$/', $lh) || preg_match('/^\d+(\.\d+)?(px|em|%)$/', $lh)) {
                    $config[$prefix . '_line_height'] = $lh;
                }
            }
            if (isset($_POST[$prefix . '_color'])) {
                $c = trim($_POST[$prefix . '_color']);
                if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $c)) {
                    $config[$prefix . '_color'] = $c;
                }
            }
            if (isset($_POST[$prefix . '_top'])) {
                $config[$prefix . '_top'] = max(-500, min(2000, (int)$_POST[$prefix . '_top']));
            }
            if (isset($_POST[$prefix . '_left'])) {
                $config[$prefix . '_left'] = max(-500, min(2000, (int)$_POST[$prefix . '_left']));
            }
            $config[$prefix . '_bold']   = !empty($_POST[$prefix . '_bold']) ? 1 : 0;
            $config[$prefix . '_italic'] = !empty($_POST[$prefix . '_italic']) ? 1 : 0;

            if (!empty($_POST[$prefix . '_stroke_enabled'])) {
                if (isset($_POST[$prefix . '_stroke_color'])) {
                    $sc = trim($_POST[$prefix . '_stroke_color']);
                    if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $sc)) {
                        $config[$prefix . '_stroke_color'] = $sc;
                    }
                }
            }
            /* scaleY (수직 비율) */
            if (isset($_POST[$prefix . '_scale_y'])) {
                $config[$prefix . '_scale_y'] = max(10, min(500, (int)$_POST[$prefix . '_scale_y']));
            }

            /* 폰트 사이즈 단위 */
            if (isset($_POST[$prefix . '_size_unit'])) {
                $unit = trim($_POST[$prefix . '_size_unit']);
                $valid_units = array('px', 'em', 'rem', 'pt', 'vw', 'vh', 'cw%');
                $config[$prefix . '_size_unit'] = in_array($unit, $valid_units) ? $unit : 'px';
            }

            /* 폰트 사이즈: 단위가 px이 아닌 경우 소수점 허용 */
            if (isset($_POST[$prefix . '_size'])) {
                $raw_size = trim($_POST[$prefix . '_size']);
                $unit_now = isset($config[$prefix . '_size_unit']) ? $config[$prefix . '_size_unit'] : 'px';
                if ($unit_now === 'px') {
                    $config[$prefix . '_size'] = max(8, min(200, (int)$raw_size));
                } elseif ($unit_now === 'cw%') {
                    $config[$prefix . '_size'] = max(1, min(200, (int)$raw_size));
                } else {
                    /* em, rem, vw 등은 소수점 허용 */
                    if (preg_match('/^\d+(\.\d+)?$/', $raw_size)) {
                        $config[$prefix . '_size'] = $raw_size;
                    }
                }
            } else {
                $config[$prefix . '_stroke_color'] = '';
            }
            if (isset($_POST[$prefix . '_stroke_width'])) {
                $config[$prefix . '_stroke_width'] = max(0, min(10, (int)$_POST[$prefix . '_stroke_width']));
            }

            $debug_saved[$prefix] = isset($config[$prefix]) ? $config[$prefix] : '';
        }

        $save_path = main_skin_storage_root_path() . '/config.json';
        $dir_exists = is_dir(main_skin_storage_root_path());
        $dir_writable = $dir_exists ? is_writable(main_skin_storage_root_path()) : false;

        if (!save_main_skin_config($config)) {
            main_skin_json_error('텍스트 설정 저장에 실패했습니다. 경로: ' . $save_path . ', 디렉토리 존재: ' . ($dir_exists ? 'Y' : 'N') . ', 쓰기 가능: ' . ($dir_writable ? 'Y' : 'N'));
        }

        $verify_config = get_main_skin_config();
        $verify_texts = array();
        foreach ($text_keys as $tk) {
            $key = 'text_' . $tk;
            $verify_texts[$key] = isset($verify_config[$key]) ? $verify_config[$key] : '(missing)';
        }

        main_skin_json_ok(array(
            'debug_received' => $debug_received,
            'debug_saved'    => $debug_saved,
            'debug_verify'   => $verify_texts,
            'config_path'    => $save_path,
            'dir_writable'   => $dir_writable
        ));
        break;

    /* ══════════════════════════════════════════════
       커스텀 폰트
    ══════════════════════════════════════════════ */
    case 'add_font_code':
        $font_name = isset($_POST['font_name']) ? trim($_POST['font_name']) : '';
        if ($font_name === '') {
            main_skin_json_error('폰트 이름을 입력해 주세요.');
        }
        $font_code = isset($_POST['font_code']) ? trim($_POST['font_code']) : '';
        if ($font_code === '') {
            main_skin_json_error('CSS 코드를 입력해 주세요.');
        }
        $result = main_skin_add_font_by_code($font_name, $font_code);
        if ($result === false) {
            main_skin_json_error('유효하지 않은 코드입니다. @font-face, @import, 또는 <link> 태그를 입력해 주세요.');
        }
        $fonts = get_main_fonts();
        $fonts[] = $result;
        if (!save_main_fonts($fonts)) {
            main_skin_json_error('폰트 저장에 실패했습니다.');
        }
        main_skin_json_ok(array('font' => $result));
        break;

    case 'upload_font':
        $font_name = isset($_POST['font_name']) ? trim($_POST['font_name']) : '';
        if ($font_name === '') {
            main_skin_json_error('폰트 이름을 입력해 주세요.');
        }
        if (!isset($_FILES['font_file']) || $_FILES['font_file']['error'] !== UPLOAD_ERR_OK) {
            main_skin_json_error('폰트 파일을 선택해 주세요.');
        }
        $result = main_skin_upload_font($_FILES['font_file'], $font_name);
        if ($result === false) {
            main_skin_json_error('폰트 업로드에 실패했습니다. TTF/OTF/WOFF/WOFF2만 지원합니다.');
        }
        $fonts = get_main_fonts();
        $fonts[] = $result;
        if (!save_main_fonts($fonts)) {
            main_skin_json_error('폰트 저장에 실패했습니다.');
        }
        main_skin_json_ok(array('font' => $result));
        break;

    case 'add_font_path':
        $font_name = isset($_POST['font_name']) ? trim($_POST['font_name']) : '';
        if ($font_name === '') {
            main_skin_json_error('폰트 이름을 입력해 주세요.');
        }
        $font_path = isset($_POST['font_path']) ? trim($_POST['font_path']) : '';
        if ($font_path === '') {
            main_skin_json_error('서버 경로를 입력해 주세요.');
        }
        $result = main_skin_add_font_by_path($font_name, $font_path);
        if ($result === 'duplicate') {
            main_skin_json_error('이미 등록된 폰트 파일입니다.');
        }
        if ($result === false) {
            main_skin_json_error('유효하지 않은 경로이거나 지원하지 않는 형식입니다. (ttf/otf/woff/woff2만 지원, 웹 접근 가능한 경로만 허용)');
        }
        $fonts = get_main_fonts();
        $fonts[] = $result;
        if (!save_main_fonts($fonts)) {
            main_skin_json_error('폰트 저장에 실패했습니다.');
        }
        main_skin_json_ok(array('font' => $result));
        break;

    case 'delete_font':
        $font_id = isset($_POST['font_id']) ? trim($_POST['font_id']) : '';
        $fonts = get_main_fonts();
        $found = -1;
        foreach ($fonts as $i => $f) {
            if (isset($f['id']) && $f['id'] === $font_id) {
                $found = $i;
                break;
            }
        }
        if ($found < 0) {
            main_skin_json_error('해당 폰트를 찾을 수 없습니다.');
        }
        if (!empty($fonts[$found]['file']) && isset($fonts[$found]['source_type']) && $fonts[$found]['source_type'] === 'file') {
            main_skin_delete_uploaded_asset($fonts[$found]['file'], 'fonts');
        }
        array_splice($fonts, $found, 1);
        if (!save_main_fonts($fonts)) {
            main_skin_json_error('폰트 삭제에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    /* ══════════════════════════════════════════════
       윈도우 위치 저장
    ══════════════════════════════════════════════ */
    case 'save_window_pos':
        $win = isset($_POST['window']) ? $_POST['window'] : '';
        if ($win === 'latest') {
            $config['latest_win_top']  = max(-500, min(2000, (int)(isset($_POST['top'])  ? $_POST['top']  : 0)));
            $config['latest_win_left'] = max(-500, min(2000, (int)(isset($_POST['left']) ? $_POST['left'] : 0)));
        } elseif ($win === 'banner') {
            $config['banner_win_top']  = max(-500, min(2000, (int)(isset($_POST['top'])  ? $_POST['top']  : 0)));
            $config['banner_win_left'] = max(-500, min(2000, (int)(isset($_POST['left']) ? $_POST['left'] : 0)));
        } else {
            main_skin_json_error('유효하지 않은 윈도우입니다.');
        }
        if (!save_main_skin_config($config)) {
            main_skin_json_error('윈도우 위치 저장에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    /* ══════════════════════════════════════════════
       최신글/배너 창 설정 + 날짜 위젯 (파트별)
    ══════════════════════════════════════════════ */
    case 'update_window':
        $config['window_title'] = main_skin_limit_text(isset($_POST['win_title']) ? $_POST['win_title'] : '', 40);
        if ($config['window_title'] === '') $config['window_title'] = '최신글';

        $config['banner_title'] = main_skin_limit_text(isset($_POST['banner_title']) ? $_POST['banner_title'] : '', 40);
        if ($config['banner_title'] === '') $config['banner_title'] = '배너';

        $config['latest_rows']   = max(1, min(20, (int)(isset($_POST['limit']) ? $_POST['limit'] : 8)));
        $config['latest_boards'] = main_skin_normalize_board_ids_text(isset($_POST['board_ids']) ? $_POST['board_ids'] : 'free');

        /* 날짜 위젯 — 공통 */
        $config['date_widget_enabled'] = !empty($_POST['date_widget_enabled']) ? 1 : 0;
        $config['date_widget_top']   = max(-500, min(2000, (int)(isset($_POST['date_widget_top']) ? $_POST['date_widget_top'] : 20)));
        $config['date_widget_right'] = max(-500, min(2000, (int)(isset($_POST['date_widget_right']) ? $_POST['date_widget_right'] : 20)));

        /* 날짜 위젯 — 파트별 색상·폰트 */
        $dw_parts = array_keys(main_skin_date_widget_parts());
        foreach ($dw_parts as $dw_p) {
            $color_key = 'date_widget_' . $dw_p . '_color';
            $font_key  = 'date_widget_' . $dw_p . '_font';

            if (isset($_POST[$color_key])) {
                $c = trim($_POST[$color_key]);
                if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $c)) {
                    $config[$color_key] = $c;
                }
            }
            if (isset($_POST[$font_key])) {
                $config[$font_key] = main_skin_limit_text($_POST[$font_key], 60);
            }
        }

        /* 날짜 위젯 — 레거시 단일 색상/폰트도 첫 파트 값으로 동기화 (하위호환) */
        $config['date_widget_color'] = $config['date_widget_issue_color'];
        $config['date_widget_font']  = $config['date_widget_issue_font'];

        /* 날짜 위젯 — 테두리(stroke) 공통 */
        if (!empty($_POST['date_widget_stroke_enabled'])) {
            if (isset($_POST['date_widget_stroke_color'])) {
                $sc = trim($_POST['date_widget_stroke_color']);
                if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $sc)) {
                    $config['date_widget_stroke_color'] = $sc;
                }
            }
        } else {
            $config['date_widget_stroke_color'] = '';
        }
        $config['date_widget_stroke_width'] = max(0, min(10, (int)(isset($_POST['date_widget_stroke_width']) ? $_POST['date_widget_stroke_width'] : 0)));

        if (!save_main_skin_config($config)) {
            main_skin_json_error('창 설정 저장에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    /* ══════════════════════════════════════════════
       패럴랙스
    ══════════════════════════════════════════════ */
    case 'update_parallax':
        $layers  = array('fg', 'ng', 'bg');
        $valid_v = array('top', 'center', 'bottom');
        $valid_h = array('left', 'center', 'right');

        foreach ($layers as $layer) {
            $file_key       = 'parallax_' . $layer . '_file';
            $url_key        = 'parallax_' . $layer . '_url';
            $config_img_key = 'parallax_' . $layer . '_image';
            $config_src_key = 'parallax_' . $layer . '_source_type';

            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
                $uploaded = main_skin_upload_image($_FILES[$file_key], 'parallax', 'parallax_' . $layer);
                if ($uploaded !== false) {
                    if (!empty($config[$config_img_key]) && isset($config[$config_src_key]) && $config[$config_src_key] === 'file') {
                        main_skin_delete_uploaded_asset($config[$config_img_key], 'parallax');
                    }
                    $config[$config_img_key] = $uploaded;
                    $config[$config_src_key] = 'file';
                }
            } elseif (isset($_POST[$url_key])) {
                $url     = main_skin_image_url(trim($_POST[$url_key]));
                $current = isset($config[$config_img_key]) ? $config[$config_img_key] : '';
                if ($url !== $current) {
                    if (!empty($current) && isset($config[$config_src_key]) && $config[$config_src_key] === 'file') {
                        main_skin_delete_uploaded_asset($current, 'parallax');
                    }
                    $config[$config_img_key] = $url;
                    $config[$config_src_key] = 'url';
                }
            }

            $pos_v_key    = 'parallax_' . $layer . '_pos_v';
            $pos_h_key    = 'parallax_' . $layer . '_pos_h';
            $offset_x_key = 'parallax_' . $layer . '_offset_x';
            $offset_y_key = 'parallax_' . $layer . '_offset_y';

            if (isset($_POST[$pos_v_key])) {
                $pv = $_POST[$pos_v_key];
                $config[$pos_v_key] = in_array($pv, $valid_v) ? $pv : 'center';
            }
            if (isset($_POST[$pos_h_key])) {
                $ph = $_POST[$pos_h_key];
                $config[$pos_h_key] = in_array($ph, $valid_h) ? $ph : 'center';
            }
            if (isset($_POST[$offset_x_key])) {
                $config[$offset_x_key] = max(-2000, min(2000, (int)$_POST[$offset_x_key]));
            }
            if (isset($_POST[$offset_y_key])) {
                $config[$offset_y_key] = max(-2000, min(2000, (int)$_POST[$offset_y_key]));
            }
        }

        if (!save_main_skin_config($config)) {
            main_skin_json_error('패럴랙스 설정 저장에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    case 'delete_parallax_image':
        $layer        = isset($_POST['layer']) ? $_POST['layer'] : '';
        $valid_layers = array('fg', 'ng', 'bg');
        if (!in_array($layer, $valid_layers)) {
            main_skin_json_error('유효하지 않은 레이어입니다.');
        }
        $config_img_key = 'parallax_' . $layer . '_image';
        $config_src_key = 'parallax_' . $layer . '_source_type';
        if (!empty($config[$config_img_key]) && isset($config[$config_src_key]) && $config[$config_src_key] === 'file') {
            main_skin_delete_uploaded_asset($config[$config_img_key], 'parallax');
        }
        $config[$config_img_key] = '';
        $config[$config_src_key] = 'url';
        if (!save_main_skin_config($config)) {
            main_skin_json_error('이미지 삭제에 실패했습니다.');
        }
        main_skin_json_ok(array());
        break;

    default:
        main_skin_json_error('알 수 없는 액션입니다.');
}
