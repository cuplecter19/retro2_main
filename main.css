(function ($) {
  'use strict';
  var RELOAD_DELAY = 700;

  /* ══════════════════════════════════════════════
     컨테이너 기준(%) 폰트 사이즈 자동 계산
     data-size-cw="50" → 컨테이너 폭(또는 높이)의 50%를 텍스트가 차지하도록 조절
  ══════════════════════════════════════════════ */
  function calcContainerFontSize() {
    var container = document.getElementById('retro-bg-container');
    if (!container) return;

    var cw = container.offsetWidth;
    var ch = container.offsetHeight;

    var els = container.querySelectorAll('[data-size-cw]');
    for (var i = 0; i < els.length; i++) {
      var el = els[i];
      var pct = parseInt(el.getAttribute('data-size-cw'), 10) || 100;
      var tk  = el.getAttribute('data-text-key') || '';

      /* title1은 -90도 회전 → 세로(ch) 기준 */
      var basis = (tk === 'title1') ? ch : cw;
      var targetWidth = basis * pct / 100;

      /* 측정을 위해 줄바꿈 방지 + inline-block + max-content */
      var origWhiteSpace = el.style.whiteSpace;
      var origDisplay    = el.style.display;
      var origWidth      = el.style.width;
      var origTransform  = el.style.transform;

      el.style.whiteSpace = 'nowrap';
      el.style.display    = 'inline-block';
      el.style.width      = 'max-content';
      el.style.transform  = 'none';

      /* 이진 탐색으로 전체 텍스트 너비가 targetWidth에 맞는 font-size 찾기 */
      var lo = 1, hi = 500, mid, tries = 0;

      while (hi - lo > 0.5 && tries < 30) {
        mid = (lo + hi) / 2;
        el.style.fontSize = mid + 'px';
        var measured = el.scrollWidth;
        if (measured > targetWidth) { hi = mid; }
        else { lo = mid; }
        tries++;
      }

      var finalSize = Math.floor(lo);
      el.style.fontSize = finalSize + 'px';

      /* 원래 스타일 복원 */
      el.style.whiteSpace = origWhiteSpace;
      el.style.display    = origDisplay;
      el.style.width      = origWidth;

      /* transform 복원: scaleY 및 title1 rotate 반영 */
      var scaleY = parseInt(el.getAttribute('data-scale-y') || '100', 10);
      var sy = (scaleY && scaleY !== 100 && scaleY > 0) ? (scaleY / 100) : 0;

      if (tk === 'title1') {
        if (sy) {
          el.style.transform = 'rotate(-90deg) scaleY(' + sy + ')';
        } else {
          el.style.transform = origTransform || '';
        }
      } else {
        if (sy) {
          el.style.transform = 'scaleY(' + sy + ')';
        } else {
          el.style.transform = origTransform || '';
        }
      }
    }
  }

  /* 초기 실행: DOM ready 후 실행 + 리사이즈 시 재계산 */
  $(function () {
    if (document.querySelectorAll('[data-size-cw]').length) {
      calcContainerFontSize();
      var cwResizeTimer = null;
      $(window).on('resize.cwfont', function () {
        clearTimeout(cwResizeTimer);
        cwResizeTimer = setTimeout(calcContainerFontSize, 150);
      });
    }
  });

  var SKIN_URL = (typeof window.RETRO_SKIN_URL !== 'undefined') ? window.RETRO_SKIN_URL : '';
  var IS_ADMIN = (typeof window.RETRO_IS_ADMIN !== 'undefined') ? window.RETRO_IS_ADMIN : false;
  var TOKEN    = (typeof window.RETRO_TOKEN    !== 'undefined') ? window.RETRO_TOKEN    : '';

  function ajaxPost(url, formData, onSuccess, onError) {
    $.ajax({
      url: url, type: 'POST', data: formData, dataType: 'json',
      contentType: false, processData: false,
      success: function (data) {
        if (data && data.ok) { onSuccess(data); }
        else { onError(data && data.error ? data.error : '요청 처리에 실패했습니다.'); }
      },
      error: function (xhr) {
        var detail = '';
        try { detail = xhr.responseText ? xhr.responseText.substring(0, 300) : ''; } catch(e) {}
        onError('서버 오류: ' + xhr.status + ' ' + xhr.statusText + (detail ? '\n' + detail : ''));
      }
    });
  }

  function showMsg(selector, msg, ok) {
    $(selector).removeClass('success error').addClass(ok ? 'success' : 'error').text(msg).show();
  }

  function reloadSoon() {
    window.setTimeout(function () { window.location.reload(); }, RELOAD_DELAY);
  }

  /* ══════════════════════════════════════════════
     잡지 날짜 위젯 — 라이브 시계
  ══════════════════════════════════════════════ */
  var clockEl = document.getElementById('retro-live-clock');
  if (clockEl) {
    function padZero(n) { return n < 10 ? '0' + n : '' + n; }
    function updateClock() {
      var now = new Date();
      clockEl.textContent = padZero(now.getHours()) + ':' + padZero(now.getMinutes()) + ':' + padZero(now.getSeconds());
    }
    updateClock();
    setInterval(updateClock, 1000);
  }

  /* ══════════════════════════════════════════════
     패럴랙스 레이어를 <body> 직속으로 이동
     — 부모 stacking context / overflow / transform 에서 완전 해방
  ══════════════════════════════════════════════ */
  (function relocateParallax() {
    var layers = document.querySelectorAll('.parallax-global-layer');
    if (!layers.length) return;

    for (var i = 0; i < layers.length; i++) {
      var layer = layers[i];

      if (layer.parentNode === document.body) continue;

      layer.parentNode.removeChild(layer);
      document.body.appendChild(layer);

      layer.style.setProperty('position', 'fixed', 'important');
      layer.style.setProperty('top',      '0',     'important');
      layer.style.setProperty('left',     '0',     'important');
      layer.style.setProperty('width',    '100vw', 'important');
      layer.style.setProperty('height',   '100vh', 'important');
      layer.style.setProperty('overflow', 'hidden','important');
      layer.style.setProperty('pointer-events', 'none', 'important');
      layer.style.setProperty('transform', 'none', 'important');
    }
  })();

  /* ══════════════════════════════════════════════
     윈도우를 retro-bg-container 밖으로 분리
     — stacking context에서 해방시켜 스티커보다 위에 표시
  ══════════════════════════════════════════════ */
  (function relocateWindows() {
    var bgContainer = document.getElementById('retro-bg-container');
    if (!bgContainer) return;

    var windows = bgContainer.querySelectorAll('.retro-draggable-window');
    if (!windows.length) return;

    /* 오버레이 div 생성 — bgContainer와 동일 위치에 절대 배치 */
    var overlay = document.createElement('div');
    overlay.id = 'retro-window-overlay';
    overlay.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;z-index:1000;pointer-events:none;';

    /* bgContainer의 부모에 position:relative 보장 */
    var parent = bgContainer.parentNode;
    if (parent) {
      var parentPos = window.getComputedStyle(parent).position;
      if (parentPos === 'static') {
        parent.style.position = 'relative';
      }
    }

    /* bgContainer 바로 뒤에 오버레이 삽입 */
    if (bgContainer.nextSibling) {
      parent.insertBefore(overlay, bgContainer.nextSibling);
    } else {
      parent.appendChild(overlay);
    }

    /* 윈도우를 오버레이로 이동 */
    for (var i = 0; i < windows.length; i++) {
      var win = windows[i];
      /* 현재 위치 보존 */
      var curTop  = win.style.top  || '0px';
      var curLeft = win.style.left || '0px';

      win.parentNode.removeChild(win);
      overlay.appendChild(win);

      win.style.position = 'absolute';
      win.style.top  = curTop;
      win.style.left = curLeft;
      win.style.pointerEvents = 'auto';
    }

    /* 오버레이 크기를 bgContainer에 동기화 */
    function syncOverlaySize() {
      overlay.style.width  = bgContainer.offsetWidth  + 'px';
      overlay.style.height = bgContainer.offsetHeight + 'px';
      /* bgContainer의 부모 내 위치도 동기화 */
      overlay.style.top  = bgContainer.offsetTop  + 'px';
      overlay.style.left = bgContainer.offsetLeft + 'px';
    }

    syncOverlaySize();

    var resizeTimer = null;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(syncOverlaySize, 100);
    });
  })();

  /* ══════════════════════════════════════════════
     관리자 모달을 <body> 직속으로 이동
     — 패럴랙스 등 모든 레이어보다 위에 표시
  ══════════════════════════════════════════════ */
  (function relocateAdminModal() {
    var modal = document.getElementById('retro-admin-modal');
    if (!modal) return;
    if (modal.parentNode === document.body) return;

    modal.parentNode.removeChild(modal);
    document.body.appendChild(modal);

    modal.style.setProperty('position', 'fixed', 'important');
    modal.style.setProperty('z-index',  '2000', 'important');
  })();

  /* ══════════════════════════════════════════════
     드래그 가능한 윈도우 (최신글 / 배너)
  ══════════════════════════════════════════════ */
  var winDragEl    = null;
  var winDragging  = false;
  var winMoved     = false;
  var winStartX    = 0;
  var winStartY    = 0;
  var winOriginL   = 0;
  var winOriginT   = 0;
  var WIN_THRESHOLD = 5;

  $(document).on('mousedown touchstart', '.retro-draggable-window > .win95-window > .win95-titlebar', function (e) {
    var isTouch = (e.type === 'touchstart');
    if (!isTouch && e.which !== 1) return;

    var clientX = isTouch ? e.originalEvent.touches[0].clientX : e.clientX;
    var clientY = isTouch ? e.originalEvent.touches[0].clientY : e.clientY;

    winDragEl  = $(this).closest('.retro-draggable-window');
    winDragging = true;
    winMoved    = false;
    winStartX   = clientX;
    winStartY   = clientY;

    var rect       = winDragEl[0].getBoundingClientRect();
    var offsetParent = winDragEl.offsetParent();
    if (!offsetParent.length) return;
    var parentRect = offsetParent[0].getBoundingClientRect();
    winOriginL     = rect.left - parentRect.left;
    winOriginT     = rect.top  - parentRect.top;

    if (!isTouch) e.preventDefault();
  });

  $(document).on('mousemove touchmove', function (e) {
    if (!winDragging || !winDragEl) return;

    var isTouch = (e.type === 'touchmove');
    var clientX = isTouch ? e.originalEvent.touches[0].clientX : e.clientX;
    var clientY = isTouch ? e.originalEvent.touches[0].clientY : e.clientY;

    var dx = clientX - winStartX;
    var dy = clientY - winStartY;

    if (!winMoved && Math.abs(dx) < WIN_THRESHOLD && Math.abs(dy) < WIN_THRESHOLD) return;

    if (!winMoved) {
      winDragEl.css({ left: winOriginL + 'px', top: winOriginT + 'px' });
      winMoved = true;
    }

    winDragEl.addClass('is-win-dragging');
    winDragEl.css({
      left: (winOriginL + dx) + 'px',
      top:  (winOriginT + dy) + 'px'
    });

    if (isTouch) e.preventDefault();
  });

  $(document).on('mouseup touchend', function () {
    if (!winDragging || !winDragEl) return;

    winDragging = false;
    winDragEl.removeClass('is-win-dragging');

    if (winMoved && IS_ADMIN) {
      var winName = '';
      var elId = winDragEl.attr('id');
      if (elId === 'retro-latest-window') winName = 'latest';
      else if (elId === 'retro-banner-window') winName = 'banner';

      if (winName) {
        var fd = new FormData();
        fd.append('action', 'save_window_pos');
        fd.append('token',  TOKEN);
        fd.append('window', winName);
        fd.append('top',  parseInt(winDragEl.css('top'),  10) || 0);
        fd.append('left', parseInt(winDragEl.css('left'), 10) || 0);
        ajaxPost(SKIN_URL + '/config_update.php', fd, function () {}, function () {});
      }
    }

    winDragEl = null;
    winMoved  = false;
  });

  /* ══════════════════════════════════════════════
     패럴랙스 마우스 효과
  ══════════════════════════════════════════════ */
  var parallaxLayers = $('.parallax-layer');
  if (parallaxLayers.length) {
    var PARALLAX_FACTORS = {
      'parallax-fg-layer': 0.045,
      'parallax-ng-layer': 0.02,
      'parallax-bg-layer': 0.01
    };

    function updateParallax(clientX, clientY) {      var centerX = window.innerWidth / 2;
      var centerY = window.innerHeight / 2;
      var deltaX = clientX - centerX;
      var deltaY = clientY - centerY;
      parallaxLayers.each(function () {
        var layer = $(this);
        var img = layer.find('img');
        if (!img.length) return;
        var factor = PARALLAX_FACTORS[this.id] || 0.02;
        var moveX = -deltaX * factor;
        var moveY = -deltaY * factor;
        var posV = layer.attr('data-pos-v') || 'center';
        var posH = layer.attr('data-pos-h') || 'center';
        var offsetX = parseInt(layer.attr('data-offset-x') || 0, 10);
        var offsetY = parseInt(layer.attr('data-offset-y') || 0, 10);
        var parts = [];
        if (posH === 'center') parts.push('translateX(-50%)');
        if (posV === 'center') parts.push('translateY(-50%)');
        parts.push('translate(' + (offsetX + moveX) + 'px,' + (offsetY + moveY) + 'px)');
        img.css('transform', parts.join(' '));
      });
    }

    var rafPending = false;
    /* 모바일(터치) 환경에서는 마우스 패럴랙스 비활성화 */
    var isMobile = window.matchMedia('(max-width: 768px)').matches || ('ontouchstart' in window);
    if (!isMobile) {
      $(document).on('mousemove.parallax', function (e) {
        if (rafPending) return;
        var cx = e.clientX, cy = e.clientY;
        rafPending = true;
        requestAnimationFrame(function () {
          updateParallax(cx, cy);
          rafPending = false;
        });
      });
    }
  }

  /* ══════════════════════════════════════════════
     이하 관리자 전용
  ══════════════════════════════════════════════ */
  if (!IS_ADMIN) return;

  /* ── 관리자 모달 ── */
  var adminModal    = $('#retro-admin-modal');
  var adminOpenBtn  = $('#retro-admin-open-btn');
  var adminCloseBtn = $('#admin-panel-close-btn');
  var adminPanel    = $('#retro-admin-panel');

  function setAdminModal(open) {
    if (!adminModal.length || !adminOpenBtn.length) return;
    adminModal.prop('hidden', !open);
    adminOpenBtn.attr('aria-expanded', open ? 'true' : 'false');
    if (open) adminPanel.trigger('focus');
    else adminOpenBtn.trigger('focus');
  }

  adminOpenBtn.on('click', function () { setAdminModal(true); });
  adminCloseBtn.on('click', function () { setAdminModal(false); });
  adminModal.on('click', '[data-admin-close]', function () { setAdminModal(false); });
  adminPanel.on('keydown', function (e) {
    if (e.key === 'Escape' && adminModal.length && !adminModal.prop('hidden')) setAdminModal(false);
  });

  /* ── 탭 전환 ── */
  $(document).on('click', '.admin-tab', function () {
    $('.admin-tab').removeClass('active');
    $(this).addClass('active');
    $('.admin-tab-pane').hide();
    $('#' + $(this).data('tab')).show();
  });

  /* ── 스티커 src_type 토글 ── */
  $('#sticker-add-form').on('change', 'input[name="src_type"]', function () {
    $('#sticker-url-rows').toggle(this.value === 'url');
    $('#sticker-file-row').toggle(this.value === 'upload');
  });
  $('#asset-add-form').on('change', 'input[name="src_type"]', function () {
    $('#asset-url-row').toggle(this.value === 'url');
    $('#asset-file-row').toggle(this.value === 'upload');
  });

  /* ══════════════════════════════════════════════
     스티커 편집 모드
  ══════════════════════════════════════════════ */
  var stickerEditMode = false;
  var stickerEditBtn  = $('#retro-sticker-edit-btn');

  function setStickerEditMode(active) {
    stickerEditMode = active;
    if (active) {
      stickerEditBtn.addClass('active').text('✏️ 편집 중...');
      $('.admin-sticker').addClass('sticker-edit-mode');
    } else {
      stickerEditBtn.removeClass('active').text('✏️ 스티커 편집');
      $('.admin-sticker').removeClass('sticker-edit-mode');
    }
  }
  if (stickerEditBtn.length) {
    stickerEditBtn.on('click', function () { setStickerEditMode(!stickerEditMode); });
  }

  /* ══════════════════════════════════════════════
     스티커 조작 (드래그 / 리사이즈 / 회전) — 통합 핸들러
  ══════════════════════════════════════════════ */
  var stDrag = null, stDragStartX = 0, stDragStartY = 0, stOriginL = 0, stOriginT = 0;
  var stResize = null, stResStartX = 0, stResStartY = 0, stResOriginW = 0, stResOriginH = 0;
  var MIN_STICKER = 20;
  var stRotate = null, stRotCX = 0, stRotCY = 0, stRotStart = 0, stRotBase = 0, stRotLive = 0;

  /* ── 스티커 드래그 mousedown ── */
  $(document).on('mousedown', '.admin-sticker.sticker-edit-mode', function (e) {
    if ($(e.target).closest('.sticker-handles').length) return;
    stDrag      = $(this);
    stDragStartX = e.clientX;
    stDragStartY = e.clientY;
    var rect       = stDrag[0].getBoundingClientRect();
    var offsetParent = stDrag.offsetParent();
    if (!offsetParent.length) return;
    var parentRect = offsetParent[0].getBoundingClientRect();
    stOriginL = rect.left - parentRect.left;
    stOriginT = rect.top  - parentRect.top;
    var rotate = parseFloat(stDrag.attr('data-rotate') || 0);
    stDrag.css({ left: stOriginL + 'px', top: stOriginT + 'px', transform: 'rotate(' + rotate + 'deg)' });
    stDrag.addClass('is-dragging');
    e.preventDefault();
  });

  /* ── 스티커 리사이즈 mousedown ── */
  $(document).on('mousedown', '.sticker-resize-handle', function (e) {
    e.stopPropagation(); e.preventDefault();
    stResize = $(this).closest('.admin-sticker');
    stResStartX = e.clientX; stResStartY = e.clientY;
    stResOriginW = stResize.outerWidth(); stResOriginH = stResize.outerHeight();
    var rect = stResize[0].getBoundingClientRect();
    var offsetParent = stResize.offsetParent();
    if (!offsetParent.length) return;
    var parentRect = offsetParent[0].getBoundingClientRect();
    var rotate = parseFloat(stResize.attr('data-rotate') || 0);
    stResize.css({ left: (rect.left - parentRect.left) + 'px', top: (rect.top - parentRect.top) + 'px', transform: 'rotate(' + rotate + 'deg)' });
  });

  /* ── 스티커 회전 mousedown ── */
  $(document).on('mousedown', '.sticker-rotate-handle', function (e) {
    e.stopPropagation(); e.preventDefault();
    stRotate = $(this).closest('.admin-sticker');
    var rect = stRotate[0].getBoundingClientRect();
    var offsetParent = stRotate.offsetParent();
    if (!offsetParent.length) return;
    var parentRect = offsetParent[0].getBoundingClientRect();
    var curRotate = parseFloat(stRotate.attr('data-rotate') || 0);
    stRotate.css({ left: (rect.left - parentRect.left) + 'px', top: (rect.top - parentRect.top) + 'px', transform: 'rotate(' + curRotate + 'deg)' });
    var r2 = stRotate[0].getBoundingClientRect();
    stRotCX = r2.left + r2.width / 2;
    stRotCY = r2.top  + r2.height / 2;
    stRotStart = Math.atan2(e.clientY - stRotCY, e.clientX - stRotCX) * 180 / Math.PI;
    stRotBase  = curRotate;
    stRotLive  = curRotate;
  });

  /* ── 통합 mousemove (드래그 + 리사이즈 + 회전) ── */
  $(document).on('mousemove.stickerop', function (e) {
    if (stDrag) {
      stDrag.css({
        left: (stOriginL + (e.clientX - stDragStartX)) + 'px',
        top:  (stOriginT + (e.clientY - stDragStartY)) + 'px'
      });
    } else if (stResize) {
      stResize.css({
        width:  Math.max(MIN_STICKER, stResOriginW + (e.clientX - stResStartX)) + 'px',
        height: Math.max(MIN_STICKER, stResOriginH + (e.clientY - stResStartY)) + 'px'
      });
    } else if (stRotate) {
      var a = Math.atan2(e.clientY - stRotCY, e.clientX - stRotCX) * 180 / Math.PI;
      stRotLive = stRotBase + (a - stRotStart);
      stRotate.css('transform', 'rotate(' + stRotLive + 'deg)');
    }
  });

  /* ── 통합 mouseup (드래그 + 리사이즈 + 회전) ── */
  $(document).on('mouseup.stickerop', function () {
    if (stDrag) {
      var id = stDrag.data('id');
      var nl = parseInt(stDrag.css('left'), 10) || 0;
      var nt = parseInt(stDrag.css('top'),  10) || 0;
      stDrag.removeClass('is-dragging');
      var fd = new FormData();
      fd.append('action', 'move_sticker'); fd.append('token', TOKEN); fd.append('id', id);
      fd.append('left', nl + 'px'); fd.append('top', nt + 'px');
      ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {}, function () {});
      stDrag = null;
    } else if (stResize) {
      var fd = new FormData();
      fd.append('action', 'update_sticker'); fd.append('token', TOKEN); fd.append('id', stResize.data('id'));
      fd.append('width', stResize.outerWidth() + 'px'); fd.append('height', stResize.outerHeight() + 'px');
      fd.append('left', (parseInt(stResize.css('left'), 10) || 0) + 'px');
      fd.append('top',  (parseInt(stResize.css('top'),  10) || 0) + 'px');
      ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {}, function () {});
      stResize = null;
    } else if (stRotate) {
      var finalAngle = Math.round(stRotLive * 100) / 100;
      stRotate.attr('data-rotate', finalAngle);
      var fd = new FormData();
      fd.append('action', 'update_sticker'); fd.append('token', TOKEN); fd.append('id', stRotate.data('id'));
      fd.append('rotate', finalAngle);
      fd.append('left', (parseInt(stRotate.css('left'), 10) || 0) + 'px');
      fd.append('top',  (parseInt(stRotate.css('top'),  10) || 0) + 'px');
      ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {}, function () {});
      stRotate = null;
    }
  });

  /* ── Z-index ── */
  function saveStickerZ(id, z) {
    var fd = new FormData();
    fd.append('action', 'update_sticker'); fd.append('token', TOKEN); fd.append('id', id); fd.append('z_index', z);
    ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {}, function () {});
  }
  $(document).on('click', '.sticker-zup-btn', function (e) {
    e.stopPropagation();
    var s = $(this).closest('.admin-sticker');
    var z = Math.min(9999, (parseInt(s.attr('data-z-index') || s.css('z-index') || 1, 10)) + 1);
    s.css('z-index', z).attr('data-z-index', z);
    saveStickerZ(s.data('id'), z);
  });
  $(document).on('click', '.sticker-zdown-btn', function (e) {
    e.stopPropagation();
    var s = $(this).closest('.admin-sticker');
    var z = Math.max(1, (parseInt(s.attr('data-z-index') || s.css('z-index') || 1, 10)) - 1);
    s.css('z-index', z).attr('data-z-index', z);
    saveStickerZ(s.data('id'), z);
  });

  /* ── 스티커 삭제 ── */
  $(document).on('click', '.sticker-del-btn, .admin-sticker-delete', function (e) {
    e.stopPropagation();
    var id = $(this).data('id');
    if (!id || !window.confirm('이 스티커를 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('action', 'delete_sticker'); fd.append('token', TOKEN); fd.append('id', id);
    ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {
      $('#sticker-' + id).remove(); $('#admin-item-' + id).remove();
      showMsg('#sticker-edit-msg', '스티커가 삭제되었습니다.', true);
    }, function (msg) { showMsg('#sticker-edit-msg', msg, false); });
  });

  /* ── 스티커 추가 ── */
  $('#sticker-add-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/sticker_update.php', new FormData(this), function () {
      showMsg('#sticker-add-msg', '스티커가 추가되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#sticker-add-msg', msg, false); });
  });
  $('#admin-sticker-list').on('submit', '.sticker-edit-form', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/sticker_update.php', new FormData(this), function () {
      showMsg('#sticker-edit-msg', '스티커가 저장되었습니다.', true);
    }, function (msg) { showMsg('#sticker-edit-msg', msg, false); });
  });

  /* ── 에셋 ── */
  $('#asset-add-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/sticker_update.php', new FormData(this), function () {
      showMsg('#asset-add-msg', '에셋이 저장되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#asset-add-msg', msg, false); });
  });
  $(document).on('click', '.asset-place-btn', function () {
    var id = $(this).data('id'); if (!id) return;
    var fd = new FormData();
    fd.append('action', 'place_asset'); fd.append('token', TOKEN); fd.append('id', id);
    ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {
      showMsg('#asset-msg', '스티커로 배치되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#asset-msg', msg, false); });
  });
  $(document).on('click', '.asset-del-btn', function () {
    var id = $(this).data('id');
    if (!id || !window.confirm('이 에셋을 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('action', 'delete_asset'); fd.append('token', TOKEN); fd.append('id', id);
    ajaxPost(SKIN_URL + '/sticker_update.php', fd, function () {
      $('#admin-asset-' + id).remove();
      showMsg('#asset-msg', '에셋이 삭제되었습니다.', true);
    }, function (msg) { showMsg('#asset-msg', msg, false); });
  });

  /* ══════════════════════════════════════════════
     배경 이미지 + 크롭
  ══════════════════════════════════════════════ */
  var cropData = { active: false, startX: 0, startY: 0, x: 0, y: 0, w: 0, h: 0 };

  $('#bg-file-input').on('change', function () {
    var file = this.files[0];
    if (!file) { $('#bg-crop-container').hide(); return; }
    var ext = file.name.split('.').pop().toLowerCase();
    if (ext === 'mp4') {
      $('#bg-crop-container').hide();
      $('#bg-cropped-data').val('');
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#bg-crop-preview').attr('src', e.target.result);
      $('#bg-crop-container').show();
      $('#bg-crop-selection').hide();
      $('#bg-crop-info').text('선택 안 됨');
      $('#bg-cropped-data').val('');
      cropData = { active: false, startX: 0, startY: 0, x: 0, y: 0, w: 0, h: 0 };
    };
    reader.readAsDataURL(file);
  });

  var cropDragging = false;
  $('#bg-crop-wrapper').on('mousedown', function (e) {
    if (e.target.id === 'bg-crop-selection') return;
    var offset = $(this).offset();
    cropData.startX = e.pageX - offset.left;
    cropData.startY = e.pageY - offset.top;
    cropDragging = true;
    $('#bg-crop-selection').css({ left: cropData.startX, top: cropData.startY, width: 0, height: 0 }).show();
    e.preventDefault();
  });
  $(document).on('mousemove.bgcrop', function (e) {
    if (!cropDragging) return;
    var offset = $('#bg-crop-wrapper').offset();
    var cx = e.pageX - offset.left;
    var cy = e.pageY - offset.top;
    var x = Math.min(cropData.startX, cx);
    var y = Math.min(cropData.startY, cy);
    var w = Math.abs(cx - cropData.startX);
    var h = Math.abs(cy - cropData.startY);
    $('#bg-crop-selection').css({ left: x, top: y, width: w, height: h });
    cropData.x = x; cropData.y = y; cropData.w = w; cropData.h = h;
    $('#bg-crop-info').text(Math.round(w) + ' × ' + Math.round(h) + ' px (미리보기 기준)');
  });
  $(document).on('mouseup.bgcrop', function () { cropDragging = false; });

  $('#bg-crop-apply').on('click', function () {
    if (cropData.w < 10 || cropData.h < 10) { alert('크롭 영역을 먼저 선택해 주세요.'); return; }
    var img = $('#bg-crop-preview')[0];
    var scaleX = img.naturalWidth  / img.width;
    var scaleY = img.naturalHeight / img.height;
    var sx = Math.round(cropData.x * scaleX);
    var sy = Math.round(cropData.y * scaleY);
    var sw = Math.round(cropData.w * scaleX);
    var sh = Math.round(cropData.h * scaleY);
    var canvas = document.createElement('canvas');
    canvas.width = sw; canvas.height = sh;
    canvas.getContext('2d').drawImage(img, sx, sy, sw, sh, 0, 0, sw, sh);
    $('#bg-cropped-data').val(canvas.toDataURL('image/png'));
    $('#bg-crop-info').text('크롭 적용됨 (' + sw + ' × ' + sh + ' px)');
    showMsg('#config-bg-msg', '크롭이 적용되었습니다. "배경 이미지 저장"을 눌러 주세요.', true);
  });

  $('#bg-crop-reset').on('click', function () {
    $('#bg-crop-selection').hide();
    $('#bg-cropped-data').val('');
    $('#bg-crop-info').text('선택 안 됨');
    cropData = { active: false, startX: 0, startY: 0, x: 0, y: 0, w: 0, h: 0 };
  });

  $('#config-bg-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/config_update.php', new FormData(this), function () {
      showMsg('#config-bg-msg', '배경 이미지가 저장되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#config-bg-msg', msg, false); });
  });

  $('#bg-del-btn').on('click', function () {
    if (!window.confirm('배경 이미지를 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('action', 'delete_bg'); fd.append('token', TOKEN);
    ajaxPost(SKIN_URL + '/config_update.php', fd, function () {
      showMsg('#config-bg-msg', '배경 이미지가 삭제되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#config-bg-msg', msg, false); });
  });

  /* ══════════════════════════════════════════════
     텍스트 오버레이 설정 저장
  ══════════════════════════════════════════════ */
  $('#config-text-form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    ajaxPost(SKIN_URL + '/config_update.php', formData, function (data) {
      var msg = '텍스트   정이 저장되었습니다.';
      if (data.dir_writable === false) {
        msg += ' [경고: 저장 디렉토리 쓰기 권한 없음]';
      }
      showMsg('#config-text-msg', msg, true);
      reloadSoon();
    }, function (msg) {
      showMsg('#config-text-msg', msg, false);
    });
  });

  /* ── 커스텀 폰트 ── */
  /* ── CSS 코드로 폰트 추가 ── */
  $('#font-code-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/config_update.php', new FormData(this), function () {
      showMsg('#font-code-msg', '폰트가 추가되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#font-code-msg', msg, false); });
  });
  $('#font-path-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/config_update.php', new FormData(this), function () {
      showMsg('#font-path-msg', '서버 폰트가 등록되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#font-path-msg', msg, false); });
  });
  $('#font-upload-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/config_update.php', new FormData(this), function () {
      showMsg('#font-upload-msg', '폰트가 업로드되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#font-upload-msg', msg, false); });
  });
  $(document).on('click', '.font-del-btn', function () {
    var id = $(this).data('id');
    if (!id || !window.confirm('이 폰트를 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('action', 'delete_font'); fd.append('token', TOKEN); fd.append('font_id', id);
    ajaxPost(SKIN_URL + '/config_update.php', fd, function () {
      $('#admin-font-' + id).remove();
      showMsg('#font-msg', '폰트가 삭제되었습니다.', true);
    }, function (msg) { showMsg('#font-msg', msg, false); });
  });

  /* ── 최신글/배너 창 설정 ── */
  $('#config-window-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/config_update.php', new FormData(this), function () {
      showMsg('#config-window-msg', '창 설정이 저장되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#config-window-msg', msg, false); });
  });

  /* ── 배너 CRUD ── */
  $('#banner-add-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/banner_update.php', new FormData(this), function () {
      showMsg('#banner-add-msg', '배너가 추가되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#banner-add-msg', msg, false); });
  });
  $('#admin-banner-list').on('submit', '.banner-edit-form', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/banner_update.php', new FormData(this), function () {
      showMsg('#banner-add-msg', '배너가 저장되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#banner-add-msg', msg, false); });
  });
  $('#admin-banner-list').on('click', '.banner-del-btn', function () {
    if (!window.confirm('이 배너를 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('action', 'delete_banner'); fd.append('token', TOKEN); fd.append('index', $(this).data('index'));
    ajaxPost(SKIN_URL + '/banner_update.php', fd, function () {
      showMsg('#banner-add-msg', '배너가 삭제되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#banner-add-msg', msg, false); });
  });

  /* ── 패럴랙스 설정 ── */
  $('#config-parallax-form').on('submit', function (e) {
    e.preventDefault();
    ajaxPost(SKIN_URL + '/config_update.php', new FormData(this), function () {
      showMsg('#config-parallax-msg', '패럴랙스 설정이 저장되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#config-parallax-msg', msg, false); });
  });
  $(document).on('click', '.parallax-del-btn', function () {
    var layer = $(this).data('layer');
    if (!layer || !window.confirm('이 레이어 이미지를 삭제하시겠습니까?')) return;
    var fd = new FormData();
    fd.append('action', 'delete_parallax_image'); fd.append('token', TOKEN); fd.append('layer', layer);
    ajaxPost(SKIN_URL + '/config_update.php', fd, function () {
      showMsg('#config-parallax-msg', '이미지가 삭제되었습니다.', true); reloadSoon();
    }, function (msg) { showMsg('#config-parallax-msg', msg, false); });
  });

}(jQuery));
