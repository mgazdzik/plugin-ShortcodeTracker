<IfModule mod_rewrite.c>
    RewriteEngine On

RewriteRule ^([a-zA-Z0-9]{6})$ /index.php?module=API&method=ShortcodeTracker.performRedirectForShortcode&code=$1 [L]
