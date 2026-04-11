
<script>
(function () {
    const API_ORIGIN = <?php echo json_encode(config('services.voltspace.api_origin'), 15, 512) ?>;
    const PREFER_SAME_ORIGIN = <?php echo json_encode((bool) config('services.voltspace.prefer_same_origin', true), 512) ?>;

    function normalizeApiBase(raw) {
        const cleaned = String(raw || '').trim().replace(/\/+$/, '');
        if (!cleaned || !/^https?:\/\//i.test(cleaned)) {
            return API_ORIGIN + '/api';
        }
        return cleaned.endsWith('/api') ? cleaned : cleaned + '/api';
    }

    function resolveApiBase() {
        const override = localStorage.getItem('api_base');
        if (override) {
            return normalizeApiBase(override);
        }
        if (PREFER_SAME_ORIGIN && typeof window !== 'undefined' && window.location && window.location.origin) {
            return normalizeApiBase(window.location.origin);
        }
        return normalizeApiBase(API_ORIGIN);
    }

    window.VoltSpaceApi = {
        getBase: resolveApiBase,

        async fetch(path, options = {}) {
            const base = resolveApiBase();
            const rel = String(path || '').startsWith('/') ? path : '/' + path;
            const token = localStorage.getItem('token');
            const headers = { ...(options.headers || {}) };
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }
            if (options.body) {
                headers['Content-Type'] = 'application/json';
            }
            headers['Accept'] = 'application/json';

            try {
                const res = await fetch(base + rel, { ...options, headers });
                if (res.status === 401) {
                    localStorage.removeItem('token');
                    location.href = '/login';
                }
                return res;
            } catch (err) {
                console.error('[VoltSpace API]', err);
                throw err;
            }
        },
    };

    window.apiFetch = (path, options) => window.VoltSpaceApi.fetch(path, options);
})();
</script>
<?php /**PATH D:\Telkom University\Materi Kuliah Semester 6\Proyek Perangkat Lunak\project_ppl-main\resources\views/partials/voltspace-api.blade.php ENDPATH**/ ?>