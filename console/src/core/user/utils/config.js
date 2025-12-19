export const config = {
    name: 'ユーザー',
    path: '/master/user',
    mod_name: 'user',
    end_point: 'user',
}

// ユーザータイプ定数
export const USER_TYPE = {
    MASTER: 'master',
    COMPANY: 'company',
    MANAGE: 'manage',
    FACILITY: 'facility',
}

// 企業管理画面のfacility_aliasを表す定数
export const MASTER_FACILITY_ALIAS = 'master'

export const userTypeOptions = [
    { label: 'システム管理者', value: USER_TYPE.MASTER },
    { label: '企業管理者', value: USER_TYPE.MANAGE },
    { label: '企業スタッフ', value: USER_TYPE.COMPANY },
    { label: '施設スタッフ', value: USER_TYPE.FACILITY },
]
