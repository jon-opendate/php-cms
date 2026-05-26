<aside class="theme-picker">
    <h3>Theme</h3>
    <ul class="theme-list">
        <li><button class="theme-item" data-theme="warm">Warm</button></li>
        <li><button class="theme-item" data-theme="dark">Dark</button></li>
        <li><button class="theme-item" data-theme="purple">Purple</button></li>
        <li><button class="theme-item" data-theme="custom">Custom</button></li>
    </ul>

    <div class="theme-custom">
        <h3>Colors</h3>
        <input type="text" placeholder="Theme name" id="theme-name" maxlength="20">
        <div class="theme-color-grid">
            <div class="theme-color-item">
                <label>Background</label>
                <input type="color" id="color-bg" value="#f5e6d3">
            </div>
            <div class="theme-color-item">
                <label>Text</label>
                <input type="color" id="color-ink" value="#3d2817">
            </div>
            <div class="theme-color-item">
                <label>Accent</label>
                <input type="color" id="color-accent" value="#d97f5f">
            </div>
            <div class="theme-color-item">
                <label>Danger</label>
                <input type="color" id="color-danger" value="#c41e3a">
            </div>
        </div>
    </div>
</aside>

<script>
(function() {
    const themeButtons = document.querySelectorAll('.theme-item');
    const colorInputs = {
        bg: document.getElementById('color-bg'),
        ink: document.getElementById('color-ink'),
        accent: document.getElementById('color-accent'),
        danger: document.getElementById('color-danger'),
    };
    const themeName = document.getElementById('theme-name');
    const html = document.documentElement;

    function setTheme(name) {
        html.setAttribute('data-theme', name);
        localStorage.setItem('cms-theme', name);
        themeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-theme') === name);
        });

        if (name === 'warm') {
            colorInputs.bg.value = '#f5e6d3';
            colorInputs.ink.value = '#3d2817';
            colorInputs.accent.value = '#d97f5f';
            colorInputs.danger.value = '#c41e3a';
        } else if (name === 'dark') {
            colorInputs.bg.value = '#0d1117';
            colorInputs.ink.value = '#f0f6fc';
            colorInputs.accent.value = '#00d9ff';
            colorInputs.danger.value = '#ff5555';
        } else if (name === 'purple') {
            colorInputs.bg.value = '#f0ecf5';
            colorInputs.ink.value = '#2a2238';
            colorInputs.accent.value = '#6750a4';
            colorInputs.danger.value = '#b42318';
        }
    }

    function applyCustomColors() {
        const colors = {
            '--bg': colorInputs.bg.value,
            '--ink': colorInputs.ink.value,
            '--panel': adjustLightness(colorInputs.bg.value, 10),
            '--muted': adjustLightness(colorInputs.ink.value, -30),
            '--line': adjustLightness(colorInputs.bg.value, -10),
            '--accent': colorInputs.accent.value,
            '--accent-dark': adjustLightness(colorInputs.accent.value, -20),
            '--danger': colorInputs.danger.value,
        };
        Object.entries(colors).forEach(([key, value]) => {
            html.style.setProperty(key, value);
        });
        setTheme('custom');
    }

    function adjustLightness(hex, amount) {
        const rgb = parseInt(hex.slice(1), 16);
        const r = Math.max(0, Math.min(255, ((rgb >> 16) & 255) + amount));
        const g = Math.max(0, Math.min(255, ((rgb >> 8) & 255) + amount));
        const b = Math.max(0, Math.min(255, (rgb & 255) + amount));
        return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
    }

    themeButtons.forEach(btn => {
        btn.addEventListener('click', () => setTheme(btn.getAttribute('data-theme')));
    });

    Object.values(colorInputs).forEach(input => {
        input.addEventListener('change', applyCustomColors);
        input.addEventListener('input', applyCustomColors);
    });

    const savedTheme = localStorage.getItem('cms-theme') || 'warm';
    setTheme(savedTheme);
})();
</script>
