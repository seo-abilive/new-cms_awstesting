import {} from 'react-icons'

export const config = {
    name: 'フィールド',
    path: '/master/:company_alias/content/:model_id/field',
    mod_name: 'content_field',
    end_point: ':company_alias/content_field',
}

export const customConfig = {
    name: 'カスタムフィールド',
    path: '/master/:company_alias/content/:model_id/field/custom',
    mod_name: 'content_field_custom',
    end_point: ':company_alias/content_field/custom',
}

export const fieldItem = [
    { label: 'テキスト', value: 'text', icon: null, isChoice: false },
    // { label: '数値', value: 'number', icon: null },
    { label: '日付', value: 'date', icon: null, isChoice: false },
    { label: 'テキストエリア', value: 'textarea', icon: null, isChoice: false },
    { label: 'リッチテキスト', value: 'richtext', icon: null, isChoice: false },
    { label: 'ラジオボタン', value: 'radio', icon: null, isChoice: true },
    { label: 'セレクトボックス', value: 'select', icon: null, isChoice: true },
    { label: 'チェックボックス', value: 'checkbox', icon: null, isChoice: true },
    { label: 'フラグボタン', value: 'switch', icon: null, isChoice: false },
    { label: 'リスト', value: 'list', icon: null, isChoice: false },
    { label: 'テーブル', value: 'table', icon: null, isChoice: false },
    { label: '画像', value: 'media_image', icon: null, isChoice: false },
    { label: '複数画像', value: 'media_image_multi', icon: null, isChoice: false },
    { label: 'ファイル', value: 'media_file', icon: null, isChoice: false },
    { label: 'コンテンツ参照', value: 'content_reference', icon: null, isChoice: false },
]
