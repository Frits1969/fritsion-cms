<?php
$uri = strtok($_SERVER['REQUEST_URI'] ?? '/backoffice', '?');
$lang = $GLOBALS['lang'] ?? [];
$backoffice_title = $lang['backoffice_title'] ?? 'Backoffice';
$nav_media = $lang['nav_media'] ?? 'Media';
$media_title = $lang['media_title'] ?? 'Media Beheer';
$media_desc = $lang['media_desc'] ?? 'Beheer al je afbeeldingen, video\'s en mappen.';
$nav_dashboard = $lang['nav_dashboard'] ?? 'Dashboard';
$nav_profile = $lang['nav_profile'] ?? 'Profiel';
$nav_logout = $lang['nav_logout'] ?? 'Uitloggen';
$btn_cancel = $lang['btn_cancel'] ?? 'Annuleren';
$role_super_admin = $lang['role_super_admin'] ?? 'Super Admin';
$nav_back_to_dashboard = $lang['nav_back_to_dashboard'] ?? 'Terug naar Dashboard';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'nl' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $media_title ?> | Fritsion CMS</title>
    <link rel="icon" type="image/png" href="/assets/logo/logo_fritsion_cms_favicon.png">
    <link rel="shortcut icon" href="/assets/logo/logo_fritsion_cms_favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin_shared.css">
    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .media-item {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            aspect-ratio: 1/1.1;
        }

        .media-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-color: var(--accent-pink);
        }

        .media-preview {
            flex: 1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .media-preview .icon {
            font-size: 3.5rem;
            opacity: 0.8;
        }

        .media-info {
            padding: 12px 15px;
            background: white;
            border-top: 1px solid var(--glass-border);
        }

        .media-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .media-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
        }

        .media-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 10;
        }

        .media-item:hover .media-actions {
            opacity: 1;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: white;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: var(--accent-pink);
            color: white;
            border-color: var(--accent-pink);
        }

        .action-btn.delete:hover {
            background: var(--accent-red);
            border-color: var(--accent-red);
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .breadcrumb-item {
            color: var(--accent-pink);
            text-decoration: none;
            font-weight: 600;
        }

        .breadcrumb-item:last-child {
            color: var(--text-muted);
            pointer-events: none;
        }

        .breadcrumb-separator {
            color: #cbd5e1;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 18px;
            padding: 40px;
            text-align: center;
            background: rgba(255,255,255,0.5);
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 30px;
        }

        .upload-zone:hover, .upload-zone.dragging {
            border-color: var(--accent-pink);
            background: rgba(232, 24, 106, 0.03);
        }

        .upload-icon { font-size: 2.5rem; margin-bottom: 15px; display: block; }
        .upload-text { font-weight: 600; color: var(--text-main); }
        .upload-subtext { color: var(--text-muted); font-size: 0.9rem; margin-top: 5px; }

        .video-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        input[type="file"] { display: none; }
    </style>
</head>

<body>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="main-wrapper">
        <header class="topbar">
            <div style="font-weight: 600; color: var(--text-muted);">
                <?= $nav_media ?> / <?= empty($currentDir) ? 'Root' : htmlspecialchars($currentDir) ?>
            </div>
            <div class="topbar-actions">
                <a href="/backoffice" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><?= $nav_back_to_dashboard ?></a>

                <div class="user-widget" id="user-widget">
                    <div class="user-avatar">
                        <?php
                        $name = $_SESSION['username'] ?? 'Admin';
                        echo strtoupper(substr($name, 0, 1) . (strlen($name) > 1 ? substr($name, 1, 1) : ''));
                        ?>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= $name ?></span>
                        <span class="user-role"><?= $role_super_admin ?></span>
                    </div>
                    <div class="user-menu" id="user-menu">
                        <a href="/backoffice/profile" class="menu-item"><?= $nav_profile ?></a>
                        <hr style="margin: 5px 0; border: none; border-top: 1px solid var(--glass-border);">
                        <a href="/backoffice/logout" class="menu-item logout"><?= $nav_logout ?></a>
                    </div>
                </div>

                <div class="lang-select" id="lang-switcher">
                    <?php $selectedLang = $_SESSION['lang'] ?? 'nl'; ?>
                    <a href="?lang=nl" class="<?= $selectedLang === 'nl' ? 'active' : '' ?>">
                        <img src="/assets/flags/nl.svg" alt="Nederlands" class="flag-icon">
                    </a>
                    <a href="?lang=en" class="<?= $selectedLang === 'en' ? 'active' : '' ?>">
                        <img src="/assets/flags/en.svg" alt="English" class="flag-icon">
                    </a>
                </div>
            </div>
        </header>

        <main class="content">
            <div class="header-section">
                <div>
                    <h1><?= $media_title ?></h1>
                    <p><?= $media_desc ?></p>
                </div>
                <div style="display:flex; gap:10px;">
                    <button class="btn-secondary" onclick="openFolderModal()">
                        <span>📁</span> <?= $lang['btn_new_folder'] ?? 'Nieuwe Map' ?>
                    </button>
                    <button class="btn-primary" onclick="triggerUpload()">
                        <span>⬆️</span> <?= $lang['btn_upload'] ?? 'Uploaden' ?>
                    </button>
                </div>
            </div>

            <div class="breadcrumb-nav">
                <a href="/backoffice/media" class="breadcrumb-item">Media</a>
                <?php 
                $pathAccumulator = '';
                foreach ($breadcrumbs as $crumb): 
                    $pathAccumulator .= ($pathAccumulator ? '/' : '') . $crumb;
                ?>
                    <span class="breadcrumb-separator">/</span>
                    <a href="?dir=<?= urlencode($pathAccumulator) ?>" class="breadcrumb-item"><?= htmlspecialchars($crumb) ?></a>
                <?php endforeach; ?>
            </div>

            <div class="upload-zone" id="dropZone" onclick="triggerUpload()">
                <span class="upload-icon">☁️</span>
                <div class="upload-text">Sleep bestanden hierheen of klik om te uploaden</div>
                <div class="upload-subtext">Ondersteunt Afbeeldingen (JPG, PNG, Webp, SVG) & Video (MP4)</div>
                <input type="file" id="fileInput" multiple onchange="handleFileUpload(this.files)">
            </div>

            <div class="media-grid">
                <?php if ($currentDir): ?>
                    <?php 
                        $parentParts = explode('/', $currentDir);
                        array_pop($parentParts);
                        $parentDir = implode('/', $parentParts);
                    ?>
                    <div class="media-item" onclick="location.href='?dir=<?= urlencode($parentDir) ?>'">
                        <div class="media-preview">
                            <span class="icon">🔙</span>
                        </div>
                        <div class="media-info">
                            <span class="media-name">..</span>
                            <span class="media-meta">Terug</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($items as $item): ?>
                    <div class="media-item" onclick="<?= $item['isDir'] ? "location.href='?dir=" . urlencode($item['path']) . "'" : "previewFile('" . $item['url'] . "')" ?>">
                        <div class="media-actions">
                            <button class="action-btn" onclick="event.stopPropagation(); openRenameModal('<?= $item['path'] ?>', '<?= pathinfo($item['name'], PATHINFO_FILENAME) ?>')" title="<?= $lang['btn_rename'] ?? 'Hernoemen' ?>">✏️</button>
                            <button class="action-btn delete" onclick="event.stopPropagation(); deleteItem('<?= $item['path'] ?>')" title="<?= $lang['btn_remove'] ?? 'Verwijderen' ?>">🗑️</button>
                        </div>
                        
                        <div class="media-preview">
                            <?php if ($item['isDir']): ?>
                                <span class="icon">📂</span>
                            <?php elseif (in_array($item['ext'], ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])): ?>
                                <img src="<?= $item['url'] ?>" alt="<?= $item['name'] ?>">
                            <?php elseif (in_array($item['ext'], ['mp4', 'webm', 'mov', 'ogv'])): ?>
                                <span class="icon">🎬</span>
                                <span class="video-badge"><?= strtoupper($item['ext']) ?></span>
                            <?php else: ?>
                                <span class="icon">📄</span>
                            <?php endif; ?>
                        </div>

                        <div class="media-info">
                            <span class="media-name" title="<?= htmlspecialchars($item['name']) ?>"><?= htmlspecialchars($item['name']) ?></span>
                            <span class="media-meta">
                                <span><?= $item['isDir'] ? 'Map' : strtoupper($item['ext']) ?></span>
                                <span><?= $item['isDir'] ? '' : round($item['size'] / 1024, 1) . ' KB' ?></span>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div id="folderModal" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h2><?= $lang['btn_new_folder'] ?? 'Nieuwe Map' ?></h2>
            </div>
            <form action="/backoffice/media/create-folder" method="POST">
                <input type="hidden" name="parent" value="<?= htmlspecialchars($currentDir) ?>">
                <div class="form-group">
                    <label><?= $lang['label_folder_name'] ?? 'Mapnaam' ?></label>
                    <input type="text" name="name" class="form-control" placeholder="bijv. Projecten" required autofocus>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:15px; margin-top: 30px;">
                    <button type="button" onclick="closeFolderModal()" class="btn-secondary"><?= $btn_cancel ?></button>
                    <button type="submit" class="btn-primary"><?= $lang['btn_create'] ?? 'Aanmaken' ?></button>
                </div>
            </form>
        </div>
    </div>

    <div id="renameModal" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h2><?= $lang['btn_rename'] ?? 'Hernoemen' ?></h2>
            </div>
            <div class="form-group">
                <label><?= $lang['label_new_name'] ?? 'Nieuwe naam' ?></label>
                <input type="text" id="newNameInput" class="form-control" required autofocus>
                <input type="hidden" id="oldPathInput">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:15px; margin-top: 30px;">
                <button type="button" onclick="closeRenameModal()" class="btn-secondary"><?= $btn_cancel ?></button>
                <button type="button" onclick="submitRename()" class="btn-primary"><?= $lang['btn_save'] ?? 'Opslaan' ?></button>
            </div>
        </div>
    </div>

    <script>
        const currentDir = '<?= $currentDir ?>';

        function triggerUpload() { document.getElementById('fileInput').click(); }

        function handleFileUpload(files) {
            if (!files.length) return;
            
            const formData = new FormData();
            formData.append('path', currentDir);

            for (let i = 0; i < files.length; i++) {
                formData.append('file', files[i]);
                
                fetch('/backoffice/media/upload', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Upload mislukt');
                    }
                })
                .catch(err => console.error(err));
            }
        }

        // Drag & Drop
        const dropZone = document.getElementById('dropZone');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        dropZone.addEventListener('dragover', () => dropZone.classList.add('dragging'));
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragging'));
        dropZone.addEventListener('drop', (e) => {
            dropZone.classList.remove('dragging');
            handleFileUpload(e.dataTransfer.files);
        });

        function deleteItem(path) {
            if (confirm('<?= $lang['confirm_delete_media'] ?? 'Weet je zeker dat je dit wilt verwijderen?' ?>')) {
                const formData = new FormData();
                formData.append('path', path);
                fetch('/backoffice/media/delete', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert(data.message);
                });
            }
        }

        function openFolderModal() { document.getElementById('folderModal').classList.add('active'); }
        function closeFolderModal() { document.getElementById('folderModal').classList.remove('active'); }

        function openRenameModal(path, currentName) {
            document.getElementById('oldPathInput').value = path;
            document.getElementById('newNameInput').value = currentName;
            document.getElementById('renameModal').classList.add('active');
        }
        function closeRenameModal() { document.getElementById('renameModal').classList.remove('active'); }

        function submitRename() {
            const oldPath = document.getElementById('oldPathInput').value;
            const newName = document.getElementById('newNameInput').value;
            if (!newName) return;

            const formData = new FormData();
            formData.append('oldPath', oldPath);
            formData.append('newName', newName);

            fetch('/backoffice/media/rename', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.message);
            });
        }

        function previewFile(url) {
            window.open(url, '_blank');
        }

        // Sidebar Fix / User Menu
        const userWidget = document.getElementById('user-widget');
        const userMenu = document.getElementById('user-menu');
        userWidget.addEventListener('click', (e) => { e.stopPropagation(); userMenu.classList.toggle('active'); });
        document.addEventListener('click', () => userMenu.classList.remove('active'));
    </script>
</body>
</html>
