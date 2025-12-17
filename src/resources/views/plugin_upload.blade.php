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
            flex-direction: column;
            gap: 0.5rem;
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
            border-radius: 10px;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .plugin-info h5 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .badge-license,
        .badge-price,
        .badge-category {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
        }

        .badge-price {
            background-color: var(--warning);
            color: #000;
        }

        .badge-license {
            background-color: #555;
            color: #fff;
        }

        .badge-category {
            background-color: var(--primary);
            color: #000;
        }

        .btn-success {
            background-color: var(--success);
            border: none;
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

        .alert {
            margin-top: 1rem;
        }

        code {
            background: #2e2e42;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85rem;
            color: #0dcaf0;
        }


        @media(max-width: 768px) {
            .main-section {
                flex-direction: column;
            }
        }

        /* === Code block for credentials === */
        code {
            background-color: #111827;
            /* Dark blue-gray */
            color: #0dcaf0;
            /* Bright cyan for contrast */
            padding: 3px 6px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }

        /* === Access URL link === */
        a.text-info {
            color: #0dcaf0;
            text-decoration: underline;
        }

        a.text-info:hover {
            color: #00bcd4;
        }

        /* === Feature list === */
        .plugin-info ul {
            list-style: none;
            padding-left: 0;
            margin: 0 0 0.5rem;
        }

        .plugin-info ul li {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .plugin-info ul li i {
            margin-right: 6px;
            color: var(--success);
        }

        /* === Emphasize plugin card with detail === */
        .plugin-card {
            background: var(--bg-card);
            border: 1px solid #3c3c4f;
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
            transition: transform .2s, box-shadow .2s;
        }

        .plugin-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 15px rgba(13, 202, 240, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-3"><i class="fas fa-plug text-primary me-2"></i>Plugin Manager</h1>

        <div style="position:fixed;bottom:10px;right:15px;font-size:0.75rem;color:#666;z-index:9999;">
            Developed by <a href="https://github.com/sumandey7689" target="_blank"
                style="color:#0dcaf0;text-decoration:none;">sumandey7689</a>
        </div>

        <div class="upload-section" id="uploadTrigger">
            <i class="fas fa-file-archive"></i>
            <h5 class="mb-1">Upload a Plugin ZIP</h5>
            <p class="text-muted small mb-0">Click to install plugin with auto migration & composer</p>
        </div>

        <input type="file" id="uploadInput" accept=".zip" style="display: none;">
        <div class="alert message" id="uploadMessage" style="display: none;"></div>

        <div class="progress" style="display:none;" id="installProgressBar">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar"
                style="width: 0%" id="installProgress">0%</div>
        </div>

        <pre id="composerLog"></pre>

        <div class="main-section mt-4">
            <div class="left-panel">
                <h5>Installed Plugins</h5>
                <div id="pluginList" class="plugin-list"></div>
            </div>

            <div class="right-panel">
                <h5>Plugin Store</h5>
                <div class="plugin-list">
                    @foreach ($pluginStore as $p)
                        <div class="plugin-card">
                            <div class="icon"><i class="fas fa-{{ $p['icon'] ?? 'puzzle-piece' }}"></i></div>
                            <div class="plugin-info">
                                <h5 class="mb-1">{{ $p['name'] }} <small
                                        class="text-muted">v{{ $p['version'] }}</small></h5>
                                <p class="mb-1">{{ $p['description'] }}</p>

                                @if (isset($p['access_url']))
                                    <div class="mb-1">
                                        <strong>Access:</strong> <a href="{{ $p['access_url'] }}" class="text-info"
                                            target="_blank">{{ $p['access_url'] }}</a>
                                    </div>
                                @endif

                                @if (isset($p['default_username']) && isset($p['default_password']))
                                    <div class="mb-1">
                                        <strong>Login:</strong> <code>{{ $p['default_username'] }}</code> /
                                        <code>{{ $p['default_password'] }}</code>
                                    </div>
                                @endif

                                @if (isset($p['features']))
                                    <ul class="small text-muted mb-2">
                                        @foreach ($p['features'] as $feature)
                                            <li><i class="fas fa-check text-success me-1"></i>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="mb-2">
                                    <span class="badge badge-license">License: {{ $p['license'] }}</span>
                                    <span class="badge badge-price">Free</span>
                                    <span class="badge badge-category">{{ $p['category'] }}</span>
                                </div>

                                <button class="btn btn-sm btn-success w-100 install-from-url"
                                    data-url="{{ $p['zip_url'] }}">
                                    <i class="fas fa-download me-1"></i>Install
                                </button>
                                <small class="text-muted d-block mt-1">By {{ $p['author'] }}</small>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>
        </div>
    </div>

    <script>
        function fetchInstalledPlugins() {
            $.get('{{ route('plugins.list') }}', function(data) {
                const container = $('#pluginList');
                container.empty();

                if (!data.length) {
                    container.html('<div class="text-muted">No plugins installed.</div>');
                    return;
                }

                data.forEach(p => {
                    const html = `
                        <div class="plugin-card">
                            <div class="icon"><i class="fas fa-${p.icon}"></i></div>
                            <div class="plugin-info">
                                <h5 class="mb-1">${p.name} <small class="text-muted">v${p.version}</small></h5>
                                <p class="mb-1">${p.desc}</p>
                                <small class="text-muted">By ${p.author}</small>
                            </div>
                        </div>
                    `;
                    container.append(html);
                });
            });
        }

        $(document).ready(function() {
            fetchInstalledPlugins();

            $('#uploadTrigger').on('click', () => $('#uploadInput').click());

            $('#uploadInput').on('change', function() {
                const file = this.files[0];
                if (!file || !file.name.endsWith('.zip')) return alert('Only ZIP files allowed.');

                const formData = new FormData();
                formData.append('plugin_zip', file);

                $('#uploadMessage, #composerLog').hide().removeClass('alert-success alert-danger').text('');
                $('#installProgressBar').show();
                let percent = 0;
                const interval = setInterval(() => {
                    percent += 15;
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
                        $('#uploadMessage').addClass('alert-success').text(response.message)
                            .fadeIn().delay(4000).fadeOut();
                        if (response.composer_output?.length) {
                            $('#composerLog').html(response.composer_output.join('<br>'))
                                .slideDown();
                        }
                        fetchInstalledPlugins();
                        setTimeout(() => $('#installProgressBar').fadeOut(), 1000);
                    },
                    error: function(xhr) {
                        clearInterval(interval);
                        $('#installProgressBar').fadeOut();
                        let msg = 'Upload failed';
                        if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                        $('#uploadMessage').addClass('alert-danger').text(msg).fadeIn().delay(
                            6000).fadeOut();
                        if (xhr.responseJSON?.composer_output) {
                            $('#composerLog').html(xhr.responseJSON.composer_output.join(
                                '<br>')).slideDown();
                        }
                    }
                });

                this.value = '';
            });

            $(document).on('click', '.install-from-url', function() {
                const zipUrl = $(this).data('url');
                if (!zipUrl) return;

                $('#uploadMessage, #composerLog').hide().removeClass('alert-success alert-danger').text('');
                $('#installProgressBar').show();
                let percent = 0;
                const interval = setInterval(() => {
                    percent += 15;
                    $('#installProgress').css('width', percent + '%').text(percent + '%');
                    if (percent >= 100) clearInterval(interval);
                }, 300);

                $.ajax({
                    url: '{{ route('plugins.install.remote') }}',
                    method: 'POST',
                    data: {
                        zip_url: zipUrl
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        clearInterval(interval);
                        $('#installProgress').css('width', '100%').text('100%');
                        $('#uploadMessage').addClass('alert-success').text(response.message)
                            .fadeIn().delay(4000).fadeOut();
                        if (response.composer_output?.length) {
                            $('#composerLog').html(response.composer_output.join('<br>'))
                                .slideDown();
                        }
                        fetchInstalledPlugins();
                        setTimeout(() => $('#installProgressBar').fadeOut(), 1000);
                    },
                    error: function(xhr) {
                        clearInterval(interval);
                        $('#installProgressBar').fadeOut();
                        let msg = 'Installation failed';
                        if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                        $('#uploadMessage').addClass('alert-danger').text(msg).fadeIn().delay(
                            6000).fadeOut();
                        if (xhr.responseJSON?.composer_output) {
                            $('#composerLog').html(xhr.responseJSON.composer_output.join(
                                '<br>')).slideDown();
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
