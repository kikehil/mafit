<?php

namespace App\Helpers;

class TextNorm
{
    /**
     * Limpia texto de Excel: UTF-8, elimina BOM, NBSP, caracteres invisibles y controles
     */
    public static function cleanExcelText(string $text): string
    {
        // Asegurar UTF-8 válido
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        // Normalización NFKC si ext-intl está disponible
        if (class_exists('\Normalizer') && \Normalizer::isNormalized($text, \Normalizer::FORM_KC) === false) {
            $text = \Normalizer::normalize($text, \Normalizer::FORM_KC);
        }

        // Eliminar BOM (U+FEFF)
        $text = str_replace("\xEF\xBB\xBF", '', $text);
        $text = str_replace("\u{FEFF}", '', $text);

        // Eliminar NBSP (U+00A0) y reemplazar por espacio normal
        $text = str_replace("\xC2\xA0", ' ', $text);
        $text = str_replace("\u{00A0}", ' ', $text);

        // Eliminar caracteres invisibles \p{Cf} (incluye U+202D) y controles \p{Cc}
        // Usar regex Unicode
        $text = preg_replace('/[\p{Cf}\p{Cc}]/u', '', $text);

        // Trim y colapsar espacios múltiples
        $text = trim($text);
        $text = preg_replace('/\s+/', ' ', $text);

        return $text;
    }

    /**
     * Genera una clave normalizada para comparación
     * - cleanExcelText
     * - uppercase
     * - quitar acentos (iconv translit)
     * - dejar solo letras/números/espacios
     * - colapsar espacios
     */
    public static function key(string $text): string
    {
        // Limpiar texto de Excel
        $text = self::cleanExcelText($text);

        // Convertir a mayúsculas
        $text = mb_strtoupper($text, 'UTF-8');

        // Quitar acentos usando iconv translit
        if (function_exists('iconv')) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        }

        // Dejar solo letras, números y espacios
        $text = preg_replace('/[^A-Z0-9\s]/', '', $text);

        // Colapsar espacios múltiples
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return $text;
    }
}

