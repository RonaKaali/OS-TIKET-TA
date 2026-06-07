// GPS dikirim sedini mungkin (sebelum Alpine & monitor session)
import './geo-location';

import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './session-monitor';
