import { customConfig, fieldItem } from '@/mod/content_field/utils/config'
import { useParams } from 'react-router'
import { useContetField } from '@/mod/content_field/utils/context/ContentFieldContext'
import { useRef } from 'react'
import { ResourceForm } from '@/utils/components/common/ResourceForm'

const Contents = () => {
    const { id } = useParams()
    const { getBreads, replacePath, model_id } = useContetField()
    const formRef = useRef(null)

    const breads = getBreads([
        { name: customConfig.name, path: replacePath(customConfig.path) },
        { name: id ? '編集' : '新規作成' },
    ])

    const formItem = [
        { title: '', id: 'model_id', default: model_id, formType: 'hidden' },
        { title: '名前', id: 'name', required: true },
        { title: 'フィールドID', id: 'field_id', required: true },
        {
            title: 'フィールド',
            id: 'fields',
            formType: 'add_fields',
            fieldTypes: fieldItem,
            onFetch: (data) => {
                return data.map((field) => {
                    if (field.content_reference_id) {
                        field.content_reference_id = {
                            label: field.content_reference.title,
                            value: field.content_reference.id,
                        }
                    }
                    return field
                })
            },
        },
    ]

    return (
        <>
            <ResourceForm
                options={{
                    breads,
                    config: (() => {
                        let clone = { ...customConfig }
                        clone.path = replacePath(customConfig.path)
                        return clone
                    })(),
                    formItem,
                    id,
                }}
                ref={formRef}
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
