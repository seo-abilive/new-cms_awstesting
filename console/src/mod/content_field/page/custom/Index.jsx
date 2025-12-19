import { useContetField } from '@/mod/content_field/utils/context/ContentFieldContext'
import { config, customConfig } from '@/mod/content_field/utils/config'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { Button } from '@/utils/components/ui/button'

export const Index = () => {
    const { model_id, getBreads, replacePath, config } = useContetField()
    const breads = getBreads([{ name: customConfig.name }])
    const { navigateTo } = useNavigation()

    const columns = [
        { key: 'name', label: '名前' },
        { key: 'field_id', label: 'フィールドID' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    columns,
                    config: (() => {
                        let clone = { ...customConfig }
                        clone.path = replacePath(customConfig.path)
                        return clone
                    })(),
                    baseParams: { criteria: { model_id: model_id } },
                    isSort: true,
                    addPageActionButtons: [
                        () => {
                            return (
                                <>
                                    <Button
                                        outline
                                        size="xs"
                                        onClick={() => {
                                            navigateTo(`${replacePath(config.path)}`)
                                        }}
                                    >
                                        フィールド
                                    </Button>
                                </>
                            )
                        },
                    ],
                }}
            />
        </>
    )
}
