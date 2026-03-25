<?php
/**
 * Template: Buchungsformular
 *
 * Wird via Shortcode [mlb_booking_form] eingebunden.
 * Variablen aus MLB_Shortcode::render() verfügbar:
 *   $atts                – Shortcode-Attribute
 *   $locations           – Array von WP_Post (mlb_location)
 *   $preset_location_id  – Vorausgewählter Standort (0 = keiner)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$wrapper_class = 'mlb-booking-form' . ( ! empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '' );
$form_id       = 'mlb-form-' . wp_unique_id();
?>

<div class="<?php echo esc_attr( $wrapper_class ); ?>" id="<?php echo esc_attr( $form_id ); ?>" data-form-id="<?php echo esc_attr( $form_id ); ?>">

    <?php if ( ! empty( $atts['title'] ) ) : ?>
        <h2 class="mlb-booking-form__title"><?php echo esc_html( $atts['title'] ); ?></h2>
    <?php endif; ?>

    <form class="mlb-form" novalidate>

        <?php wp_nonce_field( 'mlb_nonce', 'mlb_nonce_field' ); ?>

        <!-- ── Schritt 1: Standort ──────────────────────────────────────── -->
        <div class="mlb-form__step mlb-form__step--location <?php echo $preset_location_id ? 'mlb-form__step--hidden' : ''; ?>">
            <div class="mlb-form__field">
                <label for="<?php echo esc_attr( $form_id ); ?>-location" class="mlb-form__label mlb-form__label--required">
                    Standort wählen
                </label>
                <select
                    id="<?php echo esc_attr( $form_id ); ?>-location"
                    name="location_id"
                    class="mlb-form__select mlb-location-select"
                    <?php echo $preset_location_id ? 'data-preset="' . esc_attr( $preset_location_id ) . '"' : 'required'; ?>
                    <?php echo $preset_location_id ? 'data-value="' . esc_attr( $preset_location_id ) . '"' : ''; ?>
                >
                    <?php if ( ! $preset_location_id ) : ?>
                        <option value="">Bitte wählen…</option>
                    <?php endif; ?>
                    <?php foreach ( $locations as $loc ) : ?>
                        <option value="<?php echo esc_attr( $loc->ID ); ?>"<?php selected( $preset_location_id, $loc->ID ); ?>>
                            <?php echo esc_html( $loc->post_title ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if ( $preset_location_id ) : ?>
            <input type="hidden" name="location_id" value="<?php echo esc_attr( $preset_location_id ); ?>">
        <?php endif; ?>

        <!-- ── Schritt 2: Datum ─────────────────────────────────────────── -->
        <div class="mlb-form__step mlb-form__step--datetime">
            <div class="mlb-form__row">

                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-date" class="mlb-form__label mlb-form__label--required">
                        Datum
                    </label>
                    <input
                        type="text"
                        id="<?php echo esc_attr( $form_id ); ?>-date"
                        name="date"
                        class="mlb-form__input mlb-date-picker"
                        placeholder="Datum wählen"
                        autocomplete="off"
                        readonly
                        required
                    >
                </div>

                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-time" class="mlb-form__label mlb-form__label--required">
                        Uhrzeit
                    </label>
                    <select
                        id="<?php echo esc_attr( $form_id ); ?>-time"
                        name="time"
                        class="mlb-form__select mlb-time-select"
                        required
                        disabled
                    >
                        <option value="">Bitte zuerst Datum wählen</option>
                    </select>
                    <div class="mlb-slots-info"></div>
                </div>

            </div>
        </div>

        <!-- ── Schritt 3: Dienstleistung ───────────────────────────────── -->
        <div class="mlb-form__step mlb-form__step--service">
            <div class="mlb-form__row">

                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-service" class="mlb-form__label">
                        Dienstleistung
                    </label>
                    <select
                        id="<?php echo esc_attr( $form_id ); ?>-service"
                        name="service"
                        class="mlb-form__select mlb-service-select"
                    >
                        <option value="">Bitte zuerst Standort wählen</option>
                    </select>
                </div>

                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-persons" class="mlb-form__label mlb-form__label--required">
                        Personenanzahl
                    </label>
                    <input
                        type="number"
                        id="<?php echo esc_attr( $form_id ); ?>-persons"
                        name="persons"
                        class="mlb-form__input"
                        value="1"
                        min="1"
                        max="99"
                        required
                    >
                </div>

            </div>
        </div>

        <!-- ── Schritt 4: Kontaktdaten ──────────────────────────────────── -->
        <div class="mlb-form__step mlb-form__step--contact">

            <div class="mlb-form__row mlb-form__row--3">
                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-name" class="mlb-form__label mlb-form__label--required">
                        Vor- und Nachname
                    </label>
                    <input
                        type="text"
                        id="<?php echo esc_attr( $form_id ); ?>-name"
                        name="name"
                        class="mlb-form__input"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-email" class="mlb-form__label mlb-form__label--required">
                        E-Mail-Adresse
                    </label>
                    <input
                        type="email"
                        id="<?php echo esc_attr( $form_id ); ?>-email"
                        name="email"
                        class="mlb-form__input"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="mlb-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-phone" class="mlb-form__label">
                        Telefon
                    </label>
                    <input
                        type="tel"
                        id="<?php echo esc_attr( $form_id ); ?>-phone"
                        name="phone"
                        class="mlb-form__input"
                        autocomplete="tel"
                    >
                </div>
            </div>

            <div class="mlb-form__field">
                <label for="<?php echo esc_attr( $form_id ); ?>-notes" class="mlb-form__label">
                    Anmerkungen
                </label>
                <textarea
                    id="<?php echo esc_attr( $form_id ); ?>-notes"
                    name="notes"
                    class="mlb-form__textarea"
                    rows="4"
                    placeholder="Haben Sie besondere Wünsche oder Anmerkungen?"
                ></textarea>
            </div>

        </div>

        <!-- ── Submit ───────────────────────────────────────────────────── -->
        <div class="mlb-form__submit">
            <button type="submit" class="mlb-form__button">
                <span class="mlb-form__button-text">Buchung anfragen</span>
                <span class="mlb-form__button-spinner" aria-hidden="true"></span>
            </button>
            <p class="mlb-form__privacy">
                Mit dem Absenden stimmen Sie der Verarbeitung Ihrer Daten gemäß unserer
                <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Datenschutzerklärung</a> zu.
            </p>
        </div>

    </form>

    <!-- ── Erfolgsmeldung ──────────────────────────────────────────────── -->
    <div class="mlb-form__success" hidden>
        <div class="mlb-form__success-icon" aria-hidden="true">✓</div>
        <h3 class="mlb-form__success-title">Buchung eingereicht!</h3>
        <p class="mlb-form__success-message"></p>
    </div>

    <!-- ── Fehlermeldung (global) ──────────────────────────────────────── -->
    <div class="mlb-form__error-global" role="alert" hidden>
        <p class="mlb-form__error-message"></p>
    </div>

</div>
