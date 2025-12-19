'use client'

import { useEffect } from 'react'

/**
 * マークアップデータを<script>タグとしてbodyの閉じタグ直前に追加
 * @param {Object} props
 * @param {Array|Object} props.markupData - マークアップデータ（配列またはオブジェクト）
 * @param {string} props.type - スクリプトタイプ（application/ld+jsonなど）
 */
export default function MarkupScript({ markupData, type = 'application/ld+json' }) {
    useEffect(() => {
        if (!markupData) return

        // マークアップデータを配列に変換
        const dataArray = Array.isArray(markupData) ? markupData : [markupData]

        // 既存のマークアップスクリプトを削除（再レンダリング時の重複を防ぐ）
        const existingScripts = document.querySelectorAll('script[data-markup="true"]')
        existingScripts.forEach((script) => script.remove())

        // 各マークアップデータに対してスクリプトタグを追加
        dataArray.forEach((data, index) => {
            if (!data || (typeof data === 'object' && Object.keys(data).length === 0)) {
                return
            }

            const script = document.createElement('script')
            script.type = type
            script.setAttribute('data-markup', 'true')
            script.setAttribute('data-index', index.toString())
            script.textContent = JSON.stringify(data, null, 2)
            document.body.appendChild(script)
        })

        // クリーンアップ関数
        return () => {
            const scripts = document.querySelectorAll('script[data-markup="true"]')
            scripts.forEach((script) => script.remove())
        }
    }, [markupData, type])

    return null
}
