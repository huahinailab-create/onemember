<?php

return [
    /*
     * Enable Developer Tools.
     * Must be true AND APP_ENV must not be "production" for the module to be accessible.
     * Never set this to true in production.
     */
    'enabled' => env('DEV_TOOLS_ENABLED', false),
];
