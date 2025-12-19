import { useParams } from 'react-router'
import { useContentModelMarkup } from '../../utils/context/ContentModelMarkupContext'
import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { markupType } from '../../utils/config'

const Contents = () => {
    const { id } = useParams()
    const { config, getBreads, model_id } = useContentModelMarkup()
    const breads = getBreads([{ name: config.name }, { name: id ? '編集' : '新規作成' }])

    const formItem = [
        { title: '', id: 'model_id', default: model_id, formType: 'hidden' },
        {
            title: '出力タイプ',
            id: 'markup_type',
            required: true,
            formType: 'radio',
            items: markupType,
            default: 'list',
        },
        {
            title: 'テンプレートJSON',
            id: 'template_json',
            required: true,
            formType: 'code_editor',
            rows: 10,
            help_text: 'Blade形式で登録してください',
        },
    ]

    return (
        <>
            <ResourceForm
                options={{
                    breads,
                    config: (() => {
                        let clone = { ...config }
                        clone.path = clone.path.replace(':model_id', model_id)
                        return clone
                    })(),
                    formItem,
                    id,
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
