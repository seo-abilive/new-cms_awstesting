import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { useParams } from 'react-router'
import { useModPage } from '../utils/context/ModPageContext'
import { useMemo, useRef, useState } from 'react'
import { USER_TYPE, userTypeOptions } from '@/core/user/utils/config'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const { config, company } = useModPage()
    const breads = [{ name: config.name, path: config.path }, { name: pageName }]
    const [facilityEndpoint, setFacilityEndpoint] = useState(
        `/contract/facility/resource?criteria[company_id]=${company?.id}`
    )
    const [permissionEndpoint, setPermissionEndpoint] = useState(
        `/user/permissions/function/resource?criteria[company_id]=${company?.id}`
    )
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
                default: USER_TYPE.MANAGE,
                items: userTypeOptions.filter((option) => option.value !== USER_TYPE.MASTER),
            },
            {
                title: 'ステータス',
                id: 'status',
                formType: 'switch',
                default: true,
                position: 'aside',
            },
            {
                title: '企業',
                id: 'company_id',
                required: true,
                formType: 'hidden',
                default: { value: company?.id, label: company?.company_name },
                onFetch: (data, item) => {
                    return {
                        label: company?.company_name,
                        value: company?.id,
                    }
                },
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
                onFetch: (data, item) => {
                    if (item.facilities.length > 0 && item.companies.length > 0) {
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
        [id]
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
