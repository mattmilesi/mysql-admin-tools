import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss']
});

window.Echo.private('schema-change')
    .listen('.SchemaChangeProgress', (event) => {
        console.log('Received:', event);
        event.message.split('\n').forEach((line) => {
            const node = document.createElement('span');
            if (line.startsWith('CREATE ')
                || line.startsWith('INSERT ')
                || line.startsWith('SELECT ')
                || line.startsWith('DROP ')
                || line.startsWith('ALTER ')
                || line.startsWith('  `')
                || line.startsWith('  PRIMARY ')
                || line.startsWith(') ')
            ) {
                node.style.color = '#9b9b9b';
            }
            if (line.startsWith('Dry run complete.')
                || line.startsWith('Successfully altered ')
                || line.endsWith(' OK.')
            ) {
                node.style.color = 'green';
                node.style.fontWeight = 'bold';
            }
            if (line.startsWith('Cannot connect to MySQL')
                || line.startsWith('Errors ')
            ) {
                node.style.color = 'red';
                node.style.fontWeight = 'bold';
            }
            node.innerHTML = line;
            document.getElementById('perconaOutput').append(node, '\n');
        });
    });
