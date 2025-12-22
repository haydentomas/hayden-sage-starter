<?php

namespace App\Customizer;

/**
 * Load Customizer modules.
 * Keep this file as includes-only (no side effects beyond require_once).
 *
 * Prefer __DIR__ so paths stay correct even if theme directory moves.
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/defaults.php';
require_once __DIR__ . '/vars.php';
require_once __DIR__ . '/controls.php';
require_once __DIR__ . '/register.php';
require_once __DIR__ . '/output.php';
require_once __DIR__ . '/editor.php';
require_once __DIR__ . '/live-preview.php';
