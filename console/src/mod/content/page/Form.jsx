import { useParams } from 'react-router'
import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { ContentContextProvider, useContent } from '@/mod/content/utils/context/ContentContext'
import { config as fieldConfig } from '@/mod/content_field/utils/config'
import dayjs from 'dayjs'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { useEffect, useMemo, useState } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { useAuth } from '@/utils/context/AuthContext'

const Contents = () => {
    const { config, modelData } = useContent()
    const { id } = useParams()
    const breads = [{ name: config.name, path: config.path }, { name: id ? '編集' : '新規作成' }]
    const { company_alias, facility_alias } = useCompanyFacility()
    const { user } = useAuth()
    const { sendRequest: sendPermissionRequest } = useAxios()
    const { sendRequest: sendDetailRequest } = useAxios()
    const [hasWritePermission, setHasWritePermission] = useState(true)
    const [writeScope, setWriteScope] = useState(null)
    const [targetCreatedBy, setTargetCreatedBy] = useState(null)
    const [isLoadingDetail, setIsLoadingDetail] = useState(false)

    const formItem = []
    modelData?.fields.map((field, idx) => {
        if (field?.field_type === 'custom_field') {
            formItem.push({
                title: field.name,
                label: field.name,
                id: field.field_id,
                formType: 'custom_field',
                custom_field_id: field.custom_field_id, // Pass ID to the component
                show_when: field?.show_when ?? [],
                contentConfig: config, // AI添削用にcontentConfigを渡す
            })
        } else if (field?.field_type === 'custom_block') {
            formItem.push({
                title: field.name,
                label: field.name,
                id: field.field_id,
                formType: 'custom_block',
                options: {
                    parent_block_id: field?.id,
                    endpoint: `${fieldConfig.end_point}/block/resource`,
                },
                show_when: field?.show_when ?? [],
                contentConfig: config, // AI添削用にcontentConfigを渡す
            })
        } else if (field?.field_type === 'content_reference') {
            formItem.push({
                title: field.name,
                label: field.name,
                id: field.field_id,
                formType: 'content_reference',
                // 参照先ContentModelの情報を渡す
                contentReferenceModel: field?.content_reference || null,
            })
        } else {
            const item = {
                title: field?.name,
                label: field?.name,
                id: field?.field_id,
                formType: field?.field_type,
                required: field?.is_required,
                placeholder: field?.placeholder,
                help_text: field?.help_text,
                items: field?.choices ?? [],
                show_when: field?.show_when ?? [],
                onFetch: (data) => {
                    switch (field?.field_type) {
                        case 'radio':
                        case 'select':
                            return data ? data.value : null
                        case 'checkbox':
                            return data && Array.isArray(data)
                                ? data.map((item) => item.value)
                                : null
                        case 'media_image':
                            return data ? data.id : null
                    }
                    return data
                },
            }

            // richtextフィールドの場合、AI添削エンドポイントを追加
            if (field?.field_type === 'richtext') {
                item.proofreadEndpoint = `${config.end_point}/ai-proofread`
            }

            formItem.push(item)
        }
    })

    // カテゴリ使用あり
    if (modelData?.is_use_category) {
        formItem.push({
            title: 'カテゴリ',
            id: 'categories',
            formType: 'taxonomy_select',
            placeholder: '選択してください',
            endpoint: `${company_alias}/${facility_alias}/content/${modelData.alias}/category/resource`,
            isSearchable: true,
            onFetch: (data) => {
                if (data.length === 0) return null
                return { label: data[0].title, value: data[0].id }
            },
            position: 'aside',
        })
    }

    // ステータス使用あり
    if (modelData?.is_use_status) {
        formItem.push({
            title: '表示状態',
            id: 'status',
            formType: 'switch',
            label: '公開',
            default: true,
            position: 'aside',
        })
    }

    // 公開期間使用あり
    if (modelData?.is_use_publish_period) {
        formItem.push({
            title: '公開開始期間',
            id: 'publish_at',
            formType: 'date',
            label: '公開開始期間',
            position: 'aside',
            options: { enableTime: true, dateFormat: 'Y-m-d H:i' },
            onFetch: (data) => {
                return data ? dayjs(data).format('YYYY-MM-DD HH:mm') : null
            },
            show_when: [{ field_id: 'status', value: true }],
        })
        formItem.push({
            title: '公開終了期間',
            id: 'expires_at',
            formType: 'date',
            label: '公開終了期間',
            position: 'aside',
            options: { enableTime: true, dateFormat: 'Y-m-d H:i' },
            onFetch: (data) => {
                return data ? dayjs(data).format('YYYY-MM-DD HH:mm') : null
            },
            show_when: [{ field_id: 'status', value: true }],
        })
    }

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
                console.error('コンテンツ取得エラー:', err)
                setTargetCreatedBy(null)
            } finally {
                setIsLoadingDetail(false)
            }
        }

        fetchDetail()
    }, [id, config.end_point, sendDetailRequest])

    // 編集画面の場合、書き込み権限をチェック
    useEffect(() => {
        if (id && modelData?.alias && company_alias && facility_alias) {
            const checkPermission = async () => {
                try {
                    const response = await sendPermissionRequest({
                        method: 'GET',
                        url: `user/permissions/check?resource_type=content&permission=write&model_name=${modelData.alias}&company_alias=${company_alias}&facility_alias=${facility_alias}`,
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
    }, [id, modelData?.alias, company_alias, facility_alias, sendPermissionRequest])

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
            <ResourceForm
                options={{
                    breads,
                    config,
                    formItem,
                    id,
                    readonly: computedReadonly,
                    isRealTimePreview: modelData?.is_use_preview,
                    previewUrl: modelData?.preview_url,
                }}
            />
        </>
    )
}

export const New = () => {
    return (
        <>
            <Contents />
        </>
    )
}

export const Edit = () => {
    return (
        <>
            <Contents />
        </>
    )
}
