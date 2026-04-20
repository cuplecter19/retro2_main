<?php
if (!defined('_GNUBOARD_')) exit;
include_once(dirname(__FILE__) . '/main.lib.php');

$main_skin_config   = get_main_skin_config();
$main_skin_banners  = get_main_banners();
$main_skin_stickers = get_main_stickers();
$main_skin_assets   = get_main_assets();
$main_skin_is_admin = main_skin_is_admin();
$main_skin_token    = $main_skin_is_admin ? main_skin_get_token() : '';
$main_skin_latest_html = render_main_latest($main_skin_config);
$window_title = !empty($main_skin_config['window_title']) ? $main_skin_config['window_title'] : '최신글';
$banner_title = !empty($main_skin_config['banner_title']) ? $main_skin_config['banner_title'] : '배너';
$main_skin_fonts = get_main_fonts();
$font_options = main_skin_font_options();
$text_keys = main_skin_text_keys();
$text_labels = array('bg_title' => '배경 뒤 텍스트', 'title1' => 'Title 1', 'title2' => 'Title 2', 'title3' => 'Title 3', 'title3_body' => 'Title 3 본문', 'general' => '일반 텍스트');
$date_widget_parts = main_skin_date_widget_parts();

/* 커스텀 폰트 @font-face */
$custom_font_css = main_skin_render_font_faces();
?>
<?php
/* 사용 중인 커스텀 폰트만 preload */
echo main_skin_render_font_preloads();
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preload" href="https://fonts.googleapis.com/css2?family=VT323&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=VT323&display=swap"></noscript>
<?php if ($custom_font_css) { ?>
<style><?php echo $custom_font_css; ?></style>
<?php } ?>
<link rel="stylesheet" href="<?php echo main_skin_esc(MAIN_SKIN_URL); ?>/main.css">

<?php if ($main_skin_is_admin) { ?>
<div id="retro-admin-buttons">
  <button type="button" id="retro-sticker-edit-btn" class="win95-window win95-action-btn" title="스티커 편집 모드 전환">✏️ 스티커 편집</button>
  <button type="button" id="retro-admin-open-btn" class="win95-window win95-action-btn" aria-label="스킨 관리 패널 열기" aria-haspopup="dialog" aria-controls="retro-admin-modal" aria-expanded="false">🔧 스킨 관리</button>
</div>
<?php } ?>

<div id="retro-main-wrapper">
  <?php if (!empty($main_skin_config['parallax_bg_image'])) { ?>
  <div id="parallax-bg-layer" class="parallax-layer parallax-global-layer parallax-bg-layer"
       data-pos-v="<?php echo main_skin_esc($main_skin_config['parallax_bg_pos_v']); ?>"
       data-pos-h="<?php echo main_skin_esc($main_skin_config['parallax_bg_pos_h']); ?>"
       data-offset-x="<?php echo (int)$main_skin_config['parallax_bg_offset_x']; ?>"
       data-offset-y="<?php echo (int)$main_skin_config['parallax_bg_offset_y']; ?>"
       aria-hidden="true">
    <img src="<?php echo main_skin_esc($main_skin_config['parallax_bg_image']); ?>" alt="" loading="lazy" decoding="async"
         style="<?php echo main_skin_parallax_img_style($main_skin_config, 'parallax_bg'); ?>">
  </div>
  <?php } ?>

  <div id="retro-sticker-overlay" aria-hidden="true">
    <?php foreach ($main_skin_stickers as $sticker) {
        if (empty($sticker['enabled']) || empty($sticker['image'])) continue;
        $is_pct = (strpos($sticker['left'], '%') !== false && strpos($sticker['top'], '%') !== false);
        $rotate_deg = main_skin_esc($sticker['rotate']);
        $transform_val = $is_pct ? 'translate(-50%,-50%) rotate(' . $rotate_deg . 'deg)' : 'rotate(' . $rotate_deg . 'deg)';
        $sticker_w = main_skin_esc($sticker['width']);
        $sticker_h = main_skin_esc($sticker['height']);
        $is_video = main_skin_is_video($sticker['image']);
    ?>
    <div class="retro-sticker<?php echo $main_skin_is_admin ? ' admin-sticker' : ''; ?>"
         id="sticker-<?php echo main_skin_esc($sticker['id']); ?>"
         data-id="<?php echo main_skin_esc($sticker['id']); ?>"
         data-rotate="<?php echo $rotate_deg; ?>"
         data-z-index="<?php echo (int)$sticker['z_index']; ?>"
         style="left:<?php echo main_skin_esc($sticker['left']); ?>;top:<?php echo main_skin_esc($sticker['top']); ?>;z-index:<?php echo (int)$sticker['z_index']; ?>;width:<?php echo $sticker_w; ?>;height:<?php echo $sticker_h; ?>;transform:<?php echo $transform_val; ?>;">
      <?php if ($is_video) { ?>
      <video autoplay loop muted playsinline style="width:100%;height:100%;object-fit:contain;pointer-events:none;">
        <source src="<?php echo main_skin_esc($sticker['image']); ?>" type="video/mp4">
      </video>
      <?php } else { ?>
      <img src="<?php echo main_skin_esc($sticker['image']); ?>" alt="<?php echo main_skin_esc($sticker['alt']); ?>" loading="lazy">
      <?php } ?>
      <?php if ($main_skin_is_admin) { ?>
      <div class="sticker-handles">
        <button type="button" class="sticker-zup-btn" data-id="<?php echo main_skin_esc($sticker['id']); ?>" title="z-index 올리기">▲</button>
        <button type="button" class="sticker-zdown-btn" data-id="<?php echo main_skin_esc($sticker['id']); ?>" title="z-index 내리기">▼</button>
        <button type="button" class="sticker-del-btn" data-id="<?php echo main_skin_esc($sticker['id']); ?>" title="스티커 삭제">×</button>
        <div class="sticker-rotate-handle" title="드래그하여 회전"></div>
        <div class="sticker-resize-handle" title="드래그하여 크기 조절"></div>
      </div>
      <?php } ?>
    </div>
    <?php } ?>
  </div>

  <!-- ═══ 배경 이미지 컨테이너 (700×850) ═══ -->
  <div id="retro-bg-container">

    <!-- ── 레이어 -1: 배경 뒤 텍스트 (bg_title) ── -->
    <?php
    $bt_content = isset($main_skin_config['text_bg_title']) ? $main_skin_config['text_bg_title'] : '';
    if ($bt_content !== '') {
        $bt_style = main_skin_responsive_text_style('bg_title', $main_skin_config);
        $bt_size_unit = isset($main_skin_config['text_bg_title_size_unit']) ? $main_skin_config['text_bg_title_size_unit'] : 'px';
        $bt_size_val  = isset($main_skin_config['text_bg_title_size']) ? $main_skin_config['text_bg_title_size'] : 48;
        $bt_scale_y   = isset($main_skin_config['text_bg_title_scale_y']) ? (int)$main_skin_config['text_bg_title_scale_y'] : 100;
    ?>
    <div class="retro-text-overlay retro-text-bg-title retro-text-behind-bg"
         style="<?php echo $bt_style; ?>"
         <?php if ($bt_size_unit === 'cw%') { ?>data-size-cw="<?php echo (int)$bt_size_val; ?>" data-text-key="bg_title"<?php } ?>
         <?php if ($bt_scale_y !== 100) { ?>data-scale-y="<?php echo $bt_scale_y; ?>"<?php } ?>
    ><?php echo nl2br(main_skin_esc($bt_content)); ?></div>
    <?php } ?>

    <!-- ── 레이어 0: 배경 미디어 ── -->
    <?php
    $bg_image = isset($main_skin_config['bg_image']) ? $main_skin_config['bg_image'] : '';
    $bg_fit   = isset($main_skin_config['bg_fit']) ? $main_skin_config['bg_fit'] : 'cover';
    $fit_class = 'bg-fit-' . $bg_fit;
    if (!empty($bg_image)) {
        if (main_skin_is_video($bg_image)) { ?>
    <video class="retro-bg-media <?php echo $fit_class; ?>" autoplay loop muted playsinline>
      <source src="<?php echo main_skin_esc($bg_image); ?>" type="video/mp4">
    </video>
    <?php } else { ?>
    <img class="retro-bg-media <?php echo $fit_class; ?>" src="<?php echo main_skin_esc($bg_image); ?>" alt="배경" loading="lazy">
    <?php }
    } ?>

    <!-- ── 레이어 5: 텍스트 오버레이 (반응형) ── -->
    <?php foreach ($text_keys as $tk) {
        if ($tk === 'bg_title') continue;
        $content = isset($main_skin_config['text_' . $tk]) ? $main_skin_config['text_' . $tk] : '';
        if ($content === '') continue;

        $style = main_skin_responsive_text_style($tk, $main_skin_config);
        $size_unit = isset($main_skin_config['text_' . $tk . '_size_unit']) ? $main_skin_config['text_' . $tk . '_size_unit'] : 'px';
        $size_val  = isset($main_skin_config['text_' . $tk . '_size']) ? $main_skin_config['text_' . $tk . '_size'] : 14;
        $scale_y   = isset($main_skin_config['text_' . $tk . '_scale_y']) ? (int)$main_skin_config['text_' . $tk . '_scale_y'] : 100;
    ?>
    <div class="retro-text-overlay retro-text-<?php echo $tk; ?>"
         style="<?php echo $style; ?>"
         <?php if ($size_unit === 'cw%') { ?>data-size-cw="<?php echo (int)$size_val; ?>" data-text-key="<?php echo $tk; ?>"<?php } ?>
         <?php if ($scale_y !== 100) { ?>data-scale-y="<?php echo $scale_y; ?>"<?php } ?>
    ><?php echo nl2br(main_skin_esc($content)); ?></div>
    <?php } ?>

    <!-- ── 레이어 6: 잡지 날짜 위젯 ── -->
    <?php if (!empty($main_skin_config['date_widget_enabled'])) {
        $dw_top   = (int)$main_skin_config['date_widget_top'];
        $dw_right = (int)$main_skin_config['date_widget_right'];
        $dw_stroke_css = main_skin_text_stroke_css(
            isset($main_skin_config['date_widget_stroke_color']) ? $main_skin_config['date_widget_stroke_color'] : '',
            isset($main_skin_config['date_widget_stroke_width']) ? $main_skin_config['date_widget_stroke_width'] : 0
        );

        $base_h = (int)MAIN_SKIN_BASE_HEIGHT;
        $base_w = (int)MAIN_SKIN_BASE_WIDTH;
        $dw_top_pct   = round($dw_top / $base_h * 100, 4);
        $dw_right_pct = round($dw_right / $base_w * 100, 4);

        $dw_style = 'top:' . $dw_top_pct . '%;right:' . $dw_right_pct . '%;';
        if ($dw_stroke_css !== '') $dw_style .= $dw_stroke_css;

        $dw_issue_style = main_skin_date_widget_part_style($main_skin_config, 'issue');
        $dw_pub_style   = main_skin_date_widget_part_style($main_skin_config, 'pub');
        $dw_clock_style = main_skin_date_widget_part_style($main_skin_config, 'clock');

        $now_year  = date('Y');
        $now_month = date('n');
        $now_day   = date('j');
    ?>
    <div id="retro-date-widget" class="retro-date-widget" style="<?php echo $dw_style; ?>">
      <div class="date-widget-issue" style="<?php echo $dw_issue_style; ?>"><?php echo $now_year; ?>년 <?php echo $now_month; ?>월호</div>
      <div class="date-widget-pub" style="<?php echo $dw_pub_style; ?>">발간일 <?php echo $now_month; ?>/<?php echo $now_day; ?></div>
      <div class="date-widget-clock" id="retro-live-clock" style="<?php echo $dw_clock_style; ?>"></div>
    </div>
    <?php } ?>

    <!-- ── 레이어 10: 최신글 윈도우 ── -->
    <div id="retro-latest-window" class="retro-draggable-window"
         style="top:<?php echo (int)$main_skin_config['latest_win_top']; ?>px;left:<?php echo (int)$main_skin_config['latest_win_left']; ?>px;">
      <div class="win95-window win95-standalone-window">
        <div class="win95-titlebar" tabindex="0">
          <span class="win95-title-icon">💾</span>
          <span class="win95-title-text"><?php echo main_skin_esc($window_title); ?></span>
          <div class="win95-buttons"><span class="win95-btn">_</span><span class="win95-btn">□</span><span class="win95-btn">×</span></div>
        </div>
        <div class="win95-inner-border"><?php echo $main_skin_latest_html; ?></div>
      </div>
    </div>

    <!-- ── 레이어 10: 배너 윈도우 ── -->
    <div id="retro-banner-window" class="retro-draggable-window"
         style="top:<?php echo (int)$main_skin_config['banner_win_top']; ?>px;left:<?php echo (int)$main_skin_config['banner_win_left']; ?>px;">
      <div class="win95-window win95-standalone-window">
        <div class="win95-titlebar" tabindex="0">
          <span class="win95-title-icon">🗂️</span>
          <span class="win95-title-text"><?php echo main_skin_esc($banner_title); ?></span>
          <div class="win95-buttons"><span class="win95-btn">_</span><span class="win95-btn">□</span><span class="win95-btn">×</span></div>
        </div>
        <div class="win95-inner-border win95-banner-inner">
          <div class="win95-banner-area">
            <?php $visible_banners = array(); foreach ($main_skin_banners as $banner) { if (!empty($banner['enabled']) && !empty($banner['image'])) $visible_banners[] = $banner; } ?>
            <?php if (empty($visible_banners)) { ?>
            <p class="win95-no-posts">등록된 배너가 없습니다.</p>
            <?php } else { foreach ($visible_banners as $banner) { ?>
            <?php $banner_link = !empty($banner['link']) ? $banner['link'] : '#'; ?>
            <a href="<?php echo main_skin_esc($banner_link); ?>" target="<?php echo main_skin_esc($banner['target']); ?>" rel="noopener noreferrer" class="banner-link">
              <img src="<?php echo main_skin_esc($banner['image']); ?>" alt="<?php echo main_skin_esc($banner['alt']); ?>" class="banner-img" loading="lazy">
            </a>
            <?php } } ?>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /#retro-bg-container -->

  <?php if (!empty($main_skin_config['parallax_ng_image'])) { ?>
  <div id="parallax-ng-layer" class="parallax-layer parallax-global-layer parallax-above-layer"
       data-pos-v="<?php echo main_skin_esc($main_skin_config['parallax_ng_pos_v']); ?>"
       data-pos-h="<?php echo main_skin_esc($main_skin_config['parallax_ng_pos_h']); ?>"
       data-offset-x="<?php echo (int)$main_skin_config['parallax_ng_offset_x']; ?>"
       data-offset-y="<?php echo (int)$main_skin_config['parallax_ng_offset_y']; ?>"
       aria-hidden="true">
    <img src="<?php echo main_skin_esc($main_skin_config['parallax_ng_image']); ?>" alt="" loading="lazy" decoding="async"
         style="<?php echo main_skin_parallax_img_style($main_skin_config, 'parallax_ng'); ?>">
  </div>
  <?php } ?>

  <?php if (!empty($main_skin_config['parallax_fg_image'])) { ?>
  <div id="parallax-fg-layer" class="parallax-layer parallax-global-layer parallax-above-layer"
       data-pos-v="<?php echo main_skin_esc($main_skin_config['parallax_fg_pos_v']); ?>"
       data-pos-h="<?php echo main_skin_esc($main_skin_config['parallax_fg_pos_h']); ?>"
       data-offset-x="<?php echo (int)$main_skin_config['parallax_fg_offset_x']; ?>"
       data-offset-y="<?php echo (int)$main_skin_config['parallax_fg_offset_y']; ?>"
       aria-hidden="true">
    <img src="<?php echo main_skin_esc($main_skin_config['parallax_fg_image']); ?>" alt="" loading="lazy" decoding="async"
         style="<?php echo main_skin_parallax_img_style($main_skin_config, 'parallax_fg'); ?>">
  </div>
  <?php } ?>

  <?php if ($main_skin_is_admin) { ?>
  <!-- ═══════════════════════════════════════ 관리자 패널 ═══════════════════════════════════════ -->
  <div id="retro-admin-modal" class="admin-modal" hidden>
    <div class="admin-modal-backdrop" data-admin-close="true"></div>
    <div id="retro-admin-panel" class="win95-window admin-panel admin-panel-modal" role="dialog" aria-modal="true" aria-labelledby="retro-admin-panel-title" tabindex="-1">
      <div class="win95-titlebar admin-panel-titlebar">
        <span class="win95-title-icon">🔧</span>
        <span class="win95-title-text" id="retro-admin-panel-title">메인 스킨 관리 패널</span>
        <button type="button" class="win95-btn admin-panel-close-btn" id="admin-panel-close-btn" aria-label="닫기">&times;</button>
      </div>
      <div id="admin-panel-body" class="admin-panel-body">
      <div class="admin-tabs">
        <button type="button" class="admin-tab active" data-tab="tab-stickers">🎨 스티커</button>
        <button type="button" class="admin-tab" data-tab="tab-bgimage">🖼️ 배경 이미지</button>
        <button type="button" class="admin-tab" data-tab="tab-parallax">🏔️ 패럴랙스</button>
        <button type="button" class="admin-tab" data-tab="tab-window">🪟 최신글/배너</button>
      </div>

      <!-- ── 스티커 탭 ── -->
      <div class="admin-tab-pane" id="tab-stickers">
        <form id="sticker-add-form" enctype="multipart/form-data">
          <h3 class="admin-section-title">새 스티커 추가</h3>
          <input type="hidden" name="action" value="add_sticker">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <div class="admin-field-row">
            <label>이미지 종류</label>
            <span><label><input type="radio" name="src_type" value="url" checked> URL</label>&nbsp;<label><input type="radio" name="src_type" value="upload"> 파일</label></span>
          </div>
          <div id="sticker-url-rows">
            <?php for ($i = 1; $i <= 5; $i++) { ?>
            <div class="admin-field-row"><label>URL <?php echo $i; ?></label><input type="text" name="src_urls[]" style="width:300px;"<?php echo $i === 1 ? ' placeholder="https://example.com/sticker.gif"' : ''; ?>></div>
            <?php } ?>
          </div>
          <div id="sticker-file-row" style="display:none;">
            <div class="admin-field-row"><label>이미지/영상 파일</label><input type="file" name="sticker_files[]" accept="image/*,video/mp4" multiple></div>
            <p class="admin-hint">최대 5개까지 선택 가능합니다. (mp4 영상도 가능)</p>
          </div>
          <div class="admin-field-row"><label>설명(alt)</label><input type="text" name="alt" style="width:200px;"></div>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">스티커 추가</button></div>
        </form>
        <div id="sticker-add-msg" class="admin-msg" style="display:none;"></div>

        <form id="asset-add-form" enctype="multipart/form-data">
          <h3 class="admin-section-title">에셋 관리 <span class="admin-hint" style="display:inline;">(자주 쓰는 이미지를 저장)</span></h3>
          <input type="hidden" name="action" value="add_asset">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <div class="admin-field-row">
            <label>이미지 종류</label>
            <span><label><input type="radio" name="src_type" value="url" checked> URL</label>&nbsp;<label><input type="radio" name="src_type" value="upload"> 파일</label></span>
          </div>
          <div id="asset-url-row"><div class="admin-field-row"><label>이미지 URL</label><input type="text" name="src_url" style="width:300px;"></div></div>
          <div id="asset-file-row" style="display:none;"><div class="admin-field-row"><label>이미지 파일</label><input type="file" name="asset_file" accept="image/*"></div></div>
          <div class="admin-field-row"><label>설명(alt)</label><input type="text" name="alt" style="width:200px;"></div>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">에셋 저장</button></div>
        </form>
        <div id="asset-add-msg" class="admin-msg" style="display:none;"></div>

        <div id="admin-asset-list">
          <?php if (empty($main_skin_assets)) { ?>
          <p class="win95-no-posts">등록된 에셋이 없습니다.</p>
          <?php } else { foreach ($main_skin_assets as $asset) { ?>
          <div class="admin-asset-item" id="admin-asset-<?php echo main_skin_esc($asset['id']); ?>">
            <img src="<?php echo main_skin_esc($asset['image']); ?>" alt="<?php echo main_skin_esc($asset['alt']); ?>" class="admin-asset-thumb" loading="lazy">
            <span class="admin-asset-alt"><?php echo main_skin_esc($asset['alt']); ?></span>
            <div class="admin-item-actions" style="flex-direction:row;">
              <button type="button" class="win95-action-btn asset-place-btn" data-id="<?php echo main_skin_esc($asset['id']); ?>">배치</button>
              <button type="button" class="win95-action-btn asset-del-btn" data-id="<?php echo main_skin_esc($asset['id']); ?>">삭제</button>
            </div>
          </div>
          <?php } } ?>
        </div>
        <div id="asset-msg" class="admin-msg" style="display:none;"></div>

        <h3 class="admin-section-title">등록된 스티커</h3>
        <p class="admin-hint">스티커 편집 모드에서 화면 위 핸들로 이동·크기·회전·z-index를 직접 조절하세요.</p>
        <div id="admin-sticker-list">
          <?php if (empty($main_skin_stickers)) { ?>
          <p class="win95-no-posts">등록된 스티커가 없습니다.</p>
          <?php } else { foreach ($main_skin_stickers as $sticker) { ?>
          <form class="admin-sticker-item sticker-edit-form" id="admin-item-<?php echo main_skin_esc($sticker['id']); ?>">
            <input type="hidden" name="action" value="update_sticker">
            <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
            <input type="hidden" name="id" value="<?php echo main_skin_esc($sticker['id']); ?>">
            <?php if (main_skin_is_video($sticker['image'])) { ?>
            <video class="admin-sticker-thumb" autoplay loop muted playsinline><source src="<?php echo main_skin_esc($sticker['image']); ?>" type="video/mp4"></video>
            <?php } else { ?>
            <img src="<?php echo main_skin_esc($sticker['image']); ?>" alt="<?php echo main_skin_esc($sticker['alt']); ?>" class="admin-sticker-thumb" loading="lazy">
            <?php } ?>
            <div class="admin-item-fields">
              <div class="admin-inline-fields">
                <input type="text" name="alt" value="<?php echo main_skin_esc($sticker['alt']); ?>" placeholder="설명(alt)" style="min-width:160px;">
                <label class="inline-check"><input type="checkbox" name="enabled"<?php echo !empty($sticker['enabled']) ? ' checked' : ''; ?>> 노출</label>
              </div>
            </div>
            <div class="admin-item-actions">
              <button type="submit" class="win95-action-btn">저장</button>
              <button type="button" class="win95-action-btn admin-sticker-delete" data-id="<?php echo main_skin_esc($sticker['id']); ?>">삭제</button>
            </div>
          </form>
          <?php } } ?>
        </div>
        <div id="sticker-edit-msg" class="admin-msg" style="display:none;"></div>
      </div>

      <!-- ══════════ 배경 이미지 탭 ══════════ -->
      <div class="admin-tab-pane" id="tab-bgimage" style="display:none;">
        <form id="config-bg-form" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update_bg">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">

          <h3 class="admin-section-title">배경 이미지</h3>
          <div class="admin-field-row">
            <label>현재 이미지</label>
            <?php if (!empty($main_skin_config['bg_image'])) {
                if (main_skin_is_video($main_skin_config['bg_image'])) { ?>
            <video class="admin-preview-img" autoplay loop muted playsinline><source src="<?php echo main_skin_esc($main_skin_config['bg_image']); ?>" type="video/mp4"></video>
            <?php } else { ?>
            <img src="<?php echo main_skin_esc($main_skin_config['bg_image']); ?>" class="admin-preview-img" loading="lazy">
            <?php }
            } else { ?>
            <span class="admin-none">없음</span>
            <?php } ?>
            <?php if (!empty($main_skin_config['bg_image'])) { ?>
            <button type="button" class="win95-action-btn" id="bg-del-btn">삭제</button>
            <?php } ?>
          </div>
          <div class="admin-field-row"><label>이미지 URL</label><input type="text" name="bg_url" value="<?php echo main_skin_esc($main_skin_config['bg_image']); ?>" style="width:320px;"></div>
          <div class="admin-field-row"><label>업로드</label><input type="file" name="bg_file" id="bg-file-input" accept="image/*,video/mp4"></div>

          <!-- 크롭 영역 -->
          <div id="bg-crop-container" style="display:none;">
            <h3 class="admin-section-title">이미지 크롭</h3>
            <p class="admin-hint">마우스로 드래그하여 원하는 영역을 선택하세요. mp4 파일은 크롭이 불가합니다.</p>
            <div id="bg-crop-wrapper">
              <img id="bg-crop-preview" src="" alt="크롭 미리보기">
              <div id="bg-crop-selection"></div>
            </div>
            <div class="admin-field-row" style="margin-top:8px;">
              <label>크롭 영역</label>
              <span id="bg-crop-info">선택 안 됨</span>
            </div>
            <div class="admin-field-row">
              <label></label>
              <button type="button" class="win95-action-btn" id="bg-crop-apply">크롭 적용</button>
              <button type="button" class="win95-action-btn" id="bg-crop-reset">크롭 초기화</button>
            </div>
          </div>
          <input type="hidden" name="bg_cropped_data" id="bg-cropped-data" value="">

          <div class="admin-field-row">
            <label>표시 방식</label>
            <select name="bg_fit">
              <option value="cover"<?php echo $main_skin_config['bg_fit'] === 'cover' ? ' selected' : ''; ?>>영역에 맞춰 늘이기 (cover)</option>
              <option value="contain"<?php echo $main_skin_config['bg_fit'] === 'contain' ? ' selected' : ''; ?>>비율 맞춰 자르기 (contain)</option>
              <option value="original"<?php echo $main_skin_config['bg_fit'] === 'original' ? ' selected' : ''; ?>>원래 크기 (original)</option>
            </select>
          </div>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">배경 이미지 저장</button></div>
        </form>
        <div id="config-bg-msg" class="admin-msg" style="display:none;"></div>

        <!-- ── 텍스트 오버레이 설정 ── -->
        <form id="config-text-form">
          <input type="hidden" name="action" value="update_texts">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">

          <?php foreach ($text_keys as $tk) {
              $label = $text_labels[$tk];
              $prefix = 'text_' . $tk;
          ?>
          <h3 class="admin-section-title"><?php echo main_skin_esc($label); ?><?php if ($tk === 'bg_title') { ?> <span class="admin-hint" style="display:inline;">(배경 이미지 뒤에 표시됨)</span><?php } ?></h3>
          <div class="admin-field-row">
            <label>내용</label>
            <textarea name="<?php echo $prefix; ?>" rows="<?php echo ($tk === 'title3_body' || $tk === 'general') ? '4' : '2'; ?>" style="width:320px;"><?php echo main_skin_esc(isset($main_skin_config[$prefix]) ? $main_skin_config[$prefix] : ''); ?></textarea>
          </div>
          <div class="admin-field-grid">
            <div class="admin-field-row">
              <label>폰트</label>
              <select name="<?php echo $prefix; ?>_font">
                <?php foreach ($font_options as $fval => $flabel) { ?>
                <option value="<?php echo main_skin_esc($fval); ?>"<?php echo (isset($main_skin_config[$prefix . '_font']) && $main_skin_config[$prefix . '_font'] === $fval) ? ' selected' : ''; ?>><?php echo main_skin_esc($flabel); ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="admin-field-row">
              <label>크기</label>
              <input type="text" name="<?php echo $prefix; ?>_size" value="<?php echo main_skin_esc(isset($main_skin_config[$prefix . '_size']) ? $main_skin_config[$prefix . '_size'] : 14); ?>" style="width:70px;">
              <select name="<?php echo $prefix; ?>_size_unit" style="width:80px;">
                <?php
                $cur_unit = isset($main_skin_config[$prefix . '_size_unit']) ? $main_skin_config[$prefix . '_size_unit'] : 'px';
                $unit_opts = array('px' => 'px', 'em' => 'em', 'rem' => 'rem', 'pt' => 'pt', 'vw' => 'vw', 'vh' => 'vh', 'cw%' => 'cw%');
                foreach ($unit_opts as $uval => $ulabel) { ?>
                <option value="<?php echo $uval; ?>"<?php echo ($cur_unit === $uval) ? ' selected' : ''; ?>><?php echo $ulabel; ?></option>
                <?php } ?>
              </select>
              <span class="admin-hint" style="display:inline;margin-left:4px;">cw% = 컨테이너 기준</span>
            </div>
            <div class="admin-field-row"><label>자간 (px)</label><input type="number" name="<?php echo $prefix; ?>_spacing" value="<?php echo (int)(isset($main_skin_config[$prefix . '_spacing']) ? $main_skin_config[$prefix . '_spacing'] : 0); ?>" min="-20" max="50"></div>
            <div class="admin-field-row"><label>행간</label><input type="text" name="<?php echo $prefix; ?>_line_height" value="<?php echo main_skin_esc(isset($main_skin_config[$prefix . '_line_height']) ? $main_skin_config[$prefix . '_line_height'] : '1.4'); ?>" style="width:60px;"></div>
            <div class="admin-field-row"><label>색상</label><input type="color" name="<?php echo $prefix; ?>_color" value="<?php echo main_skin_esc(isset($main_skin_config[$prefix . '_color']) ? $main_skin_config[$prefix . '_color'] : '#000000'); ?>"></div>
            <div class="admin-field-row">
              <label>스타일</label>
              <label class="inline-check"><input type="checkbox" name="<?php echo $prefix; ?>_bold"<?php echo !empty($main_skin_config[$prefix . '_bold']) ? ' checked' : ''; ?>> 굵게</label>
              <label class="inline-check"><input type="checkbox" name="<?php echo $prefix; ?>_italic"<?php echo !empty($main_skin_config[$prefix . '_italic']) ? ' checked' : ''; ?>> 기울임</label>
            </div>
            <div class="admin-field-row">
              <label>수직 비율 (%)</label>
              <input type="number" name="<?php echo $prefix; ?>_scale_y" value="<?php echo (int)(isset($main_skin_config[$prefix . '_scale_y']) ? $main_skin_config[$prefix . '_scale_y'] : 100); ?>" min="10" max="500" style="width:70px;">
              <span class="admin-hint" style="display:inline;margin-left:4px;">100 = 기본, 200 = 2배 세로</span>
            </div>
            <div class="admin-field-row"><label>테두리 색</label><input type="color" name="<?php echo $prefix; ?>_stroke_color" value="<?php echo main_skin_esc(isset($main_skin_config[$prefix . '_stroke_color']) && $main_skin_config[$prefix . '_stroke_color'] !== '' ? $main_skin_config[$prefix . '_stroke_color'] : '#000000'); ?>"> <label class="inline-check"><input type="checkbox" name="<?php echo $prefix; ?>_stroke_enabled"<?php echo (!empty($main_skin_config[$prefix . '_stroke_color'])) ? ' checked' : ''; ?>> 사용</label></div>
            <div class="admin-field-row"><label>테두리 두께 (px)</label><input type="number" name="<?php echo $prefix; ?>_stroke_width" value="<?php echo (int)(isset($main_skin_config[$prefix . '_stroke_width']) ? $main_skin_config[$prefix . '_stroke_width'] : 0); ?>" min="0" max="10"></div>
            <div class="admin-field-row"><label>위치 상 (px)</label><input type="number" name="<?php echo $prefix; ?>_top" value="<?php echo (int)(isset($main_skin_config[$prefix . '_top']) ? $main_skin_config[$prefix . '_top'] : 0); ?>" min="-500" max="2000"></div>
            <div class="admin-field-row"><label><?php echo ($tk === 'title1' || $tk === 'bg_title') ? '위치 좌 (px)' : '위치 우 (px)'; ?></label><input type="number" name="<?php echo $prefix; ?>_left" value="<?php echo (int)(isset($main_skin_config[$prefix . '_left']) ? $main_skin_config[$prefix . '_left'] : 0); ?>" min="-500" max="2000"></div>
          </div>
          <?php } ?>

          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">텍스트 설정 저장</button></div>
        </form>
        <div id="config-text-msg" class="admin-msg" style="display:none;"></div>

        <!-- ── 커스텀 폰트 관리 ── -->
        <form id="font-upload-form" enctype="multipart/form-data">
          <h3 class="admin-section-title">커스텀 폰트 관리</h3>
          <input type="hidden" name="action" value="upload_font">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <div class="admin-field-row"><label>폰트 이름</label><input type="text" name="font_name" style="width:200px;" placeholder="예: NanumGothic"></div>
          <div class="admin-field-row"><label>폰트 파일</label><input type="file" name="font_file" accept=".ttf,.otf,.woff,.woff2"></div>
          <p class="admin-hint">TTF, OTF, WOFF, WOFF2 형식을 지원합니다.</p>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">폰트 업로드</button></div>
        </form>
        <div id="font-upload-msg" class="admin-msg" style="display:none;"></div>
        <form id="font-code-form">
          <h3 class="admin-section-title">CSS 코드로 폰트 추가</h3>
          <input type="hidden" name="action" value="add_font_code">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <div class="admin-field-row"><label>폰트 이름</label><input type="text" name="font_name" style="width:200px;" placeholder="예: Noto Sans KR"></div>
          <div class="admin-field-row">
            <label>CSS 코드</label>
            <textarea name="font_code" rows="4" style="width:400px;font-family:monospace;font-size:12px;" placeholder="@import url('https://fonts.googleapis.com/...');&#10;또는&#10;@font-face { font-family: '...'; src: url('...'); }&#10;또는&#10;<link> 태그 붙여넣기"></textarea>
          </div>
          <p class="admin-hint">Google Fonts 등에서 복사한 @import, @font-face, 또는 &lt;link&gt; 태그를 붙여넣으세요.</p>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">코드로 폰트 추가</button></div>
        </form>
        <div id="font-code-msg" class="admin-msg" style="display:none;"></div>
        <form id="font-path-form">
          <h3 class="admin-section-title">서버 경로로 폰트 등록</h3>
          <input type="hidden" name="action" value="add_font_path">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <div class="admin-field-row"><label>폰트 이름</label><input type="text" name="font_name" style="width:200px;" placeholder="예: NanumGothic"></div>
          <div class="admin-field-row">
            <label>서버 경로</label>
            <input type="text" name="font_path" style="width:400px;" placeholder="/data/file/main_skin/fonts/font_xxx.woff2 또는 /theme/fonts/myfont.ttf">
          </div>
          <p class="admin-hint">서버에 이미 업로드된 폰트 파일의 웹 경로를 입력하세요. 웹루트 기준 절대경로(/로 시작) 또는 상대경로를 사용합니다.<br>지원 형식: TTF, OTF, WOFF, WOFF2 | 이미 등록된 동일 파일은 중복 등록되지 않습니다.</p>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">서버 폰트 등록</button></div>
        </form>
        <div id="font-path-msg" class="admin-msg" style="display:none;"></div>
        <div id="admin-font-list">
          <?php if (empty($main_skin_fonts)) { ?>
          <p class="win95-no-posts">등록된 커스텀 폰트가 없습니다.</p>
          <?php } else { foreach ($main_skin_fonts as $font) { ?>
          <div class="admin-asset-item" id="admin-font-<?php echo main_skin_esc($font['id']); ?>">
            <span class="admin-asset-alt" style="font-family:'<?php echo main_skin_esc($font['name']); ?>',sans-serif;">Aa가나 - <?php echo main_skin_esc($font['name']); ?></span>
            <span class="admin-hint"><?php
              $st = isset($font['source_type']) ? $font['source_type'] : 'file';
              if ($st === 'code') echo '[CSS코드]';
              elseif ($st === 'path') echo '[서버경로]';
              else echo '[파일]';
            ?></span>
            <button type="button" class="win95-action-btn font-del-btn" data-id="<?php echo main_skin_esc($font['id']); ?>">삭제</button>
          </div>
          <?php } } ?>
        </div>
        <div id="font-msg" class="admin-msg" style="display:none;"></div>
      </div>

      <!-- ── 패럴랙스 탭 ── -->
      <div class="admin-tab-pane" id="tab-parallax" style="display:none;">
        <form id="config-parallax-form" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update_parallax">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <p class="admin-hint">마우스 움직임에 따라 각 레이어가 반대 방향으로 이동합니다.</p>
          <?php
          $parallax_layers = array(
              'fg' => array('label' => '초근경 (Foreground)', 'desc' => '메인 레이아웃 위 — 가장 크게 움직임'),
              'ng' => array('label' => '근경 (Near-ground)', 'desc' => '중간 정도 움직임'),
              'bg' => array('label' => '원경 (Background)', 'desc' => '가장 작게 움직임')
          );
          foreach ($parallax_layers as $pl_key => $pl_info) {
              $img_key = 'parallax_' . $pl_key . '_image';
              $pv_key = 'parallax_' . $pl_key . '_pos_v';
              $ph_key = 'parallax_' . $pl_key . '_pos_h';
              $ox_key = 'parallax_' . $pl_key . '_offset_x';
              $oy_key = 'parallax_' . $pl_key . '_offset_y';
          ?>
          <h3 class="admin-section-title"><?php echo main_skin_esc($pl_info['label']); ?> <span class="admin-hint" style="display:inline;">(<?php echo main_skin_esc($pl_info['desc']); ?>)</span></h3>
          <div class="admin-field-row">
            <label>현재 이미지</label>
            <?php if (!empty($main_skin_config[$img_key])) { ?>
            <img src="<?php echo main_skin_esc($main_skin_config[$img_key]); ?>" class="admin-preview-img" loading="lazy">
            <button type="button" class="win95-action-btn parallax-del-btn" data-layer="<?php echo $pl_key; ?>">삭제</button>
            <?php } else { ?>
            <span class="admin-none">없음</span>
            <?php } ?>
          </div>
          <div class="admin-field-row"><label>이미지 URL</label><input type="text" name="parallax_<?php echo $pl_key; ?>_url" value="<?php echo main_skin_esc(isset($main_skin_config[$img_key]) ? $main_skin_config[$img_key] : ''); ?>" style="width:320px;"></div>
          <div class="admin-field-row"><label>업로드</label><input type="file" name="parallax_<?php echo $pl_key; ?>_file" accept="image/*"></div>
          <div class="admin-field-row"><label>세로 위치</label>
            <select name="parallax_<?php echo $pl_key; ?>_pos_v">
              <option value="top"<?php echo (isset($main_skin_config[$pv_key]) && $main_skin_config[$pv_key] === 'top') ? ' selected' : ''; ?>>상단</option>
              <option value="center"<?php echo (!isset($main_skin_config[$pv_key]) || $main_skin_config[$pv_key] === 'center') ? ' selected' : ''; ?>>중앙</option>
              <option value="bottom"<?php echo (isset($main_skin_config[$pv_key]) && $main_skin_config[$pv_key] === 'bottom') ? ' selected' : ''; ?>>하단</option>
            </select>
          </div>
          <div class="admin-field-row"><label>가로 위치</label>
            <select name="parallax_<?php echo $pl_key; ?>_pos_h">
              <option value="left"<?php echo (isset($main_skin_config[$ph_key]) && $main_skin_config[$ph_key] === 'left') ? ' selected' : ''; ?>>좌측</option>
              <option value="center"<?php echo (!isset($main_skin_config[$ph_key]) || $main_skin_config[$ph_key] === 'center') ? ' selected' : ''; ?>>중앙</option>
              <option value="right"<?php echo (isset($main_skin_config[$ph_key]) && $main_skin_config[$ph_key] === 'right') ? ' selected' : ''; ?>>우측</option>
            </select>
          </div>
          <div class="admin-field-row"><label>가로 미세조정 (px)</label><input type="number" name="parallax_<?php echo $pl_key; ?>_offset_x" value="<?php echo (int)(isset($main_skin_config[$ox_key]) ? $main_skin_config[$ox_key] : 0); ?>"></div>
          <div class="admin-field-row"><label>세로 미세조정 (px)</label><input type="number" name="parallax_<?php echo $pl_key; ?>_offset_y" value="<?php echo (int)(isset($main_skin_config[$oy_key]) ? $main_skin_config[$oy_key] : 0); ?>"></div>
          <?php } ?>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">패럴랙스 설정 저장</button></div>
        </form>
        <div id="config-parallax-msg" class="admin-msg" style="display:none;"></div>
      </div>

      <!-- ── 최신글/배너 탭 ── -->
      <div class="admin-tab-pane" id="tab-window" style="display:none;">
        <form id="config-window-form">
          <input type="hidden" name="action" value="update_window">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <h3 class="admin-section-title">레트로 창 설정</h3>
          <div class="admin-field-row"><label>최신글 타이틀</label><input type="text" name="win_title" value="<?php echo main_skin_esc($window_title); ?>"></div>
          <div class="admin-field-row"><label>배너 타이틀</label><input type="text" name="banner_title" value="<?php echo main_skin_esc($banner_title); ?>"></div>
          <div class="admin-field-row"><label>게시판 ID</label><input type="text" name="board_ids" value="<?php echo main_skin_esc($main_skin_config['latest_boards']); ?>" style="width:240px;" placeholder="free,notice"></div>
          <div class="admin-field-row"><label>게시글 수</label><input type="number" name="limit" min="1" max="20" value="<?php echo (int)$main_skin_config['latest_rows']; ?>"></div>

          <h3 class="admin-section-title">📅 잡지 날짜 위젯</h3>
          <div class="admin-field-row"><label>위젯 사용</label><label class="inline-check"><input type="checkbox" name="date_widget_enabled"<?php echo !empty($main_skin_config['date_widget_enabled']) ? ' checked' : ''; ?>> 표시</label></div>
          <div class="admin-field-row"><label>위치 상 (px)</label><input type="number" name="date_widget_top" value="<?php echo (int)$main_skin_config['date_widget_top']; ?>" min="-500" max="2000"></div>
          <div class="admin-field-row"><label>위치 우 (px)</label><input type="number" name="date_widget_right" value="<?php echo (int)$main_skin_config['date_widget_right']; ?>" min="-500" max="2000"></div>

          <!-- 파트별 색상·폰트 -->
          <?php foreach ($date_widget_parts as $dw_part_key => $dw_part_label) {
              $dw_color_key = 'date_widget_' . $dw_part_key . '_color';
              $dw_font_key  = 'date_widget_' . $dw_part_key . '_font';
              $cur_color = isset($main_skin_config[$dw_color_key]) ? $main_skin_config[$dw_color_key] : '#000000';
              $cur_font  = isset($main_skin_config[$dw_font_key])  ? $main_skin_config[$dw_font_key]  : '';
          ?>
          <h4 class="admin-section-subtitle"><?php echo main_skin_esc($dw_part_label); ?></h4>
          <div class="admin-field-row">
            <label>색상</label>
            <input type="color" name="<?php echo $dw_color_key; ?>" value="<?php echo main_skin_esc($cur_color); ?>">
          </div>
          <div class="admin-field-row">
            <label>폰트</label>
            <select name="<?php echo $dw_font_key; ?>">
              <?php foreach ($font_options as $fval => $flabel) { ?>
              <option value="<?php echo main_skin_esc($fval); ?>"<?php echo ($cur_font === $fval) ? ' selected' : ''; ?>><?php echo main_skin_esc($flabel); ?></option>
              <?php } ?>
            </select>
          </div>
          <?php } ?>

          <div class="admin-field-row"><label>테두리 색</label><input type="color" name="date_widget_stroke_color" value="<?php echo main_skin_esc(isset($main_skin_config['date_widget_stroke_color']) && $main_skin_config['date_widget_stroke_color'] !== '' ? $main_skin_config['date_widget_stroke_color'] : '#000000'); ?>"> <label class="inline-check"><input type="checkbox" name="date_widget_stroke_enabled"<?php echo (!empty($main_skin_config['date_widget_stroke_color'])) ? ' checked' : ''; ?>> 사용</label></div>
          <div class="admin-field-row"><label>테두리 두께 (px)</label><input type="number" name="date_widget_stroke_width" value="<?php echo (int)(isset($main_skin_config['date_widget_stroke_width']) ? $main_skin_config['date_widget_stroke_width'] : 0); ?>" min="0" max="10"></div>

          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">창 설정 저장</button></div>
        </form>
        <div id="config-window-msg" class="admin-msg" style="display:none;"></div>

        <h3 class="admin-section-title">배너 관리</h3>
        <form id="banner-add-form" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_banner">
          <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
          <div class="admin-field-row"><label>이미지 URL</label><input type="text" name="banner_url" style="width:320px;"></div>
          <div class="admin-field-row"><label>업로드</label><input type="file" name="banner_file" accept="image/*"></div>
          <div class="admin-field-row"><label>링크</label><input type="text" name="banner_link" style="width:320px;"></div>
          <div class="admin-field-row"><label>alt</label><input type="text" name="banner_alt"></div>
          <div class="admin-field-row"><label>target / sort</label><select name="banner_target"><option value="_blank">_blank</option><option value="_self">_self</option></select> <input type="number" name="sort" value="0" style="width:60px;"></div>
          <div class="admin-field-row"><label>노출</label><label class="inline-check"><input type="checkbox" name="enabled" checked> 사용</label></div>
          <div class="admin-field-row"><label></label><button type="submit" class="win95-action-btn">배너 추가</button></div>
        </form>
        <div id="banner-add-msg" class="admin-msg" style="display:none;"></div>

        <div id="admin-banner-list">
          <?php if (empty($main_skin_banners)) { ?>
          <p class="win95-no-posts">등록된 배너가 없습니다.</p>
          <?php } else { foreach ($main_skin_banners as $index => $banner) { ?>
          <form class="admin-banner-item banner-edit-form" id="admin-banner-<?php echo (int)$index; ?>">
            <input type="hidden" name="action" value="update_banner">
            <input type="hidden" name="token" value="<?php echo main_skin_esc($main_skin_token); ?>">
            <input type="hidden" name="index" value="<?php echo (int)$index; ?>">
            <img src="<?php echo main_skin_esc($banner['image']); ?>" alt="<?php echo main_skin_esc($banner['alt']); ?>" class="admin-sticker-thumb" loading="lazy">
            <div class="admin-item-fields">
              <div class="admin-inline-fields">
                <input type="text" name="banner_link" value="<?php echo main_skin_esc($banner['link']); ?>" placeholder="링크 URL">
                <input type="text" name="banner_alt" value="<?php echo main_skin_esc($banner['alt']); ?>" placeholder="alt">
              </div>
              <div class="admin-inline-fields">
                <select name="banner_target"><option value="_blank"<?php echo $banner['target'] === '_blank' ? ' selected' : ''; ?>>_blank</option><option value="_self"<?php echo $banner['target'] === '_self' ? ' selected' : ''; ?>>_self</option></select>
                <input type="number" name="sort" value="<?php echo (int)$banner['sort']; ?>" placeholder="sort">
                <label class="inline-check"><input type="checkbox" name="enabled"<?php echo !empty($banner['enabled']) ? ' checked' : ''; ?>> 노출</label>
              </div>
            </div>
            <div class="admin-item-actions">
              <button type="submit" class="win95-action-btn">저장</button>
              <button type="button" class="win95-action-btn banner-del-btn" data-index="<?php echo (int)$index; ?>">삭제</button>
            </div>
          </form>
          <?php } } ?>
        </div>
      </div>

      </div><!-- /admin-panel-body -->
    </div>
  </div>
  <?php } ?>

<?php
$_cal_widget_path = '';
if (defined('G5_SKIN_PATH')) {
    $_cal_widget_path = G5_SKIN_PATH . '/board/calendar/calendar_widget.php';
}
if ($_cal_widget_path && file_exists($_cal_widget_path)) {
    include_once($_cal_widget_path);
}
?>

<script>
window.RETRO_SKIN_URL = '<?php echo addslashes(MAIN_SKIN_URL); ?>';
window.RETRO_IS_ADMIN = <?php echo $main_skin_is_admin ? 'true' : 'false'; ?>;
window.RETRO_TOKEN = '<?php echo addslashes($main_skin_token); ?>';
</script>
<script src="<?php echo main_skin_esc(MAIN_SKIN_URL); ?>/main.js"></script>
</div>