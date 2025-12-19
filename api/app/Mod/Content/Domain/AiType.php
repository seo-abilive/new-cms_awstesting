<?php

namespace App\Mod\Content\Domain;

class AiType
{
    public const PROOFREAD = 'proofread';
    public const SUMMARY = 'summary';
    public const TRANSLATE = 'translate';
    public const CUSTOM = 'custom';


    public static function getPrompt(string $type, string $text = '', ?string $customPrompt = null): string
    {
        $prompt = '【文章】を【目的】に応じて修正して、【形式】で出力してください。' . "\n\n";
        switch ($type) {
            case self::SUMMARY:
                $prompt .= '【目的】: 文章を要約する' . "\n\n";
                break;
            case self::TRANSLATE:
                $prompt .= '【目的】: 文章を英語に翻訳する' . "\n\n";
                break;
            case self::CUSTOM:
                $prompt .= '【目的】: ' . $customPrompt . "\n\n";
                break;
            default:
                $prompt .= '【目的】: 文章を添削する。誤字・脱字・表現の違和感を修正する。' . "\n\n";
                break;
        }

        $prompt .= '【文章】: ' . $text . "\n\n";
        $prompt .= '【形式】: 修正後の文章のみ出力' . "\n\n";

        return $prompt;
    }
}
