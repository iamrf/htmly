<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Lucide icon subset used by Modern Bilingual — local pack (no CDN).
 * Icons: https://lucide.dev — ISC License
 * viewBox 0 0 24 24; stroke via currentColor.
 */
if (!function_exists('mb_lucide_icons_data')) {
    function mb_lucide_icons_data()
    {
        return array(
            'search' => array(
                'label' => 'Search',
                'inner' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            ),
            'search-x' => array(
                'label' => 'Search X',
                'inner' => '<path d="m13.5 8.5-5 5"/><path d="m8.5 8.5 5 5"/><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            ),
            'sun' => array(
                'label' => 'Sun',
                'inner' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
            ),
            'moon' => array(
                'label' => 'Moon',
                'inner' => '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',
            ),
            'menu' => array(
                'label' => 'Menu',
                'inner' => '<path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/>',
            ),
            'x' => array(
                'label' => 'Close',
                'inner' => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
            ),
            'arrow-up' => array(
                'label' => 'Arrow up',
                'inner' => '<path d="m5 12 7-7 7 7"/><path d="M12 19V5"/>',
            ),
            'arrow-left' => array(
                'label' => 'Arrow left',
                'inner' => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
            ),
            'arrow-right' => array(
                'label' => 'Arrow right',
                'inner' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
            ),
            'external-link' => array(
                'label' => 'External link',
                'inner' => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
            ),
            'messages-square' => array(
                'label' => 'Messages',
                'inner' => '<path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/>',
            ),
            'message-circle' => array(
                'label' => 'Message',
                'inner' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
            ),
            'play' => array(
                'label' => 'Play',
                'inner' => '<polygon points="6 3 20 12 6 21 6 3"/>',
            ),
            'music' => array(
                'label' => 'Music',
                'inner' => '<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>',
            ),
            'copy' => array(
                'label' => 'Copy',
                'inner' => '<rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>',
            ),
            'link' => array(
                'label' => 'Link',
                'inner' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
            ),
            'corner-down-left' => array(
                'label' => 'Reply',
                'inner' => '<polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/>',
            ),
            'send' => array(
                'label' => 'Send',
                'inner' => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
            ),
            'reply' => array(
                'label' => 'Reply',
                'inner' => '<polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/>',
            ),
            'check' => array(
                'label' => 'Check',
                'inner' => '<path d="M20 6 9 17l-5-5"/>',
            ),
            'rss' => array(
                'label' => 'RSS',
                'inner' => '<path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1"/>',
            ),
            'file-text' => array(
                'label' => 'File',
                'inner' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>',
            ),
            'plus' => array(
                'label' => 'Plus',
                'inner' => '<path d="M5 12h14"/><path d="M12 5v14"/>',
            ),
            'home' => array(
                'label' => 'Home',
                'inner' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
            ),
        );
    }
}
