import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Import session monitor untuk auto logout
import './session-monitor';

// Import GPS integration untuk Zero Trust (opsional, tergantung izin user)
import './geo-location';
