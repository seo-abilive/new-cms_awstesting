import { useContetField } from '@/mod/content_field/utils/context/ContentFieldContext'
import { config, customConfig } from '@/mod/content_field/utils/config'
import { ResourceSort } from '@/utils/components/common/ResourceSort'

export const Sort = () => {
    const { model_id, getBreads, replacePath } = useContetField()
    const breads = getBreads([{ name: customConfig.name }, { name: '並び替え' }])

    const columns = [
        { key: 'name', label: '名前' },
        { key: 'field_id', label: 'フィールドID' },
    ]

    return (
        <ResourceSort
            options={{
                breads,
                columns,
                config: (() => {
                    let clone = { ...customConfig }
                    clone.path = replacePath(customConfig.path)
                    return clone
                })(),
                baseParams: { criteria: { model_id: model_id } },
            }}
        />
    )
}
