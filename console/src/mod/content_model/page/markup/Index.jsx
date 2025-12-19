import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useContentModelMarkup } from '../../utils/context/ContentModelMarkupContext'
import { Button } from '@/utils/components/ui/button'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { markupType, config as modelConfig } from '@/mod/content_model/utils/config'
import { HiOutlineArrowCircleLeft } from 'react-icons/hi'

export const Index = () => {
    const { config, getBreads, model_id, replacePath } = useContentModelMarkup()
    const breads = getBreads([{ name: config.name }])
    const { navigateTo } = useNavigation()

    const columns = [
        { key: 'markup_type', label: '出力タイプ' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config: (() => {
                        let clone = { ...config }
                        clone.path = clone.path.replace(':model_id', model_id)
                        return clone
                    })(),
                    addScopedColumns: {
                        markup_type: (item, row, idx) => {
                            return (
                                <td>
                                    {
                                        markupType.find((type) => type.value === item.markup_type)
                                            ?.label
                                    }
                                </td>
                            )
                        },
                    },
                    columns,
                    baseParams: {
                        criteria: {
                            model_id: model_id,
                        },
                    },
                    addPageActionButtons: [
                        () => {
                            return (
                                <Button
                                    outline
                                    size="xs"
                                    onClick={() => navigateTo(replacePath(modelConfig.path))}
                                >
                                    <HiOutlineArrowCircleLeft className="me-0.5" />
                                    モデル一覧に戻る
                                </Button>
                            )
                        },
                    ],
                }}
            />
        </>
    )
}
