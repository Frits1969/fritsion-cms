<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Binnenkort online | Fritsion CMS</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <style>
        :root {
            --primary-bg: #F6F5FF;
            --secondary-bg: #FFFFFF;
            --accent-color: #E8186A;
            --accent-orange: #F0961B;
            --accent-purple: #3B2A8C;
            --accent-gradient: linear-gradient(135deg, #E8186A 0%, #C41257 40%, #F0961B 100%);
            --text-main: #1A1336;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Blobs */
        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, #FFB347 0%, #E8186A 50%, #3B2A8C 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.1;
            animation: move 20s infinite alternate;
        }

        .blob-1 {
            top: -10%;
            left: -10%;
        }

        .blob-2 {
            bottom: -10%;
            right: -10%;
            animation-delay: -5s;
        }

        @keyframes move {
            from {
                transform: translate(0, 0) scale(1);
            }

            to {
                transform: translate(100px, 100px) scale(1.1);
            }
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 60px 40px;
            border-radius: 40px;
            width: 90%;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            margin-bottom: 40px;
        }

        .logo-container img {
            width: 180px;
            height: auto;
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logo-container img:hover {
            transform: scale(1.05);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -1px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 1.15rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(232, 24, 106, 0.08);
            color: var(--accent-pink);
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 30px;
            border: 1px solid rgba(232, 24, 106, 0.1);
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Countdown or loader representation */
        .loader-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .dot {
            width: 10px;
            height: 10px;
            background: var(--accent-gradient);
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="maintenance-card">
        <div class="logo-container">
            <img src="/assets/logo/logo_fritsion_cms.png" alt="Fritsion CMS Logo">
        </div>

        <div class="badge">Binnenkort online</div>

        <h1>Website in aanbouw</h1>

        <p>Er wordt op dit moment hard gewerkt aan een gloednieuwe website met het <strong>Fritsion-CMS</strong>. Kom
            binnenkort terug voor het resultaat!</p>

        <div class="loader-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <div class="footer">
            &copy;
            <?= date('Y') ?> - Powered by Fritsion
        </div>
    </div>
</body>

</html>