<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plugin Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        :root {
            --bg-dark: #1f1f2b;
            --bg-card: #2b2b3c;
            --bg-hover: #2e2e42;
            --text-main: #f0f0f0;
            --text-muted: #aaa;
            --primary: #0dcaf0;
            --success: #1dd1a1;
            --warning: #ffc107;
            --danger: #ff6b6b;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'Segoe UI', sans-serif;
            padding: 2rem;
        }

        h1 {
            font-weight: 600;
            color: var(--text-main);
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .form-control {
            background: #2e2e42;
            border: 1px solid #444;
            color: #fff;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .upload-section {
            border: 2px dashed #444;
            border-radius: 10px;
            background: var(--bg-card);
            text-align: center;
            padding: 20px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background .3s;
        }

        .upload-section:hover {
            background-color: var(--bg-hover);
        }

        .upload-section i {
            font-size: 40px;
            color: var(--primary);
        }

        #uploadInput {
            display: none;
        }

        .progress {
            height: 25px;
            margin-top: 1rem;
        }

        .main-section {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .left-panel,
        .right-panel {
            min-width: 320px;
        }

        .left-panel {
            flex: 1;
        }

        .right-panel {
            flex: 2;
        }

        .plugin-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .plugin-card {
            background: var(--bg-card);
            border: 1px solid #3c3c4f;
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            transition: transform .2s, box-shadow .2s;
        }

        .plugin-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 15px rgba(13, 202, 240, 0.3);
        }

        .plugin-card .icon {
            background: var(--primary);
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plugin-card.active .icon {
            background: var(--success);
        }

        .plugin-info h5 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
        }

        .badge-active {
            background-color: var(--success);
            color: #000;
        }

        .badge-inactive {
            background-color: #6c757d;
            color: #fff;
        }

        pre#composerLog {
            background: #181825;
            color: #00f0ff;
            padding: 1rem;
            border-left: 5px solid var(--primary);
            border-radius: 5px;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .plugin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        #pluginStore .plugin-card {
            flex-direction: column;
            text-align: left;
            padding: 1.5rem;
        }

        #pluginStore .icon {
            width: 55px;
            height: 55px;
            border-radius: 10px;
            font-size: 24px;
        }

        .plugin-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .badge-license {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            background: #3c3c4f;
            color: #fff;
        }

        .badge-price {
            background: var(--warning);
            color: #000;
            font-weight: bold;
        }

        .badge.bg-info {
            background-color: #0dcaf0 !important;
            color: #000;
        }

        .badge.bg-success {
            background-color: #20c997 !important;
            color: #000;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000;
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
            color: #000;
            transition: 0.3s;
        }

        .btn-success:hover {
            background-color: #1abc9c;
            color: #fff;
        }

        .alert-success {
            background-color: #14532d;
            color: #a3e635;
            border-color: #14532d;
        }

        .alert-danger {
            background-color: #7f1d1d;
            color: #f87171;
            border-color: #7f1d1d;
        }

        @media(max-width: 768px) {
            .main-section {
                flex-direction: column;
            }

            .plugin-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h1><i class="fas fa-plug me-2 text-primary"></i>Plugin Manager</h1>
            <input type="text" id="pluginSearch" class="form-control" placeholder="Search plugins...">
        </div>

        <div class="upload-section" id="uploadTrigger">
            <i class="fas fa-file-archive"></i>
            <h5 class="mb-1">Upload a Plugin ZIP</h5>
            <p class="text-muted small mb-0">Click to install plugin with auto migration & composer</p>
        </div>

        <input type="file" id="uploadInput" accept=".zip" />
        <div class="alert message" id="uploadMessage" style="display: none;"></div>

        <div class="progress" style="display:none;" id="installProgressBar">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                style="width: 0%" id="installProgress">0%</div>
        </div>

        <pre id="composerLog"></pre>

        <div class="main-section mt-4">
            <div class="left-panel">
                <h5 class="mb-3">Installed Plugins</h5>
                <div class="plugin-list" id="pluginList"></div>
            </div>

            <div class="right-panel">
                <h5 class="mb-3"><i class="fas fa-store me-2 text-warning"></i>Plugin Store</h5>
                <div class="plugin-grid" id="pluginStore">
                    <!-- Plugin Card 1 -->
                    <div class="plugin-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon bg-primary"><i class="fas fa-shopping-cart"></i></div>
                            <div class="plugin-info ms-3">
                                <h5 class="mb-0">WooCommerce <small class="text-muted">v7.0.1</small></h5>
                            </div>
                        </div>
                        <p class="mb-2">Sell anything online with full cart and checkout features.</p>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <span class="badge badge-license">License: GPL</span>
                            <span class="badge badge-price">Free</span>
                            <span class="badge bg-info">E-Commerce</span>
                        </div>
                        <button class="btn btn-sm btn-success w-100"><i
                                class="fas fa-download me-1"></i>Install</button>
                        <div class="plugin-footer mt-2">
                            <small class="text-muted">By Automattic</small>
                        </div>
                    </div>

                    <!-- Plugin Card 2 -->
                    <div class="plugin-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon bg-warning"><i class="fas fa-bolt"></i></div>
                            <div class="plugin-info ms-3">
                                <h5 class="mb-0">Cache Booster <small class="text-muted">v1.4.2</small></h5>
                            </div>
                        </div>
                        <p class="mb-2">Speed up your Laravel app with advanced caching features.</p>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <span class="badge badge-license">License: MIT</span>
                            <span class="badge badge-price">$9.99</span>
                            <span class="badge bg-success">Performance</span>
                        </div>
                        <button class="btn btn-sm btn-success w-100"><i
                                class="fas fa-download me-1"></i>Install</button>
                        <div class="plugin-footer mt-2">
                            <small class="text-muted">By Speedify</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let pluginData = [];

        function renderPlugins(filter = '') {
            const container = $('#pluginList');
            container.html('');
            const filtered = pluginData.filter(p => p.name.toLowerCase().includes(filter.toLowerCase()));
            if (!filtered.length) {
                container.html('<div class="text-muted">No plugins found.</div>');
                return;
            }
            filtered.forEach(p => {
                const html = `
                    <div class="plugin-card ${p.active ? 'active' : ''}">
                        <div class="icon"><i class="fas fa-${p.icon}"></i></div>
                        <div class="plugin-info">
                            <h5 class="mb-0">${p.name} <small class="text-muted">v${p.version}</small></h5>
                            <p class="mb-1">${p.desc}</p>
                            <small class="text-muted">By ${p.author}</small>
                        </div>
                    </div>`;
                container.append(html);
            });
        }

        function fetchPlugins() {
            $.get('{{ route('plugins.list') }}', function(data) {
                pluginData = data;
                renderPlugins($('#pluginSearch').val());
            });
        }

        $(document).ready(function() {
            fetchPlugins();

            $('#pluginSearch').on('input', function() {
                renderPlugins(this.value);
            });

            $('#uploadTrigger').click(() => $('#uploadInput').click());

            $('#uploadInput').change(function() {
                const file = this.files[0];
                if (!file || !file.name.endsWith('.zip')) return alert('Only ZIP files allowed.');

                const formData = new FormData();
                formData.append('plugin_zip', file);

                $('#composerLog').hide().text('');
                $('#uploadMessage').hide().removeClass('alert-success alert-danger');
                $('#installProgressBar').show();
                let percent = 0;
                const interval = setInterval(() => {
                    percent += 10;
                    $('#installProgress').css('width', percent + '%').text(percent + '%');
                    if (percent >= 100) clearInterval(interval);
                }, 300);

                $.ajax({
                    url: '{{ route('plugins.upload') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        clearInterval(interval);
                        $('#installProgress').css('width', '100%').text('100%');
                        $('#uploadMessage').addClass('alert-success')
                            .text(response.message).fadeIn().delay(4000).fadeOut();

                        if (response.composer_output && response.composer_output.length > 0) {
                            $('#composerLog').html(response.composer_output.join('<br>'))
                                .slideDown();
                        } else {
                            $('#composerLog').hide().text('');
                        }

                        fetchPlugins();
                        setTimeout(() => $('#installProgressBar').fadeOut(), 1000);
                    },
                    error: function(xhr) {
                        clearInterval(interval);
                        $('#installProgressBar').fadeOut();
                        let msg = 'Upload failed';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $('#uploadMessage').addClass('alert-danger')
                            .text(msg).fadeIn().delay(6000).fadeOut();

                        if (xhr.responseJSON && xhr.responseJSON.composer_output) {
                            $('#composerLog').html(xhr.responseJSON.composer_output.join(
                                '<br>')).slideDown();
                        } else {
                            $('#composerLog').hide().text('');
                        }
                    }
                });

                this.value = '';
            });
        });
    </script>
</body>

</html>
