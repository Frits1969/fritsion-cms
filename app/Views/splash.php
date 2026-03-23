<!DOCTYPE html>
<html lang="<?= $selectedLang ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <title>Fritsion CMS</title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .logo-container,
        .text-container {
            transition: opacity 0.3s ease;
        }

        img.logo {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .pieces-logo {
            display: none !important;
        }

        .text {
            font-size: 20px;
            color: #2d1f5e;
            margin-bottom: 20px;
        }

        .lang-select {
            display: flex;
            gap: 20px;
            transition: all 0.5s ease;
        }

        .lang-select a {
            text-decoration: none;
            display: block;
            transition: transform 0.2s ease, opacity 0.3s ease;
        }

        .lang-select a:hover {
            transform: scale(1.1);
        }

        .flag-icon {
            width: 50px;
            height: 35px;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: block;
        }

        .version-footer {
            position: fixed;
            bottom: 10px;
            right: 15px;
            font-size: 12px;
            color: #ccc;
            font-family: monospace;
        }

        /* Selected State Styling */
        body.selected-mode {
            justify-content: flex-start;
            padding-top: 20px;
            /* Aligned with language flag top (20px) */
        }

        body.selected-mode .splash-logo {
            display: none;
        }

        body.selected-mode .pieces-logo {
            display: block !important;
            position: absolute;
            top: 0;
            /* Align with the top of the blue piece */
            left: 50%;
            transform: translateX(-50%);
            margin-bottom: 0;
            z-index: 100;
            width: 200px;
            text-align: center;
        }

        body.selected-mode .pieces-logo img {
            max-width: 100%;
        }

        body.selected-mode .text-container {
            display: none;
            opacity: 0;
            pointer-events: none;
        }

        body.selected-mode .lang-select {
            position: absolute;
            top: 20px;
            right: 20px;
            flex-direction: column;
            /* Stack flags vertically when expanded or simple layout */
            gap: 10px;
        }

        body.selected-mode .lang-select a {
            display: none;
        }

        /* Always show active flag */
        body.selected-mode .lang-select a.active {
            display: block;
            opacity: 1;
            order: -1;
            /* Ensure active flag is top */
        }

        /* Show all flags when expanded */
        body.selected-mode .lang-select.expanded a {
            display: block;
            opacity: 0.8;
        }

        body.selected-mode .lang-select.expanded a:hover {
            opacity: 1;
        }

        .multistep-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            /* Switch to top alignment */
            gap: 0;
            width: 100%;
            margin-top: 0;
            padding: 0 30px;
            box-sizing: border-box;
            position: relative;
        }

        .puzzle-focus-group {
            display: flex;
            align-items: flex-start;
            /* Top alignment for logo anchor */
            position: relative;
            gap: 0;
            height: auto;
            /* Remove fixed height */
        }

        body.step-3-mode .multistep-wrapper {
            padding: 0;
            /* Step 3 has a flat right edge, so no compensation needed */
        }

        /* Puzzle Piece */
        .puzzle-piece {
            position: relative;
            padding: 40px;
            color: #fff;
            border-radius: 15px;
            width: 450px;
            flex-shrink: 0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.5s ease;
            min-height: 600px;
            margin-top: 220px;
            /* Start orange and green 220px lower */
        }

        /* Orange Piece (Step 1) */
        .puzzle-piece.orange {
            background-color: #F0961B;
            z-index: 2;
        }

        /* Pink Piece (Step 2) */
        .puzzle-piece.green {
            background-color: #E8186A;
            display: none;
            opacity: 0;
            z-index: 1;
        }

        /* Purple Piece (Step 3) */
        .puzzle-piece.blue {
            background-color: #3B2A8C;
            /* Fritsion logo paars */
            display: none;
            opacity: 0;
            z-index: 0;
            min-height: 720px;
            margin-top: 38px;
            /* Blue starts 38px lower than logo/flags */
        }

        /* Puzzle "Outies" (Knobs) */
        /* Orange Piece: Connection on Right */
        .puzzle-piece.orange::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            background-color: #F0961B;
            border-radius: 50%;
            right: -30px;
            top: 50%;
            transform: translateY(-50%);
            box-shadow: 1px 0 0 #F0961B;
            z-index: 2;
        }

        /* Green Piece: Left Hole */
        /* To create a "hole" feeling, we overlap with the Orange knob. */
        /* Since Orange is z-index 2 and Green is z-index 1, the Orange knob sits ON TOP of Green. */
        /* This visually looks like Orange fitting into Green. */
        /* We just need to ensure Green has NO other pseudos for Top/Bottom as requested. */

        /* Green Piece: Right Connection (Knob) */
        .puzzle-piece.green::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            background-color: #E8186A;
            border-radius: 50%;
            right: -30px;
            top: 50%;
            transform: translateY(-50%);
            box-shadow: 1px 0 0 #E8186A;
            z-index: 2;
        }

        /* Ensure no other pseudos interfere (Left is handled by overlap) */
        .puzzle-piece.green::before {
            content: none;
        }

        /* Blue Piece: Left Hole handled by Overlap */
        /* No Right Connection as it's the last step for now, or maybe not? */
        /* User said: "In dit blauwe puzzelstukje ... knop voor installeren" */
        /* So it's probably the end. */
        .puzzle-piece.blue::before,
        .puzzle-piece.blue::after {
            content: none;
        }

        /* Animation States */
        body.step-2-mode .puzzle-piece.orange {
            width: 300px;
            opacity: 1;
            pointer-events: none;
        }

        body.step-2-mode .puzzle-piece.green {
            display: block;
            opacity: 1;
            animation: slideInRight 0.5s ease forwards;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-group.summary-item {
            margin-bottom: 30px;
        }

        .summary-label {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            word-break: break-word;
        }

        /* Setup Container Reset - Remove legacy styles that interfere with puzzle pieces */
        .setup-container {
            display: none;
            width: auto;
            max-width: none;
            background: transparent;
            box-shadow: none;
            padding: 0;
            margin: 0;
            position: relative;
            z-index: 5;
            /* Ensure above background elements */
        }

        /* Enforce pointer events on active piece */
        .puzzle-piece {
            pointer-events: auto !important;
        }

        body.step-2-mode .puzzle-piece.orange {
            pointer-events: none !important;
        }

        body.step-3-mode .puzzle-piece.green,
        body.step-3-mode .puzzle-piece.orange {
            pointer-events: none !important;
        }

        .setup-container::before,
        .setup-container::after {
            content: none;
        }

        body.selected-mode .setup-container {
            display: block;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            z-index: 2;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-size: 16px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .tip {
            font-size: 14px;
            color: #2d1f5e;
            margin-bottom: 12px;
            line-height: 1.4;
            padding: 10px;
            border-left: 4px solid #6b4fa0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Specific Tip Colors */
        .puzzle-piece.orange .tip {
            background-color: #fdebd1;
            border-left-color: #a86205;
        }

        .puzzle-piece.green .tip {
            background-color: #fce4ef;
            border-left-color: #8c0040;
        }

        .puzzle-piece.blue .tip {
            background-color: #e8e0f5;
            border-left-color: #1c0e5a;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            border-color: #E8186A;
            outline: none;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }

        .input-group {
            margin-bottom: 10px;
        }

        .nav-buttons {
            display: flex;
            align-items: center;
            margin-top: 20px;
            position: relative;
            z-index: 10;
            /* Ensure buttons are above other elements */
        }

        .nav-buttons.single {
            justify-content: flex-end;
        }

        .nav-buttons.dual {
            justify-content: space-between;
        }

        .arrow-btn {
            background-color: #3B2A8C;
            color: white;
            min-width: 120px;
            /* Pill shape */
            padding: 10px 20px;
            height: 50px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .arrow-btn:hover {
            background-color: #E8186A;
            transform: scale(1.05);
        }

        .arrow-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Adjust summary width */
        body.step-2-mode .puzzle-piece.green,
        body.step-3-mode .puzzle-piece.green,
        body.step-3-mode .puzzle-piece.orange {
            width: 300px;
            opacity: 1;
            pointer-events: none;
        }

        body.step-3-mode .puzzle-piece.blue {
            display: block;
            opacity: 1;
            animation: slideInRight 0.5s ease forwards;
        }

        /* Info Box Styling */
        .info-box {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .info-box.visible {
            display: flex;
        }

        .info-box .icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .info-box .message {
            flex: 1;
        }

        /* Info variant (testing) */
        .info-box.info {
            background-color: #e8e0f5;
            border-left: 4px solid #3B2A8C;
            color: #1c0e5a;
        }

        /* Success variant */
        .info-box.success {
            background-color: #fce4ef;
            border-left: 4px solid #E8186A;
            color: #6b0030;
        }

        /* Error variant */
        .info-box.error {
            background-color: #ffe6e6;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<?php
$selectedLang = $_GET['lang'] ?? null;
$bodyClass = $selectedLang ? 'selected-mode' : '';
?>

<body class="<?= $bodyClass ?>">

    <div class="logo-container splash-logo">
        <img src="/assets/logo/logo_fritsion_cms.png" alt="Fritsion Logo" class="logo">
    </div>

    <div class="text-container">
        <div class="text"><?= $lang['welcome']; ?></div>
    </div>

    <div class="setup-container">
        <form action="" method="POST" id="install-form">
            <input type="hidden" name="action" value="install">
            <div class="multistep-wrapper">
                <div class="puzzle-focus-group">
                    <div class="logo-container pieces-logo">
                        <img src="/assets/logo/logo_fritsion_cms.png" alt="Fritsion Logo" class="logo">
                    </div>

                    <!-- Step 1: Site Info (Orange) -->
                    <div class="puzzle-piece orange" id="step-1-piece">
                        <!-- Step 1 Content: Inputs -->
                        <div id="step-1-content">
                            <div class="form-group">
                                <label><?= $lang['site_name_label'] ?></label>
                                <div class="tip"><?= $lang['site_name_tip'] ?></div>
                                <input type="text" name="site_name" id="site_name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label><?= $lang['site_desc_label'] ?></label>
                                <div class="tip"><?= $lang['site_desc_tip'] ?></div>
                                <textarea name="site_desc" id="site_desc" class="form-input" required></textarea>
                            </div>
                            <div class="nav-buttons single">
                                <button type="button" class="arrow-btn right"
                                    onclick="goToStep2()"><span><?= $lang['btn_next'] ?></span> &#8594;
                                </button>
                            </div>
                        </div>

                        <!-- Step 1 Summary (Hidden initially) -->
                        <div id="step-1-summary" style="display: none;">
                            <div class="form-group summary-item">
                                <div class="summary-label"><?= $lang['site_name_label'] ?></div>
                                <div class="summary-value" id="summary-site-name"></div>
                            </div>
                            <div class="form-group summary-item">
                                <div class="summary-label"><?= $lang['site_desc_label'] ?></div>
                                <div class="summary-value" id="summary-site-desc"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Admin Info (Green) -->
                    <div class="puzzle-piece green" id="step-2-piece">
                        <!-- Overlap Connection: Orange Knob sits on top -->

                        <div id="step-2-content">
                            <!-- Domain Input (Moved from Step 3, now at top of Step 2) -->
                            <div class="form-group">
                                <label><?= $lang['site_domain_label'] ?? 'Domeinnaam' ?></label>
                                <div class="tip">
                                    <?= $lang['site_domain_tip'] ?? 'Voer het domein in (bijv. localhost of mijnwebsite.nl)' ?>
                                </div>
                                <input type="text" name="domain" class="form-input" placeholder="example.com" required>
                            </div>

                            <div class="form-group">
                                <label><?= $lang['admin_account_label'] ?></label>
                                <div class="tip"><?= $lang['admin_account_tip'] ?></div>

                                <div class="input-group">
                                    <input type="text" name="username" placeholder="<?= $lang['username_label'] ?>"
                                        class="form-input" required>
                                </div>
                                <div class="input-group">
                                    <input type="email" name="email" placeholder="<?= $lang['email_label'] ?>"
                                        class="form-input" required>
                                </div>
                                <div class="input-group">
                                    <input type="password" name="password" placeholder="<?= $lang['password_label'] ?>"
                                        class="form-input" required>
                                </div>
                                <div class="input-group">
                                    <input type="password" name="password_repeat"
                                        placeholder="<?= $lang['password_repeat_label'] ?>" class="form-input" required>
                                </div>
                            </div>
                            <div class="nav-buttons dual">
                                <button type="button" class="arrow-btn left" onclick="goToStep1()">
                                    &#8592; <span><?= $lang['btn_back'] ?></span>
                                </button>
                                <button type="button" class="arrow-btn right" onclick="goToStep3()">
                                    <span><?= $lang['btn_next'] ?></span> &#8594;
                                </button>
                            </div>
                        </div>

                        <!-- Step 2 Summary (Hidden initially) -->
                        <div id="step-2-summary" style="display: none;">
                            <div class="form-group summary-item">
                                <div class="summary-label"><?= $lang['site_domain_label'] ?? 'Domeinnaam' ?></div>
                                <div class="summary-value" id="summary-domain"></div>
                            </div>
                            <div class="form-group summary-item">
                                <div class="summary-label"><?= $lang['username_label'] ?></div>
                                <div class="summary-value" id="summary-username"></div>
                            </div>
                            <div class="form-group summary-item">
                                <div class="summary-label"><?= $lang['email_label'] ?></div>
                                <div class="summary-value" id="summary-email"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Database Config (Blue) -->
                <div class="puzzle-piece blue" id="step-3-piece">
                    <!-- Overlap Connection: Green Knob sits on top -->

                    <!-- DB Prefix -->
                    <div class="form-group">
                        <label><?= $lang['db_prefix_label'] ?? 'Database Prefix' ?></label>
                        <div class="tip">
                            <?= $lang['db_prefix_tip'] ?? 'Kies een voorvoegsel voor de tabellen (standaard: fcms_)' ?>
                        </div>
                        <input type="text" name="db_prefix" class="form-input" value="fcms_" required>
                    </div>

                    <!-- DB Name -->
                    <div class="form-group">
                        <label><?= $lang['db_name_label'] ?></label>
                        <div class="tip"><?= $lang['db_name_tip'] ?></div>
                        <input type="text" name="db_name" class="form-input" required>
                    </div>

                    <!-- DB User -->
                    <div class="form-group">
                        <label><?= $lang['db_user_label'] ?></label>
                        <div class="tip"><?= $lang['db_user_tip'] ?></div>
                        <input type="text" name="db_user" class="form-input" required>
                    </div>

                    <!-- DB Pass -->
                    <div class="form-group">
                        <label><?= $lang['db_pass_label'] ?></label>
                        <div class="tip"><?= $lang['db_pass_tip'] ?></div>
                        <input type="password" name="db_pass" class="form-input" required>
                    </div>

                    <!-- DB Host -->
                    <div class="form-group">
                        <label><?= $lang['db_host_label'] ?></label>
                        <div class="tip"><?= $lang['db_host_tip'] ?></div>
                        <input type="text" name="db_host" class="form-input" value="localhost" required>
                    </div>

                    <div id="db-test-result" class="info-box">
                        <span class="icon"></span>
                        <span class="message"></span>
                    </div>

                    <div class="nav-buttons dual">
                        <!-- Back Button -->
                        <button type="button" class="arrow-btn left" onclick="goToStep2()">
                            &#8592; <span><?= $lang['btn_back'] ?></span>
                        </button>

                        <!-- Test Button -->
                        <button type="button" id="test-db-btn" class="arrow-btn" style="background-color: #F0961B;"
                            onclick="testDatabaseConnection()">
                            <span><?= $lang['btn_test_db'] ?></span>
                        </button>

                        <!-- Install Button (Hidden initially) -->
                        <button type="submit" id="install-btn" class="arrow-btn right" style="display:none;">
                            <span><?= $lang['start_install'] ?></span> &#8594;
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <div class="lang-select">
        <a href="?lang=nl" class="<?= $selectedLang === 'nl' ? 'active' : '' ?>" onclick="handleFlagClick(event, 'nl')">
            <img src="/assets/flags/nl.svg" alt="Nederlands" class="flag-icon">
        </a>
        <a href="?lang=en" class="<?= $selectedLang === 'en' ? 'active' : '' ?>" onclick="handleFlagClick(event, 'en')">
            <img src="/assets/flags/en.svg" alt="English" class="flag-icon">
        </a>
    </div>

    <div class="version-footer">
        v<?= isset($version) ? $version : '0.1.1' ?>
    </div>

    <script>
        function handleFlagClick(event, lang) {
            event.preventDefault(); // Always prevent default to handle logic manually behavior

            const currentParams = new URLSearchParams(window.location.search);
            const currentLang = currentParams.get('lang');

            // If user clicks the currently active flag (toggle menu)
            if (currentLang === lang) {
                document.querySelector('.lang-select').classList.toggle('expanded');
                return;
            }

            // If switching language -> Save State (Only Data, Reset Step)
            const state = {
                site_name: document.getElementById('site_name').value,
                site_desc: document.getElementById('site_desc').value,
                username: document.querySelector('input[name="username"]').value,
                email: document.querySelector('input[name="email"]').value,
                password: document.querySelector('input[name="password"]').value,
                password_repeat: document.querySelector('input[name="password_repeat"]').value,
                step: document.body.classList.contains('step-3-mode') ? 3 : (document.body.classList.contains('step-2-mode') ? 2 : 1),

                // DB Fields

                // DB Fields
                domain: document.querySelector('input[name="domain"]')?.value || '',
                db_prefix: document.querySelector('input[name="db_prefix"]')?.value || '',
                db_name: document.querySelector('input[name="db_name"]')?.value || '',
                db_user: document.querySelector('input[name="db_user"]')?.value || '',
                db_pass: document.querySelector('input[name="db_pass"]')?.value || '',
                db_host: document.querySelector('input[name="db_host"]')?.value || ''
            };
            sessionStorage.setItem('fritsion_setup_state', JSON.stringify(state));

            // Navigate
            window.location.href = '?lang=' + lang;
        }

        // Restore State on Load
        document.addEventListener('DOMContentLoaded', () => {
            // Form submit handler for installation
            const form = document.getElementById('install-form');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const resultBox = document.getElementById('db-test-result');
                const iconSpan = resultBox.querySelector('.icon');
                const messageSpan = resultBox.querySelector('.message');
                const installBtn = document.getElementById('install-btn');
                const testBtn = document.getElementById('test-db-btn');

                // Show installing state
                resultBox.className = 'info-box visible info';
                iconSpan.innerText = '⏳';
                messageSpan.innerText = '<?= $lang['installing'] ?>';
                installBtn.disabled = true;
                testBtn.disabled = true;

                // Submit installation
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            resultBox.className = 'info-box visible success';
                            iconSpan.innerText = '🎉';
                            messageSpan.innerText = '<?= $lang['install_complete'] ?>';

                            // Show redirect message after 2 seconds
                            setTimeout(() => {
                                messageSpan.innerText = '<?= $lang['redirecting'] ?>';
                                iconSpan.innerText = '↻';
                            }, 2000);

                            // Redirect after 3 seconds
                            setTimeout(() => {
                                window.location.href = 'backoffice';
                            }, 3000);

                        } else {
                            resultBox.className = 'info-box visible error';
                            iconSpan.innerText = '❌';
                            messageSpan.innerText = '<?= $lang['install_error'] ?>: ' + (data.message || 'Unknown error');
                            installBtn.disabled = false;
                            testBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        resultBox.className = 'info-box visible error';
                        iconSpan.innerText = '❌';
                        messageSpan.innerText = 'Error: ' + error;
                        installBtn.disabled = false;
                        testBtn.disabled = false;
                    });
            });

            // Check if we are on a fresh visit (no lang param) -> Clear storage
            const currentParams = new URLSearchParams(window.location.search);
            if (!currentParams.has('lang')) {
                sessionStorage.removeItem('fritsion_setup_state');
                return; // Nothing to restore
            }

            const savedState = sessionStorage.getItem('fritsion_setup_state');
            if (savedState) {
                const state = JSON.parse(savedState);

                // Restore inputs
                if (state.site_name) document.getElementById('site_name').value = state.site_name;
                if (state.site_desc) document.getElementById('site_desc').value = state.site_desc;
                if (state.username) document.querySelector('input[name="username"]').value = state.username;
                if (state.email) document.querySelector('input[name="email"]').value = state.email;
                if (state.password) document.querySelector('input[name="password"]').value = state.password;
                if (state.password_repeat) document.querySelector('input[name="password_repeat"]').value = state.password_repeat;

                // Restore DB inputs
                if (state.domain) document.querySelector('input[name="domain"]').value = state.domain;
                if (state.db_prefix) document.querySelector('input[name="db_prefix"]').value = state.db_prefix;
                if (state.db_name) document.querySelector('input[name="db_name"]').value = state.db_name;
                if (state.db_user) document.querySelector('input[name="db_user"]').value = state.db_user;
                if (state.db_pass) document.querySelector('input[name="db_pass"]').value = state.db_pass;
                if (state.db_host) document.querySelector('input[name="db_host"]').value = state.db_host;

                if (state.db_host) document.querySelector('input[name="db_host"]').value = state.db_host;

                // Restore summary content
                document.getElementById('summary-site-name').innerText = state.site_name || '';
                document.getElementById('summary-site-desc').innerText = state.site_desc || '';

                // Restore Step
                if (state.step === 2) {
                    goToStep2(true);
                } else if (state.step === 3) {
                    // Manually set step 2 state first without animation delay
                    document.getElementById('summary-site-name').innerText = state.site_name;
                    document.getElementById('summary-site-desc').innerText = state.site_desc;
                    document.body.classList.add('step-2-mode');
                    document.getElementById('step-1-content').style.display = 'none';
                    document.getElementById('step-1-summary').style.display = 'block';

                    goToStep3(true);
                }
            }
        });

        function goToStep2(instant = false) {
            const siteName = document.getElementById('site_name').value;
            const siteDesc = document.getElementById('site_desc').value;

            if (!siteName || !siteDesc) {
                alert('<?= $lang['error_fill_all_fields'] ?>');
                return;
            }

            // Update Summary for Step 1
            document.getElementById('summary-site-name').innerText = siteName;
            document.getElementById('summary-site-desc').innerText = siteDesc;

            document.body.classList.remove('step-3-mode');
            document.body.classList.add('step-2-mode');

            // Hide Step 1 Content, Show Summary
            /* We do this via CSS mostly, but to be sure */
            document.getElementById('step-1-content').style.display = 'none';
            document.getElementById('step-1-summary').style.display = 'block';

            // Show Step 2 Content, Hide Summary
            document.getElementById('step-2-content').style.display = 'block';
            document.getElementById('step-2-summary').style.display = 'none'; // Ensure content is shown

            if (!instant) {
                // Animation logic handled by CSS
            }
        }

        function goToStep3(instant = false) {
            const domain = document.querySelector('input[name="domain"]').value;
            const username = document.querySelector('input[name="username"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const passwordRepeat = document.querySelector('input[name="password_repeat"]').value;

            if (!domain || !username || !email || !password || !passwordRepeat) {
                alert('<?= $lang['error_fill_all_fields'] ?>');
                return;
            }

            if (password !== passwordRepeat) {
                alert('<?= $lang['error_passwords_mismatch'] ?>');
                return;
            }

            // Update Summary for Step 2
            document.getElementById('summary-domain').innerText = domain;
            document.getElementById('summary-username').innerText = username;
            document.getElementById('summary-email').innerText = email;

            document.body.classList.add('step-3-mode');

            // Hide Step 2 Content, Show Summary
            document.getElementById('step-2-content').style.display = 'none';
            document.getElementById('step-2-summary').style.display = 'block';

            if (!instant) {
                // Animation logic
            }
        }

        function goToStep1() {
            document.body.classList.remove('step-2-mode');
            document.body.classList.remove('step-3-mode');

            document.getElementById('step-1-content').style.display = 'block';
            document.getElementById('step-1-summary').style.display = 'none';
        }

        function testDatabaseConnection() {
            const host = document.querySelector('input[name="db_host"]').value;
            const user = document.querySelector('input[name="db_user"]').value;
            const pass = document.querySelector('input[name="db_pass"]').value;
            const name = document.querySelector('input[name="db_name"]').value;
            const resultBox = document.getElementById('db-test-result');
            const iconSpan = resultBox.querySelector('.icon');
            const messageSpan = resultBox.querySelector('.message');
            const installBtn = document.getElementById('install-btn');

            // Show testing state
            resultBox.className = 'info-box visible info';
            iconSpan.innerText = 'ℹ️';
            messageSpan.innerText = '<?= $lang['test_db_connection'] ?>';
            installBtn.style.display = 'none';

            const formData = new FormData();
            formData.append('action', 'test_db');
            formData.append('host', host);
            formData.append('user', user);
            formData.append('pass', pass);
            formData.append('name', name);

            fetch('', { // Post to self
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultBox.className = 'info-box visible success';
                        iconSpan.innerText = '✅';
                        messageSpan.innerText = '<?= $lang['success_db_connect'] ?>';
                        installBtn.style.display = 'flex'; // Show install button
                    } else {
                        resultBox.className = 'info-box visible error';
                        iconSpan.innerText = '❌';
                        messageSpan.innerText = '<?= $lang['error_db_connect'] ?>: ' + data.message;
                    }
                })
                .catch(error => {
                    resultBox.className = 'info-box visible error';
                    iconSpan.innerText = '❌';
                    messageSpan.innerText = 'Error: ' + error;
                });
        }
    </script>
</body>

</html>