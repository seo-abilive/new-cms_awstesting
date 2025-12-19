import { useParams } from 'react-router'
import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { useContentModel } from '../utils/context/ContentModelContext'

const Contents = () => {
    const { id } = useParams()
    const { config, getBreads, company } = useContentModel()
    const breads = getBreads([{ name: id ? '編集' : '新規作成' }])

    const formItem = [
        {
            title: '企業',
            id: 'company_id',
            default: company?.id,
            formType: 'hidden',
            onFetch: () => {
                return company?.id
            },
        },
        { title: 'タイトル', id: 'title', required: true },
        { title: 'エイリアス', id: 'alias', required: true },
        { title: '説明', id: 'description', formType: 'textarea' },
        {
            title: '企業使用',
            id: 'is_cms_use_master',
            formType: 'switch',
            label: '使用する',
            default: false,
            help_text: 'チェックを入れると、企業（本部管理）としてCMSが使用できます',
            onFetch: (item, data) => {
                return data?.cms_company?.length > 0 ? true : false
            },
        },
        {
            title: '使用施設',
            id: 'cms_use_facilities',
            formType: 'taxonomy_select',
            isMulti: true,
            endpoint: `contract/facility/resource?criteria[company_id]=${company?.id}`,
            placeholder: '選択してください',
            keyLabel: 'facility_name',
            help_text: '選択した施設でCMSが使用できます',
            default: [],
            onFetch: (item, data) => {
                return (
                    data?.cms_facilities?.map((facility) => ({
                        label: facility.facility_name,
                        value: facility.id,
                    })) ?? []
                )
            },
        },
        {
            title: 'Webhook URL',
            id: 'webhook_url',
            formType: 'text',
            default: null,
            placeholder: '例: https://example.com/webhook',
            help_text: 'Webhook URLを設定すると、記事が更新されたときにWebhookが呼び出されます',
        },
        {
            title: 'リアルタイムプレビュー使用',
            id: 'is_use_preview',
            formType: 'switch',
            label: '使用する',
            default: false,
        },
        {
            title: 'プレビューURL',
            id: 'preview_url',
            formType: 'text',
            default: null,
            placeholder: '例: https://example.com/preview',
            show_when: [{ field_id: 'is_use_preview', value: true }],
        },
        {
            title: 'カテゴリ',
            id: 'is_use_category',
            formType: 'switch',
            label: '使用する',
            default: false,
            position: 'aside',
        },
        {
            title: 'ステータス',
            id: 'is_use_status',
            formType: 'switch',
            label: '使用する',
            default: false,
            position: 'aside',
        },
        {
            title: '公開期間',
            id: 'is_use_publish_period',
            formType: 'switch',
            label: '使用する',
            default: false,
            position: 'aside',
            show_when: [{ field_id: 'is_use_status', value: true }],
        },
        {
            title: '記事登録数制限',
            id: 'max_content_count',
            formType: 'number',
            default: null,
            position: 'aside',
            help_text: '未設定の場合は無制限',
            placeholder: '例: 100',
        },
    ]

    return (
        <>
            <ResourceForm options={{ breads, config, formItem, id }} />
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
