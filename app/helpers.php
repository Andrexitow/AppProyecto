<?php

if (! function_exists('asset_v')) {
    /**
     * URL versionada de un archivo público (imgs/css/js) para forzar que
     * el navegador recargue el archivo cuando cambia. Antes cada vista
     * llamaba filemtime(public_path(...)) directo: si ese archivo no
     * existía en el servidor (no se subió en el deploy, nombre con
     * mayúsculas distintas en Linux, etc.) filemtime() lanzaba un error
     * fatal y tumbaba la página completa. Con is_file() de por medio, si
     * falta el archivo la página sigue funcionando — solo pierde el
     * cache-busting de ese archivo puntual.
     */
    function asset_v(string $path): string
    {
        $absoluto = public_path($path);
        // Hostinger suele usar public_html como raíz web mientras Laravel
        // conserva public_path() apuntando al directorio public original.
        $alternativo = base_path('public_html/' . ltrim($path, '/'));
        $archivo = is_file($absoluto) ? $absoluto : (is_file($alternativo) ? $alternativo : null);
        $version = $archivo ? filemtime($archivo) : '1';

        return asset($path) . '?v=' . $version;
    }
}
