import { useContetField } from '@/mod/content_field/utils/context/ContentFieldContext'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const { model_id, getBreads, config } = useContetField()
    const breads = getBreads([{ name: config.name }, { name: '並び替え' }])

    const columns = [
        { key: 'name', label: '名前' },
        { key: 'field_type', label: 'フィールドタイプ' },
    ]

    return (
        <ResourceSort
            options={{
                breads,
                columns,
                config,
                baseParams: { criteria: { model_id: model_id, is_top_field: 1 } },
            }}
        />
    )
}
