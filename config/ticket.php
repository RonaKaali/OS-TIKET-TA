<?php

return [
    'number_prefix' => env('TICKET_NUMBER_PREFIX', 'CSIRT'),
    'number_length' => (int) env('TICKET_NUMBER_LENGTH', 8),
];
