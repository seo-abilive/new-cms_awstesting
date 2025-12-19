import { useLocation, useParams } from 'react-router'
import { customConfig, fieldItem } from '@/mod/content_field/utils/config'
import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { useContetField } from '@/mod/content_field/utils/context/ContentFieldContext'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { useRef } from 'react'
import { getChoice } from '@/utils/common'

const Contents = () => {
    const { id } = useParams()
    const { getBreads, model_id, replacePath, config } = useContetField()
    const { navigateTo } = useNavigation()
    const formRef = useRef(null)

    const breads = getBreads([
        { name: config.name, path: config.path },
        { name: id ? '編集' : '新規作成' },
    ])

    // リンクからfield_type取得
    const location = useLocation()
    const fieldType = location?.state?.field_type
    const customFieldId = location?.state?.custom_field_id
    const customGroupName = location?.state?.name

    // field_typeがない場合はindexへ (新規作成時のみ)
    if (!id && !fieldType) {
        navigateTo(replacePath(config.path))
        return null
    }

    const isCustom = fieldType === 'custom_field'
    const isCustomBlock = fieldType === 'custom_block'

    const field = !isCustom ? getChoice(fieldItem, fieldType) : { label: customGroupName }

    const formItem = [
        { title: '', id: 'model_id', default: model_id, formType: 'hidden' },
        {
            title: 'フィールドタイプ',
            id: 'field_type',
            default: fieldType,
            formType: 'label',
            label: field ? field.label : '',
        },
        { title: '名前', id: 'name', required: true },
        { title: 'フィールドID', id: 'field_id', required: true },
        { title: 'プレイスホルダー', id: 'placeholder' },
        { title: 'ヘルプテキスト', id: 'help_text', formType: 'textarea' },
        {
            title: '表示条件',
            id: 'show_when',
            formType: 'add_show_when',
            default: [],
            help_text: 'このフィールドが表示される条件を設定できます。',
        },
        {
            title: 'バリデーション',
            id: 'validates',
            formType: 'add_validates',
            default: [],
            help_text: 'このフィールドのバリデーションを設定できます。',
        },
        {
            title: '必須項目',
            id: 'is_required',
            formType: 'switch',
            default: false,
            position: 'aside',
        },
        {
            title: '一覧見出し',
            id: 'is_list_heading',
            formType: 'switch',
            default: false,
            position: 'aside',
        },
    ]

    if (isCustom) {
        formItem.push({
            title: '',
            id: 'custom_field_id',
            default: customFieldId,
            formType: 'hidden',
        })
    } else if (isCustomBlock) {
        formItem.push({
            title: 'フィールド追加',
            id: 'children_block',
            formType: 'add_fields',
            fieldTypes: fieldItem,
            endpoint: replacePath(customConfig.end_point),
            model_id: model_id,
            default: [],
            onFetch: (data) => {
                return data.map((item) => {
                    if (item.content_reference_id) {
                        // コンテンツ参照の場合はコンテンツのタイトルとIDを返す
                        return {
                            ...item,
                            content_reference_id: {
                                label: item.content_reference.title,
                                value: item.content_reference.id,
                            },
                        }
                    } else {
                        return item
                    }
                })
            },
        })
    } else {
        if (field?.isChoice) {
            formItem.push({
                title: '選択肢',
                id: 'choices',
                formType: 'add_choices',
                default: [],
            })
        }
    }

    // コンテンツ参照の場合
    if (field?.value === 'content_reference') {
        formItem.splice(6, 0, {
            title: '参照コンテンツ',
            id: 'content_reference_id',
            formType: 'taxonomy_select',
            endpoint: replacePath(`:company_alias/content_model/resource`),
            placeholder: '選択してください',
            isCreatable: false,
            onFetch: (data, item) => {
                return { label: item?.content_reference?.title, value: item?.content_reference?.id }
            },
        })
    }

    return (
        <ResourceForm
            options={{
                breads,
                config,
                formItem,
                id,
            }}
            ref={formRef}
        />
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
