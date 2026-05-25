/* global S2M, jQuery */
(function ($) {
    'use strict';

    var selectedFile = null;

    // ── File selection ────────────────────────────────────────
    $('#s2m-file-input').on('change', function () {
        if (this.files && this.files[0]) {
            selectedFile = this.files[0];
            showFileInfo(selectedFile);
        }
    });

    // Drag & drop
    var $dropZone = $('#s2m-drop-zone');

    $dropZone.on('dragover dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $dropZone.addClass('drag-over');
    });

    $dropZone.on('dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $dropZone.removeClass('drag-over');
    });

    $dropZone.on('drop', function (e) {
        var dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files[0]) {
            selectedFile = dt.files[0];
            showFileInfo(selectedFile);
        }
    });

    $dropZone.on('click', function () {
        $('#s2m-file-input').trigger('click');
    });

    function showFileInfo(file) {
        var ext = file.name.split('.').pop().toLowerCase();
        var allowed = ['sqlite', 'sqlite3', 'db', 'sdb'];
        if (allowed.indexOf(ext) === -1) {
            setStatus('s2m-upload-status', 'is-error',
                '&#10007; Invalid file type: .' + ext + '. Allowed: .sqlite .sqlite3 .db .sdb');
            $('#s2m-btn-upload').prop('disabled', true);
            return;
        }
        setStatus('s2m-upload-status', 'is-info',
            '<strong>' + escHtml(file.name) + '</strong> &nbsp;(' + formatBytes(file.size) + ') ready to upload.');
        $('#s2m-btn-upload').prop('disabled', false);
    }

    // ── Auto-detect Current Database ──────────────────────────
    $('#s2m-btn-detect').on('click', function () {
        var $btn = $(this).prop('disabled', true);
        setStatus('s2m-upload-status', 'is-info', '<span class="s2m-spinner"></span> Detecting database...');

        $.post(S2M.ajax_url, { action: 's2m_detect', nonce: S2M.nonce })
        .done(function (res) {
            if (!res.success) {
                setStatus('s2m-upload-status', 'is-error', '&#10007; ' + escHtml(res.data.message));
                $btn.prop('disabled', false);
                return;
            }
            setStatus('s2m-upload-status', 'is-success', '&#10003; ' + escHtml(res.data.message));
            analyzeDatabase($btn);
        })
        .fail(ajaxFail.bind(null, 's2m-upload-status', $btn));
    });

    // ── Upload & Analyze ──────────────────────────────────────
    $('#s2m-btn-upload').on('click', function () {
        if (!selectedFile) return;

        var $btn = $(this).prop('disabled', true);
        setStatus('s2m-upload-status', 'is-info', '<span class="s2m-spinner"></span> ' + S2M.strings.uploading);

        var fd = new FormData();
        fd.append('action', 's2m_upload');
        fd.append('nonce',  S2M.nonce);
        fd.append('sqlite_file', selectedFile);

        $.ajax({
            url:         S2M.ajax_url,
            type:        'POST',
            data:        fd,
            processData: false,
            contentType: false,
        })
        .done(function (res) {
            if (!res.success) {
                setStatus('s2m-upload-status', 'is-error', '&#10007; ' + escHtml(res.data.message));
                $btn.prop('disabled', false);
                return;
            }
            setStatus('s2m-upload-status', 'is-success', '&#10003; ' + escHtml(res.data.message));
            analyzeDatabase($btn);
        })
        .fail(ajaxFail.bind(null, 's2m-upload-status', $btn));
    });

    function analyzeDatabase($btn) {
        // Now analyze
        setStatus('s2m-upload-status', 'is-info',
            '<span class="s2m-spinner"></span> ' + S2M.strings.analyzing);

        $.post(S2M.ajax_url, { action: 's2m_analyze', nonce: S2M.nonce })
        .done(function (r2) {
            if (!r2.success) {
                setStatus('s2m-upload-status', 'is-error', '&#10007; ' + escHtml(r2.data.message));
                $btn.prop('disabled', false);
                return;
            }
            setStatus('s2m-upload-status', 'is-success',
                '&#10003; Found <strong>' + r2.data.tables.length + '</strong> table(s).');
            renderTableList(r2.data.tables);
            $('#s2m-step-2, #s2m-step-3').slideDown(300);
        })
        .fail(ajaxFail.bind(null, 's2m-upload-status', $btn));
    }

    // ── Select all checkbox ───────────────────────────────────
    $(document).on('change', '#s2m-check-all', function () {
        $('#s2m-table-body input[type=checkbox]').prop('checked', $(this).is(':checked'));
    });

    // ── Run migration ─────────────────────────────────────────
    $('#s2m-btn-migrate').on('click', function () {
        var tables = [];
        $('#s2m-table-body input[type=checkbox]:checked').each(function () {
            tables.push($(this).val());
        });

        if (tables.length === 0) {
            alert('Please select at least one table to migrate.');
            return;
        }

        var dropExisting = $('#s2m-drop-existing').is(':checked');
        if (dropExisting && !confirm(S2M.strings.confirm)) {
            return;
        }

        var $btn = $(this).prop('disabled', true);

        $('#s2m-progress-card').slideDown(200);
        setProgress(10, S2M.strings.migrating);

        var data = {
            action:         's2m_migrate',
            nonce:          S2M.nonce,
            tables:         tables,
            drop_existing:  dropExisting ? 1 : 0,
            table_prefix:   $('#s2m-prefix').val(),
            batch_size:     $('#s2m-batch-size').val(),
        };

        // Simulate progress while waiting
        var progress = 10;
        var ticker = setInterval(function () {
            if (progress < 85) {
                progress += Math.random() * 8;
                setProgress(Math.min(progress, 85), S2M.strings.migrating);
            }
        }, 600);

        $.post(S2M.ajax_url, data)
        .done(function (res) {
            clearInterval(ticker);
            setProgress(100, S2M.strings.done);

            if (!res.success) {
                showResult(null, res.data.message);
                $btn.prop('disabled', false);
                return;
            }
            showResult(res.data, null);
        })
        .fail(function () {
            clearInterval(ticker);
            setProgress(0, S2M.strings.error);
            showResult(null, S2M.strings.error);
            $btn.prop('disabled', false);
        });
    });

    // ── Clear log (history page) ──────────────────────────────
    $(document).on('click', '#s2m-clear-log', function () {
        if (!confirm('Clear all migration history?')) return;
        $.post(S2M.ajax_url, { action: 's2m_clear_log', nonce: S2M.nonce })
        .done(function () { location.reload(); });
    });

    // ── Helpers ───────────────────────────────────────────────

    function renderTableList(tables) {
        var html = '';
        tables.forEach(function (t) {
            html += '<tr>';
            html += '<td><input type="checkbox" name="tables[]" value="' + escHtml(t.table) + '" checked></td>';
            html += '<td><strong>' + escHtml(t.table) + '</strong></td>';
            html += '<td>' + t.columns + '</td>';
            html += '<td>' + numberFormat(t.rows) + '</td>';
            html += '</tr>';
        });
        $('#s2m-table-body').html(html);
        $('#s2m-check-all').prop('checked', true);
    }

    function setStatus(id, cls, msg) {
        var $el = $('#' + id);
        $el.removeClass('is-info is-success is-error').addClass(cls).html(msg).show();
    }

    function setProgress(pct, label) {
        $('#s2m-progress-fill').css('width', pct + '%');
        $('#s2m-progress-label').html(label);
    }

    function showResult(data, errorMsg) {
        var html = '';
        if (errorMsg) {
            html = '<div class="notice notice-error inline"><p>&#10007; ' + escHtml(errorMsg) + '</p></div>';
        } else {
            var s = data.stats;
            html += '<div class="s2m-result-stats">';
            html += statBox(s.tables_created, 'Tables Created');
            html += statBox(numberFormat(s.rows_inserted), 'Rows Inserted');
            html += statBox(s.tables_skipped, 'Tables Skipped');
            html += '</div>';

            if (data.errors && data.errors.length) {
                html += '<div class="notice notice-error inline"><p><strong>Errors:</strong></p><ul>';
                data.errors.forEach(function (e) { html += '<li>' + escHtml(e) + '</li>'; });
                html += '</ul></div>';
            }
            if (data.warnings && data.warnings.length) {
                html += '<div class="notice notice-warning inline"><p><strong>Warnings:</strong></p><ul>';
                data.warnings.forEach(function (w) { html += '<li>' + escHtml(w) + '</li>'; });
                html += '</ul></div>';
            }
            if (!data.errors || !data.errors.length) {
                html += '<div class="notice notice-success inline"><p>&#10003; Migration completed successfully.</p></div>';
            }
        }
        $('#s2m-result-content').html(html);
        $('#s2m-result-card').slideDown(300);
        $('html, body').animate({ scrollTop: $('#s2m-result-card').offset().top - 60 }, 400);
    }

    function statBox(num, lbl) {
        return '<div class="s2m-result-stat"><div class="num">' + num +
               '</div><div class="lbl">' + escHtml(lbl) + '</div></div>';
    }

    function ajaxFail(statusId, $btn) {
        setStatus(statusId, 'is-error', '&#10007; ' + S2M.strings.error);
        if ($btn) $btn.prop('disabled', false);
    }

    function escHtml(str) {
        return $('<div>').text(String(str)).html();
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function numberFormat(n) {
        return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

}(jQuery));
