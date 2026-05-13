/* global mlbkpData, jQuery */
(function ($) {
    'use strict';

    // ── Hilfsfunktionen ───────────────────────────────────────────────────────

    function setStatus($el, message, type) {
        $el.removeClass('mlb-status-ok mlb-status-error mlb-status-info')
            .addClass('mlb-status-' + type)
            .text(message)
            .show();
    }

    function appendLog(line) {
        const $log = $('#mlb-log-output');
        $log.append($('<div class="mlb-log-line"></div>').text(line));
        $log.scrollTop($log[0].scrollHeight);
    }

    // ── Einstellungen speichern ───────────────────────────────────────────────

    $('#mlb-settings-form').on('submit', function (e) {
        e.preventDefault();

        const $btn    = $('#mlb-save-settings').prop('disabled', true);
        const $status = $('#mlb-save-status');

        setStatus($status, mlbkpData.strings.saving, 'info');

        $.ajax({
            url:    mlbkpData.ajaxUrl,
            method: 'POST',
            data:   $(this).serialize() + '&action=mlbkp_save_settings&nonce=' + mlbkpData.nonce,
            success: function (res) {
                if (res.success) {
                    setStatus($status, res.data.message, 'ok');
                    if (res.data.next_run) {
                        // Nächsten Backup-Zeitpunkt im Status-Banner aktualisieren
                        $('.mlb-status-item:first strong').text(res.data.next_run);
                    }
                } else {
                    setStatus($status, res.data.message || 'Fehler.', 'error');
                }
            },
            error: function () {
                setStatus($status, 'Verbindungsfehler.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
            },
        });
    });

    // Toggle-Karten aktiv markieren
    $(document).on('change', '.mlb-toggle-card input[type="checkbox"]', function () {
        $(this).closest('.mlb-toggle-card').toggleClass('active', this.checked);
    });

    // Wochentag-Feld ein-/ausblenden
    $('#schedule').on('change', function () {
        $('#mlb-field-day').toggle($(this).val() === 'weekly');
    });

    // ── SFTP-Verbindung testen ────────────────────────────────────────────────

    $('#mlb-test-connection').on('click', function () {
        const $btn    = $(this).prop('disabled', true);
        const $status = $('#mlb-connection-status');

        setStatus($status, mlbkpData.strings.testing, 'info');

        $.ajax({
            url:    mlbkpData.ajaxUrl,
            method: 'POST',
            data: {
                action:        'mlbkp_test_connection',
                nonce:         mlbkpData.nonce,
                sftp_host:     $('#sftp_host').val(),
                sftp_port:     $('#sftp_port').val(),
                sftp_username: $('#sftp_username').val(),
                sftp_password: $('#sftp_password').val(),
                sftp_path:     $('#sftp_path').val(),
            },
            success: function (res) {
                if (res.success) {
                    setStatus($status, mlbkpData.strings.conn_success, 'ok');
                } else {
                    setStatus($status, mlbkpData.strings.conn_error + (res.data.message || ''), 'error');
                }
            },
            error: function () {
                setStatus($status, 'Verbindungsfehler.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
            },
        });
    });

    // ── Backup starten ────────────────────────────────────────────────────────

    $('#mlb-start-backup').on('click', function () {
        const $btn      = $(this).prop('disabled', true).text('⏳ Läuft …');
        const $status   = $('#mlb-run-status');
        const $logCard  = $('#mlb-log-card').show();
        const $logOut   = $('#mlb-log-output').empty();
        const backupType = $('input[name="backup_type"]:checked').val() || 'full';

        setStatus($status, mlbkpData.strings.running, 'info');

        appendLog('[' + new Date().toLocaleTimeString('de-AT') + '] Backup wird gestartet …');

        $.ajax({
            url:     mlbkpData.ajaxUrl,
            method:  'POST',
            timeout: 0, // Kein Timeout – Backup kann lange dauern
            data: {
                action:      'mlbkp_run_backup',
                nonce:       mlbkpData.nonce,
                backup_type: backupType,
            },
            success: function (res) {
                // Server-Log-Zeilen ausgeben
                if (res.data && Array.isArray(res.data.log)) {
                    $logOut.empty();
                    res.data.log.forEach(function (line) {
                        appendLog(line);
                    });
                }

                if (res.success) {
                    setStatus($status, mlbkpData.strings.success, 'ok');
                    $logCard.addClass('mlb-log-success');
                } else {
                    setStatus($status, mlbkpData.strings.error + ' ' + (res.data.message || ''), 'error');
                    $logCard.addClass('mlb-log-error');
                }
            },
            error: function (xhr, status) {
                if (status === 'timeout') {
                    appendLog('[!] Timeout — Das Backup könnte im Hintergrund noch laufen. Bitte Protokoll prüfen.');
                    setStatus($status, '⚠ Timeout — Bitte Protokoll prüfen.', 'error');
                } else {
                    appendLog('[!] AJAX-Fehler: ' + status);
                    setStatus($status, 'AJAX-Fehler: ' + status, 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).text('▶ Backup starten');
            },
        });
    });

    // ── Backup-Typ-Karten ─────────────────────────────────────────────────────

    $(document).on('change', '.mlb-type-option input[type="radio"]', function () {
        $('.mlb-type-card').removeClass('active');
        $(this).closest('.mlb-type-option').find('.mlb-type-card').addClass('active');
    });

    // Initial aktive Karte markieren
    $('input[name="backup_type"]:checked').closest('.mlb-type-option').find('.mlb-type-card').addClass('active');

}(jQuery));
