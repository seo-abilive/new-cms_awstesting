import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { config, userTypeOptions, USER_TYPE } from '../utils/config'
import { useParams } from 'react-router'
import { useState, useMemo, useRef } from 'react'
import { useModPage } from '../utils/context/ModPageContext'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const { user_type, company, facility } = useModPage()
    const breads = [{ name: config.name, path: config.path }, { name: pageName }]
    const [facilityEndpoint, setFacilityEndpoint] = useState(null)
    const [permissionEndpoint, setPermissionEndpoint] = useState(null)
    const formRef = useRef(null)

    const formItem = useMemo(
        () => [
            { title: '名前', id: 'name', required: true },
            { title: 'メールアドレス', id: 'email', required: true },
            {
                title: 'ユーザータイプ',
                id: 'user_type',
                required: true,
                formType: 'radio',
                default: user_type || USER_TYPE.MASTER,
                items: userTypeOptions,
            },
            {
                title: 'ステータス',
                id: 'status',
                formType: 'switch',
                default: true,
                position: 'aside',
            },
            {
                title: '2段階認証',
                id: 'two_factor_enabled',
                formType: 'switch',
                default: true,
                position: 'aside',
                show_when: [
                    { field_id: 'user_type', value: USER_TYPE.COMPANY },
                    { field_id: 'user_type', value: USER_TYPE.MANAGE },
                    { field_id: 'user_type', value: USER_TYPE.FACILITY },
                ],
                hide_when: [{ field_id: 'user_type', value: USER_TYPE.MASTER }],
            },
            {
                title: '企業',
                id: 'company_id',
                required: true,
                formType: 'taxonomy_select',
                endpoint: 'contract/company/resource',
                keyLabel: 'company_name',
                keyValue: 'id',
                isCreatable: false,
                placeholder: '選択してください',
                default: company ? { value: company.id, label: company.company_name } : null,
                onFetch: (data, item) => {
                    if (item.companies.length > 0) {
                        setFacilityEndpoint(
                            `/contract/facility/resource?criteria[company_id]=${item.companies[0]?.id}`
                        )
                        setPermissionEndpoint(
                            `/user/permissions/function/resource?criteria[company_id]=${item.companies[0]?.id}`
                        )
                        return {
                            label: item.companies[0]?.company_name,
                            value: item.companies[0]?.id,
                        }
                    } else {
                        return null
                    }
                },
                onChangeItem: (value) => {
                    // 施設絞り込み
                    if (value) {
                        setFacilityEndpoint(
                            `/contract/facility/resource?criteria[company_id]=${value.value}`
                        )
                        setPermissionEndpoint(
                            `/user/permissions/function/resource?criteria[company_id]=${value.value}`
                        )
                    } else {
                        setFacilityEndpoint(null)
                        setPermissionEndpoint(null)
                    }
                    // 施設選択が変更されたら施設の選択肢をクリア
                    formRef.current?.setInputVal('facility_id', [])
                    formRef.current?.setInputVal('permission_settings', [])
                },
                show_when: [
                    { field_id: 'user_type', value: USER_TYPE.COMPANY },
                    { field_id: 'user_type', value: USER_TYPE.MANAGE },
                    { field_id: 'user_type', value: USER_TYPE.FACILITY },
                ],
            },
            {
                title: '施設',
                id: 'facility_id',
                required: true,
                formType: 'taxonomy_select',
                endpoint: facilityEndpoint,
                keyLabel: 'facility_name',
                keyValue: 'id',
                isCreatable: false,
                isMulti: true,
                placeholder: '選択してください',
                default: facility ? { value: facility.id, label: facility.facility_name } : null,
                onFetch: (data, item) => {
                    if (item.facilities.length > 0 && item.companies.length > 0) {
                        setFacilityEndpoint(
                            `/contract/facility/resource?criteria[company_id]=${item.companies[0]?.id}`
                        )
                        return item.facilities.map((facility) => ({
                            label: facility.facility_name,
                            value: facility.id,
                        }))
                    } else {
                        return null
                    }
                },
                show_when: [{ field_id: 'user_type', value: USER_TYPE.FACILITY }],
            },
            {
                title: '権限設定',
                id: 'permission_settings',
                formType: 'permission_settings',
                endpoint: permissionEndpoint,
                show_when: [
                    { field_id: 'user_type', value: USER_TYPE.COMPANY },
                    { field_id: 'user_type', value: USER_TYPE.FACILITY },
                ],
                default: [],
                onFetch: (data, item) => {
                    if (item.companies.length > 0) {
                        setPermissionEndpoint(
                            `/user/permissions/function/resource?criteria[company_id]=${item.companies[0]?.id}`
                        )
                    }
                    // 既存の権限設定を返す
                    return item.permissions || []
                },
            },
        ],
        [facilityEndpoint, permissionEndpoint, id]
    )

    const finalFormItem = useMemo(() => {
        const items = [...formItem]
        if (id) {
            items.push({
                title: 'パスワード変更',
                id: 'is_chg_pass',
                formType: 'switch',
                default: false,
            })
            items.push({
                title: 'パスワード',
                id: 'password',
                required: true,
                type: 'password',
                show_when: [{ field_id: 'is_chg_pass', value: true }],
            })
        } else {
            items.push({ title: 'パスワード', id: 'password', required: true, type: 'password' })
        }
        return items
    }, [formItem, id])

    return (
        <>
            <ResourceForm options={{ breads, config, formItem: finalFormItem, id }} ref={formRef} />
        </>
    )
}

export const New = () => {
    return (
        <>
            <Form pageName={'新規作成'} />
        </>
    )
}

export const Edit = () => {
    return (
        <>
            <Form pageName={'編集'} />
        </>
    )
}
