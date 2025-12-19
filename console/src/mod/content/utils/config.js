export const config = {
    name: ':model_name',
    path: '/manage/:company_alias/:facility_alias/:model_name',
    mod_name: 'content',
    end_point: ':company_alias/:facility_alias/content/:model_name',
}

// サイドバー用コンフィグ
export const sidebarConfig = {
    name: ':model_name',
    path: '/manage/:company_alias/:facility_alias/content',
    mod_name: 'content_menu',
    end_point: ':company_alias/:facility_alias/content/model',
}
