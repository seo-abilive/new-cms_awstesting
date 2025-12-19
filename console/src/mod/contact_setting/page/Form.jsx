import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { fieldItem } from '@/mod/contact_setting/utils/config'
import { useParams } from 'react-router'
import { useModPage } from '../utils/context/ModPageContext'
import { useEffect, useMemo, useState } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { useAuth } from '@/utils/context/AuthContext'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const { config } = useModPage()
    const breads = [{ name: config.name, path: config.path }, { name: pageName }]
    const { company_alias, facility_alias } = useCompanyFacility()
    const { user } = useAuth()
    const { sendRequest: sendPermissionRequest } = useAxios()
    const { sendRequest: sendDetailRequest } = useAxios()
    const [hasWritePermission, setHasWritePermission] = useState(true)
    const [writeScope, setWriteScope] = useState(null)
    const [targetCreatedBy, setTargetCreatedBy] = useState(null)
    const [isLoadingDetail, setIsLoadingDetail] = useState(false)
    const formItem = [
        { title: 'タイトル', id: 'title', required: true },
        { title: '送信者名', id: 'from_name' },
        { title: '送信者アドレス', id: 'from_address', required: true },
        { title: '送信先アドレス', id: 'to_address', required: true },
        { title: '送信完了ページ', id: 'thanks_page', formType: 'richtext' },
        { title: '件名', id: 'subject', required: true },
        { title: '本文', id: 'body', formType: 'textarea', rows: 10, required: true },
        { title: '返信', id: 'is_return', formType: 'switch', default: false },
        {
            title: '返信フィールド',
            id: 'return_field',
            required: true,
            show_when: [{ field_id: 'is_return', value: true }],
        },
        {
            title: '返信件名',
            id: 'return_subject',
            required: true,
            show_when: [{ field_id: 'is_return', value: true }],
        },
        {
            title: '返信本文',
            id: 'return_body',
            formType: 'textarea',
            rows: 10,
            required: true,
            show_when: [{ field_id: 'is_return', value: true }],
        },
        {
            title: 'reCAPTCHA',
            id: 'is_recaptcha',
            formType: 'switch',
            default: false,
            placeholder: 'reCAPTCHAを使用する',
        },
        {
            title: 'reCAPTCHAサイトキー',
            id: 'recaptcha_site_key',
            formType: 'text',
            required: true,
            show_when: [{ field_id: 'is_recaptcha', value: true }],
        },
        {
            title: 'reCAPTCHAシークレットキー',
            id: 'recaptcha_secret_key',
            formType: 'text',
            required: true,
            show_when: [{ field_id: 'is_recaptcha', value: true }],
        },
        { title: 'フォーム項目', id: 'fields', formType: 'add_fields', fieldTypes: fieldItem },
        { title: '表示状態', id: 'status', formType: 'switch', default: true, position: 'aside' },
    ]

    // 編集画面の場合、書き込み権限をチェック
    // 編集対象の作成者を取得
    useEffect(() => {
        if (!id) {
            setTargetCreatedBy(null)
            setIsLoadingDetail(false)
            return
        }

        const fetchDetail = async () => {
            setIsLoadingDetail(true)
            try {
                const response = await sendDetailRequest({
                    method: 'GET',
                    url: `${config.end_point}/${id}`,
                })
                const createdBy = response?.data?.payload?.data?.created_by ?? null
                setTargetCreatedBy(createdBy)
            } catch (err) {
                console.error('お問い合わせ設定取得エラー:', err)
                setTargetCreatedBy(null)
            } finally {
                setIsLoadingDetail(false)
            }
        }

        fetchDetail()
    }, [id, config.end_point, sendDetailRequest])

    // 編集画面の場合、書き込み権限をチェック
    useEffect(() => {
        if (id && company_alias && facility_alias) {
            const checkPermission = async () => {
                try {
                    const response = await sendPermissionRequest({
                        method: 'GET',
                        url: `user/permissions/check?resource_type=contact_setting&permission=write&company_alias=${company_alias}&facility_alias=${facility_alias}`,
                    })
                    const payload = response?.data?.payload
                    setHasWritePermission(payload?.has_permission !== false)
                    setWriteScope(payload?.scope ?? null)
                } catch (err) {
                    console.error('権限チェックエラー:', err)
                }
            }
            checkPermission()
        } else {
            setHasWritePermission(true)
            setWriteScope(null)
        }
    }, [id, company_alias, facility_alias, sendPermissionRequest])

    const computedReadonly = useMemo(() => {
        // データ取得中はreadonlyにしない（ローディング中）
        if (isLoadingDetail) return false
        
        // 書き込み権限がない場合はreadonly
        if (!hasWritePermission) return true
        
        // スコープが'own'の場合、作成者をチェック
        if (writeScope === 'own' || writeScope === 'OWN') {
            // まだデータが取得されていない場合はreadonlyにしない
            if (targetCreatedBy === null || !user?.id) {
                return false
            }
            // 作成者が自分でない場合はreadonly
            // 型を統一して比較（数値と文字列の両方に対応）
            const targetId = String(targetCreatedBy)
            const userId = String(user.id)
            if (targetId !== userId) {
                return true
            }
        }
        return false
    }, [hasWritePermission, writeScope, targetCreatedBy, user?.id, isLoadingDetail])

    return (
        <>
            <ResourceForm options={{ breads, config, formItem, id, readonly: computedReadonly }} />
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
