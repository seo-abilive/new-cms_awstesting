export const config = {
    name: 'お問い合わせ設定',
    path: '/manage/:company_alias/:facility_alias/contact_setting',
    mod_name: 'contact_setting',
    end_point: ':company_alias/:facility_alias/contact_setting',
}

export const fieldItem = [
    { label: 'テキスト', value: 'text', icon: null, isChoice: false },
    { label: '日付', value: 'date', icon: null, isChoice: false },
    { label: 'テキストエリア', value: 'textarea', icon: null, isChoice: false },
    { label: 'ラジオボタン', value: 'radio', icon: null, isChoice: true },
    { label: 'セレクトボックス', value: 'select', icon: null, isChoice: true },
    { label: 'チェックボックス', value: 'checkbox', icon: null, isChoice: true },
]
