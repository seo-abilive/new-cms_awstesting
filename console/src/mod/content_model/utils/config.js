export const config = {
    name: 'コンテンツモデル',
    path: '/master/:company_alias/content/model',
    mod_name: 'content_model',
    end_point: ':company_alias/content_model',
}

export const markupConfig = {
    name: '構造化管理',
    path: '/master/:company_alias/content/model/:model_id/markup',
    mod_name: 'content_model_markup',
    end_point: ':company_alias/content_model/markup',
}

export const markupType = [
    { label: '一覧用', value: 'list' },
    { label: '詳細用', value: 'detail' },
]
